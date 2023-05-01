<?php
/**
 * Plugin Name: Jobmonster Elementor Addon
 * Description: Elementor Widget Extra for Jobmonster Theme
 * Version:  1.1.0
 * Plugin URI :  https://www.nootheme.com
 * Author: NooTheme Team
 * Author URI:  https://www.nootheme.com
 * Text Domain: noo
 * License: GPLv2 or later
 */
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Jobmonster_Addon' ) ) {
    class Jobmonster_Addon{
        function __construct() {
            $this->define_constants();
            $this->define_hook();
            $this->include_files();
        }
        function define_constants() {
            // Plugin Folder Url
            if( !defined( 'JOB_ADDON_URL' ) ) {
                define( 'JOB_ADDON_URL', plugin_dir_url(__FILE__) );
            }
            if( !defined( 'JOB_ADDON_ASSETS' ) ) {
                define('JOB_ADDON_ASSETS', JOB_ADDON_URL. 'assets' );
            }
            if( !defined( 'JOB_ADDON_INCLUDES' ) ) {
                define('JOB_ADDON_INCLUDES', JOB_ADDON_URL. 'includes' );
            }
            // Plugin Folder Path
            if( !defined( 'JOB_ADDON_DIR' ) ) {
                define('JOB_ADDON_DIR', plugin_dir_path(__FILE__) );
            }
            if( !defined( 'JOB_ADDON_INCLUDES_DIR' ) ) {
                define('JOB_ADDON_INCLUDES_DIR', JOB_ADDON_DIR. 'includes' );
            }
        }
        function define_hook(){
            add_action( 'plugins_loaded', array($this, 'job_load_textdomain') );
            // Check Elementor require
            if(!$this->is_elementor_active()){
                add_action('admin_notices',array($this,'job_elemtentor_notice'));
            }
        }

        function include_files(){
            require_once JOB_ADDON_INCLUDES_DIR . '/class-elementor-extra-widgets.php';
            require_once JOB_ADDON_INCLUDES_DIR . '/functions.php';
            require_once JOB_ADDON_INCLUDES_DIR . '/class-job-indeed.php';
        }
        function job_load_textdomain() {
            load_plugin_textdomain( 'noo', false, JOB_ADDON_URL . '/languages/'  );
        }

        function job_elemtentor_notice(){
            $plugin  = get_plugin_data(__FILE__);
            echo '
				<div id="message" class="notice notice-warning">
				    <p>' . sprintf(__('<strong>%s</strong> requires <strong><a href="https://wordpress.org/plugins/elementor/" target="_blank">Elementor</a></strong> plugin to be installed and activated on your site.', 'noo'), $plugin['Name']) . '</p>
				</div>';
        }
        function is_elementor_active(){
            $active_plugins = (array) get_option( 'active_plugins' , array() );

            if( is_multisite() ){
                $active_plugins = array_merge($active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
            }

            return in_array( 'elementor/elementor.php', $active_plugins ) || array_key_exists( 'elementor/elementor.php', $active_plugins );
        }

    }
    new Jobmonster_Addon;
}