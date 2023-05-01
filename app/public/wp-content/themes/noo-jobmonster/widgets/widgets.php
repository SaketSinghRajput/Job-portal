<?php

class Noo_Widget extends WP_Widget {

	public $widget_cssclass;

	public $widget_description;

	public $widget_id;

	public $widget_name;

	public $fields;

	public $cached = true;

	/**
	 * Constructor
	 */
	public function __construct() {
		$widget_ops = array( 'classname' => $this->widget_cssclass, 'description' => $this->widget_description );

		parent::__construct( $this->widget_id, $this->widget_name, $widget_ops );

		if ( $this->cached ) {
			add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
			add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
			add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
		}
	}

	/**
	 * get_cached_widget function.
	 */
	public function get_cached_widget( $args ) {
		$cache = wp_cache_get( apply_filters( 'dh_cached_widget_id', $this->widget_id ), 'widget' );

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];

			return true;
		}

		return false;
	}

	/**
	 * Cache the widget
	 *
	 * @param string $content
	 */
	public function cache_widget( $args, $content ) {
		$cache[ $args['widget_id'] ] = $content;

		wp_cache_set( apply_filters( 'dh_cached_widget_id', $this->widget_id ), $cache, 'widget' );
	}

	/**
	 * Flush the cache
	 *
	 * @return void
	 */
	public function flush_widget_cache() {
		wp_cache_delete( apply_filters( 'dh_cached_widget_id', $this->widget_id ), 'widget' );
	}

	/**
	 * update function.
	 *
	 * @see WP_Widget->update
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$fields   = $this->get_fields();

		if ( ! $fields ) {
			return $instance;
		}

		foreach ( $fields as $key => $setting ) {

			if ( isset( $new_instance[ $key ] ) ) {
				if ( isset( $setting['multiple'] ) ) {
					$instance[ $key ] = implode( ',', $new_instance[ $key ] );
				} else {
					$instance[ $key ] = sanitize_text_field( $new_instance[ $key ] );
				}
			} elseif ( 'checkbox' === $setting['type'] ) {
				$instance[ $key ] = 0;
			}
		}
		if ( $this->cached ) {
			$this->flush_widget_cache();
		}

		return $instance;
	}

	/**
	 * form function.
	 *
	 * @see WP_Widget->form
	 *
	 * @param array $instance
	 */
	public function form( $instance ) {
		$fields = $this->get_fields();
		if ( ! $fields ) {
			return;
		}

		foreach ( $fields as $key => $setting ) {
			$setting['key']   = $key;
			$setting['value'] = isset( $instance[ $key ] ) ? $instance[ $key ] : '';
			$this->_render_field( $setting );
		}
	}

	public function get_fields() {
		return $this->fields;
	}

	protected function _render_field( $setting = array() ) {
		$key   = $setting['key'];
		$value = isset( $setting['value'] ) ? $setting['value'] : '';
		switch ( $setting['type'] ) {
			case "text" :
				?>
                <p>
                    <label
                            for="<?php echo $this->get_field_id( $key ); ?>"><?php echo esc_html( $setting['label'] ); ?></label>
                    <input class="widefat"
                           id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"
                           name="<?php echo $this->get_field_name( $key ); ?>" type="text"
                           value="<?php echo esc_attr( $value ); ?>"/>
                </p>
				<?php
				break;

			case "number" :
				?>
                <p>
                    <label
                            for="<?php echo $this->get_field_id( $key ); ?>"><?php echo esc_html( $setting['label'] ); ?></label>
                    <input class="widefat"
                           id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"
                           name="<?php echo $this->get_field_name( $key ); ?>" type="number"
                           step="<?php echo esc_attr( $setting['step'] ); ?>"
                           min="<?php echo esc_attr( $setting['min'] ); ?>"
                           max="<?php echo esc_attr( $setting['max'] ); ?>"
                           value="<?php echo esc_attr( $value ); ?>"/>
                </p>
				<?php
				break;
			case "select" :
				if ( isset( $setting['multiple'] ) ) :
					$value = explode( ',', $value );
				endif;
				?>
                <p>
                    <label
                            for="<?php echo $this->get_field_id( $key ); ?>"><?php echo esc_html( $setting['label'] ); ?></label>
                    <select class="widefat"
                            id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"
						<?php if ( isset( $setting['multiple'] ) ): ?> multiple="multiple" <?php endif; ?>
                            name="<?php echo $this->get_field_name( $key ); ?><?php if ( isset( $setting['multiple'] ) ): ?>[]<?php endif; ?>">
						<?php foreach ( $setting['options'] as $option_key => $option_value ) : ?>
                            <option value="<?php echo esc_attr( $option_key ); ?>"
								<?php if ( isset( $setting['multiple'] ) ): selected( in_array( $option_key, $value ), true );
								else: selected( $option_key, $value ); endif; ?>><?php echo esc_html( $option_value ); ?>
                            </option>
						<?php endforeach; ?>
                    </select>
                </p>
				<?php
				if ( @$setting['multi_fields'] == true ) : ?>
                    <button id="add_multi_fields"><span class="dashicons dashicons-plus"></span></button>
                    <script type="text/javascript">
                        jQuery(document).ready(function ($) {
                            var i = 0;
                            $('button#add_multi_fields').on('click', 'span', function (event) {
                                event.preventDefault();
                            });
                        });
                    </script>
				<?php endif;
				break;

			case "checkbox" :
				?>
                <p>
                    <input id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"
                           name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>"
                           type="checkbox" value="1" <?php checked( $value, 1 ); ?> /> <label
                            for="<?php echo $this->get_field_id( $key ); ?>"><?php echo esc_html( $setting['label'] ); ?></label>
                </p>
				<?php
				break;
		}
	}
}

class Noo_MailChimp extends WP_Widget {

	public function __construct() {
		parent::__construct( 'noo_mailchimp_widget',  // Base ID
			'Noo MailChimps',  // Name
			array(
				'classname'   => 'mailchimp-widget',
				'description' => __( 'Display simple MailChimp subscribe form.', 'noo' ),
			) );
	}

	public function widget( $args, $instance ) {
		if(!defined('MC4WP_VERSION')){
			echo '<p><strong>' . sprintf(__('NOO Mailchimp Widget require active plugin <a href="%s">MC4WP: Mailchimp for WordPress</a>.','noo'),'https://wordpress.org/plugins/mailchimp-for-wp/') . '</strong></p>';
			return;
		}
		
		extract( $args );
		
		if ( ! empty( $instance['title'] ) ) {
			$title = apply_filters( 'widget_title', $instance['title'] );
		}
		
		$subscribe_text = isset($instance['subscribe_text']) ? $instance['subscribe_text'] : __( 'Subscribe to stay update', 'noo' );
		
		
		echo $before_widget;
		
		if ( ! empty( $title ) ) {
			echo $before_title . $title . $after_title;
		} 
		?>
        <form class="mc-subscribe-form">
			<label for="email"><?php echo esc_attr( $subscribe_text ); ?></label>
            <div class="mc-email-wrap">
            <input type="email" id="email" name="mc_email" class="form-control mc-email" value="" placeholder="<?php _e( 'Enter your email here...', 'noo' ); ?>"/>
            </div>
            <?php do_action('noo_mailchimp_widget_form'); ?>
            <input type="hidden" name="mc_list_id" value="<?php echo( ! empty( $instance['mail_list'] ) ? esc_attr( @$instance['mail_list'] ) : '' ); ?>"/>
            <input type="hidden" name="action" value="noo_mc_subscribe"/>
			<?php wp_nonce_field( 'noo-subscribe', 'nonce' ); ?>
        </form>
		<?php
		echo $after_widget;
	}

	public function form( $instance ) {
		$defaults = array(
			'title'          => '',
			'subscribe_text' => __( 'Subscribe to stay update', 'noo' ),
			'mail_list'      => '',
		);

		$instance = wp_parse_args( (array) $instance, $defaults );
		$mail_list = null;
		
		if(defined('MC4WP_VERSION')){
			$api_key   = mc4wp_get_api_key();
			$mail_list = ! empty( $api_key ) ? NooMailChimp::getInstance()->get_mail_lists( $api_key ) : null;
		}
		echo '
        <p>
            <label>' . __( 'Title', 'noo' ) . ':</label>
            <input type="text" name="' . $this->get_field_name( 'title' ) . '" id="' . $this->get_field_id( 'title' ) . '" value="' . esc_attr( $instance['title'] ) . '" class="widefat" />
        </p>
        <p>
            <label>' . __( 'Subscribe Text', 'noo' ) . ':</label>
            <input type="text" name="' . $this->get_field_name( 'subscribe_text' ) . '" id="' . $this->get_field_id( 'subscribe_text' ) . '" value="' . esc_attr( $instance['subscribe_text'] ) . '" class="widefat" />
        </p>';
		if ( ! empty( $mail_list ) ) {
			echo '
        <p>
            <label>' . __( 'Subscribe Mail List', 'noo' ) . ':</label>
            <select name="' . $this->get_field_name( 'mail_list' ) . '" id="' . $this->get_field_id( 'mail_list' ) . '" class="widefat" >';
			echo '<option value=""> - </option>';
			foreach ( $mail_list as $id => $list_name ) {
				echo '<option value="' . $id . '" ' . selected( $instance['mail_list'], $id, false ) . '>' . $list_name . '</option>';
			}
			echo '  </select>
        </p>';
		} else {
			if(!defined('MC4WP_VERSION')){
				echo '<p><strong>' . sprintf(__('NOO Mailchimp Widget require active plugin <a href="%s">MC4WP: Mailchimp for WordPress</a>.','noo'),'https://wordpress.org/plugins/mailchimp-for-wp/') . '</strong></p>';
			}elseif (empty($mail_list)){
				echo '<p><strong>' . sprintf( __( 'There\'s a problem getting your mail list, please check your API key at MailChimp Settings in <a href="%s" target="_blank">Mailchimp for WordPress: API Settings</a>', 'noo' ), admin_url('admin.php?page=mailchimp-for-wp') ) . '</strong></p>';
			}
		}
	}
}

class Noo_Tweets extends WP_Widget {

	public function __construct() {
		parent::__construct( 'dh_tweets',  // Base ID
			'Recent Tweets',  // Name
			array( 'classname' => 'tweets-widget', 'description' => __( 'Display recent tweets', 'noo' ) ) );
	}

	public function widget( $args, $instance ) {
		extract( $args );
		if ( ! empty( $instance['title'] ) ) {
			$title = apply_filters( 'widget_title', $instance['title'] );
		}
		echo $before_widget;
		if ( ! empty( $title ) ) {
			echo $before_title . $title . $after_title;
		}

		// check settings and die if not set
		if ( empty( $instance['consumerkey'] ) || empty( $instance['consumersecret'] ) || empty( $instance['accesstoken'] ) || empty( $instance['accesstokensecret'] ) || empty( $instance['cachetime'] ) || empty( $instance['username'] ) ) {
			echo '<strong>' . __( 'Please fill all widget settings!', 'noo' ) . '</strong>' . $after_widget;

			return;
		}

		$noo_widget_recent_tweets_cache_time = get_option( 'noo_widget_recent_tweets_cache_time' );
		$diff                                = time() - $noo_widget_recent_tweets_cache_time;

		$crt = (int) $instance['cachetime'] * 3600;

		if ( $diff >= $crt || empty( $noo_widget_recent_tweets_cache_time ) ) {

			if ( ! require_once( dirname( __FILE__ ) . '/twitteroauth.php' ) ) {
				echo '<strong>' . __( 'Couldn\'t find twitteroauth.php!', 'noo' ) . '</strong>' . $after_widget;

				return;
			}

			function getConnectionWithAccessToken( $cons_key, $cons_secret, $oauth_token, $oauth_token_secret ) {
				$connection = new TwitterOAuth( $cons_key, $cons_secret, $oauth_token, $oauth_token_secret );

				return $connection;
			}

			$connection = getConnectionWithAccessToken( $instance['consumerkey'], $instance['consumersecret'], $instance['accesstoken'], $instance['accesstokensecret'] );
			$tweets     = $connection->get( "https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=" . $instance['username'] . "&count=10&exclude_replies=" . $instance['excludereplies'] );

			if ( ! empty( $tweets->errors ) ) {
				if ( $tweets->errors[0]->message == 'Invalid or expired token' ) {
					echo '<strong>' . $tweets->errors[0]->message . '!</strong><br/>' . sprintf( __( 'You\'ll need to regenerate it <a href="%s" target="_blank">here</a>!', 'noo' ), 'https://dev.twitter.com/apps' ) . $after_widget;
				} else {
					echo '<strong>' . $tweets->errors[0]->message . '</strong>' . $after_widget;
				}

				return;
			}

			if ( ! empty( $tweets->error ) ) {
				if ( is_string( $tweets->error ) ) {
					echo '<strong>' . $tweets->error . '</strong><br/>' . __( 'Please check your configuration.', 'noo' ) . $after_widget;
				}

				return;
			}

			$tweets_array = array();
			for ( $i = 0; $i <= count( $tweets ); $i ++ ) {
				if ( ! empty( $tweets[ $i ] ) ) {
					$tweets_array[ $i ]['created_at']        = $tweets[ $i ]->created_at;
					$tweets_array[ $i ]['name']              = $tweets[ $i ]->user->name;
					$tweets_array[ $i ]['screen_name']       = $tweets[ $i ]->user->screen_name;
					$tweets_array[ $i ]['profile_image_url'] = $tweets[ $i ]->user->profile_image_url;
					// clean tweet text
					$tweets_array[ $i ]['text'] = preg_replace( '/[\x{10000}-\x{10FFFF}]/u', '', $tweets[ $i ]->text );

					if ( ! empty( $tweets[ $i ]->id_str ) ) {
						$tweets_array[ $i ]['status_id'] = $tweets[ $i ]->id_str;
					}
				}
			}
			update_option( 'noo_widget_recent_tweets', serialize( $tweets_array ) );
			update_option( 'noo_widget_recent_tweets_cache_time', time() );
		}

		$noo_widget_recent_tweets = maybe_unserialize( get_option( 'noo_widget_recent_tweets' ) );
		if ( ! empty( $noo_widget_recent_tweets ) ) {
			echo '<div class="recent-tweets"><ul>';
			$i = '1';
			foreach ( $noo_widget_recent_tweets as $tweet ) {

				if ( ! empty( $tweet['text'] ) ) {
					if ( empty( $tweet['status_id'] ) ) {
						$tweet['status_id'] = '';
					}
					if ( empty( $tweet['created_at'] ) ) {
						$tweet['created_at'] = '';
					}

					echo '<li><div class="twitter_user"><a class="twitter_profile" target="_blank" href="http://twitter.com/' . $instance['username'] . '/statuses/' . $tweet['status_id'] . '"><img src="' . $tweet['profile_image_url'] . '">' . $tweet['name'] . '</a><span class="twitter_username">@' . $tweet['screen_name'] . '</span></div><span>' . $this->_convert_links( $tweet['text'] ) . '</span></li>';
					if ( $i == $instance['tweetstoshow'] ) {
						break;
					}
					$i ++;
				}
			}

			echo '</ul></div>';
		}

		echo $after_widget;
	}

