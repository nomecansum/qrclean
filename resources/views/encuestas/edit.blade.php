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

        @php
         //dd($encuesta);   
        @endphp
        
        @if($encuesta->id_encuesta==0)
            <form method="POST" action="{{ url('/encuestas/save') }}" id="edit_encuesta_form" name="edit_encuesta_form" accept-charset="UTF-8" class="form-horizontal form-ajax" >
        @else
            <form method="POST" action="{{ url('/encuestas/update') }}" id="edit_encuesta_form" name="edit_encuesta_form" accept-charset="UTF-8" class="form-horizontal form-ajax">
            <input name="id_encuesta" type="hidden" value="{{ $encuesta->id_encuesta }}">
        @endif
            {{ csrf_field() }}
            <div class="row">
                <div class="form-group col-md-12 {{ $errors->has('titulo') ? 'has-error' : '' }}">
                    <label for="des_planta" class="control-label">Titulo</label>
                        <input class="form-control" required name="titulo" type="text" id="titulo" value="{{ $encuesta->titulo }}" maxlength="50" placeholder="Enter titulo here...">
                        {!! $errors->first('titulo', '<p class="help-block">:message</p>') !!}
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-12 {{ $errors->has('pregunta') ? 'has-error' : '' }}">
                    <label for="pregunta" class="control-label">Pregunta</label>
                    <textarea  class="textarea_editor form-control" required name="pregunta" id="pregunta" rows="8" placeholder="Enter text ...">{!! $encuesta->pregunta !!}</textarea>
                        {!! $errors->first('pregunta', '<p class="help-block">:message</p>') !!}
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-3">
                    <label for="id_cliente" class="control-label">Cliente</label>
                    <select class="form-control" required id="id_cliente" name="id_cliente">
                        @foreach ($clientes as $key => $cliente)
                            <option value="{{ $key }}" {{ $encuesta->id_cliente == $key ? 'selected' : '' }}>
                                {{ $cliente }}
                            </option>
                        @endforeach
                    </select>
                    {!! $errors->first('id_cliente', '<p class="help-block">:message</p>') !!}
                </div>
                <div class="form-group col-md-4">
                    <label for="id_cliente" class="control-label">Tipo</label>
                    <input type="hidden" name="id_tipo_encuesta" id="id_tipo_encuesta" value="{{$encuesta->id_tipo_encuesta}}"></inpout>
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
                        <input type="text" class="form-control pull-left rangepicker" id="fechas" name="fechas" style="height: 33px; width: 300px" value="{{ Carbon\Carbon::now()->format('d/m/Y').' - '.Carbon\Carbon::now()->format('d/m/Y') }}">
                        <span class="btn input-group-text btn-mint" disabled  style="height: 33px"><i class="fas fa-calendar mt-1"></i></span>
                    </div>
                </div>
                <div class="form-group col-md-3">
                    <label for="val_momento" class="control-label">Momento en el que mostrarla</label>
                    <select class="form-control" required id="val_momento" name="val_momento">
                            <option value="0" {{ $encuesta->val_momento == 0 ? 'selected' : '' }}>Independiente</option>
                            <option value="0" {{ $encuesta->val_momento == 'A' ? 'selected' : '' }}>Al escanear sitio</option>
                            <option value="0" {{ $encuesta->val_momento == 'D' ? 'selected' : '' }}>Al dejar sitio</option>
                    </select>
                </div>
                <div class="form-group col-md-6" >
                   
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-12">
                    <label class="control-label">Token</label>
                    <div class="input-group mb-3">
                        <input type="text" name="token_acceso" readonly=""  id="token_1uso"  class="form-control" value="{{$encuesta->token }}">
                        <div class="input-group-btn">
                            <button class="btn btn-mint" type="button"  id="btn_generar_token">Generar</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row b-all" style="width: 98%">
                <div class="form-group col-md-11">
                    <label>URL de acceso</label><br>
                    <a href="{{ url('encuestas/get',$encuesta->token) }}" id="link_url" target="_blank"><h5 id="span_url">{{ url('encuestas/get',$encuesta->token) }}</h5></a>
                </div>
                <div class="form-group col-md-1 text-right mt-3">
                    <a href="#modal_img"  class="btn  btn-warning add-tooltip  btn_url" id="btn_gen_qr" data-toggle="modal" title="Generar QR" data-id="{{ $encuesta->id_encuesta }}" data-url="{{ url('encuestas/get',$encuesta->token) }}> <span class="fad fa-qrcode pt-1" aria-hidden="true"></span> Ver QR</a>
                    <a href="#"  class="btn  btn-info  add-tooltip btn_url" id="boton_url" title="Copiar URL" data-id="{{ $encuesta->id_encuesta }}" data-clipboard-text="{{ url('encuestas/get',$encuesta->token) }}"> <span class="fa fa-copy pt-1" aria-hidden="true"></span> Copiar</a>
                    <a href="{{ url('encuestas/get',$encuesta->token) }}" target="_blank"  class="btn  btn-success  add-tooltip btn_url" id="boton_abrir" title="Abrir URL" data-id="{{ $encuesta->id_encuesta }}" data-urk="{{ url('encuestas/get',$encuesta->token) }}"> <i class="fad fa-external-link-square-alt"></i> Abrir</a>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-12">
                    <label>Aplicar solo a perfiles</label><br>
                    <select name="list_perfiles[]" id="list_perfiles" class="select2 select2-filtro mb-2 select2-multiple form-control" multiple="multiple">
                        <option value=""></option>
                        @foreach ($perfiles as $p)
                            <option {{ in_array($p->cod_nivel,explode(",",$encuesta->list_perfiles)) ? 'selected' : ''}} value="{{$p->cod_nivel}}">{{$p->des_nivel_acceso}}</option>
                        @endforeach
                    </select>
                </div>
                
            </div>
            <div class="row">
                @include('resources.combos_filtro',[$hide=['cli'=>1,'est'=>1,'head'=>1,'btn'=>1]])
            </div>
           
            <div class="row">
                <div class="form-group col-md-12 text-right mt-3">
                    <input class="btn btn-primary" type="submit" value="Guardar">
                </div>
            </div>
            
        </form>

    </div>
