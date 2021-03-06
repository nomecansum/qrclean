<input type="hidden" name="id_incidencia" value="{{ $id }}"></input>
<input type="hidden" name="adjuntos[]" id="adjuntos" value="">
<div class="row">
    <div class="form-group col-md-12">
        <label for="val_color">Accion</label><br>
        <textarea class="form-control w-100" required name="des_accion" type="text" id="des_accion" value="" rows="5" ></textarea>
    </div>
</div>

<div id="dZUpload" class="dropzone">
    <div class="dz-default dz-message">
        <h2><i class="mdi mdi-cloud-upload"></i> Arrastre archivos <span class="text-blue">para subirlos</span></h2>&nbsp&nbsp<h6 class="display-inline text-muted"> (o Click aqui)</h6>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12 {{ $errors->has('id_estado_inicial') ? 'has-error' : '' }}">
        <label for="id_estado" class="control-label">Poner la incidencia en estado</label>
        <select class="form-control" required id="id_estado" name="id_estado">
            @foreach ($estados as $estado)
                <option value="{{ $estado->id_estado }}">
                    {{ $estado->des_estado }}
                </option>
            @endforeach
        </select>
            
        {!! $errors->first('id_estado_inicial', '<p class="help-block">:message</p>') !!}
    </div>
</div>
<script>
        $('input[type="file"]').change(function(e){
			var fileName = e.target.files[0].name;
			$(this).next('label').html(fileName);
			//$('.custom-file-label').html(fileName);
		});
</script>