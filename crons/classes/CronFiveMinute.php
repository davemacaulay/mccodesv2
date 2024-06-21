<?php
declare(strict_types=1);

if (!defined('CRON_FILE_INC') || CRON_FILE_INC !== true) {
    exit;
}

/**
 *
 */
final class CronFiveMinute extends CronHandler
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
            'updateUserStatBars',
        ], $this);
    }

    /**
     * @return void
     */
    public function updateUserStatBars(): void
    {
        $this->db->query(
            'UPDATE users SET
            brave = LEAST(brave + (((maxbrave / 10) + 0.5) * ' . $this->pendingIncrements . '), maxbrave),
            hp = LEAST(hp + ((maxhp / 3) * ' . $this->pendingIncrements . '), maxhp),
            will = LEAST(will + (10 * ' . $this->pendingIncrements . '), maxwill),
            energy = IF(donatordays > 0,
                LEAST(energy + ((maxenergy / 6) * ' . $this->pendingIncrements . '), maxenergy),
                LEAST(energy + ((maxenergy / 12.5) * ' . $this->pendingIncrements . '), maxenergy)
            ),
            verified = 0'
        );

        $this->updateAffectedRowCnt();
    }
}
