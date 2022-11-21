{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('content_header')
    <h1>{{ $__menu }} Status Kelengkapan Materi</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header">
                	<h3>{{ $title }}</h3>
				    <a class="btn btn-default fill-right" href="{{ URL($__route.'/status-silabus') }}">
				    	<i class="fas fa-arrow-left"></i> Kembali
				    </a>
                </div>
                <div class="box-body">
                	<table class="table table-border">
                		<thead>
                			<tr>
                				<th width="60px">Sesi Ke</th>
                				<th>Text Book</th>
                				<th>Topik</th>
                				<th>Sub Topic</th>
                				<th>Media Pembelajaran</th>
                				<th>Materi Pembelajaran</th>
                				<th>Capaian Pembelajaran</th>
                                <th>Peta Kompetensi</th> 
                                <th>Rubrik Penilaian</th> 
                                <th>PPT</th> 
                                <th>LN</th> 
                                <th>Video</th> 
                                <th>Materi Pendukung</th> 
                                <th>Exercise</th> 
                                <th>Kuis</th>
                			</tr>
                		</thead>
                		<tbody>
                			@if(count($report) < 1 )
                				<tr>
                					<td colspan="15" class="text-center">
                						Tidak ada data
                					</td>
                				</tr>
                			@endif
                			@foreach($report as $key => $v)
                				<tr>
                					<td class="text-center">{{ $v['sesi'] }}</td>
                					<td>{{ $v['text_book'] }}</td>
                					<td>{{ $v['topic'] }}</td>
                					<td>{!! checklistIcon($v['sub_topic']) !!}</td>
                					<td>{!! checklistIcon($v['media_keterangan']) !!}</td>
                					<td>{!! checklistIcon($v['media_pembelajaran']) !!}</td>
                                    <td>{!! checklistIcon($v['cp']) !!}</td>
                                    <td>{!! checklistIcon($v['peta_kompetensi']) !!}</td>
                                    <td>{!! checklistIcon($v['rubrik_penilaian']) !!}</td>
                                    <td>{!! checklistIcon($v['orFilePPT']) !!}</td>
                                    <td>{!! checklistIcon($v['orFileLN']) !!}</td>
                                    <td>{!! checklistIcon($v['orFileVideo']) !!}</td>
                                    <td>{!! checklistIcon($v['orFileMateriPendukung']) !!}</td>
                                    <td>{!! checklistIcon($v['exercise']) !!}</td>
                                    <td>{!! checklistIcon($v['kuis']) !!}</td>
                				</tr>
                			@endforeach
                		</tbody>
                	</table>
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
@endpush