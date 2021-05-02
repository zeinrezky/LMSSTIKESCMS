{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('content_header')
    <h1>{{ $__menu }} Status Materi</h1>
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
                                <th>Semester</th>
                                <th width = "10%">Kode Matkul</th>
                                <th>Mata Kuliah</th>
                                <th width = "8%">Action</th>
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
        ajax: "{{ '/report/kemajuan-perkembangan/data' }}",
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
            { data: 'action', name: 'action' ,searchable:false}
        ]
    });
});
</script>
@endpush