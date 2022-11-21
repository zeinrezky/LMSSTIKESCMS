<input name = "{{$name}}" class = "form-control" type='file' id="id_{{$name}}" />


@if(!empty(@$hardcodeName))
    <input type="hidden" name = "delete_{{$name}}" id = "delete_{{$name}}" value = "{{ $hardcodeName }}">
    <img width="100" style = "display:show;" height="100" id="img_{{$name}}" src="{{ Storage::url(contents_path().$hardcodeName) }}" />
@else
    <input type="hidden" name = "delete_{{$name}}" id = "delete_{{$name}}" value = "{{ $model->{$name} }}">
    <img width="100" style = "display:{{ !empty($model->{$name}) ? "show" : "none" }};" height="100" id="img_{{$name}}" src="{{ Storage::url(contents_path().$model->{$name}) }}" />
@endif

<a href="javascript:void(0);" id = "remove_{{$name}}" style = "display:{{ !empty($model->{$name}) ? "show" : "none" }};">
    remove
</a>

@push("js")
    <script>
        $("#id_{{$name}}").change(function () {
            readURL(this,'img_{{$name}}');

            if($(this).val() != "")
            {
                $("#img_{{$name}}").show();
                $("#remove_{{$name}}").show();
            }else{
                $("#img_{{$name}}").hide();
                $("#remove_{{$name}}").hide();
            }

            $("#delete_{{$name}}").val($(this).val());

        });
        
        $(document).ready(function(){
            $("#remove_{{$name}}").click(function(){
                $(this).hide();
                $("#img_{{$name}}").hide();
                $("#id_{{$name}}").val("");
                $("#delete_{{$name}}").val($(this).val());
            });
        });

    </script>
@endpush