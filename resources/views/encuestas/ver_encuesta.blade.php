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
   <div class="row" id="selector">
       <div class="col-md-12 text-center">
           <h3>{{ $encuesta->pregunta }}</h3>
           @if($encuesta->mca_anonima=='S')
              <h6> (esta encuesta es anonima)</h6>
           @endif
       </div>
       <div class="row" >
           <div class="col-md-12 text-center">
                @include('encuestas.selector',['tipo'=>$encuesta->id_tipo_encuesta])
           </div>
       </div>
   </div>

   <div class="row" id="respuesta" style="display: none">
        <div class="col-md-12 text-center">
            <h1><i class="fad fa-thumbs-up fa-2x text-success"></i> ¡Muchas gracias por su colaboracion!</h1>
        </div>

   </div>

@endsection


@section('scripts')
<script>
    $('.valor').click(function(){
        $(this).css('background-color','#7fff00')
        console.log($(this).data('value'));
        $.post('{{url('/encuestas/save_data')}}', {_token:'{{csrf_token()}}',val: $(this).data('value'), id_encuesta: "{{ $encuesta->token }}", mca_anonima: "{{ $encuesta->mca_anonima }}"}, function(data, textStatus, xhr) {
           console.log(data);
           $('#selector').hide();
           $('#respuesta').show();
           animateCSS('#respuesta','bounceInRight');
		});
    })
    $('.valor').css('cursor', 'pointer');
</script>
@endsection
