<?php

namespace App\Http\Controllers\Teacher;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use Auth;

use App\Group;
use App\Teacher;
use App\Student;
use App\Asignature;
use App\Period;
use App\NotesFinal;
use App\ScaleEvaluation;

class RecoveryController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Group $group, Asignature $asignature, Period $period)
    {
        $institution = $group->headquarter->institution;

        $scaleEvaluations = ScaleEvaluation::getMinScale($institution);

        // dd( $scaleEvaluations );
        $students = $group->recovery($asignature, $period, $scaleEvaluations);

        return $this->showAll($students);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $institution = Group::findOrFail($request->group_id)->headquarter->institution;

        $noteFinal = NotesFinal::findOrFail($id);
        
        $noteFinal->fill($request->all());
        $response = $noteFinal->updateOvercoming($institution);

        return response()->json($response, $response['code']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function byGroup(Group $group, Asignature $asignature)
    {
        $institution = $group->headquarter->institution;

        $periods = $institution->periods()
        ->with('period')
        ->with('schoolyear')
        ->with('state')
        ->with('workingday')
        ->get()
        ->pluck('period')
        ->unique()
        ->values()
        ->pluck('fullName','id');

        return View('teacher.partials.recovery.index')
        ->with('group',$group)
        ->with('asignature',$asignature)
        ->with('periods',$periods);
    }
}
