<div class="row">
    <div class="col-md-12">
        <table class="table">
            <thead>
                <tr>
                    <th  width="200">Session & Topic</th>
                    <th width="200">Judul</th>
                    <th  width="200">Link</th>
                    <th width="200">Sumber</th>
                    <th width="100">File</th>
                    <th width="10"></th>
                </tr>
            </thead>
            <tbody id="or_materi_pendukung_tbody">
                @if (isset($orFile['or_materi_pendukung']))
                    @foreach ($orFile['or_materi_pendukung'] as $key => $v)
                        <tr id="Ror_materi_pendukung-{{ $key }}">
                            <td>
                                <input type="hidden" name="old_materi_pendukung[]" value="{{ $v['id'] }}">

                                <input type="hidden" name="old_mp_data[{{ $key }}][topic]"
                                    value="{{ $v['topic_id'] }}">
                                <input type="hidden" name="old_mp_data[{{ $key }}][file]"
                                    value="{{ $v['file'] }}">

                                <!--<select class='form-control' disabled style="width:220px">
                                    @foreach ($subtopic as $keyT => $t)
                                        <option value="{{ $t['id_topic'] }}"
                                            @if ($v->topic_id == $t['id_topic']) selected @endif>{{ $t['topic'] }}
                                        </option>
                                    @endforeach()
                                </select>-->
                                @foreach ($subtopic as $keyT => $t)
                                   
                                        @if ($v->topic_id == $t['id_topic']) <span>{{ $t['topic'] }}</span> @endif
                                @endforeach()
                            </td>
                            <td class="row-index">
                                <input type="hidden" name="old_mp_data[{{ $key }}][title]"
                                    class="form-control" readonly value="{{ $v['title'] }}">
                                    <span>{{ $v['title'] }}</span>
                            </td>
                            <td class="row-index">
                                <input type="hidden" name="old_mp_data[{{ $key }}][link]"
                                    class="form-control" readonly value="{{ $v['link'] }}">
                                <a href="{{ $v['link'] }}" class="text-primary"  target="_blank" style="color:#3c8dbc" >{{ $v['link'] }}</a>
                            </td>
                            <td class="row-index">
                                <input type="hidden" name="old_mp_data[{{ $key }}][source]"
                                    class="form-control" readonly value="{{ $v['source'] }}">
                                    <span>{{ $v['source'] }}</span>
                            </td>
                            <td class="text-center" width="15%">
                                <a href="{{ Storage::url(contents_path() . 'or_materi_pendukung/' . $v['file']) }}"
                                    target="_blank" class="btn btn-block btn-outline-warning mt-2 btn-sm">
                                    View Older File
                                </a>
                            </td>
                            <td class="text-center" width="10%">
                                <button type="button" class="btn btn-danger btn-sm remove_or_materi_pendukung"><i
                                        class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                    @endforeach()
                @endif()
            </tbody>
        </table>
        <div class="row">
            <div class="col text-center">
                <button type="button" class="btn btn-success btn-sm" id="button_or_materi_pendukung">
                    + Add Materi Pendukung
                </button>
            </div>
        </div>

        {!! alertMaxSize() !!}
    </div>
</div>

@push('js')
    <script>
        $(document).ready(function() {
            var rowIdx =
            "{{ isset($orFile['or_materi_pendukung']) ? count($orFile['or_materi_pendukung']) : 0 }}";
            var topicArr = @json($subtopic);

            $("#button_or_materi_pendukung").on("click", function() {

                var selects = `<select style="width:220px"
            class = 'form-control select2materi_pendukung-${rowIdx}' name = 'materi_pendukung[${rowIdx}][topic_id]'>`;

                $.each(topicArr, function(key, value) {
                    selects += "<option value = '" + value.id_topic + "'>" + value.topic +
                        "</option>";
                });

                selects += "</select>";

                $('#or_materi_pendukung_tbody').append(`<tr id="Ror_materi_pendukung-${rowIdx}">
                    <td class="row-index " >
                        ${selects}
                    </td>
                    <td class="row-index" >
                        <textarea class="form-control" name="materi_pendukung[${rowIdx}][title]"></textarea>
                    </td>
                    <td class="row-index" >
                        <textarea type="text" class="form-control" name="materi_pendukung[${rowIdx}][link]"></textarea>
                    </td>
                    <td class="row-index" >
                        <input type="text" class="form-control" name="materi_pendukung[${rowIdx}][source]">
                    </td>
                    <td class="text-center" width="15%">
                        <input type="file" class="form-control" name="materi_pendukung[${rowIdx}][file]" id="file-mp-${rowIdx}" required>
                    </td>
                    <td class="text-center" width="10%">
                        <button type = "button" class = "btn btn-danger btn-sm remove_or_materi_pendukung">X</button>
                    </td>
                </tr>`);

                $(`.select2materi_pendukung-${rowIdx}`).select2();
                rowIdx++;
            });

            $('#or_materi_pendukung_tbody').on('click', '.remove_or_materi_pendukung', function() {

                // Getting all the rows next to the
                // row containing the clicked button
                var child = $(this).closest('tr').nextAll();

                // Iterating across all the rows
                // obtained to change the index
                child.each(function() {

                    // Getting <tr> id.
                    var id = $(this).attr('id');

                    // Getting the <p> inside the .row-index class.
                    var idx = $(this).children('.row-index').children('input');

                    // Gets the row number from <tr> id.
                    var dig = parseInt(id.substring(1));

                    // Modifying row index.
                    idx.html(`Row ${dig - 1}`);

                    // Modifying row id.
                    $(this).attr('id', `R${dig - 1}`);
                });

                // Removing the current row.
                $(this).closest('tr').remove();

                // Decreasing the total number of rows by 1.
                rowIdx--;
            });

        });
    </script>
@endpush
