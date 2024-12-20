<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Rainmaker' ) ) {

	class Rainmaker {
		var $plugin_url;
		var $plugin_path;
		var $version;

		function __construct() {
			global $ig_rm_feedback, $ig_rm_tracker;

			$feedback_version = IG_RM_FEEDBACK_TRACKER_VERSION;

			$this->plugin_url  = untrailingslashit( plugins_url( '/', __FILE__ ) ) . '/';
			$this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
			$this->version     = IG_RM_PLUGIN_VERSION;

			//welcome
			add_action( 'admin_init', array( &$this, 'welcome' ) );
			add_action( 'init', array( &$this, 'register_rainmaker_form_post_type' ) );
			add_action( 'init', array( &$this, 'register_lead_post_type' ) );
			add_action( 'admin_init', array( &$this, 'import_ig_forms' ) );
			add_action( 'edit_form_before_permalink', array( &$this, 'form_design_content' ) );
			add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_admin_styles_and_scripts' ) );
			add_action( 'wp_footer', array( &$this, 'enqueue_frontend_styles_and_scripts' ) );
			add_action( 'save_post', array( &$this, 'save_form_settings' ), 10, 2 );
			add_shortcode( 'rainmaker_form', array( &$this, 'execute_shortcode' ) );

			// support and upgrade page
			add_action( 'admin_menu', array( &$this, 'admin_menus' ) );
			add_action( 'rm_about_changelog', array( &$this, 'klawoo_subscribe_form' ) );
			//remove all actions
			add_filter( 'post_row_actions', array( &$this, 'remove_rm_form_action' ), 10, 2 );
			//remove bulk action
			add_filter( 'bulk_actions-edit-rainmaker_lead', array( &$this, 'remove_lead_bulk_action' ), 10, 2 );

			//add columns
			add_filter( 'manage_edit-rainmaker_form_columns', array( &$this, 'edit_form_columns' ) );
			add_action( 'manage_rainmaker_form_posts_custom_column', array( &$this, 'custom_form_columns' ), 2 );

			add_filter( 'manage_edit-rainmaker_lead_columns', array( &$this, 'edit_lead_columns' ) );
			add_action( 'manage_rainmaker_lead_posts_custom_column', array( &$this, 'custom_lead_columns' ), 2 );
			//sort_lead_columns
			add_filter( 'manage_edit-rainmaker_lead_sortable_columns', array( &$this, 'sort_lead_columns' ), 2 );

			add_action( 'rainmaker_add_form_design_options', array( &$this, 'rm_add_custom_css_textarea' ), 2 );

			add_filter( 'rainmaker_prepare_lead', array( &$this, 'maipoet_prepare_lead' ), 10, 2 );
			add_filter( 'rainmaker_prepare_lead', array( &$this, 'madmimi_prepare_lead' ), 10, 2 );
			add_filter( 'rainmaker_prepare_lead', array( &$this, 'rainmaker_prepare_lead' ), 100, 2 );
			add_filter( 'rainmaker_clean_lead_data', array( &$this, 'rainmaker_clean_lead_data' ), 1 );
			add_filter( 'rainmaker_validate_request', array( &$this, 'rainmaker_validate_request' ), 100, 2 );
			add_filter( 'rainmaker_before_form', array( &$this, 'rainmaker_before_form' ), 10, 3 );
			add_filter( 'rainmaker_after_form', array( &$this, 'rainmaker_after_form' ), 10, 3 );

			add_action( 'pre_get_posts', array( &$this, 'rm_custom_search_query' ), 10, 2 );
			add_filter( 'posts_search', array( &$this, 'rm_custom_search_query_string' ), 10, 2 );

			add_action( 'rainmaker_post_lead', array( &$this, 'trigger_webhook' ), 10, 2 );

			//mail on form submission
			add_action( 'rainmaker_post_lead', array( &$this, 'rm_send_mail' ), 10, 2 );

			//filter lead data
			add_filter( 'rainmaker_filter_lead', array( &$this, 'rm_filter_lead_data' ) );

			add_filter( 'ig_rm_tracking_data_params', array( &$this, 'add_tracking_data' ) );

			// execute shortcode in sidebar
			add_filter( 'widget_text', array( &$this, 'rm_widget_text_filter' ) );

			if ( defined( 'DOING_AJAX' ) && true === DOING_AJAX ) {
				add_action( 'wp_ajax_rainmaker_validate_form', array( &$this, 'rainmaker_validate_form' ) );
				add_action( 'wp_ajax_nopriv_rainmaker_validate_form', array( &$this, 'rainmaker_validate_form' ) );
			}

			if ( defined( 'DOING_AJAX' ) && true === DOING_AJAX ) {
				add_action( 'wp_ajax_rm_rainmaker_add_lead', array( &$this, 'rm_rainmaker_add_lead' ) );
				add_action( 'wp_ajax_nopriv_rm_rainmaker_add_lead', array( &$this, 'rm_rainmaker_add_lead' ) );
			}
			if ( is_admin() ) {

				$ig_rm_tracker = 'IG_Tracker_V_' . str_replace( '.', '_', IG_RM_TRACKER_VERSION );

				$this->include_files();

				// We need $ig_em_tracker in config.php file.
				// So, load all feedback class before this inclusion
				require_once( 'mailers/config.php' );

				$ig_feedback_class = 'IG_Feedback_V_' . str_replace( '.', '_', $feedback_version );
				$ig_rm_feedback    = new $ig_feedback_class( 'Rainmaker', 'icegram-rainmaker', 'ig_rm', 'rmfree.', false );

				$ig_rm_feedback->render_deactivate_feedback();

				// --- Icegram Plugin Usage Tracker ---
				$plugin_usage_tracker_class = 'IG_Plugin_Usage_Tracker_V_' . str_replace( '.', '_', IG_RM_PLUGIN_USAGE_TRACKER_VERSION );
				
				if ( class_exists($plugin_usage_tracker_class) ) {
					$name               = 'Icegram Collect';
					$text_domain        = 'icegram-rainmaker';
					$plugin_abbr        = 'ig_rm';
					$product_id         = IG_RM_PRODUCT_ID;
					$plugin_plan        = self::get_plan();
					$plugin_file_path   = IG_RM_PLUGIN_DIR . '/icegram-rainmaker.php'; //'icegram-rainmaker-premium.php'
					$allowed_by_default = ( 'lite' === $plugin_plan ) ? false : true;
				
					new $plugin_usage_tracker_class( $name, $text_domain, $plugin_abbr, $product_id, $plugin_plan, $plugin_file_path, $ig_rm_tracker, $allowed_by_default );
				}

			}


			add_action( 'admin_notices', array( &$this, 'rm_add_admin_notices' ) );
			add_action( 'admin_init', array( &$this, 'rm_dismiss_admin_notice' ) );
			add_action( 'add_meta_boxes', array( &$this, 'add_metaboxes' ) );
			add_action( 'wp_ajax_ig_rm_klawoo_subscribe', array( &$this, 'klawoo_subscribe' ) );
		}

		function include_files() {

			$classes = glob( $this->plugin_path . '/feedback/*.php' );
			foreach ( $classes as $file ) {
				// Files with 'admin' in their name are included only for admin section
				if ( is_file( $file ) && is_admin() ) {
					include_once $file;
				}
			}

			include_once $this->plugin_path . '/feedback.php';
		}

		function rm_custom_search_query( $query ) {

			if ( is_admin() && ( ! empty( $query->query['post_type'] ) && $query->query['post_type'] === 'rainmaker_lead' ) && $query->is_search() ) {
				$query->set( 'meta_query', array(
					'relation' => 'OR',
					array(
						'key'     => 'email',
						'value'   => $query->query_vars['s'],
						'compare' => 'LIKE'
					),
					array(
						'key'     => 'name',
						'value'   => $query->query_vars['s'],
						'compare' => 'LIKE'
					)

				) );
			}

			return $query;

		}

		function rm_custom_search_query_string( $search, $wp_query ) {
			global $wpdb;
			if ( is_admin() && ! empty( $wp_query->query['post_type'] ) && $wp_query->query['post_type'] === 'rainmaker_lead' && $wp_query->is_search ) {
				$search = " AND ( 
							  ( $wpdb->postmeta.meta_key = 'email' AND $wpdb->postmeta.meta_value LIKE '%" . $wp_query->query['s'] . "%' ) 
							  OR 
							  ( $wpdb->postmeta.meta_key = 'name' AND $wpdb->postmeta.meta_value LIKE '%" . $wp_query->query['s'] . "%' )
							) AND $wpdb->posts.post_type = 'rainmaker_lead' ";

			}

			return $search;
		}

		function welcome() {
			if ( false === get_option( '_icegram_rm_activation_redirect' ) ) {
				return;
			}
			// Delete the redirect transient
			delete_option( '_icegram_rm_activation_redirect' );
			$this->import_sample_data();
			wp_safe_redirect( admin_url( 'edit.php?post_type=rainmaker_form' ) );
			exit;
		}

		public function admin_menus() {
			$menu_title            = __( 'Docs & Support', 'icegram-rainmaker' );
			$about                 = add_submenu_page( 'edit.php?post_type=rainmaker_form', $menu_title, $menu_title, 'manage_options', 'icegram-rainmaker-support', array( $this, 'about_screen' ) );
			if( ! self::is_max() ) {
				$rm_upgrade_page_title = '<span style="color:#f18500;font-weight:bolder;">' . __( '🔥 Go Max', 'icegram-rainmaker' ) . '</span>';
				$upgrade               = add_submenu_page( 'edit.php?post_type=rainmaker_form', $rm_upgrade_page_title, $rm_upgrade_page_title, 'manage_options', 'icegram-rainmaker-upgrade', array( $this, 'rm_upgrade_screen' ) );
			}
		}

		public function about_screen() {
			include( 'about-icegram-rainmaker.php' );
		}

		public function rm_upgrade_screen() {
			include ( 'rm-pricing-page.php' );
		}

		public function rm_add_admin_notices() {
			$screen = get_current_screen();

			if ( ! in_array( $screen->id, array( 'edit-rainmaker_form', 'rainmaker_form', 'edit-rainmaker_lead', 'rainmaker_form_page_icegram-rainmaker-support', 'rainmaker_form_page_icegram-rainmaker-upgrade' ), true ) ) {
				return;
			}

			include_once( 'rm-offer.php' );

			if ( ! $this->is_premium_installed() ) {
				include_once( 'rm-pro-features.php' );
			}
		}

		/**
		 * Add metaboxes for upsale
		 *
		 * @since 1.2.8
		 */
		public function add_metaboxes() {
			if ( ! $this->is_premium_installed() ) {
				// Add upsale metabox only if there isn't any other ongoing sale period.
				if ( ! self::is_offer_period( 'bfcm' ) ) {
					add_meta_box( 'rm_upsale_premium_metabox', __( 'Upgrade to Icegram Collect', 'icegram-rainmaker' ), array( &$this,'rm_upsale_premium'), 'rainmaker_form', 'side', 'default' ); 
				}
			}
		}

		public function rm_upsale_premium() {
				$pricing_url = "https://www.icegram.com/rainmaker-pricing-table/?utm_source=in_app&utm_medium=rm_upgrade_notice&utm_campaign=rm_upsell";

				echo "<div style='font-size:14px'><p class='ig_message_upsale'>Get more features and integration options with <a style='font-weight:500;' href='" . $pricing_url . "' target='_blank' >Icegram Collect </a>!</p>Upgrade now & get <b>10% discount!</b><br/><br/>Use code <b class='ig_upsale_premium_code'>PREMIUM10</b></div>";
		}

		public function rm_dismiss_admin_notice() {
			if ( isset( $_GET['rm_dismiss_admin_notice'] ) && $_GET['rm_dismiss_admin_notice'] == '1' && isset( $_GET['rm_option_name'] ) ) {
				$option_name = sanitize_text_field( $_GET['rm_option_name'] );
				update_option( $option_name . '_icegram', 'yes', false );

				if ( 'rm_offer_bfcm_2024' === $option_name ) {
					$url = "https://www.icegram.com/rainmaker-pricing-table/?utm_source=in_app&utm_medium=rm_banner&utm_campaign=offer_bfcm_2024";
					header( "Location: {$url}" );
					exit();
				} else {
					$referer = wp_get_referer();
					wp_safe_redirect( $referer );
					exit();
				}
			}
		}

		public function klawoo_subscribe_form() {
			?>
            <div class="wrap">

                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e( 'For more help and tips...', 'icegram-rainmaker' ) ?></th>
                        <td>
                            <form name="klawoo_subscribe" action="#" method="POST" accept-charset="utf-8">
                                <input class="ltr" type="text" name="name" id="name" placeholder="Name"/>
                                <input class="regular-text ltr" type="text" name="email" id="email" placeholder="Email"/>
                                <input type="hidden" name="list" value="oTUKZ763WPjgZ9892LDNXKfsLA"/>
                                <input type="submit" name="submit" id="submit" class="button button-primary" value="Subscribe">
                                <br><br>
                                <input type="checkbox" name="es-gdpr-agree" id="es-gdpr-agree" value="1" required="required">
                                <label for="es-gdpr-agree"><?php echo sprintf( __( 'I have read and agreed to our %s.', 'icegram' ), '<a href="https://www.icegram.com/privacy-policy/" target="_blank">' . __( 'Privacy Policy', 'icegram' ) . '</a>' ); ?></label>
                                <br>
                            </form>
                            <div id="klawoo_response"></div>
                        </td>
                    </tr>
                </table>
            </div>
            <script type="text/javascript">
				jQuery(function () {
					jQuery("form[name=klawoo_subscribe]").submit(function (e) {
						e.preventDefault();

						jQuery('#klawoo_response').html('');
						params = jQuery("form[name=klawoo_subscribe]").serializeArray();
						params.push({name: 'action', value: 'ig_rm_klawoo_subscribe'});

						jQuery.ajax({
							method: 'POST',
							type: 'text',
							url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
							data: params,
							success: function (response) {
								if (response != '') {
									jQuery('#klawoo_response').html(response);
								} else {
									jQuery('#klawoo_response').html('error!');
								}
							}
						});
					});
				});
            </script>
			<?php
		}

		public function klawoo_subscribe() {
			$url = 'http://app.klawoo.com/subscribe';

			if ( ! empty( $_POST ) ) {
				$params = $_POST;
			} else {
				exit();
			}
			$method = 'POST';
			$qs     = http_build_query( $params );

			$options = array(
				'timeout' => 15,
				'method'  => $method
			);

			if ( $method == 'POST' ) {
				$options['body'] = $qs;
			} else {
				if ( strpos( $url, '?' ) !== false ) {
					$url .= '&' . $qs;
				} else {
					$url .= '?' . $qs;
				}
			}

			$response = wp_remote_request( $url, $options );
			if ( wp_remote_retrieve_response_code( $response ) == 200 ) {
				$data = $response['body'];
				if ( $data != 'error' ) {

					$message_start = substr( $data, strpos( $data, '<body>' ) + 6 );
					$remove        = substr( $message_start, strpos( $message_start, '</body>' ) );
					$message       = trim( str_replace( $remove, '', $message_start ) );
					echo( $message );
					exit();
				}
			}
			exit();
		}


		public function rainmaker_clean_lead_data( $lead_data ) {
			if ( ! empty( $lead_data ) ) {
				// cleanup request Data
				unset( $lead_data['action'] );
				unset( $lead_data['is_remote'] );
				unset( $lead_data['ig_is_remote'] );
				unset( $lead_data['rm_nonce_field'] );
				unset( $lead_data['rm_form-id'] );
				unset( $lead_data['added'] );
			}

			return $lead_data;

		}

		public function rainmaker_validate_request( $request ) {
			return $request;
		}

		// TODO :: for Test
		// Formate madmimi leadata according to Rainmaker Lead
		public function madmimi_prepare_lead( $lead_data, $rm_form_settings ) {
			if ( ! empty( $lead_data['signup'] ) ) {
				$lead_data['rm_lead_email'] = ! empty( $lead_data['signup']['email'] ) ? $lead_data['signup']['email'] : '';
				$lead_data['rm_lead_name']  = ! empty( $lead_data['signup']['name'] ) ? $lead_data['signup']['name'] : '';
			}

			return $lead_data;
		}

		// Formate mailpoet leadata according to Rainmaker Lead
		public function maipoet_prepare_lead( $lead_data, $rm_form_settings ) {
			if ( ! empty( $lead_data['wysija'] ) && ! empty( $lead_data['wysija']['user'] ) ) {
				$lead_data['rm_lead_email'] = ! empty( $lead_data['wysija']['user']['email'] ) ? $lead_data['wysija']['user']['email'] : '';
				$lead_data['rm_lead_name']  = ! empty( $lead_data['wysija']['user']['firstname'] ) ? $lead_data['wysija']['user']['firstname'] : '';
			}

			return $lead_data;
		}

		public function rainmaker_prepare_lead( $lead_data, $rm_form_settings ) {

			if ( ! empty( $lead_data ) ) {

				//Email Field
				if ( empty( $lead_data['rm_lead_email'] ) ) {
					$email = array();
					if ( ! empty( $lead_data['email'] ) ) {
						if ( filter_var( $lead_data['email'], FILTER_VALIDATE_EMAIL ) ) {
							$email[] = $lead_data['email'];
						}
					} else {
						foreach ( $lead_data as $key => $value ) {
							if ( filter_var( $lead_data[ $key ], FILTER_VALIDATE_EMAIL ) ) {
								$email[] = $lead_data[ $key ];
							}
						}
					}


					//if Email field is empty or invalid then return, when form type='subscription'
					if ( empty( $email ) && $rm_form_settings['type'] == 'subscription' ) {
						return array();
					}

					$lead_data['rm_lead_email'] = ! empty( $email ) ? array_shift( $email ) : '';
				}

				//Name Field
				if ( empty( $lead_data['rm_lead_name'] ) ) {
					$name      = array();
					$name_keys = array( 'name', 'your-name', 'first-name', 'fname', 'firstname' );
					foreach ( $name_keys as $key ) {
						if ( isset( $lead_data[ $key ] ) ) {
							$name[] = $lead_data[ $key ];
						}
					}
					$lead_data['rm_lead_name'] = ! empty( $name ) ? array_shift( $name ) : '';
				}
			}

			return $lead_data;
		}

		public static function rainmaker_validate_form() {
			$lead_data        = $_REQUEST;
			$response         = array();
			$form_id          = $lead_data['rm_form-id'];
			$rm_form_settings = get_post_meta( $form_id, 'rm_form_settings', true );

			$response = apply_filters( 'rainmaker_validate_form', $response, $lead_data, $rm_form_settings );
			echo json_encode( $response );
			exit;
			// return $response;
		}

		public static function rm_rainmaker_add_lead( $lead_data ) {

			$lead_data = ( empty( $lead_data ) ) ? $_REQUEST : $lead_data;

			//remove prefix from data
			if ( ! empty( $_REQUEST['rmfpx_added'] ) ) {
				$lead_data = array();
				foreach ( $_REQUEST as $key => $value ) {
					$new_key = explode( 'rmfpx_', $key );
					if ( ! empty( $new_key[1] ) ) {
						$lead_data[ $new_key[1] ] = $value;
					}
				}
			}

			$lead_data = apply_filters( 'rainmaker_validate_request', $lead_data );

			if( isset( $lead_data['is_remote'] ) || isset( $lead_data['ig_is_remote'] ) ) {
				if ( $lead_data['is_remote'] == true || $lead_data['ig_is_remote'] == true ) {
					$http_origin = isset( $_SERVER['HTTP_ORIGIN'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_ORIGIN'] ) ) : '';
					if( !empty($http_origin) ){
						header( 'Access-Control-Allow-Origin: ' . $http_origin );
					}
				}
			}

			if ( empty( $lead_data['is_remote'] ) && empty( $lead_data['ig_is_remote'] ) &&

			     ( empty( $lead_data['rm_nonce_field'] ) || ! wp_verify_nonce( $lead_data['rm_nonce_field'], 'rm_form_submission' ) ) ) {
				wp_die( 'Authentication failed', 'Invalid Submission', array( 'response' => 500 ) );
			}
			$response = array( 'error' => '' );

			if ( empty( $lead_data ) ) {
				$response['error'] = __( 'No lead Data', 'icegram-rainmaker' );
				echo json_encode( $response );
				exit;
			}

			if ( empty( $lead_data['rm_form-id'] ) ) {
				$response['error'] = __( 'Invalid Rainmaker form', 'icegram-rainmaker' );
				echo json_encode( $response );
				exit;
			}
			$form_id                     = $lead_data['rm_form-id'];
			$rm_form_settings            = get_post_meta( $form_id, 'rm_form_settings', true );
			$rm_form_settings['form_id'] = $form_id;

			if ( ! empty( $response['error'] ) ) {
				echo json_encode( $response );
				exit;
			}

			//TODO:: honey-pot validation can be added in the filter-rainmaker_validate_form
			if ( ! empty( $lead_data['rm_required_field'] ) ) {
				$response['success'] = __( 'Submission Successful', 'icegram-rainmaker' );
				echo json_encode( $response );
				exit;
			}
			//Clean data before processig it
			$lead_data = apply_filters( 'rainmaker_clean_lead_data', $lead_data );

			// Process Data
			$lead = apply_filters( 'rainmaker_prepare_lead', $lead_data, $rm_form_settings );

			if ( empty( $lead ) ) {
				if ( empty( $lead['email'] ) ) {
					$response['error'] = __( 'Invalid Email', 'icegram-rainmaker' );
				} else {
					$response['error'] = __( 'No Lead Data', 'icegram-rainmaker' );
				}
				echo json_encode( $response );
				exit;
			}

			// add leads to database
			$args        = array(
				'post_content' => '',
				'post_name'    => '',
				'post_title'   => '',
				'post_status'  => 'publish',
				'post_type'    => 'rainmaker_lead'
			);
			$new_lead_id = wp_insert_post( $args );
			//TODO :: Adding  default values for lead, only for rainmaker lead.
			// This can be done with and additional filter
			$client_ip = ! empty( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : 'unknown';
			$email     = ! empty( $lead['rm_lead_email'] ) ? $lead['rm_lead_email'] : 'unknown@' . $client_ip;
			$name      = ! empty( $lead['rm_lead_name'] ) ? $lead['rm_lead_name'] : 'unknown:user';

			update_post_meta( $new_lead_id, 'email', sanitize_email( $email ) );
			update_post_meta( $new_lead_id, 'name', sanitize_text_field( $name ) );
			update_post_meta( $new_lead_id, 'rm_form_id', $form_id );
			update_post_meta( $new_lead_id, 'rm_raw_data', serialize( $lead_data ) );

			//filter lead data
			//TODO:: check with mailpoet form + mailchimp intergration - notworking
			$lead = apply_filters( 'rainmaker_filter_lead', $lead );

			// post leads
			do_action( 'rainmaker_post_lead', $lead, $rm_form_settings );

			//TODO :: Success mesage
			$response['success']         = __( 'Lead added successfully', 'icegram-rainmaker' );
			$response['redirection_url'] = ( ! empty( $rm_form_settings['rm_enable_redirection'] ) && $rm_form_settings['rm_enable_redirection'] == 'yes' && ! empty( $rm_form_settings['redirection_url'] ) ) ? $rm_form_settings['redirection_url'] : '';
			echo json_encode( $response );
			exit;

		}

		function enqueue_admin_styles_and_scripts() {
			$screen = get_current_screen();
			if ( ! in_array( $screen->id, array( 'edit-rainmaker_form', 'rainmaker_form', 'edit-rainmaker_lead', 'rainmaker_form_page_icegram-rainmaker-support', 'rainmaker_form_page_icegram-rainmaker-upgrade' ), true ) ) {
				return;
			}
			wp_register_script( 'rainmaker_tiptip', $this->plugin_url . '../assets/js/jquery.tipTip.min.js', array( 'jquery' ), $this->version );
			wp_enqueue_script( 'rainmaker_tiptip' );
			wp_register_script( 'rainmaker_admin', $this->plugin_url . '../assets/js/admin.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-tabs', 'rainmaker_tiptip' ), $this->version );
			wp_enqueue_script( 'rainmaker_admin' );
			wp_localize_script('rainmaker_admin', 'rainmaker_admin_data', array(
				'nonce'    => wp_create_nonce('rainmaker_admin_ajax_nonce')
			));
			wp_enqueue_style( 'rainmaker_admin_styles', $this->plugin_url . '../assets/css/admin.css', array(), $this->version );
		}

		//enqueue frontend sctipt
		function enqueue_frontend_styles_and_scripts() {
			wp_register_script( 'rm_main_js', $this->plugin_url . '../assets/js/main.js', array( 'jquery' ), $this->version );
			$rm_pre_data['ajax_url']       = admin_url( 'admin-ajax.php' );
			$rm_pre_data['rm_nonce_field'] = wp_create_nonce( "rm_form_submission" );

			if ( ! wp_script_is( 'rm_main_js' ) ) {
				wp_enqueue_script( 'rm_main_js' );
			}
			
			if ( wp_script_is( 'rm_main_js', 'registered' ) ) {
				wp_localize_script( 'rm_main_js', 'rm_pre_data', $rm_pre_data );
			}

			//wp_enqueue_style( 'rainmaker_form_style', $this->plugin_url . '../assets/css/form.css', array(), $this->version );
		}

		//form submmision js
		function register_rainmaker_form_post_type() {
			$labels = array(
				'name'               => __( 'Icegram Collect', 'icegram-rainmaker' ),
				'singular_name'      => __( 'Form', 'icegram-rainmaker' ),
				'add_new'            => __( 'Create New', 'icegram-rainmaker' ),
				'add_new_item'       => __( 'Create New Form', 'icegram-rainmaker' ),
				'edit_item'          => __( 'Edit Form', 'icegram-rainmaker' ),
				'new_item'           => __( 'New Form', 'icegram-rainmaker' ),
				'all_items'          => __( 'Forms', 'icegram-rainmaker' ),
				'view_item'          => __( 'View Form', 'icegram-rainmaker' ),
				'search_items'       => __( 'Search Forms', 'icegram-rainmaker' ),
				'not_found'          => __( 'No Forms found', 'icegram-rainmaker' ),
				'not_found_in_trash' => __( 'No Forms found in Trash', 'icegram-rainmaker' ),
				'parent_item_colon'  => __( '', 'icegram-rainmaker' ),
				'menu_name'          => __( 'Icegram Collect', 'icegram-rainmaker' )
			);
			$args   = array(
				'labels'             => $labels,
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'rewrite'            => array( 'slug' => 'rainmaker_form' ),
				'capability_type'    => 'post',
				'has_archive'        => false,
				'hierarchical'       => false,
				'menu_position'      => null,
				'menu_icon'          => $this->plugin_url . '../assets/images/rm_logo_18.png',
				'supports'           => array( 'title' )
			);

			register_post_type( 'rainmaker_form', $args );
		}

		// Register lead post type
		function register_lead_post_type() {
			$labels = array(
				'name'               => __( 'Leads', 'icegram-rainmaker' ),
				'singular_name'      => __( 'Lead', 'icegram-rainmaker' ),
				'add_new'            => __( 'Create New', 'icegram-rainmaker' ),
				'add_new_item'       => __( 'Create New Lead', 'icegram-rainmaker' ),
				'edit_item'          => __( 'Edit Lead', 'icegram-rainmaker' ),
				'new_item'           => __( 'New Lead', 'icegram-rainmaker' ),
				'all_items'          => __( 'Leads', 'icegram-rainmaker' ),
				'view_item'          => __( 'View Lead', 'icegram-rainmaker' ),
				'search_items'       => __( 'Search Lead', 'icegram-rainmaker' ),
				'not_found'          => __( 'No lead found', 'icegram-rainmaker' ),
				'not_found_in_trash' => __( 'No lead found in Trash', 'icegram-rainmaker' ),
				'parent_item_colon'  => __( '', 'icegram-rainmaker' ),
				'menu_name'          => __( 'Leads', 'icegram-rainmaker' )
			);

			$args = array(
				'labels'             => $labels,
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_menu'       => 'edit.php?post_type=rainmaker_form',
				'query_var'          => true,
				'rewrite'            => array( 'slug' => 'rainmaker_lead' ),
				'capability_type'    => 'post',
				'capabilities'       => array( 'create_posts' => false ),
				'map_meta_cap'       => true,
				'has_archive'        => false,
				'hierarchical'       => false,
				'menu_position'      => null,
				'supports'           => array( '' )
			);

			register_post_type( 'rainmaker_lead', $args );
		}

		/* import Icegram forms and save them as rainmaker post type */
		function import_ig_forms() {

			if ( get_option( 'ig_forms_imported' ) ) {
				return;
			}

			$active_plugins = get_option( 'active_plugins', array() );
			$icegram_plugin_slug = array( 'icegram/icegram.php', 'icegram-engage/icegram-engage.php');
			if ( ! empty( array_intersect( $icegram_plugin_slug, $active_plugins ) ) ) {
				$args  = array(
					'post_type'   => 'ig_message',
					'post_status' => 'publish',
					'numberposts' => - 1
				);
				$posts = get_posts( $args );

				if ( ! empty( $posts ) && is_array( $posts ) ) {
					$rm_data   = array();
					$ig_rm_map = array();

					foreach ( $posts as $post ) {

						$ig_msg = get_post_meta( $post->ID, 'icegram_message_data', true );
						if ( ! empty( $ig_msg['form_html_original'] ) ) {
							if ( preg_match( '/rainmaker_form/i', $ig_msg['form_html_original'] ) ) {
								//get the ID from the Rainmaker shortcode
								$sc_part = explode( '"', $ig_msg['form_html_original'] );
								if ( ! empty( $sc_part[1] ) && is_numeric( $sc_part[1] ) ) {
									if ( ! in_array( $ig_msg['form_html_original'], $rm_data ) ) {
										$rm_data[ $sc_part[1] ] = $ig_msg['form_html_original'];
									}
									$ig_rm_map[ $post->ID ] = $sc_part[1];
								}
							} else {
								if ( ! in_array( $ig_msg['form_html_original'], $rm_data ) ) {
									$post_title = ! empty( $post->post_title ) ? $post->post_title : 'Form';
									$rm_args    = array(
										'post_name'   => '',
										'post_title'  => 'IG-' . $post_title . '-' . $post->ID,
										'post_type'   => 'rainmaker_form',
										'post_status' => 'publish'
									);

									$form_id = wp_insert_post( $rm_args );
									if ( ! empty( $form_id ) ) {
										$meta_values = array(
											'type'             => 'custom',
											'form_code'        => $ig_msg['form_html_original'],
											'form_style'       => 'rm-form-style0',
											'rm_list_provider' => 'rainmaker',
											'success_message'  => '',
										);
										update_post_meta( $form_id, 'rm_form_settings', $meta_values );
										$ig_rm_map[ $post->ID ] = $form_id;
										$rm_data[ $form_id ]    = $ig_msg['form_html_original'];
									}
								} else {
									$ig_rm_map[ $post->ID ] = array_search( $ig_msg['form_html_original'], $rm_data );
								}
							}
						}
					} // post loop

					// Add Rainmaker form ids to Icegram messages
					if ( ! empty( $ig_rm_map ) ) {
						foreach ( $ig_rm_map as $msg_id => $rm_id ) {
							$rm_form = get_post( $rm_id );
							if ( $rm_form && $rm_form->post_status == 'publish' ) {
								$ig_msg                        = get_post_meta( $msg_id, 'icegram_message_data', true );
								$ig_msg['rainmaker_form_code'] = $rm_id;
								update_post_meta( $msg_id, 'icegram_message_data', $ig_msg );
							}
						}
					}
					?>
                    <div id="message" class="updated notice notice-success"><p><?php echo count( $rm_data ) . __( ' Forms are imported from Icegram messages to Icegram Collect', 'icegram-rainmaker' ) ?></p></div>
					<?php

				}
				update_option( 'ig_forms_imported', true );
			}

		}

		function import_sample_data() {
			if ( get_option( 'rainmaker_sample_form_imported' ) ) {
				return;
			}
			$args = array(
				'post_name'   => '',
				'post_title'  => 'My First Form',
				'post_type'   => 'rainmaker_form',
				'post_status' => 'draft'
			);

			$new_form_id = wp_insert_post( $args );
			if ( ! empty( $new_form_id ) ) {
				$meta_values = array(
					'type'   => 'subscription',
					'fileds' => array(
						array(
							'show'       => 'yes',
							'label'      => 'Name',
							'input_type' => 'text',
							'field_type' => 'name',
						),

						array(
							'show'       => 'yes',
							'label'      => 'Email',
							'input_type' => 'text',
							'field_type' => 'email',
						),

						array(
							'show'       => 'yes',
							'label'      => 'Submit',
							'input_type' => 'submit',
							'field_type' => 'button',
						)

					),

					'form_style'       => 'rm-form-style2',
					'rm_list_provider' => 'rainmaker',
					'success_message'  => '',
				);
				update_post_meta( $new_form_id, 'rm_form_settings', $meta_values );

			}
			update_option( 'rainmaker_sample_form_imported', true );
		}

		function remove_rm_form_action( $actions, $post ) {
			if ( $post->post_type != 'rainmaker_lead' ) {
				return $actions;
			}
			$actions = array();

			return $actions;

		}

		//remove_lead_bulk_action
		function remove_lead_bulk_action( $actions ) {
			unset( $actions['edit'] );

			return $actions;
		}

		// Add lead columns to lead dashboard
		function edit_form_columns( $existing_columns ) {
			$date = $existing_columns['date'];
			unset( $existing_columns['date'] );
			$existing_columns['shortcode'] = __( 'Shortcode', 'icegram-rainmaker' );
			$existing_columns['date']      = $date;

			return $existing_columns;
		}

		// Add lead columns data to lead dashboard
		function custom_form_columns( $column ) {
			global $post;

			if ( ( is_object( $post ) && $post->post_type != 'rainmaker_form' ) ) {
				return;
			}
			if ( $column === 'shortcode' ) {
				echo '<code>[rainmaker_form id="' . $post->ID . '"]</code>';
			}
			// switch ( $column ) {
			// 	case 'shortcode':
			// 		echo '<code>[rainmaker_form id="' . $post->ID . '"]</code>';
			// 		break;
			// }

		}

		// Add lead columns to lead dashboard
		function edit_lead_columns( $existing_columns ) {
			$date = $existing_columns['date'];
			unset( $existing_columns['date'] );
			unset( $existing_columns['title'] );

			$existing_columns['lead_email']   = __( 'Email', 'icegram-rainmaker' );
			$existing_columns['lead_name']    = __( 'Name', 'icegram-rainmaker' );
			$existing_columns['lead_subject'] = __( 'Subject', 'icegram-rainmaker' );
			$existing_columns['lead_message'] = __( 'Message', 'icegram-rainmaker' );
			$existing_columns['lead_date']    = __( 'Submission Date', 'icegram-rainmaker' );

			// $existing_columns['date'] 		= $date;
			return $existing_columns;
		}

		// Add lead columns data to lead dashboard
		function custom_lead_columns( $column ) {
			global $post;
			if ( ( is_object( $post ) && $post->post_type != 'rainmaker_lead' ) ) {
				return;
			}

			$rm_raw_data = maybe_unserialize( get_post_meta( $post->ID, 'rm_raw_data', true ) );
			switch ( $column ) {
				case 'lead_email':
					$email = get_post_meta( $post->ID, 'email', true );
					echo esc_attr( $email );
					break;

				case 'lead_name':
					$name = get_post_meta( $post->ID, 'name', true );
					$name = ( ! empty( $name ) ) ? $name : '-';
					echo esc_attr( $name );
					break;

				case 'lead_subject':
					$subject = ( ! empty( $rm_raw_data['subject'] ) ) ? $rm_raw_data['subject'] : '-';
					echo esc_attr( $subject );
					break;

				case 'lead_message':
					$message = ( ! empty( $rm_raw_data['message'] ) ) ? $rm_raw_data['message'] : '-';
					echo esc_attr( $message );
					break;

				case 'lead_date':
					$date_format = get_option( 'date_format' );
					echo date_format( date_create( $post->post_date ), $date_format );
					break;
				default :
					break;
			}

		}

		//sort custom column
		function sort_lead_columns( $columns ) {
			$columns['lead_email'] = 'lead_email';
			$columns['lead_name']  = 'lead_name';
			$columns['lead_date']  = 'lead_date';

			return $columns;
		}

		//Add HTML before FORM tag
		function rainmaker_before_form( $form_html, $rm_form_settings, $form_id ) {
			if ( ! empty( $form_html ) ) {
				$form_html .= '<div id="rm_form_error_message_' . esc_attr( $form_id ) . '" class="rm_form_error_message" style="display:none"></div>';

			}

			return $form_html;
		}

		//Add HTML after FORM tag
		function rainmaker_after_form( $form_html, $rm_form_settings, $form_id ) {
			if ( ! empty( $form_html ) ) {
				$form_html .= '<div class="rm-loader"></div>';
			}

			return $form_html;
		}

		//execute shortcode
		function execute_shortcode( $atts = array() ) {
			ob_start();
			
			$allowedtags = self::ig_rm_allowed_html_tags_in_esc();
			add_filter( 'safe_style_css', array( $this, 'ig_rm_allowed_css_style') );

			$active_plugins = get_option( 'active_plugins', array() );
			if ( is_multisite() ) {
				$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
			}

			$html = '';
			if ( get_post_status( $atts['id'] ) !== 'publish' ) {
				return $html;
			}

			// wp_enqueue_script( 'rm_main_js', $this->plugin_url . '../assets/js/main.js', array( 'jquery' ), $this->version );
			wp_enqueue_style( 'rainmaker_form_style', $this->plugin_url . '../assets/css/form.css', array(), $this->version );

			$rm_form_settings = get_post_meta( $atts['id'], 'rm_form_settings', true );
			if ( ! empty( $rm_form_settings['rm_list_provider'] ) ) {
				$include_path = 'mailers/' . $rm_form_settings['rm_list_provider'] . '.php';
				if ( ! file_exists( $include_path ) ) {
					if ( file_exists( $this->plugin_path . '/../../pro/' . $include_path ) ) {
						$include_path = $this->plugin_path . '/../../pro/' . $include_path;
					} elseif ( file_exists( $this->plugin_path . '/../../max/' . $include_path ) ) {
						$include_path = $this->plugin_path . '/../../max/' . $include_path;
					}
				}
				if ( file_exists( $include_path ) ) {
					require_once( $include_path );
				}
			}

			$form_html     = '';
			$response_text = $rm_form_settings['success_message'];
			$rm_form_id    = "rainmaker_form_" . $atts['id'];

			if ( $rm_form_settings['type'] == 'custom' ) {
				$form_html = do_shortcode( $rm_form_settings['form_code'] );
			} else {
				$form_type_data = array();
				if ( ! empty( $rm_form_settings['contact_fields'] ) && $rm_form_settings['type'] == 'contact' ) {
					$form_type_data = $rm_form_settings['contact_fields'];
				} elseif ( ! empty( $rm_form_settings['fileds'] ) && $rm_form_settings['type'] == 'subscription' ) {
					$form_type_data = $rm_form_settings['fileds'];
				}

				$is_name_present    = false;
				$is_email_present   = false;
				$is_subject_present = false;
				$is_message_present = false;
				$form_layout        = ( ! empty( $rm_form_settings['rm_compact_layout'] ) ) ? $rm_form_settings['rm_compact_layout'] : '';
				if ( 'yes' === $form_layout ) {
					$is_name_present    = ( isset( $form_type_data['name']['show'] ) && 'yes' === $form_type_data['name']['show'] ) ? true : false;
					$is_email_present   = ( isset( $form_type_data['email']['show'] ) && 'yes' === $form_type_data['email']['show'] ) ? true : false;
					$is_subject_present = ( isset( $form_type_data['subject']['show'] ) && 'yes' === $form_type_data['subject']['show'] ) ? true : false;
					$is_message_present = ( isset( $form_type_data['msg']['show'] ) && 'yes' === $form_type_data['msg']['show'] ) ? true : false;
				}
				/*foreach ($form_type_data as  $field) { */
				foreach ( $form_type_data as $key => $field ) {
					if ( empty( $field['show'] ) ) {
						continue;
					}

					$additional_class = '';
					if ( in_array( $key, array( 'email', 'name' ) ) && $is_name_present && ( $is_subject_present || $is_message_present ) ) {
						$additional_class = 'rm_form_el_one_half';
					} elseif ( in_array( $key, array( 'email', 'name', 'subject', 'message' ) ) && $is_email_present && ! $is_name_present && ( ! $is_subject_present && ! $is_message_present ) ) {
						$additional_class = 'rm_form_el_two_third';
					} elseif ( in_array( $key, array( 'email', 'name', 'subject', 'message' ) ) && $is_name_present && ! $is_subject_present && ! $is_message_present ) {
						$additional_class = 'rm_form_el_one_third';
					} elseif ( in_array( $key, array( 'email', 'name', 'subject', 'message' ) ) && $is_name_present && ( $is_subject_present || $is_message_present ) ) {
						$additional_class = 'rm_form_el_full';
					}

					if ( ! empty( $form_type_data['gdpr']['rm_enable_gdpr'] ) && 'yes' === $form_type_data['gdpr']['rm_enable_gdpr'] && $field['field_type'] == 'button' ) {
						$form_html .= '<div class="rm_form_el_set rm_form_el_gdpr">
							<input type="checkbox" class="rm_form_gdpr_checkbox" name="rm_gdpr_consent" value="true" required><label class="rm-form-gdpr">' . wp_kses_post( $form_type_data["gdpr"]["gdpr_content"] ) . '</label></div>';
					} elseif ( in_array( 'gdpr/gdpr.php', $active_plugins ) && $field['field_type'] == 'button' ) {
						$form_html .= GDPR::get_consent_checkboxes();
					}
					$attr      = 'required placeholder="' . esc_attr( trim( $field['label'] ) ) . '"';
					$class     = "rm_form_field";
					$form_html .= '<div class="rm_form_el_set rm_form_el_' . $field['field_type'] . ' ' . $additional_class . '">';
					$label     = '<label class="rm_form_label" >' . $field['label'] . '</label>';

					if ( $field['field_type'] == 'button' ) {
						$label = '';
						$attr  = ' value="' . esc_attr( $field['label'] ) . '"';
						$class .= " rm_button";

					}
					$form_html .= $label;
					if ( $field['input_type'] == "textarea" && $field['field_type'] == "message" ) {
						$form_html .= '<textarea rows="3" maxlength="500" autocomplete="off" cols="65" class="' . $class . '" type="' . $field['input_type'] . '" name="' . $field['field_type'] . '"  ' . $attr . '></textarea></div>';
					} else {
						$form_html .= '<input class="' . $class . '" type="' . $field['input_type'] . '" name="' . $field['field_type'] . '"  ' . $attr . '/></div>';
					}
				}


				$form_html = ! empty( $form_html ) ? '<form action="' . esc_url( add_query_arg( array() ) . '#' . $rm_form_id ) . '">' . $form_html . '</form>' : '';
			}

			if ( empty( $response_text ) ) {
				$response_text = __( 'Thank you!', 'icegram-rainmaker' );
			}

			if ( ! empty( $form_html ) ) {

				//Add Style, if form is added Remote site
				//TODO:: check this with lazy loading enable Icegram, below condition is truthy
				if ( ! empty( $_SERVER['HTTP_ORIGIN'] ) && site_url() !== $_SERVER['HTTP_ORIGIN'] ) {
					$html    .= '<style id="rm_style">';
					$content = file_get_contents( dirname( __FILE__ ) . '/../assets/css/form.css' );
					$html    .= ( ! empty( $content ) ) ? $content : '';
					$html    .= '</style>';
				}
				//Append Custom style in HTML
				if ( ! empty( $rm_form_settings['form_css'] ) ) {
					$html .= '<style id="rm_custom_style_' . $atts['id'] . '" >';
					$html .= str_replace( '#this_form', '#' . $rm_form_id . ' ', $rm_form_settings['form_css'] );
					$html .= '</style>';
				}
				$form_layout_class = ( ! empty( $rm_form_settings['rm_compact_layout'] ) ) ? 'rm_compact_layout' : '';
				$html              .= '<div id="' . $rm_form_id . '" class="rm_form_container rainmaker_form ' . esc_attr( $rm_form_settings['form_style'] ) . ' ' . $form_layout_class . '" data-type="rm_' . esc_attr( $rm_form_settings['type'] ) . '" data-form-id="' . $atts['id'] . '">';
				$html              = apply_filters( 'rainmaker_before_form', $html, $rm_form_settings, $atts['id'] );
				$html              .= $form_html;
				$html              = apply_filters( 'rainmaker_after_form', $html, $rm_form_settings, $atts['id'] );
				$html              .= '</div>';
				$html              .= '<div id="rm_form_message_' . $atts['id'] . '" class="rm_form_message" style="display:none">' . $response_text . '</div>';

				//Add script, if form is added Remote site
				if ( ! empty( $_SERVER['HTTP_ORIGIN'] ) && site_url() !== $_SERVER['HTTP_ORIGIN'] ) {
					$html    .= '<script id="rm_script">';
					$html    .= 'var rm_pre_data = {"ajax_url":"' . admin_url( 'admin-ajax.php' ) . '"';
					$html    .= ', "rm_nonce_field":"' . wp_create_nonce( "rm_form_submission" ) . '"';
					$html    .= '};';
					$content = file_get_contents( dirname( __FILE__ ) . '/../assets/js/main.js' );
					$html    .= ( ! empty( $content ) ) ? $content : '';
					$html    .= '</script>';
				}

				$html      = apply_filters( 'rainmaker_modify_html', $html, $rm_form_settings, $atts['id'] );
				$form_html = '';
			}
			
			echo wp_kses($html, $allowedtags);
			
			return ob_get_clean();
		}


		//form settings
		function form_design_content() {
			global $post;
			if ( ( is_object( $post ) && $post->post_type != 'rainmaker_form' ) ) {
				return;
			}

			$form_data = get_post_meta( $post->ID, 'rm_form_settings', true );
			if ( ! empty( $form_data ) ) {
				echo '<div class="rm-form-shortcode">' . __( 'Put this shortcode', 'icegram-rainmaker' ) . ' <code>[rainmaker_form id="' . $post->ID . '"]</code>' . __( ' wherever you want to show this form', 'icegram-rainmaker' ) . '</div>';
			} else {
				$form_data           = array();
				$form_data['fileds'] = array( array(), array(), array() );
			}
			//TODO : create fileds rows in loop.
			?>
            <div id="rm-form-tabs">
                <ul class="rm-tabs-nav">
                    <li><a href="#rm-tabs-1"><?php _e( 'Form', 'icegram-rainmaker' ); ?></a></li>
                    <li><a href="#rm-tabs-2"><?php _e( 'Design', 'icegram-rainmaker' ); ?></a></li>
                    <li><a href="#rm-tabs-3"><?php _e( 'Form Actions', 'icegram-rainmaker' ); ?></a></li>
                </ul>
                <div id="rm-tabs-1" class="rm-tab">

                    <!-- Subscription Form -->
                    <label class="rm_show_label form_selection"><input type="radio" class="form_type" name="form_data[type]" id="form_subscription" value="subscription" <?php echo ( isset( $form_data['type'] ) ) ? checked( $form_data['type'], 'subscription', false ) : 'checked="checked"'; ?> />
						<?php _e( 'Subscription Form', 'icegram-rainmaker' ); ?></label>
					
                    <ul class="rm-form-field-settings subscription_settings" <?php echo ( ! empty( $form_data['type'] ) && $form_data['type'] == 'subscription' ) ? '' : 'style="display:none"'; ?> >
						
                        <!-- <input type="hidden" name="form_data[type]" value="<?php //echo ( !empty( $form_data['type']) && isset( $form_data['type']) ? $form_data['type']  : 'subscription' );
						?>"> -->
                        <li class="rm-field-row rm-row-header">
                            <div class="rm-form-field-set">
                                <label class="rm_show_label"><?php _e( 'Show?', 'icegram-rainmaker' ); ?></label>
                                <label><?php _e( 'Field', 'icegram-rainmaker' ); ?></label>
                                <label><?php _e( 'Label', 'icegram-rainmaker' ); ?></label>
                            </div>
                        </li>
                        <!-- Name Field -->
                        <li class="rm-field-row">
                            <div class="rm-form-field-set">
                                <label class="rm_show_label"><input type="checkbox" name="form_data[fileds][name][show]" value="yes" <?php ( ! empty( $form_data['fileds']['name']['show'] ) ) ? checked( $form_data['fileds']['name']['show'], 'yes' ) : ''; ?> /></label>
                                <label><?php _e( 'Name', 'icegram-rainmaker' ); ?></label>
                                <input type="text" name="form_data[fileds][name][label]" value="<?php echo( ! empty( $form_data['fileds']['name']['label'] ) ? esc_attr( $form_data['fileds']['name']['label'] ) : __( 'Name', 'icegram-rainmaker' ) ); ?>">
                            </div>
                            <input type="hidden" name="form_data[fileds][name][input_type]" value="text">
                            <input type="hidden" name="form_data[fileds][name][field_type]" value="name">
                        </li>
                        <!-- Email Field -->
                        <li class="rm-field-row">
                            <div class="rm-form-field-set">
                                <label class="rm_show_label"><input type="checkbox" checked disabled/></label>
                                <label>
									<?php _e( 'Email', 'icegram-rainmaker' ); ?>
                                </label>
                                <input type="text" name="form_data[fileds][email][label]" value="<?php echo( ! empty( $form_data['fileds']['email']['label'] ) ? esc_attr( $form_data['fileds']['email']['label'] ) : __( 'Email', 'icegram-rainmaker' ) ); ?>">
                            </div>
                            <input type="hidden" name="form_data[fileds][email][show]" value="yes"/>
                            <input type="hidden" name="form_data[fileds][email][input_type]" value="email">
                            <input type="hidden" name="form_data[fileds][email][field_type]" value="email">
                        </li>
                        <!-- Button Field -->
                        <li class="rm-field-row">
                            <div class="rm-form-field-set">
                                <label class="rm_show_label"><input type="checkbox" checked disabled/></label>
                                <label><?php _e( 'Button', 'icegram-rainmaker' ); ?></label>
                                <input type="hidden" name="form_data[fileds][button][show]" value="yes"/>
                                <input type="text" name="form_data[fileds][button][label]" value="<?php echo( ! empty( $form_data['fileds']['button']['label'] ) ? esc_attr( $form_data['fileds']['button']['label'] ) : __( 'Submit', 'icegram-rainmaker' ) ); ?>">
                            </div>
                            <input type="hidden" name="form_data[fileds][button][input_type]" value="submit">
                            <input type="hidden" name="form_data[fileds][button][field_type]" value="button">
                        </li>
                        <!--GDPR-->
                        <li class="rm-field-row">
                            <div class="rm-form-field-set">
                                <label class="rm_show_label"><input id="rm_enable_gdpr" class="rm_checkbox" type="checkbox" name="form_data[fileds][gdpr][rm_enable_gdpr]" value="yes" <?php ( ! empty( $form_data['fileds']['gdpr']['rm_enable_gdpr'] ) ) ? checked( $form_data['fileds']['gdpr']['rm_enable_gdpr'], 'yes' ) : ''; ?>/></label>
                                <label><?php _e( 'Enable GDPR', 'icegram-rainmaker' ); ?></label>
                                <textarea rows="3" autocomplete="off" cols="65" name="form_data[fileds][gdpr][gdpr_content]"
                                > <?php echo isset( $form_data['fileds']['gdpr']['gdpr_content'] ) ? wp_kses_post( $form_data['fileds']['gdpr']['gdpr_content'] ) : __( 'Please accept terms and condition' ); ?></textarea>
                            </div>
                        </li>
                    </ul>
                    <br>

                    <!-- Contact Form -->
                    <label class="rm_show_label form_selection"> <input type="radio" class="form_type" name="form_data[type]" id="form_contact" value="contact" <?php echo ( isset( $form_data['type'] ) ) ? checked( $form_data['type'], 'contact', false ) : ''; ?> />
					<?php _e( 'Contact Form', 'icegram-rainmaker' ); ?></label>

                    <ul class="rm-form-field-settings contact_settings" <?php echo ( ! empty( $form_data['type'] ) && $form_data['type'] == 'contact' ) ? '' : 'style="display:none"'; ?> >

                        <li class="rm-field-row rm-row-header">
                            <div class="rm-form-field-set">
                                <label class="rm_show_label"><?php _e( 'Show?', 'icegram-rainmaker' ); ?></label>
                                <label><?php _e( 'Field', 'icegram-rainmaker' ); ?></label>
                                <label><?php _e( 'Label', 'icegram-rainmaker' ); ?></label>
                            </div>
                        </li>

                        <!-- Name Field -->
                        <li class="rm-field-row">
                            <div class="rm-form-field-set">
                                <label class="rm_show_label"><input type="checkbox" name="form_data[contact_fields][name][show]" value="yes" <?php ( ! empty( $form_data['contact_fields']['name']['show'] ) ) ? checked( $form_data['contact_fields']['name']['show'], 'yes' ) : ''; ?> /></label>
                                <label><?php _e( 'Name', 'icegram-rainmaker' ); ?></label>
                                <input type="text" name="form_data[contact_fields][name][label]" value="<?php echo( ! empty( $form_data['contact_fields']['name']['label'] ) ? esc_attr( $form_data['contact_fields']['name']['label'] ) : __( 'Name', 'icegram-rainmaker' ) ); ?>">
                            </div>
                            <input type="hidden" name="form_data[contact_fields][name][input_type]" value="text">
                            <input type="hidden" name="form_data[contact_fields][name][field_type]" value="name">
                        </li>

                        <!-- Email Field -->
                        <li class="rm-field-row">
                            <div class="rm-form-field-set">
                                <label class="rm_show_label"><input type="checkbox" checked disabled/></label>
                                <label>
									<?php _e( 'Email', 'icegram-rainmaker' ); ?>
                                </label>
                                <input type="text" name="form_data[contact_fields][email][label]" value="<?php echo( ! empty( $form_data['contact_fields']['email']['label'] ) ? esc_attr( $form_data['contact_fields']['email']['label'] ) : __( 'Email', 'icegram-rainmaker' ) ); ?>">
                            </div>
                            <input type="hidden" name="form_data[contact_fields][email][show]" value="yes"/>
                            <input type="hidden" name="form_data[contact_fields][email][input_type]" value="email">
                            <input type="hidden" name="form_data[contact_fields][email][field_type]" value="email">
                        </li>

                        <!-- Subject Field -->
                        <li class="rm-field-row">
                            <div class="rm-form-field-set">
                                <label class="rm_show_label"><input type="checkbox" name="form_data[contact_fields][subject][show]" value="yes" <?php ( ! empty( $form_data['contact_fields']['subject']['show'] ) ) ? checked( $form_data['contact_fields']['subject']['show'], 'yes' ) : ''; ?> /></label>
                                <label><?php _e( 'Subject', 'icegram-rainmaker' ); ?></label>
                                <input type="text" name="form_data[contact_fields][subject][label]" value="<?php echo( ! empty( $form_data['contact_fields']['subject']['label'] ) ? esc_attr( $form_data['contact_fields']['subject']['label'] ) : __( 'Subject', 'icegram-rainmaker' ) ); ?>">
                            </div>
                            <input type="hidden" name="form_data[contact_fields][subject][input_type]" value="text">
                            <input type="hidden" name="form_data[contact_fields][subject][field_type]" value="subject">
                        </li>
                        <!-- Message Field -->
                        <li class="rm-field-row">
                            <div class="rm-form-field-set">
                                <label class="rm_show_label"><input type="checkbox" name="form_data[contact_fields][msg][show]" value="yes" <?php ( ! empty( $form_data['contact_fields']['msg']['show'] ) ) ? checked( $form_data['contact_fields']['msg']['show'], 'yes' ) : ''; ?> /></label>
                                <label><?php _e( 'Message', 'icegram-rainmaker' ); ?></label>
                                <input type="text" name="form_data[contact_fields][msg][label]" value="<?php echo( ! empty( $form_data['contact_fields']['msg']['label'] ) ? esc_attr( $form_data['contact_fields']['msg']['label'] ) : __( 'Message', 'icegram-rainmaker' ) ); ?>">
                            </div>
                            <input type="hidden" name="form_data[contact_fields][msg][input_type]" value="textarea">
                            <input type="hidden" name="form_data[contact_fields][msg][field_type]" value="message">
                        </li>
                        <!--Button-->
                        <li class="rm-field-row">
                            <div class="rm-form-field-set">
                                <label class="rm_show_label"><input type="checkbox" checked disabled/></label>
                                <label><?php _e( 'Button', 'icegram-rainmaker' ); ?></label>
                                <input type="hidden" name="form_data[contact_fields][button][show]" value="yes"/>
                                <input type="text" name="form_data[contact_fields][button][label]" value="<?php echo( ! empty( $form_data['contact_fields']['button']['label'] ) ? esc_attr( $form_data['contact_fields']['button']['label'] ) : __( 'Submit', 'icegram-rainmaker' ) ); ?>">
                            </div>
                            <input type="hidden" name="form_data[contact_fields][button][input_type]" value="submit">
                            <input type="hidden" name="form_data[contact_fields][button][field_type]" value="button">
                        </li>
                        <!--GDPR-->
                        <li class="rm-field-row">
                            <div class="rm-form-field-set">
                                <label class="rm_show_label"><input id="rm_enable_gdpr" class="rm_checkbox" type="checkbox" name="form_data[contact_fields][gdpr][rm_enable_gdpr]" value="yes" <?php ( ! empty( $form_data['contact_fields']['gdpr']['rm_enable_gdpr'] ) ) ? checked( $form_data['contact_fields']['gdpr']['rm_enable_gdpr'], 'yes' ) : ''; ?>/></label>
                                <label><?php _e( 'Enable GDPR', 'icegram-rainmaker' ); ?></label>
                                <textarea rows="3" autocomplete="off" cols="65" name="form_data[contact_fields][gdpr][gdpr_content]"
                                > <?php echo isset( $form_data['contact_fields']['gdpr']['gdpr_content'] ) ? wp_kses_post( $form_data['contact_fields']['gdpr']['gdpr_content'] ) : __( 'Please accept terms and condition' ); ?></textarea>
                            </div>
                        </li>
                    </ul>
                    <br>

                    <!-- Custom Form-->
                    <label class="rm_show_label form_selection"><input type="radio" class="form_type" name="form_data[type]" id="form_custom" value="custom" <?php echo ( isset( $form_data['type'] ) ) ? checked( $form_data['type'], 'custom', false ) : ''; ?> />
						<?php _e( 'Custom Form', 'icegram-rainmaker' ); ?></label>
                    <ul class="rm-form-field-settings custom_settings" <?php echo ( ! empty( $form_data['type'] ) && $form_data['type'] == 'custom' ) ? '' : 'style="display:none"'; ?>>
                        <li class="rm-field-row rm-row-header">
                            <textarea rows="10" autocomplete="off" cols="65" name="form_data[form_code]" placeholder="<?php _e( 'Paste your custom form html here', 'icegram-rainmaker' ); ?>"><?php if ( isset( $form_data['form_code'] ) ) {
		                            echo esc_attr( $form_data['form_code'] );
	                            } ?></textarea>
                        </li>
                    </ul>

                    <!-- Add more form types here-->
					<?php do_action( 'rainmaker_add_form_types', $form_data ) ?>

                </div>
                <div id="rm-tabs-2" class="rm-tab">
                    <ul class="rm-form-field-settings">

                        <li class="rm-field-row">
                            <div><label><?php _e( 'Select Form style', 'icegram-rainmaker' ); ?></label></div>
                            <input id="rm_style_selector" name="form_data[form_style]" type="hidden" value="<?php echo ( ! empty( $form_data['form_style'] ) ) ? esc_attr( $form_data['form_style'] ) : '' ?>"/>
                            <div class="rm_grid rm_clear_fix">
                                <div class="rm_grid_item" data-style="rm-form-style0">
                                    <label><?php _e( 'Classic', 'icegram-rainmaker' ) ?></label>
                                    <div class="rm_item_inner rm_style_classic"></div>
                                </div>
                                <div class="rm_grid_item" data-style="rm-form-style1">
                                    <label><?php _e( 'Iconic', 'icegram-rainmaker' ) ?></label>
                                    <div class="rm_item_inner rm_style_iconic"></div>
                                </div>
                                <div class="rm_grid_item" data-style="rm-form-style2">
                                    <label><?php _e( 'Material', 'icegram-rainmaker' ) ?></label>
                                    <div class="rm_item_inner rm_style_material"></div>
                                </div>
                                <div class="rm_grid_item" data-style="rm-form-style">
                                    <label><?php _e( 'None', 'icegram-rainmaker' ) ?></label>
                                    <div class="rm_item_inner rm_style_none"><span><?php _e( 'Inherit wordpress theme style', 'icegram-rainmaker' ) ?></span></div>
                                </div>
                            </div>
                        </li>
                        <li class="rm-field-row-layout" id="rm_row_form_layout" style="display:none;">
                            <div><label class="rm-bold-text"><input id="rm_compact_layout" class="rm_checkbox" type="checkbox" name="form_data[rm_compact_layout]" value="yes" <?php ( ! empty( $form_data['rm_compact_layout'] ) ) ? checked( $form_data['rm_compact_layout'], 'yes' ) : ''; ?>/>
									<?php _e( 'Use Compact Layout', 'icegram-rainmaker' ); ?></label>
                            </div>
                            <div class="rm_form_compact_layout" id="rm_compact_layout" style="display:none;">
                                <div class="rm_grid_item_layout" data-layout="rm-form-layout">
                                    <div class="rm_item_inner_layout rm_layout_compact"></div>
                                </div>
                            </div>
                        </li>
                        <!-- Add more form design options here-->
						<?php do_action( 'rainmaker_add_form_design_options', $form_data ) ?>
                    </ul>
                </div>
                <div id="rm-tabs-3" class="rm-tab">
                    <ul class="rm-form-field-settings rm-form-action-settings">
                        <li class="rm-field-row">
                            <div class="rm-form-field-set">
                                <span class="rm_save_db"><?php _e( 'Leads will be always collected in database', 'icegram-rainmaker' ) ?></span>
                            </div>
                        </li>
                        <li class="rm-field-row">
                            <div class="rm-form-field-set">
                                <label class="rm-bold-text"><input class="rm_checkbox" type="checkbox" disabled readonly checked="checked"/> <?php _e( 'Show a Thank You message', 'icegram-rainmaker' ); ?></label>
                                <textarea rows="3" autocomplete="off" cols="65" name="form_data[success_message]" placeholder="<?php _e( 'Thank You!', 'icegram-rainmaker' ); ?>"><?php if ( isset( $form_data['success_message'] ) ) {
										echo esc_attr( $form_data['success_message'] );
									} ?></textarea>
                            </div>
                        </li>
                        <li class="rm-field-row">
                            <div class="rm-form-field-set">
                                <label class="rm-bold-text"><input class="rm_checkbox" type="checkbox" name="form_data[rm_enable_redirection]" value="yes" <?php ( ! empty( $form_data['rm_enable_redirection'] ) ) ? checked( $form_data['rm_enable_redirection'], 'yes' ) : ''; ?>/> <?php _e( 'Redirect to URL', 'icegram-rainmaker' ); ?></label>
                                <input type="text" name="form_data[redirection_url]" placeholder="<?php _e( 'Enter link URL here', 'icegram-rainmaker' ); ?>" value="<?php if ( isset( $form_data['redirection_url'] ) ) {
									echo esc_attr( $form_data['redirection_url'] );
								} ?>"/>
                            </div>
                        </li>
                        <li class="rm-field-row">
                            <div class="rm-form-field-set">
                                <label class="rm-bold-text"><input id="rm_enable_list" class="rm_checkbox" type="checkbox" name="form_data[rm_enable_list]" value="yes" <?php ( ! empty( $form_data['rm_enable_list'] ) ) ? checked( $form_data['rm_enable_list'], 'yes' ) : ''; ?>/>
									<?php _e( 'Subscribe to a mailing list', 'icegram-rainmaker' ); ?></label>
								<?php
								$mailers                       = array();
								$mailers                       = apply_filters( 'rainmaker_mailers', $mailers );
								$form_data['rm_list_provider'] = ( ! empty( $form_data['rm_list_provider'] ) ) ? $form_data['rm_list_provider'] : '';
								?>
                                <select id="rm-list-provider" class="rm-select" name="form_data[rm_list_provider]">
									<?php
									if ( ! empty( $mailers ) ) {
										foreach ( $mailers as $slug => $setting ) {
											echo '<option value="' . $slug . '" ' . selected( $form_data['rm_list_provider'], $slug, false ) . '>' . $setting['name'] . '</option>';
										}
									}
									?>
                                </select>
                                <div id="rm-list-details" class="rm-form-field-subset">
                                    <div class="rm-loader"></div>
                                    <div id="rm-list-details-container" class="rm-list-details-container"></div>
                                </div>
                            </div>
                        </li>
                        <li class="rm-field-row">
	                            <div class="rm-form-field-set" style="display: flex">
	                            	<div style="width:29.5%">
	                                <label style="width:96%" class="rm-bold-text"><input id='rm_mail_send' class="rm_checkbox" type="checkbox" name="form_data[rm_mail_send]" value="yes" <?php ( ! empty( $form_data['rm_mail_send'] ) ) ? checked( $form_data['rm_mail_send'], 'yes' ) : ''; ?>/><?php _e( 'Email form data to', 'icegram-rainmaker' ); ?></label>
	                                
	                            </div>
	                            <div style="width:65%;">
	                                <input class="rm-mail-to-input" type="text" name="form_data[rm_mail_to]" value="<?php echo ( ! empty( $form_data['rm_mail_to'] ) ) ? esc_attr( $form_data['rm_mail_to'] ) : '' ?>" placeholder='<?php _e( 'Enter Email Id', 'icegram-rainmaker' ); ?>'/>
	                                <p class="rm-helper-text"><?php esc_html_e('Enter the email addresses that should receive form data (separated by comma).', 'icegram-rainmaker' ); ?></p>
	                            </div> 
                            </div>
                        </li>
                        <li class="rm-field-row">
                            <div class="rm-form-field-set">
                                <label class="rm-bold-text"><input class="rm_checkbox" type="checkbox" name="form_data[rm_enable_webhook]" value="yes" <?php ( ! empty( $form_data['rm_enable_webhook'] ) ) ? checked( $form_data['rm_enable_webhook'], 'yes' ) : ''; ?>/>
									<?php _e( 'Trigger a Webhook', 'icegram-rainmaker' ); ?></label>
                                <input type="text" name="form_data[webhook_url]" value="<?php echo ( ! empty( $form_data['webhook_url'] ) ) ? esc_attr( $form_data['webhook_url'] ) : '' ?>" placeholder="Enter webhook url"/>
                        </li>

                        <!-- Add more form actions here-->
						<?php do_action( 'rainmaker_add_form_actions', $form_data ) ?>
                    </ul>
                </div>

            </div>
			<?php
		}

		/* Custom Css text-area*/
		function rm_add_custom_css_textarea( $form_data ) {
			$form_css = ( ! empty( $form_data['form_css'] ) ) ? $form_data['form_css'] : '';
			?>
            <li class="rm-field-row">
                <div><label><?php _e( 'Custom CSS', 'icegram-rainmaker' ); ?></label></div>
                <div>
                    <textarea class="custom_code_area" rows="8" autocomplete="off" cols="65" name="form_data[form_css]" placeholder="<?php _e( 'Add custom CSS code for this form here ', 'icegram-rainmaker' ); ?>"><?php echo esc_attr( $form_css ); ?></textarea>
                    <span><br><?php _e( 'e.g.', 'icegram-rainmaker' ); ?> <code> #this_form .rm_button { background-color: #1355cc;} </code></span>
                </div>
            </li>
			<?php
		}


		// Save all list of messages and targeting rules
		function save_form_settings( $post_id, $post ) {
			if ( empty( $post_id ) || empty( $post ) || empty( $_POST ) ) {
				return;
			}
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
			if ( is_int( wp_is_post_revision( $post ) ) ) {
				return;
			}
			if ( is_int( wp_is_post_autosave( $post ) ) ) {
				return;
			}
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
			if ( $post->post_type != 'rainmaker_form' ) {
				return;
			}
			if ( empty( $_POST['form_data'] ) ) {
				return;
			}

			$_POST['form_data']['redirection_url'] = trim( $_POST['form_data']['redirection_url'], " " );
			$post_data                             = apply_filters( 'rainmaker_before_save_form_settings', $_POST['form_data'] );
			
			$form_data = $post_data;
			unset($form_data['form_code']);
			
			$form_data = self::sanitize_array($form_data);
			$form_data['form_code'] = $post_data['form_code'];
			
			update_post_meta( $post_id, 'rm_form_settings', $form_data );
		}

		public static function sanitize_array($form_data) {
    		foreach ( $form_data as $key => &$value ) {
		        if ( is_array( $value ) ) {
		            $value = self::sanitize_array($value);
		        } else if ( $value !== strip_tags( $value ) ) { // Check if value contains HTML.
					$value = wp_kses_post( $value );
				} else {
		            $value = sanitize_text_field( $value );
		        }
		    }

		    return $form_data;
		}

		//fetch all available form list
		public static function get_rm_form_id_name_map() {
			$rm_form_id_name_map = array();
			$post_types          = array( 'rainmaker_form' );
			$args                = array(
				'post_type'      => $post_types,
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'fields'         => 'ids',
			);

			$rm_from_ids = get_posts( $args );
			if ( ! empty( $rm_from_ids ) ) {
				foreach ( $rm_from_ids as $id ) {
					$rm_form_id_name_map[ $id ] = get_the_title( $id );
				}
			}

			return $rm_form_id_name_map;
		}

		//execute shortcode in text widget
		function rm_widget_text_filter( $content ) {
			if ( ! preg_match( '/\[[\r\n\t ]*rainmaker_form?[\r\n\t ].*?\]/', $content ) ) {
				return $content;
			}

			$content = do_shortcode( $content );

			return $content;
		}

		//filter lead data: remove unwanted
		function rm_filter_lead_data( $lead ) {
			if ( ! empty( $lead ) ) {
				foreach ( $lead as $key => $value ) {
					if ( substr( $key, 0, 3 ) == 'ig_' || substr( $key, 0, 3 ) == 'rm_' ) {
						unset( $lead[ $key ] );
					}
				}
			}

			return $lead;
		}

		public static function trigger_webhook( $params, $form_settings ) {
			if ( ! empty( $form_settings['rm_enable_webhook'] ) && ! empty( $form_settings['webhook_url'] ) ) {
				$url = $form_settings['webhook_url'];

				$options       = array(
					'timeout' => 15,
					'method'  => 'POST',
					'body'    => http_build_query( $params )
				);
				$response      = wp_remote_post( $url, $options );
				$response_code = wp_remote_retrieve_response_code( $response );
				if ( is_wp_error( $response ) ) {
					error_log( wp_strip_all_tags( $response->get_error_message() ) );
					// wp_die();
				} elseif ( $response_code == 200 ) {
					//TODO :: log in response
					error_log( $response['body'] );
					// wp_die();
				} else {
					//wp_die($response['body'], 'Error in Submission', array('response' => $response_code) );
					error_log( 'Error in Submission' );
				}
			}
		} // trigger_webhook

		public function rm_send_mail( $lead, $rm_form_settings ) {
			if ( ! empty( $rm_form_settings['rm_mail_send'] ) && $rm_form_settings['rm_mail_send'] == 'yes' && ! empty( $rm_form_settings['rm_mail_to'] ) ) {
				$style   = '<style>
							th.rm-heading{
								text-align: left;
							}
							table, th, td {
							    border: 1px solid #ccc;
							    border-collapse: collapse;
							}
							th, td{
								padding:0.3em;
							}

						 </style>';
				$heading = __( '*** Form submmision ***', 'icegram-rainmaker' );
				$html    = $style . $heading . '<table><thead><th class="rm-heading">' . __( 'Name', 'icegram-rainmaker' ) . '</th> <th class="rm-heading">' . __( 'Value', 'icegram-rainmaker' ) . '</th></thead><tbody>';
				foreach ( $lead as $key => $value ) {
					if ( ! empty( $value ) ) {
						$html .= "<tr>";
						$html .= '<td>' . $key . '</td>';
						$html .= '<td>' . $value . '</td>';
						$html .= "</tr>";
					}
				}
				$html       .= '</tbody></table>';
				$headers    = 'Content-Type: text/html; charset=UTF-8';
				$form_title = get_the_title( $rm_form_settings['form_id'] );
				$subject    = __( 'Lead added from: ', 'icegram-rainmaker' ) . $form_title;
				$rm_mail_to = explode( ',', $rm_form_settings['rm_mail_to'] );
				if( ! empty( $rm_mail_to ) && is_array( $rm_mail_to ) && count( $rm_mail_to ) > 0 ) {
					foreach ( $rm_mail_to as $email ) {
					    $email = trim($email);
						wp_mail( $email, $subject, $html, $headers );
					}
				}
			}

			return true;
		}

		public static function get_rm_meta_info() {
			$total_leads_obj = wp_count_posts( 'rainmaker_lead' );
			$total_forms_obj = wp_count_posts( 'rainmaker_form' );

			$meta_info       = array(
				'total_leads' => !empty( $total_leads_obj ) ? $total_leads_obj->publish : 0,
				'total_forms' => !empty( $total_forms_obj ) ? $total_forms_obj->publish : 0,
			);

			return $meta_info;
		}
		
		/**
		 * Is RM PLUS?
		 *
		 * @return bool
		 *
		 * @since
		 */
		public function is_plus() {
			return file_exists( IG_RM_PLUGIN_DIR . 'plus/plus-class-icegram-rainmaker.php' );
		}

		/**
		 * Is RM PRO?
		 *
		 * @return bool
		 *
		 * @since
		 */
		public function is_pro() {
			return file_exists( IG_RM_PLUGIN_DIR . 'pro/pro-class-icegram-rainmaker.php' );
		}

		/**
		 * Is RM MAX ?
		 *
		 * @return bool
		 *
		 * @since
		 */
		public function is_max() {
			return file_exists( IG_RM_PLUGIN_DIR . 'max/max-class-icegram-rainmaker.php' );
		}

		/**
		 * Is RM Premium?
		 *
		 * @return bool
		 *
		 * @since
		 */
		public function is_premium() {

			return self::is_max() || self::is_pro() || self::is_plus();
		}

		public function get_plan() {

			if ( file_exists( IG_RM_PLUGIN_DIR . '/max/max-class-icegram-rainmaker.php' ) ) {
				$plan = 'max';
			} elseif ( file_exists( IG_RM_PLUGIN_DIR . '/pro/pro-class-icegram-rainmaker.php' ) ) {
				$plan = 'pro';
			} elseif ( file_exists( IG_RM_PLUGIN_DIR . '/plus/plus-class-icegram-rainmaker.php' ) ) {
				$plan = 'plus';
			} else {
				$plan = 'lite';
			}

			return $plan;
		}

		/**
		 * Check if premium plugin installed
		 *
		 * @return boolean
		 *
		 * @since 1.2.3
		 */
		public function is_premium_installed() {
			global $ig_rm_tracker;

			$ig_rainmaker_premium = 'icegram-rainmaker-premium/icegram-rainmaker-premium.php';

			return $ig_rm_tracker::is_plugin_installed( $ig_rainmaker_premium );
		}

		/**
		 * Check if sale period
		 *
		 * @return boolean
		 */
		public static function is_offer_period( $offer_name = '' ) {

			$is_offer_period = false;
			if ( ! empty( $offer_name ) ) {
				$current_utc_time = time();
				$current_ist_time = $current_utc_time + ( 5.5 * HOUR_IN_SECONDS ); // Add IST offset to get IST time

				$offer_start_time 	= $current_ist_time;
				$offer_end_time 	= $current_ist_time;

				if ( 'bfcm' === $offer_name ) {
					$offer_start_time = strtotime( '2024-11-26 12:30:00' ); // Offer start time in IST
					$offer_end_time   = strtotime( '2024-12-05 12:30:00' ); // Offer end time in ISTsdf
				}
	
				$is_offer_period = $current_ist_time >= $offer_start_time && $current_ist_time <= $offer_end_time;
			}
			
			return $is_offer_period;
		}


		/**
		 * Allow CSS style in WP Kses
		 * 
		 * @since 1.3.9
		 */
		function ig_rm_allowed_css_style( $default_allowed_attr ) {
			return array(); // Return empty array to whitelist all CSS properties.
		}

		function ig_rm_allowed_html_tags_in_esc() {
			$context_allowed_tags = wp_kses_allowed_html( 'post' );
			$custom_allowed_tags  = array(
				'div'      => array(
					'x-data' => true,
					'x-show' => true,
				),
				'select'   => array(
					'class'    => true,
					'name'     => true,
					'id'       => true,
					'style'    => true,
					'title'    => true,
					'role'     => true,
					'data-*'   => true,
					'tab-*'    => true,
					'multiple' => true,
					'aria-*'   => true,
					'disabled' => true,
					'required' => 'required',
				),
				'optgroup' => array(
					'label' => true,
				),
				'option'   => array(
					'class'    => true,
					'value'    => true,
					'selected' => true,
					'name'     => true,
					'id'       => true,
					'style'    => true,
					'title'    => true,
					'data-*'   => true,
				),
				'input'    => array(
					'class'          => true,
					'name'           => true,
					'type'           => true,
					'value'          => true,
					'id'             => true,
					'checked'        => true,
					'disabled'       => true,
					'selected'       => true,
					'style'          => true,
					'required'       => 'required',
					'min'            => true,
					'max'            => true,
					'maxlength'      => true,
					'size'           => true,
					'placeholder'    => true,
					'autocomplete'   => true,
					'autocapitalize' => true,
					'autocorrect'    => true,
					'tabindex'       => true,
					'role'           => true,
					'aria-*'         => true,
					'data-*'         => true,
				),
				'label'    => array(
					'class' => true,
					'name'  => true,
					'type'  => true,
					'value' => true,
					'id'    => true,
					'for'   => true,
					'style' => true,
				),
				'form'     => array(
					'class'  => true,
					'name'   => true,
					'value'  => true,
					'id'     => true,
					'style'  => true,
					'action' => true,
					'method' => true,
					'data-*' => true,
				),
				'svg'      => array(
					'width'    => true,
					'height'   => true,
					'viewbox'  => true,
					'xmlns'    => true,
					'class'    => true,
					'stroke-*' => true,
					'fill'     => true,
					'stroke'   => true,
				),
				'path'     => array(
					'd'               => true,
					'fill'            => true,
					'class'           => true,
					'fill-*'          => true,
					'clip-*'          => true,
					'stroke-linecap'  => true,
					'stroke-linejoin' => true,
					'stroke-width'    => true,
					'fill-rule'       => true,
				),

				'main'     => array(
					'align'    => true,
					'dir'      => true,
					'lang'     => true,
					'xml:lang' => true,
					'aria-*'   => true,
					'class'    => true,
					'id'       => true,
					'style'    => true,
					'title'    => true,
					'role'     => true,
					'data-*'   => true,
				),
				'textarea' => array(
					'type' 		   => true,
					'autocomplete' => true,
					'required'	   => 'required',
					'placeholder'  => true,
					'maxlength'	   => true,
				),
				'style'    => array(),
				'link'     => array(
					'rel'   => true,
					'id'    => true,
					'href'  => true,
					'media' => true,
				),
				'a'        => array(
					'x-on:click' => true,
				),
				'polygon'  => array(
					'class'  => true,
					'points' => true,
				),
			);

			$allowedtags = array_merge_recursive( $context_allowed_tags, $custom_allowed_tags );

			return $allowedtags;
		}

		/**
		 * Method to add additional plugin usage tracking data specific to Icegram Collect
		 *
		 * @param array $tracking_data
		 *
		 * @return array $tracking_data
		 *
		 * @since 
		 */
		public function add_tracking_data( $tracking_data = array() ) {

			$tracking_data['plugin_meta_info'] = self::get_rm_meta_info();

			return $tracking_data;
		}

	}
}
