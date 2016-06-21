<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title><?php echo _( 'Uptime Monitor' ); ?></title>
    <link rel="stylesheet" href="css/dist/all.styles.min.css" media="screen" title="no title" charset="utf-8">
    <script type="text/javascript" src="js/dist/all.scripts.min.js"></script>
  </head>
  <body class="m-b-2 m-t-3">
    <?php
    require_once( __DIR__ . '/php/main.php' ); // require our main php file
    $monitor_robot    = new monitor_robot(); // init our class
    $monitor_response = $monitor_robot->monitor_endpoint( $UP_ACCOUNT_API_KEY ); // Call the endpoint and get the data
    // Check if curl is installed and show CURL install info if its not installed
    if ( $monitor_robot->monitor_curl() === FALSE ): ?>
    <!-- Download Curl Warning -->
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12 text-xs-center">
          <div class="alert alert-info alert-dismissible fade in" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            <h4><?php echo _( 'Oh snap! You don\'t have CURL installed!' ); ?></h4>
            <p><?php echo _( 'Don\'t worry.  You can download it for free.' ); ?></p>
            <p><a href="http://curl.haxx.se/download.html" target="_blank"><button type="button" class="btn btn-primary"><?php echo _( 'Go Download Curl' ); ?></button></a></p>
          </div>
        </div>
      </div>
    </div>
    <?php die(); endif; ?>
    <!-- Main Content -->
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12 main">
          <h1 class="text-xs-center p-b-2"><?php echo _( 'Uptime Monitor' ); ?></h1>
          <!-- Table Data -->
          <div class="table-responsive">
            <table id="up-get-monitors-table" class="table table-hover table-bordered">
              <thead>
                <tr>
                  <th><?php echo _( 'Name' ); ?></th>
                  <th><?php echo _( 'URL' ); ?></th>
                  <th><?php echo _( 'Status' ); ?></th>
                  <th><?php echo _( 'Type' ); ?></th>
                  <th><?php echo _( 'Interval' ); ?></th>
                  <th><?php echo _( 'ID' ); ?></th>
                  <th><?php echo _( 'Uptime' ); ?></th>
                </tr>
              </thead>
              <tbody>
                <?php $monitor_data = $monitor_robot->monitor_table_body( $monitor_response['monitors']['monitor'] ); ?>
              </tbody>
            </table>
          </div>
          <!-- Charts -->
          <?php $monitor_robot->charts( $monitor_response ); ?>
        </div>
      </div>
    </div>
  </body>
</html>
