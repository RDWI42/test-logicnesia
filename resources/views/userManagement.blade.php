@extends('layout.main')
@section('content')
<style>
    .success-toast {
        background-color: blue
    }
</style>

<body>
    @include('layout.navbar')
    <div class="container">
        <div class="row">
            <div class="col-md-7">
                <h1>User Management</h1>
            </div>
            <div class="col-md-5">
                @if (auth()->user()->role == 'Supervisor')
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#tambahModal"
                    style="float: right; margin-top: 10px">
                    <i class="bi bi-plus-circle"></i> Tambah Data
                </button>
                @endif
            </div>
        </div>
        <hr>
        {{ session('data')}}
        <div class="row">
            <div class="table-responsive col-md-12">
                <table class="table">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Username</th>
                            <th scope="col">Email</th>
                            <th scope="col">Role</th>
                            @if (auth()->user()->role == 'Supervisor')
                            <th scope="col">aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($DataUser as $item)
                        <tr>
                            <th scope="row">{{ $DataUser->currentPage() > 1 ? ($DataUser->perPage() *
                                ($DataUser->currentPage() - 1) + $loop->iteration) : $loop->iteration }}</th>
                            <td>{{ $item->username }}</td>
                            <td>{{$item->email}}</td>
                            <td>{{$item->role}}</td>
                            <td>
                                @if (auth()->user()->role == 'Supervisor')
                                <button type="button" class="btn btn-sm btn-info" data-username="{{$item->username}}"
                                    data-email="{{$item->email}}" data-role="{{$item->role}}" data-id="{{$item->id}}"
                                    data-toggle="modal" data-target="#editModal" id="">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" data-toggle="modal"
                                    data-id="{{$item->id}}" data-target="#deleteModal"
                                    data-username="{{$item->username}}">
                                    <i class="bi bi-trash3"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                Total Data : {{$DataUser->total()}}
            </div>
            <div class="col-md-12 mt-2">
                {{ $DataUser->links() }}
            </div>
        </div>
    </div>
    @if (session('status') == 'tambah')
    <script type="text/javascript">
        $( document ).ready(function() {
            $('#tambahModal').modal('show');
        });
    </script>
    @endif
    @if (session('success') == 'Create')
    <script type="text/javascript">
        $( document ).ready(function() {
            alert("Berhasil Menambahkan User")
        });
    </script>
    @endif
    @if (session('success') == 'Delete')
    <script type="text/javascript">
        $( document ).ready(function() {
            alert("Berhasil Menghapus User")
        });
    </script>
    @endif
    @if (session('success') == 'Edit')
    <script type="text/javascript">
        $( document ).ready(function() {
            alert("Berhasil Mengedit User")
        });
    </script>
    @endif
</body>

<script>
    $(document).ready(function(){
        $('#editModal').on('show.bs.modal', function(e) {
            let btn = $(e.relatedTarget)
            let modal = $(this)
            modal.find('#username').val(btn.data('username'))
            modal.find('#email').val(btn.data('email'))
            modal.find('#role').val(btn.data('role'))
            modal.find('#id-edit').val(btn.data('id'))
        })
        $('#editModal').on('hidden.bs.modal', function(e) {
            location.reload()
        })

        $('#deleteModal').on('show.bs.modal', function(e) {
            let btn = $(e.relatedTarget)
            let modal = $(this)
            modal.find('#id').val(btn.data('id'))
            modal.find('.keterangan').html('Apakah Anda Yakin Ingin Menghapus Data dengan Username : <b>'+btn.data('username')+'</b> ?')
        })
        $('#tambahModal').on('hidden.bs.modal', function(e) {
            location.reload()
        })

        $('#cekPass').click(function(){
            if($(this).is(':checked')){
                $('#changePass1').css('display','block')
                $('#changePass2').css('display','block')
            }else{
                $('#changePass1').css('display','none')
                $('#changePass2').css('display','none')
            }
        })


        // update
        //action update post
    $('#edit-modal').click(function(e) {
        e.preventDefault();

        //define variable
        let id = $('#id-edit').val();
        let username = $('#username').val();
        let email   = $('#email').val();
        let role = $('#role').val();
        let cekPass = $('#cekPass').is(':checked');
        let password1 = $('#password1').val();
        let password2 = $('#password2').val(); 
        let token   = $("meta[name='csrf-token']").attr("content");
        //ajax
        $.ajax({

            url: `/EditUser/${id}`,
            type: "put",
            cache: false,
            data: {
                "id": id,
                "username": username,
                "email": email,
                "role": role,
                "cekPass": cekPass,
                "password1": password1,
                "password2": password2,
                "_token": token
            },
            success:function(response){
                if(response.success){
                    $('#editModal').modal('hide');
                    alert('Data Berhasil Di update')
                }else{
                    $('#password1').addClass('is-invalid');
                    $('#notifpassword1').html(response.message);
                    $('#notifpassword1').css('display','block');
                }
            },
            error:function(error){
                if(error.responseJSON.hasOwnProperty('username')) {
                    $('#username').addClass('is-invalid');
                    $('#notifusername').html(error.responseJSON.username[0]);
                    $('#notifusername').css('display','block');
                } 
                if(error.responseJSON.hasOwnProperty('email')) {
                    $('#email').addClass('is-invalid');
                    $('#notifemail').html(error.responseJSON.email[0]);
                    $('#notifemail').css('display','block');
                }
                if(error.responseJSON.hasOwnProperty('password1')) {
                    $('#password1').addClass('is-invalid');
                    $('#notifpassword1').html(error.responseJSON.password1[0]);
                    $('#notifpassword1').css('display','block');
                }
                if(error.responseJSON.hasOwnProperty('password2')) {
                    $('#password2').addClass('is-invalid');
                    $('#notifpassword2').html(error.responseJSON.password2[0]);
                    $('#notifpassword2').css('display','block');
                }  
            }

        });
    });


    })
