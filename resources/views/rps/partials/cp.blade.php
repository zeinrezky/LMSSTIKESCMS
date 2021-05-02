<div class="row">
    <div class="col-md-12" id = "cp_12">
        <table class="table">
            <thead>
                <tr>
                    <th>CP</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id = "cp_tbody">

                @if(!empty($capaianPembelajaran))
                    @foreach($capaianPembelajaran as $key => $v)
                        <tr id="R0"> 
                            <td class="row-index text-center"> 
                                <input type = "text" class = "form-control" name = "capaian_pembelajaran[]" class = "cp_text" value="{{ $v }}" />
                            </td> 
                            <td class="text-center"> 
                                @if($key != 0)
                                    <button type = "button" class = "btn btn-danger btn-sm remove_cp">X</button>
                                @endif
                            </td> 
                        </tr>
                    @endforeach
                @else
                    <tr id="R0"> 
                        <td class="row-index text-center"> 
                            <input required type = "text" class = "form-control" name = "capaian_pembelajaran[]" class = "cp_text" />
                        </td> 
                        <td class="text-center"> 
                            
                        </td> 
                    </tr>
                @endif
            </tbody>
        </table>
        <div class="row">
            <div class="col text-center">
                <button type = "button" class = "btn btn-success btn-sm" id = "button_cp">
                + Add CP
                </button>  
            </div>
        </div>
    </div>
</div>

@push("js")
<script>
    $(document).ready(function(){
        var rowIdx = 1; 
        $("#button_cp").on("click",function(){
           $('#cp_tbody').append(`<tr id="R${++rowIdx}"> 
                    <td class="row-index text-center"> 
                        <input type = "text" class = "form-control" required name = "capaian_pembelajaran[]" class = "cp_text" />
                    </td> 
                    <td class="text-center"> 
                        <button type = "button" class = "btn btn-danger btn-sm remove_cp">X</button>
                    </td> 
                </tr>`
            ); 
        });

        $('#cp_tbody').on('click', '.remove_cp', function () { 

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
