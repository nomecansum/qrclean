@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Encuesta</h1>
@endsection

@section('styles')

@endsection

@section('breadcrumb')

@endsection

@section('content')
@php
   //dd($encuesta);
@endphp
   <div class="row">
       <div class="col-md-12 text-center">
           <h2>{{ $encuesta->pregunta }}</h2>
       </div>
       <div class="row">
           <div class="col-md-12 text-center">
                @include('encuestas.selector',['tipo'=>$encuesta->id_tipo_encuesta])
           </div>
       </div>
   </div>
    

@endsection


@section('scripts')
    
@endsection
