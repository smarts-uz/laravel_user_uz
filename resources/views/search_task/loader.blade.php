@foreach(range(1,$columns=3) as $index)
<div class="bg-white sm:p-4 sm:h-26 rounded-2xl shadow-lg flex flex-col sm:flex-row gap-5 select-none my-4">
    <div class=" sm:w-16 h-16 rounded-xl m-auto bg-gray-200 animate-pulse"></div>
    <div class="flex flex-col flex-1 gap-2">
        <div class="flex justify-between ">
            <div class="h-4 bg-gray-200 w-28 animate-pulse  rounded-2xl"></div>
            <div class="h-4 bg-gray-200 w-1/3 animate-pulse  rounded-2xl"></div>
        </div>
        <div class="flex flex-1 flex-col gap-2">
            <div class="mt-auto flex gap-3 justify-between">
                <div class="bg-gray-200 w-1/4 animate-pulse h-2.5 rounded-2xl"></div>
                <div class="bg-gray-200 w-1/4 animate-pulse h-2.5 rounded-2xl"></div>
            </div>
            <div class="mt-auto flex gap-3 justify-between">
                <div class="bg-gray-200 w-1/3 animate-pulse h-2.5 rounded-2xl"></div>
                <div class="bg-gray-200 w-1/3 animate-pulse h-2.5 rounded-2xl"></div>
            </div>
            <div class="mt-auto flex gap-3 justify-between">
                <div class="bg-gray-200 w-1/2 animate-pulse h-2.5 rounded-2xl"></div>
                <div class="bg-gray-200 w-24 animate-pulse h-2.5 rounded-2xl"></div>
            </div>
        </div>
    </div>
</div>
@endforeach