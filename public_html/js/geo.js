//это вызываем в хидере, если приходит флаг о необходимости запроса локации
function getLocation() {
    if (navigator.geolocation) {
        position = navigator.geolocation.getCurrentPosition(position => {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;
            const nearestCity = getNearestCity(longitude, latitude);

            postSetRegion({location: nearestCity.id})
                .then(data => {
                    console.log('location set');
                    document.querySelector(".location-name").textContent = data.location_admin_name;
                    document.querySelector(".navbar-location-name").textContent = data.location;
                    document.querySelector(".navbar-location-bottom-name").textContent = data.location;
                })
                .catch((error) => {
                    console.error('Error:', error);
                });
        });
    } else {
        console.log('no geo available');
    }
}

//пишем в базу и/или сессию текущую локацию
async function postSetRegion(data = {}) {
    const response = await fetch('/region/set', {
        method: 'POST',
        mode: 'cors',
        cache: 'no-cache',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(data)
    });

    return response.json();
}

function getNearestCity(lat, lon) {
    let minDistance = Infinity;
    let nearestCity = {};

    cities.forEach(city => {
        const distance = getDistance(lat, lon, parseFloat(city.lat), parseFloat(city.lng));
        if (distance < minDistance) {
            minDistance = distance;
            nearestCity = city;
        }
    });

    return nearestCity;
}

function getDistance(lat1, lon1, lat2, lon2) {
    const toRad = (value) => (value * Math.PI) / 180;

    const R = 6371; // Radius of the Earth in km
    const dLat = toRad(lat2 - lat1);
    const dLon = toRad(lon2 - lon1);
    const a =
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
        Math.sin(dLon / 2) * Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    const distance = R * c; // Distance in km

    return distance;
}

$(function () {
    //локация в трех местах
    const dropdowns = ['locationDropdown', 'locationHeaderDropdown', 'locationFooterDropdown'];
    dropdowns.forEach(function (dropdown) {
        if (document.getElementById(dropdown)) {
            const dropdownItems = document.getElementById(dropdown).nextElementSibling.querySelectorAll('.region-dropdown-item');
            dropdownItems.forEach(function (item) {
                item.addEventListener('click', function (event) {
                    if (event.target.closest('.region-toggle-arrow')) {
                        return;
                    }
                    var myfilter = Window.myfilter;
                    console.log('click');
                    event.preventDefault();
                    let selectedLocation = this.getAttribute('data-location-id');
                    if(myfilter)myfilter.region = selectedLocation;
                    fetch('/region/set', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({location: selectedLocation})
                    })
                        .then(response => response.json())
                        .then(data => {
                            if(document.querySelector(".location-name"))document.querySelector(".location-name").textContent = data.location_admin_name;
                            if(document.querySelector(".navbar-location-name"))document.querySelector(".navbar-location-name").textContent = data.location;
                            if(document.querySelector(".navbar-location-bottom-name"))document.querySelector(".navbar-location-bottom-name").textContent = data.location;
                            if(myfilter)myfilter.change();
                            if (myMap){
                                latLong = data.coordinates.split(',');
                                console.log('new coords: ', latLong);
                                myMap.setCenter([latLong[0], latLong[1]]);
                            }
                        })
                        .catch(error => console.error('Error:', error));
                });
            });
        }
    });
});