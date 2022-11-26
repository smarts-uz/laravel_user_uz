<footer class=" w-full mx-auto mt-32" style="background-color: #242325;">
    <div class="flex md:flex-row flex-col w-4/5 mx-auto rounded-xl relative bottom-20" style="background-color: #F9FAFB">
        <div class="m-5 md:w-2/3 w-11/12">
            {!! getContentText('footer', 'footer_text') !!}
        </div>
        <div class="my-auto flex xl:flex-row flex-col md:w-1/3 w-full md:m-5 m-0">
            <a class="rounded-md mx-auto my-2 my-auto xl:mr-3" rel="noopener noreferrer" href="{{setting('site.ios_url')}}" target="_blank">
                <button type="button" class="bg-black rounded-md hover:bg-yellow-500 md:my-0 my-1">
                    <img src="{{asset('images/download_ios.svg')}}" alt="">
                </button>
            </a>
            <a class="rounded-md mx-auto my-auto my-2" rel="noopener noreferrer" href="{{setting('site.android_url')}}" target="_blank">
                <button type="button" class="bg-black rounded-md hover:bg-yellow-500 md:my-0 my-1">
                    <img src="{{asset('images/download_android.svg')}}" alt="">
                </button>
            </a>
        </div>
    </div>

   <div class="grid grid-cols-3 gap-2 w-4/5 mx-auto mb-12">
       <div class="lg:col-span-1 col-span-3">
           <a href="/"><img src="/storage/{!!str_replace("\\","/",setting('site.footer_site_logo'))!!}" class="w-40 h-14"></a>
            <p class="text-base text-gray-300 my-6 w-11/12 ml-2">{{__('У частных исполнителей нет расходов на офис, рекламу, зарплату секретарю и других затрат, которые сервисные компании обычно включают в стоимость своих услуг.')}}</p>
            <div class="">
                <a href="{{ setting('site.instagram_url') }}" class="cursor-pointer">
                    <i class="fab fa-instagram text-gray-300 hover:text-yellow-500 mx-2"></i>
                </a>
                <a href="{{ setting('site.telegram_url') }}" class="cursor-pointer">
                    <i class="fab fa-telegram text-gray-300 hover:text-yellow-500 mx-2"></i>
                </a>
                <a href="{{ setting('site.youtube_url') }}" class="cursor-pointer">
                    <i class="fab fa-youtube text-gray-300 hover:text-yellow-500 mx-2"></i>
                </a>
                <a href="{{ setting('site.facebook_url') }}" class="cursor-pointer">
                    <i class="fab fa-facebook text-gray-300 hover:text-yellow-500 mx-2"></i>
                </a>
            </div>
       </div>
       <div class="md:col-span-1 col-span-3 flex flex-col md:mx-auto md:mx-0 lg:mt-0 mt-8">
           <div class="mb-3">
               <div><i class="fas fa-phone-alt  text-gray-300"></i><span class="font-bold text-gray-300 text-lg ml-4">{{__('Телефон')}}</span></div>
               <span class="text-lg text-gray-300">+998 (91) 480 96 96</span>
           </div>
           <div class="my-3">
               <div><i class="far fa-envelope text-gray-300"></i><span class="font-bold text-gray-300 text-lg ml-4">{{__('Эл.Почта')}}</span></div>
               <span class="text-lg text-gray-300">useruz@user.uz</span>
           </div>
           <div class="my-3">
               <div><i class="fas fa-map-marker-alt text-gray-300"></i><span class="font-bold text-gray-300 text-lg ml-4">{{__('Локация')}}</span></div>
               <span class="text-lg text-gray-300">{{__('город Фергана')}}</span>
           </div>
       </div>
       <div class="md:col-span-1 col-span-3 flex flex-col md:mx-auto md:mx-0 lg:mt-0 mt-8">
            <a class="text-gray-300 hover:text-yellow-400 text-lg mb-2" href="/verification">{{__('Как стать исполнителем')}}</a>
            <a class="text-gray-300 hover:text-yellow-400 text-lg my-2" href="/news">{{__('Новости сайта')}}</a>
            <a class="text-gray-300 hover:text-yellow-400 text-lg my-2" href="/geotaskshint">{{__('Как это работает')}}</a>
            <a class="text-gray-300 hover:text-yellow-400 text-lg my-2" href="/author-reviews">{{__('Отзывы заказчиков')}}</a>
            @if(Auth::check())
               <a class="text-gray-300 hover:text-yellow-400 text-lg my-2 chat" href="">{{__('Служба поддержки')}}</a>
               <script>
                   const chatPanel = (event) => {
                       jsPanel.create({
                           content: `<iframe src="{{url('/chat/' . setting('site.moderator_id'))}}" frameborder="0" style="width: 100%; height: 100%"></iframe>`,
                           theme: 'primary',
                           position: 'center',
                           closeOnEscape: true,
                           headerTitle: 'Universal Services',
                           headerControls: {
                               size: 'md',
                           },
                           borderRadius: '1rem',
                           panelSize: {
                               width: '80vw',
                               height: '90vh'
                           },
                           contentSize: '80vw 90vh',
                       });
                       event.preventDefault();
                   }
                   const chat = document.querySelector('.chat');
                   chat.addEventListener('click', chatPanel);
               </script>
            @else
               <a href="/badges" class="text-gray-300 hover:text-yellow-400 text-lg my-2">{{__('Награды и рейтинг')}}</a>
            @endif
       </div>
   </div>

    <div class="text-center h-12" style="background-color: #1B1B1C">
            <h1 class="text-center text-sm py-4" style="color: #857F7F">
                © 2022 Universal Services (USer.Uz)
                <a href="/terms">{{__('Правила сервиса')}}</a>
            </h1>
    </div>

    <div class="w-full md:block hidden">
        <div class="scroll-up-btn">
            <span><i class="fas fa-angle-up"></i></span>
        </div>
    </div>
    @include('components.message')
    <link rel="stylesheet" href="{{asset('css/scroll.css')}}">
    <script src="{{ asset('js/components/footer.js') }}"></script>
</footer>
