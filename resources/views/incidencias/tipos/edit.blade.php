
    <div class="panel">

        <div class="panel-heading">
            <div class="panel-control">
                <button class="btn btn-default" data-panel="dismiss"><i class="demo-psi-cross"></i></button>
            </div>
            <h3 class="panel-title" id="titulo">
                @if($id==0)
                    Nuevo tipo de incidencia
                @else
                    Editar tipo de incidencia
                @endif

            </h3>
        </div>

        <div class="panel-body">

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

                        
                </div>
                <div class="row">
                    <div class="form-group col-md-3  ">
                        <label for="tip_metodo" class="control-label">Método</label>
                        <select required class="form-control" id="tip_metodo" name="tip_metodo">
                                {{-- <option value="S" {{ $tipo->tip_metodo== 'S' ? 'selected' : '' }} >SMS</option> --}}
                                <option value="M" {{ $tipo->tip_metodo== 'M' ? 'selected' : '' }} >e-mail</option>
                                <option value="P" {{ $tipo->tip_metodo== 'P' ? 'selected' : '' }} >HTTP Post</option>
                                <option value="G" {{ $tipo->tip_metodo== 'G' ? 'selected' : '' }} >Http Get</option>
                                <option value="L" {{ $tipo->tip_metodo== 'L' ? 'selected' : '' }} >Gestionar en spotlinker</option>
                                <option value="N" {{ $tipo->tip_metodo== 'N' ? 'selected' : '' }} >Solo registrar</option>
                        </select>
                        {!! $errors->first('tip_metodo', '<p class="help-block">:message</p>') !!}
                    </div>
                    <div class="form-group col-md-2" style="margin-top: 7px">
                        <label for="val_color">Color</label><br>
                        <input type="text" autocomplete="off" name="val_color" id="val_color"  class="minicolors form-control" value="{{isset($puesto->val_color)?$puesto->val_Color:App\Classes\RandomColor::one(['luminosity' => 'bright'])}}" />
                    </div>
                    <div class="form-group col-md-1 mt-2" style="margin-left: 10px">
                        <div class="form-group">
                            <label>Icono</label><br>
                            <button type="button"  role="iconpicker" required name="val_icono"  id="val_icono" data-iconset="fontawesome5" class="btn btn-light iconpicker" data-search="true" data-rows="10" data-cols="30" data-search-text="Buscar..."></button>
                        </div>
                    </div>
                </div>
                <div class="row opciones P G">
                    <div class="form-group col-md-12 {{ $errors->has('val_url') ? 'has-error' : '' }}">
                        <label for="des_edificio" class="control-label">URL</label>
                        <input class="form-control" name="val_url" type="text" id="val_url" value="{{ old('val_url', optional($tipo)->val_url) }}" maxlength="200" placeholder="Enter URL here...">
                        {!! $errors->first('val_url', '<p class="help-block">:message</p>') !!}
                    </div>
                    <div class="form-group col-md-12 {{ $errors->has('param_url') ? 'has-error' : '' }}">
                        <label for="des_edificio" class="control-label">Parametros URL</label>
                        <input class="form-control" name="param_url" type="text" id="param_url" value="{{ old('param_url', optional($tipo)->vaparam_urll_url) }}" maxlength="1000" placeholder="Enter Param URL here...">
                        {!! $errors->first('param_url', '<p class="help-block">:message</p>') !!}
                    </div>
                    <div class="form-group col-md-8 {{ $errors->has('val_apikey') ? 'has-error' : '' }}">
                        <label for="val_apikey" class="control-label">API Key</label>
                        <input class="form-control" name="val_apikey" type="text" id="val_apikey" value="{{ old('val_apikey', optional($tipo)->val_url) }}" maxlength="500" placeholder="Enter API Key here...">
                        {!! $errors->first('val_apikey', '<p class="help-block">:message</p>') !!}
                    </div>

                    <div class="form-group col-md-4 {{ $errors->has('val_content_type') ? 'has-error' : '' }}">
                        <label for="val_content_type" class="control-label">Content-type</label>
                        <input class="form-control" name="val_content_type" type="text" id="val_content_type" value="{{ old('val_content_type', optional($tipo)->val_url) }}" maxlength="200" placeholder="Enter Content-type here...">
                        {!! $errors->first('val_content_type', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
                <div class="row opciones P">
                    <div class="form-group col-md-12 {{ $errors->has('val_body') ? 'has-error' : '' }}">
                        <label for="val_body" class="control-label">Body</label>
                        <textarea class="form-control" name="val_body" type="text" id="val_body" value="" maxlength="65535" placeholder="Enter URL here..." rows="8">{{ old('val_body', optional($tipo)->val_body) }}</textarea>
                        {!! $errors->first('val_body', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
                <div class="row opciones S M">
                    <div class="form-group col-md-12 {{ $errors->has('txt_destinos') ? 'has-error' : '' }}">
                        <label for="txt_destinos" class="control-label">Destinos <span style="font-size: 9px">(separados por ; )</span></label>
                        <textarea class="form-control" name="txt_destinos" type="text" id="txt_destinos" value="" maxlength="65535" placeholder="Enter Destinos here..." rows="4">{{ old('txt_destinos', optional($tipo)->txt_destinos) }}</textarea>
                        {!! $errors->first('val_txt_destinosurl', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
                <div class="row opciones P G">
                    <h3>Campos variables</h3>
                    <div class="col-md-6">
                        <b>#id_incidencia#:</b> Identificador unico de la incidencia<br>
                        <b>#des_incidencia#:</b> Descripcion corta de la incidencia<br>
                        <b>#txt_incidencia#:</b> Descripcion larga de la incidencia<br>
                        <b>#nom_usuario#:</b> Usuario que ha abierto la incidencia<br>
                        <b>#ema_usuario#:</b> e-mail del usaurio que ha abierto la incidencia<br>
                        <b>#fec_apertura#:</b> Fecha de apertura de la incidencia<br>
                        <b>#id_tipo_incidencia#:</b> Identificador de tipo de la incidencia<br>
                    </div>
                    <div class="col-md-6">
                        <b>#des_tipo_incidencia#:</b> Tipo de la incidencia<br>
                        <b>#id_puesto#:</b> Identificador del puesto<br>
                        <b>#edificio#:</b> Edificio en le que esta el puesto<br>
                        <b>#planta#:</b> Planta en la que esta el puesto<br>
                        <b>#id_cliente#:</b> Identificador de cliente<br>
                        <b>#img1#:</b> Imagen adjunta 1<br>
                        <b>#img2#:</b> Imagen adjunta 2<br>
                    </div>
                    
                    
                </div>

                <div class="form-group">
                    <div class="col-md-12 text-right">
                        <input class="btn btn-primary" type="submit" value="Guardar">
                    </div>
                </div>
            </form>

        </div>
    </div>

    <script>
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
        icon:'{{isset($t) ? ($t->val_icono) : ''}}'
    });
    </script>
    @include('layouts.scripts_panel')