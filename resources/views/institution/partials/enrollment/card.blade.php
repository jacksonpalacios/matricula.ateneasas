@extends('institution.dashboard.index')



@section('breadcrums')
    <ol class="breadcrumb">
        <li class="active">Ficha Académica</li>
    </ol>
@endsection

@section('content')
    <div class="row">
        <div id="app">

        </div>
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading clearfix">
                    <div class="pull-right">
                        <a href="{{route('student.create')}}" class="btn btn-primary btn-sm">
                            Crear Estudiante</a>
                    </div>
                    @include('institution.partials.enrollment.optionsEnrollmentCard')

                </div>


                <div class="panel-body">
                    @if(isset($grades))
                        {!! Form::open(['route' => 'enrollment.card.generate', 'method' => 'post', 'files' => true]) !!}
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('grade_id', 'Seleccione un Grado')!!}
                                    <select name="grade_id" id="grade_id" class="form-control chosen-select">
                                        <option value="">Seleccione un Grado</option>
                                        @foreach($grades as $grade)
                                            <option value="{{$grade->id}}"
                                                    value="{{old('grade_id')}}">{{$grade->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            {{--
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('school_year', 'Año Lectivo')!!}
                            {!! Form::select('school_year', [], old('school_year'), ['class'=>'form-control chosen-select',
                            'placeholder'=>'Seleccione un año']) !!}
                                    </div>
                                </div>
                            --}}
                            <div class="col-md-3">
                                <div class="form-group" style="padding-top: 25px;">
                                    <input type="hidden" name="typecard" value="byGrade">
                                    {!! Form::submit('Generar Fichas', ['class'=>'btn btn-primary']) !!}
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    @endif


                    @if(isset($groups))
                        {!! Form::open(['route' => 'enrollment.card.generate', 'method' => 'post', 'files' => true]) !!}
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('group_id', 'Seleccione un Grupo')!!}
                                    <select name="group_id" id="group_id" class="form-control chosen-select">
                                        <option value="">Seleccione un Grupo</option>
                                        @foreach($groups as $group)
                                            <option value="{{$group->id}}"
                                                    value="{{old('group_id')}}">{{$group->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" style="padding-top: 25px;">
                                    <input type="hidden" name="typecard" value="byGroup">
                                    {!! Form::submit('Generar Fichas', ['class'=>'btn btn-primary']) !!}
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    @endif

                    @if(isset($student))


                        <div class="row">
                            <div class="col-md-12">
                                <p>
                                    Buscar por documento de estudiantes
                                </p>
                            </div>
                            <div class="col-md-6">
                                {!! Form::open(['route' => 'enrollment.card.student', 'method' => 'get', 'files' => true]) !!}
                                <div class="form-group">
                                    {!! Form::label('grade_id', 'Documento Estudiante')!!}
                                    <input type="text" id="name_id" class="form-control">
                                </div>
                                {!! Form::close() !!}
                            </div>

                            <div class="col-md-12">
                                <div id="container-student">

                                </div>
                            </div>
                        </div>

                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{asset('js/bootstrap-datepicker.js')}}"></script>
    <script src="{{asset('js/jquery.min.js')}}"></script>
    <script type="text/javascript">


        $('#name_id').keyup(function () {

            var text = $('#name_id').val();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({

                type: "POST",
                url: '{!!URL::route('enrollment.autocomplete')!!}',
                data: {text: $('#name_id').val()},
                success: function (data) {
                    $('#container-student')[0].innerHTML = data;
                }
            });

        })

    </script>
@endsection
