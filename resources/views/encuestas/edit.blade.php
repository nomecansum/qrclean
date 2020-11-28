<link rel="stylesheet" href="{{ url('/plugins/html5-editor/bootstrap-wysihtml5.css') }}" />
<div class="panel">
    <div class="panel-heading">
        <div class="panel-control">
            <button class="btn btn-default" data-panel="dismiss" data-dismiss="panel"><i class="demo-psi-cross"></i></button>
        </div>
        <h3 class="panel-title">Editar encuesta</h3>
    </div>

    <div class="panel-body">

        @if ($errors->any())
            <ul class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <form method="POST" action="{{ url('/encuestas/update') }}" id="edit_encuesta_form" name="edit_encuesta_form" accept-charset="UTF-8" class="form-horizontal form-ajax"  enctype="multipart/form-data">
        {{ csrf_field() }}
        <input name="_method" type="hidden" value="PUT">
        
            <div class="row">
                <div class="form-group col-md-12 {{ $errors->has('titulo') ? 'has-error' : '' }}">
                    <label for="des_planta" class="control-label">Titulo</label>
                        <input class="form-control" required name="titulo" type="text" id="titulo" value="{{ old('titulo', optional($encuesta)->des_planta) }}" maxlength="50" placeholder="Enter titulo here...">
                        {!! $errors->first('titulo', '<p class="help-block">:message</p>') !!}
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-3">
                    <label for="id_cliente" class="control-label">Cliente</label>
                    <select class="form-control" required id="id_cliente" name="id_cliente">
                        @foreach ($clientes as $key => $cliente)
                            <option value="{{ $key }}" {{ old('id_cliente', optional($encuesta)->id_cliente) == $key ? 'selected' : '' }}>
                                {{ $cliente }}
                            </option>
                        @endforeach
                    </select>
                    {!! $errors->first('id_cliente', '<p class="help-block">:message</p>') !!}
                </div>

                <div class="form-group col-md-4">
                    <label for="id_cliente" class="control-label">Tipo</label>
                    <input type="hidden" name="id_tipo_encuesta" id="id_tipo_encuesta" value="{{ old('id_tipo_encuesta', optional($encuesta)->id_tipo_encuesta) == $key ? 'selected' : '' }}"></inpout>
                    <div class="dropdown">
                        <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false" title="Tipo de encuesta">
                            <img src="{{ url('/img',$encuesta->img_tipo) }}" id="img_tipo">  <span id="des_tipo" class="ml-3">{{ $encuesta->des_tipo_encuesta }}</span> <i class="dropdown-caret"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="" id="dropdown-acciones">
                            @foreach($tipos as $tipo)
                                <li><a href="#" data-tipo="{{ $tipo->id_tipo_encuesta }}" data-imagen="{{ $tipo->img_tipo }}" data-desc="{{ $tipo->des_tipo_encuesta }}" class="btn_tipo_check"> <img src="{{ url('/img',$tipo->img_tipo) }}"><div>{{ $tipo->des_tipo_encuesta }}</div> </a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="form-group col-md-2" style="margin-top: 7px">
                    <label for="val_color">Color</label><br>
                    <input type="text" autocomplete="off" name="val_color" id="val_color"  class="minicolors form-control" value="{{isset($encuesta->val_color)?$encuesta->val_color:App\Classes\RandomColor::one(['luminosity' => 'bright'])}}" />
                </div>
                <div class="form-group col-md-1 mt-2" style="margin-left: 10px">
                    <div class="form-group">
                        <label>Icono</label><br>
                        <button type="button"  role="iconpicker" name="val_icono"  id="val_icono" data-iconset="fontawesome5" class="btn btn-light iconpicker" data-search="true" data-rows="10" data-cols="30" data-search-text="Buscar..."></button>
                    </div>
                </div>
                <div class="col-md-1 p-t-30 mt-1">
                    <input type="checkbox" class="form-control  magic-checkbox" name="mca_fija"  id="mca_activa" value="S" {{ $encuesta->mca_activa=='S'?'checked':'' }}> 
                    <label class="custom-control-label"   for="mca_activa">Activa</label>
                </div>
                <div class="col-md-1 p-t-30 mt-1">    
                    <input type="checkbox" class="form-control  magic-checkbox" name="mca_anonima"  id="mca_anonima" value="S" {{ $encuesta->mca_anonima=='S'?'checked':'' }}> 
                    <label class="custom-control-label"   for="mca_anonima">Anonima</label>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label>Fechas </label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control pull-left rangepicker" id="fechas" name="fechas" style="height: 40px; width: 400px" value="{{ Carbon\Carbon::now()->format('d/m/Y').' - '.Carbon\Carbon::now()->format('d/m/Y') }}">
                        <span class="btn input-group-text btn-mint" disabled  style="height: 40px"><i class="fas fa-calendar mt-1"></i></span>
                    </div>
                </div>
                <div class="form-group col-md-6" >
                    <label>Aplicar a puesto </label>
                    
                </div>
            </div>

            <div class="row">

                    
                    {{--  <div class="form-group col-md-1">
                        <label for="des_planta" class="control-label">Orden</label>
                        <select class="form-control" required id="num_orden" name="num_orden">
                            @for ($n=1; $n<26;$n++ )
                                <option value="{{ $n }}" {{ old('num_orden', optional($encuesta)->num_orden) == $n ? 'selected' : '' }}>
                                    {{ $n }}
                                </option>
                            @endfor
                        </select>
                    </div>  --}}
                    {{--  <div class="row">
                        <div class="col-md-12">
                            <label for="id_edificio" class="control-label">Edificio</label>
                            <select class="form-control" required id="id_edificio" name="id_edificio">
                                    <option value="" style="display: none;" {{ old('id_edificio', optional($encuesta)->id_edificio ?: '') == '' ? 'selected' : '' }} disabled selected>Enter id edificio here...</option>
                                @foreach ($Edificios as $key => $Edificio)
                                    <option value="{{ $key }}" {{ old('id_edificio', optional($encuesta)->id_edificio) == $key ? 'selected' : '' }}>
                                        {{ $Edificio }}
                                    </option>
                                @endforeach
                            </select>
                            {!! $errors->first('id_edificio', '<p class="help-block">:message</p>') !!}
                        </div>
                    </div>  --}}

            
            </div>

            <div class="form-group">
                <div class="col-md-offset-2 col-md-10">
                    <input class="btn btn-primary" type="submit" value="Guardar">
                </div>
            </div>
        </form>

    </div>
