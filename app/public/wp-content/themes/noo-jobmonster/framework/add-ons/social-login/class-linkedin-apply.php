<?php
class Noo_Apply_Linkedin {
    public $oauth;
    public function __construct(){
        if(!class_exists('Linkedin_OAuth2Client')){
            require_once 'oauth_client_linkedin.php';
        }
        $this->oauth = new Linkedin_OAuth2Client(jm_get_3rd_api_setting('linkedin_app_id'),jm_get_3rd_api_setting('linkedin_app_secret'));

        add_action( 'noo_apply_job_linkedin', array( $this, 'via_linkedin_form_apply' ) );
//        add_action('wp_footer',array($this,'noo_open_linkedin_apply_popup'));
    }
   public static function  get_apply_url($job_id){
        if(!class_exists('Linkedin_OAuth2Client')){
            require_once 'oauth_client_linkedin.php';
        }
        $url = new Linkedin_OAuth2Client();
        $state = wp_generate_password(12,false);
        $params = array(
            "client_id"     => jm_get_3rd_api_setting('linkedin_app_id'),
            "redirect_uri"  => add_query_arg(array('job_id'=>$job_id,'apply'=>'linkedin'),home_url('/')),
            "response_type" => "code",
            "scope" => "r_basicprofile r_emailaddress",
            "state" => $state
        );

        return $url->authorizeUrl($params);
    }
    public function oauth(){
        if(isset($_REQUEST['code']) && (!isset($_REQUEST['error']) || !$_REQUEST['error'])){
            $job_id = $_REQUEST['job_id'];
            $user_data = $this->get_linkedin_profile($job_id);
            $_SESSION['member_linkedin'] = $user_data;
            $_SESSION['open_popup_apply_linkedin'] = true;
            $link = get_permalink($job_id);
            $link = add_query_arg(array('apply'=>'apply_linkedin'),$link);
            wp_redirect($link);
           exit;
        }
    }
    private function get_linkedin_profile($job_id) {
        $this->oauth->redirect_uri = add_query_arg(array('job_id'=>$job_id,'apply'=>'linkedin'),home_url('/'));
        // Use GET method since POST isn't working
        $this->oauth->curl_authenticate_method = 'GET';

        // Request access token
        $response = $this->oauth->authenticate($_REQUEST['code']);
        $this->access_token = $response->{'access_token'};

        // Get first name, last name and email address, and load
        // response into XML object
        $xml = simplexml_load_string($this->oauth->get('https://api.linkedin.com/v1/people/~:(id,first-name,last-name,email-address,headline,specialties,positions:(id,title,summary,start-date,end-date,is-current,company),summary,site-standard-profile-request,picture-url,location:(name,country:(code)),industry)'));

        return $this->parse_xml_data($xml);
    }

    private function parse_xml_data($xml) {
        $data = array();
        $data['email'] = isset($xml->{'email-address'}) ? (string) $xml->{'email-address'} : '';
        $data['id'] = isset($xml->{'id'}) ? (string) $xml->{'id'} : '';
        $data['first_name'] = isset($xml->{'first-name'}) ? (string) $xml->{'first-name'} : '';
        $data['last_name'] = isset($xml->{'last-name'}) ? (string) $xml->{'last-name'} : '';
        $data['summary'] = isset($xml->{'summary'}) ? (string) $xml->{'summary'} : '';
        $data['linkedin_url'] = isset($xml->{'site-standard-profile-request'}->url) ? (string) $xml->{'site-standard-profile-request'}->url : '';
        $data['picture_url'] = isset($xml->{'picture-url'}) ? (string) $xml->{'picture-url'} : '';
        $data['location'] = array('name' => (string) $xml->{'location'}->{'name'}, 'country_code' => (string) $xml->{'location'}->{'country'}->{'code'});
        $data['industry'] = isset($xml->{'industry'}) ? (string) $xml->{'industry'} : '';
        $data['headline'] = isset($xml->{'headline'}) ? (string) $xml->{'headline'} : '';
        $data['specialties'] = isset($xml->{'specialties'}) ? (string) $xml->{'specialties'} : '';
        $data['positions'] = array('positions_current'=>(string)$xml->{'positions'}->{'title'},'current_company'=>(string)$xml->{'positions'}->{'company'});

        return $data;
    }
}
