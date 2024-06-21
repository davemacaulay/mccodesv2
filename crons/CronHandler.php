<?php
declare(strict_types=1);

const CRON_FILE_INC = true;

if (($argc ?? 1) > 1) {
    parse_str($argv[1], $params);
    $cron = $params['cron'] ?? null;
    parse_str($argv[2], $params);
    $code   = $params['code'];
    $is_cli = defined('STDIN') || php_sapi_name() === 'cli';
} else {
    $cron   = array_key_exists('cron', $_GET) && in_array($_GET['cron'], CronHandler::$crons) ? $_GET['cron'] : null;
    $code   = array_key_exists('code', $_GET) && ctype_alnum($_GET['code']) ? $_GET['code'] : null;
    $is_cli = false;
}
global $db, $_CONFIG;
if (empty($db)) {
    require_once dirname(__DIR__) . '/globals_nonauth.php';
}
if ((!defined('CRON_OVERRIDE') || CRON_OVERRIDE !== true) && $code !== $_CONFIG['code']) {
    echo 'Access denied';
    exit;
}
spl_autoload_register(static function ($class) {
    $file = __DIR__ . '/classes/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

/**
 *
 */
class CronHandler
{
    public static array $crons = ['minute-1', 'minute-5', 'hour-1', 'day-1'];
    public static array $cronStats = [];
    protected static string $cron;
    protected static ?self $inst = null;
    public array $lastRuns = [];
    protected int $pendingIncrements = 0;
    protected ?database $db = null;
    protected array $settings = [];
    protected bool $timerStarted = false;
    protected int $affectedRows = 0;

    /**
     * @param database|null $db
     */
    public function __construct(?database $db = null)
    {
        $this->setDb($db);
        $this->setSettings();
        $this->setLastRuns();
    }

    /**
     * @param database|null $db
     * @return void
     */
    private function setDb(?database $db): void
    {
        if (!empty($db) || empty($this->db)) {
            $this->db = $db;
        }
    }

    /**
     * @return void
     */
    private function setSettings(): void
    {
        if (empty($this->settings)) {
            $this->settings = get_site_settings();
        }
    }

    /**
     * @return void
     */
    private function setLastRuns(): void
    {
        $get_last_runs = $this->db->query(
            'SELECT * FROM cron_times ORDER BY name',
        );
        while ($row = $this->db->fetch_row($get_last_runs)) {
            $this->lastRuns[$row['name']] = $row['last_run'];
        }
    }

    /**
     * @param string $cron
     * @param int $increments
     * @return void
     */
    public function run(string $cron, int $increments): void
    {
        self::$cron              = $cron;
        $this->pendingIncrements = $increments;
        if (!in_array(self::$cron, self::$crons, true)) {
            return;
        }
        $this->generateCronStats();
        if (!defined('SILENT_CRONS') || SILENT_CRONS !== true) {
            echo PHP_EOL . 'Beginning: ' . $cron . PHP_EOL;
        }
        try {
            $op = match (self::$cron) {
                'minute-1' => CronOneMinute::getInstance($this->db),
                'minute-5' => CronFiveMinute::getInstance($this->db),
                'minute-60', 'hour-1' => CronOneHour::getInstance($this->db),
                'hour-24', 'day-1' => CronOneDay::getInstance($this->db),
                default => throw new RuntimeException('Invalid cron ID'),
            };
            $op->doFullRun($increments);
            $runtime = CronHandler::$cronStats[self::$cron]['end_mil'] - CronHandler::$cronStats[self::$cron]['start_mil'];
            if (!defined('SILENT_CRONS') || SILENT_CRONS !== true) {
                echo 'Complete: ' . self::$cron . ' in ' . number_format($runtime, 5) . 's' . PHP_EOL;
            }
        } catch (Exception|Throwable $e) {
            if (!defined('SILENT_CRONS') || SILENT_CRONS !== true) {
                echo $e->getMessage() . PHP_EOL . 'Fail: ' . self::$cron . PHP_EOL;
            }
        }
    }

    /**
     * @return void
     */
    private function generateCronStats(): void
    {
        $default_stat                 = '1970-01-01 00:00:01.000';
        self::$cronStats[self::$cron] = [
            'start' => $default_stat,
            'end' => $default_stat,
        ];
    }

    /**
     * @return self|null
     */
    public static function getInstance(?database $db): ?self
    {
        if (self::$inst === null) {
            self::$inst = new self($db);
        }
        return self::$inst;
    }

    /**
     * @return void
     */
    protected function updateAffectedRowCnt(): void
    {
        $this->affectedRows += $this->db->affected_rows() ?? 0;
    }

    /**
     * @param array $methods
     * @param CronOneMinute|CronFiveMinute|CronOneHour|CronOneDay $class
     * @return void
     * @throws Throwable
     */
    protected function doFullRunActual(array $methods, CronOneMinute|CronFiveMinute|CronOneHour|CronOneDay $class): void
    {
        if (empty($methods)) {
            return;
        }
        $this->startTimer();
        foreach ($methods as $method) {
            try {
                ($class::getInstance($this->db))->$method();
                if (!defined('SILENT_CRONS') || SILENT_CRONS !== true) {
                    echo 'Running: ' . $class->getClassName() . ': ' . $method . PHP_EOL;
                }
            } catch (Exception|Throwable $e) {
                $this->logError($method, $e->getMessage());
            }
        }
        $this->endTimer();
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function startTimer(): void
    {
        if ($this->timerStarted !== true) {
            self::$cronStats[self::$cron]['start']     = $this->getTimestamp();
            self::$cronStats[self::$cron]['start_mil'] = microtime(true);
        }
        $this->timerStarted = true;
    }

    /**
     * @return string
     * @throws Exception
     */
    private function getTimestamp(): string
    {
        $get_db_time = $this->db->query(
            'SELECT NOW(3)',
        );
        $time        = $this->db->fetch_single($get_db_time);
        $this->db->free_result($get_db_time);
        return $time;
    }

    /**
     * @param string $section
     * @param string|null $message
     * @return void
     * @throws Exception
     */
    protected function logError(string $section, ?string $message = null): void
    {
        $this->db->query(sprintf(
            'INSERT INTO logs_cron_fails (cron, method, message, time_started, time_finished) VALUES (\'%s\', \'%s\', \'%s\', \'%s\', \'%s\')',
            self::$cron, $section, $message, self::$cronStats[self::$cron]['start'], self::$cronStats[self::$cron]['end'],
        ));
    }

    /**
     * @return void
     * @throws Throwable
     */
    protected function endTimer(): void
    {
        if ($this->timerStarted === true) {
            self::$cronStats[self::$cron]['end']     = $this->getTimestamp();
            self::$cronStats[self::$cron]['end_mil'] = microtime(true);
            $this->logCronRuntime();
        }
        $this->timerStarted = false;
    }

    /**
     * @return void
     * @throws Throwable
     */
    private function logCronRuntime(): void
    {
        $div = match (self::$cron) {
            'minute-1' => 60,
            'minute-5' => 300,
            'hour-1' => 3600,
            'day-1' => 86400,
        };
        if (empty($div)) {
            return;
        }
        $this->db->query(
            'UPDATE cron_times SET last_run = CONCAT(CONCAT(DATE_FORMAT(NOW(), \'%Y-%m-%d\'), \' \'), SEC_TO_TIME((TIME_TO_SEC(NOW(3)) DIV ' . $div . ') * ' . $div . ')) WHERE name = \'' . self::$cron . '\'',
        );
        $this->db->query(sprintf(
            'INSERT INTO logs_cron_runtimes (cron, time_started, time_finished, updated_cnt) VALUES (\'%s\', \'%s\', \'%s\', %u)',
            self::$cron,
            self::$cronStats[self::$cron]['start'],
            self::$cronStats[self::$cron]['end'],
            $this->affectedRows,
        ));
    }
}

if ($cron !== null && $is_cli) {
    (CronHandler::getInstance($db))->run($cron, 1);
}
