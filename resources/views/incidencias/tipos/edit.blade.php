
    @php
        use App\Models\puestos_tipos;   
    @endphp
    <div class="card editor mb-5">

        <div class="card-header toolbar">
            <div class="toolbar-start">
                <h5 class="m-0">
                    @if($id==0)
                        Nuevo tipo de incidencia
                    @else
                        Editar tipo de incidencia
                    @endif
                </h5>
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

            <form method="POST" action="{{ url('/incidencias/tipos/save') }}" id="edit_tipos_incidencia_form" name="edit_tipos_incidencia_form" accept-charset="UTF-8" class="form-horizontal form-ajax">
            {{ csrf_field() }}
                <div class="row">
                        <input type="hidden" name="id" value="{{ $id }}">
                        <div class="form-group col-md-8 {{ $errors->has('des_tipo_incidencia') ? 'has-error' : '' }}">
                            <label for="des_tipo_incidencia" class="control-label">Nombre</label>
                            <input class="form-control" required name="des_tipo_incidencia" type="text" id="dedes_tipo_incidencias_edificio" value="{{ old('des_tipo_incidencia', optional($tipo)->des_tipo_incidencia) }}" maxlength="200" placeholder="Enter nombre here...">
                            {!! $errors->first('des_tipo_incidencia', '<p class="help-block">:message</p>') !!}
                        </div>

                        @if(checkPermissions(['Tipos de incidencia'],['D']) && ( $tipo->mca_fijo!='S' || ($tipo->mca_fijo=='S' && fullAccess())))
                        <div class="form-group col-md-4 {{ $errors->has('id_cliente') ? 'has-error' : '' }}">
                            <label for="id_cliente" class="control-label">Cliente</label>
                            <select class="form-control" required id="id_cliente" name="id_cliente">
                                @foreach ($Clientes as $key => $Cliente)
                                    <option value="{{ $key }}" {{ old('id_cliente', optional($tipo)->id_cliente) == $key ? 'selected' : '' }}>
                                        {{ $Cliente }}
                                    </option>
                                @endforeach
                            </select>
                                
                            {!! $errors->first('id_cliente', '<p class="help-block">:message</p>') !!}
                        </div>
                        @endif

                        
                </div>
                <div class="row mt-2">
                    
                    <div class="form-group col-md-6 {{ $errors->has('id_estado_inicial') ? 'has-error' : '' }}">
                        <label for="id_estado_inicial" class="control-label">Estado inicial</label>
                        <select class="form-control" required id="id_estado_inicial" name="id_estado_inicial">
                            @foreach ($estados as $estado)
                                <option value="{{ $estado->id_estado }}" {{ old('id_estado_inicial', optional($tipo)->id_estado_inicial) == $estado->id_estado ? 'selected' : '' }}>
                                    {{ $estado->des_estado }}
                                </option>
                            @endforeach
                        </select>
                            
                        {!! $errors->first('id_estado_inicial', '<p class="help-block">:message</p>') !!}
                    </div>
                    <div class="form-group col-md-2">
                        <label for="val_responsable" class="control-label">Responsable</label>
                        <input class="form-control"  name="val_responsable" type="text" id="val_responsable" value="{{ old('val_responsable', optional($tipo)->val_responsable) }}" maxlength="200" placeholder="Enter val_responsable here...">
                        {!! $errors->first('val_responsable', '<p class="help-block">:message</p>') !!}
                    </div>
                    <div class="form-group col-md-2">
                        <label for="id_tipo_salas" class="control-label">ID en salas</label>
                        <input class="form-control"  name="id_tipo_salas" type="text" id="id_tipo_salas" value="{{ old('id_tipo_salas', optional($tipo)->id_tipo_salas) }}" maxlength="200" placeholder="Enter id_tipo_salas here...">
                        {!! $errors->first('id_tipo_salas', '<p class="help-block">:message</p>') !!}
                    </div>
                    <div class="form-group col-md-2">
                        <label for="id_tipo_externo" class="control-label">ID externo</label>
                        <input class="form-control"  name="id_tipo_externo" type="text" id="id_tipo_externo" value="{{ old('id_tipo_externo', optional($tipo)->id_tipo_externo) }}" maxlength="200" placeholder="Enter id_tipo_externo here...">
                        {!! $errors->first('id_tipo_externo', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="form-group col-md-2" style="margin-top: 7px">
                        <label for="val_color">Color</label><br>
                        <input type="text" autocomplete="off" name="val_color" id="val_color"  class="minicolors form-control" value="{{isset($tipo->val_color)?$tipo->val_color:App\Classes\RandomColor::one(['luminosity' => 'bright'])}}" />
                    </div>
                    <div class="form-group col-md-1 mt-2" style="margin-left: 10px">
                        <div class="form-group">
                            <label>Icono</label><br>
                            <button type="button"  role="iconpicker" required name="val_icono"  id="val_icono" data-iconset="fontawesome5"  data-placement="right"  class="btn btn-light iconpicker" data-iconset-version="5.3.1_pro" data-search="true" data-rows="10" data-cols="20" data-search-text="Buscar..." value="{{isset($tipo->val_icono)?$tipo->val_icono:''}}"></button>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="form-group  col-md-12" style="{{ (isset($hide['tip']) && $hide['tip']==1) ? 'display: none' : ''  }}">
                        <label>Tipo de puesto</label>
                        <div class="input-group select2-bootstrap-append">
                            <select class="select2 select2-filtro mb-2 select2-multiple form-control" multiple="multiple" name="tipos_puesto[]" id="multi-tipo" >
                                @php
                                    $tipos_puesto=explode(",",$tipo->list_tipo_puesto);
                                @endphp
                                @foreach(puestos_tipos::where(function($q) {
                                    $q->where('id_cliente',Auth::user()->id_cliente);
                                    $q->orwhere('mca_fijo','S');
                                    })
                                    ->where('id_tipo_puesto','>',0)
                                    ->get() as $tp)
                                    <option value="{{ $tp->id_tipo_puesto }}" {{ in_array($tp->id_tipo_puesto,$tipos_puesto)?'selected':'' }}>{{ $tp->des_tipo_puesto }}</option>
                                @endforeach
                            </select>
                            <div class="input-group-btn">
                                <button class="btn btn-primary select-all btn_todos" data-select="multi-estado"  type="button" style="margin-left:-10px; height: 45px"><i class="fad fa-check-double"></i> todos</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row bg-gray mt-4">
                    <div class="col-md-3">
                        <h5>Postprocesado de la incidencia<h5>
                    </div>
                    <div class="col-md-2 ">
                        <select class="form-control col-md-2 float-left" style="margin-top: 2px" required id="val_momento" name="val_momento">
                                <option value="C">Creacion</option>
                                <option value="A">Accion</option>
                                <option value="F">Cierre</option>
                                <option value="R">Reapertura</option>
                        </select>
                    </div>
                    <div class="col-md-7">
                        <div class="btn-group btn-group-sm pull-right" role="group">
                            <a href="#nueva-incidencia" id="btn_nueva" class="btn btn-success text-white" data-toggle="modal" title="Nueva accion">
                                <i class="fa fa-plus-square pt-2" style="font-size: 20px" aria-hidden="true"></i>
                                <span>Nueva</span>
                            </a>
                        </div>
                    </div>
                </div>
                

                <div class="row b-all rounded">
                    <div class="col-md-12" id="divacciones"></div>
                </div>
                

                <div class="form-group">
                    <div class="col-md-12 text-end">
                        @if(checkPermissions(['Tipos de incidencia'],['D']) && ( $tipo->mca_fijo!='S' || ($tipo->mca_fijo=='S' && fullAccess())))<input class="btn btn-primary" type="submit" value="Guardar">@else <span class="bg-warning">Usted no puede modificar este dato</span>@endif
                    </div>
                </div>
            </form>

        </div>
    </div>

    <script>
        function prettyPrint(el){ 
            
           try{
            var ugly = el.val();
            var obj = JSON.parse(ugly);
            var pretty = JSON.stringify(obj, undefined, 4);
            //console.log(pretty);
            el.val(pretty);
           } catch(e){
            
           }
        }


        $('.form-ajax').submit(form_ajax_submit);
        $('.opciones').hide();
        $('#tip_metodo').change(function(){
            $('.opciones').hide();
            $('.'+$(this).val()).show();
        })
        $('#tip_metodo').change();

        $('.minicolors').minicolors({
          control: $(this).attr('data-control') || 'hue',
          defaultValue: $(this).attr('data-defaultValue') || '',
          format: $(this).attr('data-format') || 'hex',
          keywords: $(this).attr('data-keywords') || '',
          inline: $(this).attr('data-inline') === 'true',
          letterCase: $(this).attr('data-letterCase') || 'lowercase',
          opacity: $(this).attr('data-opacity'),
          position: $(this).attr('data-position') || 'bottom',
          swatches: $(this).attr('data-swatches') ? $(this).attr('data-swatches').split('|') : [],
          change: function(value, opacity) {
            if( !value ) return;
            if( opacity ) value += ', ' + opacity;
          },
          theme: 'bootstrap'
        });

    //$('#frm_contador').on('submit',form_ajax_submit);
    $('#frm_contador').submit(form_ajax_submit);

    $('#val_icono').iconpicker({
        icon:'{{isset($tipo->val_icono) ? ($tipo->val_icono) : ''}}'
    });

    $(function(){
        $('#divacciones').load("{{ url('/incidencias/tipos/postprocesado/'.$tipo->id_tipo_incidencia) }}/C");
    });

    $('#val_momento').change(function(){
        $('#divacciones').load("{{ url('/incidencias/tipos/postprocesado/'.$tipo->id_tipo_incidencia) }}/"+$('#val_momento').val());
    });

    $('.select-all').click(function(event) {
        $(this).parent().parent().find('select option').prop('selected', true)
        $(this).parent().parent().find('select').select2({
            placeholder: "Todos",
            allowClear: true,
            width: "90%",
        });
        $(this).parent().parent().find('select').change();
    });

    $(".select2-filtro").select2({
        placeholder: "Todos",
        allowClear: true,
        width: "90%",
    });

    document.querySelectorAll( ".btn-close-card" ).forEach( el => el.addEventListener( "click", (e) => el.closest( ".card" ).remove()) );

    
    </script>
    @include('layouts.scripts_panel')