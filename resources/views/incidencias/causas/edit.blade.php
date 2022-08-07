
    <div class="panel editor">

        <div class="panel-heading">
            <div class="panel-control">
                <button class="btn btn-default" data-panel="dismiss"><i class="demo-psi-cross"></i></button>
            </div>
            <h3 class="panel-title" id="titulo">
                @if($id==0)
                    Nueva causa de cierre
                @else
                    Editar causa de cierre
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

            <form method="POST" action="{{ url('/incidencias/causas/save') }}" id="edit_causas_cierre_form" name="edit_causas_cierre_form" accept-charset="UTF-8" class="form-horizontal form-ajax">
            {{ csrf_field() }}
                <div class="row">
                        <input type="hidden" name="id" value="{{ $id }}">
                        <div class="form-group col-md-8 {{ $errors->has('des_tipo_incidencia') ? 'has-error' : '' }}">
                            <label for="des_causa_cierre" class="control-label">Nombre</label>
                            <input class="form-control" required name="des_causa" type="text" id="des_causa_cierre" value="{{ old('des_causa', optional($causa)->des_causa) }}" maxlength="200" placeholder="Enter nombre here...">
                            {!! $errors->first('des_causa_cierre', '<p class="help-block">:message</p>') !!}
                        </div>

                        @if(checkPermissions(['Causas de cierre'],['W']) && ($causa->mca_fija!='S' || ($causa->mca_fija=='S' && fullAccess())))
                        <div class="form-group col-md-4 {{ $errors->has('id_cliente') ? 'has-error' : '' }}">
                            <label for="id_cliente" class="control-label">Cliente</label>
                            <select class="form-control" required id="id_cliente" name="id_cliente">
                                @foreach ($Clientes as $key => $Cliente)
                                    <option value="{{ $key }}" {{ old('id_cliente', optional($causa)->id_cliente) == $key ? 'selected' : '' }}>
                                        {{ $Cliente }}
                                    </option>
                                @endforeach
                            </select>
                                
                            {!! $errors->first('id_cliente', '<p class="help-block">:message</p>') !!}
                        </div>
                        @endif

                        
                </div>
                <div class="row">
                    <div class="form-group col-md-2" style="margin-top: 7px">
                        <label for="val_color">Color</label><br>
                        <input type="text" autocomplete="off" name="val_color" id="val_color"  class="minicolors form-control" value="{{isset($causa->val_color)?$causa->val_color:App\Classes\RandomColor::one(['luminosity' => 'bright'])}}" />
                    </div>
                    <div class="form-group col-md-1 mt-2" style="margin-left: 10px">
                        <div class="form-group">
                            <label>Icono</label><br>
                            <button type="button"  role="iconpicker" name="val_icono"  id="val_icono" data-iconset="fontawesome5"  data-iconset-version="5.3.1_pro"  class="btn btn-light iconpicker" data-search="true" data-rows="10" data-cols="20" data-search-text="Buscar..."></button>
                        </div>
                    </div>
                    @if(isAdmin())
                    <div class="col-md-2 p-t-30 mt-1">
                        <input type="checkbox" class="form-control  magic-checkbox" name="mca_fija"  id="mca_fija" value="S" {{ $causa->mca_fija=='S'?'checked':'' }}> 
                        <label class="custom-control-label"   for="mca_fija">Fija</label>
                    </div>
                    @endif
                    <div class="form-group col-md-2 {{ $errors->has('id_externo') ? 'has-error' : '' }}">
                        <label for="id_externo" class="control-label">ID Externo</label>
                        <input class="form-control" required name="id_externo" type="text" id="id_externo" value="{{ old('id_externo', optional($causa)->id_externo) }}" maxlength="200" placeholder="Enter id_externo here...">
                        {!! $errors->first('id_externo', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-12 text-right">
                        @if(checkPermissions(['Causas de cierre'],['W']) && ($causa->mca_fija!='S' || ($causa->mca_fija=='S' && fullAccess())))<input class="btn btn-primary" type="submit" value="Guardar">@else <span class="bg-warning">Usted no puede modificar este dato</span>@endif
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
            icon:'{{isset($causa) ? ($causa->val_icono) : ''}}'
        });
        $('.demo-psi-cross').click(function(){
            $('.editor').hide();
        });
    </script>