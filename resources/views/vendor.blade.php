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
                <h1>Vendor</h1>
            </div>
            <div class="col-md-5">
                @if (auth()->user()->role != 'Vendor')
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#tambahModal"
                    style="float: right; margin-top: 10px">
                    <i class="bi bi-plus-circle"></i> Tambah Data
                </button>
                @endif
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="table-responsive col-md-12">
                <table class="table">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">File Name</th>
                            @if (auth()->user()->role != 'Admin')
                            <th scope="col">Path</th>
                            @endif
                            <th scope="col">Good QTY</th>
                            @if (auth()->user()->role != 'Vendor')
                            <th scope="col">aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($DataVendor as $item)
                        <tr>
                            <th scope="row">{{ $DataVendor->currentPage() > 1 ? ($DataVendor->perPage() *
                                ($DataVendor->currentPage() - 1) + $loop->iteration) : $loop->iteration }}</th>
                            <td>{{ $item->filename }}</td>
                            @if (auth()->user()->role != 'Admin')
                            <td>
                                <a href="/download/{{$item->id}}" class="btn btn-warning">
                                    <i class="bi bi-download"></i> Download
                                </a>
                            </td>
                            @endif
                            <td>{{$item->goods_qty}}</td>
                            <td>
                                @if (auth()->user()->role != 'Vendor')
                                <button type="button" class="btn btn-sm btn-info" data-filename="{{$item->filename}}"
                                    data-goods_qty="{{$item->goods_qty}}" data-id="{{$item->id}}" data-toggle="modal"
                                    data-target="#editModal" id="">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" data-toggle="modal"
                                    data-id="{{$item->id}}" data-target="#deleteModal"
                                    data-filename="{{$item->filename}}">
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
                Total Data : {{$DataVendor->total()}}
            </div>
            <div class="col-md-12 mt-2">
                {{ $DataVendor->links() }}
            </div>
        </div>
    </div>
    @if (session('success') == 'Create')
    <script type="text/javascript">
        $( document ).ready(function() {
            alert("Berhasil Menambahkan File")
        });
    </script>
    @endif
    @if (session('success') == 'Delete')
    <script type="text/javascript">
        $( document ).ready(function() {
            alert("Berhasil Menghapus File")
        });
    </script>
    @endif
    @if (session('success') == 'Edit')
    <script type="text/javascript">
        $( document ).ready(function() {
            alert("Berhasil Mengedit File")
        });
    </script>
    @endif
</body>

