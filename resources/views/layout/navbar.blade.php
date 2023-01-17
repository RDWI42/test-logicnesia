<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                <li class="nav-item {{ Request::is('home') ? 'active' : '' }}">
                    <a class="nav-link" href="/home">Home</a>
                </li>
                @if (auth()->user()->role != 'Vendor')
                <li class="nav-item {{ Request::is('userManagement') ? 'active' : '' }}">
                    <a class="nav-link" href="/userManagement">User Management</a>
                </li>
                @endif
                <li class="nav-item {{ Request::is('vendor') ? 'active' : '' }}">
                    <a class="nav-link" href="/vendor">Vendor</a>
                </li>
            </ul>

            <div class="form-inline my-2 my-lg-0">
                <div class="nav-link" style="color: white"><i class="bi bi-person-fill"></i>
                    {{auth()->user()->username}}</div>
                <form action="/logout" method="post">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger my-2 my-sm-0" type="submit">
                        Log Out <i class="bi bi-box-arrow-left"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
<br>