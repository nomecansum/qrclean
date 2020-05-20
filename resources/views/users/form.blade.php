
@section('css')
<style>
    .floating-like-gmail {
        position: absolute;
        bottom: 8px;
        right: 10px;
        max-width: 100vw;
        width: 600px;
        right: 10px;
        text-align: right;
        opacity: 0;
        transition: 500ms;
    }
    .modal {
        text-align: center !important;
    }
    .hover-this:hover .floating-like-gmail,.floating-like-gmail:hover {
        opacity: 1;
    }
    .preview {
        width: 100%;
        height: 200px;
        background-position: center;
        background-size: cover;
        border: 1px solid #ccc;
        border-radius: 6px;
        overflow: hidden;
    }
    .img-preview {
        height: 100px;
        /*width: 100px;*/
        background-position: center;
        background-size: cover;
    }
    #img-inputs input {
        display: none;
        height: 0;
    }
    .sidebar-footer a {
        width: 50%;
    }
    .user-profile .profile-img::before {
        left: 0;
        right: 0;
    }
</style>
@endsection
<div class="row">
        <div class="col-md-8">
            <div class="row">
                <div class="form-group col-md-10 {{ $errors->has('name') ? 'has-error' : '' }}">
                    <label for="name" class="control-label">Nombre</label>
                    <input class="form-control" name="name" type="text" id="name" value="{{ old('name', optional($users)->name) }}" minlength="1" maxlength="255" required="true" placeholder="Enter name here...">
                    {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-10 {{ $errors->has('email') ? 'has-error' : '' }}">
                    <label for="email" class="control-label">e-mail</label>
                    <input class="form-control" name="email" type="text" id="email" value="{{ old('email', optional($users)->email) }}" minlength="1" maxlength="255" required="true" placeholder="Enter email here...">
                    {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-10 {{ $errors->has('password') ? 'has-error' : '' }}">
                    <label for="password" class="control-label">Password</label>
                    <input class="form-control" name="password" type="password" id="password" value="{{ old('password', optional($users)->password) }}" minlength="1" maxlength="255" required="true" placeholder="Enter password here...">
                    {!! $errors->first('password', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
        </div>
        <div class="col-md-1"></div>
        <div class="col-md-2 text-center">
            <label for="imagen_usuario" class="col-md-2 control-label">Imagen</label><br>
            <div class="col-12">
                <div class="form-group  {{ $errors->has('img_usuario') ? 'has-error' : '' }}">
                    <label for="img_usuario" class="preview preview1" style="background-image: url();">
                        <img src="{{ isset($users) ? url('img/users/',$users->img_usuario) : ''}}" style="margin: auto; display: block; width: 180px; heigth:180px" alt="" class="img-fluid">
                    </label>
                    <div class="custom-file">
                        <input type="file" accept=".jpg,.png,.gif" class="form-control  custom-file-input" name="img_usuario" id="img_usuario" lang="es">
                        <label class="custom-file-label" for="img_usuario"></label>
                    </div>
                </div>
                    {!! $errors->first('img_usuario', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
</div>
<div class="row">
        <div class="col-md-2">

        </div>
        <div class="col-md-1"></div>
        <div class="col-md-2">
            <div class="form-group {{ $errors->has('id_perfil') ? 'has-error' : '' }}">
                <label for="id_perfil" class="control-label">Perfil</label>
                <select class="form-control" id="cod_nivel" name="cod_nivel">
                    @foreach ($Perfiles as $Perfile)
                        <option value="{{ $Perfile->cod_nivel }}" {{ old('cod_nivel', optional($users)->cod_nivel) == $Perfile->cod_nivel ? 'selected' : '' }}>
                            {{ $Perfile->des_nivel_acceso }}
                        </option>
                    @endforeach
                </select>
                {!! $errors->first('cod_nivel', '<p class="help-block">:message</p>') !!}
            </div>

        </div>
        <div class="col-md-1"></div>
        {{-- //Vamos a añadir un combo con las zonas horarias --}}
        @php
            $regions = array(
                'Africa' => DateTimeZone::AFRICA,
                'America' => DateTimeZone::AMERICA,
                'Antarctica' => DateTimeZone::ANTARCTICA,
                'Asia' => DateTimeZone::ASIA,
                'Atlantic' => DateTimeZone::ATLANTIC,
                'Europe' => DateTimeZone::EUROPE,
                'Indian' => DateTimeZone::INDIAN,
                'Pacific' => DateTimeZone::PACIFIC
            );
            function tz_list() {
                $zones_array = array();
                $timestamp = time();
                $dummy_datetime_object = new DateTime();
                foreach(timezone_identifiers_list() as $key => $zone) {
                    date_default_timezone_set($zone);
                    $zones_array[$key]['zone'] = $zone;
                    $zones_array[$key]['diff_from_GMT'] = 'GMT' . date('P', $timestamp);
                    $tz = new DateTimeZone($zone);
                    $zones_array[$key]['offset'] = $tz->getOffset($dummy_datetime_object);
                }
                return $zones_array;
                }

        @endphp
        <div class="col-md-3">
            <div class="form-group">
                <label>Zona horaria</label>
                <select name="timezone" class="select2" style="width: 100%; margin-top: 25px; height: 38px">
                    <option value="" selected></option>
                    @foreach($regions as $region => $list)
                    <optgroup label="{{ $region }}">
                        @foreach(tz_list() as $tz)
                        @if (stripos($tz['zone'],$region)===0)
                                <option {{ isset($users) && $tz['zone'] == $users->val_timezone  ? 'selected' : ''}} value="{{ $tz['zone'] }}">{{ $tz['zone'] }} {{ $tz['diff_from_GMT'] }}</option>
                            @endif
                        @endforeach
                    </optgroup>
                    @endforeach
                </select>
            </div>
        </div>
</div>

@section('scripts_modulo')
<script>
    function readURL(input,element) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            console.log(reader)
            reader.onload = function (e) {
                $(element).html('<img src="'+e.target.result+'" style="height: 100%; margin: auto; display: block;" alt="">');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $(".imgInp").change(function(){
        readURL(this,$(this).data('preview'));
    });
</script>
@endsection
