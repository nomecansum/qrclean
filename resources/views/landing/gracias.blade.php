@extends('layouts.landing')
@section('estilos')
<style type="text/css">
.big-checkbox {width: 25px; height: 25px;}
input[type="checkbox"] {
    vertical-align: middle;
}
</style>
@endsection
@section('cuerpo')
 <!-- Contact-->
 <section class="page-section" id="contact">
    <div class="container">
        <div class="text-center quitar">
            <h2 class="section-heading text-uppercase">Muchas gracias</h2>
            <h3 class="section-subheading text-muted">Su solicitud de mas informacion para {{ $detalles->des_marca }} ha quedado registrada.<br><br>En breve nos pondremos en contacto con usted</h3>
        </div>

       
    </div>
</section>

@endsection


@section('scripts1')
    <script>
       
    
@endsection