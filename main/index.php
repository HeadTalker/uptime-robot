<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Uptime Monitor</title>
    <link rel="stylesheet" href="css/dist/all.styles.min.css" media="screen" title="no title" charset="utf-8">
    <script type="text/javascript" src="js/dist/all.scripts.min.js"></script>
  </head>
  <body>

    <?php
    // require our config api key
    require_once( __DIR__ . '/../config.php' );
    // require our main php file
    require_once( __DIR__ . '/php/main.php' );
    // init our class
    $monitor_robot = new monitor_robot();
    // Check if curl is installed and show CURL install info if its not installed
    if ( $monitor_robot->monitor_curl() === FALSE ): ?>
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12 text-center">
          <div class="alert alert-info alert-dismissible fade in" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            <h4>Oh snap! You don't have CURL installed!</h4>
            <p>Don't worry.  You can download it for free.</p>
            <p><a href="http://curl.haxx.se/download.html" target="_blank"><button type="button" class="btn btn-primary">Go Download Curl</button></a></p>
          </div>
        </div>
      </div>
    </div>
    <?php die(); endif; ?>

    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12 main">

          <h1 class="page-header text-center">Uptime Monitor</h1>

          <table id="up-get-monitors-table" class="table table-hover">
            <thead>
              <tr>
                <th>Name</th>
                <th>URL</th>
                <th>Status</th>
                <th>Type</th>
                <th>Interval</th>
                <th>ID</th>
                <th>Uptime</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $monitor_response = $monitor_robot->monitor_endpoint( $UP_ACCOUNT_API_KEY ); // Call the endpoint and get the data
                $monitor_data     = $monitor_robot->monitor_table_body( $monitor_response['monitors']['monitor'] ); // Create our data table body
              ?>
            </tbody>
          </table>

          <?php if ( is_array( $monitor_response['monitors']['monitor'] ) ): // check if we have monitors before trying to show charts

            $i = 0;

            foreach ( $monitor_response['monitors']['monitor'] as $monitor ): // loop through each monitor and create a chart

              if ( isset( $monitor['responsetime'] ) ): ?>

                <div class="col-md-12 text-center">
                  <h3><?php echo $monitor['friendlyname']; ?></h3>
                  <h5><?php echo $monitor_robot->monitor_type( $monitor['type'] ); ?></h5>
                  <div class="l-chart">
                    <div class="aspect-ratio">
                      <canvas id="chart<?php echo $i; ?>"></canvas>
                    </div>
                  </div>
                </div>

                <?php list( $response_datetime, $response_value ) = $monitor_robot->monitor_response_data( $monitor['responsetime'] ); ?>

                <script type="text/javascript">

                var response_datetime = <?php echo json_encode($response_datetime);?>;
                var response_value    = <?php echo json_encode($response_value);?>;

                Chart.defaults.global.animationEasing        = 'easeInOutQuad',
                Chart.defaults.global.responsive             = true;
                Chart.defaults.global.tooltipFillColor       = '#FFFFFF';
                Chart.defaults.global.tooltipFontColor       = '#111';
                Chart.defaults.global.tooltipCaretSize       = 0;
                Chart.defaults.global.maintainAspectRatio    = true;
                Chart.defaults.Line.scaleShowHorizontalLines = false;
                Chart.defaults.Line.scaleShowHorizontalLines = false;
                Chart.defaults.Line.scaleGridLineColor       = '#434857';
                Chart.defaults.Line.scaleLineColor           = '#434857';

                var chart    = document.getElementById("<?php echo 'chart' . $i; ?>").getContext('2d'),
                gradient     = chart.createLinearGradient(0, 0, 0, 450);

                var data  = {
                  labels: response_datetime,

                  datasets: [
                    {
                      label: 'Response Time',
                      fillColor: gradient,
                      strokeColor: '#1CA8DD',
                      pointColor: 'white',
                      pointStrokeColor: 'rgba(220,220,220,1)',
                      pointHighlightFill: '#fff',
                      pointHighlightStroke: 'rgba(220,220,220,1)',
                      data: response_value,
                    }
                  ]
                };

                gradient.addColorStop(0, 'rgba(0, 161, 255, 0.5)');
                gradient.addColorStop(0.5, 'rgba(0, 161, 255, 0.25)');
                gradient.addColorStop(1, 'rgba(0, 161, 255, 0)');

                var chart = new Chart(chart).Line(data);

                </script>

              <?php else:

               echo '<div class="text-center">' . $monitor['friendlyname'] . 'does not enough data to make a graph yet</div>';

              endif;

            $i++; endforeach;

          endif; ?>

        </div>
      </div>
    </div>
  </body>
</html>
