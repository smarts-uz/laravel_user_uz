@extends("layouts.app")

@section("content")

    <div class="container mx-auto">
        <div class="grid grid-cols-3  grid-flow-row mt-10">
        {{-- left sidebar start --}}
            <div class="md:col-span-2 col-span-3 px-2 mx-3">
                <figure class="w-full">
                    <div class="top-0 right-0 float-right text-gray-500 text-sm">
                        <i class="far fa-eye"></i>
                        <span>2105 просмотров профиля</span>
                    </div>
                   <div>
                       <p class="text-lg text-gray-500">Был на сайте 1 ч. 8 мин. назад</p>
                       <h1 class="text-3xl font-bold ">{{$performers->name}}</h1>
                   </div>

                   <div class="flex w-full mt-6">
                    <div class="flex-initial w-1/3">
                      <img class="h-56 w-56" src="https://avatar.youdo.com/get.userAvatar?AvatarId=7441787&AvatarType=H180W180" alt="#">
                    </div>
                    <div class="flex-initial w-2/3 lg:ml-0 ml-6">
                        <div class="font-medium text-lg">
                            <i class="fas fa-check-circle text-lime-600 text-2xl"></i>
                            <span>Документы подтверждены</span>
                        </div>
                        <div class="text-gray-500 text-base mt-4">
                            <span>20 лет</span>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Санкт-Петербург</span>
                        </div>
                        <div class="text-gray-500 text-base mt-6">
                            <span>Выполнил 199 заданий, создал 3 задания</span>
                        </div>
                        <div class="text-gray-500 text-base mt-1">
                            <span>Средняя оценка: 4,9</span>
                             <i  class="fas fa-star text-amber-500"></i><i  class="fas fa-star text-amber-500"></i><i  class="fas fa-star text-amber-500"></i><i  class="fas fa-star text-amber-500"></i><i  class="fas fa-star text-amber-500"></i>
                            <span class="text-cyan-500 hover:text-red-600">(197 отзывов)</span>
                        </div>
                        <div class="flex flex-row">
                             <img class="h-24 mt-4 ml-2" src="{{ asset('images/icon_year.svg') }}">
                             <img class="h-24 mt-4 ml-4" src="{{ asset('images/icon_shield.png') }}">
                             <img class="h-20 mt-6 ml-4" src="{{ asset('images/icon_bag.png') }}">
                         </div>
                         <div>
                             <a href="#"><button class="bg-gray-300 text-inherit mt-6 disabled font-bold py-2 px-4 rounded opacity-50 ">
                                Задать вопрос
                              </button></a>
                         </div>
                         <a class="md:hidden block mt-8" href="#">
                            <button  class="bg-amber-600 hover:bg-amber-500 text-2xl text-white font-medium py-4 px-12  rounded">
                                Предложить задание
                            </button>
                        </a>
                    </div>
                  </div>
                </figure>

                <div class="mt-8">
                    <h1 class="text-3xl font-semibold text-gray-700">Обо мне</h1>
                    <div class="mt-4 mb-4 bg-orange-100 py-4 rounded-xl">
                        <p class="ml-6">Чтобы воспользоваться моими услугами, нажмите кнопку <a class="text-red-500" href="#">«Предложить задание»</a>. <br>
                            Сотрудничаю с условием, что о моей работе будет оставлен отзыв на YouDo.</p>
                    </div>
                </div>
                <p>Доброго времени суток, меня зовут Борис, я работал курьером на протяжении трёх лет в таких компаниях как: МигМигом, Sabellino и dostavista (Москва); на данный момент занимаюсь организацией доставок, иногда сам подрабатываю курьером, нахожусь в активном поиске частных лиц, процент беру минимальный, являюсь самозанятым в компании Amway, буду рад с Вами сотрудничать, для обсуждения деталей задания пожалуйста пишите на Вотсап.</p>

                <h1 class="mt-12 text-3xl font-medium">Виды выполняемых работ</h1>

               <div class="mt-8">
                    <a href="#" class="text-2xl font-medium hover:text-red-500 underline underline-offset-4 ">Курьерские услуги</a>
                    <p class="mt-2 text-gray-400 text-lg">1 место в рейтинге категории в г. Санкт-Петербург, выполнено 199 заданий <br>
                        20 место в общем рейтинге категории</p>
               </div>
               <div>
                  <ul>
                    <li class="text-lg mt-2 text-gray-500"><a class="hover:text-red-500 underline underline-offset-4"  href="#">Услуги пешего курьера</a>  ................................................1 место</li>
                    <li class="text-lg mt-2 text-gray-500"><a class="hover:text-red-500 underline underline-offset-4"  href="#">Другая посылка</a>  ...............................................................1 место</li>
                    <li class="text-lg mt-2 text-gray-500"><a class="hover:text-red-500 underline underline-offset-4"  href="#">Срочная доставка</a>  ..........................................................1 место</li>
                    <li class="text-lg mt-2 text-gray-500"><a class="hover:text-red-500 underline underline-offset-4"  href="#">Доставка продуктов</a>  .....................................................1 место</li>
                    <li class="text-lg mt-2 text-gray-500"><a class="hover:text-red-500 underline underline-offset-4"  href="#">Купить и доставить</a>  .......................................................2 место</li>
                    <li class="text-lg mt-2 text-gray-500"><a class="hover:text-red-500 underline underline-offset-4"  href="#">Услуги курьера на легковом авто</a>  .........................4 место</li>
                    <li class="text-lg mt-2 text-gray-500"><a class="hover:text-red-500 underline underline-offset-4"  href="#">Доставка еды из ресторанов</a>(нет выполненных заданий) </li>
                    <li class="text-lg mt-2 text-gray-500"><a class="hover:text-red-500 underline underline-offset-4"  href="#">Курьер на день</a>(нет выполненных заданий)</li>
                  </ul>
               </div>

            </div>
        {{-- left sidebar end --}}

        {{-- right sidebar start --}}
            <div class="md:col-span-1 col-span-3  md:mx-2 mx-auto inline-block w-4/5 float-right right-20  h-auto">
                <div class="mt-8 ">
                    <a class="md:block hidden" href="#">
                        <button  class="modal-open bg-amber-600 hover:bg-amber-500 text-2xl text-white font-medium py-4 px-12  rounded">
                            Предложить задание
                        </button>
                    </a>
                    <p class="md:block hidden text-sm text-amber-500 text-center mt-8">Исполнитель получит уведомление и сможет оказать вам свои услуги</p>
                </div>
                <div class="mt-16 border p-8 rounded-lg border-gray-300">
                    <div>
                        <h1 class="font-medium text-2xl">Исполнитель</h1>
                        <p class="text-gray-400">на YouDo с 13 апреля 2021 г.</p>
                    </div>
                    <div class="">
                        <div class="flex w-full mt-4">
                            <div class="flex-initial w-1/4">
                                <i class="text-[#fff] far fa-file-image text-2xl bg-lime-500 py-3 px-4 rounded-lg"></i>
                            </div>
                            <div class="flex-initial w-3/4 xl:ml-0 ml-8">
                                <h2 class="font-medium text-lg">Документы</h2>
                                <p>Документы проверены</p>
                            </div>
                        </div>
                        <div class="flex w-full mt-4">
                            <div class="flex-initial w-1/4">
                                <i class="text-[#fff] fas fa-phone-square text-2xl bg-amber-500 py-3 px-4 rounded-lg"></i>
                            </div>
                            <div class="flex-initial w-3/4 xl:ml-0 ml-8">
                                <h2 class="font-medium text-lg">Телефон</h2>
                                <p>Подтвержден</p>
                            </div>
                        </div>
                        <div class="flex w-full mt-4">
                            <div class="flex-initial w-1/4">
                                <i class="text-[#fff] far fa-envelope text-2xl bg-blue-500 py-3 px-4 rounded-lg"></i>
                            </div>
                            <div class="flex-initial w-3/4 xl:ml-0 ml-8">
                                <h2 class="font-medium text-lg">Email</h2>
                                <p>Подтвержден</p>
                            </div>
                        </div>
                        <div class="flex w-full mt-4">
                            <div class="flex-initial w-1/4">
                                <i class="text-[#fff] far fa-address-book text-2xl bg-blue-400 py-3 px-4 rounded-lg"></i>
                            </div>
                            <div class="flex-initial w-3/4 xl:ml-0 ml-8">
                                <h2 class="font-medium text-lg">Вконтакте</h2>
                                <p>Подтвержден</p>
                            </div>
                        </div>
                        <div class="flex w-full mt-4">
                            <div class="flex-initial w-1/4">
                                <i class=" fab fa-apple text-2xl bg-gray-400 text-[#fff] py-3 px-4 rounded-lg"></i>
                            </div>
                            <div class="flex-initial w-3/4 xl:ml-0 ml-8">
                                <h2 class="font-medium text-lg">Apple ID</h2>
                                <p>Подтвержден</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-8">
                    <h1 class="text-3xl font-medium">Новые публикации <br><a href="#" class="text-blue-500 hover:text-red-600"> в блоге</a></h1>
                    <img class="mt-4 rounded-xl " src="https://content0.youdo.com/zi.ashx?i=d36fd188a176881f" alt="#">
                    <h1 class="mt-4 font-medium text-xl text-gray-700">Из фрилансера в CEO Digital-агентства</h1>
                    <p class="mt-2 font-normal text-base text-gray-700">Вдохновляющая видео-история <br> исполнителя Александра</p>
                    <hr class="mt-4 mb-4 text-gray-300">
                    <h2 class="font-medium text-xl text-gray-700">Станьте сертифицированным мастером Tarkett</h2>
                    <hr class="mt-4 mb-4 text-gray-300">
                    <h2 class="font-medium text-xl text-gray-700">Средства для ухода за посудомоечной машиной в подарок</h2>
                    <hr class="mt-4 mb-4 text-gray-300">
                    <h2 class="font-medium text-xl text-gray-700">Решили убраться? Получите за это подарок!</h2>
                </div>
            </div>
        {{-- right sidebar end --}}
        </div>
    </div>

@endsection