	protected function _convert_links( $status, $targetBlank = true, $linkMaxLen = 50 ) {
		// the target
		$target = $targetBlank ? " target=\"_blank\" " : "";

		// convert link to url
		$status = preg_replace( "/((http:\/\/|https:\/\/)[^ )]+)/i", "<a href=\"$1\" title=\"$1\" $target >$1</a>", $status );

		// convert @ to follow
		$status = preg_replace( "/(@([_a-z0-9\-]+))/i", "<a href=\"http://twitter.com/$2\" title=\"Follow $2\" $target >$1</a>", $status );

		// convert # to search
		$status = preg_replace( "/(#([_a-z0-9\-]+))/i", "<a href=\"https://twitter.com/search?q=$2\" title=\"Search $1\" $target >$1</a>", $status );

		// return the status
		return $status;
	}

	protected function _relative_time( $a = '' ) {
		// get current timestampt
		$b = strtotime( "now" );
		// get timestamp when tweet created
		$c = strtotime( $a );
		// get difference
		$d = $b - $c;
		// calculate different time values
		$minute = 60;
		$hour   = $minute * 60;
		$day    = $hour * 24;
		$week   = $day * 7;

		if ( is_numeric( $d ) && $d > 0 ) {
			// if less then 3 seconds
			if ( $d < 3 ) {
				return "right now";
			}
			// if less then minute
			if ( $d < $minute ) {
				return sprintf( __( "%s seconds ago", 'noo' ), floor( $d ) );
			}
			// if less then 2 minutes
			if ( $d < $minute * 2 ) {
				return __( "about 1 minute ago", 'noo' );
			}
			// if less then hour
			if ( $d < $hour ) {
				return sprintf( __( '%s minutes ago', 'noo' ), floor( $d / $minute ) );
			}
			// if less then 2 hours
			if ( $d < $hour * 2 ) {
				return __( "about 1 hour ago", 'noo' );
			}
			// if less then day
			if ( $d < $day ) {
				return sprintf( __( "%s hours ago", 'noo' ), floor( $d / $hour ) );
			}
			// if more then day, but less then 2 days
			if ( $d > $day && $d < $day * 2 ) {
				return __( "yesterday", 'noo' );
			}
			// if less then year
			if ( $d < $day * 365 ) {
				return sprintf( __( '%s days ago', 'noo' ), floor( $d / $day ) );
			}

			// else return more than a year
			return __( "over a year ago", 'noo' );
		}
	}

	public function form( $instance ) {
		$defaults = array(
			'title'             => '',
			'consumerkey'       => '',
			'consumersecret'    => '',
			'accesstoken'       => '',
			'accesstokensecret' => '',
			'cachetime'         => '',
			'username'          => '',
			'tweetstoshow'      => '',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		echo '
        <p>
            <label>' . __( 'Title', 'noo' ) . ':</label>
            <input type="text" name="' . $this->get_field_name( 'title' ) . '" id="' . $this->get_field_id( 'title' ) . '" value="' . esc_attr( $instance['title'] ) . '" class="widefat" />
        </p>
        <p>
            <label>' . __( 'Consumer Key', 'noo' ) . ':</label>
            <input type="text" name="' . $this->get_field_name( 'consumerkey' ) . '" id="' . $this->get_field_id( 'consumerkey' ) . '" value="' . esc_attr( $instance['consumerkey'] ) . '" class="widefat" />
        </p>
        <p>
            <label>' . __( 'Consumer Secret', 'noo' ) . ':</label>
            <input type="text" name="' . $this->get_field_name( 'consumersecret' ) . '" id="' . $this->get_field_id( 'consumersecret' ) . '" value="' . esc_attr( $instance['consumersecret'] ) . '" class="widefat" />
        </p>
        <p>
            <label>' . __( 'Access Token', 'noo' ) . ':</label>
            <input type="text" name="' . $this->get_field_name( 'accesstoken' ) . '" id="' . $this->get_field_id( 'accesstoken' ) . '" value="' . esc_attr( $instance['accesstoken'] ) . '" class="widefat" />
        </p>
        <p>
            <label>' . __( 'Access Token Secret', 'noo' ) . ':</label>
            <input type="text" name="' . $this->get_field_name( 'accesstokensecret' ) . '" id="' . $this->get_field_id( 'accesstokensecret' ) . '" value="' . esc_attr( $instance['accesstokensecret'] ) . '" class="widefat" />
        </p>
        <p>
            <label>' . __( 'Cache Tweets in every', 'noo' ) . ':</label>
            <input type="text" name="' . $this->get_field_name( 'cachetime' ) . '" id="' . $this->get_field_id( 'cachetime' ) . '" value="' . esc_attr( $instance['cachetime'] ) . '" class="small-text" />' . __( 'hours', 'noo' ) . '
        </p>
        <p>
            <label>' . __( 'Twitter Username', 'noo' ) . ':</label>
            <input type="text" name="' . $this->get_field_name( 'username' ) . '" id="' . $this->get_field_id( 'username' ) . '" value="' . esc_attr( $instance['username'] ) . '" class="widefat" />
        </p>
        <p>
            <label>' . __( 'Tweets to display', 'noo' ) . ':</label>
            <select type="text" name="' . $this->get_field_name( 'tweetstoshow' ) . '" id="' . $this->get_field_id( 'tweetstoshow' ) . '">';
		for ( $i = 1; $i <= 10; $i ++ ) {
			echo '<option value="' . $i . '"';
			if ( $instance['tweetstoshow'] == $i ) {
				echo ' selected="selected"';
			}
			echo '>' . $i . '</option>';
		}
		echo '
            </select>
        </p>
        <p>
            <label>' . __( 'Exclude replies', 'noo' ) . ':</label>
            <input type="checkbox" name="' . $this->get_field_name( 'excludereplies' ) . '" id="' . $this->get_field_id( 'excludereplies' ) . '" value="true"';
		if ( ! empty( $instance['excludereplies'] ) && esc_attr( $instance['excludereplies'] ) == 'true' ) {
			echo ' checked="checked"';
		}
		echo '/></p>';
	}

	public function update( $new_instance, $old_instance ) {
		$instance                      = array();
		$instance['title']             = strip_tags( $new_instance['title'] );
		$instance['consumerkey']       = strip_tags( $new_instance['consumerkey'] );
		$instance['consumersecret']    = strip_tags( $new_instance['consumersecret'] );
		$instance['accesstoken']       = strip_tags( $new_instance['accesstoken'] );
		$instance['accesstokensecret'] = strip_tags( $new_instance['accesstokensecret'] );
		$instance['cachetime']         = strip_tags( $new_instance['cachetime'] );
		$instance['username']          = strip_tags( $new_instance['username'] );
		$instance['tweetstoshow']      = strip_tags( $new_instance['tweetstoshow'] );
		$instance['excludereplies']    = strip_tags( $new_instance['excludereplies'] );

		if ( $old_instance['username'] != $new_instance['username'] ) {
			delete_option( 'noo_widget_recent_tweets_cache_time' );
		}

		return $instance;
	}
}

class Noo_Job_Type_Widget extends Noo_Widget {
	public function __construct() {
		$this->widget_cssclass    = 'noo-job-type-widget';
		$this->widget_description = __( "Display Noo Job Type.", 'noo' );
		$this->widget_id          = 'noo_job_type_widget';
		$this->widget_name        = __( 'Noo Job Type', 'noo' );
		$this->cached             = true;

		parent::__construct();
	}

	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$c     = ! empty( $instance['count'] ) ? '1' : '0';
		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		$types = (array) get_terms( 'job_type', array( 'hide_empty' => false ) );
		echo '<ul>';
		foreach ( $types as $type ) {
			$type->color = jm_get_job_type_color( $type->term_id );
			$count       = ( $c ) ? '<span class="job-type-count">(' . $type->count . ')</span>' : '';
			$count       =  apply_filters('noo_job_type_widget_count', $count);
			echo '<li>';
			echo '<a class="job-type-' . esc_attr( $type->slug ) . '" title="' . esc_attr( $type->name ) . '" href="' . get_term_link( $type, 'job_type' ) . '" style="color: ' . $type->color . '">' . esc_html( $type->name ) . $count . '<i style="color: ' . $type->color . '" class="fa fa-bookmark"></i></a>';
			echo '</li>';
		}
		echo '</ul>';
		echo $args['after_widget'];
	}

	public function get_fields() {
		if ( empty( $this->fields ) ) {
			$this->fields = array(
				'title' => array( 'type' => 'text', 'std' => '', 'label' => __( 'Title', 'noo' ) ),
				'count' => array(
					'type'  => 'checkbox',
					'std'   => 0,
					'label' => __( 'Show Job Counts', 'noo' ),
				),
			);
		}

		return $this->fields;
	}
}

class Noo_Job_Category_Widget extends Noo_Widget {
	public function __construct() {
		$this->widget_cssclass    = 'noo-job-category-widget';
		$this->widget_description = __( "Display Noo Job Categories.", 'noo' );
		$this->widget_id          = 'noo_job_category_widget';
		$this->widget_name        = __( 'Noo Job Categories', 'noo' );
		$this->cached             = true;

		parent::__construct();
	}

	public function widget( $args, $instance ) {
		extract( $args );
		$title       = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$c           = ! empty( $instance['count'] ) ? '1' : '0';
		$h           = ! empty( $instance['hierarchical'] ) ? '1' : '0';
		$e           = ! empty( $instance['include_empty'] ) ? '0' : '1';
		$only_parent = empty( $instance['only_parent'] ) ? '0' : '1';

		$cat_args = array(
			'taxonomy'     => 'job_category',
			'orderby'      => 'name',
			'show_count'   => $c,
			'hide_empty'   => $e,
			'hierarchical' => $h,
		);
		if ( $only_parent == 1 ) {
			$cat_args['parent'] = 0;
		}

		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$categories = get_categories( $cat_args );
		echo '<ul>';
		echo walk_category_tree( $categories, 0, array(
			'style'              => 'list',
			'show_count'         => $c,
			'hide_empty'         => $e,
			'hierarchical'       => $h,
			'use_desc_for_title' => 1,
		) );
		echo '</ul>';
		echo $args['after_widget'];
	}

	public function get_fields() {
		if ( empty( $this->fields ) ) {
			$this->fields = array(
				'title'         => array( 'type' => 'text', 'std' => '', 'label' => __( 'Title', 'noo' ) ),
				'count'         => array(
					'type'  => 'checkbox',
					'std'   => 0,
					'label' => __( 'Show Job Counts', 'noo' ),
				),
				'only_parent'   => array(
					'type'  => 'checkbox',
					'std'   => 0,
					'label' => __( 'Only Show First Level', 'noo' ),
				),
				'include_empty' => array(
					'type'  => 'checkbox',
					'std'   => 0,
					'label' => __( 'Include Empty Categories', 'noo' ),
				),
				'hierarchical'  => array(
					'type'  => 'checkbox',
					'std'   => 0,
					'label' => __( 'Show Hierarchy', 'noo' ),
				),
			);
		}

		return $this->fields;
	}
}

class Noo_Job_Location_Widget extends Noo_Widget {
	public function __construct() {
		$this->widget_cssclass    = 'noo-job-location-widget';
		$this->widget_description = __( "Display Noo Job Location.", 'noo' );
		$this->widget_id          = 'noo_job_location_widget';
		$this->widget_name        = __( 'Noo Job Location', 'noo' );
		$this->cached             = true;

		parent::__construct();
	}

