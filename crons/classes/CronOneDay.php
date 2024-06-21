<?php
declare(strict_types=1);

if (!defined('CRON_FILE_INC') || CRON_FILE_INC !== true) {
    exit;
}

/**
 *
 */
final class CronOneDay extends CronHandler
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
            'updatePunishments',
            'updateDailyTicks',
            'processCourses',
            'payJobWages',
            'updateBankInterests',
            'clearVotes',
        ], $this);
    }

    /**
     * @return void
     */
    public function clearVotes(): void
    {
        $this->db->query('TRUNCATE TABLE votes');
    }

    /**
     * @return void
     */
    public function processCourses(): void
    {
        $q            = $this->db->query(
            'SELECT userid, course FROM users WHERE cdays <= 0 AND course > 0',
        );
        $course_cache = [];
        while ($r = $this->db->fetch_row($q)) {
            if (!array_key_exists($r['course'], $course_cache)) {
                $cd   = $this->db->query(
                    'SELECT crSTR, crGUARD, crLABOUR, crAGIL, crIQ, crNAME FROM courses WHERE crID = ' . $r['course'],
                );
                $coud = $this->db->fetch_row($cd);
                $this->db->free_result($cd);
                $course_cache[$r['course']] = $coud;
            } else {
                $coud = $course_cache[$r['course']];
            }
            $this->db->query(
                'INSERT INTO coursesdone (userid, courseid) VALUES (' . $r['userid'] . ', ' . $r['course'] . ')',
            );
            $this->updateAffectedRowCnt();
            $upd = '';
            $ev  = '';
            if ($coud['crSTR'] > 0) {
                $upd .= ', us.strength = us.strength + ' . $coud['crSTR'];
                $ev  .= ', ' . $coud['crSTR'] . ' strength';
            }
            if ($coud['crGUARD'] > 0) {
                $upd .= ', us.guard = us.guard + ' . $coud['crGUARD'];
                $ev  .= ', ' . $coud['crGUARD'] . ' guard';
            }
            if ($coud['crLABOUR'] > 0) {
                $upd .= ', us.labour = us.labour + ' . $coud['crLABOUR'];
                $ev  .= ', ' . $coud['crLABOUR'] . ' labour';
            }
            if ($coud['crAGIL'] > 0) {
                $upd .= ', us.agility = us.agility + ' . $coud['crAGIL'];
                $ev  .= ', ' . $coud['crAGIL'] . ' agility';
            }
            if ($coud['crIQ'] > 0) {
                $upd .= ', us.IQ = us.IQ + ' . $coud['crIQ'];
                $ev  .= ', ' . $coud['crIQ'] . ' IQ';
            }
            $ev = substr($ev, 1);
            $this->db->query(
                'UPDATE users AS u
                INNER JOIN userstats AS us ON u.userid = us.userid
                SET u.course = 0' . $upd . '
                WHERE u.userid = ' . $r['userid'],
            );
            $this->updateAffectedRowCnt();
            event_add($r['userid'], 'Congratulations, you completed the ' . $coud['crNAME'] . ' and gained ' . $ev . '!');
            $this->updateAffectedRowCnt();
        }
        $this->db->free_result($q);
    }

    public function updateDailyTicks(): void
    {
        $this->db->query(
            'UPDATE users SET 
             daysingang = daysingang + IF(gang > 0, ' . $this->pendingIncrements . ', 0),
             boxes_opened = 0,
             mailban = mailban - IF(mailban > 0, GREATEST(' . $this->pendingIncrements . ', 1), 0),
             donatordays = donatordays - IF(donatordays > 0, GREATEST(' . $this->pendingIncrements . ', 1), 0),
             cdays = cdays - IF(course > 0, GREATEST(' . $this->pendingIncrements . ', 1), 0)
             WHERE gang > 0 OR mailban > 0 OR donatordays > 0 OR cdays > 0 OR boxes_opened <> 0'
        );
        $this->updateAffectedRowCnt();
        $this->db->query(
            'UPDATE users SET daysold = daysold + ' . $this->pendingIncrements . ' WHERE user_level > 0',
        );
        $this->updateAffectedRowCnt();
    }

    /**
     * @throws Exception
     */
    public function updatePunishments(): void
    {
        $this->db->query(
            'UPDATE fedjail SET fed_days = GREATEST(fed_days - ' . $this->pendingIncrements . ', 0)',
        );
        $this->updateAffectedRowCnt();
        $q   = $this->db->query('SELECT * FROM fedjail WHERE fed_days <= 0');
        $ids = [];
        while ($r = $this->db->fetch_row($q)) {
            $ids[] = $r['fed_userid'];
        }
        $this->db->free_result($q);
        if (count($ids) > 0) {
            $this->db->query(
                'UPDATE users SET fedjail = 0 WHERE userid IN(' . implode(',', $ids) . ')',
            );
            $this->updateAffectedRowCnt();
        }
        $this->db->query('DELETE FROM fedjail WHERE fed_days <= 0');
        $this->updateAffectedRowCnt();
    }

    /**
     * @throws Exception
     */
    public function payJobWages(): void
    {
        $this->db->query(
            'UPDATE users AS u
            INNER JOIN userstats AS us ON u.userid = us.userid
            LEFT JOIN jobranks AS jr ON jr.jrID = u.jobrank
            SET u.money = u.money + (jr.jrPAY * ' . $this->pendingIncrements . '), u.exp = u.exp + ((jr.jrPAY / 20) * ' . $this->pendingIncrements . '),
            us.strength = (us.strength + ' . $this->pendingIncrements . ') + jr.jrSTRG - ' . $this->pendingIncrements . ',
            us.labour = (us.labour + ' . $this->pendingIncrements . ') + jr.jrLABOURG - ' . $this->pendingIncrements . ',
            us.IQ = (us.IQ + ' . $this->pendingIncrements . ') + jr.jrIQG - ' . $this->pendingIncrements . '
            WHERE u.job > 0 AND u.jobrank > 0'
        );
        $this->updateAffectedRowCnt();
    }

    /**
     * @throws Exception
     */
    public function updateBankInterests(): void
    {
        $rates    = [
            'bank' => pow(1 + 2 / 100, 1 / 365.2425),
            'cyber' => pow(1 + 7 / 100, 1 / 365.2425),
        ];
        $partials = [
            'bank' => pow($rates['bank'], $this->pendingIncrements),
            'cyber' => pow($rates['cyber'], $this->pendingIncrements),
        ];
        $this->db->query(
            'UPDATE users SET 
            bankmoney = IF(bankmoney > 0, ' . $partials['bank'] . ' * bankmoney, bankmoney),
            cybermoney = IF(cybermoney > 0, ' . $partials['cyber'] . ' * cybermoney, cybermoney)
            WHERE bankmoney > 0 OR cybermoney > 0',
        );
        $this->updateAffectedRowCnt();
    }
}
