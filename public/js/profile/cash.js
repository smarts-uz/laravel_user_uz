 // input cash
function inputCash() {
    var x = document.getElementById("myText1").value;
    let amount = MIN_AMOUNT;
    if (PAYMENT_TEST) {
        amount = 1000;
    }
    if (isNaN(x) || isNaN(parseFloat(x)) || parseInt(x) < parseInt(amount)) {
        document.getElementById('button2').removeAttribute("onclick");
        document.getElementById('button2').classList.remove("bg-green-500","hover:bg-green-600");
        document.getElementById('button2').classList.add("bg-gray-500");
    } else {
        document.getElementById('button2').setAttribute("onclick", "toggleModal();");
        document.getElementById('button2').classList.remove("bg-gray-500");
        document.getElementById('button2').classList.add("bg-green-500","hover:bg-green-600");
    }
}
    // file upload
    function fileupdate() {
        var x = document.getElementById("buttons");
        x.style.display = "block";
    }

    // tab content
     let tabsContainer = document.querySelector("#tabs");
     let tabTogglers = tabsContainer.querySelectorAll("#tabs a");
     tabTogglers.forEach(function(toggler) {
         toggler.addEventListener("click", function(e) {
             e.preventDefault();
             let tabName = this.getAttribute("href");
             let tabContents = document.querySelector("#tab-contents");
             for (let i = 0; i < tabContents.children.length; i++) {
                 tabTogglers[i].parentElement.classList.remove("bg-gray-400", "rounded-sm",
                     "text-white");
                 tabContents.children[i].classList.remove("hidden");
                 if ("#" + tabContents.children[i].id === tabName) {
                     continue;
                 }
                 tabContents.children[i].classList.add("hidden");
             }
             e.target.parentElement.classList.add("bg-gray-400", "rounded-sm", "text-white");
         });
     });

// input cash transaction
$('#period').change(function(){
    if($(this).val() === 'date-period'){
        $('#ddr').removeClass('hidden');
    }
    else{
        $('#ddr').addClass('hidden');
    }
});

 function getTransactionsByDate (method, period, table_num) {
     if (period === 'date-period') {
         var data = {
             from_date: $('#from-date').val(),
             to_date: $('#to-date').val(),
             method: method
         };
     } else {
         var data = {
             period: period,
             method: method
         };
     }
     getTransactions(data, table_num);
 }

 $(document).ready(function () {

     $('a.payment-type').on('click', function () {
         $('div.w-full').removeClass('active');
         $(this).parent().addClass('active');

         var table_num = $(this).attr('data-number');
         var method = $(this).attr('data-method');
         var period = $('select#period').val();

         getTransactionsByDate(method, period, table_num);
     });

     $('select#period').on('change', function () {
         if ($('div.w-full.active').length == 1) {
             var a = $('div.w-full.active').find('a.payment-type');
             var table_num = a.attr('data-number');
             var method = a.attr('data-method');
             var period = $(this).val();
             getTransactionsByDate(method, period, table_num);
         }
     });

     $('#from-date').on('change', function () {
         if ($('div.w-full.active').length == 1) {
             var a = $('div.w-full.active').find('a.payment-type');
             var table_num = a.attr('data-number');
             var method = a.attr('data-method');
             var from_date = $('#from-date').val();
             var to_date = $('#to-date').val();
             if (from_date !== "" && to_date !== "") {
                 var data = {
                     from_date: from_date,
                     to_date: to_date,
                     method: method
                 };
                 console.log(data);
                 getTransactions(data, table_num);
             }
         }
     });

     $('#to-date').on('change', function () {
         if ($('div.w-full.active').length == 1) {
             var a = $('div.w-full.active').find('a.payment-type');
             var table_num = a.attr('data-number');
             var method = a.attr('data-method');
             var from_date = $('#from-date').val();
             var to_date = $('#to-date').val();
             if (from_date !== "" && to_date !== "") {
                 var data = {
                     from_date: from_date,
                     to_date: to_date,
                     method: method
                 };
                 console.log(data);
                 getTransactions(data, table_num);
             }
         }
     });
 });
