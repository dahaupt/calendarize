<?php

/**
 * CalMigration.
 */
declare(strict_types=1);

namespace HDNET\Calendarize\Slots;

use HDNET\Autoloader\Annotation\SignalClass;
use HDNET\Autoloader\Annotation\SignalName;
use HDNET\Calendarize\Updates\CalMigrationUpdate;
use HDNET\Calendarize\Utility\HelperUtility;

/**
 * CalMigration.
 */
class CalMigration
{
    /**
     * Update the sys_file_reference table for tx_cal_event files like images.
     *
     * @see \HDNET\Calendarize\Updates\CalMigrationUpdate::performCalEventUpdate()
     *
     * @SignalClass \HDNET\Calendarize\Updates\CalMigrationUpdate
     * @SignalName performCalEventUpdatePostInsert
     *
     * @return array
     */
    public function updateSysFileReference()
    {
        $args = \func_get_args();
        list($calendarizeEventRecord, $event, $table, $recordId, $dbQueries) = $args;

        $q = HelperUtility::getDatabaseConnection('sys_file_reference')->createQueryBuilder();
        $q->select('*')
            ->from('sys_file_reference')
            ->where(
                $q->expr()->andX(
                    $q->expr()->eq('tablenames', $q->quote('tx_cal_event')),
                    $q->expr()->eq('uid_foreign', $q->createNamedParameter((int)$event['uid'], \PDO::PARAM_INT))
                )
            );

        $dbQueries[] = $q->getSQL();
        $selectResults = $q->execute()->fetchAll();

        foreach ($selectResults as $selectResult) {
            $q->resetQueryParts();

            $importId = CalMigrationUpdate::IMPORT_PREFIX . $selectResult['uid'];

            $fieldValues = [
                'uid_foreign' => (int)$recordId,
                'tablenames' => $table,
            ];

            $q->update('sys_file_reference')
                ->where(
                    $q->expr()->eq('import_id', $q->createNamedParameter($importId))
                )
                ->values($fieldValues);

            $dbQueries[] = $q->getSQL();

            $q->execute();
        }

        $variables = [
            'calendarizeEventRecord' => $calendarizeEventRecord,
            'event' => $event,
            'table' => $table,
            'recordId' => $recordId,
            'dbQueries' => $dbQueries,
        ];

        return $variables;
    }
}
