let k=1, m=1, r=0;
let dataGeo = [], userCoordinates = [[],[]];
$('.all_cat').click();
$(".for_check input:checkbox").each(function() {
    this.checked = true;
});

$("#svgClose").click(function() {
    $('#filter').val('');
    $('#svgClose').hide();
});

$("#suggest").keyup(function() {
    if ($('#suggest').val().trim().length == 0) {
        $('#closeBut').hide();
        $('#geoBut').show();
    }else{
        $('#geoBut').hide();
        $('#closeBut').show();
    }
});

$("#price").keyup(function() {
    if ($('#price').val().trim().length == 0) {
        $('#prcClose').hide();
    }else{
        $('#prcClose').show();
    }
});

$("#geoBut").click(function() {
    $('#closeBut').show();
    $('#geoBut').hide();
});

$("#geobut2").click(function() {
    $('#closeBut2').show();
    $('#geobut2').hide();
});

$("#closeBut").click(function() {
    $('#suggest').val('');
    $('#user_lat').val('');
    $('#user_long').val('');
    $('#closeBut').hide();
    $('#geoBut').show();
});

$("#closeBut2").click(function() {
    $('#suggest2').val('');
    $('#closeBut2').hide();
    $('#geobut2').show();
});

$("#selectGeo").change(function() {
    r = $('#selectGeo').val();
    map_reset(k);
});

$("#prcClose").click(function() {
    $('#price').val('');
    $('#prcClose').hide();
});

$("#byDate").click(function() {
    /*dataAjaxSortByDS(dataAjaxPrint, 1)*/
    $('#byDate').attr('disabled','disabled');
    $('#bySearch').removeAttr('disabled');
    $("#sortBySearch").prop("checked", false);
});
$("#bySearch").click(function() {
    /*dataAjaxSortByDS(dataAjaxPrint, 2)*/
    $('#bySearch').attr('disabled','disabled');
    $('#byDate').removeAttr('disabled');
    $("#sortBySearch").prop("checked", true);
});

$(".rotate").click(function() {
    $(this).toggleClass("rotate-[360deg]");
});

$('.all_cat').click(function() {
    if (this.checked == false) {
        $(".for_check input:checkbox").each(function() {
            this.checked = false;
        });
    } else {
        $(".for_check input:checkbox").each(function() {
            this.checked = true;
        });
    }
});

$('.par_cat').click(function() {
    if(!this.checked) {
        parcats_click_false(this.id, this.name);
    } else {
        parcats_click_true(this.id);
    }
});

$('.chi_cat').click(function() {
    if(!this.checked) {
        chicats_click_false(this.id);
    } else {
        chicats_click_true(this.id, this.name);
    }
});

function parcats_click_true(id) {
    $('.chi_cat').each(function() {
        if (this.id === id) {
            this.checked = true;
        }
    });
    $('.all_cat').each(function() {
        if (parcat_check()) {
            this.checked = true;
        } else {
            this.checked = false;
        }
    });
}

function parcats_click_false(id) {
    $('.par_cat').each(function() {
        if (this.id === id) {
            this.checked = false;
        }
    });
    $('.all_cat').each(function() {
        this.checked = false;
    });
    $('.chi_cat').each(function() {
        if (this.id === id) {
            this.checked = false;
        }
    });
}

function parcat_check() {
    let i = 1;
    $('.par_cat').each(function() {
        if (!this.checked) {
            i = 0;
            return false;
        }
    });
    return i;
}

function chicats_click_true(id, name) {
    $('.chi_cat').each(function() {
        if (this.name === name) {
            this.checked = true;
        }
    });
    $('.par_cat').each(function() {
        if (this.id === id) {
            if (chicat_check(id))
            {this.checked = true;}
        }
    });
    $('.all_cat').each(function() {
        if (parcat_check()) {
            this.checked = true;
        } else {
            this.checked = false;
        }
    });
}

function chicats_click_false(id) {
    $('.par_cat').each(function() {
        if (this.id === id) {
            this.checked = false;
        }
    });
    $('.all_cat').each(function() {
        this.checked = false;
    });
}

function chicat_check(id) {
    let i = 1;
    $('.chi_cat').each(function() {
        if (this.id === id) {
            if (!this.checked) {
                i = 0;
                return false;
            }
        }
    });
    return i;
}

function chicat_check_print() {
    let i = 0;
    $('.chi_cat').each(function() {
        if (this.checked) {
            i = 1;
            return false;
        }
    });
    return i;
}

