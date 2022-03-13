<div class="panel editor">
    <div class="panel-heading">
        <div class="panel-control">
            <button class="btn btn-default" data-panel="dismiss" data-dismiss="panel"><i class="demo-psi-cross"></i></button>
        </div>
        <h3 class="panel-title">Editar marca</h3>
    </div>

    <div class="panel-body">
        <form method="POST" action="{{ url('/ferias/marcas/save') }}" id="formulario" name="formulario" accept-charset="UTF-8" class="form-horizontal form-ajax"  enctype="multipart/form-data">
        {{ csrf_field() }}
            <div class="row">
                <input type="hidden" name="id_marca" value="{{ $datos->id_marca }}">
                <div class="form-group col-md-8 {{ $errors->has('des_marca') ? 'has-error' : '' }}">
                    <label for="des_incidencia" class="control-label">Nombre</label>
                    <input class="form-control"  name="des_marca" type="text" id="des_marca"  maxlength="50" value="{{ $datos->des_marca }}">
                    {!! $errors->first('des_marca', '<p class="help-block">:message</p>') !!}
                </div>
                <div class="form-group col-md-4 {{ $errors->has('id_cliente') ? 'has-error' : '' }}">
                    <label for="id_cliente" class="control-label">Cliente</label>
                    <select class="form-control" required id="id_cliente" name="id_cliente">
                        @foreach ($clientes as $key => $cliente)
                            <option value="{{ $key }}" {{ old('id_cliente', optional($datos)->id_cliente) == $key ? 'selected' : '' }}>
                                {{ $cliente }}
                            </option>
                        @endforeach
                    </select>
                    {!! $errors->first('id_cliente', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            

            <div class="row">
                <div class="form-group col-md-12 {{ $errors->has('observaciones') ? 'has-error' : '' }}">
                    <label for="observaciones" class="control-label">Descripcion</label>
                    <textarea class="form-control" name="observaciones" type="text" id="observaciones" value="" rows="4">{{ $datos->observaciones }}</textarea>
                    {!! $errors->first('observaciones', '<p class="help-block">:message</p>') !!}
                </div>
            </div>

            <div class="col-md-2 text-center b-all rounded">
                <div class="row font-bold" style="padding-left: 15px">
                    Imagen<br>
                </div>
                <div class="col-12">
                    <div class="form-group  {{ $errors->has('img_logo') ? 'has-error' : '' }}">
                        <label for="img_usuario" class="preview preview1">
                            <img src="{{ isset($datos) ? Storage::disk(config('app.img_disk'))->url('img/ferias/marcas/'.$datos->img_logo) : ''}}" style="margin: auto; display: block; width: 156px; heigth:180px" alt="" id="img_preview" class="img-fluid">
                        </label>
                        <input type="hidden" name="old_logo" id="old_logo" value="{{ isset($datos) ? $datos->img_logo : ''}}">
                        <div class="custom-file">
                            <input type="file" accept=".jpg,.png,.gif" class="form-control  custom-file-input editor_imagen" name="img_logo" id="img_logo" lang="es" value="{{ isset($datos) ? $datos->img_logo : ''}}">
                            <label class="custom-file-label" for="img_usuario"></label>
                        </div>
                    </div>
                        {!! $errors->first('img_logo', '<p class="help-block">:message</p>') !!}
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
    $('.form-ajax').submit(form_ajax_submit);

    $('input[type="file"]').change(function(e){
        var fileName = e.target.files[0].name;
        $(this).next('label').html(fileName);
        //$('.custom-file-label').html(fileName);
    });

    $(".editor_imagen").change(function(){
        readURL(this);
    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#img_preview').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $('#fecha').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        autoUpdateInput : true,
        //autoApply: true,
        locale: {
            format: '{{trans("general.date_format")}}',
            applyLabel: "OK",
            cancelLabel: "Cancelar",
            daysOfWeek:["{{trans('general.domingo2')}}","{{trans('general.lunes2')}}","{{trans('general.martes2')}}","{{trans('general.miercoles2')}}","{{trans('general.jueves2')}}","{{trans('general.viernes2')}}","{{trans('general.sabado2')}}"],
            monthNames: ["{{trans('general.enero')}}","{{trans('general.febrero')}}","{{trans('general.marzo')}}","{{trans('general.abril')}}","{{trans('general.mayo')}}","{{trans('general.junio')}}","{{trans('general.julio')}}","{{trans('general.agosto')}}","{{trans('general.septiembre')}}","{{trans('general.octubre')}}","{{trans('general.noviembre')}}","{{trans('general.diciembre')}}"],
            firstDay: {{trans("general.firstDayofWeek")}}
        },
        function() {
            
        }  
    });

    $('.demo-psi-cross').click(function(){
        $('.editor').hide();
    });
</script>
