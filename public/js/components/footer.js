$(window).on('scroll',function(){
    let scroll=$(window).scrollTop();
    if(scroll>130){
        $(".scroll-up-btn").addClass("show");
    }
    else{
        $(".scroll-up-btn").removeClass("show");
    }
})

$(".scroll-up-btn").click(function(){
    $('html').animate({scrollTop:0}, 900);
})