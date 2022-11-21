{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('content_header')
    <h1>Entry Rencana Pembelajaran Semester (RPS)</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header">
                
                </div>
                <div class="box-body">
                    <table class="table table-bordered" id = "table">
                        <thead>
                            <tr>
                                <th width = "20%">Semester</th>
                                <th width = "10%">Kode Matkul</th>
                                <th width = "20%">Mata Kuliah</th>
                                <th width = "10%">Kategori</th>
                                <th width = "20%">Judul Buku</th>
                                <th width = "10%">Tahun Terbit</th>
                                <th width = "20%">Status</th>
                                <th width = "20%">Action</th>
                            </tr>
                        </thead>
                        <tbody id = "tbody">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')

<script>
$(function() {
    var table = $('#table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ $__route.'/data' }}",
        "createdRow": function( row, data, dataIndex){
            console.log(data)
            if( data.active == "0"){
                $(row).addClass('danger');
            }
        },
        ordering:true,
        columns: [
            { data: 'nama_semester', name: 'semester.nama_semester' },
            { data: 'mk_kode', name: 'matakuliah.mk_kode' },
            { data: 'mk_nama', name: 'matakuliah.mk_nama' },
            { data: 'kategori', name: 'text_book.kategori' },
            { data: 'title', name: 'text_book.title' },
            { data: 'tahun', name: 'text_book.tahun' },
            { data: 'status', name: 'status' ,searchable:false},
            { data: 'action', name: 'action' ,searchable:false}
        ]
    });
});
</script>
@endpush