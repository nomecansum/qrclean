@extends('layout_email')
@php
    use App\Models\puestos;
@endphp

<div class="row">
    <div class="col-md-12 text-center">
        <img src="{{ Storage::disk(config('app.img_disk'))->url('img/clientes/images/'.session('logo_cliente')) }}" style="width: 13vw" alt="" onerror="this.src='{{ config('app.url_asset_mail').'/img/Mosaic_brand_300.png' }}';">
        {{-- <img src="data:image/png;base64, {{ base64_encode(public_path('/img/Mosaic_brand_300.png')) }}" /> --}}
    </div>
</div>
<div class="row  text-center">
    {!! $body !!}
</div>
