<?php

/// This function returns an array of location data when given an address.
/// The open&free geocoding APIs are not very flexible, so addresses must be in standard formats.

function get_geolocation($address)
{
  global $config, $debug;
  
  switch (strtolower($config['geocoding']['api']))
  {
    case 'osm':
    case 'openstreetmap':
      $location['location_geoapi'] = 'openstreetmap';
      /// Openstreetmap. The usage limits are stricter here. (http://wiki.openstreetmap.org/wiki/Nominatim_usage_policy)
      $url = "http://nominatim.openstreetmap.org/search?format=json&addressdetails=1&limit=1&q=";
      $reverse_url = "http://nominatim.openstreetmap.org/reverse?format=json&";
      break;
    case 'google':
      $location['location_geoapi'] = 'google';
      // See documentation here: https://developers.google.com/maps/documentation/geocoding/
      /// Use of the Google Geocoding API is subject to a query limit of 2,500 geolocation requests per day.
      $url = "http://maps.googleapis.com/maps/api/geocode/json?sensor=false&address=";
      $reverse_url = "http://maps.googleapis.com/maps/api/geocode/json?sensor=false&";
      break;
    case 'mapquest':
    default:
      $location['location_geoapi'] = 'mapquest';
      /// Mapquest open data. There are no usage limits.
      $url = "http://open.mapquestapi.com/nominatim/v1/search.php?format=json&addressdetails=1&limit=1&q=";
      $reverse_url = "http://open.mapquestapi.com/nominatim/v1/reverse.php?format=json&";
  }

  if($address != "Unknown" && $config['geocoding']['enable'])
  {
    // If location string contains coordinates ([33.234, -56.22]) use Reverse Geocoding.
    $pattern = '/\[\s*([+-]*\d+[\d\.]*)[,\s]+([+-]*\d+[\d\.]*)[\s\]]+/';
    if (preg_match($pattern, $address, $matches))
    {
      $location['location_lat'] = $matches[1];
      $location['location_lon'] = $matches[2];
      if ($config['geocoding']['api'] == 'google')
      {
        //latlng=40.714224,-73.961452
        $request = $reverse_url . 'latlng=' . $location['location_lat'] . ',' . $location['location_lon'];
      } else {
        //lat=51.521435&lon=-0.162714
        $request = $reverse_url . 'lat=' . $location['location_lat'] . '&lon=' . $location['location_lon'];
      }
    } else {
      $request = $url.urlencode($address);
    }
    $mapresponse = get_http_request($request);
    $data = json_decode($mapresponse, true);
    if ($config['geocoding']['api'] == 'google')
    {
      if ($data['status'] == 'OK')
      {
        // Use google data only with good status response
        $data = $data['results'][0];
      }
      elseif ($data['status'] == 'OVER_QUERY_LIMIT')
      {
        // Return empty array for overquery limit (for later recheck)
        return array();
      }
    }
    elseif (!isset($location['location_lat']))
    {
      $data = $data[0];
      if(!count($data))
      {
        /// We seem to have hit a snag geocoding. It might be that the first element of the address is a business name.
        /// Lets drop the first element and see if we get anything better! This works more often than one might expect.
        $csvArray = explode(",", $address);
        array_shift($csvArray);
        $address = implode(",", $csvArray);
        $mapresponse = get_http_request($url.urlencode($address));
        $data = json_decode($mapresponse, true);
        /// We only want the first entry in the returned data.
        $data = $data[0];
      }
    }
  }
  if ($debug) { echo "GEO-API REQUEST: $request\n"; }

  /// Put the values from the data array into the return array where they exist, else replace them with defaults or Unknown.
  if ($config['geocoding']['api'] == 'google')
  {
    $location['location_lat'] = $data['geometry']['location']['lat'];
    $location['location_lon'] = $data['geometry']['location']['lng'];
    foreach ($data['address_components'] as $entry)
    {
      switch ($entry['types'][0])
      {
        case 'locality':
          $location['location_city'] = $entry['long_name'];
          break;
        case 'administrative_area_level_2':
          $location['location_county'] = $entry['long_name'];
          break;
        case 'administrative_area_level_1':
          $location['location_state'] = $entry['long_name'];
          break;
        case 'country':
          $location['location_country'] = strtolower($entry['short_name']);
          break;
      }
    }
  } else {
    $location['location_lat'] = $data['lat'];
    $location['location_lon'] = $data['lon'];
    $location['location_city'] = (strlen($data['address']['town'])) ? $data['address']['town'] : $data['address']['city'];

    /// Would be nice to have an array of countries where we want state, and ones where we want County. For example, USA wants state, UK wants county.
    $location['location_county'] = $data['address']['county'];
    $location['location_state']  = $data['address']['state'];

    $location['location_country'] = $data['address']['country_code'];
  }

  // Use defaults if empty values
  if (!strlen($location['location_lat']))     { $location['location_lat'] = $config['geocoding']['default']['lat']; }
  if (!strlen($location['location_lon']))     { $location['location_lon'] = $config['geocoding']['default']['lon']; }
  if (!strlen($location['location_city']))    { $location['location_city']    = 'Unknown'; }
  if (!strlen($location['location_county']))  { $location['location_county']  = 'Unknown'; }
  if (!strlen($location['location_state']))   { $location['location_state']   = 'Unknown'; }
  if (!strlen($location['location_country'])) { $location['location_country'] = 'Unknown'; }

  return $location;
}

