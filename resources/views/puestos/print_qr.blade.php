
@extends('layout')

@section('content')
@php
    
    try{
        $tam_fuente=round(16*session('CL')['tam_qr']/230);
    }   catch(\Exception $e){
            $tam_fuente=14;
    }
@endphp
<form method="POST" action="{{url('/puestos/print_qr')}}"  name="frm_qr" id="frm_qr">
    <div class="row b-all rounded mb-3  ml-3">
        
            {{csrf_field()}}
            <input type="hidden" name="formato" value="" id="formato">
            <input type="hidden" name="lista_id" value="{{ implode(",",$r->lista_id) }}" id="formato">
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
    <div class="row ml-3" style="background-color: #fff" id="printarea">
        @foreach($puestos as $puesto)
            <div class="text-center pb-4 pr-4 mr-0 ml-0  cont_qr" style="width: {{ $r->tam_qr }}px; display: inline-block; border: 1px solid #ccc;">
                <div class="mb-0 pb-0">
                    <img class="qr" src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size($r->tam_qr)->generate(config('app.url_base_scan').$puesto->token)) !!} ">
                    {{--  {{config('app.url_base_scan').$puesto->token}}  --}}
                </div>
                <div class="w-100 bg-white text-center font-bold mt-0 pb-2 texto_qr" style="color: {{$puesto->val_color}}; background-color: #fff; font-size: {{ $tam_fuente }}px">
                    <i class="{{$puesto->val_icono}}"></i>  {{$puesto->des_puesto}}
                </div>
            </div>
    
        @endforeach
        </div>
</form>
@endsection


@section('scripts')
<script>
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
