let page = 1;
function loadTask(event) {
    event.preventDefault();
    $.ajax({
        url: $("#search_form").attr("action") + "?page=" + page,
        method: $("#search_form").attr("method"),
        data: {
            data: $("#search_form").serializeArray(),
        },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        dataType: "json",
        beforeSend: function () {
            $("#loader").show();
            $("#loadData").remove();
        },
        success: function (data) {
            $("#dataPlace").append(data.html);
        },
        complete: function () {
            $("#loader").hide();
        },
    });
}
$("#search_form").on("submit", function (e) {
    page = 1;
    $("#dataPlace").html("");
    loadTask(e);
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
});
