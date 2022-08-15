@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Departamentos</h1>
@endsection

@section('styles')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{url('/')}}"><i class="fa fa-home"></i> </a></li>
        <li class="breadcrumb-item">configuracion</li>
		<li class="breadcrumb-item">parametrizacion</li>
		<li class="breadcrumb-item">personas</li>
        <li class="breadcrumb-item active">departamentos</li>
    </ol>
@endsection

@section('content')
<div class="row botones_accion">
    <div class="col-md-4">

    </div>
    <div class="col-md-7">
        <br>
    </div>
    <div class="col-md-1 text-right">
        @if(checkPermissions(['Departamentos'],['C']))
        <div class="btn-group btn-group-sm pull-right" role="group">
				<a href="#" class="btn float-right hidden-sm-down btn-success btn_nuevo">
                <i class="fa fa-plus-square pt-2" style="font-size: 20px" aria-hidden="true"></i>
                <span>Nuevo departamento</span>
            </a>
        </div>
        @endif
    </div>
</div>
<div id="editorCAM" class="mt-2">

</div>
<div class="panel">
    <div class="panel-heading">

    </div>
    <div class="panel-body">
		<div class="table-responsive">
			<table id="tabladeps"  data-toggle="table"
				data-locale="es-ES"
				data-search="true"
				data-show-columns="true"
				data-show-toggle="true"
				data-show-columns-toggle-all="true"
				data-page-list="[5, 10, 20, 30, 40, 50, 75, 100]"
				data-page-size="50"
				data-pagination="true" 
				data-show-pagination-switch="true"
				data-show-button-icons="true"
				data-toolbar="#all_toolbar"
				data-buttons-class="secondary"
				data-show-button-text="true"
				>
				<thead>
					<tr>
						<th data-sortable="true" style="width: 20px" >ID</th>
						<th data-sortable="true"  style="width: 60%" >Nombre</th>
						<th data-sortable="true" class="text-center" ><i class="fa-solid fa-user"></i></th>
					</tr>
				</thead>
				<tbody> 
					@php  
							$deps = lista_departamentos('global', 0, $r); 
							$idcl = 0;
					@endphp
					@if(isset($deps))
						@foreach ($deps as $d)
							@include('resources.fila_departamento')
						@endforeach
					@endif
				</tbody>
			</table>
		</div>
    </div>
</div>


@endsection


@section('scripts')
    <script>
        $('.configuracion').addClass('active active-sub');
		$('.menu_parametrizacion').addClass('active active-sub');
		$('.menu_usuarios').addClass('active active-sub');
        $('.departamentos').addClass('active-link');

		function editar(id){
			$('#editorCAM').load("{{ url('/departments/edit/') }}"+"/"+id, function(){
                animateCSS('#editorCAM','bounceInRight');
            });
		}

		$('.btn_nuevo').click(function(){
            $('#editorCAM').load("{{ url('/departments/create') }}", function(){
                animateCSS('#editorCAM','bounceInRight');
            });
            // window.scrollTo(0, 0);
            //stopPropagation()
        });
    </script>
@endsection
