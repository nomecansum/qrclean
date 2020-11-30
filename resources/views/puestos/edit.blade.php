<div class="panel" id="editor">
    <div class="panel">
        <div class="panel-heading">
            <div class="panel-control">
                <button class="btn btn-default" data-panel="dismiss"><i class="demo-psi-cross"></i></button>
            </div>
            <h3 class="panel-title">Modificar puesto {{ $puesto->id_puesto }}</h3>
        </div>
        <div class="panel-body">
            <form  action="{{url('puestos/update')}}" method="POST" name="frm_contador" id="frm_contador" class="form-ajax"  enctype="multipart/form-data">
                
                <div class="row">
                    <input type="hidden" name="id_puesto" value="{{ $puesto->id_puesto }}">
                    <input type="hidden" name="tags" value="" id="tags">
                    <input type="hidden" name="id_cliente" value="{{ Auth::user()->id_cliente }}">
                    <input type="hidden" name="token" value="{{ $puesto->id_puesto!=0?$puesto->token:Illuminate\Support\Str::random(50) }}">

                    {{csrf_field()}}
                    <div class="form-group col-md-2">
                        <label for="cod_puesto">Identificador</label>
                        <input type="text" name="cod_puesto" id="cod_puesto" class="form-control" value="{{$puesto->cod_puesto}}">
                    </div>
                    <div class="form-group col-md-3">
                        <label for="des_puesto">Nombre descriptivo</label>
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
                        <input type="text" autocomplete="off" name="val_color" id="val_color"  class="minicolors form-control" value="{{isset($puesto->val_color)?$puesto->val_color:App\Classes\RandomColor::one(['luminosity' => 'bright'])}}" />
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label>Icono</label><br>
                            <button type="button"  role="iconpicker" required name="val_icono"  id="val_icono" data-iconset="fontawesome5" class="btn btn-light iconpicker" data-search="true" data-rows="10" data-cols="30" data-search-text="Buscar..."></button>
                        </div>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="max_horas_reservar">Max reserva(horas)</label>
                        <input type="number" min="1" max="999999" name="max_horas_reservar" id="max_horas_reservar" class="form-control" value="{{$puesto->max_horas_reservar}}">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-2">
                        <label for="id_edificio">Edificio</label>
                        <select name="id_edificio" id="id_edificio" class="form-control" data-planta="{{ $puesto->id_planta }}">
                            @foreach(DB::table('edificios')->where('id_cliente',Auth::user()->id_cliente)->get() as $edificio)
                                <option value="{{ $edificio->id_edificio}}" {{ $puesto->id_edificio==$edificio->id_edificio?'selected':'' }}>{{ $edificio->des_edificio }}</option>
                            @endforeach
                        </select>
                    </div>
                    @php
                    @endphp
                    <div class="form-group col-md-3">
                        <label for="planta">Planta</label>
                        <select name="id_planta" id="id_planta" class="form-control">
                            @foreach(DB::table('plantas')->where('id_cliente',Auth::user()->id_cliente)->where('id_edificio',$puesto->id_edificio)->get() as $planta)
                                <option value="{{ $planta->id_planta}}" {{ $puesto->id_planta==$planta->id_planta?'selected':'' }}>{{ $planta->des_planta }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 p-t-30">
                        <input type="checkbox" class="form-control  magic-checkbox" name="mca_acceso_anonimo"  id="mca_acceso_anonimo" value="S" {{ $puesto->mca_acceso_anonimo=='S'?'checked':'' }}> 
                        <label class="custom-control-label"   for="mca_acceso_anonimo">Permitir anonimo</label>
                    </div>
                    <div class="col-md-2  p-t-30">
                        <input type="checkbox" class="form-control  magic-checkbox" name="mca_reservar"  id="mca_reservar" value="S" {{ $puesto->mca_reservar=='S'?'checked':'' }}> 
                        <label class="custom-control-label"   for="mca_reservar">Permitir reserva</label>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="id_usuario">Tipo de puesto</label>
                        <select name="id_tipo_puesto" id="id_tipo_puesto" class="form-control">
                            @foreach($tipos as $t)
                                <option value="{{ $t->id_tipo_puesto}}" {{ isset($puesto->id_tipo_puesto) && $puesto->id_tipo_puesto==$t->id_tipo_puesto?'selected':'' }}>{{ $t->des_tipo_puesto }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-12">
                        <label for="planta">Tags</label>
                        <input type="text" class="edit_tag" data-role="tagsinput" placeholder="Type to add a tag" size="17" value="{{ $tags }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="id_usuario">Asignado permanentemente a usuario</label>
                        <select name="id_usuario" id="id_usuario" class="form-control select2">
                            <option value="0"></option>
                            @foreach($usuarios as $u)
                                <option value="{{ $u->id}}" {{ isset($puesto->id_usuario) && $puesto->id_usuario==$u->id?'selected':'' }}>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="id_perfil">Asignado permanentemente a perfil</label>
                        <select name="id_perfil" id="id_perfil" class="form-control select2">
                            <option value="0"></option>
                            @foreach($perfiles as $n)
                                <option value="{{ $n->cod_nivel}}" {{ isset($puesto->id_perfil) && $puesto->id_perfil==$n->cod_nivel?'selected':'' }}>{{ $n->des_nivel_acceso }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                </div>
                <div class="row mt-2 ">
                    <div class="col-md-12   text-right ">
                        @if(checkPermissions(['Puestos'],["W"]))<button type="submit" class="btn btn-primary mr-2 btn_submit">GUARDAR</button>@endif  
                    </div>
                </div>
                <div class="row mb-0">
                    <div class="col-md-6 mb-0 p-0" style="padding-left: 18px">
                        <label>Imagen</label>
                    </div>
                    <div class="col-md-6 p-0 pl-2">
                        <label>Posicion en el plano</label>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6 text-center b-all" style="padding-left: 18px">
                        <img src="{{ isset($puesto) ? Storage::disk(config('app.img_disk'))->url('img/puestos/'.$puesto->img_puesto) : ''}}" style="width: 90%;  margin-top: 50px" alt="" class="img-fluid ml-0">
                        <div class="form-group">
                            <div class="custom-file">
                                <input type="file" accept=".jpg,.png,.gif,.svg" class="form-control  custom-file-input" name="img_puesto" id="img_puesto" lang="es">
                                <label class="custom-file-label" for="img_puesto"></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-6 text-center b-all" style="padding-left: 18px">
                        @include('puestos.posicion_plano')
                    </div>
                </div>
                <div class="row mt-2 ">
                    <div class="col-md-12   text-right ">
                        @if(checkPermissions(['Puestos'],["W"]))<button type="submit" class="btn btn-primary mr-2 btn_submit">GUARDAR</button>@endif  
                    </div>
                </div>
            </form>
        </div>
    </div>
 </div>

 <script>
     $('.btn_submit').click(function(){
        //$('#frm_contador').submit();
     })
     $('#frm_contador').submit(form_ajax_submit);

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
    

    $('.demo-psi-cross').click(function(){
        $('#editor').hide();
    })

    $('#val_icono').iconpicker({
        icon:'{{isset($t) ? ($t->val_icono) : ''}}'
    });

    $('#id_edificio').change(function(){
        $('#id_planta').load("{{ url('/combos/plantas/') }}/"+$(this).val(), function(){
           
        });
    })

    $('.edit_tag').on("keypress", function(e) {
            /* ENTER PRESSED*/
            if (e.keyCode == 13) {
                /* FOCUS ELEMENT */
                e.preventDefault();
            }
            
        });
    
    $('.edit_tag').tagsinput();

    $('.edit_tag').on('itemAdded', function(event) {
        $('#tags').val($(".edit_tag").tagsinput('items'));
    });

    $(function(){
        $('#id_planta').load("{{ url('/combos/plantas/') }}/"+$('#id_edificio').val(), function(){
            $('#id_planta').val({{ $puesto->id_planta }});
            $('#id_planta option[value={{ $puesto->id_planta }}]').attr('selected','selected');
        });
        
    })

    $(".select2").select2({
        placeholder: "Seleccione",
        allowClear: true,
        width: "99.2%",
    });

    $('#id_perfil').on('select2:select', function (e) {
        $('#id_usuario').val(null).trigger('change');
    });
    $('#id_usuario').on('select2:select', function (e) {
        $('#id_perfil').val(null).trigger('change');
    });

    $('input[type="file"]').change(function(e){
			var fileName = e.target.files[0].name;
			$(this).next('label').html(fileName);
			//$('.custom-file-label').html(fileName);
		});

 </script>
