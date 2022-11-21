
@section('css')
  <link rel="stylesheet" href="{{ asset('vendor/summernote/dist/summernote.css')}}">
@endsection
<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Set</th>
                    <th>No.</th>
                    <th>Soal</th>
                    @if(!$review_stat)
                        <th>Action</th>
                    @endif
                </tr>
            </thead>
            <tbody id="html_tabel_kuis">
            </tbody>
        </table>
        @if(!$review_stat)
            {{-- @if (!isset($or) || ((isset($or) && $or['status'] == 0) || (isset($or) && ($or['status'] == 3 || $or['status'] == 4)))) --}}
            <div class="col-md-12 text-center">
                <span onclick="addQ()" class="btn btn-success">+ Add Question</span>
            </div>
            {{-- @endif --}}
    @endif
        <div class="modal fade" id="questionSetModal" tabindex="-1" role="dialog" aria-labelledby="questionSetModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="questionSetModalLabel">Add Question</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="row">
                    <div class="col-md-2">     
                        <div class="form-group">
                            {!! Form::label("Set Number") !!}
                            <div id="html_varian_kuis"></div>
                        </div>
                    </div>
                    <div class="col-md-2">     
                        <div class="form-group">
                            {!! Form::label("Duration (menit)") !!}
                            {!! Form::number(null,null,["class"=>"form-control",'id'=>'durasi']) !!}
                        </div>
                    </div>
                    <div class="col-md-8">     
                        <div class="form-group">
                            {!! Form::label("Session & Topic") !!}
                            <div id="topicSelect"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">  
                        {!! Form::label("Question") !!} 

                            <input type="hidden" id="question_id">
                        {!! Form::textarea(null,null,["class"=>"form-control","id"=>"isi_soal"]) !!}
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">  
                        {!! Form::label("Answer A") !!} 
                        {!! Form::textarea(null,null,["class"=>"form-control","id"=>"pilihan_a"]) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">  
                        {!! Form::label("Answer B") !!} 
                        {!! Form::textarea(null,null,["class"=>"form-control","id"=>"pilihan_b"]) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">  
                        {!! Form::label("Answer C") !!} 
                        {!! Form::textarea(null,null,["class"=>"form-control","id"=>"pilihan_c"]) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">  
                        {!! Form::label("Answer D") !!} 
                        {!! Form::textarea(null,null,["class"=>"form-control","id"=>"pilihan_d"]) !!}
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-4"> 
                        {!! Form::label("Correct Answer") !!}
                        <select class="form-control" id="jawaban">
                            <option>A</option>
                            <option>B</option>
                            <option>C</option>
                            <option>D</option>
                        </select> 
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">  
                        {!! Form::label("Explanation") !!} 
                        {!! Form::textarea(null,null,["class"=>"form-control","id"=>"penjelasan_jwb"]) !!}
                    </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button id="footer_create" type="button" class="btn btn-primary" onclick="saveQuestion()" >Create</button>
                <button id="footer_save" type="button" class="btn btn-success" onclick="updateQuestion()" >Save</button>
              </div>
            </div>
          </div>
        </div>
    </div>
</div>

@push("js")
<script src="{{ asset('vendor/summernote/dist/summernote.min.js')}}"></script>
<script>
    $(document).ready(function(){
        getQuestion();
    });
    // Get Question

    var topicArr = @json($topic);
    
    var selects = `<select style="width:100%" 
    class = 'form-control select2' id = 'id_topic_question'>`;
    
    $.each( topicArr, function( key, value ) {
        selects += "<option value = '"+value.id_topic+"'>"+value.topic+"</option>";
    });

    selects += "</select>";

    $("#topicSelect").html(selects);

    var kuis_data = [];

    function getQuestion() {

        const url = "{!! URL::to('/or/detail/'.$model['id_pm'].'/question') !!}?review_stat={{$review_stat}}";
        let data  = new Object();

        var form    = new URLSearchParams(data);
        var request = new Request(url, {
            method: 'GET',
            headers: new Headers({
              'Content-Type' : 'application/x-www-form-urlencoded',
              'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
            })
        });

        fetch(request)
        .then(response => response.json())
        .then(function(data) {
            $('#html_tabel_kuis').html(data.html_tabel_kuis);
            $('#html_varian_kuis').html(data.html_varian_kuis);
            kuis_data = data.kuis_data;
        })
        .catch(function(error) {
            console.log(error);
        });
    }

    function saveQuestion() {

        const url = "{!! URL::to('/or/detail/'.$model['id_pm'].'/question') !!}";
        let data  = new Object();

        data = {
            varian_latihan:$('#varian_latihan').val(),
            durasi:$('#durasi').val(),
            isi_soal:$('#isi_soal').val(),
            pilihan_a:$('#pilihan_a').val(),
            pilihan_b:$('#pilihan_b').val(),
            pilihan_c:$('#pilihan_c').val(),
            pilihan_d:$('#pilihan_d').val(),
            penjelasan_jwb:$('#penjelasan_jwb').val(),
            jawaban:$('#jawaban').val(),
            id_topic:$('#id_topic_question').val(),
        };

        var form    = new URLSearchParams(data);
        var request = new Request(url, {
            method: 'POST',
            body: form,
            data,
            headers: new Headers({
              'Content-Type' : 'application/x-www-form-urlencoded',
              'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
            })
        });

        fetch(request)
        .then(response => response.json())
        .then(function(data) {
            getQuestion();
            resetInput();
            $('#questionSetModal').modal('hide');
        })
        .catch(function(error) {
            console.log(error);
        });
    }

    function delQ(id) {

        const url = "{!! URL::to('/or/detail/'.$model['id_pm'].'/question') !!}";
        let data  = new Object();

        data = {
            id
        };

        var form    = new URLSearchParams(data);
        var request = new Request(url, {
            method: 'DELETE',
            body: form,
            data,
            headers: new Headers({
              'Content-Type' : 'application/x-www-form-urlencoded',
              'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
            })
        });

        fetch(request)
        .then(response => response.json())
        .then(function(data) {
            getQuestion();
        })
        .catch(function(error) {
            console.log(error);
        });
    }

    function editQ(id) {
        var edata = kuis_data[id];
        console.log(edata);
        // varian_latihan:$('#varian_latihan').val(),
        $('#durasi').val(edata.durasi);
        
        $('#question_id').val(id);
        $('#isi_soal').summernote("code",edata.isi_soal);
        $('#pilihan_a').summernote("code",edata.pilihan_a);
        $('#pilihan_b').summernote("code",edata.pilihan_b);
        $('#pilihan_c').summernote("code",edata.pilihan_c);
        $('#pilihan_d').summernote("code",edata.pilihan_d);
        $('#penjelasan_jwb').summernote("code",edata.penjelasan_jwb);

        $('#jawaban').val(edata.jawaban);
        $('#id_topic_question').val(edata.id_topic).trigger('change');
        $('#varian_latihan').val(edata.varian_latihan).trigger('change');

        $('#questionSetModal').modal('show');

        $('#questionSetModalLabel').html('Edit Question');
        $('#footer_create').hide();
        $('#footer_save').show();
    }

    function addQ() {
        resetInput();
        $('#questionSetModalLabel').html('Add Question');
        $('#footer_create').show();
        $('#footer_save').hide();
        $('#questionSetModal').modal('show');
    }

    function updateQuestion() {
        
        const url = "{!! URL::to('/or/detail/'.$model['id_pm'].'/question') !!}";
        let data  = new Object();

        data = {
            varian_latihan:$('#varian_latihan').val(),
            question_id:$('#question_id').val(),
            durasi:$('#durasi').val(),
            isi_soal:$('#isi_soal').val(),
            pilihan_a:$('#pilihan_a').val(),
            pilihan_b:$('#pilihan_b').val(),
            pilihan_c:$('#pilihan_c').val(),
            pilihan_d:$('#pilihan_d').val(),
            penjelasan_jwb:$('#penjelasan_jwb').val(),
            jawaban:$('#jawaban').val(),
            id_topic:$('#id_topic_question').val()
        };

        var form    = new URLSearchParams(data);
        var request = new Request(url, {
            method: 'PATCH',
            body: form,
            data,
            headers: new Headers({
              'Content-Type' : 'application/x-www-form-urlencoded',
              'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
            })
        });

        fetch(request)
        .then(response => response.json())
        .then(function(data) {
            getQuestion();
            $('#questionSetModal').modal('hide');
        })
        .catch(function(error) {
            console.log(error);
        });
        resetInput();
    }

    function resetInput() {
        $('#question_id').val("");
        $('#durasi').val("");
        $('#jawaban').val("A");
        $('#isi_soal').summernote("code","");
        $('#pilihan_a').summernote("code","");
        $('#pilihan_b').summernote("code","");
        $('#pilihan_c').summernote("code","");
        $('#pilihan_d').summernote("code","");
        $('#penjelasan_jwb').summernote("code","");
    }

    $('#isi_soal').summernote({ minHeight: 150, dialogsInBody: true });
    $('#pilihan_a').summernote({ minHeight: 150, dialogsInBody: true });
    $('#pilihan_b').summernote({ minHeight: 150, dialogsInBody: true });
    $('#pilihan_c').summernote({ minHeight: 150, dialogsInBody: true });
    $('#pilihan_d').summernote({ minHeight: 150, dialogsInBody: true });
    $('#penjelasan_jwb').summernote({ minHeight: 150, dialogsInBody: true });
    
    $('.modal').css('overflow-y', 'auto');
</script>
@endpush
