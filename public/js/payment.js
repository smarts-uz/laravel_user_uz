let payment_form = $('#choose_payment_type');
payment_form.submit(function (event){
    let data = payment_form.serializeArray()
    if (data[1]['value'] == 'Paynet') {
        // event.preventDefault();
        let form_data = {user_id: data[0]['value'], amount: data[2]['value']}
        $.ajax({
            type: "POST",
            url: "{{route('paynet-transaction')}}",
            data: form_data,
            dataType: "json",
            encode: true,
        }).done(function (data) {
            alert('Transaction ID: ' + data['id'] + "  Amount: " + data['amount'])
            console.log(data);
            toggleModal1()
        });
    }
    else {
        payment_form.submit();
    }
});


function toggleModal() {
    document.getElementById("modal-id").classList.toggle("hidden");
    document.getElementById("modal-id" + "-backdrop").classList.toggle("hidden");
    document.getElementById("modal-id").classList.toggle("flex");
    document.getElementById("modal-id" + "-backdrop").classList.toggle("flex");
}

function toggleModal1() {
    var element = document.getElementById("modal-id-backdrop");
    element.classList.add("hidden");
    var element2 = document.getElementById("modal-id");
    var b = document.getElementById("myText").value;
    var u = document.getElementById("amount_u");
    u.value = b;
    element2.classList.add("hidden");
    document.getElementById("modal-id1").classList.toggle("hidden");
    document.getElementById("modal-id1" + "-backdrop").classList.toggle("hidden");
    document.getElementById("modal-id1").classList.toggle("flex");
    document.getElementById("modal-id1" + "-backdrop").classList.toggle("flex");
}

function borderColor() {
    var element = document.getElementById("demo");
    element.classList.add("border-amber-500");
}

function inputFunction() {
    var x = document.getElementById("myText").value;
    if (x < 4000) {
        document.getElementById('button').removeAttribute("onclick");
        document.getElementById('button').classList.remove("bg-green-500");
        document.getElementById('button').classList.add("bg-gray-500");
        document.getElementById('button').classList.remove("hover:bg-green-500");
        document.getElementById("button").innerHTML = "К оплате " + x + "UZS";
    } else {
        document.getElementById('button').setAttribute("onclick", "toggleModal1();");
        document.getElementById('button').classList.remove("bg-gray-500");
        document.getElementById('button').classList.add("bg-green-500");
        document.getElementById('button').classList.add("hover:bg-green-500");
        document.getElementById("button").innerHTML = "К оплате " + x + "UZS";
    }
}

function checkFunction() {
    var x = document.getElementById("myText").value;
    var checkBox = document.getElementById("myCheck");
    if (checkBox.checked == true) {
        document.getElementById("button").innerHTML = "К оплате " + (parseInt(x) + 10000);
    } else {
        document.getElementById("button").innerHTML = "К оплате " + x + "UZS";
    }
}

function paymentSubmit(event) {
    event.preventDefault();

    const data = new FormData(e.target);

    const value = data.get('paymethod');

    console.log({ value });
    // console.log($('#choose_payment_type').values())
}

function validate(evt) {
    var theEvent = evt || window.event;
    // Handle paste
    if (theEvent.type === 'paste') {
        key = event.clipboardData.getData('text/plain');
    } else {
        // Handle key press
        var key = theEvent.keyCode || theEvent.which;
        key = String.fromCharCode(key);
    }
    var regex = /[0-9]|\./;
    if (!regex.test(key)) {
        theEvent.returnValue = false;
        if (theEvent.preventDefault) theEvent.preventDefault();
    }
}
