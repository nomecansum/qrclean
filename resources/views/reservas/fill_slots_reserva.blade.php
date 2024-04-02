@php
    use Carbon\Carbon;
    use Carbon\CarbonPeriod;
    setlocale(LC_TIME, 'Spanish');
    $period = CarbonPeriod::create(Carbon::parse($f1), Carbon::parse($f2));
    $num_dias=$period->count();
    $primero=true;
    $dias=['lunes','martes','miércoles','jueves','viernes','sábado','domingo'];
@endphp
<label>Slots de reserva disponibles</label><br>
@foreach($period as $date)
    {{-- $date->dayOfWeek --}}
    @foreach($slots as $slot)
        @if(!isset($slot->dia_semana)) @php $slot->dia_semana=-1; @endphp @endif
        @if(($date->dayOfWeek-1)==$slot->dia_semana || $slot->dia_semana==-1)
            <a class="btn btn-default slot @if($primero) btn1 @endif @if((isset($reserva) && Carbon\Carbon::parse($reserva->fec_reserva)->format('H:i')==$slot->hora_inicio && Carbon\Carbon::parse($reserva->fec_fin_reserva)->format('H:i')==$slot->hora_fin ) ) btn-info   @endif btn_slot" href="javascript:void(0)" data-inicio="{{ $slot->hora_inicio }}" data-fin="{{ $slot->hora_fin }}">
                {{ $slot->hora_inicio  }} <i class="fa-solid fa-right"></i> {{ $slot->hora_fin }} <br> {{ $slot->etiqueta??'' }} <br> @if($num_dias>1)({{ $slot->dia_semana!=-1?$dias[$slot->dia_semana]:$dias[$date->format('w')] }}) @endif
            </a>
            @php $primero=false; @endphp
        @endif
    @endforeach
    
@endforeach

<script>
    $('.btn_slot').click(function(){
        $('#hora_inicio').val($(this).data('inicio'));
        $('#hora_fin').val($(this).data('fin'));
        $('.btn_slot').removeClass('btn-info');
        $(this).addClass('btn-info');
        comprobar_puestos();  
    })
    @if(!isset($reserva))
        $('.btn1').addClass('btn-info');
        //$('.slot.btn1').click();
    @endif
</script>