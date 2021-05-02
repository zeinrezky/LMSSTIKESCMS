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
                            {!! Form::text("semester",$pengembangMateri->semester->nama_semester,["class"=>"form-control","readonly"=>true]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label("Judul Buku") !!}
                            {!! Form::text("title",null,["class"=>"form-control"]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label("Pengarang") !!}
                            {!! Form::text("author",null,["class"=>"form-control"]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label("Tahun Terbit") !!}
                            {!! Form::number("tahun",null,["class"=>"form-control","maxlength" => 4]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label("ISBN") !!}
                            {!! Form::text("isbn",null,["class"=>"form-control"]) !!}
                        </div>
                        <hr>

                        @if($model->reviewer_commen)
                            <div class="row mb-3">
                                <div class="col">
                                    {!! Form::label("Reviewer Notes") !!}
                                    {!! Form::textarea("reviewer_commen",null,["class"=>"form-control","row"=>"10",'readonly'=>true]) !!}
                                </div>
                            </div>
                        @endif

                        @if($model->approv_commen)
                            <div class="row mb-3">
                                <div class="col">
                                    {!! Form::label("Approver Notes") !!}
                                    {!! Form::textarea("approv_commen",null,["class"=>"form-control","row"=>"10",'readonly'=>true]) !!}
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label("Mata Kuliah") !!}
                            {!! Form::text("mata_kuliah",$pengembangMateri->matakuliah->mk_nama,["class"=>"form-control","readonly"=>true]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label("Edisi") !!}
                            {!! Form::text("edition",null,["class"=>"form-control"]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label("Penerbit") !!}
                            {!! Form::text("publisher",null,["class"=>"form-control"]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label("Kategori") !!}

                            {!! Form::select("kategori",['UTAMA'=>'UTAMA','PENDUKUNG'=>'PENDUKUNG'],null,["class"=>"form-control"]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label("Gambar Cover") !!} <small>(jpg,png)</small>
                            @include("components.file",["name" => "gbr_cover"])
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary btn-sm">
                    Save
                </button>

                <a href="{{ url($__route) }}" class="btn btn-default btn-sm">
                    Back
                </a>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@stop

@push("js")
{!! JsValidator::formRequest('App\Http\Requests\InputTextBookRequest', '#form'); !!}
@endpush
