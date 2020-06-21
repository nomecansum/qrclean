@php
    $edificio_ahora=0;
    $planta_ahora=0;
@endphp

@foreach ($edificios as $e)
<div class="panel">
    <div class="panel-heading bg-gray-dark">
        <div class="row">
            <div class="col-md-3">
                <span class="text-2x ml-2 mt-2 font-bold"><i class="fad fa-building"></i> {{ $e->des_edificio }}
                    <input type="checkbox" class="form-control chk_edificio magic-checkbox" name="lista_id[]" data-id="{{ $e->id_edificio }}" id="chke{{ $e->id_edificio }}" value="{{ $e->id_edificio }}">
                    <label class="custom-control-label" for="chke{{ $e->id_edificio }}"></label>
                </span>
            </div>
            <div class="col-md-7"></div>
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
                <div class="font-bold rounded mr-2 mb-2 align-middle pad-all pastilla" id="pastilla{{ $key }}" style="width: 200px; height: 46px; overflow: hidden; {{ in_array($key,$plantas_usuario)?'background-color: #02c59b': 'background-color: #eae3b8' }}">
                    <span class="h-100 align-middle">
                        <input type="checkbox" class="form-control chkpuesto magic-checkbox" name="lista_id[]" data-id="{{ $key }}" data-edificio="{{ $e->id_edificio }}" id="chkp{{ $key }}" value="{{ $key }}" {{ in_array($key,$plantas_usuario)?' checked ': '' }}>
                        <label class="custom-control-label"   for="chkp{{ $key }}"></label>
                        {{ $value }}</span>
                </div>
            @endforeach
        </div>
        
    </div>
</div>
@endforeach
<script>
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

    $('.chk_edificio').click(function(){
        estado=$(this).is(':checked');
        
        console.log(estado);
        $('[data-edificio='+$(this).data('id')+']').each(function(){
            $(this).attr('checked',estado);
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
        })
    })

</script>