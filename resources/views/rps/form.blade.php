{{-- resources/views/admin/dashboard.blade.php --}}

@extends('adminlte::page')

@section('content_header')
<h1>Entry Rencana Pembelajaran Semester (RPS)</h1>
@stop

@section('css')
  <link rel="stylesheet" href="{{ asset('vendor/bootstrap-tagsinput/dist/bootstrap-tagsinput.css')}}">
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header">
                {!! $titleAction !!}
            </div>
            {!! Form::model($model,["id" =>"form","method"=>"post","files"=>true]) !!}
            <div class="box-body">
                <div id="tabs">
                    <ul>
                        @if($review_stat)
                            <li style="display: none;"><a href="#text_book">Text Book</a></li>
                            <li style="display: none;"><a href="#attribute">Attribute</a></li>
                            <li style="display: none;"><a href="#cp">CP</a></li>
                            <li style="display: none;"><a href="#topik" onclick = "return getCp();">Topic</a></li>
                        @else
                            <li><a onclick="openTab('text_book', false)" id="text_book_btn" href="#text_book">Text Book</a></li>
                            <li><a onclick="openTab('attribute', false)" id="attribute_btn" href="#attribute">Attribute</a></li>
                            <li><a onclick="openTab('cp', false)" id="cp_btn" href="#cp">CP</a></li>
                            <li><a onclick="openTab('topik', false)" id="topik_btn" href="#topik" onclick = "return getCp();">Topic</a></li>
                        @endif()

                        <li><a id="summary_btn" onclick = "return summary();" href="#summary">Summary</a></li>
                    </ul>

                    <div id="text_book">
                        @include("rps.partials.text_book")
                    </div>
                    <div id="attribute">
                        @include("rps.partials.attribute")
                    </div>
                    <div id="cp">
                        @include("rps.partials.cp")
                    </div>
                    <div id="topik">
                        @include("rps.partials.topik")
                    </div>
                    
                    <div id="summary">
                        @include("rps.partials.summary")
                    </div>
                </div>
            </div>

            <div class="box-footer">
                @if(!$review_stat)

                    <div class="actionBtn" id="act_text_book">
                        <a href="{{ url($__route) }}" class="btn btn-default btn-sm">
                            Back
                        </a>
                        <button type="button" onclick="openTab('attribute')" class="btn btn-primary btn-sm">
                            Next
                        </button>
                    </div>

                    <div class="actionBtn" id="act_attribute">
                        <a href="javascript:void(0)" onclick="openTab('text_book')" class="btn btn-default btn-sm">
                            Back
                        </a>
                        <button type="button" onclick="openTab('cp')" class="btn btn-primary btn-sm">
                            Next
                        </button>
                    </div>

                    <div class="actionBtn" id="act_cp">
                        <a href="javascript:void(0)" onclick="openTab('attribute')" class="btn btn-default btn-sm">
                            Back
                        </a>
                        <button type="button" onclick="openTab('topik')" class="btn btn-primary btn-sm">
                            Next
                        </button>
                    </div>

                    <div class="actionBtn" id="act_topik">
                        <a href="javascript:void(0)" onclick="openTab('cp')" class="btn btn-default btn-sm">
                            Back
                        </a>
                        <button type="button" onclick="openTab('summary')" class="btn btn-primary btn-sm">
                            Next
                        </button>
                    </div>

                    <div class="actionBtn" id="act_summary">
                        <a href="javascript:void(0)" onclick="openTab('topik')" class="btn btn-default btn-sm">
                            Back
                        </a>
                        
                        @if(!isset($rps->status) || $rps->status == 4)
                            <button type="submit" name="save_draft" value="1" class="btn btn-success btn-sm">
                                Save to Draft
                            </button>
                        @endif
                        <button type="submit" name="save_draft" value="0" class="btn btn-primary btn-sm">
                            Submit
                        </button>
                    </div>
                @else
                    <div class="">
                        <div class="col-md-3">
                        </div>
                        <div class="col-md-6">
                            
                            @if($rps->reviewer_commen || $rps->approv_commen)
                                <div class="box box-info">
                                    <div class="box-body">
                                        @if($rps->reviewer_commen)
                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    {!! Form::label("Reviewer Notes") !!}
                                                    <p>{{ $rps->reviewer_commen }}</p>
                                                </div>
                                            </div>
                                        @endif
                                        @if($rps->approv_commen)
                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    {!! Form::label("Approver Notes") !!}
                                                    <p>{{ $rps->approv_commen }}</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            @if($userStatus == 'reviewer')
                                @if($rps->status == 0)
                                    <div class="row">
                                        <div class="col">
                                            {!! Form::label("Reviewer Notes") !!}
                                            {!! Form::textarea("reviewer_commen",$rps->reviewer_commen ?? '',["class"=>"form-control","row"=>"5"]) !!}
                                        </div>
                                    </div>
                                    <div class="row mt-3 mb-5">
                                        <div class="col text-right">
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
                                @if($rps->status == 1 || $thisRole == 63)
                                    <div class="row">
                                        <div class="col">
                                            {!! Form::label("Approver Notes") !!}
                                            {!! Form::textarea("approv_commen",$rps->approv_commen ?? '',["class"=>"form-control","row"=>"5"]) !!}
                                        </div>
                                    </div>
                                    <div class="row mt-3 mb-5">
                                        <div class="col text-right">
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
                    </div>
                @endif

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@stop

@push("js")
{!! JsValidator::formRequest('App\Http\Requests\InputTextBookRequest', '#form'); !!}

<script src="{{ asset('vendor/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js')}}"></script>
<script>
    $(function() {
        $( "#tabs" ).tabs();
    });

    var review_stat = '{{$review_stat}}';
    if (review_stat == 1) {
        setTimeout(function() {
            $("#summary_btn").trigger('click');
        }, 700);
    }

    $(".actionBtn").hide();
    $("#act_text_book").show();

    function openTab(tab, btn = true) {
        $(".actionBtn").hide();
        $("#act_"+tab).show();

        if (btn) {
            setTimeout(function() {
                $("#"+tab+"_btn").trigger('click');
            }, 100);
        }
    }

</script>
@endpush


