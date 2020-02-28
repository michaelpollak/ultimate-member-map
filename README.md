# ultimate-member-map
![Wordpress Users on a Geolocated on a Google Maps Map](https://raw.githubusercontent.com/michaelpollak/ultimate-member-map/master/SCREENSHOT_ultimate-member-map.png)

Geocode and display a number of Wordpress users on a map using the Google Maps API.
The usecase was a school [hakwt.at](http://hakwt.at/) that wanted to display where graduates are working and living. We set up a wordpress site and added [ultimate member](https://ultimatemember.com) as the social media plugin. We did not find a smart solution to geocode users with the Google Maps API and show them on a map. So we built this. I hope this helps somebody else out.

## Install
You need a Google Maps API key to use this functionality.
Put the file "page-heroes.php" in your theme directory.
Put the directory "heldinnen" in the same theme directory.
Put the content of this functions.php at the bottom of the functions.php file in your theme directory.
Add a field named ort (can be text or googlemap) to your ultimate member forms.
Add a field superkraft (text, textarea) to your ultimate member forms.
NOTE: These fieldnames can be changed in the variables in page-heroes.php and functions.php.
If you don't use "twentynineteen" as your theme, change the variable $theme in page-heroes.php.
Add your API key in $apikey in page-heroes.php as well as functions.php
In your wordpress interface, add a page named "heroes".

## Geocoding
Geocoding is done in the functions.php file, every time a user updates its location or registers with a location we send this to the google geolocate api. It spits back some coordinates (hopefully) and those are stored in the database with the users metadata.

## Mapping
Mapping is done in a special page template. You can name these whatever you want, the pagename just needs to correlate with the template name. In our case "heroes". We look for all users with geocoded location data, put them in an array, send it over to JS and ultimately let google build the map. Different graphics can be used for different members.

## Troubleshooting, Bugs, and Feedback
+ To report a bug, please [GitHub Issues](https://github.com/michaelpollak/ultimate-member-map/issues).
+ To provide feedback, please use the [GitHub Issues](https://github.com/michaelpollak/ultimate-member-map/issues).

## License
<a href="https://docs.moodle.org/dev/License" target="_blank"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/93/GPLv3_Logo.svg/220px-GPLv3_Logo.svg.png" alt="GPL Logo" align="right"></a> This code snippet is licensed under the [GNU General Public License, Version 3](http://www.gnu.org/licenses/gpl-3.0.html).