function firstCoordinates(){
    ymaps.ready(init);
    function init() {
        let location = ymaps.geolocation;
        location.get({
            mapStateAutoApply: true
        })
            .then(
                function(result) {
                    userCoordinates = result.geoObjects.get(0).geometry.getCoordinates();
                    $("#user_lat").val(userCoordinates[0]);
                    $("#user_long").val(userCoordinates[1]);
                    $("#geoBut").click(); 
                    location.get({
                        mapStateAutoApply: true
                    })
                        .then(
                            function(result) {
                                document.getElementById("suggest").value = result.geoObjects.get(0).properties.get('text');
                                userCoordinates = result.geoObjects.get(0).geometry.getCoordinates();
                                $("#user_lat").val(userCoordinates[0]);
                                $("#user_long").val(userCoordinates[1]);
                                myMap2.geoObjects.add(result.geoObjects);
                                /*myMap2.setCenter(result.geoObjects.get(0).geometry.getCoordinates());*/
                            },
                            function(err) {
                                console.log('Ошибка: ' + err);
                            }
                        );
                    $("#search_form").submit();
                    
                },
                function(err) {
                    console.log('Ошибка: ' + err);
                }
            );
    }
}

function map_reset(mapReset){
    $(".small-map").empty();
    $(".big-map").empty();
    $(".mobile-map").empty();
    mapReset === 1
    ? $(".small-map").append(
        `<div id="map" class="h-60 my-5 rounded-lg w-full static">
                <div class="relative float-right z-10 ml-1">
                    <img src="/images/big-map.png" class="hover:cursor-pointer bg-white w-8 h-auto mt-2 mr-2 p-1 rounded-md drop-shadow-lg" title="Kartani kattalashtirish" onclick="k = 2; map_reset(k);"/>
                </div>
             </div>`
    )
    :$(".big-map").append(
        `<div id="map" class="h-80 my-5 rounded-lg w-3/3 static align-items-center">
                <div class="relative float-right z-10 ml-1">
                    <img src="/images/small-map.png" class="hover:cursor-pointer bg-white w-8 h-auto mt-2 mr-2 p-1 rounded-md drop-shadow-lg" title="Kartani kichiklashtirish" onclick="k = 1; map_reset(k)"/>
                </div>
             </div>`
    );

    $(".mobile-map").append(
        `<div id="map" class="h-80 my-5 rounded-lg w-3/3 static align-items-center"></div>`
    );
    map_show();
}

function map_show() {
        ymaps.ready(init);
        function init() {
            let location = ymaps.geolocation;
            if (userCoordinates[0].length === 0) {
                location.get({
                    mapStateAutoApply: true
                })
                    .then(
                        function (result) {
                            userCoordinates = result.geoObjects.get(0).geometry.getCoordinates();
                        },
                        function (err) {
                            console.log('Ошибка: ' + err);
                        }
                    );
            }

            let myMap2 = new ymaps.Map('map', {
                center: [userCoordinates[0], userCoordinates[1]],
                zoom: 13,
                controls: [],
                // controls: ['zoomControl','geolocationControl'],
                // behaviors: ['default', 'scrollZoom']
            }, {
                searchControlProvider: 'yandex#search'
            });

            var suggestView = new ymaps.SuggestView('suggest',{boundedBy: myMap2.getBounds()});
            suggestView.events.add('select', function (e) {
                var myGeo = ymaps.geocode(e.get('item').value);
                myGeo.then(
                    function (res) {
                        userCoordinates = res.geoObjects.get(0).geometry.getCoordinates();
                        /*myMap2.geoObjects.add(res.geoObjects)*/
                        $("#user_lat").val(userCoordinates[0]);
                        $("#user_long").val(userCoordinates[1]);
                    }
                );
            });

            $("#geoBut").click(function(){
                location.get({
                    mapStateAutoApply: true
                })
                    .then(
                        function(result) {
                            document.getElementById("suggest").value = result.geoObjects.get(0).properties.get('text');
                            userCoordinates = result.geoObjects.get(0).geometry.getCoordinates();
                            $("#user_lat").val(userCoordinates[0]);
                            $("#user_long").val(userCoordinates[1]);
                            myMap2.geoObjects.add(result.geoObjects);
                            /*myMap2.setCenter(result.geoObjects.get(0).geometry.getCoordinates());*/
                        },
                        function(err) {
                            console.log('Ошибка: ' + err);
                        }
                    );
            });

            clusterer = new ymaps.Clusterer({
                preset: 'islands#invertedGreenClusterIcons',
                // hasBalloon: false,
                gridSize: 80,
                groupByCoordinates: false,
                clusterDisableClickZoom: true,
                clusterHideIconOnBalloonOpen: false,
                geoObjectHideIconOnBalloonOpen: false
            });
            getPointData = function (index) {
            let sd='', ed='';
                if(dataGeo[index].start_date) {
                    sd = 'Начать ' + dataGeo[index].start_date;
                }else{
                    sd = '';
                }
                if(dataGeo[index].end_date) {
                    ed = 'Закончить ' + dataGeo[index].end_date;
                }else{
                    ed = '';
                }
                return {
                    balloonContentBody: '<br><font size=4><b><a class="text-blue-500" href="/detailed-tasks/' + dataGeo[index].id + '">' + dataGeo[index].name + '</a></b></font><br><br><font size=3><p>'+sd+'</p><p>' + ed + '</p></font><br><font size=3><p>до ' + dataGeo[index].budget + ' сум</p></font>',
                    clusterCaption: 'Задания <strong>' + dataGeo[index].id + '</strong>'
                };
            }
            getPointOptions = function () {
                return {
                    preset: 'islands#greenIcon'
                };
            }

            geoObjects = [];
            if (dataGeo.length != 0) {
                for (var i = 0; i < dataGeo.length; i++) {
                    geoObjects[i] = new ymaps.Placemark([dataGeo[i].latitude,dataGeo[i].longitude], getPointData(i), getPointOptions());
                }
            }

            /*clusterer.options.set({
            });*/

            clusterer.add(geoObjects);
            myMap2.geoObjects.add(clusterer);
            myMap2.setBounds(clusterer.getBounds(), {
                boundsAutoApply: true,
                checkZoomRange: true
            });

            circle = new ymaps.Circle([userCoordinates, r*1000], null, { draggable: false, fill: false, outline: true, strokeColor: '#32CD32', strokeWidth: 3});
            myMap2.geoObjects.add(circle);
        }
}

