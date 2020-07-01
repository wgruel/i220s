<?php

    function geocode($address, $api_key){
      $lat = 0.0;
      $lng = 0.0;  

      // call Google Maps API in order to get lattitude and longitude... 
      $maps_url = 'https://' .
            'maps.googleapis.com/' .
            'maps/api/geocode/json' .
            '?address=' . urlencode($address) . 
            '&key=' . $api_key;

      // call the maps url ("open file") and read result as JSON      
      $maps_json = file_get_contents($maps_url);
      // convert JSON to an array, so we can deal with it more easily
      $maps_array = json_decode($maps_json, true);

      $lat = $maps_array['results'][0]['geometry']['location']['lat'];
      $lng = $maps_array['results'][0]['geometry']['location']['lng'];

    
      return(array($lat, $lng));
      
    }

?>