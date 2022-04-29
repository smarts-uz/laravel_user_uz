// profile text area
function fileupdate(){
    var x = document.getElementById("buttons");
    x.style.display = "block";
}
function fileadd(){
    var x = document.getElementById("buttons");
    x.classList.add("hidden");
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
$("#button_comment").click(function(event){
    let comment = $("input[name=comment]").val();
    $.ajax({
        url: "{{route('profile.comment')}}",
        type:"POST",
        data:{
            comment:comment,
            _token:$('meta[name="csrf-token"]').attr('content'),
        },
    });
    toggleModal6('modal-id6');
});
        // tabs
        function openCity(evt, cityName) {
            var index, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (index = 0; index < tabcontent.length; index++) {
              tabcontent[index].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablinks");
            for (index = 0; index < tablinks.length; index++) {
              tablinks[index].className = tablinks[index].className.replace("bg-yellow-200 text-gray-900", "");
            }
            document.getElementById(cityName).style.display = "block";
            evt.currentTarget.className += "bg-yellow-200 text-gray-900";
          }
        //tabs end
