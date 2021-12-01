
<style type="text/css">
    .popover {
        z-index: 100000;
    }
</style>

    <div class="panel editor">
        <div class="panel-heading">
            <div class="panel-control">
                <button class="btn btn-default" data-panel="dismiss"><i class="demo-psi-cross"></i></button>
            </div>
            <h3 class="panel-title" id="titulo">
                @if($id==0)
                    Nuevo tipo de puesto
                @else
                    Editar tipo de puesto
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
            <form method="POST" action="{{ url('/puestos/tipos/save') }}" id="edit_tipos_puesto_form" name="edit_tipos_puesto_form" accept-charset="UTF-8" class="form-horizontal form-ajax">
            {{ csrf_field() }}
                <div class="row">
                        <input type="hidden" name="id" value="{{ $id }}">
                        <div class="form-group col-md-8 {{ $errors->has('des_tipo_puesto') ? 'has-error' : '' }}">
                            <label for="des_tipo_puesto" class="control-label">Nombre</label>
                            <input class="form-control" required name="des_tipo_puesto" type="text" id="dedes_tipo_puestos_edificio" value="{{ old('des_tipo_puesto', optional($tipo)->des_tipo_puesto) }}" maxlength="200" placeholder="Enter nombre here...">
                            {!! $errors->first('des_tipo_puesto', '<p class="help-block">:message</p>') !!}
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
                    
                    <div class="form-group col-md-2" style="margin-top: 7px">
                        <label for="val_color">Color</label><br>
                        <input type="text" autocomplete="off" name="val_color" id="val_color"  class="minicolors form-control" value="{{isset($tipo->val_color)?$tipo->val_color:App\Classes\RandomColor::one(['luminosity' => 'bright'])}}" />
                    </div>
                    <div class="form-group col-md-1 mt-2" style="margin-left: 10px">
                        <div class="form-group">
                            <label>Icono</label><br>
                            <button type="button"  role="iconpicker" required name="val_icono"  id="val_icono" data-iconset="fontawesome6" class="btn btn-light iconpicker" data-align="right" data-placement="inline" data-search="true" data-rows="10" data-cols="15" data-search-text="Buscar..."></button>
                        </div>
                    </div>
                    @if(isAdmin())
                    <div class="col-md-2 p-t-30 mt-1">
                        <input type="checkbox" class="form-control  magic-checkbox" name="mca_fijo"  id="mca_fijo" value="S" {{ $tipo->mca_fijo=='S'?'checked':'' }}> 
                        <label class="custom-control-label"   for="mca_fijo">Fijo</label>
                    </div>
                    @endif
                    
                    <div class="form-group col-md-2" style="margin-top: 7px">
                        <label for="max_usos">Usos simultaneo</label><br>
                        <input type="number" autocomplete="off" min="1" max="20" style="width: 100px"  name="max_usos" id="max_usos"  class="form-control" value="{{isset($tipo->max_usos)?$tipo->max_usos:1}}" />
                    </div>
                    <div class="col-md-4 b-all rounded p-0">
                        <div class="col-md-7 p-t-20 mt-2">
                            <input type="checkbox" class="form-control  magic-checkbox" name="mca_liberar_auto"  id="mca_liberar_auto" value="S" {{ isset($tipo->mca_liberar_auto) && $tipo->mca_liberar_auto=='S'?'checked':'' }}> 
                            <label class="custom-control-label"   for="mca_liberar_auto">Liberar reservas AUTO</label>
                        </div>
                        <div class="form-group col-md-5" style="margin-top: 7px">
                            <label for="max_usos">Cortesia (min)</label><br>
                            <input type="number" autocomplete="off" name="hora_liberar" id="hora_liberar" style="width: 120px" min="0" max="1440"  class="form-control" value="{{$tipo->hora_liberar??config_cliente('hora_liberar_puestos',$tipo->id_cliente)}}" />
                        </div>
                    </div>
                    
                </div>
                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="des_tipo_puesto" class="control-label">Observaciones</label>
                        <input class="form-control" name="observaciones" type="text" id="observaciones" value="{{ old('observaciones', optional($tipo)->observaciones) }}" maxlength="200" placeholder="Enter observaciones here...">
                        
                    </div>
                </div>
                

                <div class="form-group">
                    <div class="col-md-12 text-right">
                        @if(checkPermissions(['Tipos de puesto'],['W']) && ($tipo->mca_fijo!='S' || ($tipo->mca_fijo=='S' && fullAccess()))) <input class="btn btn-primary" type="submit" value="Guardar">@endif
                    </div>
                </div>
            </form>

        </div>
    </div>

    <script>
        $('.form-ajax').submit(form_ajax_submit);


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
        icon:'{{isset($tipo) ? ($tipo->val_icono) : ''}}'
    });

    $('.demo-psi-cross').click(function(){
        $('.editor').hide();
    });

    
    </script>
