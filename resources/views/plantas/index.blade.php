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
        <li class="breadcrumb-item">edificios</li>
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
                    <a href="#" id="btn_nueva_puesto" class="btn btn-success" title="Nueva planta">
                    <i class="fa fa-plus-square pt-2" style="font-size: 20px" aria-hidden="true"></i>
                    <span>Nuevo</span>
                </a>
            </div>
        </div>
    </div>
    <div id="editorCAM" class="mt-2">

    </div>
    @if(Session::has('success_message'))
        <div class="alert alert-success">
            <span class="glyphicon glyphicon-ok"></span>
            {!! session('success_message') !!}
            <button type="button" class="close" data-dismiss="alert" aria-label="close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="panel">
        
        @if(count($plantasObjects) == 0)
            <div class="panel-body text-center">
                <h4>No Plantas Available.</h4>
            </div>
        @else
        <div class="panel-heading">
            <h3 class="panel-title">Plantas</h3>
        </div>
        <div class="panel-body panel-body-with-table">
            <div class="table-responsive w-100">

                <table class="table table-striped dataTable"  style="width: 98%"> 
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Planta</th>
                            <th>Edificio</th>
                            <th>Cliente</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($plantasObjects as $plantas)
                        <tr class="hover-this">
                            <td>{{ $plantas->id_planta }}</td>
                            <td>{{ $plantas->des_planta }}</td>
                            <td>{{ $plantas->des_edificio }}</td>
                            <td>{{ $plantas->nom_cliente }}</td>

                            <td>
                                <div class="btn-group btn-group-xs pull-right floating-like-gmail" role="group">
                                    {{-- <a href="#"  class="btn btn-primary btn_editar add-tooltip thumb"  title="Ver planta" data-id="{{ $plantas->id_planta }}"> <span class="fa fa-eye" aria-hidden="true"></span></a> --}}
                                    <a href="#"  class="btn btn-info btn_editar add-tooltip" onclick="editar({{ $plantas->id_planta }})" title="Editar planta" data-id="{{ $plantas->id_planta }}"> <span class="fa fa-pencil pt-1" aria-hidden="true"></span></a>
                                    <a href="#eliminar-planta-{{$plantas->id_planta}}" data-target="#eliminar-planta-{{$plantas->id_planta}}" title="Borrar planta" data-toggle="modal" class="btn btn-danger add-tooltip btn_del"><span class="fa fa-trash" aria-hidden="true"></span></a>
                                </div>
                                <div class="modal fade" id="eliminar-planta-{{$plantas->id_planta}}" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                        <div class="modal-header">

                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">×</span></button>
                                                <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                                                <h4 class="modal-title">¿Borrar planta {{$plantas->des_planta}}?</h4>
                                            </div>
                                            <div class="modal-footer">
                                                <a class="btn btn-info" href="{{url('/plantas/delete',$plantas->id_planta)}}">Si</a>
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

        
        @endif
    
    </div>
@endsection

@section('scripts')
    <script>
        $('#btn_nueva_puesto').click(function(){
            $('#editorCAM').load("{{ url('/plantas/create') }}", function(){
                animateCSS('#editorCAM','bounceInRight');
            });
            // window.scrollTo(0, 0);
            //stopPropagation()
        });

        function editar(id){
            $('#editorCAM').load("{{ url('/plantas/edit/') }}"+"/"+id, function(){
                animateCSS('#editorCAM','bounceInRight');
            });
        }

        $('.td').click(function(event){
            editar( $(this).data('id'));
        })
    </script>
@endsection