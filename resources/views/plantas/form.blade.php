
<div class="row">
    <div class="form-group col-md-12 {{ $errors->has('des_planta') ? 'has-error' : '' }}">
        <label for="des_planta" class="control-label">Nombre</label>
            <input class="form-control" name="des_planta" type="text" id="des_planta" value="{{ old('des_planta', optional($plantas)->des_planta) }}" maxlength="50" placeholder="Enter des planta here...">
            {!! $errors->first('des_planta', '<p class="help-block">:message</p>') !!}
    </div>
</div>

<div class="row">
    <div class="form-group col-md-6 {{ $errors->has('id_cliente') ? 'has-error' : '' }}">
        <label for="id_cliente" class="control-label">Cliente</label>
            <select class="form-control" id="id_cliente" name="id_cliente">
                    <option value="" style="display: none;" {{ old('id_cliente', optional($plantas)->id_cliente ?: '') == '' ? 'selected' : '' }} disabled selected>Enter id cliente here...</option>
                @foreach ($Clientes as $key => $Cliente)
                    <option value="{{ $key }}" {{ old('id_cliente', optional($plantas)->id_cliente) == $key ? 'selected' : '' }}>
                        {{ $Cliente }}
                    </option>
                @endforeach
            </select>
            
            {!! $errors->first('id_cliente', '<p class="help-block">:message</p>') !!}
    </div>

    <div class="form-group col-md-6 {{ $errors->has('id_edificio') ? 'has-error' : '' }}">
        <label for="id_edificio" class="control-label">Edificio</label>
            <select class="form-control" id="id_edificio" name="id_edificio">
                    <option value="" style="display: none;" {{ old('id_edificio', optional($plantas)->id_edificio ?: '') == '' ? 'selected' : '' }} disabled selected>Enter id edificio here...</option>
                @foreach ($Edificios as $key => $Edificio)
                    <option value="{{ $key }}" {{ old('id_edificio', optional($plantas)->id_edificio) == $key ? 'selected' : '' }}>
                        {{ $Edificio }}
                    </option>
                @endforeach
            </select>
            
            {!! $errors->first('id_edificio', '<p class="help-block">:message</p>') !!}
    </div>
</div>
