    @extends("layouts.app")
@section('content')
<link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{asset('css/admin/datatable.css')}}">
<style>
    td {
        text-align: center;
    }
</style>
    <div class="w-11/12  mx-auto text-base mt-4">
        <div class="grid lg:grid-cols-3 grid-cols-2 lg:w-5/6 w-full mx-auto">

            {{-- user ma'lumotlari --}}
            <div class="col-span-2 w-full mx-auto">

                @include('components.profileFigure')

                <div class="content  mt-20 ">
                    <div class="grid md:grid-cols-10 w-full ">
                        <ul class="md:col-span-8 col-span-10 items-center w-3/4 md:w-full">
                            <li class="inline mr-1 md:mr-5">
                                <a href="/profile" class="text-lg text-gray-600">{{__('Обо мне')}}</a>
                            </li>
                            <li class="inline mr-1 md:mr-5">
                                <a href="/profile/cash" class="text-lg font-bold text-gray-700 border-b-4 border-green-500 pb-1">{{__('Счет')}}</a>
                            </li>
                            <li class=" md:mr-5 mr-1 inline-block md:hidden block">
                                <a href="/profile/settings" class="text-lg text-gray-600" id="settingsText">{{__('Настройки')}}</a>
                            </li>
                        </ul>
                        <div class="md:col-span-2 md:block hidden text-gray-600 ml-6" id="settingsIcon">
                            <a href="/profile/settings" class="flex items-center ">
                                <i class="fas fa-cog text-2xl"></i>
                                <span class="font-medium ml-2">{{__('Настройки')}}</span>
                            </a>
                        </div>
                    </div>
                    <hr>
                    {{-- cash start --}}
                    <div class="cash block  w-full" id="tab-cash">
                        <div class="head mt-5">
                            <div class="flex sm:flex-row flex-col">
                                <h2 class="font-semibold text-2xl text-gray-700 mb-4">{{__('Ваш баланс')}} :
                                    @if ($balance == null) 0
                                    @else {{$balance->balance }}
                                    @endif
                                </h2>
                                <h1 class="font-semibold text-2xl text-gray-700 mb-4 sm:ml-12 ml-0">ID: {{$user->id}}</h1>
                            </div>
                            <p class="inline">{{__('Пополнить счет на')}}</p>
                            <input
                                class="focus:outline-none focus:border-yellow-500  inline rounded-xl xl:ml-3 ring-1 text-2xl text-center h-18 w-36  pb-1"
                                onkeyup="myText.value = this.value" oninput="inputCash()" onkeypress='validate(event)'
                                id="myText1" type='text' min="{{setting('admin.min_amount',0)}}" maxlength="7" value="{{setting('admin.min_amount',0)}}" />
                            <span class="xl:ml-1 xl:text-xl lg:text-lg text-xl">UZS</span>
                            <button onclick="toggleModal()" type="submit" id="button2"
                                class="md:inline block xl:ml-10 lg:ml-2 mx-auto mt-5 md:mt-0 h-10 rounded-xl ring-0 hover:bg-green-600 text-white bg-green-500 md:w-40 w-full">
                                {{__('Пополнить счет')}}
                            </button>
                        </div>
                        <div class="relative mt-10 p-5 bg-gray-100 w-full block">
                            <h2 class="inline-block font-medium text-2xl text-gray-700">{{__('История операций')}}</h2>
                            <label class="text-left md:inline-block w-full  md:w-1/2">
                                <select id="period"
                                    class="form-select block md:w-36 w-full h-10 rounded-xl focus:outline-none ring-1 ring-black md:0 md:ml-5">
                                    <option value="month">{{__('за месяц')}}</option>
                                    <option value="week">{{__('за неделю')}}</option>
                                    <option value="year">{{__('за год')}}</option>
                                    <option value="date-period">{{__('за период')}}</option>
                                </select>
                            </label>
                            <div class="hidden flex flex-row items-center my-4" id="ddr">
                              <div>
                                    <p class="text-xl">{{__('Период : ')}}</p>
                              </div>
                              <div class="mx-4">
                                    <input id="from-date" type="date" class="p-1 rounded-lg border-2 border-gray-300 focus:outline-none">
                              </div>
                              <div>
                                    <input id="to-date" type="date" class="p-1 rounded-lg border-2 border-gray-300 focus:outline-none">
                              </div>
                            </div>
                            <ul id="tabs" class="flex sm:flex-row flex-col rounded-sm w-full shadow bg-gray-200 mt-4">
                                <div id="first_tab" class="w-full text-center">
                                    <a id="default-tab" href="#first" data-method="Click" data-number="1"
                                        class="inline-block relative py-1 w-full payment-type">{!!__('Пополнить <br> через Click')!!}</a>
                                </div>
                                <div class="w-full text-center">
                                    <a href="#second" data-method="Payme" data-number="2"
                                        class="inline-block relative py-1 w-full payment-type">{!!__('Пополнить <br> через Payme')!!}</a>
                                </div>
                                <div class="w-full text-center">
                                    <a href="#third" data-method="Paynet" data-number="3"
                                        class="inline-block relative py-1 w-full payment-type">{!!__('Пополнить <br> через Paynet')!!}</a>
                                </div>
                                <div class="w-full text-center">
                                    <a href="#fourth" data-method="Task" data-number="4"
                                        class="inline-block relative py-1 w-full payment-type">
                                        {!!__('Списания <br> со счета')!!}
                                    </a>
                                </div>
                            </ul>
                            <div id="tab-contents">
                                <div id="first" class="hidden py-4">
                                    <div id='recipients' class="p-8 mt-6 lg:mt-0 rounded shadow bg-white">
                                        <table id="history-table1" class="stripe hover" style="width:100%; padding-top: 1em;  padding-bottom: 1em;" data-page-length='10'>
                                            <thead>
                                                <tr>
                                                    <th>{{__('Дата')}}</th>
                                                    <th>{{__('Количество')}}</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                                <div id="second" class="hidden py-4">
                                    <div id='recipients' class="p-8 mt-6 lg:mt-0 rounded shadow bg-white">
                                        <table id="history-table2" class="stripe hover" style="width:100%; padding-top: 1em;  padding-bottom: 1em;">
                                            <thead>
                                            <tr>
                                                <th data-priority="1">{{__('Дата')}}</th>
                                                <th data-priority="2">{{__('Количество')}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div id="third" class="hidden py-4">
                                    <div id='recipients' class="p-8 mt-6 lg:mt-0 rounded shadow bg-white">
                                        <table id="history-table3" class="stripe hover" style="width:100%; padding-top: 1em;  padding-bottom: 1em;">
                                            <thead>
                                            <tr>
                                                <th data-priority="1">{{__('Дата')}}</th>
                                                <th data-priority="2">{{__('Количество')}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div id="fourth" class="hidden py-4">
                                    <div id='recipients' class="p-8 mt-6 lg:mt-0 rounded shadow bg-white">
                                        <table id="history-table4" class="stripe hover" style="width:100%; padding-top: 1em;  padding-bottom: 1em;">
                                            <thead>
                                            <tr>
                                                <th data-priority="1">{{__('Дата')}}</th>
                                                <th data-priority="2">{{__('Количество')}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="FAQ reltive block w-full mt-5 text-gray-600">
                            <h2 class="font-medium text-2xl text-gray-700">{{__('Частые вопросы')}}</h2>
                            <h4 class="font-medium text-lg mt-2 text-gray-700">
                                {{__('Условия работы с Universal Services.')}}</h4>
                            <p class="text-base">
                                {{__('Universal Services списывает с исполнителей фиксированную оплату за возможность оставлять к заданиям отклики с контактными данными. Оплата за отклики не возвращается.')}}
                            </p>
                            <h4 class="font-medium text-lg mt-2 text-gray-700">
                                {{__('Какая минимальная сумма для пополнения счета?')}}</h4>
                            <p class="text-base">{{setting('admin.min_amount',0)}} UZS</p>
                        </div>
                    </div>
                    {{-- cash end --}}
                </div>

            </div>


            {{-- right-side-bar --}}
            <x-profile-info></x-profile-info>
            {{-- tugashi o'ng tomon ispolnitel --}}
        </div>
    </div>
    <script>
        let PAYMENT_TEST = '{{env('PAYMENT_TEST')}}' // Used in cash.js
        let MIN_AMOUNT = '{{setting('admin.min_amount',0)}}' // Used in cash.js
    </script>
    <script src="{{ asset('js/profile/cash.js') }}"></script>
    <script>
        // datatable js
        function getTransactions (data, table_num) {
            $(`#history-table${table_num}`).DataTable({
                destroy: true,
                processing: false,
                serverSide: false,
                paging: true,
                language:{
                    @if(session('lang') === 'ru')
                        url: "//cdn.datatables.net/plug-ins/1.12.1/i18n/ru.json"
                    @else
                        url: "//cdn.datatables.net/plug-ins/1.12.1/i18n/uz.json"
                    @endif
                },
                ajax: {
                    url: '/profile/transactions/history',
                    type: 'GET',
                    dataSrc: 'transactions',
                    data: function (d) {
                        d.method = data['method'];
                        if ('period' in data) {
                            d.period = data['period']
                        } else {
                            d.from_date = data['from_date']
                            d.to_date = data['to_date']
                        }
                    }
                },
                columns: [
                    { data: 'created_at' },
                    { data: 'amount' }
                ]
            });
        }
    </script>
@endsection
