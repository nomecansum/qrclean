
<div class="row">
    <div class="form-group col-md-12 {{ $errors->has('des_edificio') ? 'has-error' : '' }}">
        <label for="des_edificio" class="control-label">Nombre</label>
        <input class="form-control" name="des_edificio" type="text" id="des_edificio" value="{{ old('des_edificio', optional($edificios)->des_edificio) }}" maxlength="200" placeholder="Enter des edificio here...">
        {!! $errors->first('des_edificio', '<p class="help-block">:message</p>') !!}
    </div>
</div>
<div class="row">
    <div class="form-group col-md-6{{ $errors->has('id_cliente') ? 'has-error' : '' }}">
        <label for="id_cliente" class="control-label">Cliente</label>
        <select class="form-control" id="id_cliente" name="id_cliente">
                <option value="" style="display: none;" {{ old('id_cliente', optional($edificios)->id_cliente ?: '') == '' ? 'selected' : '' }} disabled selected>Enter id cliente here...</option>
            @foreach ($Clientes as $key => $Cliente)
                <option value="{{ $key }}" {{ old('id_cliente', optional($edificios)->id_cliente) == $key ? 'selected' : '' }}>
                    {{ $Cliente }}
                </option>
            @endforeach
        </select>
            
        {!! $errors->first('id_cliente', '<p class="help-block">:message</p>') !!}
    </div>
</div>