<script>
    var CSRF_TOKEN = $("meta[name='csrf-token']").attr("content");
    $(document).ready(function(){
        //Delete
        $('#deleteModal').on('show.bs.modal', function(e) {
            let btn = $(e.relatedTarget)
            let modal = $(this)
            modal.find('#id').val(btn.data('id'))
            modal.find('.keterangan').html('Apakah Anda Yakin Ingin Menghapus Data dengan Filename : <b>'+btn.data('filename')+'</b> ?')
        })
        // ADD
        $('#fileAdd').change(function(e) {
            let files = $(this)[0].files;
            $('#filename').val(files[0].name)
        })
        $('#tambahModal').on('hidden.bs.modal', function(e) {
            location.reload()
        })
        $('#submitAdd').click(function(){
            let filename = $('#filename').val();
            let goodqty = $('#goodqty').val();
            if(goodqty == '' || goodqty == null){
                $('#goodqty').addClass('is-invalid');
                $('#notifQTY').html('Goods QTY is required');
                $('#notifQTY').css('display','block');
                return
            }
            let fileupload = $('#fileAdd')[0].files
            if(fileupload.length > 0){
                let fd = new FormData();

                // Append data 
                fd.append('file',fileupload[0]);
                fd.append('_token',CSRF_TOKEN);

                // AJAX request 
                $.ajax({
                url: "{{route('uploadFile')}}",
                method: 'post',
                data: fd,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response){
                    if(response.success == 1){ // Uploaded successfully
                        SaveFile(response.filepath,filename,goodqty,null,CSRF_TOKEN,`/AddFile`)
                    }else if(response.success == 2){ // File not uploaded
                        $('#fileAdd').addClass('is-invalid');
                        $('#notifFile').html(response.message);
                        $('#notifFile').css('display','block');
                    }else{
                        $('#fileAdd').addClass('is-invalid');
                        $('#notifFile').html(response.error);
                        $('#notifFile').css('display','block');
                    } 
                },
                error: function(response){
                    console.log("error : " + JSON.stringify(response) );
                }
                });
            }else{
                $('#fileAdd').addClass('is-invalid');
                $('#notifFile').html("Please select a file.");
                $('#notifFile').css('display','block');
            }
        })
        //EDIT
        $('#editModal').on('show.bs.modal', function(e) {
            let btn = $(e.relatedTarget)
            let modal = $(this)
            modal.find('#filename1').val(btn.data('filename'))
            modal.find('#goodqty1').val(btn.data('goods_qty'))
            modal.find('#id-edit').val(btn.data('id'))
        })
        $('#fileEdit').change(function(e) {
            let files = $(this)[0].files;
            $('#filename1').val(files[0].name)
        })
        $('#editModal').on('hidden.bs.modal', function(e) {
            location.reload()
        })
        $('#edit-modal').click(function(){
            let filename = $('#filename1').val();
            let goodqty = $('#goodqty1').val();
            let id = $('#id-edit').val();
            if(goodqty == '' || goodqty == null){
                $('#goodqty1').addClass('is-invalid');
                $('#notifQTY1').html('Goods QTY is required');
                $('#notifQTY1').css('display','block');
                return
            }
            let fileupload = $('#fileEdit')[0].files
            if(fileupload.length > 0){
                let fd = new FormData();

                // Append data 
                fd.append('file',fileupload[0]);
                fd.append('_token',CSRF_TOKEN);

                // AJAX request 
                $.ajax({
                url: "{{route('uploadFile')}}",
                method: 'post',
                data: fd,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response){
                    if(response.success == 1){ // Uploaded successfully
                        SaveFile(response.filepath,filename,goodqty,id,CSRF_TOKEN,`/EditFile`)
                    }else if(response.success == 2){ // File not uploaded
                        $('#fileEdit').addClass('is-invalid');
                        $('#notifFile1').html(response.message);
                        $('#notifFile1').css('display','block');
                    }else{
                        $('#fileEdit').addClass('is-invalid');
                        $('#notifFile1').html(response.error);
                        $('#notifFile1').css('display','block');
                    } 
                },
                error: function(response){
                    console.log("error : " + JSON.stringify(response) );
                }
                });
            }else{
                SaveFile(null,filename,goodqty,id,CSRF_TOKEN,`/EditFile`)
            }
        })
    })

    function SaveFile(path, filename,qty,id,token,url){
        $.ajax({
            url: url,
            type: "put",
            cache: false,
            data: {
                "filename": filename,
                "path": path,
                "qty": qty,
                "id": id,
                "_token": token
            },
            success:function(response){
                if(response.success == 1){
                    if(response.edit == 0){
                        $('#tambahModal').modal('hide');
                        alert('Data Berhasil Di Tambahkan')
                    }else{
                        $('#editModal').modal('hide');
                        alert('Data Berhasil Di Edit')
                    }
                }else{
                    $('#goodqty').addClass('is-invalid');
                    $('#notifQTY').html(response.error);
                    $('#notifQTY').css('display','block');
                }
            },
            error:function(error){
                console.log("error : " + JSON.stringify(error) );
            }

        });
    }
</script>

<!-- Modal -->
<div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="exampleModalLabel">Tambah File</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @csrf
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <label for="filename">Filename</label>
                        <input type="text" class="form-control" name="filename" id="filename" disabled>
                    </div>
                    <div class="col-md-12 mt-2">
                        <label for="file">Upload File</label>
                        <input type="file" class="form-control" name="fileupload" id="fileAdd">
                        <div class="invalid-feedback" id="notifFile"></div>
                    </div>
                    <div class="col-md-12 mt-2">
                        <label for="goodqty">Goods QTY</label>
                        <input type="number" class="form-control" name="goodsqty" id="goodqty">
                        <div class="invalid-feedback" id="notifQTY"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="bi bi-reply-fill"></i>
                    Close</button>
                <button type="button" id="submitAdd" class="btn btn-success"><i class="bi bi-save"></i>
                    Save</button>
            </div>
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
                        <label for="filename">Filename</label>
                        <input type="text" id="id-edit" hidden>
                        <input type="text" class="form-control" name="filename" id="filename1" disabled>
                    </div>
                    <div class="col-md-12 mt-2">
                        <label for="file">Upload File</label>
                        <input type="file" class="form-control" name="fileupload" id="fileEdit">
                        <div class="invalid-feedback" id="notifFile1"></div>
                    </div>
                    <div class="col-md-12 mt-2">
                        <label for="goodqty">Goods QTY</label>
                        <input type="number" class="form-control" name="goodsqty" id="goodqty1">
                        <div class="invalid-feedback" id="notifQTY1"></div>
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
                <form action="/DeleteFile" method="POST">
                    @csrf
                    <input id="id" name="id" hidden>
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Ya</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection