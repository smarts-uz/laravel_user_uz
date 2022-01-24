@extends('layouts.app')

@include('layouts.fornewtask')

@section('content')
<style media="screen">

</style>
<!-- Information section -->
<x-roadmap/>
<form class="" action="{{route('task.create.date.store', $task->id)}}" method="post">
  @csrf

<div class="mx-auto w-9/12  my-16">
<div class="grid grid-cols-3 gap-x-20">
  <div class="md:col-span-2 col-span-3">
    <div class="w-full text-center text-2xl">
      @lang('lang.budget_lookingFor') "{{session('name')}}"
    </div>
    <div class="w-full text-center my-4 text-gray-400">
      @lang('lang.date_percent')
    </div>
    <div class="pt-1">
      <div class="overflow-hidden h-1 text-xs flex rounded bg-gray-200  mx-auto ">
        <div style="width: 70%" class="shadow-none  flex flex-col text-center whitespace-nowrap text-white justify-center bg-green-500"></div>
      </div>
    </div>
    <div class="shadow-2xl w-full md:p-16 p-4 mx-auto my-4 rounded-2xl	w-full">
      <div class="py-4 mx-auto px-auto text-center text-3xl texl-bold">
        @lang('lang.date_startTime')
      </div>
      <div class="py-4 mx-auto px-auto text-center text-sm texl-bold">
        @lang('lang.date_startDate')
      </div>
      <div class="py-4 mx-auto  text-left ">
        <div class="mb-4">
          <div id="formulario" class="flex flex-col gap-y-4">

            <div class="flex items-center rounded-lg border py-1">
                  <select name="date_type" id="periud" class="form-select appearance-none block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white bg-clip-padding bg-no-repeat rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:outline-none" aria-label="Default select example">
                      <option selected value="1" id="1">@lang('lang.date_startTask')</option>
                      <option value="2" id="2">@lang('lang.date_finishTask')</option>
                      <option value="3" id="3">@lang('lang.date_givePeriod')</option>
                  </select>
            </div>
            <div class="flex items-center rounded-lg border py-1">
              <input type="date" name="start_date" value="{{session('deyt')}}" class="mx-auto" required>
              <input type="time" name="start_time" value="{{session('taym')}}" required>
            </div>
            <div class="flex items-center rounded-lg border py-1" id="datetime" style="display: none;">
              <input type="date" name="end_date" value="{{session('deyt2')}}" class="mx-auto" >
              <input type="time" name="end_time" value="{{session('taym2')}}" >
            </div>
          </div>
          <div class="mt-4">
             <div class="flex w-full gap-x-4 mt-4">
             <a onclick="myFunction()" class="w-1/3  border border-black-700 hover:border-black transition-colors rounded-lg py-2 text-center flex justify-center items-center gap-2">
                                            <!-- <button type="button"> -->
                                            @lang('lang.notes_back')
                                            <!-- </button> -->
                                            <script>
                                                function myFunction() {
                                                    window.history.back();
                                                }
                                            </script>
                                        </a>
               <input type="submit"
                               class="bg-green-500 hover:bg-green-500 w-2/3 cursor-pointer text-white font-bold py-5 px-5 rounded"
                               name="" value="@lang('lang.name_next')">

             </div>


          </div>
        </div>
      </div>
    </div>
  </div>
    <x-faq/>
</div>
</div>
</form>


@endsection

@section("javasript")
<script>
  $("#periud").change(function(){
    if($(this).val() == 2 ){
      $("#datetime").show();
    }else{
      $("#datetime").hide();
    }

});
</script>
    <!-- <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script> -->
@endsection
