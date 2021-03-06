<?php
/**
 * Created by PhpStorm.
 * User: Jackson
 * Date: 8/09/2018
 * Time: 1:05 PM
 */

namespace App\Helpers\Statistics\Reprobated;


use App\Helpers\Statistics\Consolidated\AbstractConsolidated;
use App\Helpers\Statistics\ParamsStatistics;
use App\Helpers\Utils\Utils;

class AbstractReprobated extends AbstractConsolidated
{


    public function __construct(ParamsStatistics $params)
    {
        parent::__construct($params);

    }

    public function getProcessedRequest()
    {
        parent::getProcessedRequest();

        foreach ($this->vectorEnrollments as $key_enroll => &$enrollment) {
            $sum = 0;
            foreach ($this->vectorPeriods as &$vectorPeriod) {
                $num_asignatures_reprobated = 0;

                foreach ($enrollment->evaluatedPeriods as &$period) {

                    if ($vectorPeriod->periods_id == $period['period_id']) {

                        foreach ($period['notes'] as $key_note => $note) {
                            $value_note = Utils::process_note($note->value, $note->overcoming);
                            if ($value_note < $this->middle_point && $value_note > 0) {
                                $num_asignatures_reprobated++;
                                if (!isset($period['num_asignatures_reprobated']))
                                    $period['num_asignatures_reprobated'] = 0;
                                $period['num_asignatures_reprobated'] = $num_asignatures_reprobated;
                            } else {
                                unset($period['notes'][$key_note]);
                            }
                        }
                    }
                }

                if ($num_asignatures_reprobated > 0) {
                    $sum += $num_asignatures_reprobated;
                }


            }

            if ($sum>0) {
                $enrollment->total_asignatures_reprobated = $sum;
            } else {
                unset($this->vectorEnrollments[$key_enroll]);
            }

        }

        return $this->vectorEnrollments;

    }

}