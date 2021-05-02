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
            {!! Form::model($model,["id" =>"form","method"=>"post"]) !!}
            <div class="box-body">
                <div class="form-group">
                    {!! Form::label("Komponen") !!}
                    {!! Form::text("component",null,["class"=>"form-control"]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label("Kategori") !!}
                    {!! Form::select("category",MPCategories(),$model->category,["class"=>"form-control"]) !!}
                </div>
                <!-- <div class="row">
                    <h4 class="text-center">Bobot (%)</h4>
                    <div class="col-md-6">
                        <div class="form-group">
                            {{-- Form::label("Teori") --}}
                            {{-- Form::number("weight",null,["class"=>"form-control"]) --}}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {{-- Form::label("Praktikum") --}}
                            {{-- Form::number("weight_praktikum",null,["class"=>"form-control"]) --}}
                        </div>
                    </div>
                </div> -->
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
{!! JsValidator::formRequest('App\Http\Requests\MetodePenilaianRequest', '#form'); !!}
@endpush
