
    <div class="panel">

        <div class="panel-heading">
            <h3 class="panel-title">Nueva planta</h3>
        </div>

        <div class="panel-body">
        
            @if ($errors->any())
                <ul class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <form method="POST" action="{{ route('plantas.plantas.store') }}" accept-charset="UTF-8" id="create_plantas_form" name="create_plantas_form" class="form-horizontal form-ajax">
            {{ csrf_field() }}
            @include ('plantas.form', [
                                        'plantas' => null,
                                      ])

                <div class="form-group">
                    <div class="col-md-offset-2 col-md-10">
                        <input class="btn btn-primary" type="submit" value="Guardar">
                    </div>
                </div>

            </form>

        </div>
    </div>


    <script>
        $('.form-ajax').submit(form_ajax_submit);
    </script>