// script for mobile

$(document).ready(function() {
    $("#show").click(function() {
        map1_show();
        $("#hide").css('display', 'block');
        $("#show").css('display', 'none');
        $("#scrollbar").css('display', 'none');
        $("footer").css('display', 'none');
        $('#big-big').removeClass("hidden");
    });
    $("#hide").click(function() {
        $('#big-big').addClass("hidden");
        $("#hide").css('display', 'none');
        $("#show").css('display', 'block');
        $("#scrollbar").css('display', 'block');
        $("footer").css('display', 'block');
    });
});

$(document).ready(function() {
    $("#show_2").click(function() {
        $("#hide_2").css('display', 'block');
        $("#show_2").css('display', 'none');
        $("#mobile_bar").css('display', 'block');
    });
    $("#hide_2").click(function() {
        $("#hide_2").css('display', 'none');
        $("#show_2").css('display', 'block');
        $("#mobile_bar").css('display', 'none');
    });
});

$(document).ready(function() {
    $("#show").click(function() {
        map1_show();
        $("#hide").css('display', 'block');
        $("#show").css('display', 'none');
        $("#scrollbar").css('display', 'none');
        $("footer").css('display', 'none');
        $('#big-big').removeClass("hidden");
    });
    $("#hide").click(function() {
        $('#big-big').addClass("hidden");
        $("#hide").css('display', 'none');
        $("#show").css('display', 'block');
        $("#scrollbar").css('display', 'block');
        $("footer").css('display', 'block');
    });
});

$('#byDate').click(function(){
    $(this).addClass('font-bold')
    $('#bySearch').removeClass('font-bold')
})
$('#bySearch').click(function(){
    $(this).addClass('font-bold')
    $('#byDate').removeClass('font-bold')
})

$('.has-clear input[type="text"]').on('input propertychange', function() {
    var $this = $(this);
    var visible = Boolean($this.val());
    $this.siblings('.form-control-clear').toggleClass('hidden', !visible);
}).trigger('propertychange');

$('.form-control-clear').click(function() {
    $(this).siblings('input[type="text"]').val('')
        .trigger('propertychange').focus();
});



/* SaidMuxammad code lari*/
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
            $("#dataPlace").append(data.html);
            dataGeo.push.apply(dataGeo, data.dataForMap);
            map_reset(k);
        },
        complete: function () {
            $("#loader").hide();
        },
    });
}
$("#search_form").on("submit", function (event) {
    page = 1;
    dataGeo = [];
    $("#dataPlace").html(" ");
    loadTask(event);
});

$("input:checkbox").click(function () {
    $("#search_form").submit();
});

$(document).ready(function () {
    $("#loader").show();
    $("#selectGeo").change();
    firstCoordinates();
});
/*$(window).load(function () {
});*/

$("#search_form").on("click", "#loadMoreData", function (e) {
    page++;
    loadTask(e);
    $(this).attr("disabled", "disabled");
});

$("#byDate").click(function () {
    $("#sortBySearch").prop("checked", false);
    $("#search_form").submit();
});
$("#bySearch").click(function () {
    $("#sortBySearch").prop("checked", true);
    $("#search_form").submit();
});

$("#remjob").click(function(){
    if($('#remjob').is(':checked')){
        $(".disalable").find("select,input").prop("disabled",true)
        $(".disalable").find("select,input").addClass("bg-gray-200 relative z-10 cursor-not-allowed")
    }
    if($('#remjob').is(':not(:checked)')){
        $(".disalable").find("select,input").prop("disabled",false)
        $(".disalable").find("select,input").removeClass("bg-gray-200 relative z-10 cursor-not-allowed")
    }
})
$('#selectGeo').on('change', function() {
 if(this.value===""){
    $("#suggest").prop("disabled",true).addClass("bg-gray-200 relative z-10 cursor-not-allowed")
 }
 else{
    $("#suggest").prop("disabled",false).removeClass("bg-gray-200 relative z-10 cursor-not-allowed")
 }
 $("#search_form").submit();
  });
