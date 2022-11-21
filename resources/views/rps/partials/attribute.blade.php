<div class="row">
    <div class="col-md-12">
        <div class="col-md-6">
            <div class="form-group">
                <div class="form-group">
                    {!! Form::label("Peta Kompetensi") !!} <small>(PDF)</small>
                    {{-- @include("components.file",["name" => "peta_kompetensi"]) --}}
                    {!! Form::file('peta_kompetensi', ["class" => "form-control","id" => "peta_kompetensi"]) !!}

                    @if(@$rps->peta_kompetensi)
                        <a href="{{ Storage::url(contents_path().'peta_kompetensi/'.$rps->peta_kompetensi) }}" target="_blank" class="btn btn-outline-danger mt-2 btn-sm">
                            View Older PDF
                        </a>
                       <!--  <button type="button" class="btn btn-outline-danger mt-2 btn-sm" data-toggle="modal" data-target="#exampleModal">
                          View Older PDF
                        </button>

                        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                          <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <div class="modal-body">
                                <iframe src="URL('rps/view-pdf/peta_kompetensi/'.$rps->peta_kompetensi)" width="100%" height="400px"></iframe>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                              </div>
                            </div>
                          </div>
                        </div> -->
                    @endif
                </div>
                <div class="form-group">
                    {!! Form::label("Rubrik Penilaian") !!} <small>(PDF)</small>
                    {{-- @include("components.file",["name" => "rubik_penilaian"]) --}}
                    {!! Form::file('rubrik_penilaian', ["class" => "form-control","id" => "rubrik_penilaian"]) !!}
                    
                    @if(@$rps->peta_kompetensi)
                        <a href="{{ Storage::url(contents_path().'rubrik_penilaian/'.$rps->rubrik_penilaian) }}" target="_blank" class="btn btn-outline-danger mt-2 btn-sm">
                            View Older PDF
                        </a>
                    @endif
                </div>
                <div class="form-group">
                    {!! Form::label("Media Pembelajaran") !!}
                    {!! Form::text("media_pembelajaran",@$rps->media_pembelajaran,["class"=>"form-control"]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label("Strategi Pembelajaran") !!}
                    {!! Form::textarea("strategi_pembelajaran",@$rps->strategi_pembelajaran,["class"=>"form-control"]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label("Deskripsi Mata Kuliah") !!}
                    {!! Form::textarea("deskripsi_mata_kuliah",@$rps->deskripsi_mata_kuliah,["class"=>"form-control"]) !!}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-success">
                        <div class="box-header">
                            <h3>KOMPOSISI BOBOT NILAI</h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table">
                                        <tbody class="mp_composition_tbody">
                                            @php
                                                $longCol = ROUND(100 / (count(MPCategories()) + 1));
                                            @endphp

                                            <tr>
                                                @foreach(MPCategories() as $Ctotal_category_id => $Ctotal_category)
                                                    <td width="{{ $longCol }}%">
                                                        <h5>{{ $Ctotal_category }} (%)</h5>
                                                        <input 
                                                         onkeypress="if(event.which < 48 || event.which > 57 ) if(event.which != 8) if(event.keyCode != 9) return false;" 
                                                         type="number" 
                                                         class="form-control numberBox {{ 'composition_'.$Ctotal_category_id }} composition_VAL" 
                                                         data-category="composition" 
                                                         data-category_id="{{$Ctotal_category_id}}" 
                                                         name="mp[composition][{{$Ctotal_category_id}}]"
                                                         id="mp_total-{{$Ctotal_category_id}}"
                                                         value="{{ $metodePenilaianData['composition'][$Ctotal_category_id] ?? 0 }}"
                                                         >
                                                         <input type="hidden" value="0" class="mp_check" id="mp_check-{{$Ctotal_category_id}}">
                                                    </td>
                                                @endforeach
                                                <td width="{{ $longCol }}%">
                                                    <h5>Total Bobot (%)</h5>
                                                    <input type="hidden" value="0" class="mp_check" id="mp_check-COMPOSITION">
                                                    <input type="number" max="100" min="0" class="form-control {{ 'composition_TOTAL' }}" id="{{ 'composition_TOTAL' }}" value="0" readonly>
                                                </td>
                                            </tr>
                                            <tr class="{{ 'composition_ALERT' }}" style="display: none;">
                                                <td colspan="{{ (count(MPCategories())*2)+1 }}" class="text-right text-warning">
                                                    Total bobot harus 100%
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                @foreach(MPCategories() as $category_id => $category)
                    <div class="col-md-12">
                        <div class="box box-success">
                            <div class="box-header">
                                <h4>Bobot Penilaian {{$category}}</h4>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-border">
                                            <thead>
                                                <tr>
                                                    <th>Komponen</th>
                                                    <th colspan="2">Bobot</th>
                                                </tr>
                                            </thead>
                                            <tbody id="mp_{{ $category_id }}_tbody">
                                                @foreach($metodePenilaian[$category_id] as $mp => $c)
                                                    <tr>
                                                        <td>{{ $c['component'] }}</td>
                                                        <td width="100">
                                                            <input 
                                                             onkeypress="if(event.which < 48 || event.which > 57 ) if(event.which != 8) if(event.keyCode != 9) return false;" 
                                                             type="number" 
                                                             data-category="{{ $category_id }}" 
                                                             class="form-control numberBox {{ $category_id.'_VAL' }}" 
                                                             name="mp[detail][{{$category_id}}][{{$c['id']}}]"
                                                             value="{{ $metodePenilaianData['detail'][$category_id][$c['id']] ?? 0 }}"
                                                             >
                                                        </td>
                                                        <td width="10">
                                                            %
                                                        </td>
                                                    </tr>   
                                                @endforeach()
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td>
                                                        TOTAL
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control {{ $category_id.'_TOTAL' }}" name="" value="0" readonly>
                                                    </td>
                                                    <td width="10">
                                                        %
                                                    </td>
                                                </tr>
                                                <tr class="{{ $category_id.'_ALERT' }}" style="display: none;">
                                                    <td colspan="3" class="text-right text-warning">
                                                        Total bobot harus 0% atau 100%
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach()

            </div>
        </div>
    </div>
</div>

@push("js")
    <script>
        var metodePenilaianArr = @json(MPCategories());
        @if($metodePenilaianData)
            $.each( metodePenilaianArr, function( keyMP, valueMP ) {
                numberBoxTotal(keyMP);
            });
            numberBoxTotal('composition');
        @endif

        $( ".numberBox" ).on('change keyup keydown keypress', function() {
            var category = $(this).attr("data-category");
            var category_id = $(this).attr("data-category_id");

            if (category == 'composition') {
                $("."+category_id+"_VAL").prop('readonly',false);
            }
            
            var removeZero = $(this).val().replace(/^0+/, '');
            $(this).val(removeZero);

            if ($(this).val() > 100)
            {
                $(this).val(100);
            }
            else if ($(this).val() < 0 || $(this).val() == 0)
            {
                $(this).val(0);
                if (category == 'composition') {
                    $("."+category_id+"_VAL").prop('readonly',true);
                }
            }

            if (category) {
                numberBoxTotal(category);
            }
        });

        function numberBoxTotal(category) {
            var sum = 0;
            $('.'+category+'_VAL').each(function(){
                if (this.value) {
                    sum += parseFloat(this.value);
                }
            });
            $('.'+category+'_TOTAL').val(sum);
            $('.summary_'+category+'_TOTAL').html(sum);
            var composition_TOTAL = $('.composition_TOTAL').val();

            if (category === 'composition') {
                if (sum == 100) {
                    $('.'+category+'_ALERT').hide();
                    $('#mp_check-'+category).val(1);
                }else{
                    $('.'+category+'_ALERT').show();
                    $('#mp_check-'+category).val(0);
                }
            }else{
                if (sum == 0 || sum == 100) {
                    $('.'+category+'_ALERT').hide();
                    $('#mp_check-'+category).val(1);
                }else{
                    $('.'+category+'_ALERT').show();
                    $('#mp_check-'+category).val(0);
                }
            }

            if (composition_TOTAL == 100) {
                $('.composition_ALERT').hide();
                $('#mp_check-COMPOSITION').val(1);
            }else{
                $('.composition_ALERT').show();
                $('#mp_check-COMPOSITION').val(0);
            }

            $.each( metodePenilaianArr, function( keyMP, valueMP ) {
                // console.log($('#mp_total-'+keyMP).val(), keyMP);
                if ($('#mp_total-'+keyMP).val() == 0) {
                    $("."+keyMP+"_VAL").prop('readonly',true);
                }
            });
        }
        numberBoxTotal('composition');

    </script>
@endpush