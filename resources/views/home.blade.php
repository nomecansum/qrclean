@extends('layout')

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
<div id="reader" width="600px"></div>
@endsection


@section('scripts')
    <script src="{{ asset('/plugins/html5-qrcode/minified/html5-qrcode.min.js') }}"></script>
    <script>
        // This method will trigger user permissions
        Html5Qrcode.getCameras().then(devices => {
        /**
        * devices would be an array of objects of type:
        * { id: "id", label: "label" }
        */
        console.log(devices);
        if (devices && devices.length) {
            var cameraId = devices[0].id;
            // .. use this to start scanning.
        }
        }).catch(err => {
        // handle err
        console.log(err);
        });
    </script>
@endsection
