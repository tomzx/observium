<?php

function get_geolocation($address)
{
  global $config;
  $url = "http://nominatim.openstreetmap.org/search?format=json&addressdetails=1&limit=1&q=".urlencode($address);
  $url = "http://open.mapquestapi.com/nominatim/v1/search.php?format=json&addressdetails=1&limit=1&q=".urlencode($address);


  if($address != "Unknown")
  {
    $mapresponse = file_get_contents($url);
    $data = json_decode($mapresponse, true);
    $data = $data[0];
    if(!count($data)) 
    {
      // We seem to have hit a snag. Lets drop the first element and see if we get anything better!
      $csvArray = explode(",", $address);
      array_shift($csvArray);
      $address = implode(",", $csvArray);
      $url = "http://open.mapquestapi.com/nominatim/v1/search.php?format=json&addressdetails=1&limit=1&q=".urlencode($address);
      $mapresponse = file_get_contents($url);
      $data = json_decode($mapresponse, true);
      $data = $data[0];
    }


# print_r($data);

    if(strlen($data['lat']))     { $location['location_lat']     = $data['lat'];     } else { $location['location_lat']     = $config['geocoding']['default']['lat']; }
    if(strlen($data['lon']))     { $location['location_lon']     = $data['lon'];     } else { $location['location_lon']     = $config['geocoding']['default']['lat']; }
    if(strlen($data['address']['town'])) { $location['location_city']    = $data['address']['town'];    }
    elseif(strlen($data['address']['city']))    { $location['location_city']    = $data['address']['city'];    } else { $location['location_city']    = "Unknown"; }

    if(strlen($data['address']['county']))  { $location['location_county']  = $data['address']['county'];  } else { $location['location_county']  = "Unknown"; }
    if(strlen($data['address']['state']))   { $location['location_state']   = $data['address']['state'];   } else { $location['location_state']   = "Unknown"; }
    if(strlen($data['address']['country_code'])) { $location['location_country'] = country_from_code($data['address']['country_code']); } else { $location['location_country'] = "Unknown"; }

  } else {
    // Set default values because we couldn't do lookup
    $location['location_lat'] = $config['geocoding']['default']['lat'];
    $location['location_lon'] = $config['geocoding']['default']['lon'];
    $location['location_city'] = "Unknown";
    $location['location_county'] = "Unknown";
    $location['location_state'] = "Unknown";
    $location['location_country'] = "Unknown";
  }

  return $location;
}