	public function widget( $args, $instance ) {
		extract( $args );
		$title    = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$display  = isset( $instance['display'] ) ? absint( $instance['display'] ) : 5;
		$display  = max( $display, 1 );
		$c        = ! empty( $instance['count'] ) ? '1' : '0';
		$h        = ! empty( $instance['hierarchical'] ) ? '1' : '0';
		$e        = ! empty( $instance['include_empty'] ) ? '0' : '1';
		$cat_args = array(
			'taxonomy'     => 'job_location',
			'orderby'      => 'count',
			'order'        => 'DESC',
			'number'       => $display,
			'show_count'   => $c,
			'hide_empty'   => $e,
			'hierarchical' => $h,
		);
		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$categories = get_categories( $cat_args );
		echo '<ul>';
		echo walk_category_tree( $categories, 0, array(
			'style'              => 'list',
			'show_count'         => $c,
			'hide_empty'         => $e,
			'hierarchical'       => $h,
			'use_desc_for_title' => 1,
		) );
		echo '</ul>';

		echo $args['after_widget'];

	}

	public function get_fields() {
		if ( empty( $this->fields ) ) {
			$this->fields = array(
				'title'         => array( 'type' => 'text', 'std' => '', 'label' => __( 'Title', 'noo' ) ),
				'display'       => array( 'type' => 'text', 'std' => 5, 'label' => __( 'Display', 'noo' ) ),
				'count'         => array(
					'type'  => 'checkbox',
					'std'   => 0,
					'label' => __( 'Show jobs counts', 'noo' ),
				),
				'include_empty' => array(
					'type'  => 'checkbox',
					'std'   => 0,
					'label' => __( 'Include Empty Location', 'noo' ),
				),
				'hierarchical'  => array(
					'type'  => 'checkbox',
					'std'   => 0,
					'label' => __( 'Show hierarchy', 'noo' ),
				),
			);
		}

		return $this->fields;
	}
}
class Noo_Blog_Search_Widget extends Noo_Widget {
    public function __construct() {
        $this->widget_cssclass    = 'noo-blog-search-widget';
        $this->widget_description = __( "Keyword search for Blog.", 'noo' );
        $this->widget_id          = 'noo_blog_search_widget';
        $this->widget_name        = __( 'Simple Blog Search', 'noo' );
        $this->cached             = true;
        parent::__construct();
    }

    public function widget( $args, $instance ) {
        extract( $args );
        $title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
        echo $args['before_widget'];
        if ( $title ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        ob_start();
        ?>
        <form method="get" class="form-horizontal noo-blog-search"
              action="<?php echo esc_url( home_url( '/' ) ); ?>">
            <label class="sr-only" for="s"><?php _e( 'Search for:', 'noo' ); ?></label>
            <input type="search" id="s" class="form-control"
                   placeholder="<?php echo esc_attr__( 'Search Post&hellip;', 'noo' ); ?>"
                   value="<?php echo get_search_query(); ?>" name="s"
                   title="<?php echo esc_attr__( 'Search for:', 'noo' ); ?>"/>
            <input type="submit" id="searchsubmit" class="hidden" value="">
            <input type="hidden" name="post_type" value="post"/>
        </form>
        <?php
        $form = ob_get_clean();
        echo $form;
        echo $args['after_widget'];
    }

    public function get_fields() {
        if ( empty( $this->fields ) ) {
            $this->fields = array(
                'title' => array( 'type' => 'text', 'std' => '', 'label' => __( 'Title', 'noo' ) ),
            );
        }

        return $this->fields;
    }
}
class Noo_Job_Search_Widget extends Noo_Widget {
	public function __construct() {
		$this->widget_cssclass    = 'noo-job-search-widget';
		$this->widget_description = __( "Simple keyword search for Jobs.", 'noo' );
		$this->widget_id          = 'noo_job_search_widget';
		$this->widget_name        = __( 'Simple Job Search', 'noo' );
		$this->cached             = true;
		parent::__construct();
	}

	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		ob_start();
		do_action( 'pre_get_job_search_form' );
		?>
        <form method="get" class="form-horizontal noo-job-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
            <label class="sr-only" for="s"><?php _e( 'Search for:', 'noo' ); ?></label>
            <input type="search" id="s" class="form-control"
                   placeholder="<?php echo esc_attr__( 'Search Job&hellip;', 'noo' ); ?>"
                   value="<?php echo get_search_query(); ?>" name="s"
                   title="<?php echo esc_attr__( 'Search for:', 'noo' ); ?>"/>
            <input type="submit" id="searchsubmit" class="hidden" value="">
            <input type="hidden" name="post_type" value="noo_job"/>
        </form>
		<?php
		$form = apply_filters( 'get_job_search_form', ob_get_clean() );
		echo $form;
		echo $args['after_widget'];
	}

	public function get_fields() {
		if ( empty( $this->fields ) ) {
			$this->fields = array(
				'title' => array( 'type' => 'text', 'std' => '', 'label' => __( 'Title', 'noo' ) ),
			);
		}

		return $this->fields;
	}
}

class Noo_Company_Search_Widget extends Noo_Widget {
	public function __construct() {
		$this->widget_cssclass    = 'noo-company-search-widget';
		$this->widget_description = __( "Keyword search for Companies.", 'noo' );
		$this->widget_id          = 'noo_company_search_widget';
		$this->widget_name        = __( 'Simple Company Search', 'noo' );
		$this->cached             = true;
		parent::__construct();
	}

	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		ob_start();
		?>
        <form method="get" class="form-horizontal noo-company-search"
              action="<?php echo esc_url( home_url( '/' ) ); ?>">
            <label class="sr-only" for="s"><?php _e( 'Search for:', 'noo' ); ?></label>
            <input type="search" id="s" class="form-control"
                   placeholder="<?php echo esc_attr__( 'Search Company&hellip;', 'noo' ); ?>"
                   value="<?php echo get_search_query(); ?>" name="s"
                   title="<?php echo esc_attr__( 'Search for:', 'noo' ); ?>"/>
            <input type="submit" id="searchsubmit" class="hidden" value="">
            <input type="hidden" name="post_type" value="noo_company"/>
        </form>
		<?php
		$form = ob_get_clean();
		echo $form;
		echo $args['after_widget'];
	}

	public function get_fields() {
		if ( empty( $this->fields ) ) {
			$this->fields = array(
				'title' => array( 'type' => 'text', 'std' => '', 'label' => __( 'Title', 'noo' ) ),
			);
		}

		return $this->fields;
	}
}

class Noo_Job_Count_Widget extends Noo_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'noo-job-count-widget';
		$this->widget_description = __( "Display the number of available jobs.", 'noo' );
		$this->widget_id          = 'noo_job_count_widget';
		$this->widget_name        = __( 'Noo Job Count', 'noo' );
		$this->cached             = true;
		parent::__construct();
	}

	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		$company_count = wp_count_posts( 'noo_company' );
		$job_count     = wp_count_posts( 'noo_job' );
		echo '<ul>';
		echo '<li><a href="' . get_post_type_archive_link( 'noo_company' ) . '" >' . __( 'Companies', 'noo' ) . '</a>';
		echo '<p class="jobs-count">' . $company_count->publish . '</p>';
		echo '</li>';
		echo '<li><a href="' . get_post_type_archive_link( 'noo_job' ) . '" >' . __( 'Available Jobs', 'noo' ) . '</a>';
		echo '<p class="jobs-count">' . $job_count->publish . '</p>';
		echo '</li>';
		echo '</ul>';
		echo $args['after_widget'];
	}

	public function get_fields() {
		if ( empty( $this->fields ) ) {
			$this->fields = array(
				'title' => array( 'type' => 'text', 'std' => '', 'label' => __( 'Title', 'noo' ) ),
			);
		}

		return $this->fields;
	}
}

class Noo_Resume_Categories_Widget extends Noo_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'noo-resume-category-widget';
		$this->widget_description = __( "Display the Categories for resume.", 'noo' );
		$this->widget_id          = 'noo_resume_categories_widget';
		$this->widget_name        = __( 'Resume Categories', 'noo' );
		$this->cached             = true;

		parent::__construct();
	}

	public function widget( $args, $instance ) {
		extract( $args );
		$title       = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$show_count  = isset( $instance['count'] ) && ! empty( $instance['count'] ) ? true : false;
		$h           = ! empty( $instance['hierarchical'] ) ? '1' : '0';
		$e           = ! empty( $instance['include_empty'] ) ? '0' : '1';
		$only_parent = empty( $instance['only_parent'] ) ? '0' : '1';

		$cat_args = array(
			'taxonomy'     => 'job_category',
			'orderby'      => 'name',
			'show_count'   => $show_count,
			'hide_empty'   => 0,
			'hierarchical' => $h,
		);
		if ( $only_parent == 1 ) {
			$cat_args['parent'] = 0;
		}

		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$categoriess = get_categories( $cat_args );

		echo '<ul>';
		$archive_link = get_post_type_archive_link( 'noo_resume' );
		global $wpdb;
		foreach ( $categoriess as $category ) {

			$category_link = esc_url( add_query_arg( array( 'resume_category' => $category->term_id ), $archive_link ) );

            $count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} AS p JOIN {$wpdb->postmeta} AS m ON p.ID = m.post_id JOIN {$wpdb->postmeta} AS m2 ON p.ID = m2.post_id
                WHERE p.post_type = 'noo_resume' AND p.post_status = 'publish' AND m.meta_key = '_job_category' AND m.meta_value LIKE '%{$category->term_id}%' AND m2.meta_key = '_viewable' AND m2.meta_value = 'yes'" );
			if($e && $count == 0){
			    continue;
            }
			if (!$show_count ) {
                $count         = '';
			}
			echo '<li>';
			echo '<a class="resume-category-' . esc_attr( $category->slug ) . '" title="' . esc_attr( $category->name ) . '" href="' . $category_link . '" >' . esc_html( $category->name ) . '</a>';
			echo $show_count ? "( {$count} )" : ''; // output count value, not good for performance.

			// sub level

			if ( $only_parent != 1 && $h ) :

				$parent_id = $category->term_id;

				$cat_args = array(
					'taxonomy'   => 'job_category',
					'orderby'    => 'name',
					'show_count' => $show_count,
					'hide_empty' => 0,
					'parent'     => $parent_id,
				);

				$subs = get_categories( $cat_args );

				if ( ! empty( $subs ) ):
					?>
                    <ul class="children">
						<?php
						foreach ( $subs as $category ) {

							$category_link = esc_url( add_query_arg( array( 'resume_category' => $category->term_id ), $archive_link ) );
                            $count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} AS p JOIN {$wpdb->postmeta} AS m ON p.ID = m.post_id JOIN {$wpdb->postmeta} AS m2 ON p.ID = m2.post_id
                WHERE p.post_type = 'noo_resume' AND p.post_status = 'publish' AND m.meta_key = '_job_category' AND m.meta_value LIKE '%{$category->term_id}%' AND m2.meta_key = '_viewable' AND m2.meta_value = 'yes'" );

                            if($e && $count == 0){
                                continue;
                            }

							if ( !$show_count ) {
                                $count = '';
							}

							echo '<li>';
							echo '<a class="resume-category-' . esc_attr( $category->slug ) . '" title="' . esc_attr( $category->name ) . '" href="' . $category_link . '" >' . esc_html( $category->name ) . '</a>';
							echo $show_count ? "( {$count} )" : ''; // output count value, not good for performance.
							echo '</li>';
						}
						?>
                    </ul>
				<?php endif; ?>
			<?php endif; ?>

			<?php
			echo '</li>';
		}
		echo '</ul>';
		echo $args['after_widget'];
	}

	public function get_fields() {
		if ( empty( $this->fields ) ) {
			$this->fields = array(
				'title'         => array( 'type' => 'text', 'std' => '', 'label' => __( 'Title', 'noo' ) ),
				'count'         => array(
					'type'  => 'checkbox',
					'std'   => 0,
					'label' => __( 'Show Resume Counts', 'noo' ),
				),
				'only_parent'   => array(
					'type'  => 'checkbox',
					'std'   => 0,
					'label' => __( 'Only Show First Level', 'noo' ),
				),
				'include_empty' => array(
					'type'  => 'checkbox',
					'std'   => 0,
					'label' => __( 'Include Empty Categories', 'noo' ),
				),
				'hierarchical'  => array(
					'type'  => 'checkbox',
					'std'   => 0,
					'label' => __( 'Show Hierarchy', 'noo' ),
				),
			);
		}

		return $this->fields;
	}
}

class Noo_Resume_Search_Widget extends Noo_Widget {
	public function __construct() {
		if ( ! class_exists( 'Noo_Resume' ) ) {
			return;
		}

		$this->widget_cssclass    = 'noo-resume-search-widget';
		$this->widget_description = __( "Simple keyword search for Resumes.", 'noo' );
		$this->widget_id          = 'noo_resume_search_widget';
		$this->widget_name        = __( 'Simple Resume Search', 'noo' );
		$this->cached             = true;

		parent::__construct();
	}

	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		$candidate_name = isset( $instance['candidate_name'] ) ? $instance['candidate_name'] : '';
		$search_content = isset( $instance['search_content'] ) ? $instance['search_content'] : '';
		$education      = isset( $instance['education'] ) ? $instance['education'] : '';
		$experience     = isset( $instance['experience'] ) ? $instance['experience'] : '';
		$skill          = isset( $instance['skill'] ) ? $instance['skill'] : '';

