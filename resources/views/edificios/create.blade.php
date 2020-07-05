
    <div class="panel">

        <div class="panel-heading">
            <div class="panel-control">
                <button class="btn btn-default" data-panel="dismiss"><i class="demo-psi-cross"></i></button>
            </div>
            <h3 class="panel-title">Nuevo edificio</h3>
        </div>

        <div class="panel-body">
        
            @if ($errors->any())
                <ul class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <form method="POST" action="{{ route('edificios.edificios.store') }}" accept-charset="UTF-8" id="create_edificios_form" name="create_edificios_form" class="form-horizontal form-ajax">
            {{ csrf_field() }}
            @include ('edificios.form', [
                                        'edificios' => null,
                                      ])

                <div class="form-group">
                    <div class="col-md-offset-11 col-md-11">
                        <input class="btn btn-primary" type="submit" value="Guardar">
                    </div>
                </div>

            </form>

        </div>
    </div>

    <script>
        $('.form-ajax').submit(form_ajax_submit);
    </script>
@include('layouts.scripts_panel')

