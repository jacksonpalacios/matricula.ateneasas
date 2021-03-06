<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class GroupPensum extends Model
{
    protected $table = "group_pensum";

    // protected $primaryKey = 'code_group_pensum';

    protected $fillable = [
        'code_group_pensum', 'percent', 'description', 'ihs', 'order', 'group_id', 'asignatures_id', 'areas_id', 'subjects_type_id', 'teacher_id', 'schoolyear_id'
    ];

    /**
     * Obtiene la relacion que hay entre el docente y los grupos la asignatura (Pensum)
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    /**
     * Obtiene la relacion que hay entre el pensum y el grupo
     */
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    /**
     * Obtiene la relacion que hay entre el pensum y la asignatura
     */
    public function asignature()
    {
        return $this->belongsTo(Asignature::class, 'asignatures_id');
    }

    /**
     * Obtiene la relacion que hay entre el pensum y el tipo de la asignatura
     */
    public function subjectType()
    {
        return $this->belongsTo(SubjectType::class, 'subjects_type_id');
    }

    /**
     * Obtiene la relacion que hay entre el pensum y el area
     */
    public function area()
    {
        return $this->belongsTo(Area::class, 'areas_id');
    }

    /**
     * Obtiene la relacion que hay entre el pensum y el area
     */
    public function schoolYear()
    {
        return $this->belongsTo(Schoolyear::class, 'schoolyear_id');

    }

    public function noteParameterPerformance()
    {
        return $this->hasMany(NotesParametersPerformances::class, 'group_pensum_id');
    }

    public function performances()
    {
        return $this->hasMany(GroupPensumPerformances::class, 'group_pensum_id');
    }

    public static function getByGroup($group_id)
    {

        $pensum = DB::table('group_pensum')
            ->select(
                'group_pensum.id AS pensum_id',
                'group_pensum.asignatures_id AS asignature_id', 'group_pensum.subjects_type_id AS subjects_type_id',
                'group_pensum.areas_id AS area_id', 'group_pensum.teacher_id AS teacher_id',
                'asignatures.name as name_asignatures',
                'subjects_type.name as subjects_type_name',
                DB::raw('CONCAT(managers.last_name," ",managers.name) as name_teachers'),
                'group_pensum.order', 'group_pensum.percent', 'group_pensum.ihs')
            ->join('asignatures', 'asignatures.id', '=', 'group_pensum.asignatures_id')
            ->join('subjects_type', 'subjects_type.id', '=', 'group_pensum.subjects_type_id')
            ->leftJoin('teachers', 'teachers.id', '=', 'group_pensum.teacher_id')
            ->leftJoin('managers', 'managers.id', '=', 'teachers.manager_id')
            ->where('group_pensum.group_id', '=', $group_id)
            ->where('schoolyear_id', '=', 1)
            ->get();
        return $pensum;
    }

    public static function find($group_id)
    {
        return self::select(
            'asignatures.abbreviation', 'asignatures.name',
            'group_pensum.asignatures_id', 'group_pensum.ihs', 'group_pensum.order', 'group_pensum.percent')
            ->join('asignatures', 'asignatures.id', '=', 'group_pensum.asignatures_id')
            ->where('group_pensum.group_id', '=', $group_id)
            ->get();
    }

    public static function getAsignaturesByGroup($params)
    {
        return $asignatures = self::select(
            'asignatures.abbreviation', 'asignatures.name',
            'group_pensum.asignatures_id', 'group_pensum.ihs', 'group_pensum.order', 'group_pensum.percent')
            ->join('asignatures', 'asignatures.id', '=', 'group_pensum.asignatures_id')
            ->where('group_pensum.group_id', '=', $params->group_id)
            ->get();
    }

    public static function getAreasByGroup($params)
    {
        return $asignatures = self::select(
            'areas.abbreviation', 'areas.name',
            'group_pensum.areas_id as asignatures_id', 'group_pensum.ihs')
            ->join('areas', 'areas.id', '=', 'group_pensum.areas_id')
            ->where('group_pensum.group_id', '=', $params->group_id)
            ->groupBy('areas.id')
            ->get();
    }

    //Diferencia -> parametro
    public static function getAsignaturesByGroupX($group_id)
    {
        return $asignatures = self::select(
            'asignatures.abbreviation', 'asignatures.name',
            'group_pensum.asignatures_id', 'group_pensum.ihs', 'asignatures.subjects_type_id',
            'group_pensum.order', 'group_pensum.percent')
            ->join('asignatures', 'asignatures.id', '=', 'group_pensum.asignatures_id')
            ->where('group_pensum.group_id', '=', $group_id)
            ->get();
    }

    public static function getAreasByGroupX($group_id)
    {
        return $asignatures = self::select(
            'areas.abbreviation', 'areas.name',
            'group_pensum.areas_id as asignatures_id', 'group_pensum.ihs', 'areas.subjects_type_id')
            ->join('areas', 'areas.id', '=', 'group_pensum.areas_id')
            ->where('group_pensum.group_id', '=', $group_id)
            ->groupBy('areas.id')
            ->get();
    }


    //Para Controlador Evaluation

    public static function getAsignaturesByInstitution($institution_id)
    {
        return $asignatures = self::select(
            'asignatures.id as asignature_id', 'asignatures.name as asignature_name',
            'areas.id as area_id', 'areas.name as area_name',
            'group.id as group_id', 'group.name as group_name',
            'grade.id as grade_id', 'grade.name as grade_name', 'group_pensum.subjects_type_id',
            'group_pensum.id as group_pensum_id',
            'headquarter.id as headquarter_id', 'headquarter.name as headquarter_name'
        )
            ->join('asignatures', 'asignatures.id', '=', 'group_pensum.asignatures_id')
            ->join('areas', 'areas.id', '=', 'group_pensum.areas_id')
            ->join('group','group.id','=','group_pensum.group_id')
            ->join('grade','grade.id','=','group.grade_id')
            ->join('headquarter','headquarter.id','=','group.headquarter_id')
            ->where('headquarter.institution_id','=',$institution_id)
            ->get();
    }

    public static function getAreasByInstitution($institution_id)
    {
        return $asignatures = self::select(
            'areas.name as area_name', 'areas.id as area_id','group.id as group_id')
            ->join('areas', 'areas.id', '=', 'group_pensum.areas_id')
            ->join('group','group.id','=','group_pensum.group_id')
            ->join('headquarter','headquarter.id','=','group.headquarter_id')
            ->where('headquarter.institution_id','=',$institution_id)
            ->groupBy('group.id','areas.id')
            ->get();
    }

    public static function getAsignaturesByTeacherInstitution($institution_id ,$teacher_id)
    {
        return $asignatures = self::select(
            'asignatures.id as asignature_id', 'asignatures.name as asignature_name',
            'areas.id as area_id', 'areas.name as area_name',
            'group.id as group_id', 'group.name as group_name',
            'grade.id as grade_id', 'grade.name as grade_name', 'group_pensum.subjects_type_id',
            'group_pensum.id as group_pensum_id',
            'headquarter.id as headquarter_id', 'headquarter.name as headquarter_name'
            )
            ->join('asignatures', 'asignatures.id', '=', 'group_pensum.asignatures_id')
            ->join('areas', 'areas.id', '=', 'group_pensum.areas_id')
            ->join('group','group.id','=','group_pensum.group_id')
            ->join('grade','grade.id','=','group.grade_id')
            ->join('headquarter','headquarter.id','=','group.headquarter_id')
            ->where('group_pensum.teacher_id', '=', $teacher_id)
            ->where('headquarter.institution_id','=',$institution_id)
            ->get();
    }

    public static function getAreasByTeacherInstitution($teacher_id)
    {
        return $asignatures = self::select(
            'areas.name as area_name', 'areas.id as area_id','group_pensum.group_id')
            ->join('areas', 'areas.id', '=', 'group_pensum.areas_id')
            ->where('group_pensum.teacher_id', '=', $teacher_id)
            ->groupBy('group_pensum.group_id','areas.id')
            ->get();
    }

    public static function getAsignaturesByTeacher($teacher_id)
    {
        return $asignatures = self::select(
            'asignatures.id', 'asignatures.abbreviation', 'asignatures.name', 'asignatures.subjects_type_id',
            'asignatures.created_at', 'asignatures.updated_at')
            ->join('asignatures', 'asignatures.id', '=', 'group_pensum.asignatures_id')
            ->where('group_pensum.teacher_id', '=', $teacher_id)
            ->groupBy('group_pensum.asignatures_id')
            ->get();
    }


}