		ob_start();
		do_action( 'pre_get_resume_search_form' );
		?>
        <form method="get" class="form-horizontal noo-resume-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
            <label class="sr-only" for="s"><?php _e( 'Search for:', 'noo' ); ?></label>
            <input type="search" id="s" class="form-control"
                   placeholder="<?php echo esc_attr__( 'Search Resume&hellip;', 'noo' ); ?>"
                   value="<?php echo get_search_query(); ?>" name="s"
                   title="<?php echo esc_attr__( 'Search for:', 'noo' ); ?>"/>
            <input type="submit" id="searchsubmit" class="hidden" name="submit" value="Search">
            <input type="hidden" name="post_type" value="noo_resume"/>
			<?php if ( empty( $search_content ) ) : ?>
                <input type="hidden" name="no_content" value="1"/>
			<?php endif; ?>
			<?php if ( ! empty( $candidate_name ) ) : ?>
                <input type="hidden" name="candidate_name" value="1"/>
			<?php endif; ?>
			<?php if ( ! empty( $education ) ) : ?>
                <input type="hidden" name="education" value="1"/>
			<?php endif; ?>
			<?php if ( ! empty( $experience ) ) : ?>
                <input type="hidden" name="experience" value="1"/>
			<?php endif; ?>
			<?php if ( ! empty( $skill ) ) : ?>
                <input type="hidden" name="skill" value="1"/>
			<?php endif; ?>
        </form>
		<?php
		$form = apply_filters( 'get_resume_search_form', ob_get_clean() );
		echo $form;
		echo $args['after_widget'];
	}

	public function get_fields() {
		if ( empty( $this->fields ) ) {
			$this->fields = array(
				'title'          => array( 'type' => 'text', 'std' => '', 'label' => __( 'Title', 'noo' ) ),
				'candidate_name' => array(
					'type'  => 'checkbox',
					'std'   => '1',
					'label' => __( 'Search by Candidate Name', 'noo' ),
				),
				'search_content' => array(
					'type'  => 'checkbox',
					'std'   => '',
					'label' => __( 'Search by Resume Title &amp; Content', 'noo' ),
				),
			);

			$education  = jm_get_resume_setting( 'enable_education', '1' );
			$experience = jm_get_resume_setting( 'enable_experience', '1' );
			$skill      = jm_get_resume_setting( 'enable_skill', '1' );

			if ( $education ) {
				$this->fields['education'] = array(
					'type'  => 'checkbox',
					'std'   => '',
					'label' => __( 'Search by Education', 'noo' ),
				);
			}
			if ( $experience ) {
				$this->fields['experience'] = array(
					'type'  => 'checkbox',
					'std'   => '',
					'label' => __( 'Search by Experience', 'noo' ),
				);
			}
			if ( $skill ) {
				$this->fields['skill'] = array(
					'type'  => 'checkbox',
					'std'   => '',
					'label' => __( 'Search by Skill', 'noo' ),
				);
			}
		}

		return $this->fields;
	}
}

class Noo_Resume_Count_Widget extends Noo_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'noo-resume-count-widget';
		$this->widget_description = __( "Display the total number of available resumes.", 'noo' );
		$this->widget_id          = 'noo_resume_count_widget';
		$this->widget_name        = __( 'Resumes Count', 'noo' );
		$this->cached             = true;

		parent::__construct();
	}

	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		$resume_count = Noo_Resume::count_viewable_resumes( '', true );
		echo '<ul><li><a href="' . get_post_type_archive_link( 'noo_resume' ) . '" >' . __( 'Resumes', 'noo' ) . '</a>';
		echo '<p class="jobs-count">' . $resume_count . '</p>';
		echo '</li></ul>';
		echo $args['after_widget'];
	}

	public function get_fields() {
		if ( empty( $this->fields ) ) {
			$this->fields = array(
				'title' => array( 'type' => 'text', 'std' => '', 'label' => __( 'Title', 'noo' ) ),
			);
		}

		return $this->fields;
	}
}

class Noo_Jobs_Widget extends Noo_Widget {
	public $setting_with_values;

	public function __construct() {
		$this->widget_cssclass    = 'noo-jobs-widget';
		$this->widget_description = __( "Display style (jobs slider, jobs list) .", 'noo' );
		$this->widget_id          = 'noo_jobs_widget';
		$this->widget_name        = __( 'Noo Jobs', 'noo' );
		$this->cached             = true;

		parent::__construct();
	}

	public function widget( $args, $instance ) {
		extract( $args );
		// $title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base );
		echo $args['before_widget'];

		$paged = 1;
		//  -- Array

		$job_args = array(
			'post_type'           => 'noo_job',
			'post_status'         => 'publish',
			'paged'               => $paged,
			'posts_per_page'      => $instance['posts_per_page'] ? $instance['posts_per_page'] : 1,
			'ignore_sticky_posts' => true,
		);

		//  -- tax_query

		$job_args['tax_query'] = array( 'relation' => 'AND' );
		if ( $instance['job_category'] != 'all' ) {
			$job_args['tax_query'][] = array(
				'taxonomy' => 'job_category',
				'field'    => 'slug',
				'terms'    => $instance['job_category'],
			);
		}

		if ( $instance['job_type'] != 'all' ) {
			$job_args['tax_query'][] = array(
				'taxonomy' => 'job_type',
				'field'    => 'slug',
				'terms'    => $instance['job_type'],
			);
		}

		if ( $instance['job_location'] != 'all' ) {
			$job_args['tax_query'][] = array(
				'taxonomy' => 'job_location',
				'field'    => 'slug',
				'terms'    => $instance['job_location'],
			);
		}

		//  -- Check orderby

		if ( $instance['orderby'] == 'view' ) {
			$job_args['orderby']  = 'meta_value_num';
			$job_args['meta_key'] = '_noo_views_count';
		} else {
			$job_args['orderby'] = 'date';
		}
		
		//  -- Check sort by

		if ( $instance['order'] == 'asc' ) {
			$job_args['order'] = 'ASC';
		} else {
			$job_args['order'] = 'DESC';
		}
	
		if ( $instance['show'] == 'featured' ) {
			$job_args['meta_query'][] = array(
				'key'   => '_featured',
				'value' => 'yes',
			);
		}

		//  -- create new query

		$jobs_new_query = new WP_Query( $job_args );

		if ( $jobs_new_query->have_posts() ) {
			$loop_args = array(
				'title'         => $instance['title'],
				'query'         => $jobs_new_query,
				'display_style' => $instance['show_type'],
				'show_autoplay' => 'on',
				'slider_time'   => '3000',
				'slider_speed'  => '600',
				'is_sidebar'    => true,
				'is_widget'    => true,
			);

			jm_job_loop( $loop_args );
		}

		echo $args['after_widget'];
	}

	/**
	 * form function.
	 *
	 * @see WP_Widget->form
	 *
	 * @param array $instance
	 */
	public function form( $instance ) {
		$fields = $this->get_fields_with_value();
		if ( ! $fields ) {
			return;
		}

		foreach ( $fields as $key => $setting ) {
			$setting['key']   = $key;
			$setting['value'] = isset( $instance[ $key ] ) ? $instance[ $key ] : '';
			$this->_render_field( $setting );
		}
	}

	public function get_fields_with_value() {
		if ( empty( $this->setting_with_values ) ) {
			// --- Job Category

			$job_category      = array( 'all' => __( 'All Categories', 'noo' ) );
			$job_category_list = get_terms( 'job_category', array( 'hide_empty' => false ) );

			if ( is_array( $job_category_list ) && ! empty( $job_category_list ) ) {
				foreach ( $job_category_list as $category_details ) {
					$job_category[ $category_details->slug ] = $category_details->name;
				}
			}

			// --- Job Type

			$job_type      = array( 'all' => __( 'All types', 'noo' ) );
			$job_type_list = get_terms( 'job_type', array( 'hide_empty' => false ) );

			if ( is_array( $job_type_list ) && ! empty( $job_type_list ) ) {
				foreach ( $job_type_list as $type_details ) {
					$job_type[ $type_details->slug ] = $type_details->name;
				}
			}

			// --- Job Location

			$job_location      = array( 'all' => __( 'All locations', 'noo' ) );
			$job_location_list = get_terms( 'job_location', array( 'hide_empty' => false ) );

			if ( is_array( $job_location_list ) && ! empty( $job_location_list ) ) {
				foreach ( $job_location_list as $location_details ) {
					$job_location[ $location_details->slug ] = $location_details->name;
				}
			}

			$this->setting_with_values = array(
				'title'          => array(
					'type'  => 'text',
					'std'   => '',
					'label' => __( 'Title', 'noo' ),
				),
				'show'           => array(
					'type'    => 'select',
					'std'     => '',
					'label'   => __( 'Show', 'noo' ),
					'options' => array(
						'featured' => 'Featured',
						'recent'   => 'Recent',
					),
				),
				'show_type'      => array(
					'type'    => 'select',
					'label'   => __( 'Show Type','noo' ),
					'options' => array(
						'slider' => __( 'Slider', 'noo' ),
						'list2'  => __( 'List', 'noo' ),
						'grid'   => __( 'Grid', 'noo' ),
					)
				),
				'job_category'   => array(
					'type'    => 'select',
					'std'     => '',
					'label'   => __( 'Job Categories', 'noo' ),
					'options' => $job_category,
				),
				'job_type'       => array(
					'type'    => 'select',
					'std'     => '',
					'label'   => __( 'Job Type', 'noo' ),
					'options' => $job_type,
				),
				'job_location'   => array(
					'type'    => 'select',
					'std'     => '',
					'label'   => __( 'Job Location', 'noo' ),
					'options' => $job_location,
				),
				'posts_per_page' => array(
					'type'  => 'text',
					'std'   => '1',
					'label' => __( 'Posts per page', 'noo' ),
				),
				'orderby'        => array(
					'type'    => 'select',
					'std'     => '',
					'label'   => __( 'Order by', 'noo' ),
					'options' => array(
						'featured' => 'Date',
						'view'     => 'Popular',
					),
				),
				'order'          => array(
					'type'    => 'select',
					'std'     => '',
					'label'   => __( 'Sort by', 'noo' ),
					'options' => array(
						'desc' => 'Recent',
						'asc'  => 'Older',
					),
				),
			);
		}

		return $this->setting_with_values;
	}

	public function get_fields() {
		if ( empty( $this->fields ) ) {
			$this->fields = array(
				'title'          => array(
					'type'  => 'text',
					'std'   => '',
					'label' => __( 'Title', 'noo' ),
				),
				'show'           => array(
					'type'    => 'select',
					'std'     => '',
					'label'   => __( 'Show', 'noo' ),
					'options' => array(
						'featured' => 'Featured',
						'recent'   => 'Recent',
					),
				),
				'show_type'      => array(
					'type'    => 'select',
					'label'   => __( 'Show Type','noo' ),
					'options' => array(
						'slider' => __( 'Slider', 'noo' ),
						'list2'  => __( 'List', 'noo' ),
						'grid'   => __( 'Grid', 'noo' )
					)
				),
				'job_category'   => array(
					'type'  => 'select',
					'std'   => '',
					'label' => __( 'Job Categorie', 'noo' ),
				),
				'job_type'       => array(
					'type'  => 'select',
					'std'   => '',
					'label' => __( 'Job Type', 'noo' ),
				),
				'job_location'   => array(
					'type'  => 'select',
					'std'   => '',
					'label' => __( 'Job Location', 'noo' ),
				),
				'posts_per_page' => array(
					'type'  => 'text',
					'std'   => '1',
					'label' => __( 'Posts per page', 'noo' ),
				),
				'orderby'        => array(
					'type'    => 'select',
					'std'     => '',
					'label'   => __( 'Order by', 'noo' ),
					'options' => array(
						'featured' => 'Date',
						'view'     => 'Popular',
					),
				),
				'order'          => array(
					'type'    => 'select',
					'std'     => '',
					'label'   => __( 'Sort by', 'noo' ),
					'options' => array(
						'featured' => 'Recent',
						'popular'  => 'Older',
					),
				),
			);
		}

		return $this->fields;
	}
}

class Noo_Advanced_Job_Search_Widget extends WP_Widget {

	public function __construct() {

		parent::__construct( false, __( 'Job Advanced Search', 'noo' ) );
	}

