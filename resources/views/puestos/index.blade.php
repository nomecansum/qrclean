@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Gestión de puestos</h1>
@endsection

@section('styles')
    <link href="{{ asset('/plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.css') }}" rel="stylesheet">
    {{--  <link href="{{ asset('/plugins/fullcalendar/fullcalendar.min.css') }}" rel="stylesheet">  --}}
    <link href="{{ asset('/plugins/fullcalendar/lib/main.css') }}" rel="stylesheet">
    
	{{--  <link href="{{ asset('/plugins/fullcalendar/nifty-skin/fullcalendar-nifty.min.css') }}" rel="stylesheet">  --}}
    <style type="text/css">
        td .tooltip {
            position:absolute
        }
        .enfrente {
            z-index: 1100;
        }

    </style>
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
                            @if(checkPermissions(['Puestos'],['W']))
                                <li class="dropdown-header">Cambiar estado</li>
                                <li><a href="#" data-estado="1" class="btn_estado_check"><i class="fas fa-square text-success"></i> Disponible</a></li>
                                <li><a href="#" data-estado="2" class="btn_estado_check"><i class="fas fa-square text-danger"></i> Usado</a></li>
                                @if(session('CL')['mca_limpieza']=='S')<li><a href="#" data-estado="3" class="btn_estado_check"><i class="fas fa-square text-info"></i> Limpieza</a></li>@endif
                                <li><a href="#" data-estado="6" class="btn_estado_check"><i class="fas fa-square text-warning"></i> Incidencia</a></li>
                                <li><a href="#" data-estado="5" class="btn_estado_check"><i class="fas fa-square text-secondary"></i> Bloqueado</a></li>
                                <li><a href="#" data-estado="7" class="btn_estado_check"><i class="fas fa-square text-secondary"></i> No usable (encuesta)</a></li>
                            @endif
                            <li class="divider"></li>
                            <li class="dropdown-header">Atributos</li>
                            @if(checkPermissions(['Puestos'],['W']))<li><a href="#anonimo-puesto" class="btn_anonimo btn_toggle_dropdown" data-toggle="modal" data-tipo="M"><i class="fad fa-user-secret"></i></i> Habilitar acceso anonimo</a> </li>@endif
                            @if(checkPermissions(['Puestos'],['W']))<li><a href="#reserva-puesto" class="btn_reserva btn_toggle_dropdown" data-toggle="modal" data-tipo="M"><i class="fad fa-calendar-alt"></i> Habilitar reserva</a></li>@endif
                            @if(checkPermissions(['Puestos'],['W']))<li><a href="#modificar-puesto" class="btn_modificar_puestos btn_toggle_dropdown" data-toggle="modal" data-tipo="M"><i class="fad fa-pencil"></i> Modificar puestos</a></li>@endif
                            <li class="divider"></li>
                            <li class="dropdown-header">Acciones</li>
                            <li><a href="#" class="btn_qr"><i class="fad fa-qrcode"></i> Imprimir QR</a></li>
                            <li><a href="#" class="btn_export_qr"><i class="fad fa-file-export"></i></i> Exportar QR</a></li>
                            @if(checkPermissions(['Rondas de limpieza'],['C']) && session('CL')['mca_limpieza']=='S')<li><a href="#" class="btn_asignar" data-tipo="L" ><i class="fad fa-broom"></i >Ronda de limpieza</a></li>@endif
                            @if(checkPermissions(['Rondas de mantenimiento'],['C']))<li><a href="#" class="btn_asignar" data-tipo="M"><i class="fad fa-tools"></i> Ronda de mantenimiento</a></li>@endif
                            @if(checkPermissions(['Puestos'],['D']))<li><a href="#" class="btn_borrar_puestos btn_toggle_dropdown"  data-tipo="M"><i class="fad fa-trash"></i></i> Borrar puestos</a> </li>@endif
                        </ul>
                    </div>
                </div>
                <div class="btn">
                    @if(checkPermissions(['Puestos'],['C']))
                    <a href="#" id="btn_nueva_puesto" class="btn btn-success" title="Nuevo puesto">
                        <i class="fa fa-plus-square pt-2" style="font-size: 20px" aria-hidden="true"></i>
                        <span>Nuevo</span>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @php $etiqueta_boton="Ver puestos" @endphp
    <form method="post" name="form_puestos" id="formbuscador" action="{{ url('puestos/') }}">
        @csrf
        <input type="hidden" name="document" value="pantalla">
        @include('resources.combos_filtro',[$hide=['usu'=>1]])
    </form>
    <div id="editorCAM" class="mt-2">

    </div>
    <script>
        left_toolbar=300;
        top_toolbar=16;
    </script>
    @include('puestos.scripts_lista_puestos')

    <div id="myFilter">
        @if(!isset($r))
            @include('puestos.fill-tabla')
        @endif
    </div>
    
    <div class="modal fade" id="borrar-puestos" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="demo-psi-cross"></i></span></button>
                    <h4 class="modal-title">¿Borrar <span id="cuenta_puestos_borrar"></span> puestos?</h4><br>
                   
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

    <div class="modal fade" id="modificar-puestos" style="display: none;">
        <div class="modal-dialog  modal-lg">
            <div class="modal-content">
                <form  action="{{url('puestos/modificar_puestos')}}" method="POST" name="frm_modif_puestos" id="frm_modif_puestos" class="form-ajax">
                    {{csrf_field()}}
                    <input type="hidden" name="lista_id" id=lista_id_modif>
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="demo-psi-cross"></i></span></button>
                        <h4 class="modal-title">Modificar datos de <span id="cuenta_puestos_borrar"></span> puestos</h4><br>
                    
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="des_puesto">Nombre descriptivo</label>
                                <input type="text" name="des_puesto" id="des_puesto" class="form-control" >
                            </div>
                            <div class="form-group col-md-2">
                                <label for="id_estado">Estado</label>
                                <select name="id_estado" id="modif_id_estado"  class="form-control">
                                    <option value=""></option>
                                    @foreach(DB::table('estados_puestos')->get() as $estado)
                                    <option value="{{ $estado->id_estado}}">{{ $estado->des_estado }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="val_color">Color</label><br>
                                <input type="text" autocomplete="off" name="val_color" id="modif_val_color"  class="minicolors form-control" value="" />
                            </div>
                            <div class="col-sm-1">
                                <div class="form-group">
                                    <label>Icono</label><br>
                                    <button type="button"  role="iconpicker" name="val_icono"  id="modif_val_icono" data-iconset="fontawesome5" class="btn btn-light iconpicker enfrente" data-search="true" data-rows="10" data-cols="30" data-search-text="Buscar..."></button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="id_edificio">Edificio</label>
                                <select name="id_edificio" id="modif_id_edificio" class="form-control enfrente">
                                    <option value=""></option>
                                    @foreach(DB::table('edificios')->where('id_cliente',Auth::user()->id_cliente)->get() as $edificio)
                                        <option value="{{ $edificio->id_edificio}}">{{ $edificio->des_edificio }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="planta">Planta</label>
                                <select name="id_planta" id="modif_id_planta" class="form-control">
                                    <option value=""></option>
                                    @foreach(DB::table('plantas')->where('id_cliente',Auth::user()->id_cliente)->get() as $planta)
                                        <option value="{{ $planta->id_planta}}">{{ $planta->des_planta }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 p-t-30">
                                <input type="checkbox" class="form-control  magic-checkbox" name="mca_acceso_anonimo"  id="modif_mca_acceso_anonimo" value="S"> 
                                <label class="custom-control-label"   for="mca_acceso_anonimo">Anonimo</label>
                            </div>
                            <div class="col-md-2  p-t-30">
                                <input type="checkbox" class="form-control  magic-checkbox" name="mca_reservar"  id="modif_mca_reservar" value="S"> 
                                <label class="custom-control-label"   for="mca_reservar">Reserva</label>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="planta">Tags</label>
                                <input type="text" class="edit_tag" data-role="tagsinput" placeholder="Type to add a tag" size="17" name="tags">
                            </div>
                            
                            <div class="form-group col-md-6">
                                <label for="id_perfil">Asignar a perfil</label>
                                <select name="id_perfil" id="modif_id_perfil" class="form-control enfrente">
                                    <option value=""></option>
                                    @foreach(DB::table('niveles_acceso')->wherein('id_cliente',[1,Auth::user()->id_cliente])->where('val_nivel_acceso','<=',Auth::user()->nivel_acceso)->orderby('val_nivel_acceso')->orderby('des_nivel_acceso')->get() as $n)
                                        <option value="{{ $n->cod_nivel}}">{{ $n->des_nivel_acceso }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="id_usuario">Tipo de puesto</label>
                                <select name="id_tipo_puesto" id="id_tipo_puesto" class="form-control">
                                    <option value=""></option>
                                    @foreach($tipos as $t)
                                        <option value="{{ $t->id_tipo_puesto}}" {{ isset($puesto->id_tipo_puesto) && $puesto->id_tipo_puesto==$t->id_tipo_puesto?'selected':'' }}>{{ $t->des_tipo_puesto }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if(session('CL')['mca_reserva_horas']=='S')
                                <div class="form-group col-md-2">
                                    <label for="max_horas_reservar">Max reserva(horas)</label>
                                    <input type="number" min="1" max="999999" name="max_horas_reservar" id="max_horas_reservar" class="form-control">
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-info" id="modificar_muchos">Si</button>
                        <button type="button" data-dismiss="modal" class="btn btn-warning">No</button>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="eliminar-puesto" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="demo-psi-cross"></i></span></button>
                    <h4 class="modal-title">¿Borrar puesto <span id="txt_borrar"></span>?</h4>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-info" href="" id="link_borrar">Si</a>
                    <button type="button" data-dismiss="modal" class="btn btn-warning">No</button>
                </div>
            </div>
            <div class="modal-footer">
                <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="anonimo-puesto" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="demo-psi-cross"></i></span></button>
                        <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div><h4 class="modal-title">Habilitar acceso anonimo para los puestos</h4>
                </div>
                <div class="modal-body">
                    <input type="checkbox" class="form-control  magic-checkbox chk_accion" name="mca_anonimo" data-label="lbl_anonimo" id="mca_anonimo" checked value="S"> 
					<label class="custom-control-label" id="lbl_anonimo"  for="mca_anonimo">Habilitado</label>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-info" id="btn_si_anonimo" href="javascript:void(0)">Si</a>
                    <button type="button" data-dismiss="modal" class="btn btn-warning">No</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="reserva-puesto" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="demo-psi-cross"></i></span></button>
                        <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                        <h4 class="modal-title">Habilitar reserva para los puestos</h4>
                </div>
                <div class="modal-body">
                    <input type="checkbox" class="form-control  magic-checkbox chk_accion" name="mca_reserva" data-label="lbl_reserva" id="mca_reserva" checked value="S"> 
					<label class="custom-control-label" id="lbl_reserva"  for="mca_reserva">Habilitado</label>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-info" id="btn_si_reserva" href="javascript:void(0)">Si</a>
                    <button type="button" data-dismiss="modal" class="btn btn-warning">No</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ronda-limpieza" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                    <input type="hidden" name="tip_ronda" value="L" id="tip_ronda">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="demo-psi-cross"></i></span></button>
                    <h3 class="modal-title">Crear ronda de <span class="tipo_ronda"></span></h3><br>
                </div>
                
                <div class="modal-body" style="height: 250px">
                    <input type="hidden" id="listaID">
                    Crear ronda de <span class="tipo_ronda"></span> para <span id="cuenta_puestos_limpieza"></span> puestos.
                    <br><br>
                    <div class="form-group">
                        <label> Descripcion del trabajo</label>
                        <input type="text" class="form-control" name="des_ronda" id="des_ronda" id="listaID">
                    </div>
                    <div class="form-group">
                        <label> Asignar a empleado de <span class="tipo_ronda"></span></label>
                        <div id="divlimpiadores"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                    <a class="btn btn-info" id="btn_crear_ronda" href="javascript:void(0)">Si</a>
                    <button type="button" id="btn_cancel_ronda" data-dismiss="modal" class="btn btn-warning">No</button>
                    
                </div>
            </div>
        </div>
    </div>
    
@endsection

@section('scripts')
<script src="{{ asset('/plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js') }}"></script>
{{--  <script src="{{ asset('plugins/fullcalendar/lib/jquery-ui.custom.min.js') }}"></script>  --}}
{{--  <script src="{{ asset('plugins/fullcalendar/fullcalendar.min.js') }}"></script>  --}}
<script src="{{ asset('plugins/fullcalendar/lib/main.min.js') }}"></script>
<script src="{{ asset('plugins/fullcalendar/lib/locales/es.js') }}"></script>

<script>

    //Menu
    $('.parametrizacion').addClass('active active-sub');
    $('.puestos').addClass('active-link');

    $('.chk_accion').click(function(){
        if($(this).is(':checked')){
            $('#'+$(this).data('label')).html('Habilitado');
        } else {
            $('#'+$(this).data('label')).html('Deshabilitado');
        }
    })

    $('.btn_toggle_dropdown').click(function(){
        $('#dropdown-acciones').toggle();
    })

    

    let searchIDs=[];

    $('#frmpuestos').submit(form_pdf_submit);
    $('#formbuscador').submit(ajax_filter);

	$('#btn_nueva_puesto').click(function(){
       $('#editorCAM').load("{{ url('/puestos/edit/0') }}", function(){
		animateCSS('#editorCAM','bounceInRight');
	   });
	});

	

    $('td').click(function(event){
        editar( $(this).data('id'));
    })


    $("#chktodos").click(function(){
        $('.chkpuesto').not(this).prop('checked', this.checked);
    });

    $('#btn_si_anonimo').click(function(){
        console.log('anomino');
        var searchIDs = $('.chkpuesto:checkbox:checked').map(function(){
        return $(this).val();
        }).get(); // <----
        if(searchIDs.length==0){
            toast_error('Error','Debe seleccionar algún puesto');
            exit();
        }
        if($('#mca_anonimo').is(':checked')){
            estado='S';
        } else {
            estado='N';
        }
        $.post('{{url('/puestos/anonimo')}}', {_token: '{{csrf_token()}}',estado: estado ,lista_id:searchIDs}, function(data, textStatus, xhr) {
            $('.modal').modal('hide');
            toast_ok('Cambio de estado anonimo',data.mensaje);
        })
        .fail(function(err){
            toast_error('Error',err.responseJSON.message);
        });
    })

    $('#btn_si_reserva').click(function(){
        console.log('reserva');
        var searchIDs = $('.chkpuesto:checkbox:checked').map(function(){
        return $(this).val();
        }).get(); // <----
        if(searchIDs.length==0){
            toast_error('Error','Debe seleccionar algún puesto');
            exit();
        }
        if($('#mca_reserva').is(':checked')){
            estado='S';
        } else {
            estado='N';
        }
        $.post('{{url('/puestos/mca_rerserva')}}', {_token: '{{csrf_token()}}',estado: estado ,lista_id:searchIDs}, function(data, textStatus, xhr) {
            $('.modal').modal('hide');
            toast_ok('Permiso de reserva',data.mensaje);
        })
        .fail(function(err){
            toast_error('Error',err.responseJSON.message);
        });
    })


    $('.btn_estado_check').click(function(){
        console.log('check');
        var searchIDs = $('.chkpuesto:checkbox:checked').map(function(){
        return $(this).val();
        }).get(); // <----
        if(searchIDs.length==0){
            toast_error('Error','Debe seleccionar algún puesto');
            exit();
        }

        $.post('{{url('/puestos/accion_estado')}}', {_token: '{{csrf_token()}}',estado: $(this).data('estado'),lista_id:searchIDs}, function(data, textStatus, xhr) {
            toast_ok('Acciones',data.mensaje);
            //console.log($('.chkpuesto:checkbox:checked'));
            $('.chkpuesto:checkbox:checked').each(function(){
                //console.log('#estado_'+$(this).data('id'));
                $('#estado_'+$(this).data('id')).removeClass();
                $('#estado_'+$(this).data('id')).addClass('bg-'+data.color);
                $('#estado_'+$(this).data('id')).html(data.label);
                animateCSS('#estado_'+$(this).data('id'),'rubberBand');
            });
        })
        .fail(function(err){
            toast_error('Error',err.responseJSON.message);
        });
    });

    

    $('.btn_qr').click(function(){
        //block_espere();
        searchIDs = $('.chkpuesto:checkbox:checked').map(function(){
        return $(this).val();
        }).get(); // <----
        if(searchIDs.length==0){
            toast_error('Error','Debe seleccionar algún puesto');
            return;
        }
    //
        $('#frmpuestos').attr('action',"{{url('/puestos/print_qr')}}");
        $('#frmpuestos').submit();
        //
    });

    $('.btn_export_qr').click(function(){
        //block_espere();
        searchIDs = $('.chkpuesto:checkbox:checked').map(function(){
        return $(this).val();
        }).get(); // <----
        if(searchIDs.length==0){
            toast_error('Error','Debe seleccionar algún puesto');
            return;
        }
    //
        $('#frmpuestos').attr('action',"{{url('/puestos/export_qr')}}");
        $('#frmpuestos').submit();
        //
    });

    $('.btn_asignar').click(function(){
        //block_espere();
        searchIDs = $('.chkpuesto:checkbox:checked').map(function(){
        return $(this).val();
        }).get(); // <----
        if(searchIDs.length==0){
            toast_error('Error','Debe seleccionar algún puesto');
            return;
        }
        $('#cuenta_puestos_limpieza').html(searchIDs.length);
        $('#des_ronda').val();
        if($(this).data('tipo')=='L'){
            $('.tipo_ronda').html("limpieza");
        } else {
            $('.tipo_ronda').html("mantenimiento");
        }
        $('#tip_ronda').val($(this).data('tipo'));
        $.post('{{url('/combos/limpiadores')}}', {_token: '{{csrf_token()}}',lista_id:searchIDs, tipo:$(this).data('tipo')}, function(data, textStatus, xhr) {
            $('#divlimpiadores').html(data);
        })
        $('#ronda-limpieza').modal('show');
        //fin_espere();
    });


    $('#tablapuestos').on('click-cell.bs.table', function(e, value, row, $element){
        //console.log(e);
    });

    $('#btn_crear_ronda').click(function(){
        var userIDs = $('.chkuser:checkbox:checked').map(function(){
        return $(this).val();
        }).get(); // <----
        if(userIDs.length==0){
            toast_error('Error','Debe seleccionar algún empleado de limpieza');
            return;
        }
        $.post('{{url('/puestos/ronda_limpieza')}}', {_token: '{{csrf_token()}}',lista_id:searchIDs,lista_limpiadores: userIDs,des_ronda: $('#des_ronda').val(),tip_ronda: $('#tip_ronda').val() }, function(data, textStatus, xhr) {
            console.log(data);
            toast_ok(data.title,data.mensaje);
        })
        .fail(function(err){
            toast_error('Error',err.responseJSON.message);
        });
        $('#ronda-limpieza').modal('hide');
    });

    $('.btn_borrar_puestos').click(function(){
        //block_espere();
        searchIDs = $('.chkpuesto:checkbox:checked').map(function(){
            return $(this).val();
        }).get(); // <----
        if(searchIDs.length==0){
            toast_error('Error','Debe seleccionar algún puesto');
            return;
        }
        $('#cuenta_puestos_borrar').html(searchIDs.length);
        $('#borrar-puestos').modal('show');
        //fin_espere();
    });

    $('#borrar_muchos').click(function(){
        searchIDs = $('.chkpuesto:checkbox:checked').map(function(){
            return $(this).val();
        }).get(); // 

        $.post('{{url('/puestos/borrar_puestos')}}', {_token: '{{csrf_token()}}',lista_id:searchIDs}, function(data, textStatus, xhr) {
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
            $('#borrar-puestos').modal('hide');  
            if(data.url){
                setTimeout(()=>{window.open(data.url,'_self')},3000);
            } 
            
        });
    })

    $('#modificar_muchos').click(function(){
        searchIDs = $('.chkpuesto:checkbox:checked').map(function(){
            return $(this).val();
        }).get(); 
        $('#lista_id_modif').val(searchIDs);
        //$('#frm_modif_puestos').submit();
    })

    //$('#frm_modif_puestos').submit(form_ajax_submit);

    $('.btn_modificar_puestos').click(function(){
        //block_espere();
        searchIDs = $('.chkpuesto:checkbox:checked').map(function(){
            return $(this).val();
        }).get(); // <----
        if(searchIDs.length==0){
            toast_error('Error','Debe seleccionar algún puesto');
            return;
        }
        $('#cuenta_puestos_modificar').html(searchIDs.length);
        $('#modificar-puestos').modal('show');
        //fin_espere();
    });

    $('#modif_val_icono').on('change', function(e) {
        console.log(e.icon);
        $('.modal').css('z-index', 10000);
    });

    $('#modif_val_icono').click(function() {
        $('.modal').css('z-index', 1000);
    });

</script>
@endsection
