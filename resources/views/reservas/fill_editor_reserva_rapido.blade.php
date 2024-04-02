<h4>{{ Carbon\Carbon::parse($f1)->format('d/m/Y') }}</h4>
<h6>Tipo de puesto</h6>
<div class="mb-4 d-flex flex-wrap gap-2">
    <!-- Button groups with radio button -->
    <div class="" role="group" aria-label="Basic radio toggle button group">

        @foreach($tipos as $t)
            <input type="radio" class="btn-check btn_tipo_puesto" name="id_tipo_puesto" id="btntipo{{ $t->id_tipo_puesto }}" data-slots="{{ $t->slots_reserva }}" value="{{ $t->id_tipo_puesto }}" autocomplete="off">
            <label class="btn btn-outline-primary" for="btntipo{{ $t->id_tipo_puesto }}">{{ $t->des_tipo_puesto }}</label>
        @endforeach
    </div>
    <!-- END : Button groups with radio button -->
</div>


<h6 id="tit_planta">Planta</h6>


@foreach($edificios_usuario as $e)
    @php
        $misplantas=$plantas_usuario->where('id_edificio',$e->id_edificio)->unique();
    @endphp
    <div class="card div_plantas" id="div_plantas{{ $e->id_edificio }}" style="display: none">
        <div class="card-header" id="heading{{ $e->id_edificio }}">
            <h5 class="mb-0">
                <button class="btn btn-link" data-toggle="collapse" data-target="#collapse{{ $e->id_edificio }}" aria-expanded="true" aria-controls="collapse{{ $e->id_edificio }}">
                    {{ $e->des_edificio }}
                </button>
            </h5>
        </div>
       
        <div id="collapse{{ $e->id_edificio }}" class="" aria-labelledby="heading{{ $e->id_edificio }}" data-parent="#accordion">
            <div class="card-body">
                <div class="" role="group" aria-label="Basic radio toggle button group">
                    @foreach($misplantas as $p)
                        <input type="radio" class="btn-check btn_planta" name="id_planta" id="btnplanta{{ $p->id_planta }}" data-edificio="{{ $e->id_edificio }}" data-planta="{{ $p->id_planta }}" autocomplete="off" value="{{ $p->id_planta }}">
                        <label class="btn btn-outline-primary lbl_planta" for="btnplanta{{ $p->id_planta }}">{{ $p->des_planta }}</label>
                    @endforeach
                </div>
                
            </div>
        </div>
    </div>
@endforeach




