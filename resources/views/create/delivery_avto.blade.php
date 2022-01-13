@extends('layouts.app')

@section('content')

<!-- Information section -->
<x-roadmap/>
<form class="" action="{{route('task.create.date')}}" method="post">
  @csrf
<div class="mx-auto w-9/12  my-16">
<div class="grid grid-cols-3 gap-x-20">
  <div class="col-span-2">
    <div class="w-full text-center text-2xl">
    @lang('lang.budget_lookingFor') "{{session('name')}}"
    </div>
    <div class="w-full text-center my-4 text-[#5f5869]">
    Задание заполнено на 75%
    </div>
    <div class="relative pt-1">
      <div class="overflow-hidden h-1  flex rounded bg-gray-200  mx-auto ">
        <div style="width: 75%" class="shadow-none  flex flex-col text-center whitespace-nowrap text-white justify-center bg-green-500"></div>
      </div>
    </div>
    <div class="shadow-xl w-full mx-auto mt-7 rounded-2xl	w-full p-6 px-20">
      <div class="py-4 mx-auto px-auto text-center text-3xl texl-bold">
      Как планируете оплатить покупку?
      </div>

      <div class="py-4 mx-auto  text-left ">
        <div class="mb-4">
          <div id="formulario" class="flex flex-col gap-y-4">

        <div>

            <div class="mb-3 xl:w-full">
                <input type="radio" id="delivey_car"
                    name="delivey_car[]" value="Не требуется">
                <label for="delivey_car">Не требуется</label>
                <br>
                <br>
                <input type="radio" id="delivey_car"
                    name="delivey_car[]" value="На легковом авто">
                <label for="delivey_car[]">На легковом авто</label>
                <br>
                <br>
                <input type="radio" id="delivey_car"
                    name="delivey_car[]" value="На грузовом авто">
                <label for="delivey_car[]">На грузовом авто</label>
              </div>
        </div>

        <div>
            <!-- <span class="underline hover:text-[#5f5869] text-lg decoration-dotted cursor-pointer float-right">Приватная информация</span> -->
        </div>
        <div class="mt-4">
            <div class="flex w-full gap-x-4 mt-4">
            <a onclick="myFunction()" class="w-1/3  border border-[#000]-700 hover:border-[#000] transition-colors rounded-lg py-2 text-center flex justify-center items-center gap-2">
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
             class="bg-[#6fc727] hover:bg-[#5ab82e] w-2/3 cursor-pointer text-white font-bold py-5 px-5 rounded"
             name="" value="@lang('lang.name_next')">
            </div>
         </div>


          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-span">
    <x-faq/>
  </div>
</div>
</div>
</form>


@endsection