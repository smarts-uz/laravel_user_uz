//   input eye
$(function () {

    $('#eye').click(function () {
        if ($(this).hasClass('fa-eye-slash')) {
            $(this).removeClass('fa-eye-slash');
            $(this).addClass('fa-eye');
            $('#phone_number').attr('type', 'text');
        } else {
            $(this).removeClass('fa-eye');
            $(this).addClass('fa-eye-slash');
            $('#phone_number').attr('type', 'password');
        }
    });

});
 //input eye2
$(function () {

    $('#eye1').click(function () {
        if ($(this).hasClass('fa-eye-slash')) {
            $(this).removeClass('fa-eye-slash');
            $(this).addClass('fa-eye');
            $('.confirm_password').attr('type', 'text');
        } else {
            $(this).removeClass('fa-eye');
            $(this).addClass('fa-eye-slash');
            $('.confirm_password').attr('type', 'password');
        }
    });

});