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
        /* border: 1px solid silver; */
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
<div class="w-100" style="height: 60px; background-color:#4d627b">

</div>
<div class="text-center">
    <img src="/img/Mosaic_brand_300.png">
</div>

<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <div id="mensaje" class="alert text-center"></div>
    </div>
</div>
<div class="row">
    <div class="col-md-12" style="text-align: center;">
        <div id="qr" style="display: inline-block;">
            <div class="placeholder"> QR Code</div>
        </div>
        <div id="scannedCodeContainer"></div>
        <div id="feedback"></div>
    </div>
</div>
<div class="row scan-type-region camera" id="scanTypeCamera">
    <div class="col-md-4">
         
    </div>
    <div class="col-md-4 text-center">
        <button id="btn_requestPermission" class="btn btn-success btn-sm" style="display: none"> <i class="fad fa-question-square"></i> Habilitar permiso</button>
    </div>
    <div class="col-md-4">
         
    </div>
</div>
<div class="row">
    <div class="col-md-4">
         
    </div>
    <div class="col-md-4">
        <div id="selectCameraContainer" style="display: inline-block;"></div>
        <select id="cameraSelection" class="form-control"></select>
    </div>
    <div class="col-md-1 pt-2">
        <button id="switch_Button" class="btn btn-primary mt-3"><i class="fad fa-camera-alt"></i><i class="fad fa-repeat-alt"></i></button>
    </div>
    <div class="col-md-3">
        
    </div>
    
</div>
<div style="display: none">
    <button id="scan_Button" class="btn btn-success btn-sm">start scanning</button>
    <button id="stop_Button" class="btn btn-warning btn-sm">stop scanning</button>
</div>
@endsection


