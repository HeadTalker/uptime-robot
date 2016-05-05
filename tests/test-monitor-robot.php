<?php
class monitor_robot_test extends PHPUnit_Framework_TestCase {

  /**
  * Set up and init our class to be used for other tests
  */

  function setUp() {
    $this->monitor_robot = new monitor_robot();
  }

  /**
  * Test curl to see if it is activated
  */

  function test_monitor_curl() {
    if ( $this->monitor_robot->monitor_curl() === FALSE ) {
      echo 'You don\'t have CURL activated! Download it for free. http://curl.haxx.se/download.html ';
    }
    $this->assertTrue( $this->monitor_robot->monitor_curl() );
  }

  /**
  * Test monitor table fallback if not an array
  * incase there is no monitors or uptime robot is down
  */

  function test_monitor_table_body_not_array() {
    $this->assertEquals( '', $this->monitor_robot->monitor_table_body( 'random string' ) );
  }

  /**
  * Test for timestamps array and values array return
  */

  function test_monitor_response_data() {

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

    list( $response_datetime, $response_value ) = $this->monitor_robot->monitor_response_data( $test_array );

    $this->assertTrue( is_array( $response_datetime ) );
    $this->assertTrue( is_array( $response_value ) );

  }

  /**
  * Test Monitor Type Switch Case Statement
  */

  function test_monitor_type_https() {
    $this->assertEquals( 'HTTP(s)', $this->monitor_robot->monitor_type( 1 ) );
  }

  function test_monitor_type_keyword() {
    $this->assertEquals( 'Keyword', $this->monitor_robot->monitor_type( 2 ) );
  }

  function test_monitor_type_ping() {
    $this->assertEquals( 'Ping', $this->monitor_robot->monitor_type( 3 ) );
  }

  function test_monitor_type_port() {
    $this->assertEquals( 'Port', $this->monitor_robot->monitor_type( 4 ) );
  }

  function test_monitor_type_error() {
    $this->assertEquals( 'Unknown or Error', $this->monitor_robot->monitor_type( 387879320 ) );
  }

  /**
  * Test Monitor Status Switch Case Statement
  */

  function test_monitor_status_paused() {
    $this->assertEquals( 'Paused', $this->monitor_robot->monitor_status( 0 ) );
  }

  function test_monitor_status_not_checked() {
    $this->assertEquals( 'Not Checked Yet', $this->monitor_robot->monitor_status( 1 ) );
  }

  function test_monitor_status_up() {
    $this->assertEquals( '<img src="svg/circle.svg">', $this->monitor_robot->monitor_status( 2 ) );
  }

  function test_monitor_status_seems_down() {
    $this->assertEquals( 'SEEMS DOWN!', $this->monitor_robot->monitor_status( 8 ) );
  }

  function test_monitor_status_down() {
    $this->assertEquals( 'DOWN!', $this->monitor_robot->monitor_status( 9 ) );
  }

  function test_monitor_status_error() {
    $this->assertEquals( 'Unknown or Error', $this->monitor_robot->monitor_status( 62332 ) );
  }


}
