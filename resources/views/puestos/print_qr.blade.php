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
<form method="POST" action="{{url('/puestos/preview_qr')}}"  name="frm_qr" id="frm_qr">
    <div class="row b-all rounded mb-3  ml-3">
        
            {{csrf_field()}}
            <input type="hidden" name="formato" value="" id="formato">
            <input type="hidden" name="lista_id" value="{{ implode(",",$r->lista_id) }}" id="formato">
            <div class="col-md-1">
                <div class="form-group">
                    <label for="">Tamaño</label>
                    <input type="number" class="form-control" min="50" max="500"  required name="tam_qr" id="tam_qr" value="{{ $r->tam_qr??230 }}">
                </div>
            </div>

            <div class="col-md-1">
                <div class="form-group">
                    <label for="">Columnas</label>
                    <input type="number" class="form-control" min="1" max="10"  required name="col" id="col" value="{{ $r->col??4 }}">
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
@php
$columna=1;
@endphp
<br><br>
    <div style="background-color: #fff" id="printarea">
        
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

    $('#tam_qr,#col').change(function(){
        let form = $('#frm_qr');
        let data = new FormData(form[0]);
        $.ajax({
            url: '{{url("/puestos/preview_qr")}}',
            type: 'POST',
            contentType: false,
            processData: false,
            data: data,
        })
        .done(function(data) {
            $('#printarea').html(data);
        });
    });
        // event.preventDefault();
        // $('#frm_qr').attr('action','{{url("/puestos/preview_qr")}}');
        // $('#frm_qr').submit();
    //    $('.qr').css('width',$(this).val());
    //    $('.cont_qr').css('width',$(this).val());
    //    tam_fuente=Math.round(16*$(this).val()/230);
    //    $('.texto_qr').css("font-size", tam_fuente + "px");


    $('#btn_pdf').click(function(){
        $('#formato').val('PDF');
        $('#frm_qr').submit();
        $('#frm_qr').attr('action','{{url("/puestos/print_qr")}}');
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
