<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/restaurant.js') }}" defer></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB38R7yhcuBx3atqooETmk81J4JvQvlql8&callback=initMap&libraries=places&v=weekly" defer ></script>
<!--    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB38R7yhcuBx3atqooETmk81J4JvQvlql8&callback=initMap&libraries=places&v=weekly" defer ></script>-->


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>

    <!-- Styles -->
    <link href="{{ asset('css/restaurant.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">



</head>
<body>

    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="#">Map Restaurant</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <main role="main" id="restaurant">

        <div class="album py-5 bg-light">
            <div class="container">

                <div class="row">

                    <div class="col-12 map-center">

                            <div class="col-5">

                                <div class="">
                                    <label for="search">ค้นหาร้านอาหาร</label>
                                    <div class="form-group row">
                                        <div class="col-lg-9 col-md-7 col-sm-12">
                                            <input type="text" class="form-control" placeholder="ค้นหาร้านอาหาร" v-model="search" required="">
                                        </div>
                                        <div class="col-lg-3 col-md-5 col-sm-12 text-center">
                                            <button type="button" class="btn btn-secondary btn-block" v-on:click="searchRestaurant()">ค้นหา</button>
                                        </div>
                                    </div>
                                </div>

                                <div id="listing">
                                    <h4 class="d-flex justify-content-between align-items-center mb-3 mt-3">
                                        <span class="text-muted">ผลลัพธ์ทั้งหมด</span>
                                        <span class="badge badge-secondary badge-pill" id="totalResults">0</span>
                                    </h4>

                                    <ul class="list-group mb-3" id="results">
                                    </ul>
                                </div>

                                <div style="display: none">
                                    <div id="info-content">
                                        <table>
                                            <tr id="iw-url-row" class="iw_table_row">
                                                <td id="iw-icon" class="iw_table_icon"></td>
                                                <td id="iw-url"></td>
                                            </tr>
                                            <tr id="iw-address-row" class="iw_table_row">
                                                <td class="iw_attribute_name">Address:</td>
                                                <td id="iw-address"></td>
                                            </tr>
                                            <tr id="iw-phone-row" class="iw_table_row">
                                                <td class="iw_attribute_name">Telephone:</td>
                                                <td id="iw-phone"></td>
                                            </tr>
                                            <tr id="iw-rating-row" class="iw_table_row">
                                                <td class="iw_attribute_name">Rating:</td>
                                                <td id="iw-rating"></td>
                                            </tr>
                                            <tr id="iw-website-row" class="iw_table_row">
                                                <td class="iw_attribute_name">Website:</td>
                                                <td id="iw-website"></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-7">
                                <div id="map"></div>
                            </div>


                        </div>

                </div>
            </div>
        </div>

    </main>

    <script>

        var restaurant = new Vue({
            el: '#restaurant',
            data: {
                search: 'Bang sue',
            },
            methods: {
                searchRestaurant: function() {

                    let self = this;
                    const search = self.search;
                    let u = '/api/restaurant/searchRestaurants';
                    let data = {
                        'search': search
                    }

                    $.ajax({
                        url: u,
                        type: 'POST',
                        data: data,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res) {

                            if (res.error === 0) {

                                let marker = '';
                                let results = '';
                                let time = 0;
                                clearResults();
                                clearMarkers();
                                for (let i = 0; i < res.results.length; i++) {

                                    const markerLetter = String.fromCharCode(
                                        "A".charCodeAt(0) + (i % 26)
                                    );
                                    // const markerIcon = MARKER_PATH + markerLetter + ".png";
                                    const markerIcon = MARKER_PATH;
                                    // Use marker animation to drop the icons incrementally on the map.
                                    markers[i] = new google.maps.Marker({
                                        position: res.results[i].geometry.location,
                                        animation: google.maps.Animation.DROP,
                                        // icon: markerIcon,
                                    });
                                    // If the user clicks a hotel marker, show the details of that hotel
                                    // in an info window.
                                    markers[i].placeResult = res.results[i];
                                    google.maps.event.addListener(
                                        markers[i],
                                        "click",
                                        showInfoWindow
                                    );
                                    time = i * 100
                                    // setTimeout(dropMarker(i), i * 100);
                                    setTimeout(dropMarker(i), time);
                                    addResult(res.results[i], i);

                                    let strMatch = new RegExp(search);
                                    let resMatch = res.results[i].name.match(strMatch);
                                    if (resMatch) {

                                        results = res.results[i];
                                        marker = markers[i];

                                        let lat = results.geometry.location.lat;
                                        let lng = results.geometry.location.lng;
                                        var LatlngBangSue = new google.maps.LatLng(lat, lng);
                                        map.panTo(LatlngBangSue);
                                        map.setZoom(14);

                                        places.getDetails(
                                            {placeId: marker.placeResult.place_id},
                                            (place, status) => {
                                                if (status !== google.maps.places.PlacesServiceStatus.OK) {
                                                    return;
                                                }
                                                infoWindow.open(map, marker);
                                                buildIWContent(place);
                                            }
                                        );
                                    }

                                }

                            } else {

                                console.log(res)
                            }

                        },
                        error: function(err) {

                            console.log(err.responseText)
                        }
                    });

                },
            },
            created: function () {
            },
            mounted: function () {
                this.searchRestaurant()
            }
        })

    </script>


</body>
</html>
