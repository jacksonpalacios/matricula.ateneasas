<div id="sidebar-wrapper" >
	<ul class="sidebar-nav nav-pills nav-stacked" id="menu">
		<!-- Inicio -->
		<li>
            <a href="{{route('admin.home')}}"> 
            	<span class="fa-stack fa-lg pull-left">
            		<i class="fa fa-home fa-stack-1x "></i>
            	</span>
            	Inicio
            </a>
        </li>
		
        <!-- Grupo -->
         <li class="">
            <a data-toggle="collapse" data-parent="#menu" href="#collapse-headquarter" aria-expanded="true" aria-controls="collapse-headquarter">
                <span class="fa-stack fa-lg pull-left"><i class="fa fa-building fa-stack-1x "></i>
                </span> 
                Instituciones
            </a>
            <ul id="collapse-headquarter" class="nav-pills nav-stacked collapse collapseable" style="list-style-type:none;">
                <li><a href="{{url('admin/institution/create')}}">Crear</a></li>
                <li><a href="{{route('institution.index')}}">Ver</a></li>
            </ul>
        </li>

        {{-- Areas --}}
        <li class="">
            <a data-toggle="collapse" data-parent="#menu" href="#areas_asig" aria-expanded="true" aria-controls="areas_asig">
                <span class="fa-stack fa-lg pull-left"><i class="fa fa-book fa-stack-1x "></i>
                </span> 
                Areas y Asignaturas
            </a>
            <ul id="areas_asig" class="nav-pills nav-stacked collapse collapseable" style="list-style-type:none;">
                <li><a href="">Areas</a></li>
                <li><a href="">Asignaturas</a></li>
            </ul>
        </li>
        
        {{-- Carga de Archivos --}}
        <li class="">
            <a data-toggle="collapse" data-parent="#menu" href="#import_files" aria-expanded="true" aria-controls="import_files">
                <span class="fa-stack fa-lg pull-left"><i class="fa fa-upload fa-stack-1x "></i>
                </span> 
                Carga de archivos
            </a>
            <ul id="import_files" class="nav-pills nav-stacked collapse collapseable" style="list-style-type:none;">
                <li><a href="{{route('import.old_students.form')}}">Estudiantes antigüos</a></li>
                <li><a href="{{route('import.old_teachers.form')}}">Docentes antigüos</a></li>
            </ul>
        </li>
		<!-- Inscripción -->
{{--        <li class="">
            <a data-toggle="collapse" data-parent="#menu" href="#collapse-inscription" aria-expanded="true" aria-controls="collapse-inscription">
                <span class="fa-stack fa-lg pull-left"><i class="fa fa-archive fa-stack-1x "></i>
                </span> 
                Matricula
            </a>
            <ul id="collapse-inscription" class="nav-pills nav-stacked collapse collapseable" style="list-style-type:none;">
                <li><a href="{{route('student.create')}}">Crear</a></li>
                <li><a href="{{route('enrollment.lists', 1)}}">Ver</a></li>
                <li><a href="{{route('enrollment.enrollments.grade')}}">Ficha Académica</a></li>
            </ul>
        </li>
        <!-- Grupo -->
        <li class="">
            <a data-toggle="collapse" data-parent="#menu" href="#collapse-group" aria-expanded="true" aria-controls="collapse-group">
                <span class="fa-stack fa-lg pull-left"><i class="fa fa-users fa-stack-1x "></i>
                </span> 
                Grupo
            </a>
            <ul id="collapse-group" class="nav-pills nav-stacked collapse collapseable" style="list-style-type:none;">
                <li><a href="{{route('group.create')}}">Crear</a></li>
                <li><a href="{{route('group.index')}}">Ver grupos</a></li>
            </ul>
        </li>
	</ul> --}}
</div>