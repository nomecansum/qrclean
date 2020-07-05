@php
$edificios=$detalles->pluck('des_edificio','id_edificio')->unique();
$plantas=$detalles->pluck('des_planta','id_planta')->unique();
$puestos=$detalles;
$cnt_edificios=$edificios->count();
$cnt_plantas=$plantas->count();
$cnt_puestos=$puestos->count();
$puestos_si=$detalles->wherenotnull('fec_fin')->count();
$puestos_no=$detalles->wherenull('fec_fin')->count();
try{
    $pct_completado=(100*$puestos_si/$cnt_puestos);
} catch(\Exception $e){
    $pct_completado=0;
}

@endphp
<style type="text/css">
    .lista_emp {
    columns: 3;
    -webkit-columns: 3;
    -moz-columns: 3;
    font-size: 16px;
    list-style-type: none;
    }
</style>
@if($print==1)
    <head>
        <!--Open Sans Font [ OPTIONAL ]-->
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700' rel='stylesheet' type='text/css'>
        <link href="{{ url('/plugins/jquery-ui/jquery-ui.css') }}" rel="stylesheet">
        <link href="{{ url('/css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ url('/css/nifty.min.css') }}" rel="stylesheet">
        <link href="{{ url('/css/demo/nifty-demo.css') }}" rel="stylesheet">

        <link rel="stylesheet" href="{{ URL('/css/materialdesignicons.min.css') }}">
        <link href="{{ asset('/plugins/fontawesome5/css/all.min.css') }}" rel="stylesheet">
        <link href="{{ url('/css/mosaic.css') }}" rel="stylesheet">
        <h1 class="modal-title">Ronda de limpieza <span class="idronda">#{{ $ronda->id_ronda }}</span></h1>
    </head>
@endif

<div class="row">
    <div class="col-md-2 form-group">
        <label class="font-bold">Fecha</label>
        <div>{!! beauty_fecha($ronda->fec_ronda) !!}</div>
    </div>
    <div class="col-md-6 form-group">
        <label  class="font-bold">Descripcion</label>
        <div>{{ $ronda->des_ronda }}</div>
    </div>
    <div class="col-md-4 form-group">
        <label  class="font-bold">Creada</label>
        <div>{{ $ronda->name }}</div>
    </div>
</div>
<div class="row">
    <div class="col-md-3 form-group ">
        <div class="text-2x text-center add-tooltip" title="{{ $cnt_edificios }} Edificios"> <i class="fad fa-building"></i> {{ $cnt_edificios }} </div>
    </div>
    <div class="col-md-3 form-group"> 
        <div class="text-2x text-center add-tooltip" title="{{ $cnt_plantas }} Plantas"><i class="fad fa-layer-group"></i> {{ $cnt_plantas }}</div>
    </div>
    <div class="col-md-3 form-group">
        <div class="text-2x text-center add-tooltip" title="{{ $cnt_puestos }} Puestos"> <i class="fad fa-desktop-alt"></i> {{ $cnt_puestos }}</div>
    </div>
    <div class="col-md-3 form-group  text-center">
        <div class="text-2x font-bold text-center text-{{ color_porcentaje($pct_completado) }}">{{ round($pct_completado) }} %</div>
    </div>
</div>
<div class="row b-all rounded mb-2">
    <label class="mt-1 ml-1 mb-1">Pesonal asignado</label>
    <ul class="lista_emp">
        @foreach($limpiadores as $l)
            <li class="mb-1 item_empleado hover-this rounded pt-1 pl-1 pb-1"  data-id="{{ $l->id }}" style="color: {{ genColorCodeFromText("EMPLEADO".$l->id,2) }}; cursor: pointer;"><i class="fas fa-male fa-2x"></i> {{ $l->name }}</li>
        @endforeach
    </ul>
</div>
<div class="row mb-3  b-all rounded">
    <label class="mt-1 ml-1 mb-1">Puestos</label>

    @foreach($edificios as $key_edif=>$value_edif)
    
        <div class="col-md-12 bg-gray-dark font-2x font-bold">
            <i class="fad fa-building"></i> {{ $value_edif }}
        </div>
        @php
            $plantas=$detalles->where('id_edificio',$key_edif)->pluck('des_planta','id_planta')->unique();
        @endphp
        @foreach($plantas as $key_planta=>$value_planta)
            <div class="col-md-12 bg-gray font-2x font-bold">
                <i class="fad fa-layer-group"></i> {{ $value_planta }}
            </div>
            @php
                $puestos=$detalles->where('id_planta',$key_planta);
            @endphp
            @foreach($puestos as $p)
                <div class="col-md-2 rounded add-tooltip divpuesto_ronda" id="divpuesto{{ $p->key_id }}" data-user="{{ $p->user_audit }}" data-id="{{ $p->key_id }}" data-puesto="{{ $p->cod_puesto }}" data-container="body" title="@if(!isset($p->user_audit)) Puesto prendiente de completar @else Completado por {{ $p->name }} el {!! Carbon\Carbon::parse($p->fec_fin)->isoFormat('LLLL') !!} @endif" style="height: 45px; padding: 3px; @if(!isset($p->user_audit)) border: 2px dashed salmon; cursor: pointer; @else border: 1px solid #ccc; @endif">
                    {{ $p->cod_puesto }}<br>
                    @if(isset($p->user_audit))<i class="fas fa-male" style="color: {{ genColorCodeFromText("EMPLEADO".$p->user_audit,2) }}"></i> <span style="font-size: 12px">{!! beauty_fecha($p->fec_fin) !!}</span> @else --- @endif
                </div>
            @endforeach
        @endforeach
    @endforeach
</div>
<script>
    
    id_user={{ Auth::user()->id }};

    $('.divpuesto_ronda').click(function(){
        
        if(!$(this).data('user'))
        {
            console.log('actualizar');
            $.post("{{ url('/rondas/estado_puesto_ronda') }}", {_token:'{{csrf_token()}}',id:$(this).data('id'), user: id_user}, function(data, textStatus, xhr) {
                console.log(data);
                if (data.error){
                    toast_error('ERROR',data.error);
                    return;
                }
                $.each(data.id,function(index,value){
                    $('#divpuesto'+value).addClass('bg-success');
                    toast_ok('Estado',$('#divpuesto'+value).data('puesto')+' Actualizado');
                });
                
            })   
        } else {
            console.log('no actualizar, ya esta hecho');
        }
       
    })

    $('.item_empleado').click(function(){
        $('.item_empleado').removeClass('b-all bg-gray');
        $(this).addClass('b-all bg-gray');
        id_user=$(this).data('id');
        console.log('Cambiado a '+id_user);
    })
</script>
