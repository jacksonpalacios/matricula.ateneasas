@extends('institution.dashboard.index')

@section('css')
<link rel="stylesheet" href="{{asset('css/bootstrap-chosen.css')}}">
<link rel="stylesheet" href="{{asset('css/datepicker.css')}}">
@endsection

@section('breadcrums')
<ol class="breadcrumb">
	<li><a href="">Funcionarios</a></li>
	<li><a href="{{route('institution.list.teacher')}}">Docentes</a></li>
	<li class="active">Editar</li>
</ol>
@endsection

@section('content')
<div class="row">
	<div class="col-md-12">

		{!! Form::open(['route'=>['manager.update', $manager], 'method'=>'put'])!!}
		<div class="panel panel-default">

			<div class="panel-body">

				@include('complements.error')

				<div class="container-fluid">
					<div id="identification" class="section_inscription">
						<div class="section_inscription__tittle">
		  					<h4>Datos de Identificación</h4>
		  				</div>
						<div class="row">
							<div class="col-md-3">
								<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
									{!! Form::label('name', 'Nombres') !!}
									{!! Form::text('name', $manager->name, ['class'=>'form-control']) !!}
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
									{!! Form::label('last_name', 'Apellidos') !!}
									{!! Form::text('last_name', $manager->last_name, ['class'=>'form-control']) !!}
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group{{ $errors->has('identification_type_id') ? ' has-error' : '' }}">
									{!! Form::label('identification_type_id', 'Tipo de identificación') !!}
									{!! Form::select('identification_type_id', $identification_types, $manager->identification->identification_type_id, ['class'=>'form-control chosen-select', 'placeholder' => 'Seleccione']) !!}
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group{{ $errors->has('identification_number') ? ' has-error' : '' }}">
									{!! Form::label('identification_number', 'Número de identificación') !!}
									{!! Form::text('identification_number', $manager->identification->identification_number, ['class'=>'form-control']) !!}
								</div>
							</div>
						</div>
						<div class="row">
							<!--  CITY  EXPEDITION / BIRTH-->
							<div class="col-md-3">
								<div class="form-group{{ $errors->has('id_city_expedition') ? ' has-error' : '' }}">
									{!! Form::label('id_city_expedition', 'Ciudad de expedición') !!}
									{!! Form::select('id_city_expedition', $cities, $manager->identification->id_city_expedition, ['class'=>'form-control chosen-select', 'placeholder'=>'Selecciona una ciudad']) !!}
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group{{ $errors->has('id_city_of_birth') ? ' has-error' : '' }}">
									{!! Form::label('id_city_of_birth', 'Ciudad de nacimiento') !!}
									{!! Form::select('id_city_of_birth', $cities, $manager->identification->id_city_of_birth, ['class'=>'form-control chosen-select', 'placeholder'=>'Selecciona una ciudad']) !!}
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group{{ $errors->has('birthdate') ? ' has-error' : '' }}">
									{!! Form::label('birthdate', 'Fecha de nacimiento') !!}
									{!! Form::text('birthdate', $manager->identification->birthdate, ['class'=>'form-control datepicker']) !!}
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group{{ $errors->has('gender_id') ? ' has-error' : '' }}">
									{!! Form::label('gender_id', 'Genero') !!}
									{!! Form::select('gender_id', $genders, $manager->identification->gender_id, ['class'=>'form-control chosen-select', 'placeholder'=>'seleccione un genero']) !!}
								</div>
							</div>
						</div>
					</div>

					<div id="identification" class="section_inscription">
		  				<div class="section_inscription__tittle">
		  					<h4>Dirección Residencia</h4>
		  				</div>
						<div class="row">
							<div class="col-md-4">
								<div class="form-group {{ $errors->has('address') ? ' has-error' : '' }}">
									{!! Form::label('address', 'Dirección') !!}
									{!! Form::text('address', $manager->address->address, ['class' => 'form-control', 'id'=>'adress']) !!}
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group {{ $errors->has('neighborhood') ? ' has-error' : '' }}">
									{!! Form::label('neighborhood', 'Barrio') !!}
									{!! Form::text('neighborhood', $manager->address->neighborhood, ['class' => 'form-control', 'id'=>'neighborhood']) !!}
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group {{ $errors->has('id_city_address') ? ' has-error' : '' }}">
									{!! Form::label('id_city_address', 'Ciudad') !!}
									{!! Form::select('id_city_address', $cities, $manager->address->id_city_address, ['class'=>'form-control chosen-select', 'placeholder'=>'seleccione una ciudad']) !!}
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group {{ $errors->has('zone_id') ? ' has-error' : '' }}">
									{!! Form::label('zone_id', 'Zona (Urbana/Rural)') !!}
									{!! Form::select('zone_id', $zones, $manager->address->zone_id, ['class'=>'form-control chosen-select', 'placeholder'=>'seleccione una zona']) !!}
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4">
								<div class="form-group {{ $errors->has('phone') ? ' has-error' : '' }}">
									{!! Form::label('phone', 'Telefono') !!}
									{!! Form::text('phone', $manager->address->phone, ['class' => 'form-control', 'id'=>'phone']) !!}
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group {{ $errors->has('mobil') ? ' has-error' : '' }}">
									{!! Form::label('mobil', 'Celular') !!}
									{!! Form::text('mobil', $manager->address->mobil, ['class' => 'form-control', 'id'=>'mobil']) !!}
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
									{!! Form::label('email', 'Email') !!}
									{!! Form::text('email', $manager->address->email, ['class' => 'form-control', 'id'=>'email']) !!}
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="panel-footer">
				<div class="form-group text-center">
					{!! Form::submit('Actualizar', ['class'=>'btn btn-primary']) !!}
				</div>
			</div>
		</div>
		{!! Form::close()!!}
	</div>
</div>
@endsection

@section('js')
<script src="{{asset('js/chosen.jquery.js')}}"></script>
<script src="{{asset('js/bootstrap-datepicker.js')}}"></script>
<script>
	$(function() {
		$('.chosen-select').chosen({width: "100%"});
		$('.chosen-select-deselect').chosen({ allow_single_deselect: true });
		$('.datepicker').datepicker({
		    format: 'yyyy/mm/dd',
		    startDate: '-3d'
		});
	});
</script>
@endsection