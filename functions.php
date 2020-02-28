<?php

/**
 * Extend ultimate member with the fields lat and lng to geocode location.
 * Usage: Add a field with the Meta Key set in $fieldort to your forms.
 * Put this code in your functions.php within your theme directory.
 * If a user registers or updates we call google maps api and ask for geolocation.
 * If geolocation is possible we add lat and lng to users metadata.
 * Change variables if you need to, if you have questions please open up an issue or
 * contagt github@michaelpollak.org
 */

// You may wish to change these variables.
$theme = "twentynineteen";
$fieldort = "ort";
$fieldsuperkraft = "superkraft";
$apikey = "???"; // Your Google Maps API Key.

// Name the fields in your ultimate member form as defined in $fieldort, default is "ort".
add_filter( 'um_add_user_frontend_submitted', 'geocode_hero', 10, 1 );
function geocode_hero( $submitted ) {
    $location = $submitted[$fieldort];

    // If none of the fields are present or data was entered, just skip.
    if (!$location) {
        return $submitted;
    }

    // Call the Google Maps API to geocode location, see https://developers.google.com/maps/documentation/geocoding/intro?hl=de .
    $response = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($location) . '&region=at&key=AIzaSyADpAfoc-YYu09kxshs5DqFlB4ndCZyZRs');
    $response = json_decode($response);

    // Ignore in case google cant geocode location.
    if ($response->status != "OK") {
        return $submitted;
    }

    $submitted['submitted']['lat'] = $response->results[0]->geometry->location->lat;
    $submitted['submitted']['lng'] = $response->results[0]->geometry->location->lng;

    return $submitted;
}

// In case users update there location in their profile update geocoding as well.
add_filter( 'um_user_pre_updating_profile_array', 'update_geocode_hero', 10, 2 );
function update_geocode_hero( $to_update, $user_id ) {

    $location = $to_update[$fieldort];

    // Ignore in case the location was not changed.
    if (!$location) {
        return $to_update;
    }

    // Call the Google Maps API to geocode location, see https://developers.google.com/maps/documentation/geocoding/intro?hl=de.
    // NOTE: We look in the region of Austria because this is the most plausible.
    $apicall = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($location) . '&region=at&key=' . $apikey;
    $response = file_get_contents($apicall);
    $response = json_decode($response);

    // Ignore in case google cant geocode location.
    if ($response->status != "OK") {
        return $to_update;
    }

    $to_update['lat'] = $response->results[0]->geometry->location->lat;
    $to_update['lng'] = $response->results[0]->geometry->location->lng;

    return $to_update;
}
