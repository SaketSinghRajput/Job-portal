<?php
if (!class_exists('NooMailChimp')) :

    class NooMailChimp
    {

        public static $instance;

        public static function getInstance()
        {
            if (empty(self::$instance)) {
                self::$instance = new self;
            }
            return self::$instance;
        }

        private function __construct()
        {
            // Ajax for mail list
            add_action('wp_ajax_noo_mail_list', array($this, 'ajax_mail_list'));

            add_action('wp_ajax_noo_mc_subscribe', array($this, 'ajax_mc_subscribe'));
            add_action('wp_ajax_nopriv_noo_mc_subscribe', array($this, 'ajax_mc_subscribe'));
        }

        public function ajax_mc_subscribe()
        {
            if (!check_ajax_referer('noo-subscribe', 'nonce', false)) {
                $this->_ajax_exit(__('Your session has expired. Please reload and retry.', 'noo'));
            }

            // setup the email and name varaibles
            $email = strip_tags($_POST['mc_email']);

            // check for a valid email
            if (!is_email($email)) {
                $this->_ajax_exit(__('Your email address is invalid. Click back and enter a valid email address.', 'noo'), __('Invalid Email', 'noo'));
            }

            $list_id = strip_tags($_POST['mc_list_id']);

            if (empty($list_id)) {
                $this->_ajax_exit(__('There\'s an unknown problem. Please reload and retry.', 'noo'));
            }

            $result = $this->_subscribe_email($email, $list_id);
           
            if ( is_wp_error($result)) {
            	$this->_ajax_exit($result->get_error_message());
            } 
            
            if($result){
            	$this->_ajax_exit(__('Thank you for your subscription.', 'noo'), true);
            } else {
                $this->_ajax_exit(__('There\'s an unknown problem. Please reload and retry.', 'noo'));
            }
        }

        public function ajax_mail_list()
        {
            $api_key = isset($_POST['api_key']) ? $_POST['api_key'] : '';
            if (empty($api_key)) {
                exit();
            }

            $lists = $this->get_mail_lists($api_key);
            if (empty($lists)) {
                exit();
            }

            foreach ($lists as $id => $list_name) {
                echo '<option value="' . $id . '" >' . $list_name . '</option>';
            }

            exit();
        }

        // get an array of all campaign monitor subscription lists
        public function get_mail_lists($api_key = '')
        {
            $api_key = trim($api_key);
            $api_key = empty($api_key) ? noo_get_option('noo_mailchimp_api_key') : $api_key;

            if(strlen($api_key) > 0 ) {

                $lists = array();
                
                $api = new MC4WP_API_v3($api_key);
                $list_data = $api->get_lists();
                if($list_data) {
                	foreach($list_data as $list) {
                		$lists[$list->id] = $list->name;
                	}
                }
                return $lists;
                    
            }

            return false;
        }

        // adds an email to the campaign_monitor subscription list
        private function _subscribe_email($email, $list_id)
        {
        	$api = new MC4WP_API_v3(mc4wp_get_api_key());
        	
        	$args = array(
        		'status' 		=> 'pending',
        		'email_address' => $email,
        		'interests' 	=> array(),
        		'merge_fields' 	=> array(),
        	);
        	try{
        		if($api->add_list_member($list_id, $args )){
        			return true;
        		}
        	}catch (MC4WP_API_Exception $mc4wp_e){
        		return new WP_Error( 'mc4wp_api_exception', $mc4wp_e->detail );
        	}catch (Exception $e){
        		return new WP_Error( 'mc4wp_api_exception', $e->getMessage() );
        	}
        	
        	return false;
        }

        private function _ajax_exit($data = '', $success = false, $redirect = '')
        {
            $response = array(
                'success' => $success,
                'data' => $data,
            );

            if (!empty($redirect)) {
                $response['redirect'] = $redirect;
            }

            echo json_encode($response);
            exit();
        }
    }

    NooMailChimp::getInstance();
endif;