$('.change-email').click(function() {
    $('#send-data-form').css('display', 'block')
    $(this).css('display', 'none')
    $('.send-email').css('display', 'none')
})
$("#cancel-email").click(function() {
    $('#send-data-form').css('display', 'none')
    $('.change-email').css('display', "initial")
    $('.send-email').css('display', 'initial')
})
var element = document.getElementById('phone_number');
var maskOptions = {
    mask: '+998(00)000-00-00',
    lazy: false
}
if (element) {
    var mask = new IMask(element, maskOptions);
}