	public function form( $instance ) {

		$default                 = array(
			'title'                   => '',
			'show_keyword'            => 'yes',
			'r_pos1'                  => '',
			'r_pos2'                  => '',
			'r_pos3'                  => '',
			'r_pos4'                  => '',
			'r_pos5'                  => '',
			'r_pos6'                  => '',
			'r_pos7'                  => '',
			'r_pos8'                  => '',
			'disable_live_search'     => false,
			'reset_button'            => false,
			'hide_search_button'      => false,
			// 'disable_multiple_select' => false,
		);
		$instance                = wp_parse_args( (array) $instance, $default );
		$title                   = esc_attr( $instance['title'] );
		$disable_live_search     = (bool) $instance['disable_live_search'];
		$reset_button            = (bool) $instance['reset_button'];
		$hide_search_button      = (bool) $instance['hide_search_button'];
		// $disable_multiple_select = (bool) $instance['disable_multiple_select'];
		$show_keyword            = esc_attr( $instance['show_keyword'] );

		$custom_fields = jm_get_job_search_custom_fields();
		$search_fields = array(
			'no' => __( 'None', 'noo' ),
		);
		foreach ( $custom_fields as $k => $field ) {
			if ( isset( $field['is_default'] ) ) {
				$label                = isset( $field['label'] ) ? $field['label'] : $k;
				$id                   = $field['name'];
				$search_fields[ $id ] = $label;
			} else {
				$label                = __( 'Custom Field: ', 'noo' ) . ( isset( $field['label_translated'] ) ? $field['label_translated'] : ( isset( $field['label'] ) ? $field['label'] : $k ) );
				$id                   = jm_job_custom_fields_name( $field['name'], $field );
				$search_fields[ $id ] = $label;
			}
		}
		?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>">
				<?php _e( 'Title', 'noo' ); ?>
            </label>
            <input class="widefat"
                   id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                   name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
                   value="<?php echo $title; ?>"/>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'show_keyword' ); ?>">
				<?php _e( 'Enable Keyword Search', 'noo' ); ?>
            </label>
            <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'show_keyword' ) ); ?>" name="<?php echo $this->get_field_name( 'show_keyword' ); ?>">
                <option value="yes"<?php echo $show_keyword == 'yes' ? ' selected' : ''; ?>><?php _e( 'Yes', 'noo' ); ?></option>
                <option value="no"<?php echo $show_keyword == 'no' ? ' selected' : ''; ?>><?php _e( 'No', 'noo' ); ?></option>
            </select>
        </p>

		<?php for ( $po = 1; $po <= 8; $po ++ ) : ?>
			<?php $r_pos = esc_attr( $instance["r_pos{$po}"] ); ?>
            <!-- Search Position #<?php echo $po; ?> -->

            <p>
                <label for="<?php echo $this->get_field_id( "r_pos{$po}" ); ?>">
					<?php _e( 'Search Position #' . $po, 'noo' ); ?>
                </label>
                <select class="widefat search-position"
                        id="<?php echo esc_attr( $this->get_field_id( "r_pos{$po}" ) ); ?>"
                        name="<?php echo $this->get_field_name( "r_pos{$po}" ); ?>">
					<?php foreach ( $search_fields as $key => $value ) {
						$selected = ( $r_pos == $key ) || strpos( $r_pos, $key . '|' ) !== false;
						echo "<option value='{$key}'" . ( $selected ? ' selected' : '' ) . ">{$value}</option>";
					} ?>
                </select>
            </p>

            <!-- /Search Position #<?php echo $po; ?> -->

		<?php endfor; ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'disable_live_search' ); ?>">
				<?php _e( 'Disable Live Search', 'noo' ); ?>
            </label>
            <input class="widefat" type="checkbox"
                   id="<?php echo esc_attr( $this->get_field_id( 'disable_live_search' ) ); ?>"
                   name="<?php echo $this->get_field_name( 'disable_live_search' ); ?>" type="text"
                   value="1" <?php checked( true, $disable_live_search ); ?> />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'reset_button' ); ?>">
				<?php _e( 'Enable Reset Button', 'noo' ); ?>
            </label>
            <input class="widefat" type="checkbox"
                   id="<?php echo esc_attr( $this->get_field_id( 'reset_button' ) ); ?>"
                   name="<?php echo $this->get_field_name( 'reset_button' ); ?>" type="text"
                   value="1" <?php checked( true, $reset_button ); ?> />
        <p>
            <small><?php _e( 'Show the button that will reset all criteria.', 'noo' ); ?></small>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'hide_search_button' ); ?>">
				<?php _e( 'Hide Search Button', 'noo' ); ?>
            </label>
            <input class="widefat" type="checkbox"
                   id="<?php echo esc_attr( $this->get_field_id( 'hide_search_button' ) ); ?>"
                   name="<?php echo $this->get_field_name( 'hide_search_button' ); ?>" type="text"
                   value="1" <?php checked( true, $hide_search_button ); ?> />
        <p>
            <small><?php _e( 'The search form is live so you may not need the search button.', 'noo' ); ?></small>
        </p>
       <!--  <p>
            <label for="<?php //echo $this->get_field_id( 'disable_multiple_select' ); ?>">
				<?php //_e( 'Disable Multiple Select', 'noo' ); ?>
            </label>
            <input class="widefat" type="checkbox"
                   id="<?php //echo esc_attr( $this->get_field_id( 'disable_multiple_select' ) ); ?>"
                   name="<?php //echo $this->get_field_name( 'disable_multiple_select' ); ?>" type="text"
                   value="1" <?php //checked( true, $disable_multiple_select ); ?> />
        </p> -->
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance                            = $old_instance;
		$instance['title']                   = strip_tags( $new_instance['title'] );
		$instance['disable_live_search']     = isset( $new_instance['disable_live_search'] ) ? strip_tags( $new_instance['disable_live_search'] ) : '';
		// $instance['disable_multiple_select'] = isset( $new_instance['disable_multiple_select'] ) ? strip_tags( $new_instance['disable_multiple_select'] ) : '';
		$instance['reset_button']            = isset( $new_instance['reset_button'] ) ? strip_tags( $new_instance['reset_button'] ) : '';
		$instance['hide_search_button']      = isset( $new_instance['hide_search_button'] ) ? strip_tags( $new_instance['hide_search_button'] ) : '';
		$instance['show_keyword']            = strip_tags( $new_instance['show_keyword'] );
		for ( $po = 1; $po <= 8; $po ++ ) {
			$instance["r_pos{$po}"] = strip_tags( $new_instance["r_pos{$po}"] );
		}

		return $instance;
	}

	public function widget( $args, $instance ) {
		extract( $args );
		$title                   = apply_filters( 'widget_title', $instance['title'] );
		$show_keyword            = $instance['show_keyword'];
		$disable_live_search     = isset( $instance['disable_live_search'] ) && ! empty( $instance['disable_live_search'] );
		$reset_button            = isset( $instance['reset_button'] ) && ! empty( $instance['reset_button'] );
		$hide_search_button      = isset( $instance['hide_search_button'] ) && ! empty( $instance['hide_search_button'] );
		$disable_multiple_select = noo_get_option( 'noo_job_search_field_type',0);
		$allow_multiple_select   = ! $disable_multiple_select;


		$prefix = uniqid();

		echo $before_widget;
		echo $before_title . $title . $after_title;
		?>
        <form id="<?php echo $prefix . '_form'; ?>" method="get" class="widget-advanced-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
            <style type="text/css">
                .widget-advanced-search ul li:first-child, .widget-advanced-search ol li:first-child {
                    padding-top: 6px;
                }
            </style>
			<?php
			if ( $show_keyword == 'yes' ) :
				?>
                <div class="form-group">
                    <label class="sr-only" for="<?php echo $prefix . '_search-keyword'; ?>"><?php _e( 'Keyword', 'noo' ); ?></label>
                    <input type="text" class="form-control" id="<?php echo $prefix . '_search-keyword'; ?>" name="s" placeholder="<?php _e( 'Keyword', 'noo' ); ?>" value="<?php echo get_search_query(); ?>"/>
                </div>
			<?php
			else :
				?>
                <input type="hidden" name="s" value=""/>
			<?php
			endif;
			for ( $po = 1; $po <= 8; $po ++ ) {
				$field = $instance["r_pos{$po}"];
				jm_job_advanced_search_field( $field, $disable_multiple_select );
			}
			?>
			
			<?php do_action('noo_job_advanced_search_form_widget'); ?>
			
            <input type="hidden" class="form-control" name="post_type" value="noo_job" />
            
			<?php if ( ! $disable_live_search ) : ?>
                <input type="hidden" name="action" value="live_search"/>
				<?php wp_nonce_field( 'noo-advanced-live-search', 'live-search-nonce' ); ?>
			<?php endif; ?>
			<?php if ( ! $hide_search_button ) : ?>
                <button type="submit" class="btn btn-primary btn-search-submit"><?php _e( 'Search', 'noo' ); ?></button>
			<?php endif; ?>
			<?php if ( $reset_button ) :?>
                <a href="<?php echo $url = is_tax() ? get_post_type_archive_link('noo_job') : '#'?>" class="btn btn-default reset-search"><i class="fas fa-redo-alt"></i> <?php _e( 'Reset', 'noo' ); ?></a>
			<?php endif; ?>
        </form>
        <?php if(!is_tax()):?>
	        <script type="text/javascript">
	            jQuery(document).ready(function ($) {
	                $("#<?php echo $prefix . '_form'; ?> .reset-search").click(function (e) {
	                    e.preventDefault();

	                    var $form = $("#<?php echo $prefix . '_form'; ?>");

	                    $form.get(0).reset();
	                    $form.find('option:selected').removeAttr('selected');

	                    // $form.find(".noo-select.form-control-chosen").val('').trigger("chosen:updated");

	                    $form.find('.form-group').first().find(':input').not(':button, :submit, :reset, :hidden').change();

	                    // $form.find(":input").not(':button, :submit, :reset, :hidden').change();

	                    $('select.form-control',$form).multiselect('refresh');
	                    
	                    return false;
	                });
	            });
	        </script>
	    <?php endif;?>
		<?php if ( ! $disable_live_search ) : ?>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    var container = $(".noo-main > .jobs");
                    if (!$('.map-info').length && container.length) {
                        $("#<?php echo $prefix . '_form'; ?>").on('change','select, input:not([type="checkbox"]):not([type="radio"]):not([type="hidden"])', function (event) {
                            event.preventDefault();
                            // Filter button when run on Mobile
                    		$(this).closest('.widget_noo_advanced_job_search_widget').removeClass('on-filter');
                            $('.noo-main').addClass('noo-loading').append('<div class="noo-loader loadmore-loading"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>');
                            $('html,body').animate({scrollTop: $('.noo-main').offset().top - 200}, 500);
                            var $form = $("#<?php echo $prefix . '_form'; ?> .form-control");
                            var data = $(this).parents('form').serialize();
                            history.pushState(null, null, "?" + $form.serialize());
                            $.ajax({
                                url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                                data: data
                            })
                                .done(function (data) {
                                    if (data !== "-1") {
                                        $(".noo-main").html(data);
                                        $('.noo-main').removeClass('noo-loading');
                                        if ($('[data-paginate="loadmore"]').find(".loadmore-action").length) {
                                            $('[data-paginate="loadmore"]').each(function () {
                                                var $this = $(this);
                                                $this.nooLoadmore({
                                                    navSelector: $this.find("div.pagination"),
                                                    nextSelector: $this.find("div.pagination a.next"),
                                                    itemSelector: "article.loadmore-item",
                                                    finishedMsg: "<?php echo __( 'All jobs displayed', 'noo' ); ?>"
                                                });
                                            });
                                        }
                                    } else {
                                        location.reload();
                                    }
                                })
                                .fail(function () {

                                })
                        });
                    }
                    $("#<?php echo $prefix . '_form'; ?>").submit(function () {
                        $(this).find("input[name='action']").remove();
                        $(this).find("input[name='_wp_http_referer']").remove();
                        $(this).find("input[name='live-search-nonce']").remove();

                        return true;
                    });
                });
            </script>
		<?php
		endif;
		echo $after_widget;
	}
}

class Noo_Advanced_Job_Filter_Widget extends WP_Widget {

    public function __construct() {

        parent::__construct( false, esc_html__( 'Job Filter Widget', 'noo' ) );
    }

