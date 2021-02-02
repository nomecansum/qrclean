<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">Mis reservas</h3>
        <span class="float-right" id="spin" style="display: none"><img src="{{ url('/img/loading.gif') }}" style="height: 25px;">LOADING</span>
    </div>
    <div class="panel-body">
        <div id="calendario"></div>
    </div>
</div>

@section('scripts5')
    <script>
        function loadMonth(month = null,type = null)
        {
            $('#spinner').show();
            $.post('{{url('reservas/loadMonthSchedule')}}', {_token:'{{csrf_token()}}',month: month,type:type,emp:'{{Auth::user()->id}}'}, function(data, textStatus, xhr) {
                $('#calendario').html(data);
                $('.des_evento').css('font-size','0.6vw');
                $('.des_evento').css('font-weight','normal');
                $('.des_evento').css("overflow","hidden");
                $('.dia').css('height','50px')
                
                $('.changeMonth').click(function(event) {
                    loadMonth($(this).data('month'),$(this).data('action'));
                });
                $('#spinner').hide();
            });
        }

        $(function(){
            loadMonth();
        })

        $('#calendario').click(function(){
            window.location.replace("{{ url('/reservas') }}");
        })

        

    </script>
@endsection