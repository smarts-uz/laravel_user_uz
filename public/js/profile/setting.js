

let tabsContainer = document.querySelector("#tabs");

let tabTogglers = tabsContainer.querySelectorAll("a");


tabTogglers.forEach(function (toggler) {
    toggler.addEventListener("click", function (e) {
        e.preventDefault();

        let tabName = this.getAttribute("href");

        let tabContents = document.querySelector("#tab-contents");

        for (let i = 0; i < tabContents.children.length; i++) {

            tabTogglers[i].parentElement.classList.remove("border-blue-400", "border-b", "-mb-px", "opacity-100");
            tabContents.children[i].classList.remove("hidden");
            if ("#" + tabContents.children[i].id === tabName) {
                continue;
            }
            tabContents.children[i].classList.add("hidden");

        }
        e.target.parentElement.classList.add("border-blue-400", "border-b-4", "-mb-px", "opacity-100");
    });
});

document.getElementById("default-tab").click();
var element = document.getElementById('phone_number');
var maskOptions = {
    mask: '+998 00 000-00-00',
    lazy: false
}
var mask = new IMask(element, maskOptions);

if ($('#tab-contents').children(".error").length) {
    $('#tab-contents').children('.tab-pane').addClass('hidden')
    $('.error').removeClass('hidden')
    $('#tabs').children('.tab-name').removeClass("border-blue-400 border-b-4  -mb-px opacity-100")
    $('#tabs').children('.error').addClass("border-blue-400 border-b-4  -mb-px opacity-100")

}

function fileupdate() {
    var x = document.getElementById("buttons");
    x.style.display = "block";
}
function toggleModal111() {
    document.getElementById("modal-id111").classList.toggle("hidden");
    document.getElementById("modal-id111" + "-backdrop").classList.toggle("hidden");
    document.getElementById("modal-id111").classList.toggle("flex");
    document.getElementById("modal-id111" + "-backdrop").classList.toggle("flex");
}

