<?php
class SampleTest extends PHPUnit_Framework_TestCase {


  /**
  * Test curl to see if it is activated
  */

  function test_monitor_curl() {
    $monitor_robot = new monitor_robot();
    if ( $monitor_robot->monitor_curl() === FALSE ) {
      echo 'You don\'t have CURL activated! Download it for free. http://curl.haxx.se/download.html ';
    }
    $this->assertTrue( $monitor_robot->monitor_curl() );
  }

  /**
  * Test monitor table fallback if not an array
  * incase there is no monitors or uptime robot is down
  */

  function test_monitor_table_body_not_array() {
    $monitor_robot = new monitor_robot();
    $this->assertEquals( '', $monitor_robot->monitor_table_body( 'random string' ) );
  }

  /**
  * Test for timestamps array and values array return
  */

  function test_monitor_response_data() {
    $monitor_robot = new monitor_robot();

    $test_array = array(
      '0' => array(
        'datetime' => '03/24/2016 22:31:57',
        'value'    => 204 ),
      '1' => array(
        'datetime' => '03/24/2016 21:01:57',
        'value'    => 250 ),
      '2' => array(
        'datetime' => '03/24/2016 16:31:56',
        'value'    => 406 ),
    );

    list( $response_datetime, $response_value ) = $monitor_robot->monitor_response_data( $test_array );

    $this->assertTrue( is_array( $response_datetime ) );
    $this->assertTrue( is_array( $response_value ) );

  }

  /**
  * Test Monitor Type Switch Case Statement
  */

  function test_monitor_type_https() {
    $monitor_robot = new monitor_robot();
    $this->assertEquals( 'HTTP(s)', $monitor_robot->monitor_type( 1 ) );
  }

  function test_monitor_type_keyword() {
    $monitor_robot = new monitor_robot();
    $this->assertEquals( 'Keyword', $monitor_robot->monitor_type( 2 ) );
  }

  function test_monitor_type_ping() {
    $monitor_robot = new monitor_robot();
    $this->assertEquals( 'Ping', $monitor_robot->monitor_type( 3 ) );
  }

  function test_monitor_type_port() {
    $monitor_robot = new monitor_robot();
    $this->assertEquals( 'Port', $monitor_robot->monitor_type( 4 ) );
  }

  function test_monitor_type_error() {
    $monitor_robot = new monitor_robot();
    $this->assertEquals( 'Unknown or Error', $monitor_robot->monitor_type( 387879320 ) );
  }

  /**
  * Test Monitor Status Switch Case Statement
  */

  function test_monitor_status_paused() {
    $monitor_robot = new monitor_robot();
    $this->assertEquals( 'Paused', $monitor_robot->monitor_status( 0 ) );
  }

  function test_monitor_status_not_checked() {
    $monitor_robot = new monitor_robot();
    $this->assertEquals( 'Not Checked Yet', $monitor_robot->monitor_status( 1 ) );
  }

  function test_monitor_status_up() {
    $monitor_robot = new monitor_robot();
    $this->assertEquals( '<img src="svg/circle.svg">', $monitor_robot->monitor_status( 2 ) );
  }

  function test_monitor_status_seems_down() {
    $monitor_robot = new monitor_robot();
    $this->assertEquals( 'SEEMS DOWN!', $monitor_robot->monitor_status( 8 ) );
  }

  function test_monitor_status_down() {
    $monitor_robot = new monitor_robot();
    $this->assertEquals( 'DOWN!', $monitor_robot->monitor_status( 9 ) );
  }

  function test_monitor_status_error() {
    $monitor_robot = new monitor_robot();
    $this->assertEquals( 'Unknown or Error', $monitor_robot->monitor_status( 62332 ) );
  }


}
