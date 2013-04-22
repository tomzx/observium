<?php

/// This function returns an array of location data when given an address.
/// The open&free geocoding APIs are not very flexible, so addresses must be in standard formats.

function get_geolocation($address)
{
  global $config;
  /// Openstreetmap. The usage limits are stricter here.
  #$url = "http://nominatim.openstreetmap.org/search?format=json&addressdetails=1&limit=1&q=".urlencode($address);

  /// Mapquest open data. There are no usage limits.
  $url = "http://open.mapquestapi.com/nominatim/v1/search.php?format=json&addressdetails=1&limit=1&q=".urlencode($address);


  if($address != "Unknown" && $config['geocoding']['enable'] == TRUE)
  {
    $mapresponse = file_get_contents($url);
    $data = json_decode($mapresponse, true);
    $data = $data[0];
    if(!count($data))
    {
      /// We seem to have hit a snag geocoding. It might be that the first element of the address is a business name.
      /// Lets drop the first element and see if we get anything better! This works more often than one might expect.
      $csvArray = explode(",", $address);
      array_shift($csvArray);
      $address = implode(",", $csvArray);
      $url = "http://open.mapquestapi.com/nominatim/v1/search.php?format=json&addressdetails=1&limit=1&q=".urlencode($address);
      $mapresponse = file_get_contents($url);
      $data = json_decode($mapresponse, true);
      /// We only want the first entry in the returned data.
      $data = $data[0];
    }
  }

  /// Put the values from the data array into the return array where they exist, else replace them with defaults or Unknown.

  if(strlen($data['lat']))     { $location['location_lat']     = $data['lat'];     } else { $location['location_lat']     = $config['geocoding']['default']['lat']; }
  if(strlen($data['lon']))     { $location['location_lon']     = $data['lon'];     } else { $location['location_lon']     = $config['geocoding']['default']['lat']; }
  if(strlen($data['address']['town'])) { $location['location_city']    = $data['address']['town'];    }
  elseif(strlen($data['address']['city']))    { $location['location_city']    = $data['address']['city'];    } else { $location['location_city']    = "Unknown"; }

  /// Would be nice to have an array of countries where we want state, and ones where we want County. For example, USA wants state, UK wants county.
  if(strlen($data['address']['county']))  { $location['location_county']  = $data['address']['county'];  } else { $location['location_county']  = "Unknown"; }

  if(strlen($data['address']['state']))   { $location['location_state']   = $data['address']['state'];   } else { $location['location_state']   = "Unknown"; }
  if(strlen($data['address']['country_code'])) { $location['location_country'] = $data['address']['country_code']; } else { $location['location_country'] = "Unknown"; }

  return $location;
}

