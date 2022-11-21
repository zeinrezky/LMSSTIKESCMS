<div class="row">
    <div class="col-md-12">
        <table class="table">
            <thead>
                <tr>
                    <th>Session & Topic</th>
                    <th>File</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id = "vid_tbody">
                @if(isset($orFile['or_video']))
                    @foreach($orFile['or_video'] as $key => $v)
                        <tr id="Rvid-{{$key}}">
                            <td>
                                <input type="hidden" name="old_video[]" value="{{$v['id']}}">
                                <input type="hidden" name="old_video_file[]" value="{{ Storage::url(contents_path().'or_video/'.$v['file']) }}">
                                <input type="hidden" name="old_video_topic[]" value="{{$v['topic_id']}}">
                                <select class = 'form-control' disabled>
                                    @foreach($topic as $keyT => $t)
                                        <option value="{{ $t['id_topic'] }}"
                                        @if($v->topic_id == $t['id_topic'])
                                        selected
                                        @endif
                                        >{{ $t['topic'] }}</option>
                                    @endforeach()
                                </select>
                            </td>
                            <td class="text-center" width="30%"> 
                                <a href="{{ Storage::url(contents_path().'or_video/'.$v['file']) }}" target="_blank" class="btn btn-block btn-outline-warning mt-2 btn-sm">
                                    View Older File
                                </a>
                            </td> 
                            <td class="text-center" width="10%"> 
                                <button type = "button" class = "btn btn-danger btn-sm remove_video"><i class="fa fa-trash"></i></button>
                            </td> 
                        </tr>
                    @endforeach()
                @endif()
            </tbody>
        </table>
        <div class="row">
            <div class="col text-center">
                <button type = "button" class = "btn btn-success btn-sm" id = "vid">
                + Add video
                </button>  
            </div>
        </div>
        
        {!! alertMaxSize() !!}
    </div>
</div>

@push("js")
<script>
    $(document).ready(function(){
        var rowIdx = "{{ isset($orFile['or_video']) ? count($orFile['or_video']) : 0}}";
        var topicArr = @json($topic);

        $("#vid").on("click",function(){
            var selects = `<select 
            class = 'form-control select2vid-${rowIdx}' name = 'video[${rowIdx}][topic_id]'>`;
            
            $.each( topicArr, function( key, value ) {
                selects += "<option value = '"+value.id_topic+"'>"+value.topic+"</option>";
            });

            selects += "</select>";

           $('#vid_tbody').append(`<tr id="Rvid-${rowIdx}"> 
                    <td class="row-index " > 
                        ${selects}
                    </td> 
                    <td class="text-center" width="15%"> 
                        <input type="file" class="form-control" name="video[${rowIdx}][file]" id="file-video-${rowIdx}" required>
                    </td> 
                    <td class="text-center" width="10%"> 
                        <button type = "button" class = "btn btn-danger btn-sm remove_video">X</button>
                    </td> 
                </tr>`
            ); 

            $(`.select2vid-${rowIdx}`).select2();
            rowIdx++;
        });

        $('#vid_tbody').on('click', '.remove_video', function () { 

            // Getting all the rows next to the 
            // row containing the clicked button 
            var child = $(this).closest('tr').nextAll(); 

            // Iterating across all the rows 
            // obtained to change the index 
            child.each(function () { 
                
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
