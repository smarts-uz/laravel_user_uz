<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.css" rel="stylesheet">
    <title>Universal services</title>
</head>
<body>
<h1 class="text-center mt-4 text-3xl font-bold">Users info</h1>
<div class="w-11/12 mx-auto mt-4 grid grid-cols-4">
    <!-- Tabs -->
    <div id="tabs" class="col-span-1 flex flex-col pt-2 px-1 w-full">
        <div class="bg-blue-500 text-white text-center border border-gray-300 focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2">
            <a id="default-tab" href="#first">
                user yaratgan tasklari
            </a>
        </div>
        <div class="text-center border border-gray-300 focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2">
            <a href="#second">
                user otklik tashlagan tasklar
            </a>
        </div>
       <div class="text-center border border-gray-300 focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2">
           <a href="#third">
               user qoldirgan izohlari
           </a>
       </div>
        <div class="text-center border border-gray-300 focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2">
            <a href="#fourth">
                userga qoldirilgan izohlar
            </a>
        </div>
        <div class="text-center border border-gray-300 focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2">
            <a href="#five">
                user qoldirgan okliklari
            </a>
        </div>
        <div class="text-center border border-gray-300 focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2">
            <a href="#six">
                user yuklagan rasmlari
            </a>
        </div>
        <div class="text-center border border-gray-300 focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2">
            <a href="#seven">
                user yuklagan youtube link
            </a>
        </div>
    </div>

    <!-- Tab Contents -->
    <div id="tab-contents" class="w-full col-span-3 border-2 rounded-xl mt-2">
        <div id="first" class="p-4">
            @foreach($tasks as $task)
                <a target="_blank" href="/detailed-tasks/{{$task->id}}">{{$task->name}}</a> <br>
            @endforeach
        </div>
        <div id="second" class="hidden p-4">
            @foreach($performer_tasks as $performer_task)
                <a target="_blank" href="/detailed-tasks/{{$performer_task->id}}">{{$performer_task->name}}</a> <br>
            @endforeach
        </div>
        <div id="third" class="hidden p-4">
            @foreach($user_reviews as $user_review)
                <a target="_blank" href="">{{$user_review->description}}</a> <br>
            @endforeach
        </div>
        <div id="fourth" class="hidden p-4">
            @foreach($performer_reviews as $performer_review)
                <a target="_blank" href="">{{$performer_review->description}}</a> <br>
            @endforeach
        </div>
        <div id="five" class="hidden p-4">
            @foreach($task_responses as $task_response)
                <a target="_blank" href="">{{$task_response->description}}</a> <br>
            @endforeach
        </div>
        <div id="six" class="hidden p-4">
            Portfolio
        </div>
        <div id="seven" class="hidden p-4">
            {{$user->youtube_link}}
        </div>
    </div>
</div>

<script>
    let tabsContainer = document.querySelector("#tabs");
    let tabTogglers = tabsContainer.querySelectorAll("#tabs a");
    tabTogglers.forEach(function(toggler) {
        toggler.addEventListener("click", function(e) {
            e.preventDefault();
            let tabName = this.getAttribute("href");
            let tabContents = document.querySelector("#tab-contents");
            for (let i = 0; i < tabContents.children.length; i++) {
                tabTogglers[i].parentElement.classList.remove("bg-blue-500","text-white");
                tabContents.children[i].classList.remove("hidden");
                if ("#" + tabContents.children[i].id === tabName) {
                    continue;
                }
                tabContents.children[i].classList.add("hidden");
            }
            e.target.parentElement.classList.add("bg-blue-500","text-white");
        });
    });
</script>
</body>
</html>
