@extends('layout_email')
@php
    use App\Models\incidencias;
    use App\Models\incidencias_tipos;
    use App\Models\users;

    //$inc=incidencias::find(3);
    $tipo=incidencias_tipos::find($inc->id_tipo_incidencia);
    $puesto=DB::table('puestos')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where('id_puesto',$inc->id_puesto)
            ->first();
    $usuario=users::find($inc->id_usuario_apertura);
@endphp

<div class="row">
    <div class="col-md-12 text-center">
        <img src="{{ Storage::disk(config('app.img_disk'))->url('img/clientes/images/'.session('logo_cliente')) }}" style="width: 13vw" alt="" onerror="this.src='{{ config('app.url_asset_mail').'/img/Mosaic_brand_300.png' }}';">
        {{-- <img src="data:image/png;base64, {{ base64_encode(public_path('/img/Mosaic_brand_300.png')) }}" /> --}}
    </div>
</div>
<div class="row  text-center">
    El usuario {{ $usuario->name }} ( <a href="mailto:{{ $usuario->email }}"> {{ $usuario->email }} </a> ) ha creado una incidencia de tipo <b>{{ $tipo->des_tipo_incidencia }}</b> el {!! beauty_fecha($inc->fec_apertura) !!} en el puesto {{ $puesto->des_puesto }}, edificio {{ $puesto->des_edificio  }} | {{ $puesto->des_planta }} 
</div>
<div class="row  text-center" style="margin-top: 10px; font-weight: bold">
    {{ $inc->des_incidencia }}
</div>
<div class="row  text-center" style="margin-top: 10px;">
    {{ $inc->txt_incidencia }}
</div>
<br>
<br>
<div class="row  text-center text-muted">
    Muchas gracias por su colaboración, este e-mail no acepta mensajes entrantes. Para cualquier duda por favor, acceda a la solución y a través de un nuevo escaneo háganoslo saber, muy gustosamente, estaremos encantados de ayudarle .Un cordial saludo
</div>
