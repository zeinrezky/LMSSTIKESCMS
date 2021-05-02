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
                            {!! Form::label("nip") !!}
                            {!! Form::text("NIP",null,["class"=>"form-control"]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label("nama") !!}
                            {!! Form::text("nama",null,["class"=>"form-control"]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label("email") !!}
                            {!! Form::text("email",null,["class"=>"form-control"]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label("telepon") !!}
                            {!! Form::text("telepon",null,["class"=>"form-control"]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label("Jenis Kelamin") !!}
                            {!! Form::select("kelamin",["P" => "Perempuan","L" => "Laki-laki"],null,["class"=>"form-control"]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label("password") !!}
                            <input type="password" name="password" class="form-control" value="{{ $model->password_plain }}">
                        </div>
                        <div class="form-group">
                            {!! Form::label("verifikasi_password") !!}
                            <input type="password" name="verifikasi_password" class="form-control" value="{{ $model->password_plain }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label("alamat") !!}
                            {!! Form::textarea("alamat",null,["class"=>"form-control"]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label("foto") !!} <small>(jpg,png)</small>
                            @include("components.file",["name" => "foto"])
                        </div>
                        
                    </div>
                </div>
                <?php 
                /*

                <div class="form-group">
                    {!! Form::label("background") !!}
                    @include("admin.components.file",["name" => "background"])
                </div>
                */
                ?>
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary btn-sm">
                    {{ $titleAction == "Create" ? "Save" : "Save" }}
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
{!! JsValidator::formRequest('App\Http\Requests\DosenRequest', '#form'); !!}
@endpush