</div>


<script src="{{ url('/plugins/html5-editor/wysihtml5-0.3.0.js') }}"></script>
<script src="{{ url('/plugins/html5-editor/bootstrap-wysihtml5.js') }}"></script>
<script>
    $('.form-ajax').submit(form_ajax_submit);
    $('input[type="file"]').change(function(e){
        var fileName = e.target.files[0].name;
        $('.custom-file-label').html(fileName);
    });
    $('.btn_tipo_check').click(function(){
        $('#id_tipo_encuesta').val($(this).data('tipo'));
        $('#img_tipo').attr('src',"{{ url('/img') }}/"+$(this).data('imagen'));
        $('#des_tipo').html($(this).data('desc'));
    })

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
        icon:'{{isset($encuesta) ? ($encuesta->val_icono) : ''}}'
    });
    $('.textarea_editor').wysihtml5();

    $('#fechas').daterangepicker({
            autoUpdateInput: true,
            locale: {
                format: '{{trans("general.date_format")}}',
                applyLabel: "OK",
                cancelLabel: "Cancelar",
                daysOfWeek:["{{trans('general.domingo2')}}","{{trans('general.lunes2')}}","{{trans('general.martes2')}}","{{trans('general.miercoles2')}}","{{trans('general.jueves2')}}","{{trans('general.viernes2')}}","{{trans('general.sabado2')}}"],
                monthNames: ["{{trans('general.enero')}}","{{trans('general.febrero')}}","{{trans('general.marzo')}}","{{trans('general.abril')}}","{{trans('general.mayo')}}","{{trans('general.junio')}}","{{trans('general.julio')}}","{{trans('general.agosto')}}","{{trans('general.septiembre')}}","{{trans('general.octubre')}}","{{trans('general.noviembre')}}","{{trans('general.diciembre')}}"],
                firstDay: {{trans("general.firstDayofWeek")}}
            },
            opens: 'right',
            parentEl: "#asignar-puesto .modal-body" 
        });

</script>
@include('layouts.scripts_panel')