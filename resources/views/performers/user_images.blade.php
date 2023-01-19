<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.css" rel="stylesheet">

<div class=" float-left mr-4">
    <img class="rounded-lg w-24 h-24 border-2 mb-2"
         @if ((!$user->avatar)) src='{{asset("storage/images/default.jpg")}}'
         @else src="{{asset("storage/{$user->avatar}")}}" @endif alt="avatar">
    <div class="flex sm:flex-row items-center text-sm">
        <p class="text-black ">{{__('Отзывы:')}}</p>
        <i class="far fa-thumbs-up text-blue-500 ml-1 mb-1"></i>
        <span class="text-gray-800 mr-2 ">{{$user->review_good}}</span>
        <i class="far fa-thumbs-down mt-0.5 text-blue-500"></i>
        <span class="text-gray-800">{{$user->review_bad}}</span>
    </div>
    <div class="flex items-center" id="stars{{$user->id}}">
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/raty/3.1.1/jquery.raty.min.css"
      integrity="sha512-XsO5ywONBZOjW5xo5zqAd0YgshSlNF+YlX39QltzJWIjtA4KXfkAYGbYpllbX2t5WW2tTGS7bmR0uWgAIQ8JLQ=="
      crossorigin="anonymous" referrerpolicy="no-referrer"/>
<script src="https://code.jquery.com/jquery-3.6.0.js"
        integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-raty-js@2.8.0/lib/jquery.raty.min.js"></script>

<script>
    $("#stars{{$user->id}}").raty({
        path: 'https://cdn.jsdelivr.net/npm/jquery-raty-js@2.8.0/lib/images',
        readOnly: true,
        score: {{$user->review_rating ?? 0}},
        size: 12
    });
</script>
