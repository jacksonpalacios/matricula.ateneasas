<?php

namespace App\Pdf\Notebook;

/**
 *
 */
use Illuminate\Support\Facades\Storage;
use Codedge\Fpdf\Fpdf\Fpdf;

use App\Traits\utf8Helper;

/**
 *
 */
class Notebook extends Fpdf
{

    use utf8Helper;

    private $data = array();

    private $_h_c = 4;
    private $reprobated_tecn = false;

    function header()
    {
        // Logo
        if ($this->data['institution']['picture'] != NULL) {
            try {

                $this->Image(
                    Storage::disk('uploads')->url(
                        $this->data['institution']->picture
                    ), 12, 12, 17, 17);

            } catch (\Exception $e) {
            }
        }

        //Marco
        $this->Cell(0, 24, '', 1, 0);
        $this->Ln(0);

        // NOMBRE DE LA INSTITUCIÓN
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 6, $this->hideTilde($this->data['institution']->name), 0, 0, 'C');
        $this->Ln(6);

        $this->SetFont('Arial', 'B', 9);
        // NOMBRE DE LA SEDE
        if (!empty($this->data['headquarter'])):
            $this->Cell(0, 4, 'SEDE: ' . $this->hideTilde(strtoupper(($this->data['headquarter']->name))), 0, 0, 'C');

        endif;

        $this->Ln(4);

        // TITULO DEL PDF
        $this->Cell(0, 4, strtoupper($this->data['tittle']), 0, 0, 'C');
        $this->Ln();

        // NOMBRE DEL GRUPO
        $this->SetFont('Arial', '', 8);
        $this->Cell(20, 4, '', 0, 0);
        $this->Cell(90, 4, 'GRUPO: ' . $this->data['group']->name, 0, 0, 'L');

        // DIRECTOR DE GRUPO
        $this->Cell(0, 4, (!is_null($this->data['director'])) ? $this->hideTilde('DIR. DE GRUPO: ' .
            $this->data['director']->fullName) : 'DIR. DE GRUPO: ', 0, 0, 'L');
        $this->Ln();

        // NOMBRE DEL ESTUDIANTE
        $this->Cell(20, 4, '', 0, 0);
        $this->Cell(90, 4, 'ESTUDIANTE: ' . $this->hideTilde(
                $this->data['student']->fullNameInverse
            ), 0, 0, 'L');

        // FECHA
        $this->Cell(0, 4, 'FECHA: ' . $this->data['date'], 0, 0, 'L');
        // Salto de línea
        $this->Ln(8);

