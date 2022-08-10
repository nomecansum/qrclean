
    <div class="panel editor">

        <div class="panel-heading">
            <div class="panel-control">
                <button class="btn btn-default" data-panel="dismiss"><i class="demo-psi-cross"></i></button>
            </div>
            <h3 class="panel-title">Editar edificio</h3>
        </div>

        <div class="panel-body">

            @if ($errors->any())
                <ul class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <form method="POST" action="{{ route('edificios.edificios.update', $edificios->id_edificio) }}" id="edit_edificios_form" name="edit_edificios_form" accept-charset="UTF-8" class="form-horizontal form-ajax">
            {{ csrf_field() }}
            <input name="_method" type="hidden" value="PUT">
            @include ('edificios.form', [
                                        'edificios' => $edificios,
                                      ])

                <div class="form-group">
                    <div class="col-md-offset-2 col-md-10 text-right">
                        <input class="btn btn-primary" type="submit" value="Guardar">
                    </div>
                </div>
            </form>

        </div>
    </div>

    <script>
        $('.form-ajax').submit(form_ajax_submit);
        $('.demo-psi-cross').click(function(){
            $('.editor').hide();
        });
    </script>
    @include('layouts.scripts_panel')