@extends('layout.main')
@section('content')
<style>
    .boxlogin {
        border-radius: 10px;
        box-shadow: 1px 1px 10px 1px;
        position: relative;
        width: 100%;
        height: 320px;
        background-color: white
    }

    body {
        background-color: #95afc0
    }
</style>

<body>
    <div class="container" style="margin-top: 200px">
        @if(session()->has('LoginError'))
        <div class="col-md-6 offset-md-3">
            <div class="alert alert-danger" role="alert">
                {{ session('LoginError') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
        @endif
        <div class="col-md-6 offset-md-3 boxlogin">
            <div class="col-md-12" style="padding-top: 20px">
                <h4>LOGIN - TEST Logicnesia</h4>
            </div>
            <hr>
            <form action="/" method="post">
                @csrf
                <div class="col-md-12">
                    <label for="username">Username</label>
                    <input type="text" id="username" class="form-control @error('username') is-invalid @enderror"
                        name="username" placeholder="Username" required>
                    @error('username')
                    <div class="invalid-feedback">
                        {{$message}}
                    </div>
                    @enderror
                </div>
                <div class="col-md-12">
                    <label for="password">Password</label>
                    <input type="password" id="password" class="form-control" name="password" placeholder="Password"
                        required>
                </div>
                <hr>
                <div class="col-md-12">
                    <button class="btn btn-primary" style="float: right" type="submit">
                        Login <i class="bi bi-box-arrow-in-right"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
@endsection