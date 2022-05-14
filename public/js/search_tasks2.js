let page = 1;
function loadTask(event) {
    event.preventDefault();
    $.ajax({
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
            $("#dataPlace").append(data.html);
        },
        complete: function () {
            $("#loader").hide();
        },
    });
}
$("#search_form").on("submit", function (event) {
    event.preventDefault();
    $.ajax({
        url: $(this).attr("action") + "?page=" + 1,
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
            $("#dataPlace").html("");
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

$("#search_form").on("click", "#loadMoreData", function (e) {
    page++;
    loadTask(e);
    $(this).attr("disabled", "disabled");
});
