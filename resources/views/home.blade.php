@extends('layouts.app')

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

@section('breadcrumb')
<ol class="breadcrumb">
    <li><a href="{{url('/')}}"><i class="demo-pli-home"></i> Home</a></li>
    {{--  <li class="active">Helper Classes</li>  --}}
</ol>
@endsection

@section('content')

@endsection



@section('scripts')
    
@endsection
