{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('content_header')
    <h1>{{ $__menu }}</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header">
                    {!! Html::link($__route."/create","Add New",["class"=>"btn btn-primary btn-sm"]) !!}
                </div>
                <div class="box-body">
                    <table class="table table-bordered" id = "table">
                        <thead>
                            <tr>
                                <th width = "30%">Nama Semester Penugasan</th>
                                <th width = "30%">Dari</th>
                                <th width = "30%">Sampai</th>
                                <th width = "10%">Action</th>
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
    $.fn.dataTable.render.moment = function ( from, to, locale ) {
        // Argument shifting
        if ( arguments.length === 1 ) {
            locale = 'en';
            to = from;
            from = 'YYYY-MM-DD';
        }
        else if ( arguments.length === 2 ) {
            locale = 'en';
        }
     
        return function ( d, type, row ) {
            if (! d) {
                return type === 'sort' || type === 'type' ? 0 : d;
            }
     
            var m = window.moment( d, from, locale, true );
     
            // Order and type get a number value from Moment, everything else
            // sees the rendered value
            return m.format( type === 'sort' || type === 'type' ? 'x' : to );
        };
    };

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
        order: [[ 0, "asc" ]],
        columns: [
            { data: 'nama_semester', name: 'nama_semester' },
            { name: 'from', data: 'from', render: $.fn.dataTable.render.moment( 'DD-MMM-YYYY' ) },
            { name: 'to', data: 'to', render: $.fn.dataTable.render.moment( 'DD-MMM-YYYY' ) },
            { data: 'action', name: 'action' ,searchable:false}
        ]
    });
});
</script>
@endpush