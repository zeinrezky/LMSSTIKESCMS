@include("rps.partials.text_book")

    <div id="summary_ppt"></div>
    <div id="summary_ln"></div>
    <div id="summary_video"></div>
    <div id="summary_materi_pendukung"></div>
    <div id="tabel_or_detail"></div>

@push("js")

    <script>
        var topicArr = @json($topic);

        var topicIndex = [];

        $.each( topicArr, function( key, value ) {
            topicIndex[value.id_topic] = value.topic;
        });

        function summary(){
            openTab('summary', false);

            // question
            const url = "{!! URL::to('/or/detail/'.$model['id_pm'].'/summary') !!}";
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
                $('#tabel_or_detail').html(data.tabel_or_detail);
            })
            .catch(function(error) {
                console.log(error);
            });

            // PPT
            
            $.ajax({
                url: "/parse-str",
                data: $("#ppt_tbody :input").serialize(),
                success: function(res){

                    console.log(topicIndex, res);
                    var html = "";
                    html += '<div class="row">';
                        html += '<h3>PPT</h3>';
                        html += '<hr>';
                        html += '<div class="col-md-8">';
                            html += '<table class="table">';
                                html += '<tr>';
                                    html += '<th>Topic</th>';
                                    html += '<th>File</th>';
                                html += '</tr>';

                                    $.each( res.old_ppt_topic, function( key, value ) {

                                        html += '<tr>';
                                            html += '<td>'+topicIndex[value]+'</td>';
                                            html += '<td><i class="fa fa-file"></i> View</td>';
                                        html += '</tr>';
                                    });

                                    $.each( res.ppt, function( key, value ) {

                                        html += '<tr>';
                                            html += '<td>'+topicIndex[value.topic_id]+'</td>';
                                            html += '<td><i class="fa fa-file"></i> View</td>';
                                        html += '</tr>';
                                    });

                            html += '</table>';
                        html += '</div>';
                    html += '</div>';
                    $("#summary_ppt").html(html);
                },
            });

            // LN
            
            $.ajax({
                url: "/parse-str",
                data: $("#ln_tbody :input").serialize(),
                success: function(res){

                    // console.log(res);
                    var html = "";
                    html += '<div class="row">';
                        html += '<h3>LN</h3>';
                        html += '<hr>';
                        html += '<div class="col-md-8">';
                            html += '<table class="table">';
                                html += '<tr>';
                                    html += '<th>Topic</th>';
                                    html += '<th>File</th>';
                                html += '</tr>';

                                    $.each( res.old_ln_topic, function( key, value ) {

                                        html += '<tr>';
                                            html += '<td>'+topicIndex[value]+'</td>';
                                            html += '<td><i class="fa fa-file"></i> View</td>';
                                        html += '</tr>';
                                    });

                                    $.each( res.ln, function( key, value ) {

                                        html += '<tr>';
                                            html += '<td>'+topicIndex[value.topic_id]+'</td>';
                                            html += '<td><i class="fa fa-file"></i> View</td>';
                                        html += '</tr>';
                                    });

                            html += '</table>';
                        html += '</div>';
                    html += '</div>';
                    $("#summary_ln").html(html);
                },
            });

            // Video
            
            $.ajax({
                url: "/parse-str",
                data: $("#vid_tbody :input").serialize(),
                success: function(res){

                    // console.log(res);
                    var html = "";
                    html += '<div class="row">';
                        html += '<h3>Video</h3>';
                        html += '<hr>';
                        html += '<div class="col-md-8">';
                            html += '<table class="table">';
                                html += '<tr>';
                                    html += '<th>Topic</th>';
                                    html += '<th>File</th>';
                                html += '</tr>';

                                    $.each( res.old_video_topic, function( key, value ) {

                                        html += '<tr>';
                                            html += '<td>'+topicIndex[value]+'</td>';
                                            html += '<td><i class="fa fa-file"></i> View</td>';
                                        html += '</tr>';
                                    });

                                    $.each( res.video, function( key, value ) {

                                        html += '<tr>';
                                            html += '<td>'+topicIndex[value.topic_id]+'</td>';
                                            html += '<td><i class="fa fa-file"></i> View</td>';
                                        html += '</tr>';
                                    });

                            html += '</table>';
                        html += '</div>';
                    html += '</div>';
                    $("#summary_video").html(html);
                },
            });

            // Materi Pendukung
            
            $.ajax({
                url: "/parse-str",
                data: $("#or_materi_pendukung_tbody :input").serialize(),
                success: function(res){

                    // console.log(res);
                    var html = "";
                    html += '<div class="row">';
                        html += '<h3>Materi Pendukung</h3>';
                        html += '<hr>';
                        html += '<div class="col-md-8">';
                            html += '<table class="table">';
                                html += '<tr>';
                                    html += '<th>Topic</th>';
                                    html += '<th>Judul</th>';
                                    html += '<th>Link</th>';
                                    html += '<th>Sumber</th>';
                                    html += '<th>File</th>';
                                html += '</tr>';

                                    $.each( res.old_mp_data, function( key, value ) {

                                        html += '<tr>';
                                            html += '<td>'+topicIndex[value.topic]+'</td>';
                                            html += '<td>'+value.title+'</td>';
                                            html += '<td>'+value.link+'</td>';
                                            html += '<td>'+value.source+'</td>';
                                            html += '<td><i class="fa fa-file"></i> View</td>';
                                        html += '</tr>';
                                    });

                                    $.each( res.materi_pendukung, function( key, value ) {

                                        html += '<tr>';
                                            html += '<td>'+topicIndex[value.topic_id]+'</td>';
                                            html += '<td>'+value.title+'</td>';
                                            html += '<td>'+value.link+'</td>';
                                            html += '<td>'+value.source+'</td>';
                                            html += '<td><i class="fa fa-file"></i> View</td>';
                                        html += '</tr>';
                                    });

                            html += '</table>';
                        html += '</div>';
                    html += '</div>';
                    $("#summary_materi_pendukung").html(html);
                },
            });
        }

    </script>
@endpush