    public function form( $instance ) {

        $default                 = array(
            'title'                   => '',
            'show_keyword'            => 'yes',
            'r_pos1'                  => '',
            'r_pos2'                  => '',
            'r_pos3'                  => '',
            'r_pos4'                  => '',
            'r_pos5'                  => '',
            'r_pos6'                  => '',
            'r_pos7'                  => '',
            'r_pos8'                  => '',
            'r_pos9'                  => '',
            'r_pos10'                  => '',
            'r_pos11'                  => '',
            'r_pos12'                  => '',
        );
        $instance                = wp_parse_args( (array) $instance, $default );
        $title                   = esc_attr( $instance['title'] );
        $show_keyword            = esc_attr( $instance['show_keyword'] );

        $custom_fields = jm_get_job_filter_custom_fields();
        $search_fields = array(
            'no' => esc_html__( 'None', 'noo' ),
        );
        foreach ( $custom_fields as $k => $field ) {
            if ( isset( $field['is_default'] ) ) {
                $label                = isset( $field['label'] ) ? $field['label'] : $k;
                $id                   = $field['name'];
                $search_fields[ $id ] = $label;
            } else {
                $label                = esc_html__( 'Custom Field: ', 'noo' ) . ( isset( $field['label_translated'] ) ? $field['label_translated'] : ( isset( $field['label'] ) ? $field['label'] : $k ) );
                $id                   = jm_job_custom_fields_name( $field['name'], $field );
                $search_fields[ $id ] = $label;
            }
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>">
                <?php esc_html_e( 'Title', 'noo' ); ?>
            </label>
            <input class="widefat"
                   id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                   name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
                   value="<?php echo $title; ?>"/>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'show_keyword' ); ?>">
                <?php esc_html_e( 'Enable Keyword Search', 'noo' ); ?>
            </label>
            <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'show_keyword' ) ); ?>" name="<?php echo $this->get_field_name( 'show_keyword' ); ?>">
                <option value="yes"<?php echo $show_keyword == 'yes' ? ' selected' : ''; ?>><?php esc_html_e( 'Yes', 'noo' ); ?></option>
                <option value="no"<?php echo $show_keyword == 'no' ? ' selected' : ''; ?>><?php esc_html_e( 'No', 'noo' ); ?></option>
            </select>
        </p>

        <?php for ( $po = 1; $po <= 12; $po ++ ) : ?>
            <?php $r_pos = esc_attr( $instance["r_pos{$po}"] ); ?>
            <!-- Search Position #<?php echo $po; ?> -->

            <p>
                <label for="<?php echo $this->get_field_id( "r_pos{$po}" ); ?>">
                    <?php esc_html_e( 'Search Position #' . $po, 'noo' ); ?>
                </label>
                <select class="widefat search-position" id="<?php echo esc_attr( $this->get_field_id( "r_pos{$po}" ) ); ?>" name="<?php echo $this->get_field_name( "r_pos{$po}" ); ?>">
                    <?php foreach ( $search_fields as $key => $value ) {
                        $selected = ( $r_pos == $key ) || strpos( $r_pos, $key . '|' ) !== false;
                        echo "<option value='{$key}'" . ( $selected ? ' selected' : '' ) . ">{$value}</option>";
                    } ?>
                </select>
            </p>

            <!-- /Search Position #<?php echo $po; ?> -->

        <?php endfor;
    }

    public function update( $new_instance, $old_instance ) {
        $instance                            = $old_instance;
        $instance['title']                   = strip_tags( $new_instance['title'] );
        $instance['show_keyword']            = strip_tags( $new_instance['show_keyword'] );
        for ( $po = 1; $po <= 12; $po ++ ) {
            $instance["r_pos{$po}"] = strip_tags( $new_instance["r_pos{$po}"] );
        }
        return $instance;
    }

    public function widget( $args, $instance ) {
        extract( $args );
        $title                   = apply_filters( 'widget_title', $instance['title'] );
        $show_keyword            = $instance['show_keyword'];
        $prefix = uniqid();

        echo $before_widget;
        echo $before_title . $title . $after_title;
        ?>

        <form
                id="<?php echo $prefix . '_form'; ?>"
                method="get"
                class="widget-csf-live-filter"
                action="<?php echo esc_url( home_url( '/' ) ); ?>"
                data-off-livesearch
                data-is-tax="<?php echo is_tax();?>">
            <?php if($show_keyword == 'yes'): ?>
                <div class="form-group">
                    <label class="sr-only" for="<?php echo $prefix.'_search-keyword'; ?>"><?php esc_html_e('Keyword','noo'); ?></label>
                    <input type="text"  class="form-control " id="<?php echo $prefix.'_search-keyword'; ?>" data-value="<?php echo esc_url(noo_upload_url()) ?>/job-tags.json"  name="s" placeholder="<?php esc_html_e('Keyword','noo') ?>" value="<?php echo get_search_query(); ?>">
                </div>
            <?php else: ?>
                <input type="hidden" name ="s" value="">
            <?php endif; ?>
            <div class="widget-fields-live-filter">
                <?php
                $list_post_id = array();
                if (isset($_GET['post_type'])=='noo_job'){
                    $args = array(
                        'post_type'         => $_GET['post_type'],
                        'post_status'       => 'publish',
                        's'                 => esc_html($_GET['s']),
                        'posts_per_page'    =>  -1,
                        'paged'             => isset($_GET['paged']) ? $_GET['paged'] : 1
                    );
                    unset($_GET['s']);
                    unset($_GET['action']);
                    unset($_GET['live-filter-nonce']);
                    $args = jm_job_query_from_request($args, $_GET);
                    $status = noo_get_option('noo_jobs_show_expired', false) ? array('publish', 'expired') : 'publish';
                    $args['post_status'] = $status;

                    $query = new WP_Query($args);
                    while ($query->have_posts()) {
                        $query->the_post();
                        $list_post_id[] = get_the_ID();
                    }
                }
                for ( $po = 1; $po <= 12; $po ++ ) {
                    if($instance["r_pos{$po}"] =='no'){
                        continue;
                    }
                    $field = $instance["r_pos{$po}"];
                    jm_job_filter_field( $field, $list_post_id);
                } ?>
            </div>
            <input type="hidden" class="form-control" name="post_type" value="noo_job"/>
            <?php   for ( $po = 1; $po <= 12; $po ++ ):
                if($instance["r_pos{$po}"] =='no'){
                    continue;
                } ?>
                <input type="hidden" name="widget_cs_field[]" value="<?php echo esc_attr($instance["r_pos{$po}"]); ?>">
            <?php endfor; ?>
            <input type="hidden" name="action" value="live_filter"/>
            <?php wp_nonce_field( 'noo-widget-live-filter', 'live-filter-nonce' ); ?>

        </form>
        <?php
        echo $after_widget;
    }
}

class Noo_Advanced_Resume_Search_Widget extends WP_Widget {

	public function __construct() {

		parent::__construct( false, __( 'Resume Advanced Search', 'noo' ) );
	}

	public function form( $instance ) {

		$default                 = array(
			'title'                   => '',
			'show_keyword'            => 'yes',
			'r_pos1'                  => '',
			'r_pos2'                  => '',
			'r_pos3'                  => '',
			'r_pos4'                  => '',
			'r_pos5'                  => '',
			'r_pos6'                  => '',
			'r_pos7'                  => '',
			'r_pos8'                  => '',
			'disable_live_search'     => false,
			'reset_button'            => false,
			'hide_search_button'      => false,
			// 'disable_multiple_select' => false,
		);
		$instance                = wp_parse_args( (array) $instance, $default );
		$title                   = esc_attr( $instance['title'] );
		$disable_live_search     = (bool) $instance['disable_live_search'];
		$reset_button            = (bool) $instance['reset_button'];
		$hide_search_button      = (bool) $instance['hide_search_button'];
		// $disable_multiple_select = (bool) $instance['disable_multiple_select'];
		$show_keyword            = esc_attr( $instance['show_keyword'] );

		$resume_fields = jm_get_resume_search_custom_fields();
		$search_fields = array(
			'no' => __( 'None', 'noo' ),
		);
		foreach ( $resume_fields as $k => $field ) {
			if ( isset( $field['is_default'] ) ) {
				$label                = ( isset( $field['label_translated'] ) ? $field['label_translated'] : ( isset( $field['label'] ) ? $field['label'] : $k ) );
				$id                   = $field['name'];
				$search_fields[ $id ] = $label;
			} else {
				$label                = __( 'Custom Field: ', 'noo' ) . ( isset( $field['label_translated'] ) ? $field['label_translated'] : ( isset( $field['label'] ) ? $field['label'] : $k ) );
				$id                   = jm_resume_custom_fields_name( @$field['name'], $field ) . '|' . ( isset( $field['label'] ) ? $field['label'] : $k );
				$search_fields[ $id ] = $label;
			}
		}

		?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>">
				<?php _e( 'Title', 'noo' ); ?>
            </label>
            <input class="widefat"
                   id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                   name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
                   value="<?php echo $title; ?>"/>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'show_keyword' ); ?>">
				<?php _e( 'Enable Keyword Search', 'noo' ); ?>
            </label>
            <select class="widefat"
                    id="<?php echo esc_attr( $this->get_field_id( 'show_keyword' ) ); ?>"
                    name="<?php echo $this->get_field_name( 'show_keyword' ); ?>">
                <option
                        value="yes"<?php echo $show_keyword == 'yes' ? ' selected' : ''; ?>><?php _e( 'Yes', 'noo' ); ?></option>
                <option
                        value="no"<?php echo $show_keyword == 'no' ? ' selected' : ''; ?>><?php _e( 'No', 'noo' ); ?></option>
            </select>
        </p>

		<?php for ( $po = 1; $po <= 8; $po ++ ) : ?>
			<?php $r_pos = esc_attr( $instance["r_pos{$po}"] ); ?>
            <!-- Search Position #<?php echo $po; ?> -->

            <p>
                <label for="<?php echo $this->get_field_id( "r_pos{$po}" ); ?>">
					<?php _e( 'Search Position #' . $po, 'noo' ); ?>
                </label>
                <select class="widefat search-position"
                        id="<?php echo esc_attr( $this->get_field_id( "r_pos{$po}" ) ); ?>"
                        name="<?php echo $this->get_field_name( "r_pos{$po}" ); ?>">
					<?php foreach ( $search_fields as $key => $value ) {
						echo "<option value='{$key}'" . ( $r_pos == $key ? ' selected' : '' ) . ">{$value}</option>";
					} ?>
                </select>
            </p>

            <!-- /Search Position #<?php echo $po; ?> -->

		<?php endfor; ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'disable_live_search' ); ?>">
				<?php _e( 'Disable Live Search', 'noo' ); ?>
            </label>
            <input class="widefat" type="checkbox"
                   id="<?php echo esc_attr( $this->get_field_id( 'disable_live_search' ) ); ?>"
                   name="<?php echo $this->get_field_name( 'disable_live_search' ); ?>" type="text"
                   value="1" <?php checked( true, $disable_live_search ); ?> />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'reset_button' ); ?>">
				<?php _e( 'Enable Reset Button', 'noo' ); ?>
            </label>
            <input class="widefat" type="checkbox"
                   id="<?php echo esc_attr( $this->get_field_id( 'reset_button' ) ); ?>"
                   name="<?php echo $this->get_field_name( 'reset_button' ); ?>" type="text"
                   value="1" <?php checked( true, $reset_button ); ?> />
        <p>
            <small><?php _e( 'Show the button that will reset all criteria.', 'noo' ); ?></small>
        </p>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'hide_search_button' ); ?>">
				<?php _e( 'Hide Search Button', 'noo' ); ?>
            </label>
            <input class="widefat" type="checkbox"
                   id="<?php echo esc_attr( $this->get_field_id( 'hide_search_button' ) ); ?>"
                   name="<?php echo $this->get_field_name( 'hide_search_button' ); ?>" type="text"
                   value="1" <?php checked( true, $hide_search_button ); ?> />
        <p>
            <small><?php _e( 'The search form is live so you may not need the search button.', 'noo' ); ?></small>
        </p>
        <!-- <p>
            <label for="<?php // $this->get_field_id( 'disable_multiple_select' ); ?>">
				<?php //_e( 'Disable Multiple Select', 'noo' ); ?>
            </label>
            <input class="widefat" type="checkbox"
                   id="<?php //echo esc_attr( $this->get_field_id( 'disable_multiple_select' ) ); ?>"
                   name="<?php //echo $this->get_field_name( 'disable_multiple_select' ); ?>" type="text"
                   value="1" <?php //checked( true, $disable_multiple_select ); ?> />
        </p> -->
        </p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance                            = $old_instance;
		$instance['title']                   = strip_tags( $new_instance['title'] );
		$instance['disable_live_search']     = isset( $new_instance['disable_live_search'] ) ? strip_tags( $new_instance['disable_live_search'] ) : '';
		// $instance['disable_multiple_select'] = isset( $new_instance['disable_multiple_select'] ) ? strip_tags( $new_instance['disable_multiple_select'] ) : '';
		$instance['reset_button']            = isset( $new_instance['reset_button'] ) ? strip_tags( $new_instance['reset_button'] ) : '';
		$instance['hide_search_button']      = isset( $new_instance['hide_search_button'] ) ? strip_tags( $new_instance['hide_search_button'] ) : '';
		$instance['show_keyword']            = strip_tags( $new_instance['show_keyword'] );
		for ( $po = 1; $po <= 8; $po ++ ) {
			$instance["r_pos{$po}"] = strip_tags( $new_instance["r_pos{$po}"] );
		}

		return $instance;
	}

	public function widget( $args, $instance ) {
		extract( $args );
		$title                   = apply_filters( 'widget_title', $instance['title'] );
		$disable_live_search     = isset( $instance['disable_live_search'] ) && ! empty( $instance['disable_live_search'] );
		$reset_button            = isset( $instance['reset_button'] ) && ! empty( $instance['reset_button'] );
		$hide_search_button      = isset( $instance['hide_search_button'] ) && ! empty( $instance['hide_search_button'] );
		$show_keyword            = $instance['show_keyword'];
		$disable_multiple_select = noo_get_option( 'noo_show_resume_search_field_type',0);
		// $disable_multiple_select = isset( $instance['disable_multiple_select'] ) && ! empty( $instance['disable_multiple_select'] );
		$allow_multiple_select   = ! $disable_multiple_select;

		$prefix = uniqid();

		echo $before_widget;
		echo $before_title . $title . $after_title;
		?>
        <form id="<?php echo $prefix . '_form'; ?>" method="get" class="widget-advanced-search"
              action="<?php echo esc_url( home_url( '/' ) ); ?>">
            <style type="text/css">
                .widget-advanced-search ul li:first-child, .widget-advanced-search ol li:first-child {
                    padding-top: 6px;
                }
            </style>
			<?php
			if ( $show_keyword == 'yes' ) :
				?>
                <div class="form-group">
                    <label class="sr-only"
                           for="<?php echo $prefix . '_search-keyword'; ?>"><?php _e( 'Keyword', 'noo' ); ?></label>
                    <input type="text" class="form-control" id="<?php echo $prefix . '_search-keyword'; ?>" name="s"
                           placeholder="<?php _e( 'Keyword', 'noo' ); ?>"
                           value="<?php echo get_search_query(); ?>"/>
                </div>
			<?php
			else :
				?>
                <input type="hidden" name="s" value=""/>
			<?php
			endif;
			for ( $po = 1; $po <= 8; $po ++ ) {
				$field = $instance["r_pos{$po}"];
				jm_resume_advanced_search_field( $field, $disable_multiple_select );
			}
			?>
            <input type="hidden" class="form-control" name="post_type" value="noo_resume"/>
			<?php if ( jm_can_view_resume( null, true ) && ! $disable_live_search ) : ?>
                <input type="hidden" name="action" value="live_search"/>
				<?php wp_nonce_field( 'noo-advanced-live-search', 'live-search-nonce' ); ?>
			<?php endif; ?>
			<?php if ( ! $hide_search_button ) : ?>
                <button type="submit" class="btn btn-primary btn-search-submit"><?php _e( 'Search', 'noo' ); ?></button>
			<?php endif; ?>
			<?php if ( $reset_button ) : ?>
                <a href="#" class="btn btn-default reset-search"><i class="fas fa-redo-alt"></i> <?php _e( 'Reset', 'noo' ); ?></a>
			<?php endif; ?>
        </form>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $("#<?php echo $prefix . '_form'; ?> .reset-search").click(function (e) {
                    e.preventDefault();

                    var $form = $(this).parent('form');
                    $form.find('option:selected').removeAttr('selected');

                    $form.get(0).reset(); /* Reset form value*/

                    // $form.find(".form-control-chosen").trigger("chosen:updated");

                    /* Get any input tag and run the function change() --> callback ajax*/
                    $form.find('.form-group').first().find(':input').not(':button, :submit, :reset, :hidden').change();

                    $('select.form-control',$form).multiselect('refresh');

                    return false;
                });
            });
        </script>
		<?php if ( jm_can_view_resume( null, true ) && ! $disable_live_search ) : ?>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    var container = $(".noo-main > .resumes");
                    if (!$('.map_info').length && container.length) {
                        $("#<?php echo $prefix . '_form'; ?>").on('change','select, input:not([type="checkbox"]):not([type="radio"]):not([type="hidden"])', function (event) {
                            event.preventDefault();
                            // Filter button when run on Mobile
                    		$(this).closest('.widget_noo_advanced_resume_search_widget').removeClass('on-filter');
                            $('.noo-main').addClass('noo-loading').append('<div class="noo-loader loadmore-loading"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>');
                            $('html,body').animate({scrollTop: $('.noo-main').offset().top - 200}, 500);
                            var $form = $("#<?php echo $prefix . '_form'; ?> .form-control");
                            var data = $(this).parents('form').serialize();
                            history.pushState(null, null, "?" + $form.serialize());
                            $.ajax({
                                url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                                data: data
                            })
                                .done(function (data) {
                                    $(".noo-main").html(data);
                                    $('.noo-main').removeClass('noo-loading');
                                    if ($('[data-paginate="loadmore"]').find(".loadmore-action").length) {
                                        $('[data-paginate="loadmore"]').each(function () {
                                            var $this = $(this);
                                            $this.nooLoadmore({
                                                navSelector: $this.find("div.pagination"),
                                                nextSelector: $this.find("div.pagination a.next"),
                                                itemSelector: "article.loadmore-item",
                                                finishedMsg: "<?php echo __( 'All resumes displayed', 'noo' ); ?>"
                                            });
                                        });
                                    }
                                })
                                .fail(function () {

                                })
                        });
                    }
                    $("#<?php echo $prefix . '_form'; ?>").submit(function () {
                        $(this).find("input[name='action']").remove();
                        $(this).find("input[name='_wp_http_referer']").remove();
                        $(this).find("input[name='live-search-nonce']").remove();

                        return true;
                    });
                });
            </script>
		<?php
		endif;
		echo $after_widget;
	}
}

