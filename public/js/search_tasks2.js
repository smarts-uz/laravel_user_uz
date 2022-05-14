let page = 0;
$("#search_form").on("submit", function (event) {
    event.preventDefault();
    page++;
    $.ajax({
        url: $(this).attr("action") + "?page=" + page,
        method: $(this).attr("method"),
        data: {
            data: $(this).serializeArray(),
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        beforeSend: function () {
            $("#loader").show();
        },
        success: function (data) {
            $("#loadData").remove();
            $("#dataPlace").append(data.html);
        },
        complete: function () {
            $("#loader").hide();
        },
    });
});
$("input:checkbox").click(function () {
    page = 0;
    $("#dataPlace").html("");
    $("#search_form").submit();
});
$(document).ready(function () {
    page = 0;
    $("#dataPlace").html("");
    $("#search_form").submit();
});
$("#findBut").click(function () {
    page = 0;
    $("#dataPlace").html("");
    $("#search_form").submit();
});
