
<link href="{{ asset('/plugins/fullcalendar/lib/main.css') }}" rel="stylesheet">
<style>
    .floating-like-gmail {
        position: absolute;
        bottom: 8px;
        right: 10px;
        max-width: 100vw;
        width: 600px;
        right: 10px;
        text-align: right;
        opacity: 0;
        transition: 500ms;
    }
    .modal {
        text-align: center !important;
    }
    .hover-this:hover .floating-like-gmail,.floating-like-gmail:hover {
        opacity: 1;
    }
    .preview {
        width: 100%;
        height: 200px;
        background-position: center;
        background-size: cover;
        border: 1px solid #ccc;
        border-radius: 6px;
        overflow: hidden;
    }
    .img-preview {
        height: 100px;
        /*width: 100px;*/
        background-position: center;
        background-size: cover;
    }
    #img-inputs input {
        display: none;
        height: 0;
    }
    .sidebar-footer a {
        width: 50%;
    }
    .user-profile .profile-img::before {
        left: 0;
        right: 0;
    }

    .ui-sortable-handle{
        padding: 10px 10px 10px 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        cursor: move;
        min-height: 40px;
        min-width: 250px;
        font-size: 18px;
        list-style-type: none;
        width: 400px;
    }

    .ui-draggable-handle{
        padding: 10px 10px 10px 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        cursor: move;
        min-height: 40px;
        min-width: 120px;
        list-style-type: none;
        width: 400px;
    }

    .fc-event-title{
        font-size: 10px;
        font-weight: normal;
    }

    .bottom-right {
        position: relative;
        bottom: 0;
        right: 0;
        cursor: pointer;
    }

    .detalle{
        font-weight: 600;
    }

    .ui-sortable{
        width: 414px;
    }

    
