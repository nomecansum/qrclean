
    <div class="panel">

        <div class="panel-heading">
            <div class="panel-control">
                <button class="btn btn-default" data-panel="dismiss"><i class="demo-psi-cross"></i></button>
            </div>
            <h3 class="panel-title" id="titulo">
                @if($id==0)
                    Nuevo estado de incidencia
                @else
                    Editar estado de incidencia
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

            <form method="POST" action="{{ url('/incidencias/estados/save') }}" id="edit_estado_form" name="edit_estado_form" accept-charset="UTF-8" class="form-horizontal form-ajax">
            {{ csrf_field() }}
                <div class="row">
                        <input type="hidden" name="id" value="{{ $id }}">
                        <div class="form-group col-md-8 {{ $errors->has('des_tipo_incidencia') ? 'has-error' : '' }}">
                            <label for="des_estado" class="control-label">Nombre</label>
                            <input class="form-control" required name="des_estado" type="text" id="des_estado" value="{{ old('des_estado', optional($estado)->des_estado) }}" maxlength="200" placeholder="Enter nombre here...">
                            {!! $errors->first('des_estado', '<p class="help-block">:message</p>') !!}
                        </div>


                        <div class="form-group col-md-4 {{ $errors->has('id_cliente') ? 'has-error' : '' }}">
                            <label for="id_cliente" class="control-label">Cliente</label>
                            <select class="form-control" required id="id_cliente" name="id_cliente">
                                @foreach ($Clientes as $key => $Cliente)
                                    <option value="{{ $key }}" {{ old('id_cliente', optional($estado)->id_cliente) == $key ? 'selected' : '' }}>
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
                        <input type="text" autocomplete="off" name="val_color" id="val_color"  class="minicolors form-control" value="{{isset($estado->val_color)?$estado->val_color:App\Classes\RandomColor::one(['luminosity' => 'bright'])}}" />
                    </div>
                    <div class="form-group col-md-1 mt-2" style="margin-left: 10px">
                        <div class="form-group">
                            <label>Icono</label><br>
                            <button type="button"  role="iconpicker" name="val_icono"  id="val_icono" data-iconset="fontawesome5" class="btn btn-light iconpicker" data-search="true" data-rows="10" data-cols="30" data-search-text="Buscar..."></button>
                        </div>
                    </div>
                    @if(isAdmin())
                    <div class="col-md-2 p-t-30 mt-1">
                        <input type="checkbox" class="form-control  magic-checkbox" name="mca_fijo"  id="mca_fijo" value="S" {{ $estado->mca_fijo=='S'?'checked':'' }}> 
                        <label class="custom-control-label"   for="mca_fijo">Fijo</label>
                    </div>
                    @endif
                    <div class="col-md-2 p-t-30 mt-1">
                        <input type="checkbox" class="form-control  magic-checkbox" name="mca_cierre"  id="mca_cierre" value="S" {{ $estado->mca_cierre=='S'?'checked':'' }}> 
                        <label class="custom-control-label"   for="mca_cierre">Implica cierre</label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-12 text-right">
                        @if(checkPermissions(['Estados de incidencia'],['D']) && ($estado->mca_fijo=='N' || ($estado->mca_fijo=='S' && fullAccess())))<input class="btn btn-primary" type="submit" value="Guardar">@endif
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

        $('#val_icono').iconpicker({
            icon:'{{isset($estado) ? ($estado->val_icono) : ''}}'
        });
    </script>