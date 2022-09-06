{{-- pay modal start --}}
<div class="hidden overflow-x-auto overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center" style="background-color:rgba(0,0,0,0.5)"
     id="modal-id">
    {{-- 1 --}}
    <div class="relative w-full my-6 mx-auto max-w-3xl" id="modal11">
        <div class="border-0 rounded-lg shadow-2xl px-10 relative flex mx-auto flex-col sm:w-4/5 w-full bg-white outline-none focus:outline-none">
            <div class=" text-center p-6  rounded-t">
                <button type="submit" onclick="toggleModal()" class="rounded-md w-100 h-16 absolute top-1 right-4 focus:outline-none">
                    <i class="fas fa-times  text-slate-400 hover:text-slate-600 text-xl w-full"></i>
                </button>
                <h3 class="font-medium text-3xl block mt-6">
                    {!!__('На какую сумму хотите пополнить <br> кошелёк')!!}
                </h3>
            </div>
            <div class="text-center h-64">
                <div class="w-1/3 mx-auto h-16 border-b" id="demo" onclick="borderColor()">
                    <input class="focus:outline-none focus:border-yellow-500  w-full h-full text-4xl text-center " maxlength="7" minlength="3" id="myText" oninput="inputFunction()"
                           onkeypress='validate(event)' type="text" value="4000">
                </div>
                <p class="text-sm mt-2 leading-6 text-gray-400">{{__('Сумма пополнения, минимум — 4000 UZS')}}</p>
                <div class="mt-16">
                    <a onclick="toggleModal1()" class="px-10 py-4 font-sans  text-xl  font-semibold bg-green-500 text-white hover:bg-green-500  h-12 rounded-md text-xl" id="button"
                       href="#">{{__('К оплате')}}</a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="hidden opacity-25 fixed inset-0 z-40 bg-black" id="modal-id-backdrop"></div>
{{-- 2 --}}
<div class="hidden overflow-x-auto overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none justify-center items-center" style="background-color:rgba(0,0,0,0.5)"
     id="modal-id1">
    <div class="relative w-auto my-6 mx-auto max-w-3xl">
        <div class="border-2 shadow-2xl rounded-lg bg-gray-100 relative flex flex-col sm:w-4/5 w-full mx-auto mt-16 bg-white outline-none focus:outline-none">
            <div class=" text-center p-6  rounded-t">
                <button type="submit" onclick="toggleModal1()" class="rounded-md w-100 h-16 absolute top-1 right-4 focus:outline-none">
                    <i class="fas fa-times  text-slate-400 hover:text-slate-600 text-xl w-full"></i>
                </button>
                <h3 class="font-medium text-3xl block mt-6">
                    {{__('Способ оплаты')}}
                </h3>
            </div>

            <div class="container mb-12">
                <form action="{{route('payment.client.checkout')}}" method="GET">
                    @isset(Auth::user()->id)
                        <input type="hidden" name="key" value="{{Auth::user()->id}}">
                    @endisset
                    <div class="my-3 w-3/5 mx-auto">
                        <div class="custom-control custom-radio mb-4 text-3xl flex flex-row items-center">
                            <input id="credit" onclick="doBlock()" name="payment_system" checked type="radio" value="payme" class="custom-control-input w-5 h-5 ">
                            <button type="button" class=" w-52 focus:border-2 focus:border-dashed focus:border-green-500 mx-8" name="button"><label for="credit"><img
                                        src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQBUPBIFq--0RLaG-j2I9jtnOewkrM5v70tPi5NnWBLwrdL0v2gyIyFBdqXCKW__A8MTA&usqp=CAU" class="h-12" alt=""></label></button>
                        </div>
                        <div class="custom-control custom-radio my-8 text-3xl flex flex-row items-center">
                            <input id="debit" onclick="doBlock()" name="payment_system" value="click" type="radio" class="custom-control-input w-5 h-5 ">
                            <button type="button" class=" w-52 focus:border-2 focus:border-dashed focus:border-green-500 mx-8" name="button"><label for="debit"><img
                                        src="https://docs.click.uz/wp-content/themes/click_help/assets/images/logo.png" class="h-14" alt=""></label></button>
                        </div>
                        <div class="custom-control custom-radio mb-4 text-3xl flex flex-row items-center">
                            <a href="/paynet_oplata" class=" w-52 focus:border-2 focus:border-dashed focus:border-green-500 mx-8">
                                <img src="https://paynet.uz/medias/article/big/134/logo-paynet.png" class="cursor-pointer" alt="">
                            </a>
                        </div>
                        <div class="d-none input-group my-5" id="forhid">
                            <input id="amount_u" type="hidden" name="amount" class="form-control">
                        </div>
                    </div>
                    <div class="text-center mt-8">
                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white text-2xl font-bold py-3 px-8 rounded">{{__('Оплата')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="h-16"></div>
<div class="hidden opacity-25 fixed inset-0 z-40 bg-black" id="modal-id-backdrop"></div>
<div class="hidden opacity-25 fixed inset-0 z-40 bg-black" id="modal1-id-backdrop"></div>

<script>
    let PAYMENT_TEST = '{{env('PAYMENT_TEST')}}' // Used in payment.js
    let MIN_AMOUNT = '{{setting('admin.min_amount') ?? 4000}}' // Used in payment.js
</script>
<script src="{{ asset('js/payment.js') }}"></script>
