<div class="form-group">
    <label>Fechas</label>
    <div class="input-group">
        <input type="text" class="form-control pull-left" id="fechas" name="fechas" style="height: 33px; width: 200px" value="{{ (isset($f1)?$f1->format('d/m/Y'):Carbon\Carbon::now()->startofmonth()->format('d/m/Y')).' - '.(isset($f2)?$f2->format('d/m/Y'):Carbon\Carbon::now()->endofmonth()->format('d/m/Y')) }}">
        <span class="btn input-group-text btn-mint" disabled  style="height: 44px;"><i class="fas fa-calendar mt-1"></i></span>
    </div>
</div>
@section('scripts5')
<script>
//Date range picker
$('#fechas').daterangepicker({
    autoUpdateInput: false,
    locale: {
        format: '{{trans("general.date_format")}}',
        applyLabel: "OK",
        cancelLabel: "Cancelar",
        daysOfWeek:["{{trans('general.domingo2')}}","{{trans('general.lunes2')}}","{{trans('general.martes2')}}","{{trans('general.miercoles2')}}","{{trans('general.jueves2')}}","{{trans('general.viernes2')}}","{{trans('general.sabado2')}}"],
        monthNames: ["{{trans('general.enero')}}","{{trans('general.febrero')}}","{{trans('general.marzo')}}","{{trans('general.abril')}}","{{trans('general.mayo')}}","{{trans('general.junio')}}","{{trans('general.julio')}}","{{trans('general.agosto')}}","{{trans('general.septiembre')}}","{{trans('general.octubre')}}","{{trans('general.noviembre')}}","{{trans('general.diciembre')}}"],
        firstDay: {{trans("general.firstDayofWeek")}}
    },
    opens: 'right',
});
</script>
@endsection