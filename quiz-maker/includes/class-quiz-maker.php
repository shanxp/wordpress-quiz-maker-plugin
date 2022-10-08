<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Quiz_Maker
 * @subpackage Quiz_Maker/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Quiz_Maker
 * @subpackage Quiz_Maker/includes
 * @author     AYS Pro LLC <info@ays-pro.com>
 */
class Quiz_Maker {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Quiz_Maker_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'AYS_QUIZ_NAME_VERSION' ) ) {
			$this->version = AYS_QUIZ_NAME_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'quiz-maker';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();


	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Quiz_Maker_Loader. Orchestrates the hooks of the plugin.
	 * - Quiz_Maker_i18n. Defines internationalization functionality.
	 * - Quiz_Maker_Admin. Defines all hooks for the admin area.
	 * - Quiz_Maker_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
        if ( ! class_exists( 'WP_List_Table' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
        }
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-quiz-maker-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-quiz-maker-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-quiz-maker-admin.php';


		/*
		 * The class is responsible for showing quizes in wordpress default WP_LIST_TABLE style
		 */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/lists/class-quiz-maker-quizes-list-table.php';

        /*
         * The class is responsible for showing quiz categories in wordpress default WP_LIST_TABLE style
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/lists/class-quiz-maker-quiz-categories-list-table.php';

        /*
         * The class is responsible for showing questions in wordpress default WP_LIST_TABLE style
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/lists/class-quiz-maker-questions-list-table.php';

        /*
         * The class is responsible for showing question categories in wordpress default WP_LIST_TABLE style
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/lists/class-quiz-maker-question-categories-list-table.php';

        /*
         * The class is responsible for showing quiz results in wordpress default WP_LIST_TABLE style
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/lists/class-quiz-maker-results-list-table.php';

        /*
         * The class is responsible for showing all quiz reviews in wordpress default WP_LIST_TABLE style
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/lists/class-quiz-maker-all-reviews-list-table.php';

        /**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/class-quiz-maker-category-shortcode.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/class-quiz-maker-all-results-shortcode.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/class-quiz-maker-quiz-all-results-shortcode.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/class-quiz-maker-display-questions-shortcode.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/class-quiz-maker-extra-shortcode.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/class-quiz-maker-most-popular-shortcode.php';

        /*
         * The class is responsible for showing quiz results in wordpress default WP_LIST_TABLE style
         */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/settings/quiz-maker-settings-actions.php';
		
		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-quiz-maker-public.php';

		$this->loader = new Quiz_Maker_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Quiz_Maker_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Quiz_Maker_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
        $this->loader->add_action( 'init', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Quiz_Maker_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_head', $plugin_admin, 'admin_menu_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

        // Add menu item
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_quizzes_submenu', 90 );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_questions_submenu', 95 );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_quiz_categories_submenu', 100 );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_questions_categories_submenu', 105 );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_custom_fields_submenu', 110 );
        // $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_orders_submenu', 115 );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_results_submenu', 120 );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_integrations_submenu', 125 );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_dashboard_submenu', 132 );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_general_settings_submenu', 130 );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_featured_plugins_submenu', 135 );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_subscribe_email', 140 );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_quiz_features_submenu', 145 );

        $this->loader->add_action('wp_ajax_gen_ays_quiz_shortcode', $plugin_admin, 'gen_ays_quiz_shortcode_callback');
        $this->loader->add_filter("mce_external_plugins", $plugin_admin, "ays_quiz_register_tinymce_plugin");
        $this->loader->add_filter('mce_buttons', $plugin_admin, 'ays_quiz_add_tinymce_button');

        $this->loader->add_action( 'wp_ajax_add_question_rows', $plugin_admin, 'add_question_rows' );
		$this->loader->add_action( 'wp_ajax_nopriv_add_question_rows', $plugin_admin, 'add_question_rows' );
		
