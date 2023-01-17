@extends('layout.main')
@section('content')

<body>
    @include('layout.navbar')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Halaman HOME</h1>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <div class="jumbotron jumbotron-fluid">
                    <div class="container">
                        <h1 class="display-4">Hallo, {{auth()->user()->username}}</h1>
                        <p class="lead">
                            Email : {{auth()->user()->email}}
                            <br>
                            Role : {{auth()->user()->role}}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card text-white bg-success">
                    <div class="card-header">Jumlah Data User :</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                Supervisor
                            </div>
                            <div class="col-md-4">
                                <span class="badge badge-light" style="float:right">
                                    {{$totalUser['Supervisor']}}
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                Admin
                            </div>
                            <div class="col-md-4">
                                <span class="badge badge-light" style="float:right">
                                    {{$totalUser['Admin']}}
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                Vendor
                            </div>
                            <div class="col-md-4">
                                <span class="badge badge-light" style="float:right">
                                    {{$totalUser['Vendor']}}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-white bg-info">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-8">
                                Jumlah Data File :
                            </div>
                            <div class="col-md-4">
                                <span class="badge badge-light" style="float:right">
                                    {{$totalFile}}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

@endsection