@php
    $edificio_ahora=0;
    $planta_ahora=0;
@endphp

@foreach ($edificios as $e)
<div class="panel">
    <div class="panel-heading bg-gray-dark">
        <div class="row">
            <div class="col-md-5">
                <span class="text-2x ml-2 mt-2 font-bold"><i class="fad fa-building"></i> {{ $e->des_edificio }}
                    <input type="checkbox" class="form-control chk_edificio magic-checkbox" name="lista_id[]" data-id="{{ $e->id_edificio }}" id="chke{{ $e->id_edificio }}" value="{{ $e->id_edificio }}">
                    <label class="custom-control-label" for="chke{{ $e->id_edificio }}"></label>
                </span>
            </div>
            <div class="col-md-5"></div>
            <div class="col-md-2 text-right">
                <h4>
                    <span class="mr-2"><i class="fad fa-layer-group"></i> {{ $e->plantas }}</span>
                    <span class="mr-2"><i class="fad fa-desktop-alt"></i> {{ $e->puestos }}</span>
                </h4>
            </div>
        </div>
    </div>
    <div class="panel-body">
        @php
            $plantas=$puestos->where('id_edificio',$e->id_edificio)->pluck('des_planta','id_planta')->sortby('des_planta');
        @endphp
        <div class="d-flex flex-wrap">
            @foreach($plantas as $key=>$value)
                <div class="font-bold rounded mr-2 mb-2 align-middle pad-all pastilla  add-tooltip" title="{{ $value }}" id="pastilla{{ $key }}" style="font-size: 12px; width: 200px; height: 46px; overflow: hidden;  {{ $check&&in_array($key,$plantas_usuario)?'background-color: #02c59b': 'background-color: #eae3b8' }}">
                    <span class="h-100 align-middle" >
                        <input type="checkbox" class="form-control chkpuesto magic-checkbox" name="lista_id[]" data-id="{{ $key }}" data-edificio="{{ $e->id_edificio }}" id="chkp{{ $key }}" value="{{ $key }}" {{ $check&&in_array($key,$plantas_usuario)?' checked ': '' }}>
                        <label class="custom-control-label"   for="chkp{{ $key }}"></label>
                        {{ substr($value,0,21) }} {{ strlen($value)>19?'...':'' }}</span>
                </div>
            @endforeach
        </div>
        
    </div>
</div>
@endforeach


    <script>
        var tooltip = $('.add-tooltip');
        if (tooltip.length)tooltip.tooltip();
        @if($check)
        //{{-- Esta logica solo se ejecuta cuando estamos en detalle de usuario, para los multiples la pongo en la pagina del listado de usuarios --}}
        $('.chkpuesto').click(function(){
            if($(this).is(':checked')){
                $.get("{{ url('users/addplanta/'.$id) }}/"+$(this).data('id'),function(data){
                    $('#pastilla'+data.id).css("background-color",'#02c59b');
                })
            } else {
                $.get("{{ url('users/delplanta/'.$id) }}/"+$(this).data('id'),function(data){
                    $('#pastilla'+data.id).css("background-color",'#eae3b8');
                })
            }
        })
        @endif

        $('.chk_edificio').click(function(){
            estado=$(this).is(':checked');
            
            console.log(estado);
            $('[data-edificio='+$(this).data('id')+']').each(function(){
                $(this).attr('checked',estado);
                @if($check)
                 //{{-- Esta logica solo se ejecuta cuando estamos en detalle de usuario, para los multiples la pongo en la pagina del listado de usuarios --}}
                    if(estado){
                        $.get("{{ url('users/addplanta/'.$id) }}/"+$(this).data('id'),function(data){
                            $('#pastilla'+data.id).css("background-color",'#02c59b');
                        })
                    }
                    else  {
                        $.get("{{ url('users/delplanta/'.$id) }}/"+$(this).data('id'),function(data){
                            $('#pastilla'+data.id).css("background-color",'#eae3b8');
                        })
                    } 
                @endif
            })
        })
    </script>