class Noo_Advanced_Resume_Filter_Widget extends WP_Widget {

    public function __construct() {

        parent::__construct( false, esc_html__( 'Resume Filter Widget', 'noo' ) );
    }

    public function form( $instance ) {

        $default                 = array(
            'title'                   => '',
            'show_keyword'            => 'yes',
            'r_pos1'                  => '',
            'r_pos2'                  => '',
            'r_pos3'                  => '',
            'r_pos4'                  => '',
            'r_pos5'                  => '',
            'r_pos6'                  => '',
            'r_pos7'                  => '',
            'r_pos8'                  => '',
            'r_pos9'                  => '',
            'r_pos10'                  => '',
            'r_pos11'                  => '',
            'r_pos12'                  => '',
        );
        $instance                = wp_parse_args( (array) $instance, $default );
        $title                   = esc_attr( $instance['title'] );
        $show_keyword            = esc_attr( $instance['show_keyword'] );

        $custom_fields = jm_get_filter_resume_custom_fields();
        $search_fields = array(
            'no' => esc_html__( 'None', 'noo' ),
        );
        foreach ( $custom_fields as $k => $field ) {
            if ( isset( $field['is_default'] ) ) {
                $label                = isset( $field['label'] ) ? $field['label'] : $k;
                $id                   = $field['name'];
                $search_fields[ $id ] = $label;
            } else {
                $label                = esc_html__( 'Custom Field: ', 'noo' ) . ( isset( $field['label_translated'] ) ? $field['label_translated'] : ( isset( $field['label'] ) ? $field['label'] : $k ) );
                $id                   = jm_resume_custom_fields_name( $field['name'], $field );
                $search_fields[ $id ] = $label;
            }
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>">
                <?php esc_html_e( 'Title', 'noo' ); ?>
            </label>
            <input class="widefat"
                   id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                   name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
                   value="<?php echo $title; ?>"/>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'show_keyword' ); ?>">
                <?php esc_html_e( 'Enable Keyword Search', 'noo' ); ?>
            </label>
            <select class="widefat"
                    id="<?php echo esc_attr( $this->get_field_id( 'show_keyword' ) ); ?>"
                    name="<?php echo $this->get_field_name( 'show_keyword' ); ?>">
                <option
                        value="yes"<?php echo $show_keyword == 'yes' ? ' selected' : ''; ?>><?php esc_html_e( 'Yes', 'noo' ); ?></option>
                <option
                        value="no"<?php echo $show_keyword == 'no' ? ' selected' : ''; ?>><?php esc_html_e( 'No', 'noo' ); ?></option>
            </select>
        </p>

        <?php for ( $po = 1; $po <= 12; $po ++ ) : ?>
            <?php $r_pos = esc_attr( $instance["r_pos{$po}"] ); ?>
            <!-- Search Position #<?php echo $po; ?> -->

            <p>
                <label for="<?php echo $this->get_field_id( "r_pos{$po}" ); ?>">
                    <?php esc_html_e( 'Search Position #' . $po, 'noo' ); ?>
                </label>
                <select class="widefat search-position"
                        id="<?php echo esc_attr( $this->get_field_id( "r_pos{$po}" ) ); ?>"
                        name="<?php echo $this->get_field_name( "r_pos{$po}" ); ?>">
                    <?php foreach ( $search_fields as $key => $value ) {
                        $selected = ( $r_pos == $key ) || strpos( $r_pos, $key . '|' ) !== false;
                        echo "<option value='{$key}'" . ( $selected ? ' selected' : '' ) . ">{$value}</option>";
                    } ?>
                </select>
            </p>

            <!-- /Search Position #<?php echo $po; ?> -->

        <?php endfor;
    }

    public function update( $new_instance, $old_instance ) {
        $instance                            = $old_instance;
        $instance['title']                   = strip_tags( $new_instance['title'] );
        $instance['show_keyword']            = strip_tags( $new_instance['show_keyword'] );
        for ( $po = 1; $po <= 12; $po ++ ) {
            $instance["r_pos{$po}"] = strip_tags( $new_instance["r_pos{$po}"] );
        }
        return $instance;
    }

    public function widget( $args, $instance ) {
        extract( $args );
        $title                   = apply_filters( 'widget_title', $instance['title'] );
        $show_keyword            = $instance['show_keyword'];
        $can_view_resume 		 = jm_can_view_resume( null, true );
        $prefix = uniqid();

        echo $before_widget;
        echo $before_title . $title . $after_title;
        ?>
        <form
                id="<?php echo $prefix . '_form'; ?>"
                method="get"
                class="widget-csf-resume-live-filter"
                action="<?php echo esc_url( home_url( '/' ) ); ?>"
                data-off-livesearch
                data-can-view="<?php echo esc_attr($can_view_resume);?>">
            <?php if($show_keyword == 'yes'): ?>
                <div class="form-group">
                    <label class="sr-only" for="<?php echo $prefix.'_search-keyword'; ?>"><?php esc_html_e('Keyword','noo'); ?></label>
                    <input type="text" class="form-control" id="<?php echo $prefix. '_search-keyword'; ?>" name="s" placeholder="<?php esc_html_e('Keyword','noo') ?>" value="<?php echo get_search_query(); ?>">
                </div>
            <?php else: ?>
                <input type="hidden" name ="s" value="">
            <?php endif; ?>
            <div class="widget-fields-live-filter">
                <?php
                $list_post_id = array();
                if (isset($_GET['post_type'])=='noo_resume'){
                    $args = array(
                        'post_type'         => $_GET['post_type'],
                        'post_status'       => 'publish',
                        's'                 => esc_html($_GET['s']),
                        'posts_per_page'    =>  -1,
                        'paged'             => isset($_GET['paged']) ? $_GET['paged'] : 1
                    );
                    unset($_GET['s']);
                    unset($_GET['action']);
                    unset($_GET['live-filter-nonce']);
                    $args = jm_resume_query_from_request($args, $_GET);
                    $query = new WP_Query($args);
                    while ($query->have_posts()) {
                        $query->the_post();
                        $list_post_id[] = get_the_ID();
                    }
                }
                for ( $po = 1; $po <= 12; $po ++ ) {
                    if($instance["r_pos{$po}"] =='no'){
                        continue;
                    }
                    $field = $instance["r_pos{$po}"];
                    jm_resume_filter_field( $field,$list_post_id);
                } ?>
            </div>
            <input type="hidden" class="form-control" name="post_type" value="noo_resume"/>
            <?php   for ( $po = 1; $po <= 12; $po ++ ):
                if($instance["r_pos{$po}"] =='no'){
                    continue;
                } ?>
                <input type="hidden" name="widget_cs_field[]" value="<?php echo esc_attr($instance["r_pos{$po}"]); ?>">
            <?php endfor; ?>
            <input type="hidden" name="action" value="live_filter"/>
            <?php wp_nonce_field( 'noo-widget-live-filter', 'live-filter-nonce' ); ?>

        </form>
        <?php
        echo $after_widget;
    }
}

class Noo_Advanced_Company_Search_Widget extends WP_Widget{
    public function __construct() {

        parent::__construct( false, __( 'Company Advanced Search', 'noo' ) );
    }

    public function form( $instance ) {

        $default                 = array(
            'title'                   => '',
            'show_keyword'            => 'yes',
            'r_pos1'                  => '',
            'r_pos2'                  => '',
            'r_pos3'                  => '',
            'r_pos4'                  => '',
            'r_pos5'                  => '',
            'r_pos6'                  => '',
            'r_pos7'                  => '',
            'r_pos8'                  => '',
            'reset_button'            => false,
            'hide_search_button'      => false,
            'disable_multiple_select' => false,
        );
        $instance                = wp_parse_args( (array) $instance, $default );
        $title                   = esc_attr( $instance['title'] );
        $reset_button            = (bool) $instance['reset_button'];
        $hide_search_button      = (bool) $instance['hide_search_button'];
        $disable_multiple_select = (bool) $instance['disable_multiple_select'];
        $show_keyword            = esc_attr( $instance['show_keyword'] );

        $company_fields = jm_get_company_search_custom_fields();
        $search_fields = array(
            'no' => __( 'None', 'noo' ),
        );
        foreach ( $company_fields as $k => $field ) {
            if ( isset( $field['is_default'] ) ) {
                $label                = ( isset( $field['label_translated'] ) ? $field['label_translated'] : ( isset( $field['label'] ) ? $field['label'] : $k ) );
                $id                   = $field['name'];
                $search_fields[ $id ] = $label;
            } else {
                $label                = __( 'Custom Field: ', 'noo' ) . ( isset( $field['label_translated'] ) ? $field['label_translated'] : ( isset( $field['label'] ) ? $field['label'] : $k ) );
                $id                   = jm_company_custom_fields_name( @$field['name'], $field ) . '|' . ( isset( $field['label'] ) ? $field['label'] : $k );
                $search_fields[ $id ] = $label;
            }
        }

        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>">
                <?php _e( 'Title', 'noo' ); ?>
            </label>
            <input class="widefat"
                   id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                   name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
                   value="<?php echo $title; ?>"/>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'show_keyword' ); ?>">
                <?php _e( 'Enable Keyword Search', 'noo' ); ?>
            </label>
            <select class="widefat"
                    id="<?php echo esc_attr( $this->get_field_id( 'show_keyword' ) ); ?>"
                    name="<?php echo $this->get_field_name( 'show_keyword' ); ?>">
                <option
                        value="yes"<?php echo $show_keyword == 'yes' ? ' selected' : ''; ?>><?php _e( 'Yes', 'noo' ); ?></option>
                <option
                        value="no"<?php echo $show_keyword == 'no' ? ' selected' : ''; ?>><?php _e( 'No', 'noo' ); ?></option>
            </select>
        </p>

        <?php for ( $po = 1; $po <= 8; $po ++ ) : ?>
            <?php $r_pos = esc_attr( $instance["r_pos{$po}"] ); ?>
            <!-- Search Position #<?php echo $po; ?> -->