        $this->loader->add_action( 'wp_ajax_ays_show_results', $plugin_admin, 'ays_show_results' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_show_results', $plugin_admin, 'ays_show_results' );

        $this->loader->add_action( 'wp_ajax_deactivate_plugin_option_qm', $plugin_admin, 'deactivate_plugin_option');
        $this->loader->add_action( 'wp_ajax_nopriv_deactivate_plugin_option_qm', $plugin_admin, 'deactivate_plugin_option');

        $this->loader->add_action( 'wp_ajax_ays_quick_start', $plugin_admin, 'ays_quick_start' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_quick_start', $plugin_admin, 'ays_quick_start' );

        $this->loader->add_action( 'wp_ajax_ays_quiz_subscribe_email', $plugin_admin, 'ays_quiz_subscribe_email' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_quiz_subscribe_email', $plugin_admin, 'ays_quiz_subscribe_email' );

		$this->loader->add_action( 'plugins_loaded', $plugin_admin, 'ays_change_db_questions' );
        // Add Settings link to the plugin
        $plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_name . '.php' );
        $this->loader->add_filter( 'plugin_action_links_' . $plugin_basename, $plugin_admin, 'add_action_links' );

        // Before VC Init
        $this->loader->add_action( 'vc_before_init', $plugin_admin, 'vc_before_init_actions' );

        $this->loader->add_action( 'elementor/widgets/widgets_registered', $plugin_admin, 'quiz_maker_el_widgets_registered' );
        
		$this->loader->add_action( 'admin_title', $plugin_admin, 'change_dashboard_title' );
		
        $this->loader->add_action( 'in_admin_footer', $plugin_admin, 'quiz_maker_admin_footer', 1 );
        
		$this->loader->add_action( 'wp_dashboard_setup', $plugin_admin, 'quiz_maker_add_dashboard_widgets' );
		
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'codemirror_enqueue_scripts');

		// Add aditional links to the plugin
		$this->loader->add_filter( 'plugin_row_meta', $plugin_admin, 'add_quiz_row_meta' , 10 , 2 );

		// Sale Banner
        $this->loader->add_action( 'admin_notices', $plugin_admin, 'ays_quiz_sale_baner', 1 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Quiz_Maker_Public( $this->get_plugin_name(), $this->get_version() );
		$plugin_public_quiz_category = new Quiz_Maker_Quiz_Category( $this->get_plugin_name(), $this->get_version() );
		$plugin_public_results_page = new Quiz_Maker_All_Results( $this->get_plugin_name(), $this->get_version() );
		$plugin_public_quiz_all_results_page = new Quiz_Maker_Quiz_All_Results( $this->get_plugin_name(), $this->get_version() );
		$plugin_public_display_questions = new Quiz_Maker_Display_Questions( $this->get_plugin_name(), $this->get_version() );
		$plugin_public_extra_shortcodes = new Ays_Quiz_Maker_Extra_Shortcodes_Public( $this->get_plugin_name(), $this->get_version() );
		$plugin_public_most_popular_shortcodes = new Ays_Quiz_Maker_Most_Popular_Shortcodes_Public( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'wp_ajax_ays_finish_quiz', $plugin_public, 'ays_finish_quiz' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_finish_quiz', $plugin_public, 'ays_finish_quiz' );

        $this->loader->add_action( 'wp_ajax_ays_rate_the_quiz', $plugin_public, 'ays_rate_the_quiz' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_rate_the_quiz', $plugin_public, 'ays_rate_the_quiz' );
        
        $this->loader->add_action( 'wp_ajax_ays_get_rate_last_reviews', $plugin_public, 'ays_get_rate_last_reviews' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_get_rate_last_reviews', $plugin_public, 'ays_get_rate_last_reviews' );
        
        $this->loader->add_action( 'wp_ajax_ays_load_more_reviews', $plugin_public, 'ays_load_more_reviews' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_load_more_reviews', $plugin_public, 'ays_load_more_reviews' );
        
        $this->loader->add_action( 'wp_ajax_ays_get_user_information', $plugin_public, 'ays_get_user_information' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_get_user_information', $plugin_public, 'ays_get_user_information' );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles_early' );
		// $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		// $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		
		// $this->loader->add_action( 'wp_head', $plugin_public, 'aaaa' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Quiz_Maker_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}




}
