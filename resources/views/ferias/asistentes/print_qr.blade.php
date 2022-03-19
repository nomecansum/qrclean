@extends($layout)

@section('content')
@php
    
    try{
        if (isset($r->tam_qr)){
            $tf=$r->tam_qr;
        } else {
            $tf=session('CL')['tam_qr'];
        }
        $tam_fuente=round(15*$tf/230);
    }   catch(\Exception $e){
            $tam_fuente=14;
    }
@endphp
@if($layout!='layout_simple')
<form method="POST" action="{{url('/ferias/asistentes/print_qr')}}"  name="frm_qr" id="frm_qr">
    <div class="row b-all rounded mb-3  ml-3">
        
            {{csrf_field()}}
            <input type="hidden" name="formato" value="" id="formato">
            <div class="col-md-2">
                <div class="form-group">
                    <label for="">Tamaño QR</label>
                    <input type="number" class="form-control" min="50" max="500"  required name="tam_qr" id="tam_qr" value="{{ $r->tam_qr??230 }}">
                </div>
            </div>
            <div class="col-md-4">
               
            </div>

            <div class="col-md-2">
                <a href="javascript:void(0)" class="btn  btn-primary add-tooltip mt-3 " id="btn_pdf"  title="Exportar en PDF" data-id="1" data-url=""> <i class="fad fa-file-pdf"></i> Exportar a PDF</a>
            </div>

            <div class="col-md-2">
                <a href="javascript:void(0)" class="btn  btn-warning add-tooltip mt-3" id="btn_print" title="Imprimir" data-id="1" data-url=""> <i class="fad fa-print"></i> Impirmir</a>
            </div>

    </div>
    @else
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="{{ url('/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/plugins/fontawesome6/css/all.min.css') }}" rel="stylesheet">
@endif
<br><br>
@foreach($datos as $dato)
    <div style="background-color: #fff" id="printarea">
        <div class="row pb-4 pr-4 mr-0 ml-1 mb-4 cont_qr" style=" display: inline-block; border: 1px solid #ccc;padding: 5px 5px 5px 5px">
            <div class="col-md-6" >
                <img class="qr" src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size($r->tam_qr)->generate($dato->token)) !!} ">
            </div>
            <div class="col-md-6" >
                
            </div>
            <div class="w-100 bg-white text-center font-bold mt-0 pb-2 texto_qr col-md-12" style="background-color: #fff; font-size: {{ $tam_fuente }}px">
                {{$dato->nombre}} - {{ $dato->empresa }} - {{ $dato->email }}
            </div>
        </div>
    </div>
@endforeach
</form>
@endsection


@section('scripts')
<script>
    $('.ferias').addClass('active active-sub');
	$('.ferias_marcas').addClass('active-link');

    $('#btn_print').click(function(){
        $('#printarea:visible').printThis({
            importCSS: true,
            importStyle: true,
            footer: "<img src='{{ url('/imgcompo/Mosaic_brand_20.png') }}' class='float-right'>"
        });
    })

    $('#tam_qr').change(function(){
        event.preventDefault();
       $('.qr').css('width',$(this).val());
       $('.cont_qr').css('width',$(this).val());
       tam_fuente=Math.round(16*$(this).val()/230);
       $('.texto_qr').css("font-size", tam_fuente + "px");
    })

    $('#btn_pdf').click(function(){
        $('#formato').val('PDF');
        $('#frm_qr').submit();
    })


    $('#tam_qr').keydown(function(){
        console.log(event.keyCode);
        if(event.keyCode == 13) {
            event.preventDefault();
            return false;
            }
    })

</script>
@endsection
