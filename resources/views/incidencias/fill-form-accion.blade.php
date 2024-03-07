@php
$entidad=$incidencia->id_puesto==0?'solicitud':'incidencia';
@endphp

<input type="hidden" name="id_incidencia" value="{{ $id }}"></input>
<input type="hidden" name="adjuntos[]" id="adjuntos" value="">
<input type="hidden" name="procedencia" value="web"></input>
<div class="row">
    <div class="form-group col-md-12">
        <label for="val_color">Accion</label><br>
        <textarea class="form-control w-100" required name="des_accion" type="text" id="des_accion" value="" rows="5" ></textarea>
    </div>
</div>

<div id="dZaccion" class="dropzone">
    <div class="dz-default dz-message">
        <h2><i class="mdi mdi-cloud-upload"></i> Arrastre archivos <span class="text-blue">para subirlos</span></h2>&nbsp&nbsp<h6 class="display-inline text-muted"> (o Click aqui)</h6>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-12 {{ $errors->has('id_estado_inicial') ? 'has-error' : '' }}">
        <label for="id_estado" class="control-label" style="text-align: left">Poner la {{ $entidad }} en estado</label>
        <select class="form-control select2" required id="id_estado" name="id_estado">
            @foreach ($estados as $estado)
                <option value="{{ $estado->id_estado }}" {{ $estado->id_estado==$incidencia->id_estado?'selected':'' }} data-cierre="{{ $estado->mca_cierre }}">
                    {{ $estado->des_estado }}
                </option>
            @endforeach
        </select>
            
        {!! $errors->first('id_estado_inicial', '<p class="help-block">:message</p>') !!}
    </div>
</div>
<div class="row" id="row_importe" style="display:none">
	<div class="form-group col-md-4 {{ $errors->has('val_importe') ? 'has-error' : '' }}">
		<label for="val_presupuesto" class="control-label">Importe final (€)</label>
		<input class="form-control"  name="val_importe" type="number" step="any"  id="val_importe"  maxlength="200" >
	</div>
</div>
<script>
	$('input[type="file"]').change(function(e){
		var fileName = e.target.files[0].name;
		$(this).next('label').html(fileName);
		//$('.custom-file-label').html(fileName);
	});
	$('#id_estado').change(function(){
		var id_estado = $(this).val();
		var txt_estado= $(this).find('option:selected').text();
		$('#des_accion').val('---> Cambiado estado a: '+txt_estado);
		if($(this).find('option:selected').data('cierre')=='S'){
			$('#row_importe').show();
		}else{
			$('#row_importe').hide();
		}
	});

	var myDropzone = new Dropzone("#dZaccion" , {
		url: '{{ url('/incidencias/upload_imagen/') }}',
		autoProcessQueue: true,
		uploadMultiple: true,
		parallelUploads: 1,
		maxFiles: {{ $config->num_imagenes_incidencias??2 }},
		addRemoveLinks: true,
		maxFilesize: 15,
		autoProcessQueue: true,
		acceptedFiles: 'image/*,video/*,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation',
		dictDefaultMessage: '<span class="text-center"><span class="font-lg visible-xs-block visible-sm-block visible-lg-block"><span class="font-lg"><i class="fa fa-caret-right text-danger"></i> Arrastre archivos <span class="font-xs">para subirlos</span></span><span>&nbsp&nbsp<h4 class="display-inline"> (O haga Click)</h4></span>',
		dictResponseError: 'Error subiendo fichero!',
		dictDefaultMessage :
			'<span class="bigger-150 bolder"><i class=" fa fa-caret-right red"></i> Drop files</span> to upload \
			<span class="smaller-80 grey">(or click)</span> <br /> \
			<i class="upload-icon fa fa-cloud-upload blue fa-3x"></i>'
		,
		dictResponseError: 'Error while uploading file!',
		headers: {
			'X-CSRF-TOKEN': "{{ csrf_token() }}"
		},
		init: function() {
			dzClosure = this; // Makes sure that 'this' is understood inside the functions below.
			this.on("sending", function(file, xhr, formData) {
				formData.append("id_cliente", {{ Auth::user()->id_cliente }});
				// formData.append("enviar_email", $("#enviar_email").is(':checked'));
				console.log(formData)
			});
			
			//send all the form data along with the files:
			this.on("sendingmultiple", function(data, xhr, formData) {
				console.log("multiple")
			});

			this.on("drop", function(event) {
				
			});

			this.on("removedfile", function(event) {
				console.log(event);
				value=event.name;
				lista_ficheros = lista_ficheros.filter(item => item.orig !== value);
				console.log(lista_ficheros);     
				ficheros_final=lista_ficheros.map(function(item,index,array){
					return item.nuevo;
				});
				$('#adjuntos').val(ficheros_final);
			});


			this.on("maxfilesexceeded", function(event) {
				toast_warning('Incidencias','El numero maximo de adjuntos es {{ $config->num_imagenes_incidencias??2 }}')   
			});

			this.on("success", function(file, responseText) {
				//Dropzone.forElement("#dZUpload").removeAllFiles(true);
				fic=new Object();
				fic.orig=responseText.filename;
				fic.nuevo=responseText.newfilename;
				lista_ficheros.push(fic);
				ficheros_final=lista_ficheros.map(function(item,index,array){
					return item.nuevo;
				});
				$('#adjuntos').val(ficheros_final);
				console.log(lista_ficheros);
			});
		}
	});

	$('.select2').select2({
		width: '100%',
		dropdownParent: $("#accion-incidencia")
	});
</script>