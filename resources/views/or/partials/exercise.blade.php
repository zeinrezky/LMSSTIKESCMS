
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
                    @if(!isset($or) || ((isset($or) && $or['status'] == 0) || (isset($or) && $or['status'] == 3)))
                        <th>Action</th>
                    @endif
                </tr>
            </thead>
            <tbody id="tabel_exercise">
            </tbody>
        </table>
        @if(!isset($or) || ((isset($or) && $or['status'] == 0) || (isset($or) && $or['status'] == 3)))
            <div class="col-md-12 text-center">
                <span onclick="ExerciseaddQ()" class="btn btn-success">+ Add Question</span>
            </div>
        @endif

        <div class="modal fade" id="exercise_questionSetModal" tabindex="-1" role="dialog" aria-labelledby="exercise_questionSetModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exercise_questionSetModalLabel">Add Question</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="row">
                    <div class="col-md-2">     
                        <div class="form-group">
                            {!! Form::label("Set Number") !!}
                            <div id="exercise_variant"></div>
                        </div>
                    </div>
                    <div class="col-md-2">     
                        <div class="form-group">
                            {!! Form::label("Duration (menit)") !!}
                            {!! Form::number(null,null,["class"=>"form-control","id"=>"exercise_durasi"]) !!}
                        </div>
                    </div>
                    <div class="col-md-8">     
                        <div class="form-group">
                            {!! Form::label("Session & Topic") !!}
                            <div id="ExercisetopicSelect"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">  
                        {!! Form::label("Question") !!} 

                            <input type="hidden" id="exercise_question_id">
                        {!! Form::textarea(null,null,["class"=>"form-control","id"=>"exercise_isi_soal"]) !!}
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">  
                        {!! Form::label("Answer A") !!} 
                        {!! Form::textarea(null,null,["class"=>"form-control","id"=>"exercise_pilihan_a"]) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">  
                        {!! Form::label("Answer B") !!} 
                        {!! Form::textarea(null,null,["class"=>"form-control","id"=>"exercise_pilihan_b"]) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">  
                        {!! Form::label("Answer C") !!} 
                        {!! Form::textarea(null,null,["class"=>"form-control","id"=>"exercise_pilihan_c"]) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">  
                        {!! Form::label("Answer D") !!} 
                        {!! Form::textarea(null,null,["class"=>"form-control","id"=>"exercise_pilihan_d"]) !!}
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-4"> 
                        {!! Form::label("Correct Answer") !!}
                        <select class="form-control" id="exercise_jawaban">
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
                        {!! Form::textarea(null,null,["class"=>"form-control","id"=>"exercise_penjelasan_jwb"]) !!}
                    </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button id="exercise_footer_create" type="button" class="btn btn-primary" onclick="ExercisesaveQuestion()" >Create</button>
                <button id="exercise_footer_save" type="button" class="btn btn-success" onclick="ExerciseupdateQuestion()" >Save</button>
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
        ExerciseGetQuestion();
    });
    // Get Question

    var topicArr = @json($topic);
    
    var selects = `<select style="width:100%" 
    class = 'form-control select2' id = 'exercise_id_topic_question'>`;
    
    $.each( topicArr, function( key, value ) {
        selects += "<option value = '"+value.id_topic+"'>"+value.topic+"</option>";
    });

    selects += "</select>";

    $("#ExercisetopicSelect").html(selects);

    var exercise_data = [];

    function ExerciseGetQuestion() {

        const url = "{!! URL::to('/or/detail/'.$model['id_pm'].'/question-exercise') !!}";
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
            $('#tabel_exercise').html(data.html_tabel_exercise);
            $('#exercise_variant').html(data.html_varian_exercise);
            exercise_data = data.exercise_data;
        })
        .catch(function(error) {
            console.log(error);
        });
    }

    function ExercisesaveQuestion() {

        const url = "{!! URL::to('/or/detail/'.$model['id_pm'].'/question-exercise') !!}";
        let data  = new Object();

        data = {
            varian_latihan:$('#exercise_varian_latihan').val(),
            durasi:$('#exercise_durasi').val(),
            isi_soal:$('#exercise_isi_soal').val(),
            pilihan_a:$('#exercise_pilihan_a').val(),
            pilihan_b:$('#exercise_pilihan_b').val(),
            pilihan_c:$('#exercise_pilihan_c').val(),
            pilihan_d:$('#exercise_pilihan_d').val(),
            penjelasan_jwb:$('#exercise_penjelasan_jwb').val(),
            jawaban:$('#exercise_jawaban').val(),
            id_topic:$('#exercise_id_topic_question').val(),
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
            ExerciseGetQuestion();
            ExerciseresetInput();
            $('#exercise_questionSetModal').modal('hide');
        })
        .catch(function(error) {
            console.log(error);
        });
    }

    function ExerciseDelQ(id) {

        const url = "{!! URL::to('/or/detail/'.$model['id_pm'].'/question-exercise') !!}";
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
            ExerciseGetQuestion();
        })
        .catch(function(error) {
            console.log(error);
        });
    }

    function ExerciseEditQ(id) {
        var edata = exercise_data[id];
        console.log(edata);
        // varian_latihan:$('#exercise_varian_latihan').val(),
        $('#exercise_durasi').val(edata.durasi);
        
        $('#exercise_question_id').val(id);
        $('#exercise_isi_soal').summernote("code",edata.isi_soal);
        $('#exercise_pilihan_a').summernote("code",edata.pilihan_a);
        $('#exercise_pilihan_b').summernote("code",edata.pilihan_b);
        $('#exercise_pilihan_c').summernote("code",edata.pilihan_c);
        $('#exercise_pilihan_d').summernote("code",edata.pilihan_d);
        $('#exercise_penjelasan_jwb').summernote("code",edata.penjelasan_jwb);

        $('#exercise_jawaban').val(edata.jawaban);
        $('#exercise_id_topic_question').val(edata.id_topic).trigger('change');
        $('#exercise_varian_latihan').val(edata.varian_latihan).trigger('change');

        $('#exercise_questionSetModal').modal('show');

        $('#exercise_questionSetModalLabel').html('Edit Question');
        $('#exercise_footer_create').hide();
        $('#exercise_footer_save').show();
    }

    function ExerciseaddQ() {
        ExerciseresetInput();
        $('#exercise_questionSetModalLabel').html('Add Question');
        $('#exercise_footer_create').show();
        $('#exercise_footer_save').hide();
        $('#exercise_questionSetModal').modal('show');
    }

    function ExerciseupdateQuestion() {
        
        const url = "{!! URL::to('/or/detail/'.$model['id_pm'].'/question-exercise') !!}";
        let data  = new Object();

        data = {
            varian_latihan:$('#exercise_varian_latihan').val(),
            question_id:$('#exercise_question_id').val(),
            durasi:$('#exercise_durasi').val(),
            isi_soal:$('#exercise_isi_soal').val(),
            pilihan_a:$('#exercise_pilihan_a').val(),
            pilihan_b:$('#exercise_pilihan_b').val(),
            pilihan_c:$('#exercise_pilihan_c').val(),
            pilihan_d:$('#exercise_pilihan_d').val(),
            penjelasan_jwb:$('#exercise_penjelasan_jwb').val(),
            jawaban:$('#exercise_jawaban').val(),
            id_topic:$('#exercise_id_topic_question').val()
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
            ExerciseGetQuestion();
            $('#exercise_questionSetModal').modal('hide');
        })
        .catch(function(error) {
            console.log(error);
        });
        ExerciseresetInput();
    }

    function ExerciseresetInput() {
        $('#exercise_question_id').val("");
        $('#exercise_durasi').val("");
        $('#exercise_jawaban').val("A");
        $('#exercise_isi_soal').summernote("code","");
        $('#exercise_pilihan_a').summernote("code","");
        $('#exercise_pilihan_b').summernote("code","");
        $('#exercise_pilihan_c').summernote("code","");
        $('#exercise_pilihan_d').summernote("code","");
        $('#exercise_penjelasan_jwb').summernote("code","");
    }

    $('#exercise_isi_soal').summernote({ minHeight: 150, dialogsInBody: true });
    $('#exercise_pilihan_a').summernote({ minHeight: 150, dialogsInBody: true });
    $('#exercise_pilihan_b').summernote({ minHeight: 150, dialogsInBody: true });
    $('#exercise_pilihan_c').summernote({ minHeight: 150, dialogsInBody: true });
    $('#exercise_pilihan_d').summernote({ minHeight: 150, dialogsInBody: true });
    $('#exercise_penjelasan_jwb').summernote({ minHeight: 150, dialogsInBody: true });

</script>
@endpush
