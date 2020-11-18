@extends('layout')

@section('styles')
    <!--Bootstrap FLEX Stylesheet [ REQUIRED ]-->
    <link href="{{ url('/css/bootstrap-grid.min.css') }}" rel="stylesheet">
    <style type="text-css">
        .show-calendar{
            z-indez: 15000;
        }
    </style>
@endsection

@section('title')
    <h1 class="page-header text-overflow pad-no">Gestión de usuarios</h1>
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li><a href="{{url('/')}}"><i class="fa fa-home"></i> </a></li>
        <li class="breadcrumb-item">configuración</li>
        <li class="breadcrumb-item">usuarios</li>
        <li class="breadcrumb-item">listado</li>
        {{--  <li class="breadcrumb-item active">Editar usuario {{ !empty($users->name) ? $users->name : '' }}</li>  --}}
    </ol>
@endsection

@section('content')

    @if(Session::has('success_message'))
        <div class="alert alert-success">
            <span class="glyphicon glyphicon-ok"></span>
            {!! session('success_message') !!}

            <button type="button" class="close" data-dismiss="alert" aria-label="close">
                <span aria-hidden="true">&times;</span>
            </button>

        </div>
    @endif

    <div class="row botones_accion">
        <div class="col-md-8">

        </div>
        <div class="col-md-4 text-right">
            <div class="btn-group btn-group-xs pull-right" role="group">
                <div class="btn-group mr-3">
                    <div class="dropdown">
                        <button class="btn btn-warning dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false" title="Acciones sobre la seleccion de puestos">
                            <i class="fad fa-poll-people pt-2" style="font-size: 20px" aria-hidden="true"></i> Acciones <i class="dropdown-caret"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="" id="dropdown-acciones">
                            <li class="dropdown-header">Atributos</li>
                            @if(checkPermissions(['Usuarios'],['W']))<li><a href="#asignar-planta" class="btn_plantas btn_toggle_dropdown btn_search" data-toggle="modal" data-tipo="M"><i class="fad fa-layer-plus"></i> Asignar planta</a> </li>@endif
                            @if(checkPermissions(['Usuarios'],['W']))<li><a href="#asignar-supervisor" class="btn_supervisor btn_toggle_dropdown btn_search" data-toggle="modal" data-tipo="M"><i class="fad fa-user-friends"></i> Asignar supervisor</a></li>@endif
                            
                            {{--  @if(checkPermissions(['Puestos'],['W']))<li><a href="#modificar-puesto" class="btn_modificar_puestos btn_toggle_dropdown" data-toggle="modal" data-tipo="M"><i class="fad fa-pencil"></i> Modificar puestos</a></li>@endif  --}}
                            <li class="divider"></li>
                            <li class="dropdown-header">Acciones</li>
                            {{--  <li><a href="#" class="btn_qr"><i class="fad fa-qrcode"></i> Imprimir QR</a></li>  --}}
                            {{--  @if(checkPermissions(['Rondas de limpieza'],['C']))<li><a href="#" class="btn_asignar" data-tipo="L" ><i class="fad fa-broom"></i >Ronda de limpieza</a></li>@endif
                            @if(checkPermissions(['Rondas de mantenimiento'],['C']))<li><a href="#" class="btn_asignar" data-tipo="M"><i class="fad fa-tools"></i> Ronda de mantenimiento</a></li>@endif  --}}
                            @if(checkPermissions(['Asignar puesto a usuario'],['R']))<li><a href="#asignar-puesto" data-toggle="modal" class="btn_asignar_puesto reserva btn_search" data-tipo="M"><i class="fad fa-desktop-alt"></i> Asignar temporalmente puesto a usuario</a></li>@endif
                            @if(checkPermissions(['Crear reservas para otros'],['R']))<li><a href="#crear-reserva" data-toggle="modal"  class="btn_crear_reserva reserva btn_search" data-tipo="M"><i class="fad fa-calendar-alt"></i> Crear reserva para usuario</a></li>@endif 
                            @if(checkPermissions(['Usuarios'],['D']))<li><a href="#" class="btn_borrar_usuarios btn_toggle_dropdown btn_search"  data-tipo="M"><i class="fad fa-trash"></i></i> Borrar usuarios</a> </li>@endif
                            {{--  @if(checkPermissions(['Usuarios'],['W']))<li><a href="#reserva-puesto" class="btn_reserva btn_toggle_dropdown" data-toggle="modal" data-tipo="M"><i class="fad fa-calendar-alt"></i> Reservar puesto supervisor</a></li>@endif  --}}
                        </ul>
                    </div>
                </div>
                <div class="col-md-1 text-right">
                    @if(checkPermissions(['Usuarios'],['C']))
                    <a href="{{ route('users.users.create') }}" id="btn_nueva_puesto" class="btn btn-success" title="Nuevo usuario">
                        <i class="fa fa-plus-square pt-2" style="font-size: 20px" aria-hidden="true"></i>
                        <span>Nuevo</span>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">Usuarios</h3>
        </div>


        @if(count($usersObjects) == 0)
            <div class="panel-body text-center">
                <h4>No Users Available.</h4>
            </div>
        @else
        <div class="panel-body panel-body-with-table">
            <div class="table-responsive">
                <div id="all_toolbar" class="ml-3">
                    <input type="checkbox" class="form-control custom-control-input magic-checkbox" name="chktodos" id="chktodos"><label  class="custom-control-label"  for="chktodos">Todos</label>
                </div>
                <table id="tablarondas"  data-toggle="table"
                    data-locale="es-ES"
                    data-search="true"
                    data-show-columns="true"
                    data-show-columns-toggle-all="true"
                    data-page-list="[5, 10, 20, 30, 40, 50]"
                    data-page-size="50"
                    data-pagination="true" 
                    data-show-pagination-switch="true"
                    data-show-button-icons="true"
                    data-toolbar="#all_toolbar"
                    >
                    <thead>
                        <tr>
                            <th style="width: 10px" class="no-sort"  data-switchable="false"></th>
                            <th style="width: 30px" class="no-sort"  data-switchable="false"></th>
                            <th  data-sortable="true">Nombre</th>
                            <th  data-sortable="true">Perfil</th>
                            <th  data-sortable="true">Ult acceso</th>
                            <th  data-sortable="true">Email</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($usersObjects as $users)
                        <tr class="hover-this" data-id="{{ $users->id }}" data-href="{{ route('users.users.edit', $users->id ) }}">
                            <td class="text-center">
                                <input type="checkbox" class="form-control chkuser magic-checkbox" name="lista_id[]" data-id="{{ $users->id }}" id="chku{{ $users->id }}" value="{{ $users->id }}">
                                <label class="custom-control-label"   for="chku{{ $users->id }}"></label>
                            </td>
                            <td class="center">
                                @if (isset($users->img_usuario ) && $users->img_usuario!='')
                                    <img src="{{ Storage::disk(config('app.img_disk'))->url('img/users/'.$users->img_usuario) }}" class="img-circle" style="height: 50px">
                                @else
                                    {!! icono_nombre($users->name) !!}
                                @endif
                            </td>
                            <td class="pt-3">{{ $users->name }}</td>
                            <td>
                                @if(isset($users->cod_nivel))
                                    {{$users->des_nivel_acceso}}
                                @else
                                    <div>
                                        <i class="fa fa-warning" style="color:orange">Pendiente</i>
                                    </div>
                                @endif
                            </td>
                            <td>{!! beauty_fecha($users->last_login) !!}</td>
                            <td style="vertical-align: middle">
                                {{ $users->email }}
                                <form method="POST" action="{!! route('users.users.destroy', $users->id) !!}" accept-charset="UTF-8">
                                <input name="_method" value="DELETE" type="hidden">
                                {{ csrf_field() }}
                                    <div class="pull-right floating-like-gmail" role="group" style="width: 170px">
                                        @if (checkPermissions(['ReLogin'],["R"]))<a href="{{url('relogin',$users->id)}}" class="btn btn-xs btn-warning"><i class="fa fa-user" ></i> Suplantar</a>@endif
                                        <a href="{{ route('users.users.edit', $users->id ) }}" class="btn btn-xs btn-info  add-tooltip" title="Editar Usuario"  style="float: left"><span class="fa fa-pencil pt-1" ></span> Edit</a>
                                        <a class="btn btn-xs btn-danger add-tooltip ml-1 btn_eliminar"  data-target="#eliminar-usuario" data-toggle="modal" style="float: left" title="Borrar usuario" onclick="$('#txt_borrar').html('{{ $users->name }}'); $('#id_usuario_borrar').val({{ $users->id }})" data-name="{{ $users->name }}"   style="float: right">
                                            <span class="fa fa-trash"></span> Del
                                        </a>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    <div class="modal fade" id="eliminar-usuario" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="demo-psi-cross"></i></span></button>
                    <h4 class="modal-title">¿Borrar usuario <span id="txt_borrar"></span>?</h4>
                    <input type="hidden" id="id_usuario_borrar">
                </div>
                <div class="modal-footer">
                    <a class="btn btn-info" href="#" id="link_borrar">Si</a>
                    <button type="button" data-dismiss="modal" class="btn btn-warning">No</button>
                </div>
            </div>
            <div class="modal-footer">
                <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="borrar-usuarios" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="demo-psi-cross"></i></span></button>
                    <h4 class="modal-title">¿Borrar <span id="cuenta_usuarios_borrar"></span> usuarios?</h4><br>
                   
                </div>
                <div class="modal-body">
                    Esta accion no puede deshacerse
                </div>

                <div class="modal-footer">
                    <a class="btn btn-info" href="javascript:void(0)" id="borrar_muchos">Si</a>
                    <button type="button" data-dismiss="modal" class="btn btn-warning">No</button>
                </div>
            </div>
            <div class="modal-footer">
                <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="asignar-planta" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="demo-psi-cross"></i></span></button>
                        <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                        <h4 class="modal-title">Habilitar plantas a los usuarios</h4>
                </div>
                
                <div class="modal-body" id="">
                    <input type="checkbox" class="form-control magic-checkbox ml-2" name="chk_anterior" id="chk_anterior">
                    <label class="custom-control-label"   for="chk_anterior">Borrar plantas que tuvieran ya asignadas los usuarios</label>   
                    <br><br>
                    <div id="txt_cuerpo"></div>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-info" id="btn_si_planta" href="javascript:void(0)">Si</a>
                    <button type="button" data-dismiss="modal" class="btn btn-warning">No</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="asignar-supervisor" style="display: none;">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="demo-psi-cross"></i></span></button>
                        <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                        <h4 class="modal-title">Asignar supervisor</h4>
                </div>
                
                <div class="modal-body" id="">
                    <div class="form-group col-md-12">
                        <select name="supervisor" id="supervisor" class="form-control enfrente" >
                            <option value="0">Ninguno</option>
                            @foreach($supervisores as  $n)
                                <option value="{{ $n->id}}">{{ $n->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-info" id="btn_si_supervisor" href="javascript:void(0)">Si</a>
                    <button type="button" data-dismiss="modal" class="btn btn-warning">No</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="crear-reserva" style="display: none;">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="demo-psi-cross"></i></span></button>
                        <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                        <h4 class="modal-title">Crear nueva reserva para el usuario</h4>
                </div>
                
                <div class="modal-body" id="">
                    
                </div>
                <div class="modal-footer">
                    <a class="btn btn-info" id="btn_si_supervisor" href="javascript:void(0)">Si</a>
                    <button type="button" data-dismiss="modal" class="btn btn-warning">No</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="asignar-puesto" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="demo-psi-cross"></i></span></button>
                        <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                        <h4 class="modal-title">Asignar temporalmente puesto a usuario</h4>
                </div>
                
                <div class="modal-body" id="">

                        <label>Fechas </label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control pull-left" id="fechas" name="fechas" style="height: 40px; width: 200px" value="{{ Carbon\Carbon::now()->format('d/m/Y').' - '.Carbon\Carbon::now()->addMonth()->format('d/m/Y') }}">
                            <span class="btn input-group-text btn-mint" disabled  style="height: 40px"><i class="fas fa-calendar mt-1"></i></span>
                        
                        </div>
                    <div id="puestos_usuario_asignar"></div>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-info" id="btn_si_supervisor" href="javascript:void(0)">Si</a>
                    <button type="button" data-dismiss="modal" class="btn btn-warning">No</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        
        $('.configuracion').addClass('active active-sub');
        $('.usuarios').addClass('active-link');
        
         //Date range picker
         $('#fechas').daterangepicker({
            autoUpdateInput: false,
            locale: {
                format: '{{trans("general.date_format")}}',
                applyLabel: "OK",
                cancelLabel: "Cancelar",
                daysOfWeek:["{{trans('general.domingo2')}}","{{trans('general.lunes2')}}","{{trans('general.martes2')}}","{{trans('general.miercoles2')}}","{{trans('general.jueves2')}}","{{trans('general.viernes2')}}","{{trans('general.sabado2')}}"],
                monthNames: ["{{trans('general.enero')}}","{{trans('general.febrero')}}","{{trans('general.marzo')}}","{{trans('general.abril')}}","{{trans('general.mayo')}}","{{trans('general.junio')}}","{{trans('general.julio')}}","{{trans('general.agosto')}}","{{trans('general.septiembre')}}","{{trans('general.octubre')}}","{{trans('general.noviembre')}}","{{trans('general.diciembre')}}"],
                firstDay: {{trans("general.firstDayofWeek")}}
            },
            opens: 'right',
        });

        $('#link_borrar').click(function(){
            console.log("{{ url('users/delete/') }}/"+$('#id_usuario_borrar').val());
            window.open("{{ url('users/delete/') }}/"+$('#id_usuario_borrar').val(),'_self');
        })

        $('.btn_eliminar').click(function(){
            console.log($(this).data('name'));
            $('#txt_borrar').html($(this).data('name'));
        })

        $('.hover-this').click(function(){
            window.open($(this).data('href'),'_self');
        })

        $("#chktodos").click(function(){
            $('.chkuser').not(this).prop('checked', this.checked);
        });

        $('.btn_plantas').click(function(){
            $('#txt_cuerpo').load("{{ url('/users/plantas',Auth::user()->id) }}/0");
        })
        
        var tooltip = $('.add-tooltip');
        if (tooltip.length)tooltip.tooltip();

        $('.btn_borrar_usuarios').click(function(){
            //block_espere();
            searchIDs = $('.chkuser:checkbox:checked').map(function(){
                return $(this).val();
            }).get(); // <----

            $('#cuenta_usuarios_borrar').html(searchIDs.length);
            $('#borrar-usuarios').modal('show');
            //fin_espere();
        });

        $('#borrar_muchos').click(function(){
            searchIDs = $('.chkuser:checkbox:checked').map(function(){
                return $(this).val();
            }).get(); // 

            $.post('{{url('/users/borrar_usuarios')}}', {_token: '{{csrf_token()}}',lista_id:searchIDs}, function(data, textStatus, xhr) {
                console.log(data);
                if(data.error){
                    toast_error(data.title,data.error);
                } else if(data.alert){
                    toast_warning(data.title,data.alert);
                } else{
                    toast_ok(data.title,data.message);
                }
            }) 
            .fail(function(err){
                toast_error('Error',err.responseJSON.message);
            })
            .always(function(data){
                $('#borrar-usuarios').modal('hide');  
                if(data.url){
                    setTimeout(()=>{window.open(data.url,'_self')},3000);
                } 
                
            });
        })

        $('#btn_si_planta').click(function(){
            searchIDs = $('.chkuser:checkbox:checked').map(function(){
                return $(this).val();
            }).get(); // 

            searchPLs= $('.chkplanta:checkbox:checked').map(function(){
                return $(this).val();
            }).get(); // 

            $.post('{{url('/users/asignar_plantas')}}', {_token: '{{csrf_token()}}',lista_id:searchIDs,lista_plantas:searchPLs,borrar_ant: $('#chk_anterior').is(':checked')}, function(data, textStatus, xhr) {
                console.log(data);
                if(data.error){
                    toast_error(data.title,data.error);
                } else if(data.alert){
                    toast_warning(data.title,data.alert);
                } else{
                    toast_ok(data.title,data.message);
                }
            }) 
            .fail(function(err){
                toast_error('Error',err.responseJSON.message);
            })
            .always(function(data){
                $('#asignar-planta').modal('hide');  
                if(data.url){
                    setTimeout(()=>{window.open(data.url,'_self')},3000);
                } 
                
            });  
        })

        $('.btn_search').click(function(){
            searchIDs = $('.chkuser:checkbox:checked').map(function(){
                return $(this).val();
            }).get(); // 
            if(searchIDs.length==0){
                $('.modal').modal('hide');  
                toast_error('Error','Debe seleccionar algún usuario');
                exit();
            }
        })

        $('#btn_si_supervisor').click(function(){
            searchIDs = $('.chkuser:checkbox:checked').map(function(){
                return $(this).val();
            }).get(); // 


            $.post('{{url('/users/asignar_supervisor')}}', {_token: '{{csrf_token()}}',lista_id:searchIDs,supervisor: $('#supervisor').val()}, function(data, textStatus, xhr) {
                console.log(data);
                if(data.error){
                    toast_error(data.title,data.error);
                } else if(data.alert){
                    toast_warning(data.title,data.alert);
                } else{
                    toast_ok(data.title,data.message);
                }
            }) 
            .fail(function(err){
                toast_error('Error',err.responseJSON.message);
            })
            .always(function(data){
                $('#asignar-supervisor').modal('hide');  
                if(data.url){
                    setTimeout(()=>{window.open(data.url,'_self')},3000);
                } 
                
            });  
        })

        $('.btn_asignar_puesto').click(function(){
            $('#puestos_usuario_asignar').load("{{ url('users/puestos_usuario/'.$users->id) }}", function(){
                $('.flpuesto').click(function(){
                    console.log($(this).data('id'));
                })
            });
            
        })

    </script>
@endsection