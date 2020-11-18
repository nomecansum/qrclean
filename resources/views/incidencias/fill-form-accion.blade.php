<input type="hidden" name="id_incidencia" value="{{ $id }}"></input>
<h5>Añadir nueva accion</h5>
<textarea class="form-control" required name="des_accion" type="text" id="des_accion" value="" rows="5"></textarea>
<div class="row">
    <div class="row" style="padding-left: 15px">
        Imagen 1<br>
    </div>
    <div class="col-md-12">
        <div class="form-group  {{ $errors->has('img_usuario') ? 'has-error' : '' }}">
            <div class="custom-file">
                <input type="file" accept=".jpg,.png,.gif" class="form-control  custom-file-input" name="img_attach1" id="img_attach1" lang="es">
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

<script>
        $('input[type="file"]').change(function(e){
			var fileName = e.target.files[0].name;
			$(this).next('label').html(fileName);
			//$('.custom-file-label').html(fileName);
		});
</script>