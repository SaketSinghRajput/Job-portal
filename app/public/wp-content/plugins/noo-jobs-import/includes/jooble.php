<?php

class Jooble
{
	public $jooble_uri = '';
	private static $API_SEARCH_REQUIRED = array( array( 'q', 'l' ) );
	private static $API_JOBS_REQUIRED   = array( 'jobkeys' );
	public function __construct( $api_key, $country = 'us' ){
		$this->api_key = $api_key;
		$this->country   = $country;
		$this->jooble_uri = "https://{$this->country}.jooble.org/api/";
	}

	public function search( $args = array() ){
		return $this->process_request( $this->jooble_uri, $this->validate_args( self::$API_SEARCH_REQUIRED, $args ) );
	}

	private function process_request($url, $args) {
		$keyword = $args['q'];
		$location = $args['l'];

		//create request object
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url."".$this->api_key);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, '{ "keywords": "'.$keyword.'", "location": "'.$location.'" }');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));

		// receive server response ...
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$server_output = curl_exec ($ch);
		$err = curl_error($ch);
		curl_close ($ch);

		if ($err) {
			return $err;
		} else {
			return $server_output;
		}
	}

	private function validate_args($required_fields, $args){
		foreach($required_fields as $field){
			if( is_array( $field ) ){
				$has_one_required = false;
				foreach($field as $f){
					if(array_key_exists($f, $args)){
						$has_one_required = True;
						break;
					}
				}
				if( !$has_one_required ){
					throw new Exception(sprintf( esc_html__( "You must provide one of the following %s", 'noo' ) , implode(",", $field)));
				}
			} elseif( !array_key_exists( $field, $args ) ){
				throw new Exception( esc_html__( "The field $field is required", 'noo' ) );
			}
		}
		return $args;
	}
}