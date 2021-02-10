<input type="hidden" name="id_incidencia" value="{{ $id }}"></input>
<div class="row">
    <div class="form-group col-md-12">
        <label for="val_color">Accion</label><br>
        <textarea class="form-control w-100" required name="des_accion" type="text" id="des_accion" value="" rows="5" ></textarea>
    </div>
</div>

<div class="row">
    <div class="row" style="padding-left: 15px">
        Imagen 1<br>
    </div>
    <div class="col-md-12">
        <div class="form-group  {{ $errors->has('img_usuario') ? 'has-error' : '' }}">
            <div class="custom-file">
                <input type="file"  accept=".jpg,.png,.gif,.mp4,.avi,.mpg" class="form-control  custom-file-input" name="img_attach1" id="img_attach1" lang="es">
                <label class="custom-file-label" for="img_attach1"></label>
            </div>
        </div>
            {!! $errors->first('img_attach1', '<p class="help-block">:message</p>') !!}
    </div>
</div>
<div class="row">
    <div class="row" style="padding-left: 15px">
        Imagen 2<br>
    </div>
    <div class="col-md-12">
        <div class="form-group  {{ $errors->has('img_usuario') ? 'has-error' : '' }}">
            <div class="custom-file">
                <input type="file" accept=".jpg,.png,.gif,.mp4,.avi,.mpg" class="form-control  custom-file-input" name="img_attach2" id="img_attach2" lang="es">
                <label class="custom-file-label" for="img_attach2"></label>
            </div>
        </div>
            {!! $errors->first('img_attach2', '<p class="help-block">:message</p>') !!}
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