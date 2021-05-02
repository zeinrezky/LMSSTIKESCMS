@include("rps.partials.text_book")

<div class="row">
    <h3>Attribute</h3>
    <hr>
    <div class="col-md-8">
        <table class= "table">
            <tbody>
                <tr>
                    <td width="200px"><strong>Peta Kompetensi</strong></td>
                    <td>
                        <span id="td_peta_kompetensi"></span>
                        @if(@$rps->peta_kompetensi)
                            <a href="{{ Storage::url(contents_path().'peta_kompetensi/'.$rps->peta_kompetensi) }}" target="_blank" class="btn btn-outline-danger mt-2 btn-sm">
                                View PDF
                            </a>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td><strong>Strategi Pembelajaran</strong></td>
                    <td>
                        <span id="td_strategi_pembelajaran"></span>
                    </td>
                </tr>
                <tr>
                    <td><strong>Rubik Penilaian</strong></td>
                    <td>
                        <span id="td_rubrik_penilaian"></span>
                        @if(@$rps->peta_kompetensi)
                            <a href="{{ Storage::url(contents_path().'rubrik_penilaian/'.$rps->rubrik_penilaian) }}" target="_blank" class="btn btn-outline-danger mt-2 btn-sm">
                                View PDF
                            </a>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td><strong>Deskripsi Mata Kuliahh</strong></td>
                    <td id = "td_deskripsi_mata_kuliah"></td>
                </tr>
                <tr>
                    <td><strong>Media Pembelajaran</strong></td>
                    <td id = "td_media_pembelajaran"></td>
                </tr>
            </tbody>
        </table>   
    </div>
    <div class="col-md-4">
        <div class="row">
            <div class="col-md-12">
                <strong>KOMPOSISI BOBOT NILAI</strong>
                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>Metode Penilaian</th>
                            <th width="50">Bobot</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(MPCategories() as $category_id => $category)
                            <tr>
                                <td><strong>{{ $category }}</strong></td>
                                <td class="text-right" id="summary_mp_composition_{{$category_id}}"></td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="text-right">
                                <strong>Total Bobot :</strong>
                            </td>
                            <td class="text-right">
                                <strong><span class="{{ 'summary_composition_TOTAL' }}">0</span>%</strong>
                            </td>
                        </tr>
                        <tr class="{{ 'composition_ALERT' }}" style="display: none;">
                            <td colspan="{{ (count(MPCategories())*2)+1 }}" class="text-right text-warning">
                                Total bobot harus 100%
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <hr>
            </div>
        </div>
        @foreach(MPCategories() as $category_id => $category)
            <div class="row">
                <div class="col-md-12">
                    <strong>Metode Penilaian {{$category}}</strong>
                    <table class="table table-bordered mt-3">
                        <thead>
                            <tr>
                                <th>Komponen</th>
                                <th width="50">Bobot</th>
                            </tr>
                        </thead>
                        <tbody id = "summary_mp_{{ $category_id }}_tbody">
                            
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="text-right">
                                    <strong>Total Bobot :</strong>
                                </td>
                                <td class="text-right">
                                    <strong><span class="{{ 'summary_'.$category_id.'_TOTAL' }}">0</span>%</strong>
                                </td>
                            </tr>
                            <tr class="{{ $category_id.'_ALERT' }}" style="display: none;">
                                <td colspan="{{ (count(MPCategories())*2)+1 }}" class="text-right text-warning">
                                    Total bobot harus 0% atau 100%
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endforeach

    </div>
</div>

<div class="row">
    <h3>Capaian Pembelajaran</h3>
    <hr>
    <div class="col-md-12">
        <table class = "table" id = "table_summary_pembelajaran">

        </table>
    </div>
</div>

<div class="row">
    <h3>Topik</h3>
    <hr>
    <div class="col-md-12">
        <table class = "table table-bordered" id = "table_summary_topic">
            <thead>
                <tr>
                    <th>Sesi</th>
                    <th>Topic</th>
                    <th>CP</th>
                    <th>Sub Topik</th>
                </tr>
            </thead>
            <tbody id = "tbody_summary_topic"></tbody>
        </table>
    </div>
