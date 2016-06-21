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
  * @param array $monitor_data Monitor Data
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
  * @param  array $monitor_response   Monitor response data
  * @return array $data               Array of timestamps and array of values
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

    $data = array( $response_datetime, $response_value );

    return $data;

  }
  
  /**
  * Past Incidents
  *
  */

  public function past_incidents( $monitor_response ) {
    if ( is_array( $monitor_response['monitors']['monitor'] ) ) {

      if ( count( $monitor_response['monitors']['monitor'] ) >= 4 ) {
        $column = 3;
      } else {
        $column = 12;
      }
 
      foreach ( $monitor_response['monitors']['monitor'] as $monitor ):
          
        echo "<div class='col-md-" . $column .  " col-incident text-xs-center p-a-1'><h4>" . $monitor['friendlyname'] . "</h4><hr>";
  
        if ( isset( $monitor['log'] ) ): 

          foreach ( $monitor['log'] as $log ):
         
          echo "<span class=" . $this->log_type( $log['type'] ) . ">" . _( 'Monitor' ) . ' ' . $this->log_type( $log['type'] ) . ' ' . _( 'on' ) . ' ' . $log['datetime'] . "</span><hr>"; 
 
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

    if ( is_array( $monitor_response['monitors']['monitor'] ) ): // check if we have monitors before trying to show charts

      $i = 0;

      if ( count( $monitor_response['monitors']['monitor'] ) > 1 ) {
        $column = 6;
      } else {
        $column = 12;
      }

      foreach ( $monitor_response['monitors']['monitor'] as $monitor ): // loop through each monitor and create a chart

        if ( isset( $monitor['responsetime'] ) ): ?>

          <div class="col-md-<?php echo $column; ?> col-chart text-xs-center">
            <h3><?php echo $monitor['friendlyname']; ?></h3>
            <h6><?php echo $this->monitor_type( $monitor['type'] ) . ' ' . _( 'Response Times' ); ?></h6>
            <canvas id="chart<?php echo $i; ?>"></canvas>
          </div>

          <?php list( $response_datetime, $response_value ) = $this->monitor_response_data( $monitor['responsetime'] ); ?>

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

         <div class="text-xs-center"><?php echo $monitor['friendlyname'] . ' ' . _( 'does not enough data to make a graph yet.' ); ?></div>

       <?php endif;

      $i++; endforeach;

    endif;
  }


}