            <p>
                <label for="<?php echo $this->get_field_id( "r_pos{$po}" ); ?>">
                    <?php _e( 'Search Position #' . $po, 'noo' ); ?>
                </label>
                <select class="widefat search-position"
                        id="<?php echo esc_attr( $this->get_field_id( "r_pos{$po}" ) ); ?>"
                        name="<?php echo $this->get_field_name( "r_pos{$po}" ); ?>">
                    <?php foreach ( $search_fields as $key => $value ) {
                        echo "<option value='{$key}'" . ( $r_pos == $key ? ' selected' : '' ) . ">{$value}</option>";
                    } ?>
                </select>
            </p>

            <!-- /Search Position #<?php echo $po; ?> -->

        <?php endfor; ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'reset_button' ); ?>">
                <?php _e( 'Enable Reset Button', 'noo' ); ?>
            </label>
            <input class="widefat" type="checkbox"
                   id="<?php echo esc_attr( $this->get_field_id( 'reset_button' ) ); ?>"
                   name="<?php echo $this->get_field_name( 'reset_button' ); ?>" type="text"
                   value="1" <?php checked( true, $reset_button ); ?> />
        <p>
            <small><?php _e( 'Show the button that will reset all criteria.', 'noo' ); ?></small>
        </p>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'hide_search_button' ); ?>">
                <?php _e( 'Hide Search Button', 'noo' ); ?>
            </label>
            <input class="widefat" type="checkbox"
                   id="<?php echo esc_attr( $this->get_field_id( 'hide_search_button' ) ); ?>"
                   name="<?php echo $this->get_field_name( 'hide_search_button' ); ?>" type="text"
                   value="1" <?php checked( true, $hide_search_button ); ?> />
        <p>
            <small><?php _e( 'The search form is live so you may not need the search button.', 'noo' ); ?></small>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'disable_multiple_select' ); ?>">
                <?php _e( 'Disable Multiple Select', 'noo' ); ?>
            </label>
            <input class="widefat" type="checkbox"
                   id="<?php echo esc_attr( $this->get_field_id( 'disable_multiple_select' ) ); ?>"
                   name="<?php echo $this->get_field_name( 'disable_multiple_select' ); ?>" type="text"
                   value="1" <?php checked( true, $disable_multiple_select ); ?> />
        </p>
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance                            = $old_instance;
        $instance['title']                   = strip_tags( $new_instance['title'] );
        $instance['disable_multiple_select'] = isset( $new_instance['disable_multiple_select'] ) ? strip_tags( $new_instance['disable_multiple_select'] ) : '';
        $instance['reset_button']            = isset( $new_instance['reset_button'] ) ? strip_tags( $new_instance['reset_button'] ) : '';
        $instance['hide_search_button']      = isset( $new_instance['hide_search_button'] ) ? strip_tags( $new_instance['hide_search_button'] ) : '';
        $instance['show_keyword']            = strip_tags( $new_instance['show_keyword'] );
        for ( $po = 1; $po <= 8; $po ++ ) {
            $instance["r_pos{$po}"] = strip_tags( $new_instance["r_pos{$po}"] );
        }

        return $instance;
    }

    public function widget( $args, $instance ) {
        extract( $args );
        $title                   = apply_filters( 'widget_title', $instance['title'] );
        $reset_button            = isset( $instance['reset_button'] ) && ! empty( $instance['reset_button'] );
        $hide_search_button      = isset( $instance['hide_search_button'] ) && ! empty( $instance['hide_search_button'] );
        $show_keyword            = $instance['show_keyword'];
        $disable_multiple_select = isset( $instance['disable_multiple_select'] ) && ! empty( $instance['disable_multiple_select'] );
        $allow_multiple_select   = ! $disable_multiple_select;

        $prefix = uniqid();

        echo $before_widget;
        echo $before_title . $title . $after_title;
        ?>
        <form id="<?php echo $prefix . '_form'; ?>" method="get" class="widget-advanced-search"
              action="<?php echo esc_url( home_url( '/' ) ); ?>">
            <style type="text/css">
                .widget-advanced-search ul li:first-child, .widget-advanced-search ol li:first-child {
                    padding-top: 6px;
                }
            </style>
            <?php
            if ( $show_keyword == 'yes' ) :
                ?>
                <div class="form-group">
                    <label class="sr-only"
                           for="<?php echo $prefix . '_search-keyword'; ?>"><?php _e( 'Keyword', 'noo' ); ?></label>
                    <input type="text" class="form-control" id="<?php echo $prefix . '_search-keyword'; ?>" name="s"
                           placeholder="<?php _e( 'Keyword', 'noo' ); ?>"
                           value="<?php echo get_search_query() ?>"/>
                </div>
            <?php
            else :
                ?>
                <input type="hidden" name="s" value=""/>
            <?php
            endif;
            for ( $po = 1; $po <= 8; $po ++ ) {
                $field = $instance["r_pos{$po}"];
                jm_company_advanced_search_field( $field, $disable_multiple_select );
            }
            ?>
            <input type="hidden" class="form-control" name="post_type" value="noo_company"/>
            <?php if ( ! $hide_search_button ) : ?>
                <button type="submit" class="btn btn-primary btn-search-submit"><?php _e( 'Search', 'noo' ); ?></button>
            <?php endif; ?>
            <?php if ( $reset_button ) : ?>
                <a href="<?php echo $url = get_post_type_archive_link('noo_company')?>" class="btn btn-default reset-search"><i class="fas fa-redo-alt"></i> <?php _e( 'Reset', 'noo' ); ?></a>
            <?php endif; ?>
        </form>
        <?php
        echo $after_widget;
    }
}

class Noo_Social_Profile extends WP_Widget {
	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'Noo_Social_Profile',
			__( 'Noo Social Networks', 'noo' ), // Name
			array( 'description' => __( 'Links to Author social media profile', 'noo' ), )
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {

		$title     = apply_filters( 'widget_title', $instance['title'] );
		$facebook  = $instance['facebook'];
		$twitter   = $instance['twitter'];
		$google    = $instance['google'];
		$instagram = $instance['instagram'];
		$pinterest = $instance['pinterest'];
		$youtobe   = $instance['youtobe'];
		$linkedin  = $instance['linkedin'];
		$xing      = isset($instance['xing']) ? $instance['xing'] : '';

		// social profile link
		$facebook_profile  = '<a class="facebook" href="' . $facebook . '"><i class="fab fa-facebook-f"></i></a>';
		$twitter_profile   = '<a class="twitter" href="' . $twitter . '"><i class="fab fa-twitter"></i></a>';
		$google_profile    = '<a class="google" href="' . $google . '"><i class="fab fa-google-plus-g"></i></a>';
		$instagram_profile = '<a class="instagram" href="' . $instagram . '"><i class="fab fa-instagram"></i></a>';
		$pinterest_profile = '<a class="instagram" href="' . $pinterest . '"><i class="fab fa-pinterest-p"></i></i></a>';
		$youtobe_profile   = '<a class="instagram" href="' . $youtobe . '"><i class="fab fa-youtube"></i></i></a>';
		$linkedin_profile  = '<a class="linkedin" href="' . $linkedin . '"><i class="fab fa-linkedin-in"></i></a>';
		$xing_profile      = '<a class="xing" href="'.$xing.'"><i class="fab fa-xing"></i></a>';


		echo $args['before_widget'];

		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		echo '<div class="social-icons">';
		echo ( ! empty( $facebook ) ) ? $facebook_profile : '';
		echo ( ! empty( $twitter ) ) ? $twitter_profile : '';
		echo ( ! empty( $google ) ) ? $google_profile : '';
		echo ( ! empty( $instagram ) ) ? $instagram_profile : '';
		echo ( ! empty( $pinterest ) ) ? $pinterest_profile : '';
		echo ( ! empty( $youtobe ) ) ? $youtobe_profile : '';
		echo ( ! empty( $linkedin ) ) ? $linkedin_profile : '';
		echo ( ! empty( $xing)) ? $xing_profile : '';
		echo '</div>';

		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {

		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		$title = empty( $instance['title'] ) ? $title = '' : '';

		$facebook  = isset( $instance['facebook'] ) ? ( $instance['facebook'] ) : '';
		$twitter   = isset( $instance['twitter'] ) ? ( $instance['twitter'] ) : '';
		$google    = isset( $instance['google'] ) ? ( $instance['google'] ) : '';
		$instagram = isset( $instance['instagram'] ) ? ( $instance['instagram'] ) : '';
		$pinterest = isset( $instance['pinterest'] ) ? ( $instance['pinterest'] ) : '';
		$youtobe   = isset( $instance['youtobe'] ) ? ( $instance['youtobe'] ) : '';
		$linkedin  = isset( $instance['linkedin'] ) ? ( $instance['linkedin'] ) : '';
		$xing      = isset( $instance['xing']) ? ( $instance['xing']) : '';

		?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'noo' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
                   name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
                   value="<?php echo esc_attr( $title ); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'facebook' ); ?>"><?php _e( 'Facebook:', 'noo' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'facebook' ); ?>"
                   name="<?php echo $this->get_field_name( 'facebook' ); ?>" type="text"
                   value="<?php echo esc_attr( $facebook ); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'twitter' ); ?>"><?php _e( 'Twitter:', 'noo' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'twitter' ); ?>"
                   name="<?php echo $this->get_field_name( 'twitter' ); ?>" type="text"
                   value="<?php echo esc_attr( $twitter ); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'google' ); ?>"><?php _e( 'Google+:', 'noo' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'google' ); ?>"
                   name="<?php echo $this->get_field_name( 'google' ); ?>" type="text"
                   value="<?php echo esc_attr( $google ); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'instagram' ); ?>"><?php _e( 'Instagram:', 'noo' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'instagram' ); ?>"
                   name="<?php echo $this->get_field_name( 'instagram' ); ?>" type="text"
                   value="<?php echo esc_attr( $instagram ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'pinterest' ); ?>"><?php _e( 'Pinterest:', 'noo' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'pinterest' ); ?>"
                   name="<?php echo $this->get_field_name( 'pinterest' ); ?>" type="text"
                   value="<?php echo esc_attr( $pinterest ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'youtobe' ); ?>"><?php _e( 'Youtobe:', 'noo' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'youtobe' ); ?>"
                   name="<?php echo $this->get_field_name( 'youtobe' ); ?>" type="text"
                   value="<?php echo esc_attr( $youtobe ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'linkedin' ); ?>"><?php _e( 'Linkedin:', 'noo' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'linkedin' ); ?>"
                   name="<?php echo $this->get_field_name( 'linkedin' ); ?>" type="text"
                   value="<?php echo esc_attr( $linkedin ); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('xing'); ?>"><?php _e('Xing:','noo'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('xing'); ?>" name="<?php echo $this->get_field_name('xing') ?>" value="<?php echo esc_attr($xing); ?>" type="text">
        </p>
		<?php
	}

	/**
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance              = array();
		$instance['title']     = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['facebook']  = ( ! empty( $new_instance['facebook'] ) ) ? strip_tags( $new_instance['facebook'] ) : '';
		$instance['twitter']   = ( ! empty( $new_instance['twitter'] ) ) ? strip_tags( $new_instance['twitter'] ) : '';
		$instance['google']    = ( ! empty( $new_instance['google'] ) ) ? strip_tags( $new_instance['google'] ) : '';
		$instance['instagram'] = ( ! empty( $new_instance['instagram'] ) ) ? strip_tags( $new_instance['instagram'] ) : '';
		$instance['pinterest'] = ( ! empty( $new_instance['pinterest'] ) ) ? strip_tags( $new_instance['pinterest'] ) : '';
		$instance['youtobe']   = ( ! empty( $new_instance['youtobe'] ) ) ? strip_tags( $new_instance['youtobe'] ) : '';
		$instance['linkedin']  = ( ! empty( $new_instance['linkedin'] ) ) ? strip_tags( $new_instance['linkedin'] ) : '';
		$instance['xing']      = ( ! empty( $new_instance['xing'])) ? strip_tags($new_instance['xing']) : '' ;

		return $instance;
	}

}

function noo_register_widget() {
	register_widget( 'Noo_Tweets' );
	register_widget( 'Noo_MailChimp' );
	if ( class_exists( 'Noo_Job' ) ) {
		register_widget( 'Noo_Job_Type_Widget' );
		register_widget( 'Noo_Job_Category_Widget' );
		register_widget( 'Noo_Job_Location_Widget' );
		register_widget( 'Noo_Job_Search_Widget' );
        register_widget( 'Noo_Blog_Search_Widget' );
		register_widget( 'Noo_Job_Count_Widget' );
		register_widget( 'Noo_Jobs_Widget' );
		register_widget( 'Noo_Advanced_Job_Search_Widget' );
        register_widget( 'Noo_Advanced_Job_Filter_Widget' );
		register_widget( 'Noo_Company_Search_Widget' );
		register_widget( 'Noo_Social_Profile' );
		register_widget( 'Noo_Advanced_Company_Search_Widget');
	}

	if ( class_exists( 'Noo_Resume' ) ) {
		register_widget( 'Noo_Resume_Categories_Widget' );
		register_widget( 'Noo_Resume_Search_Widget' );
		register_widget( 'Noo_Resume_Count_Widget' );
		register_widget( 'Noo_Advanced_Resume_Search_Widget' );
        register_widget( 'Noo_Advanced_Resume_Filter_Widget' );
	}
}

add_action( 'widgets_init', 'noo_register_widget' );