</div>

@push("js")
<script>
    function summary(){
        openTab('summary', false);
        $("#td_strategi_pembelajaran").html($('[name="strategi_pembelajaran"]').val());
        $("#td_deskripsi_mata_kuliah").html($('[name="deskripsi_mata_kuliah"]').val());
        $("#td_metode_penilaian").html($('[name="metode_penilaian"]').val());
        $("#td_metode_penilaian_praktikum").html($('[name="metode_penilaian_praktikum"]').val());
        $("#td_media_pembelajaran").html($('[name="media_pembelajaran"]').val());
        $("#td_peta_kompetensi").html($('[name="peta_kompetensi"]').val().replace(/C:\\fakepath\\/i, ''));
        $("#td_rubrik_penilaian").html($('[name="rubrik_penilaian"]').val().replace(/C:\\fakepath\\/i, ''));

        var topics = "";
        topicLoop = 1;
        $("#cp_tbody :text").each(function(){
            topics += "<tr><td>"+$(this).val()+"</td></tr>";
            topicLoop++;
        });


        $("#table_summary_pembelajaran").html(topics);

        
        $("#table_summary_topic .remove_topik").remove();
        $("#table_summary_topic").find("input").each(function(){
            value = $(this).attr('name','not');
        });

        // Metode
        var metodePenilaianArr = @json(MPCategories());
        var metodePenilaian = @json($metodePenilaian)

        var disableBtn = false;

        $.each( metodePenilaianArr, function( keyMP, valueMP ) {
        
            if ($('.composition_'+keyMP).val() == 0) {
                $("."+keyMP+"_VAL").val(0);
                $('.summary_'+keyMP+'_TOTAL').html(0);
                $('.'+keyMP+'_TOTAL').val(0);
                $('.'+keyMP+'_ALERT').hide();
            }

            $.ajax({
                url: "/parse-str",
                data: $("#mp_"+keyMP+"_tbody :input").serialize(),
                success: function(res){

                    var metod = "";
                    $.each( res.mp.detail[keyMP], function( key, value ) {
                        metod += '<tr>';
                            metod += '<td>';
                                metod += metodePenilaian[keyMP][key].component;
                            metod += '</td>';
                            metod += '<td class="text-right">';
                                metod += (value || 0);
                            metod += '%</td>';
                        metod += '</tr>';
                    });
                    $("#summary_mp_"+keyMP+"_tbody").html(metod);

                },
            });

            $("#summary_mp_composition_"+keyMP).html(($("#mp_total-"+keyMP).val() || 0)+"%");

            if ($('#mp_check-'+keyMP).val() == 0 && $('#mp_total-'+keyMP).val() > 0) {
                disableBtn = true;
            }

        });

        if ($('#mp_check-COMPOSITION').val() == 0) {
            disableBtn = true;
        }

        $(".btn-submit").prop('disabled', disableBtn);

        // Topic
        
        $.ajax({
            url: "/parse-str",
            data: $("#topik_tbody :input").serialize(),
            success: function(res){

                // console.log(res);
                var topics = "";

                $.each( res.topic, function( key, value ) {
                    var rowspanVal = (value.sub_topik.length + 1);
                    topics += '<tr>';
                        topics += '<td rowspan="'+rowspanVal+'">';
                            if (value.sesi) {
                                topics += value.sesi;   
                            }
                        topics += '</td>';

                        topics += '<td rowspan="'+rowspanVal+'">';
                            if (value.topic) {
                                topics += value.topic;   
                            }
                        topics += '</td>';

                        topics += '<td rowspan="'+rowspanVal+'">';
                            if (value.capaian_pembelajaran) {
                                topics += value.capaian_pembelajaran;   
                            }
                        topics += '</td>';

                    topics += '</tr>';

                    $.each( value.sub_topik, function( k, v ) {
                        topics += '<tr>';
                            topics += '<td>';
                                topics += v;
                            topics += '</td>';
                        topics += '</tr>';
                    });
                });

                $("#tbody_summary_topic").html(topics);
            },
        });
        

    }
</script>
@endpush