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
                	<h3>{{ $title }}</h3>
                    <a class="btn btn-default fill-right" href="{{ URL($__route.'/kemajuan-perkembangan') }}">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="box-body">
                	<table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Dosen SME</th>
                                <th>Dosen Reviewer</th>
                                <th>Dosen Approver</th>
                                <th>Status Text Book</th>
                                <th>Status RPS</th>
                                <th>Status OR</th>
                            </tr>
                        </thead>   
                        <tbody>
                            @if(count($report) < 1 )
                                <tr>
                                    <td colspan="6" class="text-center">
                                        Tidak ada data
                                    </td>
                                </tr>
                            @endif
                            @foreach($report as $key => $v)
                                <tr>
                                    <td>
                                        <span data-toggle="tooltip" data-placement="top" title="id pm {{ $v['id_pm'] }}">
                                            {{ $v['sme'] }}
                                        </span>
                                    </td>
                                    <td>{{ $v['reviewer'] }}</td>
                                    <td>{{ $v['approver'] }}</td>
                                    <td>{!! statusProgressReport($v['textbook']) !!}</td>
                                    <td>{!! statusProgressReport($v['rps']) !!}</td>
                                    <td>{!! statusProgressReport($v['or']) !!}</td>
                                </tr>
                            @endforeach()
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
@endpush