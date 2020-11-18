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
        <form method="POST" action="{{ url('/incidencias/save') }}" id="incidencia_form" name="incidencia_form" accept-charset="UTF-8" class="form-horizontal form-ajax" enctype="multipart/form-data">
        {{ csrf_field() }}
            <div class="row">
                <input type="hidden" name="id_puesto" value="{{ $puesto->id_puesto }}">
                <input type="hidden" name="referer" value="{{ $referer }}">
                <div class="form-group col-md-8 {{ $errors->has('des_incidencia') ? 'has-error' : '' }}">
                    <label for="des_incidencia" class="control-label">Titulo</label>
                    <input class="form-control" required name="des_incidencia" type="text" id="des_incidencia"  maxlength="200" >
                    {!! $errors->first('des_incidencia', '<p class="help-block">:message</p>') !!}
                </div>
                <div class="form-group col-md-4 {{ $errors->has('id_tipo_incidencia') ? 'has-error' : '' }}">
                    <label for="id_tipo_incidencia" class="control-label">Tipo</label>
                    <select class="form-control" required id="id_tipo_incidencia" name="id_tipo_incidencia">
                        @foreach ($tipos as $tipo)
                            <option value="{{ $tipo->id_tipo_incidencia }}">
                                {{ $tipo->des_tipo_incidencia }}
                            </option>
                        @endforeach
                    </select>
                </div>   
            </div>
            <div class="row">
                <div class="form-group col-md-12 {{ $errors->has('txt_incidencia') ? 'has-error' : '' }}">
                    <label for="txt_incidencia" class="control-label">Descripcion</label>
                    <textarea class="form-control" name="txt_incidencia" type="text" id="txt_incidencia" value="" rows="4"></textarea>
                    {!! $errors->first('txt_incidencia', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
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
            <div class="form-group">
                <div class="col-md-12 text-right">
                    <input class="btn btn-primary" type="submit" value="Guardar">
                </div>
            </div>
        </form>

    </div>
</div>

