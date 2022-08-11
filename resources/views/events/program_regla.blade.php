@php
    //dd($sched);   

    function check_hora($d,$h,$sched){
        foreach($sched as $sc){
            if($sc->num_dia==$d){
                foreach($sc->horas as $sh){
                    if($sh==$h){
                        return "checked";
                    }
                }
            }
        }
    }
@endphp


<div class="row mb-3">
    <div class="col-md-3">
        <label for="fec_inicio">Desde</label><br>
        <div class="input-group float-right" id="div_fechas">
            <input type="text" class="form-control pull-left singledate" name="fec_inicio"  id="fec_inicio" style="width:120px"  value="{{ isset($regla->fec_inicio) ? Carbon\Carbon::parse($regla->fec_inicio)->format('d/m/Y') : Carbon\Carbon::now()->format('d/m/Y') }}">
            <span class="btn input-group-text btn-mint" disabled  style="height: 40px"><i class="fas fa-calendar mt-1"></i></span>
        </div>
    </div>
    <div class="col-md-3">
        <label for="fec_inicio">Hasta</label><br>
        <div class="input-group float-right" id="div_fechas">
            <input type="text" class="form-control pull-left singledate" name="fec_fin"  id="fec_fin" style="width:120px"  value="{{ isset($regla->fec_fin) ? Carbon\Carbon::parse($regla->fec_fin)->format('d/m/Y') : Carbon\Carbon::now()->addYear()->format('d/m/Y') }}">
            <span class="btn input-group-text btn-mint" disabled  style="height: 40px"><i class="fas fa-calendar mt-1"></i></span>
        </div>
    </div>
</div>
<table class="table " border="0" cellpadding="0" cellspacing="0">
    <thead>
        <tr class="text-center p-0">
            <th></th>
            @for($h=0; $h<24; $h++)
                <th data-hora={{ $h }} class="td_hora p-0" style="width: 4%; padding-left:15px">{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}</th>
            @endfor
        </tr>
    </thead>
    <tbody>
        @for($d=1; $d<8; $d++)
            <tr class="p-0">
                <td class="td_dia" data-dia="{{ $d }}">{{$dowMap[$d-1]}}</td>
                @for($h=0; $h<24; $h++)
                    <td class="p-0 text-center">
                        <div class="p-0 m-0">
                            {{-- <input type="checkbox" class="chk_dia" name="dia{{ $d }}[]" data-dia="{{ $d }}" data-hora="{{ $h }}" id="check_{{ $d }}_{{ $h }}" value="{{ $h }}" @isset($sched){{ check_hora($d,$h,$sched) }}@endisset /> --}}
                            <input type="checkbox" class="form-control  magic-checkbox chk_dia"name="dia{{ $d }}[]" data-dia="{{ $d }}" data-hora="{{ $h }}" id="check_{{ $d }}_{{ $h }}" value="{{ $h }}" @isset($sched){{ check_hora($d,$h,$sched) }}@endisset> 
                            <label class="custom-control-label"   for="check_{{ $d }}_{{ $h }}"></label>
                        </div>
                    </td>
                @endfor
            </tr>
        @endfor
    </tbody>
</table>
<div class="row">
    <div class="col-md-12">
        <button type="submit" class="btn btn-primary btn_form float-right">{{trans('general.submit')}}</button>
    </div>
</div>

<script>
    $('.singledate').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        autoUpdateInput : true,
        autoApply: true,
        locale: {
            format: '{{trans("general.date_format")}}',
            applyLabel: "OK",
            cancelLabel: "Cancelar",
            daysOfWeek:["{{trans('general.domingo2')}}","{{trans('general.lunes2')}}","{{trans('general.martes2')}}","{{trans('general.miercoles2')}}","{{trans('general.jueves2')}}","{{trans('general.viernes2')}}","{{trans('general.sabado2')}}"],
            monthNames: ["{{trans('general.enero')}}","{{trans('general.febrero')}}","{{trans('general.marzo')}}","{{trans('general.abril')}}","{{trans('general.mayo')}}","{{trans('general.junio')}}","{{trans('general.julio')}}","{{trans('general.agosto')}}","{{trans('general.septiembre')}}","{{trans('general.octubre')}}","{{trans('general.noviembre')}}","{{trans('general.diciembre')}}"],
            firstDay: {{trans("general.firstDayofWeek")}}
        },
    });

    $('.singledate').change(function(){
        if(Date.parse($(fec_inicio).val()) > Date.parse($(fec_fin).val())){
        //end is less than start
        $(fec_inicio).val($(fec_fin).val());
        }
    })

    $('.td_dia').click(function(){
        $('*[data-dia='+$(this).data('dia')+']').each(function () { this.checked = !this.checked; });
    })

    $('.td_hora').click(function(){
        $('*[data-hora='+$(this).data('hora')+']').each(function () { this.checked = !this.checked; });
    })

    $('.chk_dia').click(function(){
        $(this).checked=!$(this).is('checked');
    })
</script>