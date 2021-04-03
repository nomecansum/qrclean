@php
    use Carbon\Carbon;
    if(!isset($reserva_sala)){
        $reserva_sala=[];
    }
@endphp

<div class="row mb-4 {{ $link??'' }}" data-id="{{ $sala->id_puesto }}" data-desc="{{ $sala->des_puesto }}">
    <div class="col-md-2 text-center p-t-10">
        <span class="text-2x">{{ $sala->cod_puesto }}</span><br>
        {{ $sala->des_puesto }}
        @if(config('app.debug'))
        <br>[{{ $sala->id_puesto }}]
        @endif
    </div>
    <div class="col-md-2">
        <img src="{{ isset($sala) ? Storage::disk(config('app.img_disk'))->url('img/puestos/'.$sala->img_puesto) : ''}}" style="width: 90%;" alt="" class="img-fluid ml-0">
    </div>
    <div class="col-md-8">
        <div class="row">
            <div class="d-flex flex-wrap mr-2 add-tooltip" title="Capacidad"><i class="fad fa-users fa-2x"></i><span class="text-2x font-bold">{{$sala->val_capacidad}}</span></div>
            <div class="d-flex flex-wrap mr-2 add-tooltip" title="Maximo tiempo de reserva"><i class="fad fa-clock fa-2x"></i><span class="text-2x font-bold">{{$sala->max_horas_reservar}}h</span></div>
            <div class="d-flex flex-wrap mr-2 add-tooltip" title="Proyector"><i class="fad fa-projector fa-2x text-info" style="{{ $sala->mca_proyector=='N'?'color:#eee':'' }}"></i></div>
            <div class="d-flex flex-wrap mr-2 add-tooltip" title="Pantalla"><i class="fad fa-tv-alt fa-2x  text-info"  style="{{ $sala->mca_pantalla=='N'?'color:#eee':'' }}"></i></div>
            <div class="d-flex flex-wrap mr-2 add-tooltip" title="Videoconferencia"><i class="fad fa-webcam fa-2x  text-info" style="{{ $sala->mca_videoconferencia=='N'?'color:#eee':'' }}"></i></div>
            <div class="d-flex flex-wrap mr-2 add-tooltip" title="Manos libres"><i class="fad fa-volume-up fa-2x  text-info" style="{{ $sala->mca_manos_libres=='N'?'color:#eee':'' }}"></i></div>
            <div class="d-flex flex-wrap mr-2 add-tooltip" title="Pizarra"><i class="fad fa-chalkboard fa-2x  text-info" style="{{ $sala->mca_pizarra=='N'?'color:#eee':'' }}"></i></div>
            <div class="d-flex flex-wrap mr-2 add-tooltip" title="Pizarra digital"><i class="fad fa-chalkboard-teacher fa-2x  text-info" style="{{ $sala->mca_pizarra_digital=='N'?'color:#eee':'' }}"></i></div>
        </div>
        <div class="row">
            <div class="col-md-12">
                {{ $sala->obs_sala }}
            </div>
        </div>
        <div class="row">
            <div style="border: 1px solid #999; width: 100%; height: 20px; border-radius: 3px; background-color: #ddf7c5">
                @foreach($reserva_sala as $res)
                    <div class="add-tooltip" title="Reservada de {{ Carbon::parse($res->fec_reserva)->format('H:i') }} a {{ Carbon::parse($res->fec_fin_reserva)->format('H:i') }} @if(config_cliente('mca_mostrar_nombre_usando')=='S') por {{ $res->name }} @endif" 
                        style="background-color: {{ $res->id_usuario==Auth::user()->id?'#ffd700':'#cd5c5c' }};border-radius: 2px; position: absolute; height: 18px; width: {{ 100*(Carbon::parse($res->fec_fin_reserva)->diffinMinutes(Carbon::parse($res->fec_reserva)))/1440 }}%; left: {{ 100*(Carbon::parse($res->fec_reserva)->secondsSinceMidnight()/60)/1440 }}%">
                    </div>
                @endforeach
            </div>
            
           
        </div>
        <div class="row nowrap">
            @for($n=0;$n<=23;$n++)
                <div style="width: {{ 100/24 }}%; height: 10px; font-size: 10px; color: #ccc; border-left: 1px solid #efefef; text-align: center; padding-left: 5px">{{ $n }}</div>
            @endfor
           
        </div>
    </div>
</div>