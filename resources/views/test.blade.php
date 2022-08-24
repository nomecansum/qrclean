@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Pagina de prueba</h1>
@endsection

@section('styles')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item">Configuracion</li>
        <li class="breadcrumb-item"><a href="{{url('/users')}}">usuarios</a></li>
        <li class="breadcrumb-item active">Editar usuario {{ !empty($users->name) ? $users->name : '' }}</li>
    </ol>
@endsection

@section('content')
<div class="row botones_accion mb-2">
    <br><br>
</div>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Titulo</h3>
    </div>
    <div class="card-body">
       {{-- @php
           $icons=[];
           $json=file_get_contents(public_path('/plugins/fontawesome6/metadata/categories.json'));
           $json=json_decode($json);
           foreach($json as $cat){
                foreach($cat->icons as $icon){
                    $icons[]='fa-solid fa-'.$icon;
                    $icons[]='fa-regular fa-'.$icon;
                    $icons[]='fa-dutone fa-'.$icon;
                }
           }
           $icons=array_unique($icons);
           dd(json_encode(array_values($icons)));
       @endphp --}}

       @php
          use Sendpulse\RestApi\ApiClient;
          use Sendpulse\RestApi\Storage\FileStorage;

            $SPApiClient = new ApiClient("6e43e3c74ee1634a1df029ffe518c13a", "ef7ce241d35b94f590278b83a131e4fd", new FileStorage());

            $task = array(
                'title' => 'Hello!',
                'body' => 'This is my first push message',
                'website_id' => 1,
                'ttl' => 20,
                'stretch_time' => 0,
            );

            // This is optional
            $additionalParams = array(
                'link' => 'https://qrclean.techlab.mobi',
                'filter_browsers' => 'Chrome,Safari',
                'filter_lang' => 'en',
                'filter' => '{"variable_name":"some","operator":"or","conditions":[{"condition":"likewith","value":"a"},{"condition":"notequal","value":"b"}]}',
            );
            dd($SPApiClient->createPushTask($task, $additionalParams));

       @endphp
    </div>
</div>

@endsection


@section('scripts')
    <script>
        $('.SECCION_MENU').addClass('active active-sub');
        $('.ITEM_MENU').addClass('active-link');
    </script>
@endsection
