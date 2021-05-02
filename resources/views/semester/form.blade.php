{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('content_header')
<h1>{{ $__menu }}</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="box box-primary">
            <div class="box-header">
                {{ $titleAction }}
            </div>
            {!! Form::model($model,["id" =>"form","method"=>"post","files"=>true]) !!}
            <div class="box-body">
                <div class="form-group">
                    {!! Form::label("Nama Semester Penugasan") !!}
                    {!! Form::text("nama_semester",null,["class"=>"form-control"]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label("dari") !!} <small>(Bulan dan Tahun)</small>
                    {!! Form::select("dari_bulan",getMonthData(),$fromMonth,["class"=>"form-control"]) !!}
                    {!! Form::selectRange("dari_tahun",date("Y") - 10,date("Y") + 10,$fromYear,["class"=>"form-control","style" => "margin-top:10px;"]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label("sampai") !!}<small>(Bulan dan Tahun)</small>
                    {!! Form::select("sampai_bulan",getMonthData(),$toMonth,["class"=>"form-control"]) !!}
                    {!! Form::selectRange("sampai_tahun",date("Y") - 10,date("Y") + 10,$toYear,["class"=>"form-control","style" => "margin-top:10px;"]) !!}
                </div>
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary btn-sm">
                    {{ request()->segment(2) == "create" ? "Save" : "Save" }}
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
{!! JsValidator::formRequest('App\Http\Requests\SemesterRequest', '#form'); !!}
@endpush
