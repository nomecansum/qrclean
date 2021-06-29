<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title" id="titulo">
            Crear incidencia para el puesto 
            @isset($puesto->val_icono)
                <i class="{{ $puesto->val_icono }} fa-2x" style="color:{{ $puesto->val_color }}"></i>
            @endisset
           <span class="font-bold" style="color:{{ $puesto->val_color }}; font-size: 20px">{{ $puesto->des_puesto }}</span>

        </h3>
    </div>
    <div class="panel-body">
        <form method="POST" action="{{ url('/incidencias/save') }}" id="incidencia_form" name="incidencia_form" accept-charset="UTF-8" class="form-horizontal form-ajax">
        {{ csrf_field() }}
            <div class="row">
                <input type="hidden" name="id_puesto" value="{{ $puesto->id_puesto }}">
                <input type="hidden" name="referer" value="{{ $referer }}">
                <input type="hidden" name="adjuntos[]" id="adjuntos" value="">
                @if(isset($config->val_layout_incidencias) && ($config->val_layout_incidencias=='T' || $config->val_layout_incidencias=='A'))
                    <div class="form-group col-md-8 {{ $errors->has('des_incidencia') ? 'has-error' : '' }}">
                        <label for="des_incidencia" class="control-label">Titulo</label>
                        <input class="form-control"  name="des_incidencia" type="text" id="des_incidencia"  maxlength="200" >
                        {!! $errors->first('des_incidencia', '<p class="help-block">:message</p>') !!}
                    </div>
                @endif
                <div class="form-group col-md-4 {{ $errors->has('id_tipo_incidencia') ? 'has-error' : '' }}">
                    <label for="id_tipo_incidencia" class="control-label">Tipo</label>
                    <select class="form-control selectpicker" required id="id_tipo_incidencia" name="id_tipo_incidencia">
                        @foreach ($tipos as $tipo)
                            <option value="{{ $tipo->id_tipo_incidencia }}" data-content="<i class='fa {{ $tipo->val_icono }}' aria-hidden='true' style='color: {{ $tipo->val_color }}'></i> {{ $tipo->des_tipo_incidencia }}"></option>
                        @endforeach
                    </select>
                </div>   
            </div>
            @if((isset($config->val_layout_incidencias) && ($config->val_layout_incidencias=='D' || $config->val_layout_incidencias=='A')) || (!isset($config->val_layout_incidencias)))
            <div class="row">
                <div class="form-group col-md-12 {{ $errors->has('txt_incidencia') ? 'has-error' : '' }}">
                    <label for="txt_incidencia" class="control-label">Descripcion</label>
                    <textarea class="form-control" name="txt_incidencia" type="text" id="txt_incidencia" value="" rows="4"></textarea>
                    {!! $errors->first('txt_incidencia', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            @endif
            <div id="dZUpload" class="dropzone">
                <div class="dz-default dz-message">
                    <h2><i class="mdi mdi-cloud-upload"></i> Arrastre archivos <span class="text-blue">para subirlos</span></h2>&nbsp&nbsp<h6 class="display-inline text-muted"> (o Click aqui)</h6>
                </div>
            </div>

            <div class="form-group mt-3">
                <div class="col-md-12 text-center">
                    <input class="btn btn-lg btn-primary" type="submit" value="Guardar">
                </div>
            </div>
        </form>

    </div>
</div>
<script>
    function iformat(icon) {
        var originalOption = icon.element;
        return $('<span><i class="mdi ' + $(originalOption).data('icon') + '"></i> ' + icon.text + '</span>');
    }

</script>
