<div class="panel" id="editor">
    <div class="panel">
        <div class="panel-heading">
            <div class="panel-control">
                <button class="btn btn-default" data-panel="dismiss"><i class="demo-psi-cross"></i></button>
            </div>
            <h3 class="panel-title">Modificar puesto</h3>
        </div>
        <div class="panel-body">
            <form  action="{{url('puestos/update')}}" method="POST" name="frm_contador" id="frm_contador" class="form-ajax">
                <div class="row">
                    <input type="hidden" name="id_puesto" value="{{ $puesto->id_puesto }}">
                    <input type="hidden" name="id_cliente" value="{{ Auth::user()->id_cliente }}">
                    <input type="hidden" name="token" value="{{ $puesto->id_puesto!=0?$puesto->token:Illuminate\Support\Str::random(50) }}">

                    {{csrf_field()}}
                    <div class="form-group col-md-2">
                        <label for="cod_puesto">ID</label>
                        <input required type="text" name="cod_puesto" id="cod_puesto" class="form-control" required value="{{$puesto->cod_puesto}}">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="des_puesto">Nombre</label>
                        <input required type="text" name="des_puesto" id="des_puesto" class="form-control" required value="{{$puesto->des_puesto}}">
                    </div>

                    <div class="form-group col-md-2">
                        <label for="id_estado">Estado</label>
                        <select name="id_estado" id="id_estado"  class="form-control">
                            @foreach(DB::table('estados_puestos')->get() as $estado)
                            <option value="{{ $estado->id_estado}}">{{ $estado->des_estado }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="val_color">Color</label><br>
                        <input type="text" autocomplete="off" name="val_color" id="val_color"  class="minicolors form-control" value="{{isset($puesto->val_color)?$puesto->val_Color:App\Classes\RandomColor::one(['luminosity' => 'bright'])}}" />
                    </div>
                    <div class="col-sm-1">
                        <div class="form-group">
                            <label>Icono</label><br>
                            <button type="button"  role="iconpicker" required name="val_icono"  id="val_icono" data-iconset="fontawesome5" class="btn btn-light iconpicker" data-search="true" data-rows="10" data-cols="30" data-search-text="Buscar..."></button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-2">
                        <label for="id_edificio">Edificio</label>
                        <select name="id_edificio" id="id_edificio" class="form-control">
                            @foreach(DB::table('edificios')->where('id_cliente',Auth::user()->id_cliente)->get() as $edificio)
                                <option value="{{ $edificio->id_edificio}}" {{ $puesto->id_edificio==$edificio->id_edificio?'selected':'' }}>{{ $edificio->des_edificio }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="planta">Planta</label>
                        <select name="id_planta" id="id_planta" class="form-control">
                            @foreach(DB::table('plantas')->where('id_cliente',Auth::user()->id_cliente)->where('id_edificio',$puesto->id_edificio)->get() as $planta)
                                <option value="{{ $planta->id_planta}}" {{ $puesto->id_planta==$planta->id_planta?'selected':'' }}>{{ $planta->des_planta }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 p-t-30">
                        <input type="checkbox" class="form-control  magic-checkbox" name="mca_acceso_anonimo"  id="mca_acceso_anonimo" value="S" {{ $puesto->mca_acceso_anonimo=='S'?'checked':'' }}> 
                        <label class="custom-control-label"   for="mca_acceso_anonimo">Permitir acceso anonimo</label>
                    </div>
                    <div class="col-md-2  p-t-30">
                        <input type="checkbox" class="form-control  magic-checkbox" name="mca_reservar"  id="mca_reservar" value="S" {{ $puesto->mca_reservar=='S'?'checked':'' }}> 
                        <label class="custom-control-label"   for="mca_reservar">Permitir reserva</label>
                    </div>
                    <div class="row mt-2">
						<div class="col-md-offset-11 col-md-12">
							@if(checkPermissions(['Puestos'],["W"]))<button type="submit" class="btn btn-primary">GUARDAR</button>@endif
						</div>
					</div>
                </div>
            </form>
        </div>
    </div>
 </div>

 <script>
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

    $('.demo-psi-cross').click(function(){
        $('#editor').hide();
    })

    $('#val_icono').iconpicker({
        icon:'{{isset($t) ? ($t->val_icono) : ''}}'
    });

    $('#id_edificio').change(function(){
        $('#id_planta').load("{{ url('/combos/plantas/') }}/"+$(this).val())
    })

 </script>
