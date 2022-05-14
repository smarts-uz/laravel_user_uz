let page = 1;
let request = null;
function loadTask(event) {
    if (request && request.readyState != 4) {
        request.abort();
    }
    event.preventDefault();
    request = $.ajax({
        url: $("#search_form").attr("action") + "?page=" + page,
        method: $("#search_form").attr("method"),
        dataType: "json",
        data: {
            data: $("#search_form").serializeArray(),
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        beforeSend: function () {
            $("#loader").show();
            $("#loadData").remove();
        },
        success: function (data) {
            console.log(data.dataForMap);
            $("#dataPlace").append(data.html);
        },
        complete: function () {
            $("#loader").hide();
        },
    });
}
$("#search_form").on("submit", function (event) {
    page = 1;
    $("#dataPlace").html(" ");
    loadTask(event);
});

$("input:checkbox").click(function () {
    $("#search_form").submit();
});
$(document).ready(function () {
    $("#search_form").submit();
});

$("#search_form").on("click", "#loadMoreData", function (e) {
    page++;
    loadTask(e);
    $(this).attr("disabled", "disabled");
});
