let map;
let places;
let infoWindow;
let markers = [];
let autocomplete;
const MARKER_PATH = "http://maps.google.com/mapfiles/marker.png";
const hostnameRegexp = new RegExp("^https?://.+?/");

$( document ).ready(function() {

    setTimeout(function(){

        // set default marker Bang Sue
        var LatlngBangSue = new google.maps.LatLng(13.828253, 100.5284507);
        map.panTo(LatlngBangSue);
        map.setZoom(14);
    }, 200);
});

function initMap() {

    map = new google.maps.Map(document.getElementById("map"), {
        zoom: 14,
        center: {lat: 13.8126759, lng: 100.5256721},
        mapTypeControl: false,
        panControl: false,
        zoomControl: false,
        streetViewControl: false,
    });

    infoWindow = new google.maps.InfoWindow({
        content: document.getElementById("info-content"),
    });

    places = new google.maps.places.PlacesService(map);
}

// When the user selects a city, get the place details for the city and
// zoom the map in on the city.
function onPlaceChanged() {
    const place = autocomplete.getPlace();

    let dataSearch = {
        'name': place.name,
        'lat': place.geometry.location.lat,
        'lng': place.geometry.location.lng
    }
    saveSearch(dataSearch)

    if (place.geometry) {
        map.panTo(place.geometry.location);
        map.setZoom(14);
        search();
    } else {
        document.getElementById("autocomplete").placeholder = "Enter a Restaurant";
    }
}

function clearMarkers() {
    for (let i = 0; i < markers.length; i++) {
        if (markers[i]) {
            markers[i].setMap(null);
        }
    }
    markers = [];
}

function dropMarker(i) {
    return function () {
        markers[i].setMap(map);
    };
}

function addResult(result, i) {

    const results = document.getElementById("results");
    const markerIcon = MARKER_PATH;
    const li = document.createElement("li");
    li.setAttribute("class", "list-group-item d-flex justify-content-between lh-condensed");

    li.onclick = function () {
        google.maps.event.trigger(markers[i], "click");
    };
    const icon = document.createElement("img");
    icon.src = markerIcon;
    icon.setAttribute("class", "placeIcon");
    icon.setAttribute("className", "placeIcon");
    const name = document.createElement("h6");
    name.setAttribute("class", "mt-3 w-100");
    name.innerText = result.name;
    li.appendChild(icon);
    li.appendChild(name);
    results.appendChild(li);

    document.getElementById("totalResults").innerText = (i + 1);
}

function clearResults() {
    const results = document.getElementById("results");

    while (results.childNodes[0]) {
        results.removeChild(results.childNodes[0]);
    }
}

// Get the place details for a hotel. Show the information in an info window,
// anchored on the marker for the hotel that the user selected.
function showInfoWindow() {
    const marker = this;
    places.getDetails(
        { placeId: marker.placeResult.place_id },
        (place, status) => {
            if (status !== google.maps.places.PlacesServiceStatus.OK) {
                return;
            }
            infoWindow.open(map, marker);
            buildIWContent(place);
        }
    );
}

// Load the place information into the HTML elements used by the info window.
function buildIWContent(place) {
    document.getElementById("iw-icon").innerHTML =
        '<img class="hotelIcon" ' + 'src="' + place.icon + '"/>';
    document.getElementById("iw-url").innerHTML =
        '<b><a href="' + place.url + '">' + place.name + "</a></b>";
    document.getElementById("iw-address").textContent = place.vicinity;

    if (place.formatted_phone_number) {
        document.getElementById("iw-phone-row").style.display = "";
        document.getElementById("iw-phone").textContent =
            place.formatted_phone_number;
    } else {
        document.getElementById("iw-phone-row").style.display = "none";
    }

    // Assign a five-star rating to the hotel, using a black star ('&#10029;')
    // to indicate the rating the hotel has earned, and a white star ('&#10025;')
    // for the rating points not achieved.
    if (place.rating) {
        let ratingHtml = "";

        for (let i = 0; i < 5; i++) {
            if (place.rating < i + 0.5) {
                ratingHtml += "&#10025;";
            } else {
                ratingHtml += "&#10029;";
            }
            document.getElementById("iw-rating-row").style.display = "";
            document.getElementById("iw-rating").innerHTML = ratingHtml;
        }
    } else {
        document.getElementById("iw-rating-row").style.display = "none";
    }

    // The regexp isolates the first part of the URL (domain plus subdomain)
    // to give a short URL for displaying in the info window.
    if (place.website) {
        let fullUrl = place.website;
        let website = String(hostnameRegexp.exec(place.website));

        if (!website) {
            website = "http://" + place.website + "/";
            fullUrl = website;
        }
        document.getElementById("iw-website-row").style.display = "";
        document.getElementById("iw-website").textContent = website;
    } else {
        document.getElementById("iw-website-row").style.display = "none";
    }
}