</style>





    <div class="card editor mb-5">
        <div class="card-header toolbar">
            <div class="toolbar-start">
                <h5 class="m-0">Editar usuario {{ !empty($users->name) ? $users->name : '' }}</h5>
            </div>
            <div class="toolbar-end">
                <button type="button" class="btn-close btn-close-card">
                    <span class="visually-hidden">Close the card</span>
                </button>
            </div>
        </div>
        
        <div class="card-body">

            @if ($errors->any())
                <ul class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
            <form method="POST" action="{{ url('users/update',$users->id) }}" id="edit_users_form" name="edit_users_form" accept-charset="UTF-8" class="form-horizontal mt-4 form-ajax" enctype="multipart/form-data">
            {{ csrf_field() }}
                <div class="tab-base">		
                    <!--Nav tabs-->
                    <ul class="nav nav-tabs"  role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#demo-stk-lft-tab-1" type="button" role="tab" aria-controls="generales" aria-selected="true">Datos generales</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link"data-bs-toggle="tab" data-bs-target="#demo-stk-lft-tab-2"  type="button" role="tab" aria-controls="config" aria-selected="false">Configuracion</button>
                        </li>
                        @if (isset($users))
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab"  data-bs-target="#demo-stk-lft-tab-6" type="button" role="tab" aria-controls="seguridad" aria-selected="false">Seguridad</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab"  data-bs-target="#demo-stk-lft-tab-3" type="button" role="tab" aria-controls="reserva" aria-selected="false">Reserva automatica</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#demo-stk-lft-tab-4"  type="button" role="tab" aria-controls="puestos" aria-selected="false">Plantas/puestos</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab"  data-bs-target="#demo-stk-lft-tab-5"  id="actividad" type="button" role="tab" aria-controls="actividad" aria-selected="false">Actividad</button>
                            </li>
                        @endif
                    </ul>
                
                    <!--Tabs Content-->
                    <div class="tab-content">
                        <div id="demo-stk-lft-tab-1" class="tab-pane fade active show" role="tabpanel" aria-labelledby="generales-tab">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-10">
                                            <div class="row">
                                                <div class="form-group col-md-12 {{ $errors->has('name') ? 'has-error' : '' }}">
                                                    <label for="name" class="control-label">Nombre</label>
                                                    <input class="form-control" name="name" type="text" id="name" value="{{ old('name', optional($users)->name) }}" minlength="1" maxlength="255" required="true" placeholder="Enter name here...">
                                                    {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
                                                </div>
                                            </div>
                                            <div class="row mt-2">
                                                <div class="form-group col-md-6 {{ $errors->has('email') ? 'has-error' : '' }}">
                                                    <label for="email" class="control-label">e-mail</label>
                                                    <input class="form-control" name="email" type="text" id="email" value="{{ old('email', optional($users)->email) }}" minlength="1" maxlength="255" required="true" placeholder="Enter email here...">
                                                    {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
                                                </div>
                                                <div class="form-group col-md-6 {{ $errors->has('password') ? 'has-error' : '' }}">
                                                    <label for="password" class="control-label">Password</label>
                                                    <div class="input-group mb-4">
                                                        <input class="form-control" name="password" type="password" id="password"  minlength="4" maxlength="255" placeholder="Enter password here...">
                                                        {!! $errors->first('password', '<p class="help-block">:message</p>') !!}
                                                        <button class="btn btn-secondary" type="button"  id="btn_generar_password">Generar</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-n2">
                                                <div class="col-md-10">
                                                    <label class="control-label">Token acceso</label>
                                                    <div class="input-group mb-3">
                                                        <input type="text" name="token_acceso" readonly=""  id="token_1uso"  class="form-control" value="{{isset($users) ? $users->token_acceso : ''}}">
                                                        <div class="input-group-btn">
                                                            <button class="btn btn-secondary" type="button"  id="btn_generar_token">Generar</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label for="name" class="control-label">Expira (dias)</label>
                                                        <input class="form-control" name="token_expires" type="number" id="token_expires" value="{{ old('token_expires', optional($users)->token_expires) }}" min="1" max="1000000" >
                                                        {!! $errors->first('token_expires', '<p class="help-block">:message</p>') !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 text-center b-all rounded">
                                            <div class="row font-bold" style="padding-left: 15px">
                                                Imagen<br>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group  {{ $errors->has('img_usuario') ? 'has-error' : '' }}">
                                                    <label for="img_usuario" class="preview preview1">
                                                        <img src="{{ isset($users) ? Storage::disk(config('app.img_disk'))->url('img/users/'.$users->img_usuario) : ''}}" style="margin: auto; display: block; width: 156px; heigth:180px" alt="" id="img_preview" class="img-fluid">
                                                    </label>
                                                    <div class="custom-file">
                                                        <input type="file" accept=".jpg,.png,.gif,.webp.jiff" class="form-control  custom-file-input" name="img_usuario" id="img_usuario" lang="es" value="{{ isset($users) ? $users->img_usuario : ''}}">
                                                        <label class="custom-file-label" for="img_usuario"></label>
                                                    </div>
                                                </div>
                                                    {!! $errors->first('img_usuario', '<p class="help-block">:message</p>') !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        <div id="demo-stk-lft-tab-2"  class="tab-pane fade" role="tabpanel" aria-labelledby="config-tab">
                            <div class="card">
                                <div class="card-header">
                                    <p class="text-main text-semibold">Configuracion</p>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Cliente</label><br>
                                                <select required name="id_cliente" id="id_cliente" class="select2" style="width: 100%">
                                                    <option value=""></option>
                                                    @foreach (\DB::table('clientes')->where(function($q){
                                                        if (!fullAccess()){
                                                                $q->WhereIn('id_cliente',clientes());
                                                            }
                                                        })->wherenull('fec_borrado')->get() as $c)
                                                        <option {{isset($users) && $users->id_cliente == $c->id_cliente ? 'selected' : ''}} value="{{$c->id_cliente}}">{{$c->nom_cliente}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group {{ $errors->has('id_perfil') ? 'has-error' : '' }}">
                                                <label for="id_perfil">Perfil</label>
                                                <select class="select2 notsearch"  id="cod_nivel" name="cod_nivel">
                                                    @foreach ($Perfiles as $Perfile)
                                                        <option value="{{ $Perfile->cod_nivel }}" {{ old('cod_nivel', optional($users)->cod_nivel) == $Perfile->cod_nivel ? 'selected' : '' }}>
                                                            {{ $Perfile->des_nivel_acceso }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                {!! $errors->first('cod_nivel', '<p class="help-block">:message</p>') !!}
                                            </div>
                                        </div>
                                    
                                        {{-- //Vamos a añadir un combo con las zonas horarias --}}
                                        @php
                                            $regions = array(
                                                'Africa' => DateTimeZone::AFRICA,
                                                'America' => DateTimeZone::AMERICA,
                                                'Antarctica' => DateTimeZone::ANTARCTICA,
                                                'Asia' => DateTimeZone::ASIA,
                                                'Atlantic' => DateTimeZone::ATLANTIC,
                                                'Europe' => DateTimeZone::EUROPE,
                                                'Indian' => DateTimeZone::INDIAN,
                                                'Pacific' => DateTimeZone::PACIFIC
                                            );
                                            function tz_list() {
                                                $zones_array = array();
                                                $timestamp = time();
                                                $dummy_datetime_object = new DateTime();
                                                foreach(timezone_identifiers_list() as $key => $zone) {
                                                    date_default_timezone_set($zone);
                                                    $zones_array[$key]['zone'] = $zone;
                                                    $zones_array[$key]['diff_from_GMT'] = 'GMT' . date('P', $timestamp);
                                                    $tz = new DateTimeZone($zone);
                                                    $zones_array[$key]['offset'] = $tz->getOffset($dummy_datetime_object);
                                                }
                                                return $zones_array;
                                                }
                                    
                                        @endphp
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Zona horaria</label>
                                                <select name="val_timezone" class="select2" style="width: 100%; margin-top: 25px; height: 38px">
                                                    <option value="" selected></option>
                                                    @foreach($regions as $region => $list)
                                                    <optgroup label="{{ $region }}">
                                                        @foreach(tz_list() as $tz)
                                                        @if (stripos($tz['zone'],$region)===0)
                                                                <option {{ isset($users) && $tz['zone'] == $users->val_timezone  ? 'selected' : ''}} value="{{ $tz['zone'] }}">{{ $tz['zone'] }} {{ $tz['diff_from_GMT'] }}</option>
                                                            @endif
                                                        @endforeach
                                                    </optgroup>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Edificio de referencia</label><br>
                                                <select name="id_edificio" id="id_edificio" class="select2 notsearch">
                                                    <option value=""></option>
                                                    @foreach ($edificios as $t)
                                                        <option {{isset($users) && $users->id_edificio == $t->id_edificio ? 'selected' : ''}} value="{{$t->id_edificio}}">{{$t->des_edificio}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
											<div class="form-group">
												<label>Departamento</label>
													<select   name="id_departamento" class="select2 tab_general" style="width: 100%" id="id_departamento">
														<option value="0"> </option>
														@php $departamentos=lista_departamentos("cliente",$users->id_cliente); @endphp
														@isset($departamentos)
															@foreach($departamentos as $departamento)
                                                                <option style="padding-left: 20px"  value="{{ $departamento->cod_departamento}}"  {{ old('id_departamento', optional($users)->id_departamento) == $departamento->cod_departamento ? 'selected' : '' }}>
                                                                    @for($i = 1; $i <= $departamento->num_nivel; $i++) &nbsp;&nbsp;&nbsp; @endfor{{ $departamento->nom_departamento}}
                                                                </option>
															@endforeach
														@endisset
													</select>
												<br>
											</div>
										</div>
                                        <div class="col-md-3">
											<div class="form-group">
												<label for="name" class="control-label">ID Externo</label>
                                                <input class="form-control" name="id_usuario_externo" type="text" id="id_usuario_externo" value="{{ old('id_usuario_externo', optional($users)->id_usuario_externo) }}" minlength="1" maxlength="255" required="true" placeholder="Enter id_usuario_externo here...">
                                                {!! $errors->first('id_usuario_externo', '<p class="help-block">:message</p>') !!}
											</div>
										</div>
                                    </div>
                                    <div class="row mt-2">
                                        <label class="text-nowrap">Notificacion</label><br>
                                        <div class="col-md-3  mt-1">
                                            <div class="form-check pt-3">
                                                <input  name="mca_notif_push"  id="mca_notif_push" value="S" {{ isset($users->mca_notif_push)&&$users->mca_notif_push=='S'?'checked':'' }} class="form-check-input" type="checkbox">
                                                <label class="form-check-label" for="mca_notif_push">Web PUSH</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3  mt-1">
                                            <div class="form-check pt-3">
                                                <input  name="mca_notif_email"  id="mca_notif_email" value="S" {{ isset($users->mca_notif_email)&&$users->mca_notif_email=='S'?'checked':'' }} class="form-check-input" type="checkbox">
                                                <label class="form-check-label" for="mca_notif_email">e-mail</label>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-6 {{ $errors->has('email') ? 'has-error' : '' }}">
                                            <label for="email" class="control-label">PlayerID</label>
                                            <input class="form-control" name="id_onesignal" disabled type="text" id="id_onesignal" value="{{ old('id_onesignal', optional($users)->id_onesignal) }}" minlength="1" maxlength="255" required="true" placeholder="Enter PlayerID here...">
                                            {!! $errors->first('id_onesignal', '<p class="help-block">:message</p>') !!}
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-12" >
                                            <div class="form-group"  style="overflow: hidden">
                                                <label class="text-nowrap">{{trans('strings.collectives')}}</label><br>
                                                <select  name="val_colectivo[]" multiple="" class="form-control  select2" style="width: 100%" id="val_colectivo">
                                                    @foreach ($colectivos_cliente as $col)
                                                        <option {{ in_array($col->cod_colectivo,$colectivos_user)===true ? 'selected' : ''}} value="{{$col->cod_colectivo}}">{{$col->des_colectivo}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row rounded b-all mt-4 pb-3">
                                        <div class="col-md-4" >
                                            <div class="form-group ">
                                                <label><b>Supervisor</b></label><br>
                                                <select name="id_usuario_supervisor" id="id_usuario_supervisor" class="select2 form-control" style="width:350px">
                                                    <option value=""></option>
                                                    @foreach ($supervisores as $c)
                                                        <option {{isset($users) && $users->id_usuario_supervisor == $c->id ? 'selected' : ''}} value="{{$c->id}}">{{$c->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        @if(isset($users) && isSupervisor($users->id))
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label>Usuarios a los que supervisa</label><br>
                                                    <select name="lista_id[]" id="lista_id" class="select2 select2-filtro mb-2 select2-multiple form-control" multiple="multiple">
                                                        <option value=""></option>
                                                        @foreach ($usuarios_supervisables as $c)
                                                            <option {{ in_array($c->id,$usuarios_supervisados) ? 'selected' : ''}} value="{{$c->id}}">{{$c->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="demo-stk-lft-tab-6"  class="tab-pane fade " role="tabpanel" aria-labelledby="seguridad-tab">
                            <div class="card">
                                <div class="card-header">
                                    <span class="text-main text-semibold float-left mr-5">@if ($users->two_factor_secret!=null) <i class="fa-solid fa-circle-check text-success check_2fa"></i>@endif  Autenticacion de doble factor</span>
                                    @if ($users->two_factor_secret!=null)
                                    <a href="#" class="btn btn-danger btn_desactivar_2fa">
                                        Desactivar
                                    </a>
                                    @else
                                    <a href="#" class="badge bg-warning">
                                        No activada
                                    </a>
                                    @endif
                                </div>
                                <div class="card-body" id="content_2fa">
                                    @if ($users->two_factor_secret!=null)
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>QR de activacion</label><br>
                                            {!! App\user::find($users->id)->twoFactorQrCodeSvg()!!}<br>
                                           {{ decrypt(App\user::find($users->id)->two_factor_secret)}}
                                        </div>
                                        <div class="col-md-6">
                                            <label>Codigos de emergencia</label>
                                            <ul class="list-group mb-2" style="columns: 2; -webkit-columns: 2; -moz-columns: 2;">
                                            @foreach (json_decode(decrypt(App\user::find($users->id)->two_factor_recovery_codes)) as $code)
                                                <li class="list-group-item">{{ $code }}</li>
                                            @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                    @endif
                                    
                                </div>
                            </div>
                        </div>
                        <div id="demo-stk-lft-tab-3"  class="tab-pane fade " role="tabpanel" aria-labelledby="reserva-tab">
                            <div class="card">
                                <div class="card-header">
                                    <p class="text-main text-semibold">Reserva automatica</p>
                                </div>
                                <input type="hidden" name="list_puestos_preferidos" id="list_puestos_preferidos" value="{{ $users->list_puestos_preferidos }}">
                                <div class="card-body">
                                    @if(isset($users))
                                        <div class="row rounded b-all mb-2 bg-gray-light">
                                            <div class="col-md-12">
                                                <label><b>Turno de asistencia</b></label><br>
                                                @foreach($turnos as $t)
                                                <div class="form-group col-md-3">
                                                    <div class="form-check pt-2">
                                                        <input  name="turno[]" id="turno{{$t->id_turno}}" value="{{$t->id_turno}}" {{ in_array($t->id_turno,$turnos_usuario)?'checked':'' }} class="form-check-input chkdia" type="checkbox">
                                                        <label class="form-check-label" for="turno{{$t->id_turno}}"><b>{{$t->des_turno}} <i class="fa-solid fa-square" style="color: {{ $t->val_color }}"></i></b></label>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label><b>Preferencia de asignacion automática de puestos</b></label>
                                        </div>
                                        <div class="row">
                                            
                                            <div class="col-md-5">
                                                <label>Criterios seleccionados</label>
                                                <div id="sortable" style="border: 1px dashed #ccc; padding: 5px 5px 30px 5px; border-radius: 9px">
                                                    <div class="ui-state-default ui-state-disabled static"><i class="fa-solid fa-clock-rotate-left" id="u" type="ul"></i> <div class="detalle" data-tipo="ul" data-id="20">Ultimas 20 reservas</div></div>
                                                    @foreach ($pref_turnos??[] as $pf )
                                                        @if($pf->tipo !='ul')
                                                            <div id="puesto_pref" class="draggable" style="background-color:{{ $pf->color }} ">
                                                                <h4 class="text-white">{!! $pf->icono !!} </h4>
                                                                <div class="detalle" data-id="{{ $pf->id }}" data-tipo="{{ $pf->tipo }}">{{ $pf->text }}</div>
                                                                <div class="bottom-right text-end papelera text-danger">
                                                                    <a href="#" class="btn_borrar"><i class="fa-solid fa-trash-can"></i></a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                            </div>
                                            <div class="col-md-5">
                                                <label>Criterios disponibles</label>
                                                <div>
                                                    <div id="puesto_pref" class="draggable" style="background-color:#efc3e6 ">
                                                        <h4 class="text-white"><i class="fa-solid fa-chair-office"></i> Puesto</h4>
                                                        <select class="puesto_pref identificador form-control">
                                                            <option value=""></option>
                                                            @foreach($edificios as $e)
                                                                @php
                                                                    $plantas_edificio=$plantas_usuario->where('id_edificio',$e->id_edificio)->pluck('des_planta','id_planta')->unique();
                                                                @endphp
                                                                <optgroup label="{{$e->des_edificio}}">
                                                                    @foreach($plantas_edificio as $key=>$value)
                                                                        <optgroup label="{{$value}}">
                                                                            @php
                                                                                $puestos_plantas=$puestos->where('id_planta',$key);
                                                                            @endphp
                                                                            @foreach($puestos_plantas as $t)
                                                                                <option value="{{$t->id_puesto}}">{{ nombrepuesto($t) }}</option>
                                                                            @endforeach
                                                                        </optgroup>
                                                                    @endforeach
                                                                </optgroup>
                                                            @endforeach
                                                        </select>
                                                        <div class="detalle" data-id="0" data-tipo="pu" style="display: none"></div>
                                                        <div class="bottom-right text-end papelera text-danger"  style="display: none">
                                                            <a href="#" class="btn_borrar"><i class="fa-solid fa-trash-can"></i></a>
                                                        </div>
                                                    </div>
                                                    <div id="planta_pref" class="draggable" style="background-color:#fcc14a;">
                                                        <h4 class="text-white"><i class="fa-solid fa-layer-group"></i> Planta</h4>
                                                        @php
                                                            $edificios=$plantas_usuario->unique('id_edificio')->pluck('des_edificio','id_edificio')->all();
                                                        @endphp
                                                        <select class="puesto_pref identificador form-control">
                                                            <option value=""></option>
                                                            @foreach($edificios as $key=>$value)
                                                                @php
                                                                    $ple=$plantas_usuario->where('id_edificio',$key)->pluck('des_planta','id_planta')->unique()->all();
                                                                @endphp
                                                                <optgroup label="{{$value}}">
                                                                    @foreach($ple as $key=>$value)
                                                                        <option value="{{$key}}">{{ $value }}</option>
                                                                    @endforeach
                                                                </optgroup>
                                                            @endforeach
                                                        </select>
                                                        <div class="detalle" data-id="0" data-tipo="pl" style="display: none"> </div>
                                                        <div class="bottom-right text-end papelera text-danger"  style="display: none">
                                                            <a href="#" class="btn_borrar"><i class="fa-solid fa-trash-can"></i></a>
                                                        </div>
                                                    </div>
                                                    <div id="zona_pref" class="draggable" style="background-color:#c6dabe" >
                                                        <h4 class="text-white"><i class="fa-solid fa-draw-square"></i> Zona</h4>
                                                        @php
                                                            $edificios=$plantas_usuario->unique('id_edificio')->pluck('des_edificio','id_edificio')->all();
                                                        @endphp
                                                        <select class="puesto_pref identificador form-control">
                                                            <option value=""></option>
                                                            @foreach($edificios as $key=>$value)
                                                                @php
                                                                    $ple=$plantas_usuario->where('id_edificio',$key)->pluck('des_planta','id_planta')->unique()->all();
                                                                @endphp
                                                                <optgroup label="{{$value}}">
                                                                    @foreach($ple as $key=>$value)
                                                                        <optgroup label="{{$value}}">
                                                                            @php
                                                                                $z=$plantas_usuario->where('id_planta',$key)->pluck('des_zona','key_id')->unique()->all();
                                                                            @endphp
                                                                            @foreach($z as $key=>$value)
                                                                                <option value="{{$key}}">{{ $value }}</option>
                                                                            @endforeach
                                                                        </optgroup>
                                                                    @endforeach
                                                                </optgroup>
                                                            @endforeach
                                                        </select>
                                                        <div class="detalle" data-id="0" data-tipo="zo" style="display: none"> </div>
                                                        <div class="bottom-right text-end papelera text-danger"  style="display: none">
                                                            <a href="#" class="btn_borrar"><i class="fa-solid fa-trash-can"></i></a>
                                                        </div>
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                        
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div id="demo-stk-lft-tab-4"  class="tab-pane fade" role="tabpanel" aria-labelledby="puestos-tab">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Tipos de puesto que puede reservar</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row rounded b-all mb-2 bg-gray-light">
                                        <div class="col-md-12">
                                            <label><b>Tipos de puesto que puede reservar</b></label><br>
                                            @foreach($tipos_puestos as $t)
                                                <div class="form-group col-md-4">
                                                    <div class="form-check pt-2">
                                                        <input name="tipos_puesto_admitidos[]" id="tipo_puesto{{$t->id_tipo_puesto}}" value="{{$t->id_tipo_puesto}}" {{ in_array($t->id_tipo_puesto,$tipos_puesto_usuario)?'checked':'' }} class="form-check-input" type="checkbox">
                                                        <label class="form-check-label" for="tipo_puesto{{$t->id_tipo_puesto}}"> {{$t->des_tipo_puesto}}</label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Plantas en las que puede reservar</h3>
                                </div>
                                <div class="card-body" id="plantas_usuario">
                        
                                </div>
                            </div>
                            @if(isSupervisor($users->id))
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Puestos que puede gestionar como supervisor</h3>
                                </div>
                                <div class="card-body" id="puestos_usuario">
                        
                                </div>
                            </div>
                            @endif
                        </div>
                        <div id="demo-stk-lft-tab-5"  class="tab-pane fade" role="tabpanel" aria-labelledby="actividad-tab">
                            
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Bitácora de {{ $users->name }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="tablapuestos"  data-toggle="table" data-mobile-responsive="true"
                                            data-locale="es-ES"
                                            data-search="true"
                                            data-show-columns="true"
                                            data-show-columns-toggle-all="true"
                                            data-page-list="[5, 10, 20, 30, 40, 50]"
                                            data-page-size="10"
                                            data-pagination="true" 
                                            data-show-button-text="true"
                                            >
                                            <thead>
                                                <tr>
                                                    <th>Modulo</th>
                                                    <th>Seccion</th>
                                                    <th style="width:30%">Accion</th>
                                                    <th>Status</th>
                                                    <th style="width: 140px">Fecha</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($bitacoras as $bitacora)
                                                <tr @if($bitacora->status=="error" || strpos($bitacora->accion,"ERROR:")!==false) class="bg-red color-palette" @endif>
                                                    <td>{{ $bitacora->id_modulo }}</td>
                                                    <td>{{ $bitacora->id_seccion }}</td>
                                                    <td style="word-break: break-all;">{{ $bitacora->accion }}</td>
                                                    <td class="text-center" ><span @if(strtoupper($bitacora->status)=="OK") class="badge p-2 bg-success" @else class="badge p-2 bg-danger" @endif style="padding: 0 5px 0 5px">{{ $bitacora->status }}</span></td>
                                                    <td>{!! beauty_fecha($bitacora->fecha) !!}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Reservas de {{ $users->name }}</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row mt-2 ">
                                        <div class="fluid col-md-12">
                                            <div id='demo-calendar'></div>
                                            <div id="events-popover-head" class="hide">Events</div>
                                            <div id="events-popover-content" class="hide">Test</div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group mt-2">
                    <div class="col-md-12 text-end">
                        <input class="btn btn-primary btn-lg btn_guardar" type="submit" value="Guardar">
                    </div>
                </div>
            </form>

        </div>
    </div>


<script src="{{ asset('plugins/fullcalendar/lib/main.min.js') }}"></script>
<script src="{{ asset('plugins/fullcalendar/lib/locales/es.js') }}"></script>
<script src="{{ asset('plugins/fullcalendar/tooltip.min.js') }}"></script>
    <script>
        $(function(){
            $('#plantas_usuario').load("{{ url('users/plantas/'.$users->id) }}/1")
        });

        $('.configuracion').addClass('active active-sub');
	    $('.usuarios').addClass('active-link');
        @if(isSupervisor($users->id))
            $(function(){
                $('#puestos_usuario').load("{{ url('users/puestos_supervisor/'.$users->id) }}")
            });
        @endif

        $('#tablapuestos').bootstrapTable();
        
        
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
    
                reader.onload = function (e) {
                    $('#img_preview').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $(".select2").select2();
    
        $(".select2-filtro").select2({
            placeholder: "Todos",
            allowClear: true,
            @desktop width: "90%", @elsedesktop width: "75%", @enddesktop 
        });
    
        $('.select-all').click(function(event) {
            $(this).parent().parent().find('select option').prop('selected', true)
            $(this).parent().parent().find('select').select2({
                placeholder: "Todos",
                allowClear: true,
                width: "99.2%",
            });
            $(this).parent().parent().find('select').change();
        });
    
        $("#img_usuario").change(function(){
            readURL(this);
        });
    
        $('#btn_generar_token').click(function(event){
            //console.log('token');
            $.get( "/users/gen_token/{{ $users->id??'' }}")
            .done(function( data, textStatus, jqXHR ) {
                $('#token_1uso').val(data.access_token);
            })
            .fail(function( jqXHR, textStatus, errorThrown ) {
                    console.log(errorThrown);
            });	
        })
        
        $('#btn_generar_password').click(function(event){
            //console.log('token');
            $.get( "/users/gen_password/{{ $users->id??'' }}")
            .done(function( data, textStatus, jqXHR ) {
                $('#password').val(data.pwd);
                $('#password').attr('type', 'text')
            })
            .fail(function( jqXHR, textStatus, errorThrown ) {
                    console.log(errorThrown);
            });	
        })
    
        $('.notsearch').select2({
            minimumResultsForSearch: -1,
            width: "100%"
        });
    
        $('.chkdia').click(function(){
            $.get("{{ url('users/turno') }}/{{ $users->id??0 }}/"+$(this).val()+"/"+$(this).is(':checked'));
        })
    
        $('#actividad').click(function(){
            setTimeout(function(){
                calendar.render();
            },200)
        })
    
        $( function() {
            
            $( "#sortable" ).sortable({
                revert: true,
                dropOnEmpty: true,
                connectWith : ".draggable",
                stop: function( event, ui ) {
    
                    ui.item.find('.puesto_pref').css('display','none');
                    ui.item.find('.detalle').css('display','block');
                    ui.item.find('.papelera').css('display','block');
                    ui.item.attr('id',ui.item.find('.puesto_pref').val());
                    ui.item.attr('type',ui.item.attr('type'));
                    $('.btn_borrar').click(function(){
                        $(this).parents(':eq(1)').remove();
                    });
                    if(ui.item.find('.detalle').data('id')==0){
                        console.log("El elemento no tiene ID");
                            
                        $( "#sortable" ).sortable( "cancel" );
                        toast_error('Error', 'Debe seleccionar una opcion en el elemento');
                        ui.item.remove();
                    }
                }
            });
    
            $( ".draggable" ).draggable({
                connectToSortable: "#sortable",
                helper: "clone",
                revert: "invalid",
                opacity: 0.7,
            });
            $( "ul, li" ).disableSelection();
        });
            
        $('.puesto_pref').on('change',function(){
            $(this).next('.detalle').attr('data-id', $(this).val());
            $(this).next('.detalle').html($(this).find('option:selected').text());
        })
    
        $('.btn_guardar').click(function(){
    
            if($('#demo-stk-lft-tab-3').hasClass('active')){
                event.preventDefault();
                resultado=[];
                $('#sortable').children().each(function(item){
                    elem = new Object();
                    elem.id=$(this).find('.detalle').data('id');
                    elem.tipo=$(this).find('.detalle').data('tipo');
                    elem.text=$(this).find('.detalle').html();
                    elem.color=$(this).css( "background-color" );
                    elem.icono=$(this).find('h4').html();
                    resultado.push(elem);
                });
                console.log(resultado);
                $('#list_puestos_preferidos').val(JSON.stringify(resultado));
                $('#edit_users_form').submit();
            }
        });

        $('.btn_borrar').click(function(){
            console.log("papelera");
           $(this).parents(':eq(1)').remove();
        });

        $('.btn_desactivar_2fa').click(function(){
            $.get("{{ url('users/activar_2fa/'.$users->id) }}/D")
            .done(function( data, textStatus, jqXHR ) {
                $('.btn_activar_2fa').html('Activar 2FA');
                $('.btn_desactivar_2fa').hide();
                $('.btn_activar_2fa').show();
                $('.check_2fa').hide();
                $('#content_2fa').empty();
            })
            .fail(function( jqXHR, textStatus, errorThrown ) {
                    console.log(errorThrown);
            });	
        })
      
    // Initialize the calendar
    // -----------------------------------------------------------------
    var calendarEl = document.getElementById('demo-calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listWeek'
            },
            eventDidMount: function(info) {
                //console.log(info);
                var tooltip = new Tooltip(info.el, {
                title: info.event.extendedProps.description,
                placement: 'top',
                trigger: 'hover',
                container: 'body'
                });
            },
            eventLimit: 4,

            eventLimitClick: function (cellInfo, jsEvent) {
        
                $(cellInfo.dayEl).popover({
                    html: true,
                    placement: 'bottom',
                    container: 'body',
                    title: function () {
                        return $("#events-popover-head").html();
                    },
                    content: function () {
                        return $("#events-popover-content").html();
                    }
                });

                $(cellInfo.dayEl).popover('show');
            },
            dayClick: function (cellInfo, jsEvent) {
                $(this).popover({
                    html: true,
                    placement: 'bottom',
                    container: 'body',
                    title: function () {
                        return $("#events-popover-head").html();
                    },
                    content: function () {
                        return $("#events-popover-content").html();
                    }
                });

                $(this).popover('show');
            },
            editable: false,
            droppable: false, // this allows things to be dropped onto the calendar
            eventLimit: true, // allow "more" link when too many events
            locale: 'es',
            firstDay: 1,
            themeSystem: 'bootstrap',
            moreLinkClick: "popover",
            dayMaxEventRows: 4,
            initialView: 'listWeek',
            events: {!! $eventos !!}
        });
        calendar.render();


        $('.form-ajax').submit(form_ajax_submit);

        document.querySelectorAll( ".btn-close-card" ).forEach( el => el.addEventListener( "click", (e) => el.closest( ".card" ).remove()) );
    
</script>

