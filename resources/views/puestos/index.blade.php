@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Gestión de puestos</h1>
@endsection

@section('styles')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{url('/')}}"><i class="fa fa-home"></i> </a></li>
        <li class="breadcrumb-item">puestos</li>
        {{--  <li class="breadcrumb-item"><a href="{{url('/users')}}">Usuarios</a></li>
        <li class="breadcrumb-item active">Editar usuario {{ !empty($users->name) ? $users->name : '' }}</li>  --}}
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
            <div class="btn-group btn-group-sm pull-right" role="group">
                    <a href="#" id="btn_nueva_puesto" class="btn btn-success" title="Nueva puesto">
                    <i class="fa fa-plus-square pt-2" style="font-size: 20px" aria-hidden="true"></i>
                    <span>Nuevo</span>
                </a>
            </div>
        </div>
    </div>
    <div id="editorCAM" class="mt-2">

    </div>
    <div class="row mt-2">
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title">Puestos</h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive w-100">
                    <table class="table table-striped table-hover table-vcenter  dataTable " id="tablapuestos">
                        <thead>
                            <tr>
                                <th style="width: 10px" class="no-sort"><input type="checkbox" class="form-control custom-control-input magic-checkbox" name="chktodos" id="chktodos"><label  class="custom-control-label"  for="chktodos"></label></th>
                                <th style="width: 20px" class="no-sort"></th>
                                <th>Puesto</th>
                                <th>Edificio</th>
                                <th>Planta</th>
                                <th class="text-center" style="width: 100px">Estado</th>
                                <td class="no-sort"></td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($puestos as $puesto)
                            <tr class="hover-this">
                                <td class="text-center">
                                    <input type="checkbox" class="form-control chkpuesto magic-checkbox" name="chk{{ $puesto->id_puesto }}" id="chk{{ $puesto->id_puesto }}">
                                    <label class="custom-control-label"   for="chk{{ $puesto->id_puesto }}"></label>
                                </td>
                                <td class="thumb text-center" data-id="{{ $puesto->id_puesto }}" >
                                    @isset($puesto->val_icono)
                                        <i class="{{ $puesto->val_icono }} fa-2x" style="color:{{ $puesto->val_color }}"></i>
                                    @endisset
                                </td>
                                <td class="td" data-id="{{ $puesto->id_puesto }}"><b>{{$puesto->cod_puesto}}</b> - {{$puesto->des_puesto}}</td>
                                <td class="td" data-id="{{ $puesto->id_puesto }}">{{ $puesto->des_edificio }}</td>
                                <td class="td" data-id="{{ $puesto->id_puesto }}">{{$puesto->des_planta}}</td>
                                <td class="td text-center" data-id="{{ $puesto->id_puesto }}">
                                    @switch($puesto->id_estado)
                                        @case(1)
                                            <div class="bg-success" style="width: 100%; height: 100%;">
                                            @break
                                        @case(2)
                                            <div class="bg-danger" style="width: 100%; height: 100%;">
                                            @break
                                        @case(3)
                                            <div class="bg-info" style="width: 100%; height: 100%;">
                                            @break
                                        @case(4)
                                            <div class="bg-secondary" style="width: 100%; height: 100%;">
                                            @break
                                        @default
                                    @endswitch
                                    {{$puesto->des_estado}}
                                    </div>
                                </td>
                                
                                <td style="position: relative;">
                                    <div class="btn-group btn-group-xs pull-right floating-like-gmail" role="group">
                                        <a href="#"  class="btn btn-primary btn_editar add-tooltip thumb"  title="Ver puesto" data-id="{{ $puesto->id_puesto }}"> <span class="fa fa-eye" aria-hidden="true"></span></a>
                                        <a href="#"  class="btn btn-info btn_editar add-tooltip" onclick="editar({{ $puesto->id_puesto }})" title="Editar puesto" data-id="{{ $puesto->id_puesto }}"> <span class="fa fa-pencil pt-1" aria-hidden="true"></span></a>
                                        <a href="#eliminar-puesto-{{$puesto->id_puesto}}" data-target="#eliminar-puesto-{{$puesto->id_puesto}}" title="Borrar puesto" data-toggle="modal" class="btn btn-danger add-tooltip btn_del"><span class="fa fa-trash" aria-hidden="true"></span></a>
                                    </div>
                                    <div class="btn-group btn-group-xs pull-right floating-like-gmail" role="group">
                                        <a href="#"  class="btn btn-success btn_editar add-tooltip thumb"  title="Poner como disponible" data-id="{{ $puesto->id_puesto }}"> <span class="fad fa-thumbs-up" aria-hidden="true"></span></a>
                                        <a href="#"  class="btn btn-danger btn_editar add-tooltip thumb"  title="Poner como usado" data-id="{{ $puesto->id_puesto }}"> <span class="fad fa-lock-alt" aria-hidden="true"></span></a>
                                        <a href="#"  class="btn btn-info btn_editar add-tooltip thumb"  title="Poner para limpiar" data-id="{{ $puesto->id_puesto }}"> <span class="fad fa-broom" aria-hidden="true"></span></a>
                                    </div>
                                    <div class="modal fade" id="eliminar-puesto-{{$puesto->id_puesto}}" style="display: none;">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                            <div class="modal-header">

                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">×</span></button>
                                                    <div><img src="/img/onthespot_20.png" class="float-right"></div>
                                                    <h4 class="modal-title">¿Borrar puesto {{$puesto->des_puesto}}?</h4>
                                                </div>
                                                <div class="modal-footer">
                                                    <a class="btn btn-info" href="{{url('/puestos/delete',$puesto->id_puesto)}}">Si</a>
                                                    <button type="button" data-dismiss="modal" class="btn btn-warning">No</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

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

@section('scripts')
<script>
	$('#btn_nueva_puesto').click(function(){
       $('#editorCAM').load("{{ url('/puestos/edit/0') }}", function(){
		animateCSS('#editorCAM','bounceInRight');
	   });
	  // window.scrollTo(0, 0);
      //stopPropagation()
	});

	function editar(id){
        $('#editorCAM').load("{{ url('/puestos/edit/') }}"+"/"+id, function(){
			animateCSS('#editorCAM','bounceInRight');
		});
    }

    $('.td').click(function(event){
        editar( $(this).data('id'));
    })


    $('.dataTable').dataTable({
        "lengthChange": false,
        "pageLength":100,
        "responsive": true,
        "bSort": true,
        "language": {
            "paginate": {
              "previous": '<i class="demo-psi-arrow-left"></i>',
              "next": '<i class="demo-psi-arrow-right"></i>'
            }
        },
        columnDefs: [ { targets: 'no-sort', orderable: false } ],
    });

    $("#chktodos").click(function(){
        $('.chkpuesto').not(this).prop('checked', this.checked);
    });


</script>
@endsection
