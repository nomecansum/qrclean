@extends('layout_email')
@php
    use App\Models\puestos;

@endphp

<div class="row">
    <div class="col-md-12 text-center">
        <img src="{{ Storage::disk(config('app.img_disk'))->url('img/clientes/images/'.$cliente->img_logo) }}" style="width: 13vw" alt="" onerror="this.src='{{ config('app.url_asset_mail').'/img/Mosaic_brand_300.png' }}';">
        {{-- <img src="data:image/png;base64, {{ base64_encode(public_path('/img/Mosaic_brand_300.png')) }}" /> --}}
    </div>
</div>
<div class="row  text-center">
    {!! $body !!}
</div>
<br>
<br>
<div class="row  text-center text-muted">
    Muchas gracias por su colaboración, este e-mail no acepta mensajes entrantes. Para cualquier duda por favor, acceda a la solución y a través de un nuevo escaneo háganoslo saber, muy gustosamente, estaremos encantados de ayudarle .Un cordial saludo
</div>

