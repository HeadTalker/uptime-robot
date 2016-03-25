<?php

/**
* Uptime Robot Class
*/

class monitor_robot {


  /**
  * Check if curl is installed.
  * Returns TRUE if it is installed.
  * Returns FALSE if its NOT installed.
  *
  * @return  bool
  */

  public function monitor_curl() {
    return function_exists( 'curl_version' );
  }

  /**
  * Set the API endpoint for uptime robot
  * https://uptimerobot.com/api
  *
  * @param   $UP_ACCOUNT_API_KEY - uptime robot api key
  * @return  $decoderesponse - decoded json response
  */

  public function monitor_endpoint( $UP_ACCOUNT_API_KEY ) {
    $endpoint = "https://api.uptimerobot.com/getMonitors?apiKey=" . $UP_ACCOUNT_API_KEY . "&responseTimes=1&logs=1&format=json&noJsonCallback=1";
    $curl     = curl_init( $endpoint );

    curl_setopt_array( $curl, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_URL            => $endpoint,
    ] );

    $response         = curl_exec( $curl );
    $monitor_response = json_decode( $response, true );

    return $monitor_response;
  }

  /**
  * Prints out the table body in the datatables with relevant info
  *
  * @param  $monitor_data
  */

  public function monitor_table_body( $monitor_data ) {

    if ( is_array( $monitor_data ) ) {

      foreach ( $monitor_data as $monitor ) {

        $monitor_id           = $monitor['id'];
        $monitor_name         = $monitor['friendlyname'];
        $monitor_url          = $monitor['url'];
        $monitor_type         = $this->monitor_type( $monitor['type'] );
        $monitor_interval     = $monitor['interval'];
        $monitor_status       = $this->monitor_status( $monitor['status'] );
        $monitor_uptime_ratio = $monitor['alltimeuptimeratio'];

        echo "<tr><td>" . $monitor_name . "</td>";
        echo "<td>" . $monitor_url . "</td>";
        echo "<td>" . $monitor_status . "</td>";
        echo "<td>" . $monitor_type . "</td>";
        echo "<td>" . $monitor_interval . " seconds</td>";
        echo "<td>" . $monitor_id . "</td>";
        echo "<td>" . $monitor_uptime_ratio . "%</td></tr>";

      }

    }

  }

  /**
  * Monitor Response Data Timestamps and values
  *
  * @param  array - $monitor_response
  * @return array - $response_datetime, $response_value - Array of timestamps and array of values
  */

  public function monitor_response_data( $monitor_response_data ) {
    $response_datetime = [];
    $response_value    = [];

    $monitor['responsetime'] = $monitor_response_data;

    if ( is_array( $monitor['responsetime'] ) ) {

      foreach ( array_reverse( $monitor['responsetime'] ) as $response ) {

        $response_datetime[] = $response['datetime'];
        $response_value[]    = $response['value'];

      }

    }

    return array( $response_datetime, $response_value );

  }


  /**
  * Monitor Type Name
  *
  * @param  int - $monitor_type
  * @return str - monitor type
  */

  public function monitor_type( $monitor_type ) {
    switch ( $monitor_type ) {
    case 1:
      return 'HTTP(s)';
      break;
    case 2:
      return 'Keyword';
      break;
    case 3:
      return 'Ping';
      break;
    case 4:
      return 'Port';
      break;
    default:
      return 'Unknown or Error';
      break;
    }
  }

  /**
  * Monitor Status Name
  *
  * @param   int - $monitor_status
  * @return  str - monitor type
  */

  public function monitor_status( $monitor_status ) {
    switch ( $monitor_status ) {
    case 0:
      return 'Paused';
      break;
    case 1:
      return 'Not Checked Yet';
      break;
    case 2:
      return '<img src="svg/circle.svg">';
      break;
    case 8:
      return 'SEEMS DOWN!';
      break;
    case 9:
      return 'DOWN!';
      break;
    default:
      return 'Unknown or Error';
      break;
    }
  }


}
