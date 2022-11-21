<div class="row">
    <div class="col-md-12">
        <table class="table">
            <thead>
                <tr>
                    <th>Session & Topic <small>*Each session & Topic can consists of doc & pdf</small></th>
                    <th>File</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id = "ln_tbody">
                @if(isset($orFile['or_ln']))
                    @foreach($orFile['or_ln'] as $key => $v)
                        <tr id="Rln-{{$key}}">
                            <td>
                                <input type="hidden" name="old_ln[]" value="{{$v['id']}}">
                                <input type="hidden" name="old_ln_file[]" value="{{ Storage::url(contents_path().'or_ln/'.$v['file']) }}">
                                <input type="hidden" name="old_ln_topic[]" value="{{$v['topic_id']}}">
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
                                <a href="{{ Storage::url(contents_path().'or_ln/'.$v['file']) }}" target="_blank" class="btn btn-block btn-outline-warning mt-2 btn-sm">
                                    View Older File
                                </a>
                            </td> 
                            <td class="text-center" width="10%"> 
                                <button type = "button" class = "btn btn-danger btn-sm remove_ln"><i class="fa fa-trash"></i></button>
                            </td> 
                        </tr>
                    @endforeach()
                @endif()
            </tbody>
        </table>
        <div class="row">
            <div class="col text-center">
                <button type = "button" class = "btn btn-success btn-sm" id = "button_ln">
                + Add Ln
                </button>  
            </div>
        </div>
        
        {!! alertMaxSize() !!}
    </div>
</div>

@push("js")
<script>
    $(document).ready(function(){
        var rowIdx = "{{ isset($orFile['or_ln']) ? count($orFile['or_ln']) : 0}}";
        var topicArr = @json($topic);

        $("#button_ln").on("click",function(){


            var selects = `<select style="width:100%" 
            class = 'form-control select2ln-${rowIdx}' name = 'ln[${rowIdx}][topic_id]'>`;
            
            $.each( topicArr, function( key, value ) {
                selects += "<option value = '"+value.id_topic+"'>"+value.topic+"</option>";
            });

            selects += "</select>";

           $('#ln_tbody').append(`<tr id="Rln-${rowIdx}"> 
                    <td class="row-index " > 
                        ${selects}
                    </td> 
                    <td class="text-center" width="15%"> 
                        <input type="file" class="form-control" name="ln[${rowIdx}][file]" required id="file-ln-${rowIdx}">
                    </td> 
                    <td class="text-center" width="10%"> 
                        <button type = "button" class = "btn btn-danger btn-sm remove_ln">X</button>
                    </td> 
                </tr>`
            ); 

            $(`.select2ln-${rowIdx}`).select2();
            rowIdx++;
        });

        $('#ln_tbody').on('click', '.remove_ln', function () { 

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
