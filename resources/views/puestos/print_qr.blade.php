
@extends('layout_simple')

@section('content')
    <div class="row" style="background-color: #fff" id="printarea">
        @foreach($puestos as $puesto)
            <div class="text-center pb-4 pr-4 mr-0 ml-0" style="width: 240px; display: inline-block; border: 1px solid #ccc;">
                <div class="mb-0 pb-0">
                    <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(session('CL')['tam_qr'])->generate(config('app.url_base_scan').$puesto->token)) !!} ">
                    {{--  {{config('app.url_base_scan').$puesto->token}}  --}}
                </div>
                <div class="w-100 bg-white text-center font-bold mt-0 pb-2" style="color: {{$puesto->val_color}}; background-color: #fff">
                    <i class="{{$puesto->val_icono}}"></i>  {{$puesto->des_puesto}}
                </div>
            </div>
    
        @endforeach
        </div>
@endsection

