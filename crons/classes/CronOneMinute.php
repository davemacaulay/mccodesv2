<?php
declare(strict_types=1);

if (!defined('CRON_FILE_INC') || CRON_FILE_INC !== true) {
    exit;
}

/**
 *
 */
final class CronOneMinute extends CronHandler
{
    private static ?self $instance = null;

    /**
     * @param database|null $db
     * @return self|null
     */
    public static function getInstance(?database $db): ?self
    {
        parent::getInstance($db);
        if (self::$instance === null) {
            self::$instance = new self($db);
        }
        return self::$instance;
    }

    public function getClassName(): string
    {
        return __CLASS__;
    }

    /**
     * @param int $increments
     * @return void
     * @throws Throwable
     */
    public function doFullRun(int $increments): void
    {
        if (!empty($increments) && empty($this->pendingIncrements)) {
            $this->pendingIncrements = $increments;
        }
        parent::doFullRunActual([
            'updateJailHospitalTimes',
        ], $this);
    }

    /**
     * @return void
     * @throws Throwable
     */
    public function updateJailHospitalTimes(): void
    {
        $this->db->query(
            'UPDATE users SET hospital = GREATEST(hospital - ' . $this->pendingIncrements . ', 0), jail = GREATEST(jail - ' . $this->pendingIncrements . ', 0) WHERE jail > 0 OR hospital > 0'
        );
        $this->updateAffectedRowCnt();
        $get_counts = $this->db->query(
            'SELECT 
            SUM(IF(hospital > 0, 1, 0)) AS hc,
            SUM(IF(jail > 0, 1, 0)) AS jc
            FROM users'
        );
        $counts     = $this->db->fetch_row($get_counts);
        $this->db->query(
            'UPDATE settings SET conf_value = IF(conf_name = \'hospital_count\', ' . $counts['hc'] . ', conf_value), conf_value = IF(conf_name = \'jail_count\', ' . $counts['jc'] . ', conf_value) WHERE conf_name IN (\'hospital_count\', \'jail_count\')'
        );
        $this->updateAffectedRowCnt();
    }
}
