


    <div class="panel">

        <div class="panel-heading">
            <div class="panel-control">
                <button class="btn btn-default" data-panel="dismiss" data-dismiss="panel"><i class="demo-psi-cross"></i></button>
            </div>
            <h3 class="panel-title">Editar planta</h3>
        </div>

        <div class="panel-body">

            @if ($errors->any())
                <ul class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <form method="POST" action="{{ route('plantas.plantas.update', $plantas->id_planta) }}" id="edit_plantas_form" name="edit_plantas_form" accept-charset="UTF-8" class="form-horizontal form-ajax"  enctype="multipart/form-data">
            {{ csrf_field() }}
            <input name="_method" type="hidden" value="PUT">
            @include ('plantas.form', [
                                        'plantas' => $plantas,
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
@include('layouts.scripts_panel')