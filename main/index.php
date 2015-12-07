<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Uptime Monitor</title>
    <link rel="stylesheet" href="css/dist/all.styles.min.css" media="screen" title="no title" charset="utf-8">
    <script type="text/javascript" src="js/dist/all.scripts.min.js"></script>
  </head>
  <body>

<?php require_once(__DIR__ . '/../config.php'); ?>

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

            // set our end point
            $endpoint = "https://api.uptimerobot.com/getMonitors?apiKey=" . $UP_ACCOUNT_API_KEY . "&responseTimes=1&logs=1&format=json&noJsonCallback=1";
            $curl = curl_init($endpoint);

            curl_setopt_array($curl, [
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_URL            => $endpoint,
            ]);
            $response = curl_exec($curl);
            $decoderesponse = json_decode($response, true);

            foreach ($decoderesponse['monitors']['monitor'] as $monitor ) {

              // monitor id
              $monitor_id = $monitor['id'];
              // monitor friendly name
              $monitor_name = $monitor['friendlyname'];
              // monitor URL
              $monitor_url =  $monitor['url'];
              // monitor type for example http, ping etc
              $monitor_type = $monitor['type'];
              // check monitor type values and give them a value
              switch ($monitor_type) {
                case 1:
                  $monitor_type = "HTTP(s)";
                break;

                case 2:
                  $monitor_type = "Keyword";
                  break;

                case 3:
                  $monitor_type = "Ping";
                  break;

                case 4:
                  $monitor_type = "Port";
                  break;

                default:
                  $monitor_type = "Unknown or Error";
                  break;
              }
              // monitor interval every X seconds
              $monitor_interval = $monitor['interval'];
              // monitor status - up, down, paused
              $monitor_status =  $monitor['status'];
              // check monitor status values and give them a value
              switch ($monitor_status) {
                case 0:
                  $monitor_status = "Paused";
                break;

                case 1:
                  $monitor_status = "Not Checked Yet";
                  break;

                case 2:
                  $monitor_status = '<img src="svg/circle.svg">';
                  break;

                case 8:
                  $monitor_status = "SEEMS DOWN!";
                  break;

                case 9:
                  $monitor_status = "DOWN!";
                  break;

                default:
                  $monitor_status = "Unknown or Error";
                  break;
              }
              // monitor uptime % out of 100 ratio
              $monitor_uptime_ratio =  $monitor['alltimeuptimeratio'];

              echo "<tr><td>" . $monitor_name . "</td>";
              echo "<td>" . $monitor_url . "</td>";
              echo "<td>" . $monitor_status . "</td>";
              echo "<td>" . $monitor_type . "</td>";
              echo "<td>" . $monitor_interval . " seconds</td>";
              echo "<td>" . $monitor_id . "</td>";
              echo "<td>" . $monitor_uptime_ratio . "%</td></tr>";

            }

            ?>
          </tbody>
        </table>

          <script type="text/javascript">
          $(document).ready(function() {
              jQuery('#up-get-monitors-table').DataTable({
                "aaSorting": [],
                "oLanguage": {
                    "sInfo": 'Showing _START_ to _END_ of _TOTAL_ Monitors.',
                    "sInfoEmpty": 'No Monitors yet.',
                    "sInfoFiltered": 'filtered from _MAX_ total Monitors',
                    "sZeroRecords": 'No Monitors Found',
                    "sLengthMenu": 'Show _MENU_ Monitors',
                    "sEmptyTable": "No Monitors found currently.",
                  }
                });
              });
          </script>

        <?php

          // set i to 0 so we can auto increment tables
          $i = 0;

          foreach ($decoderesponse['monitors']['monitor'] as $monitor ) {

            // monitor friendly name
            $monitor_name = $monitor['friendlyname'];
            // monitor type for example http, ping etc
            $monitor_type = $monitor['type'];
            // check monitor type values and give them a value
            switch ($monitor_type) {
              case 1:
                $monitor_type = "HTTP(s)";
              break;

              case 2:
                $monitor_type = "Keyword";
                break;

              case 3:
                $monitor_type = "Ping";
                break;

              case 4:
                $monitor_type = "Port";
                break;

              default:
                $monitor_type = "Unknown or Error";
                break;
            }

            ?>

              <div class="col-md-12 text-center">
                <h3><?php echo $monitor_name; ?></h3>
                <h5><?php echo $monitor_type; ?></h5>
                <div class="l-chart">
                  <div class="aspect-ratio">
                    <canvas id="chart<?php echo $i; ?>"></canvas>
                  </div>
                </div>
              </div>

              <?php
              $response_datetime = [];
              $response_value = [];
              foreach (array_reverse($monitor['responsetime']) as $response) {
                // get all the response timestamps
                $response_datetime[] = $response['datetime'];
                // get all the response values
                $response_value[] = $response['value'];
              }
              ?>

            <script type="text/javascript">

              var response_datetime = <?php echo json_encode($response_datetime);?>;
              var response_value = <?php echo json_encode($response_value);?>;

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
                gradient = chart.createLinearGradient(0, 0, 0, 450);

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
                    data: response_value
                  }
                ]
              };

              gradient.addColorStop(0, 'rgba(0, 161, 255, 0.5)');
              gradient.addColorStop(0.5, 'rgba(0, 161, 255, 0.25)');
              gradient.addColorStop(1, 'rgba(0, 161, 255, 0)');

              var chart = new Chart(chart).Line(data);

            </script>

        <?php $i++; } ?>

    </div>
  </div>
</div>
</body>
</html>
