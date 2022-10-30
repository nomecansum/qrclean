<script src="{{ asset('/js/cron/later.min.js')}}" defer></script>
<script>
    var cronSched = later.parse.cron({{ $expresion }});
    siguientes=later.schedule(cronSched).next({{ $veces }});
    document.write(JSON.stringify(siguientes));
</script>