{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('content_header')
<h1>{{ $__menu }}</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
                {{ $titleAction }}
            </div>
            {!! Form::model($model,["id" =>"form","method"=>"post","files"=>true]) !!}
            <div class="box-body">
                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label("Semester") !!}
                            <p>{{ $pengembangMateri->semester->nama_semester }}</p>
                        </div>
                        <div class="form-group">
                            {!! Form::label("Judul Buku") !!}
                            <p>{{ $model->title }}</p>
                        </div>
                        <div class="form-group">
                            {!! Form::label("Pengarang") !!}
                            <p>{{ $model->author }}</p>
                        </div>
                        <div class="form-group">
                            {!! Form::label("Tahun Terbit") !!}
                            <p>{{ $model->tahun }}</p>
                        </div>
                        <div class="form-group">
                            {!! Form::label("ISBN") !!}
                            <p>{{ $model->isbn }}</p>
                        </div>
                        <hr>

                        @if($model->reviewer_commen || $model->approv_commen)
                            <div class="box box-info">
                                <div class="box-body">
                                    @if($model->reviewer_commen)
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                {!! Form::label("Reviewer Notes") !!}
                                                <p>{{ $model->reviewer_commen }}</p>
                                            </div>
                                        </div>
                                    @endif
                                    @if($model->approv_commen)
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                {!! Form::label("Approver Notes") !!}
                                                <p>{{ $model->approv_commen }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($userStatus == 'reviewer')
                            @if($model->status == 0)
                                <div class="row">
                                    <div class="col-md-12">
                                        {!! Form::label("Reviewer Notes") !!}
                                        {!! Form::textarea("reviewer_commen",null,["class"=>"form-control","row"=>"5"]) !!}
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-12 text-right">
                                        <button type="submit" name="status" value="1" class="btn btn-success mr-2">
                                            Approve
                                        </button>
                                        <button type="submit" name="status" value="0" class="btn btn-danger">
                                            Reject
                                        </button>
                                    </div>
                                </div>
                            @endif

                        @elseif($userStatus == 'approv')
                            @if($model->status == 1)
                                <div class="row">
                                    <div class="col-md-12">
                                        {!! Form::label("Approver Notes") !!}
                                        {!! Form::textarea("approv_commen",null,["class"=>"form-control","row"=>"5"]) !!}
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-12 text-right">
                                        <button type="submit" name="status" value="1" class="btn btn-success mr-2">
                                            Approve
                                        </button>
                                        <button type="submit" name="status" value="0" class="btn btn-danger">
                                            Reject
                                        </button>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label("Status") !!}
                            <p>
                                @if($model->status == 0) 
                                    <span class="label label-primary">Waiting Approval</span> 
                                @else
                                    {!! statusCaption($model->status, true) !!} </p>
                                @endif
                        </div>
                        <div class="form-group">
                            {!! Form::label("Mata Kuliah") !!}
                            <p>{{ $pengembangMateri->matakuliah->mk_nama }}</p>
                        </div>
                        <div class="form-group">
                            {!! Form::label("Edisi") !!}
                            <p>{{ $model->edition }}</p>
                        </div>
                        <div class="form-group">
                            {!! Form::label("Penerbit") !!}
                            <p>{{ $model->publisher }}</p>
                        </div>
                        <div class="form-group">
                            {!! Form::label("Kategori") !!}
                            <p>{{ $model->kategori }}</p>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    {!! Form::label("Cover") !!}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <img width="400px" src="{{ Storage::url(contents_path().$model->gbr_cover) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@stop

@push("js")
{!! JsValidator::formRequest('App\Http\Requests\InputTextBookRequest', '#form'); !!}
@endpush