@section('scripts')
    <script src="{{ asset('/plugins/html5-qrcode/minified/html5-qrcode.min.js') }}"></script>

    <script>
        function requestPermission(){
            console.log('requestPermission');
            const scanRegionCamera = document.getElementById('scanTypeCamera');
            const scanButton = document.getElementById('scanButton');
            const stopButton = document.getElementById('stopButton');
            const requestPermissionButton = document.getElementById('requestPermission');
            const selectCameraContainer = document.getElementById('selectCameraContainer');
            const cameraSelection = document.getElementById('cameraSelection');
            const scannedCodeContainer = document.getElementById('scannedCodeContainer');
            const feedbackContainer = document.getElementById('feedback');
            const statusContainer = document.getElementById('status');
            const SCAN_TYPE_CAMERA = "camera";
            // declaration of html5 qrcode
            const html5QrCode = new Html5Qrcode("qr", /* verbose= */ true);
            var currentScanTypeSelection = SCAN_TYPE_CAMERA;
            var codesFound = 0;
            var lastMessageFound = null;
            const setPlaceholder = () => {
                const placeholder = document.createElement("div");
                placeholder.innerHTML = "";
                placeholder.className = "placeholder";
                document.getElementById('qr').appendChild(placeholder);
            }
            const setFeedback = message => {
                console.log(message);
                //feedbackContainer.innerHTML = message;
            }
            const setStatus = status => {
               console.log(status);
            }
            const qrCodeSuccessCallback = qrCodeMessage => {
                setStatus("Pattern Found");
                setFeedback("");
                if (lastMessageFound === qrCodeMessage.toLocaleLowerCase()) {
                    return;
                }
                ++codesFound;
                lastMessageFound = qrCodeMessage.toLocaleLowerCase();
                const result = document.createElement('div');
                result.innerHTML = `[${codesFound}] New code found: <strong>${qrCodeMessage}</strong>`;
                scannedCodeContainer.appendChild(result);
            }
            const qrCodeErrorCallback = message => {
                //setStatus("Scanning");
            }
            const videoErrorCallback = message => {
                setFeedback(`Video Error, error = ${message}`);
            }
            const classExists = (element, needle) => {
                const classList = element.classList;
                for (var i = 0; i < classList.length; i++) {
                    if (classList[i] == needle) {
                        return true;
                    }
                }
                return false;
            }
            const addClass = (element, className) => {
                if (!element || !className) throw "Both element and className mandatory";
                if (classExists(element, className)) return;
                element.classList.add(className);
            };
            const removeClass = (element, className) => {
                if (!element || !className) throw "Both element and className mandatory";
                if (!classExists(element, className)) return;
                element.classList.remove(className);
            }

            const setupCameraOption = () => {
                currentScanTypeSelection = SCAN_TYPE_CAMERA;
                html5QrCode.clear();
                setPlaceholder();
                removeClass(scanRegionCamera, "disabled");
                setFeedback("Click 'Start Scanning' to <b>start scanning QR Code</b>");
            }

            Html5Qrcode.getCameras().then(cameras => {

            if (cameras && cameras.length) {
                var camara = cameras[0].id;
               console.log(cameras);
            }

            if (cameras.length == 0) {
                return setFeedback("Error: Zero cameras found in the device");
            }
            for (var i = 0; i < cameras.length; i++) {
                const camera = cameras[i];
                const value = camera.id;
                const name = camera.label == null ? value : camera.label;
                const option = document.createElement('option');
                option.value = value;
                option.innerHTML = name;
                cameraSelection.appendChild(option);
            }

            def_value=getCookie('cam_def');    
            console.log('def '+def_value);       
            if(def_value!=null){
                $('#cameraSelection').val(def_value);
            }   

            scan_Button.addEventListener('click', () => {
                if (currentScanTypeSelection != SCAN_TYPE_CAMERA) return;
                const cameraId = cameraSelection.value;
                // Start scanning.
                html5QrCode.start(
                    cameraId,
                    {
                        fps: 10,
                        qrbox: 250
                    },
                    qrCodeSuccessCallback,
                    qrCodeErrorCallback)
                    .then(_ => {
                        setFeedback("");
                    })
                    .catch(error => {
                        videoErrorCallback(error);
                    });
            });
            stop_Button.addEventListener('click', function() {
                html5QrCode.stop().then(ignore => {
                    setFeedback('Stopped');
                    setFeedback("Click 'Start Scanning' to <b>start scanning QR Code</b>");
                    scannedCodeContainer.innerHTML = "";
                    setPlaceholder();
                }).catch(err => {
                    setFeedback('Error');
                    setFeedback("Race condition, unable to close the scan.");
                });
            });

            html5QrCode.clear();
            setPlaceholder();
            removeClass(scanRegionCamera, "disabled");
            setFeedback("Click 'Start Scanning' to <b>start scanning QR Code</b>");
            setupCameraOption();

            const cameraId = cameraSelection.value;
            // Start scanning.
            html5QrCode.start(
                cameraId,
                {
                    fps: 10,
                    qrbox: 250
                },
                qrCodeSuccessCallback,
                qrCodeErrorCallback)
                .then(_ => {
                    //setStatus("scanning");
                    setFeedback("");
                })
                .catch(error => {
                    videoErrorCallback(error);
                });

            }).catch(err => {
                console.log('Error camaras '+err);
                $('#mensaje').html('<i class="fad fa-exclamation-triangle"></i> No se ha podido acceder a la camara. <br> Debe dar permiso de acceso a la camara a QRClean');
                $('#mensaje').addClass('alert-danger');
                $('#mensaje').show();
                $('#btn_requestPermission').show();
            });
        }

        $(function(){
            requestPermission();
        });

        $('#btn_requestPermission').click(function(){
            location.reload();
        })

        $('#cameraSelection').change(function(){
            $('#stop_Button').click();
            setCookie('cam_def',$('#cameraSelection').val(),9999);
            $('#scan_Button').click();
        })

        $('#switch_Button').click(function(){
            
        })
    </script>

@endsection
