<div class="row">
    <div class="col-md-12">
        <table class="table">
            <thead>
                <tr>
                    <th>Session & Topic <small>*Each session & topic can consists of ppt & pdf</small></th>
                    <th>File</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id = "ppt_tbody">
                @if(isset($orFile['or_ppt']))
                    @foreach($orFile['or_ppt'] as $key => $v)
                        <tr id="Rppt-{{$key}}">
                            <td>
                                <input type="hidden" name="old_ppt[]" value="{{$v['id']}}">
                                <input type="hidden" name="old_ppt_topic[]" value="{{$v['topic_id']}}">
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
                                <a href="{{ Storage::url(contents_path().'or_ppt/'.$v['file']) }}" target="_blank" class="btn btn-block btn-outline-warning mt-2 btn-sm">
                                    View Older File
                                </a>
                            </td> 
                            <td class="text-center" width="10%"> 
                                <button type = "button" class = "btn btn-danger btn-sm remove_ppt"><i class="fa fa-trash"></i></button>
                            </td> 
                        </tr>
                    @endforeach()
                @endif()
            </tbody>
        </table>
        <div class="row">
            <div class="col text-center">
                <button type = "button" class = "btn btn-success btn-sm" id = "button_ppt">
                + Add PPT
                </button>  
            </div>
        </div>
        {!! alertMaxSize() !!}
    </div>
</div>

@push("js")
<script>
    $(document).ready(function(){
        var rowIdx = "{{ isset($orFile['or_ppt']) ? count($orFile['or_ppt']) : 0}}";
        var topicArr = @json($topic);

        $("#button_ppt").on("click",function(){


            var selects = `<select 
            class = 'form-control select2ppt-${rowIdx}' name = 'ppt[${rowIdx}][topic_id]'>`;
            
            $.each( topicArr, function( key, value ) {
                selects += "<option value = '"+value.id_topic+"'>"+value.topic+"</option>";
            });

            selects += "</select>";

           $('#ppt_tbody').append(`<tr id="Rppt-${rowIdx}"> 
                    <td class="row-index" > 
                        ${selects}
                    </td> 
                    <td class="text-center" width="30%"> 
                        <input type="file" class="form-control" name="ppt[${rowIdx}][file]" required>
                    </td> 
                    <td class="text-center" width="10%"> 
                        <button type = "button" class = "btn btn-danger btn-sm remove_ppt">X</button>
                    </td> 
                </tr>`
            ); 

            $(`.select2ppt-${rowIdx}`).select2();
            rowIdx++;
        });

        $('#ppt_tbody').on('click', '.remove_ppt', function () { 

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
