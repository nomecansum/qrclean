@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Gestión de edificios</h1>
@endsection

@section('styles')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{url('/')}}"><i class="fa fa-home"></i> </a></li>
        <li class="breadcrumb-item">parametrización</li>
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
        @if(checkPermissions(['Edificios'],['C']))
        <div class="btn-group btn-group-sm pull-right" role="group">
                <a href="#" id="btn_nueva_puesto" class="btn btn-success" title="Nuevo edificio">
                <i class="fa fa-plus-square pt-2" style="font-size: 20px" aria-hidden="true"></i>
                <span>Nuevo</span>
            </a>
        </div>
        @endif
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

        <div class="panel-heading">
            <h3 class="panel-title">Edificios</h3>
        </div>
        
        @if(count($edificiosObjects) == 0)
            <div class="panel-body text-center">
                <h4>No Edificios Available.</h4>
            </div>
        @else
        <div class="panel-body panel-body-with-table">
            <div class="table-responsive w-100" >

                <table class="table table-bordered table-condensed table-hover dataTable" style="width: 98%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Cliente</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($edificiosObjects as $edificios)
                        <tr class="hover-this">
                            <td>{{ $edificios->id_edificio }}</td>
                            <td>{{ $edificios->des_edificio }}</td>
                            <td>{{ $edificios->nom_cliente }}</td>

                            <td>
                                <div class="pull-right floating-like-gmail" role="group">
                                    {{-- <a href="#"  class="btn btn-primary btn_editar add-tooltip thumb"  title="Ver planta" data-id="{{ $edificios->id_edificio }}"> <span class="fa fa-eye" aria-hidden="true"></span></a> --}}
                                    @if(checkPermissions(['Edificios'],['W']))<a href="#"  class="btn btn-xs btn-info btn_editar add-tooltip" onclick="editar({{ $edificios->id_edificio }})" title="Editar edificio" data-id="{{ $edificios->id_edificio }}"> <span class="fa fa-pencil pt-1" aria-hidden="true"></span> Edit</a>@endif
                                    @if(checkPermissions(['Edificios'],['D']))<a href="#eliminar-planta-{{$edificios->id_edificio}}" data-target="#eliminar-planta-{{$edificios->id_edificio}}" title="Borrar edificio" data-toggle="modal" class="btn btn-xs btn-danger add-tooltip btn_del"><span class="fa fa-trash" aria-hidden="true"></span> Del</a>@endif
                                </div>
                                <div class="modal fade" id="eliminar-planta-{{$edificios->id_edificio}}" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                        <div class="modal-header">

                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">×</span></button>
                                                <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                                                <h4 class="modal-title">¿Borrar edificio {{$edificios->des_edificio}}?</h4>
                                            </div>
                                            <div class="modal-footer">
                                                <a class="btn btn-info" href="{{url('/edificios/delete',$edificios->id_edificio)}}">Si</a>
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
        $('.parametrizacion').addClass('active active-sub');
        $('.edificios').addClass('active-link');
        
        $('#btn_nueva_puesto').click(function(){
            $('#editorCAM').load("{{ url('/edificios/create') }}", function(){
                animateCSS('#editorCAM','bounceInRight');
            });
            // window.scrollTo(0, 0);
            //stopPropagation()
        });

        function editar(id){
            $('#editorCAM').load("{{ url('/edificios/edit/') }}"+"/"+id, function(){
                animateCSS('#editorCAM','bounceInRight');
            });
        }

        $('.td').click(function(event){
            editar( $(this).data('id'));
        })
    </script>
@endsection