</div>
<div class="modal fade" id="modal_img">
    <div class="modal-dialog modal-md">
        <div class="modal-content"><div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
            <div class="modal-body">
                <img style="width:100%" id="img_accion">
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-warning">Cerrar</button>
            </div>
        </div>
    </div>
</form>
</div>

<script src="{{ url('/plugins/clipboard-js/clipboard.js') }}"></script>
<script src="{{ url('/plugins/html5-editor/wysihtml5-0.3.0.js') }}"></script>
<script src="{{ url('/plugins/html5-editor/bootstrap-wysihtml5.js') }}"></script>
<script>

    $(".select2").select2({
        placeholder: "Seleccione",
        allowClear: true,
        width: "99.2%",
    });

    var clipboard = new ClipboardJS('#boton_url');

    $('#id_cliente').change(function(){
        $("#multi-cliente").val($("#id_cliente").val());
        $("#multi-cliente").change();
    })

    $('#id_cliente').change();


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

    $('#btn_generar_token').click(function(event){
        //console.log('token');
        $.get( "/clientes/gen_key")
        .done(function( data, textStatus, jqXHR ) {
            $('#token_1uso').val(data);
            $('.btn_url').data('clipboard-text',"{{ url('encuestas/get') }}/"+data);
            $('.btn_url').data('url',"{{ url('encuestas/get') }}/"+data);
            $('#link_url').attr('href',"{{ url('encuestas/get') }}/"+data);
            $('#span_url').html("{{ url('encuestas/get') }}/"+data);
            $('#boton_abrir').attr('href',"{{ url('encuestas/get') }}/"+data);
        })
        .fail(function( jqXHR, textStatus, errorThrown ) {
                console.log(errorThrown);
        });	
    })

    $('#btn_gen_qr').click(function(){
        console.log($(this).data('clipboard-text'));
        $.post('{{url('/gen_qr')}}', {_token:'{{csrf_token()}}',url: $(this).data('url')}, function(data, textStatus, xhr) {
            $('#img_accion').attr('src','data:image/png;base64, '+data);
		});
    })

</script>
@include('layouts.scripts_panel')

@yield('scripts2')