        $this->subHeader();
    }

    private function subHeader()
    {


        if ($this->data['config']['periodIF']):
            $this->Cell(140, $this->_h_c, $this->hideTilde($this->data['tittle_if']), 1, 0, 'L');
        else:
            $this->Cell(140, $this->_h_c, $this->data['tittle'] . $this->hideTilde(' PERIODO ' . $this->data["current_period"]->periods_id . ' - AÑO LECTIVO ') . date('Y'), 1, 0, 'L');
        endif;


        $this->Cell(10, $this->_h_c, 'IHS', 1, 0, 'C');

        $this->Cell(17, $this->_h_c, 'VAL', 1, 0, 'C');
        $this->Cell(0, $this->_h_c, $this->hideTilde('DESEMPEÑO'), 1, 0, 'C');

        $this->Ln($this->_h_c + 4);

        // Marco
        // $this->Cell(0, 212, '', 1,0);
        //    $this->Ln(0);
    }

    public function setData($data = array())
    {
        $this->data = $data;
    }

    public function create()
    {
        // AÑADIMOS UNA PAGINA EN BLANCO

        $this->addPage();

        // RECOREMOS LOS PERIODOS
        foreach ($this->data['periods'] as $periodKey => $period):

            // PREGUNTAMOS SI EL PERIODO RECORRIDO ES IGUAL AL PERIOD SOLICITADO
            if ($period['periods_id'] == $this->data['current_period']->periods_id):

                // MOSTRAMOS LAS AREAS
                $this->showAreas($period['areas']);
                $this->Cell(0, $this->_h_c, '', 'T', 0, 'L');
            endif;

        endforeach;

        if ($this->data['config']['combinedEvaluation']):

            // MOSTRAMOS EL CUADRO ACUMULATIVO
            $this->showCombineEvaluation();

            // MOSTRAMOS EL PROMEDIO GENERAL
            $this->showScore();

            // MOSTRAMOS EL PUESTO OCUPADO
            $this->showPosition();
        endif;

        // Detallado de notas
        //if($this->data['config'][0]['tableDetail']):
        // 	$this->showTableDetail($period);
        //endif;

        // MOSTRAR ESCALA VALORATIVA
        if ($this->data['config']['valorationScale']):
            $this->showValueScale();
        endif;

        // MOSTRAMOS LAS INASISTENCIA EN CASO DE QUE SEA UN CURSO INFERIOR A 6°
        if ($this->data['grade']['id'] < 10):

            $this->showTotalAttendanceByPeriod();

        endif;

        // MOSTRAMOS LAS OBSERVACIONES GENERALES
        $this->showGeneralObservation();

        // Activanos el doble cara
        if ($this->data['config']['doubleFace']):
            $this->DoubleFace();
        endif;
    }

    private function showAreas($areas = array())
    {
        // RECORREMOS LAS AREAS
        foreach ($areas as $areaKey => $area):

            // FONDO PARA LAS CELDAS DE LAS AREAS
            $this->SetFillColor(230, 230, 230);
            $this->SetFont('Arial', 'B', 8);

            // VERIFICAMOS LA CALIFICACIÓN DEL AREA
            if ($area['note'] > 0):
                // PREGUNTAMOS SI EL PERIODO IF ESTA ACTIVO
                if (!$this->data['config']['periodIF']):
                    // PREGUNTAMOS SI LAS AREAS NO SE DESACTIVAN
                    if (!$this->data['config']['areasDisabled']):

                        $this->Cell(150, $this->_h_c, $this->hideTilde($area['area']), 'TBL', 0, 'L', true);
                        $this->Cell(17, $this->_h_c, $area['note'], 'TB', 0, 'C', true);
                        $this->Cell(0, $this->_h_c, $this->hideTilde(strtoupper($area['valoration']['name'])), 'TBR', 0, 'C', true);

                    else:

                        $this->Cell(0, $this->_h_c, $this->hideTilde($area['area']), 1, 0, 'L', true);

                    endif;
                    $this->Ln();
                else:

                endif;
                // RECORREMOS LAS ASIGNATURAS
                $this->showAsignature($area['asignatures']);
            endif;


        endforeach;
    }

    /***/
    private function showAsignature($asignatures = array())
    {
        //
        foreach ($asignatures as $keyAsignature => $asignature):

            if ($this->determineShowValoration($asignature)):

                $asigFinalNote = (isset($asignature['final_note']['value'])) ? $asignature['final_note']['value'] : 0;

                if ($asigFinalNote > 0):

                    if ($this->data['config']['periodIF']):
                        //dd($this->data);
                        foreach ($this->finalReportList as $report):
                            if ($this->data['student']['id'] == $report['id_student'] && $report['id_asignature'] == $asignature['asignature_id']):
                                $def = $report['nota'];

                                $this->showValoration($report, $asignature['ihs'], true);
                            endif;
                        endforeach;
                    else:
                        // MOSTRAMOS LA VALORACIÓN
                        $this->showValoration($asignature, $asignature['ihs'], false);
                    endif;

                    if (!$this->data['config']['periodIF']):
                        // MOSTRAMOS LOS DESEMPEÑOS (LOGROS)
                        $this->SetFont('Arial', '', 8);
                        if ($this->data['config']['showPerformance'] == 'indicators')
                            $this->showPerformance($asignature['indicators']);
                        else if ($this->data['config']['showPerformance'] == 'asignature')
                            $this->showPerformance($asignature['final_note']['performances']);

                        // MOSTRAMOS LAS OBSERVACIONES
                        // $this->showObservationsByAsignature($asignature);
                    endif;

                    // MOSTRAMOS AL DOCENTE
                    if ($this->data['config']['showTeacher']):
                        $this->showTeacher($asignature['teacher']);
                    endif;

                endif;


            endif;

        endforeach;
    }

    /**
     *
     *
     */
    private function determineShowValoration($asignature = array())
    {
        foreach ($this->data['periods'] as $keyPeriod => $period) {
            foreach ($period['areas'] as $keyAreas => $area) {
                foreach ($area['asignatures'] as $keyAsignature => $asignaturee) {
                    if (isset($asignaturee['final_note']['value'])) {
                        if ($asignature['asignature_id'] == $asignaturee['asignature_id'] && $asignaturee['final_note']['value'] > 0) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     *
     *
     */
    private function showValoration($asignature, $ihs = 0, $utf8_encode = false)
    {
        $this->SetFont('Arial', 'B', 8);

        $pahtImage = env('APP_URL') . "/img/notebook/";
        $height = 0;
        $note = 0;
        $valoration = '';

        if ($this->data['config']['NumberValoration']):
            $note = (isset($asignature['final_note']['value'])) ? $asignature['final_note']['value'] : 0;
            $valoration = $this->hideTilde(strtoupper($asignature['final_note']['valoration']['name']));
        else:
            $note = '';
            $valoration = '';
        endif;

        $ihs = ($ihs <= 0) ? '' : $ihs;

        if ($this->data['config']['showFaces'] == true):
            $height = 11;
        else:
            $height = $this->_h_c;
        endif;

        if (!$utf8_encode)
            $this->Cell(140, $height, $this->hideTilde($asignature['asignature']), 'L', 0, 'L');
        else
            $this->Cell(140, $height, ($asignature['asignature']), 'L', 0, 'L');

        $this->Cell(10, $height, $ihs, 0, 0, 'C');

        $this->Cell(17, $height, $note, 0, 0, 'C');

        if ($this->data['config']['showFaces'] == true):

            $this->Image($pahtImage . strtolower($asignature['final_note']['valoration']['name']) . '.png', 185, $this->GetY() + 1, 9, 9);
            $this->Cell(0, $height, '', 'R', 0, 'C');

        else:

            $this->Cell(0, $this->_h_c, ($valoration), 'R', 0, 'C');
        endif;

        $this->Ln($height);
    }

    /**
     *
     *
     */
    private function showPerformance($performances = array())
    {
        if (!is_null($performances))
            foreach ($performances as $key => $performance)
                if (isset($performance->name))
                    $this->determineCell($this->hideTilde('   * ' . strtoupper($performance->name)), 'LR');
    }


    /**
     *
     *
     */
    private function showCombineEvaluation()
    {
        $this->Ln($this->_h_c * 2);

        $hasIF = count($this->data['periods']) == $this->data['current_period']->periods_id;
        //$hasIF = 3 == $this->data['current_period']->periods_id;
        $withHeader = /*(!empty($this->finalReportList)) ? (95 + (22 * count($this->data['periods']))+8) :*/
            (96 + (22 * count($this->data['periods'])));

        $this->SetFont('Arial', 'B', 8);

        $this->Cell($withHeader, $this->_h_c, $this->hideTilde('VALORACIONES ACUMULADAS DURANTE EL AÑO LECTIVO'), 1, 0, 'C');

        $this->Ln();

        $this->Cell(90, $this->_h_c, 'AREAS / ASIGNATURAS', 1, 0, 'C');
        $this->Cell(6, $this->_h_c, 'IHS', 1, 0, 'C');

        // RECORREMOS LOS PERIODOS
        foreach ($this->data['periods'] as $periodKey => $period):

            $this->Cell(6, $this->_h_c, 'Fa', 1, 0, 'C');
            $this->Cell(8, $this->_h_c, 'P' . $period['periods_id'], 'LTB', 0, 'C');
            $this->Cell(8, $this->_h_c, $period['percent'], 'TRB', 0, 'C');
        endforeach;

        // SI EL PERIODO A IMPRIMIR EL EL ULTIMO SE MUESTRA LA COLUMNA IF
        if ($hasIF && $this->data['config']['includeIF']) {
            //dd($this->data);
            $this->Cell(14, $this->_h_c, 'IF', 'LRT', 0, 'C');
        }

        $this->Ln();

        //
        $this->Cell(90, $this->_h_c, '', 1, 0, 'C');
        $this->Cell(6, $this->_h_c, '', 1, 0, 'C');

        foreach ($this->data['periods'] as $periodKey => $period):

            $this->Cell(6, $this->_h_c, '', 1, 0, 'C');
            $this->Cell(8, $this->_h_c, 'VAL', 1, 0, 'C');
            $this->Cell(8, $this->_h_c, 'SUP', 1, 0, 'C');

        endforeach;

        if ($hasIF && $this->data['config']['includeIF']) {
            $this->Cell(7, $this->_h_c, 'VAL', 1, 0, 'C');
            $this->Cell(7, $this->_h_c, 'RES', 1, 0, 'C');
        } else {
            $this->Cell(7, ($this->_h_c), 'VG', 1, 0, 'C');
            $this->Cell(7, ($this->_h_c), 'VRA', 1, 0, 'C');
        }

        $this->Ln();

        foreach ($this->data['periods'] as $periodKey => $period):

            if ($this->data['current_period']->periods_id == $period['periods_id']):
                // MOSTRAMOS LAS AREAS
                $this->showAreaTableDetail($period);
            endif;
        endforeach;
    }

    /**
     *
     *
     */
    private function showPeriodValorationByArea($area = array())
    {

        foreach ($this->data['periods'] as $periodKey => $period):

            $note = '';
            if (!empty($period['areas']) && $period['periods_id'] <= $this->data['current_period']->periods_id):
                foreach ($period['areas'] as $areaKey => $areaa):


                    if ($area['area_id'] == $areaa['area_id'] && !$this->data['config']['areasDisabled']):

                        $note = $areaa['note'];

                    endif;

                endforeach;
            endif;

            $this->Cell(6, $this->_h_c, '', 1, 0, 'C', true);

            $this->Cell(8, $this->_h_c, $note, 1, 0, 'C', true);

            $this->Cell(8, $this->_h_c, '', 1, 0, 'C', true);

        endforeach;
        $hasIF = count($this->data['periods']) == $this->data['current_period']->periods_id;

        if ($hasIF && $this->data['config']['includeIF']):
            $this->showAreaFinalReport($area['area_id'], $area);
        else:
            $this->Cell(7, $this->_h_c, '', 1, 0, 'C', true);
            $this->Cell(7, $this->_h_c, '', 1, 0, 'C', true);
        endif;


    }

    public function showAreaFinalReport($area_id, $area)
    {

        if (isset($this->data['report_areas'])) {
            foreach ($this->data['report_areas'] as $report_area) {
                if ($report_area->asignatures_id == $area_id) {
                    $value = self::processNote($report_area->value, 0);
                    $this->Cell(7, $this->_h_c, round($value, 1), 1, 0, 'C');
                    $min_basic = ($this->data['valueScale']->where('abbreviation', 'BS')->first()->rank_start);

                    $report_result = $value >= $min_basic ? 'APR' : 'REP';
                    $this->Cell(7, $this->_h_c, $report_result, 1, 0, 'C');
                    if ($report_area->subjects_type_id == 2 && $value < $min_basic)
                        $this->reprobated_tecn = true;
                    return 0;
                }
            }
        }

        $this->Cell(7, $this->_h_c, '', 1, 0, 'C', true);
        $this->Cell(7, $this->_h_c, '', 1, 0, 'C', true);
    }


    /**
     *
     *
     */
    private function showTableDetail($period = array())
    {
        // $this->Ln($this->_h_c);

        $this->SetFont('Arial', 'B', 8);

        $this->Cell((100 + (22 * count($this->data['periods']))), $this->_h_c, $this->hideTilde('CUADRO DETALLADO DURANTE EL AÑO LECTIVO'), 1, 0, 'C');

        $this->Ln();

        $this->Cell(96, $this->_h_c, 'AREAS / ASIGNATURAS', 1, 0, 'C');
        // $this->Cell(6, $this->_h_c, 'IHS', 1,0, 'C');


        // MOSTRAMOS LOS DESEMPEÑOS
        // $this->showPerformanceTableDetail();

        //
        foreach ($this->data['periods'] as $keyPeriod => $period):

            if ($this->data['current_period']->periods_id == $period['periods_id']):

                $this->showAreaTableDetail($period, 'tableDetail');

            endif;
        endforeach;
    }

    /**
     *
     *
     */
    private function showAreaTableDetail($period = array(), $type = 'combinedEvaluation')
    {

        foreach ($period['areas'] as $areaKey => $area):
            // FONDO PARA LAS CELDAS DE LAS AREAS
            $this->SetFillColor(230, 230, 230);
            $this->SetFont('Arial', 'B', 8);

            $this->Cell(96, $this->_h_c,
                $this->hideTilde(
                    substr($area['area'], 0, 58)
                ),
                1, 0, 'L', true);

            if ($type == "combinedEvaluation"):
                // MOSTRAMOS LA VALORACION DE CADA PERIODO
                $this->showPeriodValorationByArea($area);

            endif;

            $this->Ln();

            // MOSTRAMOS LAS ASIGNATURAS
            $this->showAsignatureTableDetail($area['asignatures'], $type);


        endforeach;
    }

    /**
     *
     *
     */
    private function showAsignatureTableDetail($asignatures = array(), $type = 'combinedEvaluation')
    {
        foreach ($asignatures as $keyAsignature => $asignature):
            $this->SetFont('Arial', '', 8);

            if ($this->determineShowValoration($asignature)):

                if ($type == 'combinedEvaluation'):

                    $this->Cell(90, $this->_h_c,
                        $this->hideTilde(substr($asignature['asignature'], 0, 51)), 'TBL', 0, 'L');

                    $this->Cell(6, $this->_h_c,
                        ($asignature['ihs'] <= 0) ? '' : $asignature['ihs'], 1, 0, 'C');
                    // MOSTRAMOS LA VALORACION DE LOS PERIODOS
                    $this->showPeriodValorationByAsignature($asignature);

                else:
                    $this->Cell(96, $this->_h_c,
                        $this->hideTilde(substr($asignature['asignature'], 0, 51)), 'TBL', 0, 'L');

                    // $this->showValorationPerformanceTableDetail($asignature);
                endif;

                $this->Ln();

            endif;
        endforeach;
    }

    /**
     *
     *
     */
    private function showPeriodValorationByAsignature($asignature = array())
    {
        $dataVg = array();

        foreach ($this->data['periods'] as $periodKey => $period):

            $note = '';
            $noAttendace = '';
            $recovery_note = '';

            if (!empty($period['areas']) && $period['periods_id'] <= $this->data['current_period']->periods_id):
                foreach ($period['areas'] as $areaKey => $areaa):

                    foreach ($areaa['asignatures'] as $asignaturee):
                        if ($asignature['asignature_id'] == $asignaturee['asignature_id']):

                            $asigRecoveryN = (isset($asignaturee['final_note']['value'])) ? $asignaturee['final_note']['value'] : 0;
                            $asigOverCo = (isset($asignaturee['final_note']['overcoming'])) ? $asignaturee['final_note']['overcoming'] : 0;

                            if ($asigOverCo > 0):
                                $recovery_note = $asigOverCo;
                            endif;

                            $note = $asigRecoveryN;

                            // Asistencias
                            if ($this->data['grade']['name'] >= 10):
                                if (isset($asignaturee['final_note']['noAttendances'])) {
                                    $noAttendace = $asignaturee['final_note']['noAttendances'];
                                } else {
                                    $noAttendace = 0;
                                }

                            endif;


                            if (!$this->checkVG($dataVg, $asignaturee['asignature_id'], $period['periods_id'])) {
                                array_push($dataVg, [
                                    'period_id' => $period['periods_id'],
                                    'percent' => $period['percent'],
                                    'asignature_id' => $asignaturee['asignature_id'],
                                    'note' => $note,
                                    'overcoming' => $recovery_note,
                                ]);
                            }


                        endif;

                    endforeach;

                endforeach;

            endif;

            $this->Cell(6, $this->_h_c, $noAttendace, 1, 0, 'C');
            $this->Cell(8, $this->_h_c, ($note == 0) ? '' : $note, 1, 0, 'C');
            $this->Cell(8, $this->_h_c, $recovery_note, 1, 0, 'C');

        endforeach;

        $hasIF = count($this->data['periods']) == $this->data['current_period']->periods_id;

        if ($hasIF && $this->data['config']['includeIF']) {
            $this->showAsignatureFinalReport($asignature['asignature_id']);
        } else {
            $this->Cell(7, $this->_h_c, round($this->getVG($dataVg), 1), 1, 0, 'C');
            $this->Cell(7, $this->_h_c, $this->getVRA($dataVg), 1, 0, 'C');
        }

        $dataVg = array();
    }

    public function showAsignatureFinalReport($asignature_id)
    {
        if (isset($this->data['report_asignatures'])) {
            foreach ($this->data['report_asignatures'] as $report_asignature) {
                if ($report_asignature->asignatures_id == $asignature_id) {
                    $value = self::processNote($report_asignature->value, $report_asignature->overcoming);
                    $this->Cell(7, $this->_h_c, $value, 1, 0, 'C');
                    $this->Cell(7, $this->_h_c, $report_asignature->report, 1, 0, 'C');
                    return 0;
                }
            }
        }

        $this->Cell(7, $this->_h_c, '', 1, 0, 'C');
        $this->Cell(7, $this->_h_c, '', 1, 0, 'C');
    }

    private function processNote($note, $overcoming)
    {
        $noteAux = 0;
        $overcomingAux = 0;
        if ($note > 0) {
            if ($overcoming != null && $overcoming > 0) {
                $overcomingAux = $overcoming;
            }
            $noteAux = $note;
        }

        if ($noteAux > $overcomingAux)
            return $noteAux;
        else
            return $overcomingAux;
    }

    private function checkVG($array = array(), $id, $period)
    {
        foreach ($array as $key => $value) {
            if ($value['asignature_id'] == $id && $value['period_id'] == $period)
                return true;
        }

        return false;
    }

    private function getVG($asignatures = array())
    {
        $vg = 0;
        $nota = 0;
        $response = '';

        foreach ($asignatures as $key => $asignature) {
            if ($asignature['period_id'] <= $this->data['current_period']->periods_id) {

                $note = $asignature['note'];
                if ($asignature['note'] < $asignature['overcoming'])
                    $note = $asignature['overcoming'];

                $vg += $note * ($asignature['percent'] / 100);
            }
        }

        //return  $response;
        return $vg;
    }

    private function getVRA($periods = array())
    {
        $vra = 0;
        // PERIODOS A EVALUAR
        $period_tobe_evaluated = (count($this->data['periods']) - ($this->data['current_period']->periods_id));

        $min_basic = ($this->data['valueScale']->where('abbreviation', 'BS')->first()->rank_start);
        $max_super = ($this->data['valueScale']->where('abbreviation', 'S')->first()->rank_end);

        // RECORREMOS LOS PERIODOS
        foreach ($periods as $key => $period) {
            if ($this->data['current_period']->periods_id == 1) {
                $vg_period_previous = $this->getVG($periods);
                $vra = ((($min_basic - $vg_period_previous) / $period_tobe_evaluated) / ($period['percent'] / 100));

                break;
            } else if ($period['period_id'] == $this->data['current_period']->periods_id) {

                $vg_period_previous = $this->getVG($periods);
                if ($vg_period_previous > 0 && $period_tobe_evaluated > 0) {
                    $vra = (($min_basic - $vg_period_previous) / $period_tobe_evaluated) / ($period['percent'] / 100);
                }


            }
        }

        if ($vra > $max_super || $vra < 0) {
            foreach ($periods as $key => $period) {
                if ($period['period_id'] == 1) {
                    $vg_period_previous = $this->getVG($periods);
                    $vra = ((($min_basic - $vg_period_previous) / $period_tobe_evaluated) / ($period['percent'] / 100));
                    break;
                }
            }
        }

        if ($vra > $max_super || $vra < 0)
            return '';

        if ($period_tobe_evaluated == 0) {
            if ($vg_period_previous >= $min_basic)
                return 'APR';
            else
                return 'REP';
        }


        return round($vra, 1);
    }
    //

    /**
     *
     *
     */
    private function determineCell($data, $border)
    {
        $this->SetFont('Arial', '', 8);

        if (strlen($data) > 100 && strlen($data) > 0)
            $this->MultiCell(0, $this->_h_c, strip_tags($data), $border, 'L');
        else if (strlen($data) > 0) {
            $this->Cell(0, $this->_h_c, strip_tags($data), $border, 0, 'L');
            $this->Ln(4);
        }
    }

    /**
     *
     *
     */
    private function showTeacher($teacher)
    {
        if (!is_null($teacher)) {
            $this->SetFont('Arial', 'B', 8);
            $this->Cell(0, $this->_h_c, 'DOCENTE: ' . $this->hideTilde($teacher->manager->fullNameInverse), 'LR', 0, 'R');

            $this->Ln($this->_h_c);
        }
    }

    /**
     *
     *
     */
    private function showScore()
    {
        $this->SetFont('Arial', 'B', 8);

        $this->Cell(90, $this->_h_c,
            $this->hideTilde(
                'PROMEDIO GENERAL DEL ESTUDIANTE:'
            ),
            1, 0, 'R');
        $this->Cell(6, $this->_h_c, '', 1, 0, 'R');

        // MOSTRAMOS LOS PUESTOS DE CADA PERIODO
        foreach ($this->data['periods'] as $periodKey => $period):
            // PREGUNTAMOS SI EL PERIODO RECORRIDO ES IGUAL AL PERIOD SOLICITADO
            if ($period['periods_id'] <= $this->data['current_period']->periods_id):
                $this->showPeriodScores($period);
            endif;
        endforeach;

        $hasIF = count($this->data['periods']) == $this->data['current_period']->periods_id;
        if ($hasIF && $this->data['config']['includeIF']):
            $this->getAverageReport();
        else:
            $this->Cell(7, $this->_h_c, "", 1, 0, 'C');
            $this->Cell(7, $this->_h_c, "", 1, 0, 'C');
        endif;

        $this->Ln();
    }

    public function getAverageReport()
    {
        if (isset($this->data['report_final'])) {
            $this->Cell(7, $this->_h_c, $this->data['report_final']->average, 1, 0, 'C');
            $this->Cell(7, $this->_h_c, '', 1, 0, 'C');
            return 0;

        }

    }

    /**
     *
     *
     */
    private function showPeriodScores($period)
    {

        $average = (isset($period['average']['average'])) ? $period['average']['average'] : null;
        $this->Cell(6, $this->_h_c, '', 1, 0, 'C');

        $this->Cell(8, $this->_h_c, $average, 1, 0, 'C');

        $this->Cell(8, $this->_h_c, '', 1, 0, 'C');
    }

    /**
     *
     *
     */
    private function showPosition()
    {
        $this->SetFont('Arial', 'B', 8);

        $this->Cell(90, $this->_h_c,
            $this->hideTilde(
                'PUESTO EN EL GRUPO:'
            ),
            1, 0, 'R');
        $this->Cell(6, $this->_h_c, '', 1, 0, 'R');

        // MOSTRAMOS LOS PUESTOS DE CADA PERIODO
        foreach ($this->data['periods'] as $periodKey => $period):
            // PREGUNTAMOS SI EL PERIODO RECORRIDO ES IGUAL AL PERIOD SOLICITADO
            if ($period['periods_id'] <= $this->data['current_period']->periods_id):
                $this->showPeriodPositions($period);
            endif;
        endforeach;


        $this->positionReportFinal();
        $this->Ln();
    }

    public function positionReportFinal()
    {
        $hasIF = count($this->data['periods']) == $this->data['current_period']->periods_id;
        if ($hasIF && $this->data['config']['includeIF']) {
            if (isset($this->data['report_final'])) {
                $this->Cell(7, $this->_h_c, $this->data['report_final']->rating, 1, 0, 'C');
                $this->Cell(7, $this->_h_c, '', 1, 0, 'C');
                return 0;

            }
        }

        $this->Cell(7, $this->_h_c, "", 1, 0, 'C');
        $this->Cell(7, $this->_h_c, "", 1, 0, 'C');
    }

    /**
     *
     *
     */
    private function showPeriodPositions($period)
    {
        $position = (isset($period['average']['position'])) ? $period['average']['position'] : null;

        $this->Cell(6, $this->_h_c, '', 1, 0, 'C');

        $this->Cell(8, $this->_h_c, $position, 1, 0, 'C');

        $this->Cell(8, $this->_h_c, '', 1, 0, 'C');
    }

    /**
     *
     *
     */
    private function showValueScale()
    {
        $this->Ln($this->_h_c * 2);

        $this->Cell(0, $this->_h_c, $this->hideTilde('ESCALA DE VALORACIÓN:'), 0, 0, '');
        $this->Ln($this->_h_c);

        $this->SetFont('Arial', '', 8);
        foreach ($this->data['valueScale'] as $key => $scale):

            $this->Cell(0, $this->_h_c, $this->hideTilde("{$scale->name}: {$scale->rank_start} A {$scale->rank_end}"), 0, 0, '');
            $this->Ln($this->_h_c);
        endforeach;
    }


    /**
     *
     *
     */
    private function showGeneralObservation()
    {
        $hasIF = count($this->data['periods']) == $this->data['current_period']->periods_id;

        if ($hasIF && $this->data['config']['includeIF']) {
            $this->ln();
            if (isset($this->data['report_final'])) {
                if ($this->data['report_final']->news_id == 39)
                    $message = 'SI APROBÓ EL GRADO';
                if ($this->data['report_final']->news_id == 45)
                    $message = 'NO APROBÓ EL GRADO';
                $this->Cell(0, $this->_h_c, $this->hideTilde('DECISIONES DE LA COMISIÓN DE EVALUACIÓN Y PROMOCIÓN'), 0, 0, 'L');
                $this->ln();
                $this->Cell(0, $this->_h_c, $this->hideTilde($message), 0, 1, 'L');
                /*
                if ($this->reprobated_tecn) {
                    $message = 'DEBE CAMBIAR DE INSTITUCIÓN POR HABER REPROBADO EL ÁREA TÉCNICA';
                    $this->Cell(0, $this->_h_c, $this->hideTilde($message), 0, 0, 'L');
                    $this->reprobated_tecn = false;
                }
                */
                if ($this->data['report_final']->news_name != null) {
                    $message = $this->data['report_final']->news_name;
                    $this->Cell(0, $this->_h_c, $this->hideTilde(strtoupper($message)), 0, 0, 'L');
                    $this->reprobated_tecn = false;
                }
            }
        }

        $this->Ln($this->_h_c * 2);

        $this->SetFont('Arial', 'B', 8);
        $this->Cell(0, $this->_h_c, 'OBSERVACIONES GENERALES:', 0, 0, 'L');

        if (is_null($this->data['general_observation'])):
            // MOSTRAMOS LAS LINEAS
            $this->Ln($this->_h_c * 1.5);
            $this->Cell(190, $this->_h_c, '', 'B', 0, 'L');

            $this->Ln($this->_h_c * 1.5);
            $this->Cell(190, $this->_h_c, '', 'B', 0, 'L');

            $this->Ln($this->_h_c * 1.5);
            $this->Cell(190, $this->_h_c, '', 'B', 0, 'L');
        else:

            $this->Ln();
            $this->determineCell($this->hideTilde($this->data['general_observation']->observation), 0);

        endif;

        $this->Ln($this->_h_c * 4);

        if (!is_null($this->data['director'])) {
            // DIRECTOR DE GRUPO
            $this->SetFont('Arial', 'B', 8);
            $this->Cell(0, 4, $this->hideTilde('DIR. DE GRUPO: ' . $this->data['director']->fullName), 0, 0, 'L');

            $this->Ln();

            $this->SetFont('Arial', '', 8);
            $this->Cell(0, $this->_h_c, 'DIRECTOR DE GRUPO', 0, 0);
        }
    }

    /**
     *
     */
    private function showTotalAttendanceByPeriod()
    {

        $this->Ln($this->_h_c);

        $noAttendace = 0;

        foreach ($this->data['periods'] as $key => $period):

            if ($this->data['current_period']->periods_id == $period['periods_id']):

                foreach ($period['areas'] as $keyArea => $area) {
                    foreach ($area['asignatures'] as $keyAsignature => $asignature) {
                        if (isset($asignature['final_note']['noAttendances']))
                            $noAttendace += $asignature['final_note']['noAttendances'];
                    }
                }
            endif;

        endforeach;

        if ($noAttendace > 0):

            $this->SetFont('Arial', '', 8);

            $this->Cell(53, $this->_h_c, "Faltas de asistencia durante el periodo {$this->data['current_period']->periods_id}: ", 0, 0);

            $this->SetFont('Arial', 'B', 8);

            $this->Cell(0, $this->_h_c, $noAttendace, 0, 0);

        endif;
    }

    /**
     *
     *
     */
    private function DoubleFace()
    {
        if ($this->PageNo() % 2 != 0 && $this->PageNo() >= 1):
            $this->AddPage();
        endif;
    }

    function footer()
    {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);

        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Número de página
        $this->Cell(0, 5, $this->hideTilde('@tenea - Página ') . $this->PageNo(), 0, 0, 'C');
    }
}
