


@foreach($data as $index => $tipo)
    <div class="row rounded b-all mb-3" id="fila{{ $tipo->id_proceso }}">  
        @include('incidencias.tipos.fila_procesado_tipo', ['tipo' => $tipo, 'index'=>$index, 'momento'=>$momento])
        
    </div>
@endforeach

<script>
    $('#btn_nueva').click(function(){
        $.get("{{ url('/incidencias/tipos/add_procesado',$id) }}/{{ $momento }}",function(){
            $('#divacciones').load("{{ url('/incidencias/tipos/postprocesado',$id) }}/{{ $momento }}");
        });
        $
    });  

    $('.btn_borrar_accion').click(function(){
        $.get("{{ url('/incidencias/tipos/fila_postprocesado/delete') }}/"+$(this).data('id'),function(data){
            $('#fila'+data.id).remove();
        });
    });
</script>