</script>

<!-- Modal -->
<div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="exampleModalLabel">Tambah User</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="/AddUser" method="post">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="username">Username</label>
                            <input type="text" id="id" hidden>
                            <input type="text" class="form-control @error('username') is-invalid  @enderror"
                                name="username">
                            @error('username')
                            <div class="invalid-feedback">
                                {{$message}}
                            </div>
                            @enderror
                        </div>
                        <div class="col-md-12 mt-2">
                            <label for="email">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email">
                            @error('email')
                            <div class="invalid-feedback">
                                {{$message}}
                            </div>
                            @enderror
                        </div>
                        <div class="col-md-12 mt-2">
                            <label for="role">Role</label>
                            <select name="role" id="role" class="form-control @error('role') is-invalid @enderror"
                                name="role">
                                <option value="Supervisor">Supervisor</option>
                                <option value="Admin">Admin</option>
                                <option value="Vendor">Vendor</option>
                            </select>
                            @error('role')
                            <div class="invalid-feedback">
                                {{$message}}
                            </div>
                            @enderror
                        </div>
                        <div class="col-md-12 mt-2">
                            <label for="password">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                name="password">
                            @error('password')
                            <div class="invalid-feedback">
                                {{$message}}
                            </div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="bi bi-reply-fill"></i>
                        Close</button>
                    <button type="submit" id="submitAdd" class="btn btn-success"><i class="bi bi-save"></i>
                        Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal edot-->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="exampleModalLabel">Edit User</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @csrf
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <label for="username">Username</label>
                        <input type="text" id="id-edit" hidden>
                        <input type="text" class="form-control" id="username">
                        <div class="invalid-feedback" id="notifusername"></div>
                    </div>

                    <div class="col-md-12 mt-2">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email">
                        <div class="invalid-feedback" id="notifemail"></div>
                    </div>

                    <div class="col-md-12 mt-2">
                        <label for="role">Role</label>
                        <select name="role" id="role" class="form-control">
                            <option value="Supervisor">Supervisor</option>
                            <option value="Admin">Admin</option>
                            <option value="Vendor">Vendor</option>
                        </select>
                        <div class="invalid-feedback" id="notifrole"></div>
                    </div>

                    <div class="col-md-12 mt-2">
                        <input type="checkbox" id="cekPass"><label for="cekPass"> Ganti Password</label>
                    </div>

                    <div class="col-md-12" style="display: none" id="changePass1">
                        <label for="password1">Password Lama</label>
                        <input type="password" class="form-control" id="password1">
                        <div class="invalid-feedback" id="notifpassword1"></div>
                    </div>

                    <div class="col-md-12 mt-2" style="display: none" id="changePass2">
                        <label for="password2">Password Baru</label>
                        <input type="password" class="form-control" id="password2">
                        <div class="invalid-feedback" id="notifpassword2"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="bi bi-reply-fill"></i>
                    Close</button>
                <button type="button" id="edit-modal" class="btn btn-success"><i class="bi bi-save"></i>
                    Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="exampleModalLabel">Delete Modal</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body keterangan"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">&times; Tidak</button>
                <form action="/DeleteUser" method="POST">
                    @csrf
                    <input id="id" name="id" hidden>
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Ya</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection