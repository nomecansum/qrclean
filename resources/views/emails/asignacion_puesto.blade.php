@extends('layout_email')
@php
    use App\Models\puestos;

@endphp

<div class="row">
    <div class="col-md-12 text-center">
        <img src="{{ url('/img/Mosaic_brand_300.png') }}">
        {{-- <img src="data:image/png;base64, {{ base64_encode(public_path('/img/Mosaic_brand_300.png')) }}" /> --}}
    </div>
</div>
<div class="row  text-center">
    {{ $body }}
</div>
