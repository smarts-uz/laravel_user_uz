function getMapData() {
    var result = false
    $.ajax({
        url: $("#map_route").text(),
        dataType: "json",
        type: "get",
        async: false,
        success: function (data) {
            result = data
        },
    });

    return result
}


function init() {

    var data = getMapData()
    var points = []
    for (let i = 0; i < data.length; i++) {
        points.push([
            data[i].latitude, data[i].longitude
        ])
    }

    var multiRoute = new ymaps.multiRouter.MultiRoute({
        referencePoints: points,
        // Routing options.
        params: {
            // Limit on the maximum number of routes returned by the router.
            results: 2
        }
    }, {
        // Automatically set the map boundaries so the entire route is visible.
        boundsAutoApply: true
    });

    // Creating buttons for controlling the multiroute.
    var trafficButton = new ymaps.control.Button({
            data: {content: "Considering traffic"},
            options: {selectOnClick: true}
        }),
        viaPointButton = new ymaps.control.Button({
            data: {content: "Adding a throughpoint"},
            options: {selectOnClick: true}
        });

    // Declaring handlers for the buttons.
    trafficButton.events.add('select', function () {
        /**
         * Setting routing parameters for the multiroute model.
         * @see https://api.yandex.com/maps/doc/jsapi/2.1/ref/reference/multiRouter.MultiRouteModel.xml#setParams
         */
        multiRoute.model.setParams({avoidTrafficJams: true}, true);
    });

    trafficButton.events.add('deselect', function () {
        multiRoute.model.setParams({avoidTrafficJams: false}, true);
    });

    viaPointButton.events.add('select', function () {
        var referencePoints = multiRoute.model.getReferencePoints();
        referencePoints.splice(1, 0, "Moscow, Solyanka Street, 7");
        /**
         * Adding a throughpoint to the multiroute model.
         * Note that throughpoints can only be placed between two waypoints.
         * In other words, they can't be end points on a route.
         * @see https://api.yandex.com/maps/doc/jsapi/2.1/ref/reference/multiRouter.MultiRouteModel.xml#setReferencePoints
         */
        multiRoute.model.setReferencePoints(referencePoints, [1]);
    });

    viaPointButton.events.add('deselect', function () {
        var referencePoints = multiRoute.model.getReferencePoints();
        referencePoints.splice(1, 1);
        multiRoute.model.setReferencePoints(referencePoints, []);
    });


    // Creating the map with the button added to it.
    var myMap = new ymaps.Map('map', {
        center: [data[0].latitude, data[0].longitude],
        zoom: 12,
        type: 'yandex#hybrid',

        controls: ['zoomControl'],
    }, {
        buttonMaxWidth: 300
    });

    // Adding a multiroute to the map.
    myMap.geoObjects.add(multiRoute);

    //
    // var myMap = new ymaps.Map("map", {
    //     center: [data[0].latitude,data[0].longitude],
    //     zoom: 6,
    //     controls: ['zoomControl'],
    //     type: 'yandex#hybrid',
    //     restrictMapArea: [59.838, 29.511],
    //
    // })
    //
    //
    // for (let i = 0; i < data.length; i++) {
    //     myMap.geoObjects
    //         .add(new ymaps.Placemark([data[i].latitude,data[i].longitude], {
    //             balloonContent: data[i].location
    //         }, {
    //             preset: 'islands#icon',
    //             iconColor: '#0095b6'
    //         }));
    // }
}

ymaps.ready(init);
