<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/raty/3.1.1/jquery.raty.min.css"
      integrity="sha512-XsO5ywONBZOjW5xo5zqAd0YgshSlNF+YlX39QltzJWIjtA4KXfkAYGbYpllbX2t5WW2tTGS7bmR0uWgAIQ8JLQ=="
      crossorigin="anonymous" referrerpolicy="no-referrer"/>
<script src="https://code.jquery.com/jquery-3.6.0.js"
        integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-raty-js@2.8.0/lib/jquery.raty.min.js"></script>

<script>
    @foreach ($users as $user)
    $("#stars{{$user->id}}").raty({
        path: 'https://cdn.jsdelivr.net/npm/jquery-raty-js@2.8.0/lib/images',
        readOnly: true,
        score: {{$user->review_rating ?? 0}},
        size: 12
    });
    @endforeach
</script>
<script>
    @foreach ($categories as $category)
    $("#{{ preg_replace('/[ ,]+/', '', $category->name) }}").click(function () {
        if ($("#{{$category->slug}}").hasClass("hidden")) {
            $("#{{$category->slug}}").removeClass('hidden');
        } else {
            $("#{{$category->slug}}").addClass('hidden');
        }
    });
    @endforeach
</script>
<script type="text/javascript">
    function toggleModal12(modalID12) {
        document.getElementById(modalID12).classList.toggle("hidden");
        document.getElementById(modalID12 + "-backdrop").classList.toggle("hidden");
        document.getElementById(modalID12).classList.toggle("flex");
        document.getElementById(modalID12 + "-backdrop").classList.toggle("flex");
    }
</script>
<script> //Bu scriptda Active Performers id lari User table dan Ajax orqali chaqililadi va ekranga chiqaziladi.
    let activePerformersId = [];
    $('#online').click(function () {
        let id, find;
        if (this.checked == true) {
            $.ajax({
                url: "{{route('performers.active_performers')}}",
                type: 'GET',
                success: function (data) {
                    activePerformersId = $.parseJSON(JSON.stringify(data));
                    $('.difficultTask').each(function () {
                        id = $(this).attr('id');
                        find = 0;
                        $.each(activePerformersId, function (index, activePerformersId) {
                            if (activePerformersId.id == id) {
                                find = 1;
                            }
                        });
                        if (find) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                },
                error: function (error) {
                    console.error("Ajax orqali yuklashda xatolik...", error);
                }
            });
        } else {
            $('.difficultTask').each(function () {
                $(this).show();
            });
        }
    });
</script>

<script>
    @foreach($users as $user)
    $("#open{{$user->id}}").click(function () {
        var username = $(".{{$user->id}}").text();
        var namem = $(".namem").text('{{__('Вы предложили задание исполнителю')}}' + username);
        $(".modal_content").show();
        let user_id = $('#performer_id').val();
        $.ajax({
            url: "/give-task",
            type: "POST",
            data: {
                user_id: user_id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                console.log(response);
                if (response) {
                    $('.success').text(response.success);
                }
            },
            error: function (error) {
                console.log(error);
            }
        });
    });
    $(".close").click(function () {
        $(".modal_content").hide();
    });
    @endforeach
</script>
<script type="text/javascript">
    function showDiv(select) {
        if (select.value == 0) {
            document.getElementById('hidden_div').style.display = "block";
        }
        if (select.value == 1) {
            document.getElementById('hidden_div').style.display = "none";
            document.getElementById('hidden_div2').style.display = "block";
        } else {
            document.getElementById('hidden_div2').style.display = "none";
            document.getElementById('hidden_div').style.display = "block";

        }
    }
</script>

<script>
    function myFunc() {
        document.getElementById('modal').style.display = "block";
        document.getElementById('modal_content').style.display = "none";
        let task_id = $("#task_name").val();
        $.ajax({
            url: "/give-task",
            type: "POST",
            data: {
                task_id: task_id,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                console.log(response);
                if (response) {
                    $('.success').text(response.success);
                }
            },
            error: function (error) {
                console.log(error);
            }
        });
    };

    function myFunction1() {
        document.getElementById('modal').style.display = "none";
        document.getElementById('modal_content').style.display = "none";
    };
</script>
