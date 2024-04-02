<form  action="{{url('reservas/save')}}" method="POST" name="frm_contador" id="frm_contador" class="form-ajax">
    <div class="row">
        <input type="hidden" name="id_reserva" value="{{ $reserva->id_reserva }}">
        <input type="hidden" name="id_cliente" value="{{ $reserva->id_cliente }}">
        <input type="hidden" id="id_puesto" name="id_puesto" value="">
        <input type="hidden" id="des_puesto_form" name="des_puesto" value="">
        <input type="hidden" name="tipo_vista" id="tipo_vista" value="{{ Auth::user()->val_vista_puestos??'comprobar' }}">
        <input type="hidden" name="hora_inicio" id="hora_inicio" value="{{ isset($reserva->fec_reserva)?Carbon\Carbon::parse($reserva->fec_reserva)->format('H:i'):'00:00' }}">
        <input type="hidden" name="hora_fin" id="hora_fin" value="{{ isset($reserva->fec_fin_reserva)?Carbon\Carbon::parse($reserva->fec_fin_reserva)->format('H:i'):'23:59' }}">
        {{csrf_field()}}
        <div class="form-group col-md-4">
            <label for="fechas">Fecha</label>
            {{--  <div class="input-group">
                <input type="text" class="form-control pull-left singledate" id="fechas" name="fechas" style="width: 180px" value="{{ $f1->format('d/m/Y')}}">
                <span class="btn input-group-text btn-secondary datepickerbutton" disabled  style="height: 33px"><i class="fas fa-calendar mt-1"></i></span>
            </div>  --}}
            <div class="input-group">
                <input type="text" class="form-control pull-left" id="fechas" autocomplete="off" name="fechas" style="" value="{{ $f1->format('d/m/Y').' - '.$f1->format('d/m/Y') }}">
                <span class="btn input-group-text btn-secondary btn_calendario"   style="height: 40px"><i class="fas fa-calendar mt-1"></i> <i class="fas fa-arrow-right"></i> <i class="fas fa-calendar mt-1"></i></span>
            </div>

        </div>
        <div class="form-group col-md-3">
            <label for="id_edificio"><i class="fad fa-building"></i> Edificio</label>
            <select name="id_edificio" id="id_edificio" class="form-control">
                @foreach($edificios_usuario as $edificio)
                    <option value="{{ $edificio->id_edificio}}" {{ $reserva->id_edificio==$edificio->id_edificio?'selected':'' }}>{{ $edificio->des_edificio }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-2">
            <label for="planta"><i class="fad fa-layer-group"></i> Planta</label>
            <select name="id_planta" id="id_planta" class="form-control">
                <option value="0">Cualquiera</option>
                @foreach($plantas_usuario as $p)
                    <option value="{{ $p->id_planta}}" {{ $reserva->id_planta!=0?($reserva->id_planta==$p->id_planta?'selected':''):($p->id_planta==session('planta_pref')?'selected':'') }}>{{ $p->des_planta }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-3">
            <label for="id_usuario">Tipo de puesto</label>
            <select name="id_tipo_puesto" id="id_tipo_puesto" class="form-control">
                {{-- <option value="0">Cualquiera</option> --}}
                <option value="0">Seleccione un tipo de puesto</option>
                @foreach($tipos as $t)
                    <option value="{{ $t->id_tipo_puesto}}" {{ isset($reserva->id_tipo_puesto)&&$reserva->id_tipo_puesto==$t->id_tipo_puesto?'selected':'' }} data-observaciones="{{ $t->observaciones }}" data-slots="{{ $t->slots_reserva }}">{{ $t->des_tipo_puesto }}</option>
                @endforeach
            </select>
        </div>
        
        
       
        
    </div>
</form>