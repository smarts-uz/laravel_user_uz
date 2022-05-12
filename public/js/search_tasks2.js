$("#search_form").on("submit", function (event) {
    event.preventDefault();
    $.ajax({
        url: $(this).attr("action"),
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
            $("#dataPlace").html(data.html);
        },
        complete: function () {
            $("#loader").hide();
        },
    });
});
$("input:checkbox").click(function () {
    $("#search_form").submit();
});
$(document).ready(function () {
    $("#search_form").submit();
});
