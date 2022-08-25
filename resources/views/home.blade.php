@extends('layout')

@section('styles')
<style>
    #qr {
        width: 640px;
        border: 1px solid silver
    }
    @media(max-width: 600px) {
        #qr {
            width: 300px;
            border: 1px solid silver
        }
    }
    button:disabled,
    button[disabled]{
      opacity: 0.5;
    }
    .scan-type-region {
        display: block;
        border: 1px solid silver;
        padding: 10px;
        margin: 5px;
        border-radius: 5px;
    }
    .scan-type-region.disabled {
        opacity: 0.5;
    }
    .empty {
        display: block;
        width: 100%;
        height: 20px;
    }
    #qr .placeholder {
        padding: 50px;
    }
    </style>
@endsection

@section('title')
{{--  <h1 class="page-header text-overflow pad-no">Helper Classes</h1>  --}}
@endsection


@section('content')
    <div id="page-head">
        <div class="row">
            <div class="col-md-12 text-center">
                @if(session('logo_cliente'))
                <img src="{{ Storage::disk(config('app.img_disk'))->url('img/clientes/images/'.session('logo_cliente')) }}" class="rounded" style="max-width:400px; width: 30vw" alt="">
                @else   
                <img src="{{ url('/img/Mosaic_brand_white.png') }}" style="height: 100px">
                @endif
            </div>
        </div>
        
    
        
       
        <div class="pad-all text-center text-primary mt-3">
            <div class="text-primary text-3x font-bold">Bienvenido de nuevo {{ Auth::user()->name }}</div>
            <p1>Su ultima visita fue el {!! beauty_fecha(Auth::user()->last_login) !!}<p></p>
        </p1></div>
    </div>
    
    @include($contenido_home)
    

@endsection

@section('scripts6')
    <script>
        // inicializacion de onesignal
        window.OneSignal = window.OneSignal || [];
        OneSignal.push(function() {
            OneSignal.init({
            appId: "{{ env('ONESIGNAL_APP_ID') }}",
            });
            OneSignal.setExternalUserId("{{Auth::user()->id}}");
            OneSignal.sendTags({"cliente": "{{Auth::user()->id_cliente}}"});
        });
        
        OneSignal.push(function() {               
            OneSignal.getUserId().then(function(userId) {
                alert("User ID:", userId);
                $.post('{{url('/users/osid')}}', {_token: '{{csrf_token()}}', data: userId}, function(data, textStatus, xhr) {
            
                })
            });
        });
    </script>

@endsection


