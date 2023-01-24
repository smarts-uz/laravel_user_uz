// profile text area
function fileupdate(){
    var x = document.getElementById("buttons");
    x.style.display = "block";
}
$('#padd').click(function(){
    $('.desc').addClass('hidden')
    $('.formdesc').removeClass('hidden').addClass('block')
});
$('#s2').click(function(event){
    event.preventDefault();
    $('.desc').addClass('block').removeClass('hidden');
    $('.formdesc').removeClass('block').addClass('hidden')
});
