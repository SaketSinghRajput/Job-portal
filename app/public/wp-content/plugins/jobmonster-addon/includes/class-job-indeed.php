<?php
if (!class_exists('Noo_Elementor_Job_Indeed')):
    class  Noo_Elementor_Job_Indeed
    {
        public function __construct()
        {
            add_action('init', array($this, 'init'), 100);

            add_action('wp_ajax_load_job_item', array($this, 'load_job_item'));
            add_action('wp_ajax_nopriv_load_job_item', array($this, 'load_job_item'));
        }

        public function init()
        {
            $this->registerRSS();
        }

        public function registerRSS()
        {
            add_feed('indeed-job', array($this, 'xml_feed'));
        }

        public static function _get_default_args()
        {
            $query    = '';
            $location = '';
            $type     = 'fulltime';
            if (isset($_REQUEST['post_type']) && $_REQUEST['post_type'] == 'noo_job') {
                if (isset($_REQUEST['s']) && !empty($_REQUEST['s'])) {
                    $query    = $_REQUEST['s'];
                }
                if (isset($_REQUEST['location']) && !empty($_REQUEST['location'])) {
                    $location = $_REQUEST['location'];
                }
                if (isset($_REQUEST['type']) && !empty($_REQUEST['type'])) {
                    $type     = $_REQUEST['type'];
                }
            }
            if (is_tax('job_location')) {
                global $wp_query;
                if (isset($wp_query->query_vars['job_location'])) {
                    $location = $wp_query->query_vars['job_location'];
                }
            }
            if (is_tax('job_type')) {
                global $wp_query;
                if (isset($wp_query->query_vars['job_type'])) {
                    $type = $wp_query->query_vars['job_type'];
                }
            }
            $default = array(
                'co'        => '',
                'filter'    => '',
                'fromage'   => '',
                'jt'        => $type,
                'l'         => $location,
                'latlong'   => 1,
                'limit'     => 1,
                'publisher' => '',
                'q'         => $query,
                'radius'    => '',
                'sort'      => '',
                'st'        => '',
                'start'     => 0,
                'v'         => 2
            );
            return apply_filters('jm_indeed_integration_default_args', $default);
        }

        public static function _get_transient_key($args, $step = 0)
        {
            unset($args['start']);
            unset($args['limit']);

            ksort($args);
            $key = 'indeed';

            foreach ($args as $k => $v) {
                $key .= "_{$k}:{$v}";
            }

            return $key . '_' . $step;
        }

        public static function _get_indeed_url($args = array())
        {
            foreach ($args as $key => $value) {
                if (is_array($value)) {
                    $value = reset($value);
                }
                $args[$key] = urlencode($value);
            }
            return add_query_arg($args, 'http://api.indeed.com/ads/apisearch');
        }

        public static function _get_indeed_jobs($args = array())
        {
            $trunk   = 25;
            $default = self::_get_default_args();
            $args    = array_merge($default, $args);
            $results = array();
            $start   = isset($args['start']) ? absint($args['start']) : 0;
            $limit   = isset($args['limit']) ? absint($args['limit']) : 1;
            if (isset($_POST['location'])) {
                $args['1'] = esc_html($_POST['location']);
            }
            $step       = (int)($start / $trunk);
            $step_start = $start % $trunk;
            $step_limit = ($limit < $trunk) ? $limit % $trunk : $trunk;
            do {
                $transient_key = self::_get_transient_key($args, $step);
                $jobs   = get_transient($transient_key);
                if (!$jobs) {
                    $args['start'] = $step * $trunk;
                    $args['limit'] = $trunk;

                    $url = self::_get_indeed_url($args);

                    $get_list = file_get_contents($url);
                    if (!empty($get_list) && preg_match('#<result>(.*?)</result>#is', $get_list)) {
                        preg_match_all('#<result>(.*?)</result>#is', $get_list, $jobs);
                    }

                    $jobs = isset($jobs[1]) ? $jobs[1] : array();
                    set_transient($transient_key, $jobs, MINUTE_IN_SECONDS);
                }

                if (!empty($jobs) && count($jobs)) {
                    $results = array_merge($results, array_slice($jobs, $step_start, $step_limit));
                }
                $limit -= $trunk;
            } while ($limit > 0);
            return $results;
        }

        public static function _indeed_one_job($container = '', $type = '')
        {
            if (empty($container)) {
                return;
            }
            // === << precess title
            preg_match('#<jobtitle>(.*?)</jobtitle>#is', $container, $tit);
            $title = trim($tit[1]);

            // === << precess url
            preg_match('#<url>(.*?)</url>#is', $container, $url);
            $url = trim($url[1]);

            // ===== <<< [ Check enable show company url ] >>> ===== //
            $page_job = file_get_contents($url);
            if (!empty($page_job)) {
                preg_match('#<div class="cmp_title">(.*?)href="(.*?)"#is', $page_job, $url_company);
                if (isset($url_company[2]))
                    $url_company = "http://www.indeed.com{$url_company[2]}";
                else
                    $url_company = $url;
            }

            // === << precess company
            preg_match('#<company>(.*?)</company>#is', $container, $company);
            $company = trim($company[1]);

            // === << precess formattedLocationFull
            preg_match('#<formattedLocationFull>(.*?)</formattedLocationFull>#is', $container, $formattedLocationFull);
            $formattedLocationFull = trim($formattedLocationFull[1]);

            // === << precess date
            preg_match('#<date>(.*?)</date>#is', $container, $date);
            $date = explode(' ', substr(trim($date[1]), 5, 11));

            $date_text = "{$date[1]} $date[0], $date[2]";
            $date_unix = strtotime("{$date[2]}-{$date[1]}-{$date[0]}");
            if (!empty($date_unix)) {
                $date_text = date_i18n(get_option('date_format'), $date_unix);
            }
            ?>
            <article class="noo_job type-noo_job hentry indeed-job loadmore-item">
                <div class="loop-item-wrap">
                    <div class="item-featured">
                        <a href="<?php echo (isset( $url_company ) ? $url_company : $url )?>">
                            <img src="<?php echo JOB_ADDON_ASSETS ?>/images/company-logo.png">
                        </a>
                    </div>

                    <div class="loop-item-content" style="width: 73%;float: left; padding-left:25px">
                        <h5 class="loop-item-title">
                            <a href="<?php echo $url ?>" title="<?php echo $title; ?>" target="_blank"><?php echo $title; ?></a>
                        </h5>
                        <p class="content-meta">
							<span class="job-company">
								<a href="<?php echo (isset( $url_company ) ? $url_company : $url )?>" target="_blank"><?php echo $company ?></a>
							</span>
                            <?php if( !empty( $type ) ) : ?>
                                <span class="job-type">
									<a href="<?php echo $url ?>" style="color: <?php echo $type->color; ?>" target="_blank">
										<i class="fa fa-bookmark"></i><?php echo $type->name; ?>
									</a>
								</span>
                            <?php endif; ?>
                            <span class="job-location">
								<i class="fa fa-map-marker"></i>
								<a href="<?php echo $url ?>" target="_blank">
									<em><?php echo $formattedLocationFull; ?></em>
								</a>
							</span>
                            <span>
								<time class="entry-date" datetime="<?php echo $date_text; ?>">
									<i class="fa fa-calendar"></i>
                                    <?php echo $date_text; ?>
								</time>
							</span>
                        </p>
                    </div>
                    <div class="show-view-more" style="float: right;">
                        <a class="btn btn-primary" title="<?php _e('View more', 'noo'); ?>" href="<?php echo $url ?>" target="_blank">
                            <i class="indeed-icon"></i>
                            <?php _e('View more', 'noo'); ?>
                        </a>
                    </div>

                </div>
            </article>
            <?php
        }

        public function load_job_item()
        {
            $args = array();
            if (isset($_POST['public_id'])) $args['publisher'] = esc_html($_POST['public_id']);
            if (isset($_POST['indeed_query'])) $args['q'] = esc_html($_POST['indeed_query']);
            if (isset($_POST['indeed_localtion'])) $args['l'] = esc_html($_POST['indeed_localtion']);
            if (isset($_POST['indeed_job_type'])) $args['jt'] = esc_html($_POST['indeed_job_type']);
            if (isset($_POST['indeed_country'])) $args['co'] = esc_html($_POST['indeed_country']);
            if (isset($_POST['start'])) $args['start'] = absint($_POST['start']);
            if (isset($_POST['limit'])) $args['limit'] = absint($_POST['limit']);

            $type = get_term_by('slug', $args['jt'], 'job_type');
            if ($type) {
                $type->color = jm_get_job_type_color($type->term_id);
            } else {
                $type        = new stdClass();
                $type->name  = $args['jt'];
                $type->color = '#f14e3b';
            }

            $list_job = $this->_get_indeed_jobs($args);
            if (!empty($list_job)) :
                foreach ($list_job as $container) :
                    $this->_indeed_one_job($container, $type);
                endforeach;
            endif;
            wp_die();
        }
    }

    new Noo_Elementor_Job_Indeed();
endif;