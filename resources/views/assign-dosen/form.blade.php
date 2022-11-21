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
                            {!! Form::select("id_semester",$semesterListsBox,null,["class"=>"form-control select2"]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label("Mata Kuliah") !!}
                            {!! Form::select("id_matakuliah",$matakuliahListsBox,null,["class"=>"form-control select2"]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label("SME") !!}
                            {!! Form::select("sme_id",$dosenListsBox,@$model->pm_assign->sme_id,["class"=>"check-unique form-control select2"]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label("Reviewer") !!}
                            {!! Form::select("reviewer_id",$dosenListsBox,@$model->pm_assign->reviewer_id,["class"=>"check-unique form-control select2"]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label("Approver") !!}
                            {!! Form::select("approval_id",$dosenListsBox,@$model->pm_assign->approval_id,["class"=>"check-unique form-control select2"]) !!}
                        </div>
                    </div>
                    
                </div>
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary btn-sm btn-submit">
                    {{ $titleAction == "Tambah Data" ? "Save" : "Save" }}
                </button>

                <a href="{{ url($__route) }}" class="btn btn-default btn-sm">
                    Back
                </a>
                <br>
                <small id="alert-ad" class="text-danger">*SME , Reviewer, dan Approver tidak boleh sama</small>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@stop

@push("js")
{!! JsValidator::formRequest('App\Http\Requests\AssignDosenRequest', '#form'); !!}
<script type="text/javascript">

    $(".check-unique").on('change onclick', function() {
        check();
    })

    function check() {
        arr = [];
        $(".check-unique").each(function(){
            if ($(this).val() > 0) {
                arr.push($(this).val());
            }
        });

        if (unique(arr).length != 3) {
            $("#alert-ad").show();
            $(".btn-submit").prop('disabled', true);
        }else{
            $("#alert-ad").hide();
            $(".btn-submit").prop('disabled', false);
        }
    }

    check();

    function unique(array) {
        return $.grep(array, function(el, index) {
            return index === $.inArray(el, array);
        });
    }

</script>
@endpush
