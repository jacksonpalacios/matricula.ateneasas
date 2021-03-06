@extends('institution.dashboard.index')


@section('breadcrums')
    <ol class="breadcrumb">
        <li class="active">Grupos</li>
    </ol>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading clearfix">


                    <a class="btn btn-success btn-sm" style="margin-right: 3px" href="{{route('group.assignment')
					}}">Asignar Grupos</a>

                    <a class="btn btn-primary btn-sm pull-right" href="{{route('group.create')}}">Crear Grupo</a>
                </div>
                <div class="panel-body">

                    <table class="table">
                        <thead>
                        <tr>
                            {{-- <th>#</th> --}}
                            <th>Sede</th>
                            <th>Grupo</th>
                            <th>Jornada</th>
                            <th>Evaluación</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($groups as $key => $group)
                            <tr>
                                {{-- <td> {{ ($key+1) }}</td> --}}
                                <td>{{ $group->headquarter->name }}</td>
                                <td>{{ $group->name }}</td>
                                <td>{{ $group->workingday->name }}</td>
                                <td>
                                    <div class='btn-group' role='group'>
                                        <button type='button' class='btn btn-primary dropdown-toggle'
                                                data-toggle='dropdown'
                                                aria-haspopup='true' aria-expanded='false'>
                                            Evaluación
                                            <span class='caret'></span>
                                        </button>
                                        <ul class='dropdown-menu'>
                                            <li>
                                                <a href="{{route('evaluation.index', [$group->id,'all'])}}">
                                                    Evaluar Periodo
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{route('institution.recovery', [$group->id])}}">Superaciones
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{route('institution.periodPending', [$group->id])}}">Evaluar
                                                    Periodo Pendiente
                                                </a>
                                            </li>
                                            <li><a href=''>Refuerzo Academico</a></li>
                                        </ul>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{route('group.edit', $group)}}" class="btn btn-primary btn-sm"><i
                                                class="fa fa-edit"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function () {

            $(".table").DataTable({
                "language": {
                    "url": "{{asset('plugin/DataTables/languaje/Spanish.json')}}"
                },
                "info": false,
                // "order": [2],
                "autoWidth": false,
            });
        });
    </script>
@endsection