@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Gestión de encuestas</h1>
@endsection

@section('styles')
    {{-- Boostrap Select --}}

@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{url('/')}}"><i class="fa fa-home"></i> </a></li>
        <li class="breadcrumb-item">parametrización</li>
        <li class="breadcrumb-item">encuestas</li>
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
            @if(checkPermissions(['Encuestas'],['C']))
            <div class="btn-group btn-group-sm pull-right" role="group">
                    <a href="#" id="btn_nueva_puesto" class="btn btn-success" title="Nueva encuesta">
                    <i class="fa fa-plus-square pt-2" style="font-size: 20px" aria-hidden="true"></i>
                    <span>Nueva</span>
                </a>
            </div>
            @endif
        </div>
    </div>
    <div id="editorCAM" class="mt-2">

    </div>

    <div id="editorPUESTOS" class="mt-2">

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
        {{--  <i class="fas fa-grin-alt fa-3x text-success"></i>
        <i class="fas fa-smile  fa-3x text-mint"></i>
        <i class="fas fa-meh-rolling-eyes  fa-3x text-primary"></i>
        <i class="fas fa-frown  fa-3x text-warning"></i>
        <i class="fas fa-tired  fa-3x text-danger"></i>

        <i class="fas fa-smile fa-3x text-success"></i>
        <i class="fas fa-meh  fa-3x text-warning"></i>
        <i class="fas fa-frown  fa-3x text-danger"></i>

        <i class="fas fa-star fa-3x" style="color: #ffd700"></i>
        <i class="fas fa-star fa-3x" style="color: #ffd700"></i>
        <i class="fas fa-star fa-3x" style="color: #ffd700"></i>
        <i class="fas fa-star fa-3x" style="color: #ffd700"></i>
        <i class="fas fa-star fa-3x" style="color: #ffd700"></i>  --}}
        @if(count($encuestas) == 0)
            <div class="panel-body text-center">
                <h4>No encuestas Available.</h4>
            </div>
        @else
        <div class="panel-heading">
            <h3 class="panel-title">Encuestas</h3>
        </div>
        <div class="panel-body panel-body-with-table">
            <div class="table-responsive w-100">

                <table id="tablaenc"  data-toggle="table"
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
                            <th data-sortable="true" data-width="1%">ID</th>
                            <th data-width="2%"></th>
                            <th data-sortable="true">Titulo</th>
                            <th data-sortable="true" data-width="200">Tipo</th>
                            
                            <th data-sortable="true"data-width="5%" >Cliente</th>
                            <th data-width="1%">Act</th>
                            <th data-width="1%">Anon</th>
                            <th data-sortable="true" data-width="10%">Fechas</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($encuestas as $enc)
                        <tr class="hover-this">
                            <td>{{ $enc->id_encuesta }}</td>
                            <td class="text-center"><i class="{{ $enc->val_icono }} fa-2x" style="color:{{ $enc->val_color }}"></i></td>
                            <td>{{ $enc->titulo }}</td>
                            <td><img src="{{ url('/img',$enc->img_tipo) }}" id="img_tipo" class="imagen_tipo">  <span id="des_tipo" class="ml-3">{{ $enc->des_tipo_encuesta }}</span></td>
                            
                            
                            <td>{{ $enc->nom_cliente }}</td>
                            <td class="text-center">@if($enc->mca_activa=='S') <i class="fas fa-circle text-success"></i> @endif</td>
                            <td class="text-center">@if($enc->mca_anonima=='S') <i class="fas fa-circle text-info"></i> @endif</td>
                            
                            <td class="text-center" style="position: relative"  data-valign="middle">{!! beauty_fecha($enc->fec_inicio) !!} <i class="fas fa-arrow-right"></i> {!! beauty_fecha($enc->fec_fin) !!}
                                <div class="pull-right floating-like-gmail mt-3" style="width: 240px;">
                                    <a href="#modal-resultados"  class="btn btn-xs btn-primary add-tooltip btn_result" data-toggle="modal" onclick="resultados('{{ $enc->id_encuesta }}')" title="Ver resultados" data-id="{{ $enc->id_encuesta }}"> <span class="fad fa-file-chart-line" aria-hidden="true"></span> Resultados</a>
                                    @if(checkPermissions(['Encuestas'],['W']))<a href="#"  class="btn btn-xs btn-info btn_editar add-tooltip" onclick="editar({{ $enc->id_encuesta }})" title="Editar planta" data-id="{{ $enc->id_encuesta }}"> <span class="fa fa-pencil pt-1" aria-hidden="true"></span> Edit</a>@endif
                                    @if(checkPermissions(['Encuestas'],['D']))<a href="#eliminar-planta-{{$enc->id_encuesta}}" data-target="#eliminar-planta-{{$enc->id_encuesta}}" title="Borrar planta" data-toggle="modal" class="btn btn-xs btn-danger add-tooltip btn_del"><span class="fa fa-trash" aria-hidden="true"></span> Del</a>@endif
                                </div>
                                <div class="modal fade" id="eliminar-planta-{{$enc->id_encuesta}}" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                        <div class="modal-header">

                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">×</span></button>
                                                <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                                                <h4 class="modal-title">¿Borrar encuesta {{$enc->titulo}}?</h4>
                                            </div>
                                            <div class="modal-footer">
                                                <a class="btn btn-info" href="{{url('/encuestas/delete',$enc->id_encuesta)}}">Si</a>
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

    <div class="modal fade" id="modal-resultados">
        <div class="modal-dialog modal-lg">
            <div class="modal-content"><div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                <div class="modal-body" id="body_resultados">
                    
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-warning">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')

    <script>
        $('.parametrizacion').addClass('active active-sub');
        $('.encuestas').addClass('active-link');

        $('#btn_nueva_puesto').click(function(){
            $('#editorCAM').load("{{ url('/encuestas/create') }}", function(){
                animateCSS('#editorCAM','bounceInRight');
            });
            // window.scrollTo(0, 0);
            //stopPropagation()
        });

        function editar(id){
            $('#editorCAM').load("{{ url('/encuestas/edit/') }}"+"/"+id, function(){
                animateCSS('#editorCAM','bounceInRight');
            });
        }


        $('.td').click(function(event){
            editar( $(this).data('id'));
        })

        $('.imagen_tipo').css('width','30%');

        function resultados(id){
            console.log('Get resultados '+id);
            $.post('{{url('/encuestas/resultados')}}', {_token:'{{csrf_token()}}',id_encuesta: id}, function(data, textStatus, xhr) {
               $('#body_resultados').html(data);
            });
        }
    </script>
@endsection