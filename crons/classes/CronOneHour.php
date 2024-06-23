<?php
declare(strict_types=1);

use ParagonIE\EasyDB\EasyPlaceholder;

if (!defined('CRON_FILE_INC')) {
    exit;
}

/**
 *
 */
final class CronOneHour extends CronHandler
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
            'processGangCrimes',
            'resetVerifiedStatus',
        ], $this);
    }

    /**
     * @return void
     */
    public function processGangCrimes(): void
    {
        $this->db->query(
            'UPDATE gangs SET gangCHOURS = GREATEST(gangCHOURS - ' . $this->pendingIncrements . ', 0) WHERE gangCRIME > 0',
        );
        $this->updateAffectedRowCnt();
        $q = $this->db->query(
            'SELECT gangID,ocSTARTTEXT, ocSUCCTEXT, ocFAILTEXT, ocMINMONEY, ocMAXMONEY, ocID, ocNAME
            FROM gangs AS g
            INNER JOIN orgcrimes AS oc ON g.gangCRIME = oc.ocID
            WHERE g.gangCRIME > 0 AND g.gangCHOURS <= 0'
        );
        while ($r = $this->db->fetch_row($q)) {
            $suc = rand(0, 1);
            if ($suc) {
                $log  = $r['ocSTARTTEXT'] . $r['ocSUCCTEXT'];
                $muny = rand($r['ocMINMONEY'], $r['ocMAXMONEY']);
                $log  = $this->db->escape(str_replace('{muny}', (string)$muny, $log));
                $this->db->query(
                    'UPDATE gangs SET gangMONEY = gangMONEY + ' . $muny . ', gangCRIME = 0 WHERE gangID = ' . $r['gangID'],
                );
                $this->updateAffectedRowCnt();
                $this->db->query(sprintf(
                    'INSERT INTO oclogs (oclOC, oclGANG, oclLOG, oclRESULT, oclMONEY, ocCRIMEN, ocTIME)
                    VALUES (%u, %u, \'%s\', \'success\', %u, \'%s\', %u)',
                    $r['ocID'], $r['gangID'], $log, $muny, $r['ocNAME'], time(),
                ));
                $i = $this->db->insert_id();
                $this->updateAffectedRowCnt();
                $qm = $this->db->query(
                    'SELECT userid FROM users WHERE gang = ' . $r['gangID'],
                );
                while ($rm = $this->db->fetch_row($qm)) {
                    event_add($rm['userid'], 'Your Gang\'s Organised Crime Succeeded. Go <a href="oclog.php?ID=' . $i . '">here</a> to view the details.');
                    $this->updateAffectedRowCnt();
                }
            } else {
                $log  = $r['ocSTARTTEXT'] . $r['ocFAILTEXT'];
                $muny = 0;
                $log  = $this->db->escape(str_replace('{muny}', (string)$muny, $log));
                $this->db->query(
                    'UPDATE gangs SET gangCRIME = 0 WHERE gangID = ' . $r['gangID']
                );
                $this->updateAffectedRowCnt();
                $this->db->query(sprintf(
                    'INSERT INTO oclogs (oclOC, oclGANG, oclLOG, oclRESULT, oclMONEY, ocCRIMEN, ocTIME)
                    VALUES (%u, %u, \'%s\', \'failure\', %u, \'%s\', %u)',
                    $r['ocID'], $r['gangID'], $log, $muny, $r['ocNAME'], time(),
                ));
                $i = $this->db->insert_id();
                $this->updateAffectedRowCnt();
                $qm = $this->db->query(
                    'SELECT userid FROM users WHERE gang = ' . $r['gangID'],
                );
                while ($rm = $this->db->fetch_row($qm)) {
                    event_add($rm['userid'], 'Your Gang\'s Organised Crime Failed. Go <a href="oclog.php?ID=' . $i . '">here</a> to view the details.');
                    $this->updateAffectedRowCnt();
                }
            }
            $this->db->free_result($qm);
        }
        $this->db->free_result($q);
    }

    public function resetVerifiedStatus(): void
    {
        $this->db->query(
            'UPDATE users SET verified = 0 WHERE verified > 0',
        );
        $this->updateAffectedRowCnt();
    }

    public function getClassName(): string
    {
        return __CLASS__;
    }
}
