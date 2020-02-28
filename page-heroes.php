<?php
/**
 * Here the google maps API is called within a page template, to draw markers.
 * Add a page "heroes" to your wordpress site and locate this file in the theme directory you are using.
 * Put the directory "heldinnen", which contains mainly the graphics, in your theme directory.
 * You need a formfield for the location and one for the string, we call these "ort" and "superkraft".
 * Change variables if you need to, if you have questions please open up an issue or
 * contagt github@michaelpollak.org
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 */

get_header();

// You may wish to change these variables.
$theme = "twentynineteen";
$fieldort = "ort";
$fieldsuperkraft = "superkraft";
$apikey = "???"; // Your Google Maps API Key.

?>

    <div id="heroesmap" class="content-area">

    <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 1000px;
      }
    </style>

    <div id="map"></div>

    <script>
    // Change the design of your map here.
    // https://snazzymaps.com/style/151/ultra-light-with-labels
    // https://mapstyle.withgoogle.com/
    var style = [
    {
        "featureType": "administrative",
        "elementType": "geometry",
        "stylers": [
          {
            "visibility": "off"
          }
        ]
    },
    {
        "featureType": "administrative.land_parcel",
        "stylers": [
          {
            "visibility": "off"
          }
        ]
    },
    {
        "featureType": "administrative.neighborhood",
        "stylers": [
          {
            "visibility": "off"
          }
        ]
    },
    {
        "featureType": "poi",
        "stylers": [
          {
            "visibility": "off"
          }
        ]
    },
    {
        "featureType": "poi",
        "elementType": "labels.text",
        "stylers": [
          {
            "visibility": "off"
          }
        ]
    },
    {
        "featureType": "road",
        "elementType": "labels",
        "stylers": [
          {
            "visibility": "off"
          }
        ]
    },
    {
        "featureType": "road",
        "elementType": "labels.icon",
        "stylers": [
          {
            "visibility": "off"
          }
        ]
    },
    {
        "featureType": "road.arterial",
        "elementType": "labels",
        "stylers": [
          {
            "visibility": "off"
          }
        ]
    },
    {
        "featureType": "road.highway",
        "elementType": "labels",
        "stylers": [
          {
            "visibility": "off"
          }
        ]
    },
    {
        "featureType": "road.local",
        "stylers": [
          {
            "visibility": "off"
          }
        ]
    },
    {
        "featureType": "transit",
        "stylers": [
          {
            "visibility": "off"
          }
        ]
    },
    {
        "featureType": "water",
        "elementType": "labels.text",
        "stylers": [
          {
            "visibility": "off"
          }
        ]
    }
    ]

    <?php
    // We use two images, one for the center of the map (the school) and one for the students.
    $url = get_site_url(null, '/wp-content/themes/', 'https');
    echo "var fliegerlogo = '" . $url . $theme . "/heldinnen/nurflieger.png';";
    echo "var haklogo = '" . $url . $theme . "/heldinnen/haklogo.png';";
    ?>

    // Initiate the map.
    function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
            // Center the map on the schools coordinates.
            center: new google.maps.LatLng(48.81121063232422, 15.280478477478027),
            zoom: 8,
            styles: style
        });

        // Draw the school on the map, same coordinates.
        var marker = new google.maps.Marker({
            position: {lat: 48.81121063232422, lng: 15.280478477478027},
            map: map,
            icon: haklogo
        });

        <?php

        // Build a list of all users the do have coordinates in their profile, ignore all others.
        $heldinnen = get_users(); // Maybe remove some roles here 'role=subscriber'.
        foreach ( $heldinnen as $heldin ) {
            if (get_user_meta( $heldin->id, 'lat', true ) && get_user_meta( $heldin->id, 'lng', true ) ) {
                $heros[] = array(
                    "name" => $heldin->display_name,
                    "superkraft" => json_encode( get_user_meta( $heldin->id, $fieldsuperkraft, true )),
                    "ort" => get_user_meta( $heldin->id, $fieldort, true ),
                    "lat" => get_user_meta( $heldin->id, 'lat', true ),
                    "lng" => get_user_meta( $heldin->id, 'lng', true ),
                );
            }
        }

        // Add each found user to a javascript array we can pass to the google maps api.
        foreach ($heros as $i => $hero) {

            // Move markers randomly a tiny bit to untangle same locations.
            $hero['lat'] += rand(1, 11) / 25000;
            $hero['lng'] += rand(1, 11) / 25000;

            // Draw a marker for one user, see content to change infofield.
            echo "
            var marker$i = new google.maps.Marker({
              position: {lat: ".$hero['lat'].", lng: ".$hero['lng']."},
              map: map,
              icon: fliegerlogo
            });
            var infoWindow$i = new google.maps.InfoWindow({
                content: '<b>".$hero['name']."</b><br>".$hero['superkraft']."<br>".$hero['ort']."',
            });
            marker$i.addListener('click', function() {
                infoWindow$i.open(map, marker$i);
            });
            ";
            $markers[] = 'marker'.$i;
        }
        // Add clustering to the markers, if we see a lot of users in the same location.
        echo "var markers = [".implode(",", $markers)."];";
        echo "var markerCluster = new MarkerClusterer(map, markers, {imagePath: '" . $url . $theme . "/heldinnen/m'});";
        ?>

    }
    </script>

    <?php
        echo "<script src='" . $url . $theme . "/heldinnen/markerclusterer.js'></script>";
    ?>

    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=<?php echo $apikey; ?>&callback=initMap">
    </script>

    </div>

    <!-- Add the normal loop to show page content below map. -->
    <div id="primary" class="content-area">
        <main id="main" class="site-main">

            <?php

            /* Start the Loop */
            while ( have_posts() ) :
                the_post();

                get_template_part( 'template-parts/content/content', 'page' );

                // If comments are open or we have at least one comment, load up the comment template.
                if ( comments_open() || get_comments_number() ) {
                    comments_template();
                }

            endwhile; // End of the loop.
            ?>

        </main><!-- #main -->
    </div><!-- #primary -->


<?php
get_footer();
