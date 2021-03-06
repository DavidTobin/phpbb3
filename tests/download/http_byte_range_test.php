<?php
/**
*
* @package testing
* @copyright (c) 2010 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

require_once dirname(__FILE__) . '/../../phpBB/includes/functions_download.php';
require_once dirname(__FILE__) . '/../mock/request.php';

class phpbb_download_http_byte_range_test extends phpbb_test_case
{
	public function test_find_range_request()
	{
		// Missing 'bytes=' prefix
		$GLOBALS['request'] = new phpbb_mock_request();
		$GLOBALS['request']->set_header('Range', 'bztes=');
		$this->assertEquals(false, phpbb_find_range_request());
		unset($GLOBALS['request']);

		$GLOBALS['request'] = new phpbb_mock_request();
		$_ENV['HTTP_RANGE'] = 'bztes=';
		$this->assertEquals(false, phpbb_find_range_request());
		unset($_ENV['HTTP_RANGE']);

		$GLOBALS['request'] = new phpbb_mock_request();
		$GLOBALS['request']->set_header('Range', 'bytes=0-0,123-125');
		$this->assertEquals(array('0-0', '123-125'), phpbb_find_range_request());
		unset($GLOBALS['request']);
	}

	/**
	* @dataProvider parse_range_request_data()
	*/
	public function test_parse_range_request($request_array, $filesize, $expected)
	{
		$this->assertEquals($expected, phpbb_parse_range_request($request_array, $filesize));
	}

	public function parse_range_request_data()
	{
		return array(
			// Does not read until the end of file.
			array(
				array('3-4'),
				10,
				false,
			),

			// Valid request, handle second range.
			array(
				array('0-0', '120-125'),
				125,
				array(
					'byte_pos_start'	=> 120,
					'byte_pos_end'		=> 124,
					'bytes_requested'	=> 5,
					'bytes_total'		=> 125,
				)
			),
		);
	}
}
