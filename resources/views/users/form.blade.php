
@section('css')
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
</style>
@endsection

<div class="tab-base tab-stacked-left">
					
    <!--Nav tabs-->
    <ul class="nav nav-tabs">
        <li class="active">
            <a data-toggle="tab" href="#demo-stk-lft-tab-1" aria-expanded="true">Datos generales</a>
            
            
        </li>
        <li class="">
            <a data-toggle="tab" href="#demo-stk-lft-tab-2" aria-expanded="false">Configuracion</a>
            
        </li>
        <li class="">
            <a data-toggle="tab" href="#demo-stk-lft-tab-3" aria-expanded="false">Reserva automatica</a>
        </li>
        <li class="">
            <a data-toggle="tab" href="#demo-stk-lft-tab-4" aria-expanded="false">Plantas/puestos</a>
        </li>
        <li class="">
            <a data-toggle="tab" href="#demo-stk-lft-tab-5" aria-expanded="false" id="actividad">Actividad</a>
        </li>
    </ul>

    <!--Tabs Content-->
    <div class="tab-content">
        <div id="demo-stk-lft-tab-1" class="tab-pane fade">
            <div class="panel">
                <div class="panel-heading">
                    <p class="text-main text-semibold">Datos generales</p>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-10">
                            <div class="row">
                                <div class="form-group col-md-12 {{ $errors->has('name') ? 'has-error' : '' }}">
                                    <label for="name" class="control-label">Nombre</label>
                                    <input class="form-control" name="name" type="text" id="name" value="{{ old('name', optional($users)->name) }}" minlength="1" maxlength="255" required="true" placeholder="Enter name here...">
                                    {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6 {{ $errors->has('email') ? 'has-error' : '' }}">
                                    <label for="email" class="control-label">e-mail</label>
                                    <input class="form-control" name="email" type="text" id="email" value="{{ old('email', optional($users)->email) }}" minlength="1" maxlength="255" required="true" placeholder="Enter email here...">
                                    {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
                                </div>
                                <div class="form-group col-md-6 {{ $errors->has('password') ? 'has-error' : '' }}">
                                    <div class="input-group mb-3">
                                        <label for="password" class="control-label">Password</label>
                                        <input class="form-control" name="password" type="password" id="password"  minlength="4" maxlength="255" placeholder="Enter password here...">
                                        {!! $errors->first('password', '<p class="help-block">:message</p>') !!}
                                        <div class="input-group-btn">
                                            <button class="btn btn-mint mt-4" type="button"  id="btn_generar_password">Generar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-10">
                                    <label class="control-label">Token acceso</label>
                                    <div class="input-group mb-3">
                                        <input type="text" name="token_acceso" readonly=""  id="token_1uso"  class="form-control" value="{{isset($users) ? $users->token_acceso : ''}}">
                                        <div class="input-group-btn">
                                            <button class="btn btn-mint" type="button"  id="btn_generar_token">Generar</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1">
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
                                        <input type="file" accept=".jpg,.png,.gif" class="form-control  custom-file-input" name="img_usuario" id="img_usuario" lang="es" value="{{ isset($users) ? $users->img_usuario : ''}}">
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
        <div id="demo-stk-lft-tab-2" class="tab-pane fade panel-body">
            <div class="panel">
                <div class="panel-heading">
                    <p class="text-main text-semibold">Configuracion</p>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-3 mr-3">
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
                        <div class="col-md-3 mr-3">
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
                        <div class="col-md-3 mr-3">
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
                    </div>
                    <div class="row rounded b-all">
                        <div class="form-group col-md-3 ">
                            <label><b>Supervisor</b></label><br>
                            <select name="id_usuario_supervisor" id="id_usuario_supervisor" class="select2">
                                <option value=""></option>
                                @foreach ($supervisores as $c)
                                    <option {{isset($users) && $users->id_usuario_supervisor == $c->id ? 'selected' : ''}} value="{{$c->id}}">{{$c->name}}</option>
                                @endforeach
                            </select>
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
        <div id="demo-stk-lft-tab-3" class="tab-pane fade active in">
            <div class="panel">
                <div class="panel-heading">
                    <p class="text-main text-semibold">Reserva automatica</p>
                </div>
                <div class="panel-body">
                    @if(isset($users))
                        <div class="row rounded b-all mb-2 bg-gray-light">
                            <div class="col-md-12">
                                <label><b>Turno de asistencia</b></label><br>
                                @foreach($turnos as $t)
                                <div class="form-group col-md-3">
                                    <input type="checkbox" class="form-control  magic-checkbox chkdia" name="turno[]" id="turno{{$t->id_turno}}" value="{{$t->id_turno}}" {{ in_array($t->id_turno,$turnos_usuario)?'checked':'' }}> 
                                    <label class="custom-control-label"   for="turno{{$t->id_turno}}"><b>{{$t->des_turno}} <i class="fa-solid fa-square" style="color: {{ $t->val_color }}"></i></b></label><br>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="row">
                            <label><b>Preferencia de asignacion automática de puestos</b></label><br>
                            <div class="col-md-4">
                                <label>Criterios seleccionados</label>
                                <ul id="sortable1" class="connectedSortable">
                                    <li class="ui-state-default">Item 1</li>
                                    <li class="ui-state-default">Item 2</li>
                                    <li class="ui-state-default">Item 3</li>
                                    <li class="ui-state-default">Item 4</li>
                                    <li class="ui-state-default">Item 5</li>
                                  </ul>
                            </div>
                            <div class="col-md-4">
                            </div>
                            <div class="col-md-4">
                                <label>Criterios disponibles</label>
                                <ul id="sortable1" class="connectedSortable">
                                    <li class="ui-state-default">Item 1</li>
                                    <li class="ui-state-default">Item 2</li>
                                    <li class="ui-state-default">Item 3</li>
                                    <li class="ui-state-default">Item 4</li>
                                    <li class="ui-state-default">Item 5</li>
                                  </ul>
                            </div>
                        </div>
                        
                    @endif
                </div>
            </div>
            
            
            
        </div>
        <div id="demo-stk-lft-tab-4" class="tab-pane fade">
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title">Tipos de puesto que puede reservar</h3>
                </div>
                <div class="panel-body">
        
                </div>
            </div>
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title">Plantas en las que puede reservar</h3>
                </div>
                <div class="panel-body" id="plantas_usuario">
        
                </div>
            </div>
            @if(isSupervisor($users->id))
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title">Puestos que puede gestionar como supervisor</h3>
                </div>
                <div class="panel-body" id="puestos_usuario">
        
                </div>
            </div>
            @endif
        </div>
        <div id="demo-stk-lft-tab-5" class="tab-pane fade">
            
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title">Bitácora de {{ $users->name }}</h3>
                </div>
                <div class="panel-body panel-body-with-table">
                    <div class="table-responsive">
                        <table id="tablapuestos"  data-toggle="table"
                            data-locale="es-ES"
                            data-search="true"
                            data-show-columns="true"
                            data-show-columns-toggle-all="true"
                            data-page-list="[5, 10, 20, 30, 40, 50]"
                            data-page-size="10"
                            data-pagination="true" 
                            data-show-pagination-switch="true"
                            data-show-button-icons="true"
                            data-toolbar="#all_toolbar"
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
                                    <td class="text-center" ><span @if(strtoupper($bitacora->status)=="OK") class="bg-success" @else class="bg-danger" @endif style="padding: 0 5px 0 5px">{{ $bitacora->status }}</span></td>
                                    <td>{!! beauty_fecha($bitacora->fecha) !!}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="panel">
                <div class="panel-heading">
                    <h3 class="panel-title">Reservas de {{ $users->name }}</h3>
                </div>
                <div class="panel-body">
                    <div class="row mt-2 ">
                        <div class="col-md-1"></div>
                        <div class="fluid col-md-10">
                            <div id='demo-calendar'></div>
                            <div id="events-popover-head" class="hide">Events</div>
                            <div id="events-popover-content" class="hide">Test</div>
                        </div>
                        <div class="col-md-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>






@section('scripts2')
<script>
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#img_preview').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $(".select2-filtro").select2({
        placeholder: "Todos",
        allowClear: true,
        width: "99.2%",
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
    

</script>
@endsection
