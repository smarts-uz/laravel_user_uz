@extends("layouts.app")

@section("content")

{{-- web search_task start--}}
@include('search_task.web_task_search')
{{-- web search_task end--}}

@include('search_task.mobile_task_search')

<div class="w-11/12">
    <div class="no_tasks" hidden>
{{--
        Show no tasks image
--}}
        <div class=" w-3/5 h-3/5 mx-auto">
            <img src="images/notlikes.png" class="w-full h-full">
            <div class="text-center w-full h-full">
                <p className="text-4xl"><b>{{__('Задания не найдены')}}</b></p>
                <p className="text-xl">{{__('Попробуйте уточнить запрос или выбрать другие категории')}}</p>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="https://api-maps.yandex.ru/2.1/?apikey=f4b34baa-cbd1-432b-865b-9562afa3fcdb&lang={{__('ru_RU')}}" type="text/javascript"></script>
<script src="js/search_tasks.js"></script>
<script>
    $("form").on("submit", function(event) {
        event.preventDefault();
        let data_seria = $(this).serializeArray();
        console.log(data_seria)
        $.ajax({
            url: "{{route('searchTask.search_new2')}}",
            type: 'POST',
            data: {zapros:data_seria},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                $('.show_tasks').html(data)
            },
            error: function (error) {
                console.error("Ajax orqali yuklashda xatolik..." , error);
            }
        });
    });
</script>
@endsection
