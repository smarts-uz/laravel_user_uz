<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.5.1/css/swiper.min.css">
<div class="h-auto" style="background: #FFF5E5">
    <div class="w-xl">
        <main class="w-11/12 mx-auto grid grid-cols-2 gap-2">
            <div class="col-span-1 lg:block hidden">
                <div class="mt-16">
                    <div class="swiper-container">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide ">
                                <div class="card-image rounded-full shadow-lg">
                                    <img class="rounded-full w-full" style="height: 400px" src="/storage/{!!str_replace("\\","/",setting('site.carusel_img1'))!!}" alt="Image Slider">
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="card-image rounded-full shadow-lg">
                                    <img class="rounded-full w-full" style="height: 400px" src="/storage/{!!str_replace("\\","/",setting('site.carusel_img2'))!!}" alt="Image Slider">
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="card-image rounded-full shadow-lg">
                                    <img class="rounded-full w-full" style="height: 400px" src="/storage/{!!str_replace("\\","/",setting('site.carusel_img3'))!!}" alt="Image Slider">
                                </div>
                            </div>
                        </div>

                        <div class="next">
                            <div class="nextt inline-block text-yellow-600 cursor-pointer -translate-x-5 bg-white rounded-full shadow-md active:translate-y-0.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div class="prev">
                            <div class="prevv inline-block text-yellow-600 cursor-pointer -translate-x-5 bg-white rounded-full shadow-md active:translate-y-0.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.707-10.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L9.414 11H13a1 1 0 100-2H9.414l1.293-1.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="lg:col-span-1 col-span-2 pt-32 relative z-10">
                {!! App\Services\CustomService::getContentText('home', 'main_text') !!}
                <div class="mx-auto">
                    <div class="xl:w-4/5 w-full flex-1 mt-8">
                        <input name="TypeList" list="TypeList" type="text" id="header_input" maxlength="40" placeholder="{{__('Чем вам помочь...')}}" onkeyup="searchTaskName()"
                               class="input_text w-full md:px-4 px-2 py-2.5 md:py-3 rounded-xl focus:placeholder-transparent focus:outline-none focus:border-yellow-500 flex-1 text-lg border-0">
                        <datalist id="TypeList">
                            @foreach($child_categories as $category)
                                <option
                                    value="{{$category->getTranslatedAttribute('name', Session::get('lang') , 'fallbackLocale')}}" id="{{ $category->id }}">{{$category->getTranslatedAttribute('name', Session::get('lang') , 'fallbackLocale')}}</option>
                            @endforeach
                        </datalist>
                        <a href="/task/create?category_id=22" id="createhref"
                           class="float-right sm:block hidden text-lg border bg-blue-900 z-10 border-transparent rounded-xl md:px-3.5 px-2 pt-2 pb-1.5 md:py-2.2 mr-1 md:mt-2 mt-2.5 -ml-24 md:-top-14 -top-14 relative text-white focus:outline-none">
                            {{__('Заказать услугу')}}
                        </a>
                        <a href="/task/create?category_id=22" id="createhref"
                           class="float-right sm:hidden block text-lg border bg-blue-900 z-10 border-transparent rounded-xl md:px-3.5 px-2 pt-2 pb-1.5 md:py-2 mr-1 md:mt-2 mt-2.5 -ml-24 -top-14 relative text-white focus:outline-none">
                           {{__(' Заказать')}}
                        </a>
                        <div class="mt-8 float-left">
                            <a href="{{ setting('site.instagram_url','') }}">
                                <i class="fab fa-instagram text-yellow-500 hover:text-yellow-600 mx-2"></i>
                            </a>
                            <a href="{{ setting('site.telegram_url','') }}">
                                <i class="fab fa-telegram text-yellow-500 hover:text-yellow-600 mx-2"></i>
                            </a>
                            <a href="{{ setting('site.youtube_url','') }}">
                                <i class="fab fa-youtube text-yellow-500 hover:text-yellow-600 mx-2"></i>
                            </a>
                            <a href="{{ setting('site.facebook_url','') }}">
                                <i class="fab fa-facebook text-yellow-500 hover:text-yellow-600 mx-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.5.1/js/swiper.min.js"></script>
<script>
    // Swiper Configuration
    var swiper = new Swiper(".swiper-container", {
        slidesPerView: 1,
        spaceBetween: 10,
        centeredSlides: true,
        speed: 700,
        loop: true,
        autoplay: {
            delay: 3000,
            disableOnInteraction: false
        },
        navigation: {
            nextEl: " .next",
            prevEl: ".prev",
        },
    });

    function searchTaskName()
    {
        $.ajax({
            url: '{{route('search.task_name')}}',
            data: {name: $("#header_input").val()},
            success: function (res) {
                $("#TypeList").html(res)
            },
        });
    }

</script>
