
@extends('layout')

@section('content')
<div class="cls-content">
    <div class="cls-content-sm panel">
        <div class="panel-body">
            <div class="mar-ver pad-btm">
                <img src="{{url('/img/Mosaic_brand_300.png')}}" style="width:300px">
            </div>
            <h5>Indique su e-mail para restablecer la password</h5>
            <br>
            <br>
            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="form-group row">
                    <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('e-Mail Address') }}</label>

                    <div class="col-md-12">
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <br>
                <br>

                <div class="form-group row mb-0">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary" style="width: 100%">
                           Enviar e-mail
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection

