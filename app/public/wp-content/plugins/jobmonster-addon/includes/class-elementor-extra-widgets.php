<?php
if(!class_exists('Noo_Elementor_Widgets')){
    class Noo_Elementor_Widgets {
        /**
         * @var Noo_Elementor_Widgets
         */
        public static $instance = null;

        /**
         * The version of file
         * @var string
         */
        public static $version = '1.0.0';

        /**
         * Add elementor widget
         */
        protected function init() {
            // Add category for Jobmonster Widget Addon
            add_action( 'elementor/init', array( $this, 'add_elementor_category' ),9 );
            // Register Widget
            add_action( 'elementor/widgets/widgets_registered', array( $this, 'add_elementor_widgets' ) );
            // Register Style
            add_action( 'elementor/frontend/after_register_styles', array( $this, 'register_frontend_styles' ), 10 );
            // Register Script
            add_action( 'elementor/frontend/after_register_scripts', array( $this, 'register_frontend_scripts' ), 10 );
            // Load Style for Editor of Elementor
            //this was the missing part.
            add_action( 'elementor/preview/enqueue_styles', array( $this, 'enqueue_preview_styles'));

        }

        public function register_frontend_styles() {
            wp_register_style( 'noo-rating',
                JOB_ADDON_ASSETS . '/vendor/rating/jquery.raty.css'
            );
            wp_register_style( 'owl-carousel',
                JOB_ADDON_ASSETS. '/vendor/owl-carousel/owl.carousel.min.css',
                array(),
                null
            );

            wp_register_style( 'noo-swiper',
                JOB_ADDON_ASSETS . '/vendor/swiper/css/swiper.min.css',
                array(),
                null
            );

            wp_register_style( 'jobmonster-addon',
                JOB_ADDON_ASSETS . '/css/jobmonster-addon.css',
                array()
            );
            wp_enqueue_style('jobmonster-addon');

            wp_register_style( 'owl-jobmonster-addon',
                JOB_ADDON_ASSETS . '/css/owl.jobaddon.css',
                array()
            );
            wp_enqueue_style('owl-jobmonster-addon');
        }
        public function register_frontend_scripts() {
            wp_register_script('vendor-easing',
                JOB_ADDON_ASSETS . '/vendor/easing-1.3.0.min.js',
                array('jquery'),
                null,
                true
            );
            wp_register_script('vendor-appear',
                JOB_ADDON_ASSETS . '/vendor/jquery.appear.js',
                array(
                'jquery',
                'vendor-easing',
            ), null, true);
            wp_register_script('vendor-countTo',
                JOB_ADDON_ASSETS . '/vendor/jquery.countTo.js',
                array('jquery', 'vendor-appear'),
                null,
                true
            );
            wp_register_script(
                'noo-rating', JOB_ADDON_ASSETS . '/vendor/rating/jquery.raty.js',
                null,
                null,
                false
            );
            wp_localize_script('noo-rating', 'noo_rating', array(
                'starHalf' => JOB_ADDON_ASSETS . '/vendor/rating/images/star-half.png',
                'starOff' => JOB_ADDON_ASSETS . '/vendor/rating/images/star-off.png',
                'starOn' => JOB_ADDON_ASSETS . '/vendor/rating/images/star-on.png',
            ));

            wp_register_script( 'owl-carousel',
                JOB_ADDON_ASSETS. '/vendor/owl-carousel/owl.carousel.min.js',
                array('jquery'),
                null,
                true
            );
            wp_register_script( 'noo-swiper',
                JOB_ADDON_ASSETS . '/vendor/swiper/js/swiper.min.js', array('jquery'),
                null,
                false
            );
            wp_register_script( 'addon-imageloaded',
                JOB_ADDON_ASSETS. '/vendor/imageloaded/imageloaded.pkgd.min.js',
                array('jquery'),
                null,
                true
            );
            wp_register_script( 'isotope',
                JOB_ADDON_ASSETS. '/vendor/isotope/isotope.pkgd.min.js',
                array('jquery,addon-imageloaded'),
                null,
                true
            );
            wp_register_script( 'jquery-countdown',
                JOB_ADDON_ASSETS. '/vendor/countdown/jquery.countdown.min.js',
                array('jquery'),
                null,
                true
            );
            wp_register_script( 'noo-elementor',
                JOB_ADDON_ASSETS. '/js/noo-elementor.js',
                array('jquery'),
                null,
                true
            );
        }
        //
        public function enqueue_preview_styles(){
            wp_enqueue_style('owl-carousel');
        }

        /**
         * Add the Category for JObmonster Widget Addon
         */
        public function add_elementor_category() {

            $category_args = apply_filters( 'noo_elementor_category_args', array(
                'slug'  => 'noo-element-widgets',
                'title' => esc_html__( 'Jobmonster Addon', 'noo' ),
                'icon'  => 'fa fa-plug',
            ) );

            \Elementor\Plugin::instance()->elements_manager->add_category(
                $category_args['slug'],
                array(
                    'title' => $category_args['title'],
                    'icon'  => $category_args['slug'],
                ),
                1
            );
        }
        /**
         * Require and instantiate Elementor Widgets
         *
         * @param $widgets_manager
         */
        public function add_elementor_widgets( $widgets_manager ) {

            $elementor_widgets = $this->get_dir_files( __DIR__ . '/elementor-extra' );
            foreach ( $elementor_widgets as $widget ) {
                require_once $widget;

                $widget = basename( $widget, ".php" );

                $classname = $this->convert_filename_to_classname( $widget );
                if ( class_exists( $classname ) ) {
                    $widget_object = new $classname();
                    $widgets_manager->register_widget_type( $widget_object );
                }
            }
        }

        /**
         * Returns an array of all PHP files in the specified absolute path.
         * Inspired from jetpack's glob_php
         *
         * @param string $absolute_path The absolute path of the directory to search.
         *
         * @return array Array of absolute paths to the PHP files.
         */
        protected function get_dir_files( $absolute_path ) {
            if ( function_exists( 'glob' ) ) {
                return glob( "$absolute_path/*.php" );
            }

            $absolute_path = untrailingslashit( $absolute_path );
            $files         = array();
            if ( ! $dir = @opendir( $absolute_path ) ) {
                return $files;
            }

            while ( false !== $file = readdir( $dir ) ) {
                if ( '.' == substr( $file, 0, 1 ) || '.php' != substr( $file, - 4 ) ) {
                    continue;
                }

                $file = "$absolute_path/$file";

                if ( ! is_file( $file ) ) {
                    continue;
                }

                $files[] = $file;
            }

            closedir( $dir );

            return $files;
        }

        protected function convert_filename_to_classname( $widget ) {
            $classname = ucwords( $widget, "-" );
            $classname = str_replace( '-', '_', $classname );
            $classname = '\\Noo_Elementor_Widgets\\' . $classname;
            return $classname;
        }

        /**
         *
         * @static
         * @since 1.0.0
         * @access public
         * @return ElementorExtraWidgets
         */
        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
                self::$instance->init();
            }

            return self::$instance;
        }

        /**
         * Throw error on object clone
         *
         * The whole idea of the singleton design pattern is that there is a single
         * object therefore, we don't want the object to be cloned.
         *
         * @access public
         * @since 1.0.0
         * @return void
         */
        public function __clone() {
            // Cloning instances of the class is forbidden.
            _doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'noo' ), '1.0.0' );
        }

        /**
         * Disable unserializing of the class
         *
         * @access public
         * @since 1.0.0
         * @return void
         */
        public function __wakeup() {
            // Unserializing instances of the class is forbidden.
            _doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'noo' ), '1.0.0' );
        }
    }
    Noo_Elementor_Widgets::instance();
}