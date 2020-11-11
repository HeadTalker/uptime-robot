<?php

/**
* Require Config
*/

require_once( __DIR__ . '/../../config.php' );

/**
* Uptime Robot Class
*/

class monitor_robot {


  /**
  * Check if curl is installed.
  *
  * @return bool true | false  Returns true if CURL is installed. | Returns false  if CURL is NOT installed.
  */

  public function monitor_curl() {
    return function_exists( 'curl_version' );
  }

  /**
  * Set the API endpoint for uptime robot
  *
  * @param  string $UP_ACCOUNT_API_KEY Your uptime robot api key
  * @return array  $monitor_response Decoded json response from the API
  *
  * @link https://uptimerobot.com/api
  */

  public function monitor_endpoint( $UP_ACCOUNT_API_KEY ) {
    $endpoint = "https://api.uptimerobot.com/v2/getMonitors";
    $curl     = curl_init( $endpoint );

    curl_setopt_array( $curl, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_URL            => $endpoint,
    ] );

    curl_setopt_array($curl, array(
      CURLOPT_URL => $endpoint,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => "api_key=" . $UP_ACCOUNT_API_KEY . "&logs=1&format=json&all_time_uptime_ratio=1&response_times=1&response_times_average=60",
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/x-www-form-urlencoded"
      ),
    ));

    $response         = curl_exec( $curl );
    $monitor_response = json_decode( $response, true );
 
    return $monitor_response;
  }

  /**
  * Prints out the table body in the datatables with relevant info
  *
  * @param array $monitor_data Monitor Data
  */

  public function monitor_table_body( $monitor_data ) {

    if ( is_array( $monitor_data ) ) {

      foreach ( $monitor_data as $monitor ) {

        $monitor_id           = $monitor['id'];
        $monitor_name         = $monitor['friendly_name'];
        $monitor_url          = $monitor['url'];
        $monitor_type         = $this->monitor_type( $monitor['type'] );
        $monitor_interval     = $monitor['interval'];
        $monitor_status       = $this->monitor_status( $monitor['status'] );
        $monitor_uptime_ratio = $monitor['all_time_uptime_ratio'];

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
  * @param  array $monitor_response   Monitor response data
  * @return array $data               Array of timestamps and array of values
  */

  public function monitor_response_data( $monitor_response_data ) {
    $response_datetime = [];
    $response_value    = [];

    $monitor['response_times'] = $monitor_response_data;

    if ( is_array( $monitor['response_times'] ) ) {

      foreach ( array_reverse( $monitor['response_times'] ) as $response ) {

        $response_datetime[] = date('Y-m-d H:i', $response['datetime']);
        $response_value[]    = $response['value'];

      }

    }

    $data = array( $response_datetime, $response_value );

    return $data;

  }

  /**
   * Convert seconds to hh:mm:ss format
   * 
   */
  private function pretty_print_seconds($seconds) {
    $t = round($seconds);
    return sprintf('%02d:%02d:%02d', ($t/3600),($t/60%60), $t%60);
  }

  
  /**
  * Past Incidents
  *
  */

  public function past_incidents( $monitor_response ) {
    if ( is_array( $monitor_response['monitors'] ) ) {

      if ( count( $monitor_response['monitors'] ) >= 4 ) {
        $column = 3;
      } else {
        $column = 12;
      }
 
      foreach ( $monitor_response['monitors'] as $monitor ):
          
        echo "<div class='col-md-" . $column .  " col-incident text-xs-center p-a-1'><h4>" . $monitor['friendly_name'] . "</h4><hr>";
  
        if ( isset( $monitor['logs'] ) ): 

          foreach ( $monitor['logs'] as $log ):

          echo "<span class=" . $this->log_type( $log['type'] ) . ">" . _( 'Monitor' ) . ' ' . $this->log_type( $log['type'] ) . ' ' . _( 'on' ) . ' ' . date('Y-m-d H:i:s', $log['datetime']) . "</br>"; 
          echo "Duration : " . $this->pretty_print_seconds($log['duration']) ."</br>"; 
          echo "Reason : " . $log['reason']['code'] . " " . $log['reason']['detail'] . "</span><hr>"; 
 
          endforeach;

        endif;

      echo "</div>";

      endforeach;
    }

  }


  /**
  * Log Type Name
  *
  * @param  int    $type      The log type number value
  * @return string $type_name The log string name
  */

  private function log_type( $type ) {
   switch ( $type ) {
    case 1:
      $type_name = _( 'down' );
      break;
    case 2:
      $type_name = _( 'up' );
      break;
    case 99:
      $type_name = _( 'paused' );
      break;
    case 98:
      $type_name = _( 'started' );
      break;
    default:
      $type_name = _( 'Unknown or Error' );
      break;
    }
    return $type_name;
  }

  /**
  * Monitor Type Name
  *
  * @param  int    $type          The monitor type number value
  * @return string $monitor_name  The monitor string name
  */

  public function monitor_type( $type ) {
    switch ( $type ) {
    case 1:
      $monitor_name = _( 'HTTP(s)' );
      break;
    case 2:
      $monitor_name = _( 'Keyword' );
      break;
    case 3:
      $monitor_name = _( 'Ping' );
      break;
    case 4:
      $monitor_name = _( 'Port' );
      break;
    default:
      $monitor_name = _( 'Unknown or Error' );
      break;
    }
    return $monitor_name;
  }

  /**
  * Monitor Status Name
  *
  * @param  int    $status      The monitor status number value
  * @return string $status_name The monitor string name
  */

  public function monitor_status( $status ) {
    switch ( $status ) {
    case 0:
      $status_name = _( 'Paused' );
      break;
    case 1:
      $status_name = _( 'Not Checked Yet' );
      break;
    case 2:
      $status_name = _( '<img src="svg/circle.svg">' );
      break;
    case 8:
      $status_name = _( 'SEEMS DOWN!' );
      break;
    case 9:
      $status_name = _( 'DOWN!' );
      break;
    default:
      $status_name = _( 'Unknown or Error' );
      break;
    }
    return $status_name;
  }

  /**
  * Create charts with response time data
  *
  * @param array $monitor_response Monitor data
  */

  public function charts( $monitor_response ) {

    if ( is_array( $monitor_response['monitors'] ) ): // check if we have monitors before trying to show charts

      $i = 0;

      if ( count( $monitor_response['monitors'] ) > 1 ) {
        $column = 6;
      } else {
        $column = 12;
      }

      foreach ( $monitor_response['monitors'] as $monitor ): // loop through each monitor and create a chart

        if ( isset( $monitor['response_times'] ) ): ?>

          <div class="col-md-<?php echo $column; ?> col-chart text-xs-center">
            <h3><?php echo $monitor['friendly_name']; ?></h3>
            <h6><?php echo $this->monitor_type( $monitor['type'] ) . ' ' . _( 'Response Times' ); ?></h6>
            <canvas id="chart<?php echo $i; ?>"></canvas>
          </div>

          <?php list( $response_datetime, $response_value ) = $this->monitor_response_data( $monitor['response_times'] ); ?>

          <script type="text/javascript">

          jQuery(document).ready(function() {

            var response_datetime = <?php echo json_encode($response_datetime);?>;
            var response_value    = <?php echo json_encode($response_value);?>;

            var ctx = document.getElementById("<?php echo 'chart' . $i; ?>");
            var myChart = new Chart(ctx, {
              type: 'line',
              data: {
                labels: response_datetime,
                datasets: [
                  {
                    backgroundColor: 'rgba(0, 161, 255, 0.02)',
                    borderColor: '#1CA8DD',
                    pointBackgroundColor: 'white',
                    borderJoinStyle: 'miter',
                    pointBorderWidth: 1,
                    data: response_value,
                  },
                ]
              },
              options: {
                legend: {
                  display: false,
                },
                scales: {
                  yAxes: [{
                    ticks: {
                      beginAtZero: true
                    }
                  }],
                },
              }
            });
          });

        </script>

        <?php else: ?>

         <div class="text-xs-center"><?php echo $monitor['friendly_name'] . ' ' . _( 'does not enough data to make a graph yet.' ); ?></div>

       <?php endif;

      $i++; endforeach;

    endif;
  }


}
