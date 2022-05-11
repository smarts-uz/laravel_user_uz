@extends("layouts.app")

@section("content")

{{-- web search_task start--}}
@include('search_task.web_task_search')
{{-- web search_task end--}}

@include('search_task.mobile_task_search')


<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="https://api-maps.yandex.ru/2.1/?apikey=f4b34baa-cbd1-432b-865b-9562afa3fcdb&lang={{__('ru_RU')}}"
    type="text/javascript"></script>
<script src="js/search_tasks.js"></script>
<script>
$("#search_form").on("submit", function(event) {
    event.preventDefault();
    $.ajax({
        url: $(this).attr("action"),
        method: $(this).attr("method"),
        data: {
            data: $(this).serializeArray()
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        beforeSend: function() {
            $('#loader').show();
        },
        success: function(data) {
            $('#dataPlace').html(data.html)
        },
        complete: function() {
            $('#loader').hide();
        }
    })
});
$("input:checkbox").click(function() {
    $("#search_form").submit();
})
$(document).ready(function() {
    $("#search_form").submit();
})
</script>
@endsection