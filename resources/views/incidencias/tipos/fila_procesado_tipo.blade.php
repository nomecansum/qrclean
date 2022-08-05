

    <form  name="form_proceso{{ $tipo->id_proceso }}" method="POST" class="form-ajax mb-5" id="form_proceso{{ $tipo->id_proceso }}" data-id="{{ $tipo->id_proceso }}"> 
        @csrf
        <input type="hidden" name="id" value="{{ $tipo->id_proceso }}"> 
        <input type="hidden" name="val_momento" value="{{ $momento }}"> 
        <div class="form-group col-md-2  ">
            <label for="tip_metodo" class="control-label"><span class="text-muted " style="font-size:26px; font-weight: bold">{{ $index??'-' }}</span></label>
            <select required class="form-control tip_metodo tocado" name="tip_metodo" data-id="{{ $tipo->id_proceso }}">
                    {{-- <option value="S" {{ $tipo->tip_metodo== 'S' ? 'selected' : '' }} >SMS</option> --}}
                    <option value="M" {{ $tipo->tip_metodo== 'M' ? 'selected' : '' }} >e-mail</option>
                    <option value="P" {{ $tipo->tip_metodo== 'P' ? 'selected' : '' }} >HTTP Post</option>
                    <option value="G" {{ $tipo->tip_metodo== 'G' ? 'selected' : '' }} >Http Get</option>
                    <option value="L" {{ $tipo->tip_metodo== 'L' ? 'selected' : '' }} >Gestionar en spotlinker</option>
                    <option value="N" {{ $tipo->tip_metodo== 'N' ? 'selected' : '' }} >Solo registrar</option>
            </select>
            {!! $errors->first('tip_metodo', '<p class="help-block">:message</p>') !!}
        </div>
        <div class="col-md-9">
            @switch($tipo->tip_metodo)
                @case("M")
                        <div class="form-group col-md-12 {{ $errors->has('txt_destinos') ? 'has-error' : '' }}">
                            <label for="txt_destinos" class="control-label">Destinos <span style="font-size: 9px">(separados por ; )</span></label>
                            <textarea class="form-control tocado" name="txt_destinos" type="text" id="txt_destinos" value="" maxlength="65535" placeholder="Enter Destinos here..." rows="4">{{ old('txt_destinos', optional($tipo)->txt_destinos) }}</textarea>
                            {!! $errors->first('val_txt_destinosurl', '<p class="help-block">:message</p>') !!}
                        </div>
                    @break
                @case("P")
                        <div class="row">
                            <div class="form-group col-md-12 {{ $errors->has('val_url') ? 'has-error' : '' }}">
                                <label for="des_edificio" class="control-label">URL</label>
                                <input class="form-control tocado" name="val_url" type="text" id="val_url" value="{{ old('val_url', optional($tipo)->val_url) }}" maxlength="200" placeholder="Enter URL here...">
                                {!! $errors->first('val_url', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="form-group col-md-12 {{ $errors->has('param_url') ? 'has-error' : '' }}">
                                <label for="des_edificio" class="control-label">Parametros URL</label>
                                <input class="form-control tocado" name="param_url" type="text" id="param_url" value="{{ old('param_url', optional($tipo)->param_url) }}" maxlength="1000" placeholder="Enter Param URL here...">
                                {!! $errors->first('param_url', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="form-group col-md-12 {{ $errors->has('val_body') ? 'has-error' : '' }}">
                                <label for="val_body" class="control-label">Header (json)</label>
                                <textarea class="form-control tocado" name="val_header" type="text" id="val_header" value="" maxlength="65535" placeholder="Enter URL here..." rows="8">{{ old('val_header', optional($tipo)->val_header) }}</textarea>
                                {!! $errors->first('val_header', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="form-group col-md-12 {{ $errors->has('val_body') ? 'has-error' : '' }}">
                                <label for="val_body" class="control-label">Body</label>
                                <textarea class="form-control tocado" name="val_body" type="text" id="val_body" value="" maxlength="65535" placeholder="Enter URL here..." rows="8">{{ old('val_body', optional($tipo)->val_body) }}</textarea>
                                {!! $errors->first('val_body', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div>
                    @include('incidencias.tipos.leyenda_http')
                    @break

                @case("G")
                    <div class="row">
                        <div class="form-group col-md-12 {{ $errors->has('val_url') ? 'has-error' : '' }}">
                            <label for="des_edificio" class="control-label">URL</label>
                            <input class="form-control tocado" name="val_url" type="text" id="val_url" value="{{ old('val_url', optional($tipo)->val_url) }}" maxlength="200" placeholder="Enter URL here...">
                            {!! $errors->first('val_url', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="form-group col-md-12 {{ $errors->has('param_url') ? 'has-error' : '' }}">
                            <label for="des_edificio" class="control-label">Parametros URL</label>
                            <input class="form-control tocado" name="param_url" type="text" id="param_url" value="{{ old('param_url', optional($tipo)->param_url) }}" maxlength="1000" placeholder="Enter Param URL here...">
                            {!! $errors->first('param_url', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="form-group col-md-12 {{ $errors->has('val_body') ? 'has-error' : '' }}">
                            <label for="val_body" class="control-label">Header (JSON)</label>
                            <textarea class="form-control tocado" name="val_header" type="text" id="val_header" value="" maxlength="65535" placeholder="Enter URL here..." rows="8">{{ old('val_header', optional($tipo)->val_header) }}</textarea>
                            {!! $errors->first('val_header', '<p class="help-block">:message</p>') !!}
                        </div>
                    </div>
                    @include('incidencias.tipos.leyenda_http')
                    @break

                @default
            @endswitch

        </div>
        <div class="col-md-1 nowrap mt-4">
            <a href="javascript:void(0)"  class="btn btn-xs btn-info btn_save add-tooltip"  id="btn_editar{{ $tipo->id_proceso }}" title="Editar tipo" data-id="{{ $tipo->id_proceso }}" style="display: none"> <span class="fa fa-save" aria-hidden="true" ></span> Guardar</a>
            <a href="javascript:void(0)"  class="btn btn-xs btn-danger add-tooltip btn_borrar_accion" data-id="{{$tipo->id_proceso}}" title="Borrar tipo"  ><span class="fa fa-trash" aria-hidden="true"></span> Borrar</a>
        </div>
        <div class="col-md-12">
            <div class="row bg-gray-light">
                <div class="col-md-2 mt-1">
                    Aplica a (procedencia)
                </div>
                <div class="col-md-3 mt-1">
                    <input type="checkbox" class="form-control  magic-checkbox tocado" name="mca_web"  id="mca_web{{$tipo->id_proceso}}" value="S" {{ isset($tipo->mca_web)&&$tipo->mca_web=='S'?'checked':'' }}> 
                    <label class="custom-control-label"   for="mca_web{{$tipo->id_proceso}}">WEB</label>
                </div>
                <div class="col-md-3 mt-1">
                    <input type="checkbox" class="form-control  magic-checkbox tocado" name="mca_api"  id="mca_api{{$tipo->id_proceso}}" value="S" {{ isset($tipo->mca_api)&&$tipo->mca_api=='S'?'checked':'' }}> 
                    <label class="custom-control-label"   for="mca_api{{$tipo->id_proceso}}">API</label>
                </div>
                <div class="col-md-3 mt-1">
                    <input type="checkbox" class="form-control  magic-checkbox tocado" name="mca_salas"  id="mca_salas{{$tipo->id_proceso}}" value="S" {{ isset($tipo->mca_salas)&&$tipo->mca_salas=='S'?'checked':'' }}> 
                    <label class="custom-control-label"   for="mca_salas{{$tipo->id_proceso}}">SALAS</label>
                </div>
            </div>
        </div>
    </form>

<script>

    $('.tocado').on('change keyup paste', function() {
        $('#btn_editar'+$(this).closest("form").data('id')).show();
    });

    $('.tip_metodo').change(function(){
        id_accion=$(this).data('id');
        $('#fila'+$(this).data('id')).load("{{ url('/incidencias/tipos/fila_postprocesado') }}/"+$(this).data('id')+"/"+$(this).val()+"/{{ $momento }}",function(){
           $('#btn_editar'+id_accion).show();
            
        }); 
    });
    $('#btn_editar{{ $tipo->id_proceso }}').click(function(){
        $.post("{{ url('/incidencias/tipos/postprocesado/save') }}", $('#form_proceso'+$(this).data('id')).serializeArray(), function(data, textStatus, xhr) {
			
		}).fail((e)=>{
			toast_error(JSON.parse(e.responseText))
		})
        .done((data)=>{
			toast_ok(data.mensaje);
            $('#btn_editar'+data.id).hide();
		});
    });
</script>