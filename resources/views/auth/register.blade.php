@extends('layouts.app')

@extends('layout')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" id="loginform" action="{{ route('register') }}">
                        @csrf
                        <div class="cls-content">
                            <div class="cls-content-lg panel">
                                <div class="panel-body">
                                    <div class="mar-ver pad-btm">
                                        <img src="{{url('/img/Mosaic_brand_300.png')}}" style="width:300px">
                                    </div>
                                    <div class="mar-ver pad-btm">
                                        <h1 class="h3">Create a New Account</h1>
                                    </div>
                                    <form action="pages-login.html">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" placeholder="Full name" name="name">
                                                </div>
                                                <div class="form-group">
                                                    <input type="text" class="form-control" placeholder="E-mail" name="email">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <input type="password" class="form-control" placeholder="Password" name="password">
                                                </div>
                                                <div class="form-group">
                                                    <input id="password-confirm"  type="password" class="form-control" placeholder="Repeat password" name="password_confirmation">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="checkbox pad-btm text-left">
                                            <input id="demo-form-checkbox" class="magic-checkbox" type="checkbox">
                                            <label for="demo-form-checkbox">I agree with the <a href="#" class="btn-link text-bold">Terms and Conditions</a></label>
                                        </div>
                                        <button class="btn btn-primary btn-lg btn-block" type="submit">Register</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
