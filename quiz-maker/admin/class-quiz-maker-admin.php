<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Quiz_Maker
 * @subpackage Quiz_Maker/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Quiz_Maker
 * @subpackage Quiz_Maker/admin
 * @author     AYS Pro LLC <info@ays-pro.com>
 */

class Quiz_Maker_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;


    private $quizes_obj;
    private $quiz_categories_obj;
    private $questions_obj;
    private $question_categories_obj;
    private $results_obj;
    private $settings_obj;
    private $all_reviews_obj;
    
    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $plugin_name The name of this plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($plugin_name, $version){

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        add_filter('set-screen-option', array(__CLASS__, 'set_screen'), 10, 3);
        $per_page_array = array(
            'quizes_per_page',
            'questions_per_page',
            'quiz_categories_per_page',
            'question_categories_per_page',
            'quiz_results_per_page',
            'quiz_all_reviews_per_page',
        );
        foreach($per_page_array as $option_name){
            add_filter('set_screen_option_'.$option_name, array(__CLASS__, 'set_screen'), 10, 3);
        }
    }

    /**
     * Register the styles for the admin menu area.
     *
     * @since    1.0.0
     */
    public function admin_menu_styles(){
        
        echo "<style>
            .ays_menu_badge{
                color: #fff;
                display: inline-block;
                font-size: 10px;
                line-height: 14px;
                text-align: center;
                background: #ca4a1f;
                margin-left: 5px;
                border-radius: 20px;
                padding: 2px 5px;
            }

            #adminmenu a.toplevel_page_quiz-maker div.wp-menu-image img {
                width: 32px;
                padding: 1px 0 0;
                transition: .3s ease-in-out;
            }
        </style>";

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles($hook_suffix){
        wp_enqueue_style($this->plugin_name . '-admin', plugin_dir_url(__FILE__) . 'css/admin.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '-sweetalert-css', AYS_QUIZ_PUBLIC_URL . '/css/quiz-maker-sweetalert2.min.css', array(), $this->version, 'all');

        if (false === strpos($hook_suffix, $this->plugin_name))
            return;

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Quiz_Maker_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Quiz_Maker_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style('wp-color-picker');
        // You need styling for the datepicker. For simplicity I've linked to the jQuery UI CSS on a CDN.
        // wp_register_style( 'jquery-ui', 'https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css' );
        // wp_enqueue_style( 'jquery-ui' );

        wp_enqueue_style($this->plugin_name . '-animate.css', plugin_dir_url(__FILE__) . 'css/animate.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '-sweetalert-css', AYS_QUIZ_PUBLIC_URL . '/css/quiz-maker-sweetalert2.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '-font-awesome', AYS_QUIZ_PUBLIC_URL . '/css/quiz-maker-font-awesome.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '-select2', AYS_QUIZ_PUBLIC_URL .  '/css/quiz-maker-select2.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '-bootstrap', plugin_dir_url(__FILE__) . 'css/bootstrap.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '-data-bootstrap', plugin_dir_url(__FILE__) . 'css/dataTables.bootstrap4.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '-jquery-datetimepicker', plugin_dir_url(__FILE__) . 'css/jquery-ui-timepicker-addon.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/quiz-maker-admin.css', array(), time()/*$this->version*/, 'all');
        wp_enqueue_style($this->plugin_name . '-banner', plugin_dir_url(__FILE__) . 'css/quiz-maker-banner.css', array(), time(), 'all');
        wp_enqueue_style($this->plugin_name . "-loaders", plugin_dir_url(__FILE__) . 'css/loaders.css', array(), time()/*$this->version*/, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts($hook_suffix){
        global $wp_version;

        $version1 = $wp_version;
        $operator = '>=';
        $version2 = '5.5';
        $versionCompare = $this->versionCompare($version1, $operator, $version2);

        if ($versionCompare) {
            wp_enqueue_script( $this->plugin_name.'-wp-load-scripts', plugin_dir_url(__FILE__) . 'js/ays-wp-load-scripts.js', array(), $this->version, true);
        }

        if (false !== strpos($hook_suffix, "plugins.php")){
            wp_enqueue_script($this->plugin_name . '-sweetalert-js', AYS_QUIZ_PUBLIC_URL . '/js/quiz-maker-sweetalert2.all.min.js', array('jquery'), $this->version, true );
            wp_enqueue_script($this->plugin_name . '-admin', plugin_dir_url(__FILE__) . 'js/admin.js', array('jquery'), $this->version, true);
            wp_localize_script($this->plugin_name . '-admin',  'quiz_maker_admin_ajax', array('ajax_url' => admin_url('admin-ajax.php')));
        }

        if (false === strpos($hook_suffix, $this->plugin_name))
            return;

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Quiz_Maker_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Quiz_Maker_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        $quiz_banner_date = $this->ays_quiz_update_banner_time();

        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-effects-core');
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('jquery-ui-datepicker');

        wp_enqueue_script( $this->plugin_name . '-color-picker-alpha', plugin_dir_url(__FILE__) . 'js/wp-color-picker-alpha.min.js', array( 'wp-color-picker' ), $this->version, true );
        $color_picker_strings = array(
            'clear'            => __( 'Clear', $this->plugin_name ),
            'clearAriaLabel'   => __( 'Clear color', $this->plugin_name ),
            'defaultString'    => __( 'Default', $this->plugin_name ),
            'defaultAriaLabel' => __( 'Select default color', $this->plugin_name ),
            'pick'             => __( 'Select Color', $this->plugin_name ),
            'defaultLabel'     => __( 'Color value', $this->plugin_name ),
        );
        wp_localize_script( $this->plugin_name . '-color-picker-alpha', 'wpColorPickerL10n', $color_picker_strings );

        wp_enqueue_media();
        wp_enqueue_script('ays_quiz_popper', plugin_dir_url(__FILE__) . 'js/popper.min.js', array('jquery'), $this->version, false);
        wp_enqueue_script('ays_quiz_bootstrap', plugin_dir_url(__FILE__) . 'js/bootstrap.min.js', array('jquery'), $this->version, false);
        wp_enqueue_script($this->plugin_name . '-select2js', AYS_QUIZ_PUBLIC_URL . '/js/quiz-maker-select2.min.js', array('jquery'), $this->version, true);
        wp_enqueue_script($this->plugin_name . '-sweetalert-js', AYS_QUIZ_PUBLIC_URL . '/js/quiz-maker-sweetalert2.all.min.js', array('jquery'), $this->version, true );
        wp_enqueue_script($this->plugin_name . '-datatable-min', AYS_QUIZ_PUBLIC_URL . '/js/quiz-maker-datatable.min.js', array('jquery'), $this->version, true);
		wp_enqueue_script($this->plugin_name . "-db4.min.js", plugin_dir_url( __FILE__ ) . 'js/dataTables.bootstrap4.min.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script($this->plugin_name . "-jquery.datetimepicker.js", plugin_dir_url( __FILE__ ) . 'js/jquery-ui-timepicker-addon.js', array( 'jquery' ), $this->version, true );
        wp_enqueue_script($this->plugin_name . '-ajax', plugin_dir_url(__FILE__) . 'js/quiz-maker-admin-ajax.js', array('jquery'), $this->version, true);
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'js/quiz-maker-admin.js',
            array('jquery', 'wp-color-picker'),
            $this->version,
            true
        );
        wp_localize_script($this->plugin_name . '-ajax', 'quiz_maker_ajax', array(
            'ajax_url'          => admin_url('admin-ajax.php'),
            "emptyEmailError"   => __( 'Email field is empty', $this->plugin_name),
            "invalidEmailError" => __( 'Invalid Email address', $this->plugin_name),
        ));
        wp_localize_script( $this->plugin_name, 'quizLangObj', array(
            'quizBannerDate'    => $quiz_banner_date,

            'questionTitle'     => __( 'Question Default Title', $this->plugin_name),
            'radio'             => __( 'Radio', $this->plugin_name),
            'checkbox'          => __( 'Checkbox', $this->plugin_name),
            'dropdawn'          => __( 'Dropdawn', $this->plugin_name),
            'emptyAnswer'       => __( 'Empty Answer', $this->plugin_name),
            'addGif'            => __( 'Add Gif', $this->plugin_name),
            'textType'          => __( 'Text', $this->plugin_name),
            'answerText'        => __( 'Answer text', $this->plugin_name),
            'copied'            => __( 'Copied!', $this->plugin_name),
            'clickForCopy'      => __( 'Click for copy.', $this->plugin_name),
            'shortTextType'     => __( 'Short Text', $this->plugin_name),
            'true'              => __( 'True', $this->plugin_name),
            'false'             => __( 'False', $this->plugin_name),
            'number'            => __( 'Number', $this->plugin_name),
            'trueOrFalse'       => __( 'True/False', $this->plugin_name),
            'date'              => __( 'Date', $this->plugin_name),
            'currentTime'       => current_time( 'Y-m-d' ),
            'nextQustionPage'   => __( 'Are you sure you want to go to the next question page?', $this->plugin_name),
            'areYouSureButton'  => __( 'Are you sure you want to redirect to another quiz? Note that the changes made in this quiz will not be saved.', $this->plugin_name),
            'deleteQuestion'    => __( 'Are you sure you want to delete question ?', $this->plugin_name),
        ) );

        $question_categories = $this->get_question_categories();
        wp_localize_script( $this->plugin_name, 'aysQuizCatObj', array(
            'category' => $question_categories,
        ) );

    }

    function codemirror_enqueue_scripts($hook) {
        if (false === strpos($hook, $this->plugin_name)){
            return;
        }
        if(function_exists('wp_enqueue_code_editor')){
            $cm_settings['codeEditor'] = wp_enqueue_code_editor(array(
                'type' => 'text/css',
                'codemirror' => array(
                    'inputStyle' => 'contenteditable',
                    'theme' => 'cobalt',
                )
            ));

            wp_enqueue_script('wp-theme-plugin-editor');
            wp_localize_script('wp-theme-plugin-editor', 'cm_settings', $cm_settings);
        
            wp_enqueue_style('wp-codemirror');
        }
    }

    function versionCompare($version1, $operator, $version2) {
   
        $_fv = intval ( trim ( str_replace ( '.', '', $version1 ) ) );
        $_sv = intval ( trim ( str_replace ( '.', '', $version2 ) ) );
       
        if (strlen ( $_fv ) > strlen ( $_sv )) {
            $_sv = str_pad ( $_sv, strlen ( $_fv ), 0 );
        }
       
        if (strlen ( $_fv ) < strlen ( $_sv )) {
            $_fv = str_pad ( $_fv, strlen ( $_sv ), 0 );
        }
       
        return version_compare ( ( string ) $_fv, ( string ) $_sv, $operator );
    }


    /**
     * Register the administration menu for this plugin into the WordPress Dashboard menu.
     *
     * @since    1.0.0
     */
    public function add_plugin_admin_menu(){

        /*
         * Add a settings page for this plugin to the Settings menu.
         *
         * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
         *
         *        Administration Menus: http://codex.wordpress.org/Administration_Menus
         *
         */
        global $wpdb;
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_reports WHERE `read` = 0";
        $unread_results_count = $wpdb->get_var($sql);        
        $menu_item = ($unread_results_count == 0) ? 'Quiz Maker' : 'Quiz Maker' . '<span class="ays_menu_badge ays_results_bage">' . $unread_results_count . '</span>';
        
        add_menu_page(
            'Quiz Maker', 
            $menu_item, 
            'manage_options', 
            $this->plugin_name, 
            array($this, 'display_plugin_quiz_page'), 
            AYS_QUIZ_ADMIN_URL . '/images/icons/icon-128x128.png', 
            '6.20'
        );
    }
    
    public function add_plugin_quizzes_submenu(){
        $hook_quiz_maker = add_submenu_page(
            $this->plugin_name,
            __('Quizzes', $this->plugin_name),
            __('Quizzes', $this->plugin_name),
            'manage_options',
            $this->plugin_name,
            array($this, 'display_plugin_quiz_page')
        );

        add_action("load-$hook_quiz_maker", array($this, 'screen_option_quizes'));
        add_action("load-$hook_quiz_maker", array( $this, 'add_tabs' ));
    }

    public function add_plugin_questions_submenu(){
        $hook_questions = add_submenu_page(
            $this->plugin_name,
            __('Questions', $this->plugin_name),
            __('Questions', $this->plugin_name),
            'manage_options',
            $this->plugin_name . '-questions',
            array($this, 'display_plugin_questions_page')
        );

        add_action("load-$hook_questions", array($this, 'screen_option_questions'));
        add_action("load-$hook_questions", array( $this, 'add_tabs' ));
    }

    public function add_plugin_quiz_categories_submenu(){
        $hook_quiz_categories = add_submenu_page(
            $this->plugin_name,
            __('Quiz Categories', $this->plugin_name),
            __('Quiz Categories', $this->plugin_name),
            'manage_options',
            $this->plugin_name . '-quiz-categories',
            array($this, 'display_plugin_quiz_categories_page')
        );

        add_action("load-$hook_quiz_categories", array($this, 'screen_option_quiz_categories'));
        add_action("load-$hook_quiz_categories", array( $this, 'add_tabs' ));
    }

    public function add_plugin_questions_categories_submenu(){
        $hook_questions_categories = add_submenu_page(
            $this->plugin_name,
            __('Question Categories', $this->plugin_name),
            __('Question Categories', $this->plugin_name),
            'manage_options',
            $this->plugin_name . '-question-categories',
            array($this, 'display_plugin_question_categories_page')
        );

        add_action("load-$hook_questions_categories", array($this, 'screen_option_questions_categories'));
        add_action("load-$hook_questions_categories", array( $this, 'add_tabs' ));
    }

    public function add_plugin_custom_fields_submenu(){
        $hook_quiz_categories = add_submenu_page(
            $this->plugin_name,
            __('Custom Fields', $this->plugin_name),
            __('Custom Fields', $this->plugin_name),
            'manage_options',
            $this->plugin_name . '-quiz-attributes',
            array($this, 'display_plugin_quiz_attributes_page')
        );
        add_action("load-$hook_quiz_categories", array( $this, 'add_tabs' ));
    }

    public function add_plugin_orders_submenu(){
        $hook_quiz_orders = add_submenu_page(
            $this->plugin_name,
            __('Orders', $this->plugin_name),
            __('Orders', $this->plugin_name),
            'manage_options',
            $this->plugin_name . '-quiz-orders',
            array($this, 'display_plugin_orders_page')
        );
        add_action("load-$hook_quiz_orders", array( $this, 'add_tabs' ));
    }

    public function add_plugin_results_submenu(){
        global $wpdb;
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_reports WHERE `read` = 0";
        $unread_results_count = $wpdb->get_var($sql);
        $results_text = __('Results', $this->plugin_name);
        $menu_item = ($unread_results_count == 0) ? $results_text : $results_text . '<span class="ays_menu_badge ays_results_bage">' . $unread_results_count . '</span>';
        $hook_results = add_submenu_page(
            $this->plugin_name,
            $results_text,
            $menu_item,
            'manage_options',
            $this->plugin_name . '-results',
            array($this, 'display_plugin_results_page')
        );

        add_action("load-$hook_results", array($this, 'screen_option_results'));
        add_action("load-$hook_results", array( $this, 'add_tabs' ));

        $hook_all_reviews = add_submenu_page(
            'all_reviews_slug',
            __('Reviews', $this->plugin_name),
            null,
            'manage_options',
            $this->plugin_name . '-all-reviews',
            array($this, 'display_plugin_all_reviews_page')
        );

        add_action("load-$hook_all_reviews", array($this, 'screen_option_all_quiz_reviews'));
        add_action("load-$hook_all_reviews", array( $this, 'add_tabs' ));

        add_filter('parent_file', array($this,'quiz_maker_select_submenu'));
    }

    public function add_plugin_dashboard_submenu(){
        $hook_quizes = add_submenu_page(
            $this->plugin_name,
            __('How to use', $this->plugin_name),
            __('How to use', $this->plugin_name),
            'manage_options',
            $this->plugin_name . '-dashboard',
            array($this, 'display_plugin_setup_page')
        );
        add_action("load-$hook_quizes", array( $this, 'add_tabs' ));
    }

    public function add_plugin_general_settings_submenu(){
        $hook_settings = add_submenu_page( $this->plugin_name,
            __('General Settings', $this->plugin_name),
            __('General Settings', $this->plugin_name),
            'manage_options',
            $this->plugin_name . '-settings',
            array($this, 'display_plugin_settings_page') 
        );
        add_action("load-$hook_settings", array($this, 'screen_option_settings'));
        add_action("load-$hook_settings", array( $this, 'add_tabs' ));
    }

    public function add_plugin_featured_plugins_submenu(){
        $hook_our_products = add_submenu_page( $this->plugin_name,
            __('Our products', $this->plugin_name),
            __('Our products', $this->plugin_name),
            'manage_options',
            $this->plugin_name . '-featured-plugins',
            array($this, 'display_plugin_featured_plugins_page') 
        );

        add_action("load-$hook_our_products", array( $this, 'add_tabs' ));
    }

    public function add_plugin_quiz_features_submenu(){
        $hook_pro_features = add_submenu_page(
            $this->plugin_name,
            __('PRO Features', $this->plugin_name),
            __('PRO Features', $this->plugin_name),
            'manage_options',
            $this->plugin_name . '-quiz-features',
            array($this, 'display_plugin_quiz_features_page')
        );

        add_action("load-$hook_pro_features", array( $this, 'add_tabs' ));
    }

    public function add_plugin_subscribe_email(){
        $hook_grab_your_gift = add_submenu_page(
            $this->plugin_name,
            __('Grab your GIFT', $this->plugin_name),
            __('Grab your GIFT', $this->plugin_name),
            'manage_options',
            $this->plugin_name . '-quiz-subscribe-email',
            array($this, 'display_plugin_subscribe_email')
        );

        add_action("load-$hook_grab_your_gift", array( $this, 'add_tabs' ));
    }

    public function add_plugin_integrations_submenu(){
        $hook_integrations = add_submenu_page( $this->plugin_name,
            __('Integrations', $this->plugin_name),
            __('Integrations', $this->plugin_name),
            'manage_options',
            $this->plugin_name . '-integrations',
            array($this, 'display_plugin_integrations_page') 
        );
        
        // add_action("load-$hook_integrations", array($this, 'screen_option_settings'));
        add_action("load-$hook_integrations", array( $this, 'add_tabs' ));
    }

    public function quiz_maker_select_submenu($file) {
        global $plugin_page;
        if ( $this->plugin_name . "-all-reviews" == $plugin_page ) {
            $plugin_page = $this->plugin_name."-results";
        }

        return $file;
    }

    /**
     * Add settings action link to the plugins page.
     *
     * @since    1.0.0
     */
    public function add_action_links($links){
        /*
        *  Documentation : https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
        */
        $settings_link = array(
            '<a href="' . admin_url('admin.php?page=' . $this->plugin_name) . '">' . __('Settings', $this->plugin_name) . '</a>',
            '<a href="https://quiz-plugin.com/wordpress-quiz-plugin-free-demo/" target="_blank">' . __('Demo', $this->plugin_name) . '</a>',
            '<a href="https://ays-pro.com/wordpress/quiz-maker" target="_blank" style="color:red;font-weight:bold;">' . __('Buy Now', $this->plugin_name) . '</a>',
            );
        return array_merge($settings_link, $links);

    }

    public function add_quiz_row_meta( $links, $file ) {
        if ( AYS_QUIZ_BASENAME == $file ) {
            $row_meta = array(
                'ays-quiz-support'    => '<a href="' . esc_url( 'https://wordpress.org/support/plugin/quiz-maker/' ) . '" target="_blank">' . esc_html__( 'Free Support', $this->plugin_name ) . '</a>'
                );

            return array_merge( $links, $row_meta );
        }
        return $links;
    }

    /**
     * Render the settings page for this plugin.
     *
     * @since    1.0.0
     */
    public function display_plugin_setup_page(){
        include_once('partials/quiz-maker-admin-display.php');
    }

    public function display_plugin_quiz_categories_page(){
        $action = (isset($_GET['action'])) ? sanitize_text_field($_GET['action']) : '';

        switch ($action) {
            case 'add':
                include_once('partials/quizes/actions/quiz-maker-quiz-categories-actions.php');
                break;
            case 'edit':
                include_once('partials/quizes/actions/quiz-maker-quiz-categories-actions.php');
                break;
            default:
                include_once('partials/quizes/quiz-maker-quiz-categories-display.php');
        }
    }
    
    public function display_plugin_quiz_attributes_page(){
        include_once('partials/attributes/quiz-maker-attributes-display.php');
    }
    
    public function display_plugin_quiz_page(){
        $action = (isset($_GET['action'])) ? sanitize_text_field($_GET['action']) : '';

        switch ($action) {
            case 'add':
                include_once('partials/quizes/actions/quiz-maker-quizes-actions.php');
                break;
            case 'edit':
                include_once('partials/quizes/actions/quiz-maker-quizes-actions.php');
                break;
            default:
                include_once('partials/quizes/quiz-maker-quizes-display.php');
        }
    }

    public function display_plugin_question_categories_page(){
        $action = (isset($_GET['action'])) ? sanitize_text_field($_GET['action']) : '';

        switch ($action) {
            case 'add':
                include_once('partials/questions/actions/quiz-maker-questions-categories-actions.php');
                break;
            case 'edit':
                include_once('partials/questions/actions/quiz-maker-questions-categories-actions.php');
                break;
            default:
                include_once('partials/questions/quiz-maker-question-categories-display.php');
        }
    }

    public function display_plugin_questions_page(){
        $action = (isset($_GET['action'])) ? sanitize_text_field($_GET['action']) : '';

        switch ($action) {
            case 'add':
                include_once('partials/questions/actions/quiz-maker-questions-actions.php');
                break;
            case 'edit':
                include_once('partials/questions/actions/quiz-maker-questions-actions.php');
                break;
            default:
                include_once('partials/questions/quiz-maker-questions-display.php');
        }
    }
    
    public function display_plugin_orders_page(){

        include_once('partials/orders/quiz-maker-orders-display.php');
    }
    
    public function display_plugin_settings_page(){        
        include_once('partials/settings/quiz-maker-settings.php');
    }

    public function display_plugin_quiz_features_page(){
        include_once('partials/features/quiz-maker-features-display.php');
    }

    public function display_plugin_subscribe_email(){
        include_once('partials/subscribe/quiz-maker-subscribe-email-display.php');
    }

    public function display_plugin_featured_plugins_page(){
        include_once('partials/features/quiz-maker-plugin-featured-display.php');
    }

    public function display_plugin_results_page(){
        include_once('partials/results/quiz-maker-results-display.php');
    }

    public function display_plugin_all_reviews_page(){
        include_once('partials/results/quiz-maker-all-reviews-display.php');
    }

    public function display_plugin_integrations_page(){        
        include_once('partials/integrations/quiz-maker-integrations.php');
    }

    public static function set_screen($status, $option, $value){
        return $value;
    }

    public function screen_option_quizes(){
        $option = 'per_page';
        $args = array(
            'label' => __('Quizzes', $this->plugin_name),
            'default' => 20,
            'option' => 'quizes_per_page'
        );

        if( ! ( isset( $_GET['action'] ) && ( $_GET['action'] == 'add' || $_GET['action'] == 'edit' ) ) ){
            add_screen_option($option, $args);
        }

        $this->quizes_obj = new Quizes_List_Table($this->plugin_name);
        $this->settings_obj = new Quiz_Maker_Settings_Actions($this->plugin_name);
    }

    public function screen_option_quiz_categories(){
        $option = 'per_page';
        $args = array(
            'label' => __('Quiz Categories', $this->plugin_name),
            'default' => 20,
            'option' => 'quiz_categories_per_page'
        );

        if( ! ( isset( $_GET['action'] ) && ( $_GET['action'] == 'add' || $_GET['action'] == 'edit' ) ) ){
            add_screen_option($option, $args);
        }

        $this->quiz_categories_obj = new Quiz_Categories_List_Table($this->plugin_name);
        $this->settings_obj = new Quiz_Maker_Settings_Actions($this->plugin_name);
    }

    public function screen_option_questions(){
        $option = 'per_page';
        $args = array(
            'label' => __('Questions', $this->plugin_name),
            'default' => 20,
            'option' => 'questions_per_page'
        );

        if( ! ( isset( $_GET['action'] ) && ( $_GET['action'] == 'add' || $_GET['action'] == 'edit' ) ) ){
            add_screen_option($option, $args);
        }

        $this->questions_obj = new Questions_List_Table($this->plugin_name);
        $this->settings_obj = new Quiz_Maker_Settings_Actions($this->plugin_name);
    }

    public function screen_option_questions_categories(){
        $option = 'per_page';
        $args = array(
            'label' => __('Question Categories', $this->plugin_name),
            'default' => 20,
            'option' => 'question_categories_per_page'
        );

        if( ! ( isset( $_GET['action'] ) && ( $_GET['action'] == 'add' || $_GET['action'] == 'edit' ) ) ){
            add_screen_option($option, $args);
        }
        
        $this->question_categories_obj = new Question_Categories_List_Table($this->plugin_name);
        $this->settings_obj = new Quiz_Maker_Settings_Actions($this->plugin_name);
    }

    public function screen_option_results(){
        $option = 'per_page';
        $args = array(
            'label' => __('Results', $this->plugin_name),
            'default' => 50,
            'option' => 'quiz_results_per_page'
        );

        add_screen_option($option, $args);
        $this->results_obj = new Results_List_Table($this->plugin_name);
    }

     public function screen_option_all_quiz_reviews(){
        $option = 'per_page';
        $args = array(
            'label' => __('All Reviews', $this->plugin_name),
            'default' => 50,
            'option' => 'quiz_all_reviews_per_page'
        );

        add_screen_option($option, $args);
        $this->all_reviews_obj = new All_Reviews_List_Table($this->plugin_name);
    }

    public function screen_option_settings(){
        $this->settings_obj = new Quiz_Maker_Settings_Actions($this->plugin_name);
    }

    /**
     * Adding questions from modal to table
     */
    public function add_question_rows(){
        if ( (isset($_REQUEST["action"]) && $_REQUEST["action"] == "add_question_rows") || (isset($_REQUEST["action"]) && $_REQUEST["action"] == "add_question_rows_top") ) {

            $question_ids = (isset($_REQUEST["ays_questions_ids"]) && !empty($_REQUEST["ays_questions_ids"])) ? array_map( 'sanitize_text_field', $_REQUEST['ays_questions_ids'] ) : array();

            $rows = array();
            $ids = array();
            if (!empty($question_ids)) {
                $question_categories = $this->get_questions_categories();
                $question_categories_array = array();
                foreach($question_categories as $cat){
                    $question_categories_array[$cat['id']] = $cat['title'];
                }
                foreach ($question_ids as $question_id) {
                    $data = $this->get_published_questions_by('id', absint(intval($question_id)));
                    $table_question = (strip_tags(stripslashes($data['question'])));
                    if(isset($data['question']) && strlen($data['question']) != 0){

                        $is_exists_ruby = Quiz_Maker_Admin::ays_quiz_is_exists_needle_tag( $data['question'] , '<ruby>' );

                        if ( $is_exists_ruby ) {
                            $table_question = strip_tags( stripslashes($data['question']), '<ruby><rbc><rtc><rb><rt>' );
                        } else {
                            $table_question = strip_tags(stripslashes($data['question']));
                        }

                    }elseif ((isset($data['question_image']) && $data['question_image'] !='')){
                        $table_question = 'Image question';
                    }

                    switch ( $data['type'] ) {
                        case 'short_text':
                            $ays_question_type = 'short text';
                            break;
                        case 'true_or_false':
                            $ays_question_type = 'true/false';
                            break;
                        default:
                            $ays_question_type = $data['type'];
                            break;
                    }

                    $table_question = $this->ays_restriction_string("word", $table_question, 8);
                    $edit_question_url = "?page=".$this->plugin_name."-questions&action=edit&question=".$data['id'];
                    $rows[] = '<tr class="ays-question-row ui-state-default" data-id="' . $data['id'] . '">
                        <td class="ays-sort"><i class="ays_fa ays_fa_arrows" aria-hidden="true"></i></td>
                        <td>                        
                            <a href="'. $edit_question_url .'" target="_blank" class="ays-edit-question" title="'. __('Edit question', $this->plugin_name) .'">
                                ' . $table_question . '
                            </a> 
                        </td>
                        <td>' . $ays_question_type . '</td>
                        <td>' . $question_categories_array[$data['category_id']] . '</td>
                        <td>' . stripslashes($data['id']) . '</td>
                        <td>
                            <input type="checkbox" class="ays_del_tr">
                            <a href="'. $edit_question_url .'" target="_blank" class="ays-edit-question" title="'. __('Edit question', $this->plugin_name) .'">
                                <i class="ays_fa ays_fa_pencil_square" aria-hidden="true"></i>
                            </a>
                            <a href="javascript:void(0)" class="ays-delete-question" data-id="' . $data['id'] . '">
                                <i class="ays_fa ays_fa_minus_square" aria-hidden="true"></i>
                            </a>
                        </td>
                   </tr>';
                    $ids[] = $data['id'];
                }
                ob_end_clean();
                $ob_get_clean = ob_get_clean();
                echo json_encode(array(
                    'status' => true,
                    'rows' => $rows,
                    'ids' => $ids
                ));
                wp_die();
            } else {
                ob_end_clean();
                $ob_get_clean = ob_get_clean();
                echo json_encode(array(
                    'status' => true,
                    'rows' => '',
                    'ids' => array()
                ));
                wp_die();
            }
        } else {
            ob_end_clean();
            $ob_get_clean = ob_get_clean();
            echo json_encode(array(
                'status' => true,
                'rows' => '',
                'ids' => array()
            ));
            wp_die();
        }
    }

    /**
     * @return string
     */
    public function ays_quick_start(){
        global $wpdb;

        $quiz_title = stripslashes( sanitize_text_field( $_REQUEST['ays_quiz_title'] ) );
        $quiz_description = (isset( $_REQUEST['ays_quick_quiz_description'] ) && $_REQUEST['ays_quick_quiz_description'] != "") ? stripslashes( wp_kses_post( $_REQUEST['ays_quick_quiz_description'] ) ) : "";
        $quiz_cat_id = sanitize_text_field( $_REQUEST['ays_quiz_category'] );
        $questions = array_map( 'sanitize_text_field', $_REQUEST['ays_quick_question'] );
        $questions_type = array_map( 'sanitize_text_field', $_REQUEST['ays_quick_question_type'] );
        $questions_cat = array_map( 'sanitize_text_field', $_REQUEST['ays_quick_question_cat'] );
        $answers_correct = self::recursive_sanitize_text_field( $_REQUEST['ays_quick_answer_correct'] );
        $answers = self::recursive_sanitize_text_field( $_REQUEST['ays_quick_answer'] );

        $answers_table = esc_sql( $wpdb->prefix . 'aysquiz_answers' );
        $questions_table = esc_sql( $wpdb->prefix . 'aysquiz_questions' );
        $quizes_table = esc_sql( $wpdb->prefix . 'aysquiz_quizes' );

        $max_id = $this->get_max_id('quizes');
        $ordering = ( $max_id != NULL ) ? ( $max_id + 1 ) : 1;
        
        $questions_ids = '';
        $create_date = current_time( 'mysql' );
        $user_id = get_current_user_id();
        $user = get_userdata($user_id);
        $author = array(
            'id' => $user->ID,
            'name' => $user->data->display_name
        );
        $options = array(
            'author' => $author,
        );
        foreach ($questions as $question_key => $question) {

            $cat_key = array_search( $question_key, $questions_cat );
            $q_category_id = (isset( $questions_cat[$cat_key] ) && $questions_cat[$cat_key] != "") ? esc_sql( $questions_cat[$cat_key] ) : 1;

            if ( !isset( $questions_type[$question_key] ) || is_null( $questions_type[$question_key] ) ) {
                continue;
            }

            $wpdb->insert($questions_table, array(
                'category_id'   => $q_category_id,
                'question'      => esc_sql( stripslashes($question) ),
                'published'     => 1,
                'type'          => $questions_type[$question_key],
                'create_date'   => esc_sql( $create_date ),
                'options'       => json_encode($options)
            ));
            $question_id = $wpdb->insert_id;
            $questions_ids .= $question_id . ',';
            if ( isset( $answers[$question_key] ) && ! empty( $answers[$question_key] ) ) {

                foreach ($answers[$question_key] as $key => $answer) {
                    $type = $questions_type[$question_key];

                    if($type == "text" || $type == "short_text"){
                        $correct = 1;
                    }else{
                        $correct = ($answers_correct[$question_key][$key] == "true") ? 1 : 0;
                    }
                    $placeholder = '';

                    $wpdb->insert($answers_table, array(
                        'question_id' => esc_sql( $question_id ),
                        'answer' => esc_sql( trim( stripslashes($answer) ) ),
                        'correct' => $correct,
                        'ordering' => $key,
                        'placeholder' => $placeholder
                    ));
                }
            }
        }
        $questions_ids = rtrim($questions_ids, ",");
        $wpdb->insert($quizes_table, array(
            'title' => $quiz_title,//esc_sql( $quiz_title ),
            'description' => $quiz_description,//esc_sql( $quiz_description ),
            'question_ids' => $questions_ids,
            'published' => 1,
            'options' => json_encode(array(
                'quiz_theme' => 'classic_light',
                'color' => '#27AE60',
                'bg_color' => '#fff',
                'text_color' => '#000',
                'height' => 350,
                'width' => 400,
                'timer' => 100,
                'information_form' => 'disable',
                'form_name' => '',
                'form_email' => '',
                'form_phone' => '',
                'enable_logged_users' => 'off',
                'image_width' => '',
                'image_height' => '',
                'enable_correction' => 'off',
                'enable_questions_counter' => 'on',
                'limit_users' => 'off',
                'limitation_message' => '',
                'redirect_url' => '',
                'redirection_delay' => '',
                'enable_progress_bar' => 'on',
                'randomize_questions' => 'off',
                'randomize_answers' => 'off',
                'enable_questions_result' => 'on',
                'enable_average_statistical' => 'on',
                'enable_next_button' => 'on',
                'enable_previous_button' => 'on',
                'custom_css' => '',
                'enable_restriction_pass' => 'off',
                'restriction_pass_message' => '',
                'user_role' => '',
                'result_text' => '',
                'enable_result' => 'off',
                'enable_timer' => 'off',
                'enable_pass_count' => 'on',
                'enable_quiz_rate' => 'off',
                'enable_rate_avg' => 'off',
                'enable_rate_comments' => 'off',
                'hide_score' => 'off',
                'rate_form_title' => '',
                'enable_box_shadow' => 'on',
                'box_shadow_color' => '#000',
                'quiz_border_radius' => '0',
                'quiz_bg_image' => '',
                'enable_border' => 'off',
                'quiz_border_width' => '1',
                'quiz_border_style' => 'solid',
                'quiz_border_color' => '#000',
                'quiz_timer_in_title' => 'off',
                'enable_restart_button' => 'on',
                'quiz_loader' => 'default',
                'create_date' => $create_date,
                'author' => $author,
                'autofill_user_data' => 'off',
                'quest_animation' => 'shake',
                'form_title' => '',
                'enable_bg_music' => 'off',
                'quiz_bg_music' => '',
                'answers_font_size' => '15',
                'show_create_date' => 'on',
                'show_author' => 'on',
                'enable_early_finish' => 'off',
                'answers_rw_texts' => 'on_passing',
                'disable_store_data' => 'off',
                'enable_background_gradient' => 'off',
                'background_gradient_color_1' => '#000',
                'background_gradient_color_2' => '#fff',
                'quiz_gradient_direction' => 'vertical',
                'redirect_after_submit' => 'off',
                'submit_redirect_url' => '',
                'submit_redirect_delay' => '',
                'progress_bar_style' => 'second',
                'enable_exit_button' => 'off',
                'exit_redirect_url' => '',
                'image_sizing' => 'cover',
                'quiz_bg_image_position' => 'center center',
                'custom_class' => '',
                'enable_social_buttons' => 'on',
                'enable_social_links' => 'off',
                'social_links' => array(
                    'linkedin_link' => '',
                    'facebook_link' => '',
                    'twitter_link' => ''
                ),
                'show_quiz_title' => 'on',
                'show_quiz_desc' => 'on',
                'show_login_form' => 'off',
                'mobile_max_width' => '',
                'limit_users_by' => 'ip',
                'activeInterval' => '',
                'deactiveInterval' => '',
                'active_date_check' => 'off',
                'active_date_message' => __("The quiz has expired!", $this->plugin_name),
                'explanation_time' => '4',
                'enable_clear_answer' => 'off',
                'enable_enter_key' => 'on',
                'enable_leave_page' => 'on',
            )),
            'quiz_category_id' => $quiz_cat_id,
            'ordering' => $ordering,
        ));
        $quiz_id = $wpdb->insert_id;
        ob_end_clean();
        $ob_get_clean = ob_get_clean();
        echo json_encode(array(
            'status' => true,
            'quiz_id' => $quiz_id
        ));
        wp_die();
    }
    
    /**
     * Recursive sanitation for an array
     * 
     * @param $array
     *
     * @return mixed
     */
    public static function recursive_sanitize_text_field($array) {
        foreach ( $array as $key => &$value ) {
            if ( is_array( $value ) ) {
                $value = self::recursive_sanitize_text_field($value);
            } else {
                $value = sanitize_text_field( $value );
            }
        }

        return $array;
    }
    
    public static function get_max_id($table) {
        global $wpdb;
        $quiz_table = $wpdb->prefix . 'aysquiz_'.$table;

        $sql = "SELECT max(id) FROM {$quiz_table}";

        $result = intval($wpdb->get_var($sql));

        return $result;
    }
    
    public function ays_show_results(){
        global $wpdb;
        $results_table = $wpdb->prefix . "aysquiz_reports";
        $questions_table = $wpdb->prefix . "aysquiz_questions";

        if (isset($_REQUEST['action']) && sanitize_text_field( $_REQUEST['action'] ) == 'ays_show_results') {
            $id = absint(intval($_REQUEST['result']));
            $results = $wpdb->get_row("SELECT * FROM {$results_table} WHERE id={$id}", "ARRAY_A");            
            $score = $results['score'];
            // $user_id = intval($results['user_id']);
            $user_id = isset($results['user_id']) ? intval($results['user_id']) : null;
            $quiz_id = isset($results['quiz_id']) ? intval($results['quiz_id']) : null;
            
            $user = get_user_by('id', $user_id);
            
            $user_ip = $results['user_ip'];
            $options = json_decode($results['options']);
            $user_attributes = isset( $options->attributes_information ) ? $options->attributes_information : null;
            $start_date = $results['start_date'];
            $duration = isset( $options->passed_time ) ? $options->passed_time : '';
            $rate_id = isset($options->rate_id) ? $options->rate_id : null;
            $rate = $this->ays_quiz_rate($rate_id);
            $calc_method = isset($options->calc_method) ? $options->calc_method : 'by_correctness';
            
            $from = self::get_user_country_by_ip( $user_ip );

            $note_text = ( isset($options->note_text) && $options->note_text != '' ) ? sanitize_text_field( stripslashes( $options->note_text ) ) : '';
            
            $row = "<table id='ays-results-table'>";

            $row .= '<tr class="ays_result_element">
                        <td colspan="3">
                            <div class="ays-quiz-admin-note">
                                <div class="ays-quiz-click-for-admin-note">
                                    <a href="https://ays-pro.com/wordpress/quiz-maker/" target="_blank" class="ays-pro-a">
                                        <button class="button button-primary disabled-button" style="color:#ffffff !important; font-weight:normal;" title="This property aviable only in pro version">'
                                            .__( 'Click For Admin Note', $this->plugin_name ).
                                        '</button>
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>';
            
            $row .= '<tr class="ays_result_element">
                        <td colspan="3"><h1>' . __('User Information',$this->plugin_name) . '</h1></td>
                        <td style="text-align: right;">
                            <span style="min-width: 70px;">'.__("Export to", $this->plugin_name).'</span>
                            <a href="https://ays-pro.com/wordpress/quiz-maker/" target="_blank" class="ays-pro-a">
                                <span type="button" class="disabled-button" title="This property aviable only in pro version">'.__("PDF", $this->plugin_name).'</span>
                            </a> 
                            <a href="https://ays-pro.com/wordpress/quiz-maker/" target="_blank" class="ays-pro-a">
                                <span type="button" class="disabled-button" title="This property aviable only in pro version">'.__("XLSX", $this->plugin_name).'</span>
                            </a>    
                        </td>
                    </tr>';       
            
            if ($user_ip != '') {
                $row .= '<tr class="ays_result_element">
                            <td>User IP</td>
                            <td colspan="3">' . $from . '</td>
                        </tr>';
            }
            
            $user_name = $user_id === 0 ? __( "Guest", $this->plugin_name ) : $user->data->display_name;
            if($user_id !== 0){
                $row .= '<tr class="ays_result_element">
                        <td>User ID</td>
                        <td colspan="3">' . $user_id . '</td>                    
                    </tr>';
            }
            $row .= '<tr class="ays_result_element">
                    <td>User</td>
                    <td colspan="3">' . $user_name . '</td>
                </tr>';
            
            if(isset($results['user_email']) && $results['user_email'] !== ''){
                $row .= "<tr class=\"ays_result_element\">
                        <td>".__('Email',$this->plugin_name)."</td>
                        <td colspan='3'>".stripslashes($results['user_email'])."</td>
                     </tr>";
            }
            if(isset($results['user_name']) && $results['user_name'] !== ''){
                $row .= "<tr class=\"ays_result_element\">
                        <td>".__('Name',$this->plugin_name)."</td>
                        <td colspan='3'>".stripslashes($results['user_name'])."</td>
                     </tr>";
            }
            if(isset($results['user_phone']) && $results['user_phone'] !== ''){
                $row .= "<tr class=\"ays_result_element\">
                        <td>".__('Phone',$this->plugin_name)."</td>
                        <td colspan='3'>".stripslashes($results['user_phone'])."</td>
                     </tr>";
            }
            if ($user_attributes !== null) {

                foreach ($user_attributes as $name => $value) {
                    $attr_value = stripslashes($value) == '' ? '-' : stripslashes($value);
                    $row .= '<tr class="ays_result_element">
                            <td>' . stripslashes($name) . '</td>
                            <td colspan="3">' . $attr_value . '</td>
                        </tr>';
                }
            }
            
            $row .= '<tr class="ays_result_element">
                        <td colspan="4"><h1>' . __('Quiz Information',$this->plugin_name) . '</h1></td>
                    </tr>';
            if(isset($rate['score'])){
                $rate_html = '<tr style="vertical-align: top;" class="ays_result_element">
                    <td>'.__('Rate',$this->plugin_name).'</td>
                    <td>'. __("Rate Score", $this->plugin_name).":<br>" . $rate['score'] . '</td>
                    <td colspan="2" style="max-width: 200px;">'. __("Review", $this->plugin_name).":<br>" . $rate['review'] . '</td>
                </tr>';
            }else{
                $rate_html = '<tr class="ays_result_element">
                    <td>'.__('Rate',$this->plugin_name).'</td>
                    <td colspan="3">' . $rate['review'] . '</td>
                </tr>';
            }
            $row .= '<tr class="ays_result_element">
                        <td>'.__('Start date',$this->plugin_name).'</td>
                        <td colspan="3">' . $start_date . '</td>
                    </tr>                        
                    <tr class="ays_result_element">
                        <td>'.__('Duration',$this->plugin_name).'</td>
                        <td colspan="3">' . $duration . '</td>
                    </tr>
                    <tr class="ays_result_element">
                        <td>'.__('Score',$this->plugin_name).'</td>
                        <td colspan="3">' . $score . '%</td>
                    </tr>'.$rate_html;

                    
            if(! empty($options->correctness)){
                $row .= '<tr class="ays_result_element">
                            <td colspan="4"><h1>' . __('Questions',$this->plugin_name) . '</h1></td>
                        </tr>';                
                $index = 1;
                //$user_exp = array();
                //if( isset( $results['user_explanation'] ) && $results['user_explanation'] != '' || $results['user_explanation'] !== null){
                //    $user_exp = json_decode($results['user_explanation'], true);
                //}
                
                foreach ($options->correctness as $key => $option) {
                    if (strpos($key, 'question_id_') !== false) {
                        $question_id = absint(intval(explode('_', $key)[2]));
                        $question = $wpdb->get_row("SELECT * FROM {$questions_table} WHERE id={$question_id}", "ARRAY_A");
                        
                        if ( is_null( $question ) || empty( $question )  ) {
                            continue;
                        }

                        $qoptions = isset($question['options']) && $question['options'] != '' ? json_decode($question['options'], true) : array();
                        $use_html = isset($qoptions['use_html']) && $qoptions['use_html'] == 'on' ? true : false;
                        $correct_answers = $this->get_correct_answers($question_id);
                        $is_text_type = $this->question_is_text_type($question_id);
                        $text_type = $this->text_answer_is($question_id);
                        $not_multiple_text_types = array("number", "date");
                        if($text_type){
                            $user_answered = $this->get_user_text_answered($options->user_answered, $key);                            
                        }else{
                            $user_answered = $this->get_user_answered($options->user_answered, $key);
                        }
                        $ans_point = $option;
                        $ans_point_class = 'success';
                        if(is_array($user_answered)){
                            $user_answered = $user_answered['message'];
                            $ans_point = '-';
                            $ans_point_class = 'error';
                        }
                        $tr_class = "ays_result_element";
                        //if(isset($user_exp[$question_id])){
                        //    $tr_class = "";
                        //}

                        $not_influance_check = isset($question['not_influence_to_score']) && $question['not_influence_to_score'] == 'on' ? false : true;
                        if (!$not_influance_check) {
                            $not_influance_check_td = ' colspan="2" ';
                        }else{
                            $not_influance_check_td = '';                            
                        }
                        $question_image = isset( $question["question_image"] ) && $question["question_image"] != '' ? $question["question_image"] : '';
                        $question_title = isset( $question["question"] ) && $question["question"] != '' ? $question["question"] : '';
                        if($calc_method == 'by_correctness'){
                            if ($option == true) {
                                $row .= '<tr class="'.$tr_class.'">
                                            <td>'.__('Question',$this->plugin_name).' ' . $index . ' :<br/>';
                                if( $question_image != '' ){
                                    $row .= '<img class="ays-quiz-question-image-in-report" src="' . $question_image . '"><br/>';
                                }
                                $row .= (stripslashes($question["question"])) . 
                                    '</td>';
                                if($is_text_type && ! in_array($text_type, $not_multiple_text_types)){
                                    $c_answers = explode('%%%', $correct_answers);
                                    $c_answer = $c_answers[0];
                                    foreach($c_answers as $c_ans){
                                        if(strtolower(trim($user_answered)) == strtolower(trim($c_ans))){
                                            $c_answer = $c_ans;
                                            break;
                                        }
                                    }
                                    $row .='<td>'.__('Correct answer',$this->plugin_name).':<br/>';
                                    $row .= '<p class="success">' . esc_attr(stripslashes($c_answer)) . '</p>';
                                    $row .='</td>';
                                }else{
                                    if($text_type == 'date'){
                                        $correct_answers = date( 'm/d/Y', strtotime( $correct_answers ) );
                                    }
                                    $correct_answer_content = esc_attr( stripslashes( $correct_answers ) );
                                    if($use_html){
                                        $correct_answer_content = stripslashes( $correct_answers );
                                    }
                                    $row .='<td>'.__('Correct answer',$this->plugin_name).':<br/><p class="success">' . $correct_answer_content . '</p></td>';
                                }
                                
                                if($text_type == 'date'){
                                    if(self::validateDate($user_answered, 'Y-m-d')){
                                        $user_answered = date( 'm/d/Y', strtotime( $user_answered ) );
                                    }
                                }
                                $user_answer_content = esc_attr( stripslashes( $user_answered ) );
                                if($use_html){
                                    $user_answer_content = stripslashes( $user_answered );
                                }
                                $row .='<td '.$not_influance_check_td.'>'.__('User answered',$this->plugin_name).':<br/><p class="success">' . $user_answer_content . '</p></td>';
                                if ($not_influance_check) {
                                    $row .='<td>
                                                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
                                                    <circle class="path circle" fill="none" stroke="#73AF55" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1"/>
                                                    <polyline class="path check" fill="none" stroke="#73AF55" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" points="100.2,40.2 51.5,88.8 29.8,67.5 "/>
                                                </svg>
                                                <p class="success">'.__('Succeed',$this->plugin_name).'!</p>
                                            </td>';
                                }
                                $row .= '</tr>';
                            } else {
                                $row .= '<tr class="'.$tr_class.'">
                                            <td>'.__('Question',$this->plugin_name).' ' . $index . ' :<br/>';
                                if( $question_image != '' ){
                                    $row .= '<img class="ays-quiz-question-image-in-report" src="' . $question_image . '"><br/>';
                                }
                                $row .= (stripslashes($question_title)) . 
                                    '</td>';
                                if($is_text_type && ! in_array($text_type, $not_multiple_text_types)){
                                    $c_answers = explode('%%%', $correct_answers);
                                    $row .= '<td>'.__('Correct answer',$this->plugin_name).':<br/>';
                                    $row .= '<p class="success">' . esc_attr(stripslashes($c_answers[0])) . '</p>';
                                    $row .= '</td>';
                                }else{
                                    if($text_type == 'date'){
                                        $correct_answers = date( 'm/d/Y', strtotime( $correct_answers ) );
                                    }
                                    $correct_answer_content = esc_attr( stripslashes( $correct_answers ) );
                                    if($use_html){
                                        $correct_answer_content = stripslashes( $correct_answers );
                                    }
                                    $row .= '<td>'.__('Correct answer',$this->plugin_name).':<br/><p class="success">' . $correct_answer_content . '</p></td>';
                                }
                                                
                                if($text_type == 'date'){
                                    if(self::validateDate($user_answered, 'Y-m-d')){
                                        $user_answered = date( 'm/d/Y', strtotime( $user_answered ) );
                                    }
                                }
                                $user_answer_content = esc_attr( stripslashes( $user_answered ) );
                                if($use_html){
                                    $user_answer_content = stripslashes( $user_answered );
                                }
                                $row .= '<td '.$not_influance_check_td.'>'.__('User answered',$this->plugin_name).':<br/><p class="error">' . $user_answer_content . '</p></td>';
                                if ($not_influance_check) {
                                    $row .='<td>
                                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
                                            <circle class="path circle" fill="none" stroke="#D06079" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1"/>
                                            <line class="path line" fill="none" stroke="#D06079" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" x1="34.4" y1="37.9" x2="95.8" y2="92.3"/>
                                            <line class="path line" fill="none" stroke="#D06079" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" x1="95.8" y1="38" x2="34.4" y2="92.2"/>
                                        </svg>
                                        <p class="error">'.__('Failed',$this->plugin_name).'!</p>
                                    </td>';
                                }
                                $row .= '</tr>';
                            }
                        }elseif($calc_method == 'by_points'){
                            $row .= '<tr class="'.$tr_class.'">
                                        <td>'.__('Question',$this->plugin_name).' ' . $index . ' :<br/>';
                            if( $question_image != '' ){
                                $row .= '<img class="ays-quiz-question-image-in-report" src="' . $question_image . '"><br/>';
                            }
                            $row .= (stripslashes($question["question"])) . 
                                    '</td>';
                            $row .= '<td>'.__('User answered',$this->plugin_name).':<br/><p class="'.$ans_point_class.'">' . esc_attr(stripslashes($user_answered)) . '</p></td>
                                    <td>'.__('Answer point',$this->plugin_name).':<br/><p class="'.$ans_point_class.'">' . esc_attr($ans_point) . '</p></td>
                                </tr>';
                            
                        }
                        $index++;
                        //if(isset($user_exp[$question_id])){
                        //    $row .= '<tr class="ays_result_element">
                        //        <td>'.__('User explanation for this question',$this->plugin_name).'</td>
                        //        <td colspan="3">'.$user_exp[$question_id].'</td>
                        //    </tr>';
                        //}
                    }
                }
            }
            $row .= "</table>";
            
            $sql = "UPDATE $results_table SET `read`=1 WHERE `id`=$id";
            $wpdb->get_var($sql);

            ob_end_clean();
            $ob_get_clean = ob_get_clean();
            echo json_encode(array(
                "status" => true,
                "rows" => $row
            ));
            wp_die();
        }
    }
    
    protected function ays_quiz_rate( $id ) {
        global $wpdb;
        if($id === '' || $id === null){
            $reason = __("No rate provided", $this->plugin_name);
            $output = array(
                "review" => $reason,
            );
        }else{
            $rate = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}aysquiz_rates WHERE id={$id}", "ARRAY_A");
            $output = array();
            if($rate !== null){
                $review = $rate['review'];
                $reason = stripslashes($review);
                if($reason == ''){
                    $reason = __("No review provided", $this->plugin_name);
                }
                $score = $rate['score'];
                $output = array(
                    "score" => $score,
                    "review" => $reason,
                );
            }else{
                $reason = __("No rate provided", $this->plugin_name);
                $output = array(
                    "review" => $reason,
                );
            }
        }
        return $output;
    }

    public function get_correct_answers($id){
        global $wpdb;
        $answers_table = $wpdb->prefix . "aysquiz_answers";
        $correct_answers = $wpdb->get_results("SELECT answer FROM {$answers_table} WHERE correct=1 AND question_id={$id}");
        $text = "";
        foreach ($correct_answers as $key => $correct_answer) {
            if ($key == (count($correct_answers) - 1))
                $text .= $correct_answer->answer;
            else
                $text .= $correct_answer->answer . ',';
        }
        return $text;
    }

    public function get_user_answered($user_choice, $key){
        global $wpdb;
        $answers_table = $wpdb->prefix . "aysquiz_answers";
        $choices = $user_choice->$key;
        
        if($choices == ''){
            return array(
                'message' => __( "The user has not answered this question.", $this->plugin_name ),
                'status' => false
            );
        }
        $text = array();
        if (is_array($choices)) {
            foreach ($choices as $choice) {
                $result = $wpdb->get_row("SELECT answer FROM {$answers_table} WHERE id={$choice}", 'ARRAY_A');
                $text[] = $result['answer'];
            }
            $text = implode(', ', $text);
        } else {
            $result = $wpdb->get_row("SELECT answer FROM {$answers_table} WHERE id={$choices}", 'ARRAY_A');
            $text = $result['answer'];
        }
        return $text;
    }

    public function get_user_text_answered($user_choice, $key){
        
        if($user_choice->$key == ""){
            $choices = array(
                'message' => __( "The user has not answered this question.", $this->plugin_name ),
                'status' => false
            );
        }else{
            $choices = trim($user_choice->$key);
        }
        
        return $choices;
    }
    
    public function question_is_text_type($question_id){
        global $wpdb;
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $question_id = absint(intval($question_id));
        $text_types = array('text', 'short_text', 'number', 'date');
        $get_answers = $wpdb->get_var("SELECT type FROM {$questions_table} WHERE id={$question_id}");
        if (in_array($get_answers, $text_types)) {
            return $get_answers;
        }
        return false;
    }

    public function text_answer_is($question_id){
        global $wpdb;
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $question_id = absint(intval($question_id));

        $text_types = array('text', 'short_text', 'number', 'date');
        $get_answers = $wpdb->get_var("SELECT type FROM {$questions_table} WHERE id={$question_id}");

        if (in_array($get_answers, $text_types)) {
            return $get_answers;
        }
        return false;
    }

    /**
     * Changes previos version db structure
     */
    public function ays_change_db_questions(){
        global $wpdb;
        $quiz_table = $wpdb->prefix . 'aysquiz_quizes';

        $sql = "SELECT id, question_ids FROM {$quiz_table}";

        $rows = $wpdb->get_results($sql, 'ARRAY_A');


        foreach ($rows as $key => $row) {
            if (strpos($row['question_ids'], '***') !== false) {
                $question_ids = implode(',', explode('***', $row['question_ids']));
                $wpdb->update(
                    $quiz_table,
                    array('question_ids' => $question_ids),
                    array('id' => $row['id']),
                    array('%s'),
                    array('%d')
                );
            }
        }
    }
    
    public function get_questions_categories(){
        global $wpdb;
        $categories_table = $wpdb->prefix . "aysquiz_categories";
        $get_cats = $wpdb->get_results("SELECT * FROM {$categories_table}", ARRAY_A);
        return $get_cats;
    }
    
    public function get_published_questions_by($key, $value) {
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_questions WHERE {$key} = {$value};";

        $results = $wpdb->get_row( $sql, 'ARRAY_A' );

        return $results;

    }

    public static function ays_get_quiz_options(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'aysquiz_quizes';
        $res = $wpdb->get_results("SELECT id, title FROM $table_name");
        $aysGlobal_array = array();

        foreach ($res as $ays_res_options) {
            $aysStatic_array = array();
            $aysStatic_array[] = $ays_res_options->id;
            $aysStatic_array[] = $ays_res_options->title;
            $aysGlobal_array[] = $aysStatic_array;
        }
        return $aysGlobal_array;
    }

    function ays_quiz_register_tinymce_plugin($plugin_array){
        $this->settings_obj = new Quiz_Maker_Settings_Actions($this->plugin_name);

        // General Settings | options
        $gen_options = ($this->settings_obj->ays_get_setting('options') === false) ? array() : json_decode( stripcslashes($this->settings_obj->ays_get_setting('options') ), true);

        // Show quiz button to Admins only
        $gen_options['quiz_show_quiz_button_to_admin_only'] = isset($gen_options['quiz_show_quiz_button_to_admin_only']) ? sanitize_text_field( $gen_options['quiz_show_quiz_button_to_admin_only'] ) : 'off';
        $quiz_show_quiz_button_to_admin_only = (isset($gen_options['quiz_show_quiz_button_to_admin_only']) && sanitize_text_field( $gen_options['quiz_show_quiz_button_to_admin_only'] ) == "on") ? true : false;

        if ( $quiz_show_quiz_button_to_admin_only ) {

            if( current_user_can( 'manage_options' ) ){
                $plugin_array['ays_quiz_button_mce'] = AYS_QUIZ_BASE_URL . 'ays_quiz_shortcode.js';
            }

        } else {
            $plugin_array['ays_quiz_button_mce'] = AYS_QUIZ_BASE_URL . 'ays_quiz_shortcode.js';
        }

        return $plugin_array;
    }

    function ays_quiz_add_tinymce_button($buttons){
        $buttons[] = "ays_quiz_button_mce";
        return $buttons;
    }

    function gen_ays_quiz_shortcode_callback(){
        $shortcode_data = $this->ays_get_quiz_options();
        ob_end_clean();
        $ob_get_clean = ob_get_clean();
        ?>
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <title><?php echo __('Quiz Maker', $this->plugin_name); ?></title>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <script language="javascript" type="text/javascript"
                    src="<?php echo site_url(); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
            <script language="javascript" type="text/javascript"
                    src="<?php echo site_url(); ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
            <script language="javascript" type="text/javascript"
                    src="<?php echo site_url(); ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>

            <?php
            wp_print_scripts('jquery');
            ?>
            <base target="_self">
        </head>
        <body id="link" onLoad="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';" dir="ltr"
              class="forceColors">
        <div class="select-sb">

            <table align="center">
                <tr>
                    <td><label for="ays_quiz">Quiz Maker</label></td>
                    <td>
                     <span>
                               <select id="ays_quiz" style="padding: 2px; height: 25px; font-size: 16px;width:100%;">
                           <option>--Select Quiz--</option>
                                   <?php
                                   echo "<pre>";
                                   print_r($shortcode_data);
                                   echo "</pre>";
                                   ?>
                                   <?php foreach ($shortcode_data as $index => $data)
                                       echo '<option id="' . $data[0] . '" value="' . $data[0] . '"  class="ays_quiz_options">' . $data[1] . '</option>';
                                   ?>
                               </select>
                           </span>
                    </td>
                </tr>
            </table>
        </div>
        <div class="mceActionPanel">
            <input type="submit" id="insert" name="insert" value="Insert" onClick="quiz_insert_shortcode();"/>
        </div>
        <script>

        </script>
        <script type="text/javascript">
            function quiz_insert_shortcode() {
                var tagtext = '[ays_quiz id="' + document.getElementById('ays_quiz')[document.getElementById('ays_quiz').selectedIndex].id + '"]';
                window.tinyMCE.execCommand('mceInsertContent', false, tagtext);
                tinyMCEPopup.close();
            }
        </script>
        </body>
        </html>
        <?php
        die();
    }
    
    public function vc_before_init_actions() {
        require_once( AYS_QUIZ_DIR.'pb_templates/quiz_maker_wpbvc.php' );
    }

    public function quiz_maker_el_widgets_registered() {
        wp_enqueue_style($this->plugin_name . '-admin', plugin_dir_url(__FILE__) . 'css/admin.css', array(), $this->version, 'all');
        // We check if the Elementor plugin has been installed / activated.
        if ( defined( 'ELEMENTOR_PATH' ) && class_exists( 'Elementor\Widget_Base' ) ) {
            // get our own widgets up and running:
            // copied from widgets-manager.php
            if ( class_exists( 'Elementor\Plugin' ) ) {
                if ( is_callable( 'Elementor\Plugin', 'instance' ) ) {
                    $elementor = Elementor\Plugin::instance();
                    if ( isset( $elementor->widgets_manager ) ) {
                        if ( method_exists( $elementor->widgets_manager, 'register_widget_type' ) ) {
                            $widget_file   = 'plugins/elementor/quiz_maker_elementor.php';
                            $template_file = locate_template( $widget_file );
                            if ( !$template_file || !is_readable( $template_file ) ) {
                                $template_file = AYS_QUIZ_DIR.'pb_templates/quiz_maker_elementor.php';
                            }
                            if ( $template_file && is_readable( $template_file ) ) {
                                require_once $template_file;
                                Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Elementor\Widget_Quiz_Maker_Elementor() );
                            }
                        }
                    }
                }
            }
        }
    }
    
    public function deactivate_plugin_option(){
        $request_value = esc_sql( sanitize_text_field( $_REQUEST['upgrade_plugin'] ) );
        $upgrade_option = get_option('ays_quiz_maker_upgrade_plugin','');
        if($upgrade_option === ''){
            add_option('ays_quiz_maker_upgrade_plugin',$request_value);
        }else{
            update_option('ays_quiz_maker_upgrade_plugin',$request_value);
        }
        ob_end_clean();
        $ob_get_clean = ob_get_clean();
        echo json_encode(array(
            'option' => get_option('ays_quiz_maker_upgrade_plugin', '')
        ));
        wp_die();
    }
    
    public static function ays_restriction_string($type, $x, $length){
        $output = "";
        switch($type){
            case "char":                
                if(strlen($x)<=$length){
                    $output = $x;
                } else {
                    $output = substr($x,0,$length) . '...';
                }
                break;
            case "word":
                $res = explode(" ", $x);
                if(count($res)<=$length){
                    $output = implode(" ",$res);
                } else {
                    $res = array_slice($res,0,$length);
                    $output = implode(" ",$res) . '...';
                }
            break;
        }
        return $output;
    }
    
    public static function validateDate($date, $format = 'Y-m-d H:i:s'){
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
    
    // Title change function in dashboard
    public function change_dashboard_title( $admin_title ) {
        
        global $current_screen;
        global $wpdb;
        
        if(strpos($current_screen->id, $this->plugin_name) === false){
            return $admin_title;
        }
        $action = (isset($_GET['action'])) ? sanitize_text_field($_GET['action']) : '';        
        $quiz_id = (isset($_GET['quiz'])) ? absint(intval($_GET['quiz'])) : null;
        $question_id = (isset($_GET['question'])) ? absint(intval($_GET['question'])) : null;
        $question_cat_id = (isset($_GET['question_category'])) ? absint(intval($_GET['question_category'])) : null;
        $quiz_cat_id = (isset($_GET['quiz_category'])) ? absint(intval($_GET['quiz_category'])) : null;
        
        if($quiz_id !== null){
            $id = $quiz_id;
        }elseif($question_id !== null){
            $id = $question_id;
        }elseif($question_cat_id !== null){
            $id = $question_cat_id;
        }elseif($quiz_cat_id !== null){
            $id = $quiz_cat_id;
        }else{
            $id = null;
        }
        
        $current = explode($this->plugin_name, $current_screen->id);
        $current = trim($current[count($current)-1], "-");
        $sql = '';
        switch($current){
            case "":
                $page = __("Quiz", $this->plugin_name);
                if($id !== null){
                    $sql = "SELECT * FROM ".$wpdb->prefix."aysquiz_quizes WHERE id=".$id;
                }
                break;
            case "questions":
                $page = __("Question", $this->plugin_name);;
                if($id !== null){
                    $sql = "SELECT * FROM ".$wpdb->prefix."aysquiz_questions WHERE id=".$id;
                }
                break;
            case "quiz-categories":
                $page = __("Category", $this->plugin_name);;
                if($id !== null){
                    $sql = "SELECT * FROM ".$wpdb->prefix."aysquiz_quizcategories WHERE id=".$id;
                }
                break;
            case "question-categories":
                $page = __("Category", $this->plugin_name);;
                if($id !== null){
                    $sql = "SELECT * FROM ".$wpdb->prefix."aysquiz_categories WHERE id=".$id;
                }
                break;
            default:
                $page = '';
                $sql = '';
                break;
        }
        $results = null;
        if($sql != ""){
            $results = $wpdb->get_row($sql, "ARRAY_A");
        }
        $change_title = null;
        switch($action){
            case "add":
                $change_title = __("Add New", $this->plugin_name) ." ‹ ".$page;
                break;
            case "edit":
                if($results !== null){
                    $title = "";
                    if($current == "questions"){
                        if(isset($results['question']) && strlen($results['question']) != 0){
                            $title = strip_tags(stripslashes($results['question']));
                        }elseif ((isset($results['question_image']) && $results['question_image'] !='')){
                            $title = 'Image question';
                        }
                    }else{                        
                        $title = stripslashes($results['title']);
                    }
                    $title = strip_tags($title);
                    $change_title = $this->ays_restriction_string("word", $title, 5) ." ‹ ". __("Edit", $this->plugin_name) . " ".$page;
                }
                break;
            default:
                $change_title = $admin_title;
                break;
        }
        if($change_title === null){
            $change_title = $admin_title;
        }
        
        return $change_title;

    }    

    public static function get_listtables_title_length( $listtable_name ) {
        global $wpdb;

        $settings_table = $wpdb->prefix . "aysquiz_settings";
        $sql = "SELECT meta_value FROM ".$settings_table." WHERE meta_key = 'options'";
        $result = $wpdb->get_var($sql);
        $options = ($result == "") ? array() : json_decode(stripcslashes($result), true);

        $listtable_title_length = 5;
        if(! empty($options) ){
            switch ( $listtable_name ) {
                case 'questions':
                    $listtable_title_length = (isset($options['question_title_length']) && intval($options['question_title_length']) != 0) ? absint(intval($options['question_title_length'])) : 5;
                    break;
                case 'quizzes':
                    $listtable_title_length = (isset($options['quizzes_title_length']) && intval($options['quizzes_title_length']) != 0) ? absint(intval($options['quizzes_title_length'])) : 5;
                    break;
                case 'results':
                    $listtable_title_length = (isset($options['results_title_length']) && intval($options['results_title_length']) != 0) ? absint(intval($options['results_title_length'])) : 5;
                    break;   
                case 'question_categories':
                    $listtable_title_length = (isset($options['question_categories_title_length']) && intval($options['question_categories_title_length']) != 0) ? absint(sanitize_text_field($options['question_categories_title_length'])) : 5;
                    break;
                case 'quiz_categories':
                    $listtable_title_length = (isset($options['quiz_categories_title_length']) && intval($options['quiz_categories_title_length']) != 0) ? absint(sanitize_text_field($options['quiz_categories_title_length'])) : 5;
                    break;
                case 'quiz_reviews':
                    $listtable_title_length = (isset($options['quiz_reviews_title_length']) && intval($options['quiz_reviews_title_length']) != 0) ? absint(sanitize_text_field($options['quiz_reviews_title_length'])) : 5;
                    break;        
                default:
                    $listtable_title_length = 5;
                    break;
            }
            return $listtable_title_length;
        }
        return $listtable_title_length;
    }
    
    public function quiz_maker_add_dashboard_widgets() {
        if(current_user_can('manage_options')){ // Administrator
            wp_add_dashboard_widget( 
                'quiz-maker', 
                'Quiz Maker Status', 
                array( $this, 'quiz_maker_dashboard_widget' )
            );

            // Globalize the metaboxes array, this holds all the widgets for wp-admin
            global $wp_meta_boxes;

            // Get the regular dashboard widgets array 
            // (which has our new widget already but at the end)
            $normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];

            // Backup and delete our new dashboard widget from the end of the array
            $example_widget_backup = array( 
                'quiz-maker' => $normal_dashboard['quiz-maker'] 
            );
            unset( $normal_dashboard['example_dashboard_widget'] );

            // Merge the two arrays together so our widget is at the beginning
            $sorted_dashboard = array_merge( $example_widget_backup, $normal_dashboard );

            // Save the sorted array back into the original metaboxes 
            $wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
        }
    } 

    /**
     * Create the function to output the contents of our Dashboard Widget.
     */
    public function quiz_maker_dashboard_widget() {
        global $wpdb;
        $questions_count = Questions_List_Table::record_count();
        $quizzes_count = Quizes_List_Table::record_count();
        $results_count = Results_List_Table::unread_records_count();
        
        $questions_label = intval($questions_count) == 1 ? "question" : "questions";
        $quizzes_label = intval($quizzes_count) == 1 ? "quiz" : "quizzes";
        $results_label = intval($results_count) == 1 ? "new result" : "new results";
        
        // Display whatever it is you want to show.
        ?>
        <ul class="ays_quiz_maker_dashboard_widget">
            <li class="ays_dashboard_widget_item">
                <a href="<?php echo "admin.php?page=".$this->plugin_name; ?>">
                    <img src="<?php echo AYS_QUIZ_ADMIN_URL."/images/icons/icon-128x128.png"; ?>" alt="Quizzes">
                    <span><?php echo $quizzes_count; ?></span>
                    <span><?php echo __($quizzes_label, $this->plugin_name); ?></span>
                </a>
            </li>
            <li class="ays_dashboard_widget_item">
                <a href="<?php echo "admin.php?page=".$this->plugin_name."-questions" ?>">
                    <img src="<?php echo AYS_QUIZ_ADMIN_URL."/images/icons/question2.png"; ?>" alt="Questions">
                    <span><?php echo $questions_count; ?></span>
                    <span><?php echo __($questions_label, $this->plugin_name); ?></span>
                </a>
            </li>
            <li class="ays_dashboard_widget_item">
                <a href="<?php echo "admin.php?page=".$this->plugin_name."-results" ?>">
                    <img src="<?php echo AYS_QUIZ_ADMIN_URL."/images/icons/users2.png"; ?>" alt="Results">
                    <span><?php echo $results_count; ?></span>
                    <span><?php echo __($results_label, $this->plugin_name); ?></span>
                </a>
            </li>
        </ul>
        <div style="padding:10px;font-size:14px;border-top:1px solid #ccc;">
            <?php echo "Works version ".AYS_QUIZ_VERSION." of "; ?>
            <a href="<?php echo "admin.php?page=".$this->plugin_name ?>">Quiz Maker</a>
        </div>
    <?php
    }
    
    public static function ays_query_string($remove_items){
        $query_string = $_SERVER['QUERY_STRING'];
        $query_items = explode( "&", $query_string );
        foreach($query_items as $key => $value){
            $item = explode("=", $value);
            foreach($remove_items as $k => $i){
                if(in_array($i, $item)){
                    unset($query_items[$key]);
                }
            }
        }
        return implode( "&", $query_items );
    }

    public function get_question_categories(){
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_categories";

        $result = $wpdb->get_results($sql, 'ARRAY_A');

        return $result;
    }
    
    public function quiz_maker_admin_footer($a){
        if(isset($_REQUEST['page'])){
            if(false !== strpos( sanitize_text_field( $_REQUEST['page'] ), $this->plugin_name)){
                ?>
                <p style="font-size:13px;text-align:center;font-style:italic;">
                    <span style="margin-left:0px;margin-right:10px;" class="ays_heart_beat"><i class="ays_fa ays_fa_heart_o animated"></i></span>
                    <span><?php echo __( "If you love our plugin, please do big favor and rate us on", $this->plugin_name); ?></span> 
                    <a target="_blank" href='https://wordpress.org/support/plugin/quiz-maker/reviews/?rate=5#new-post'>WordPress.org</a>
                    <span class="ays_heart_beat"><i class="ays_fa ays_fa_heart_o animated"></i></span>
                </p>
            <?php
            }
        }
    }
    
    public static function get_user_country_by_ip( $ip ){
        
        $url = AYS_QUIZ_ADMIN_URL . "/quiz-maker-countries-names.json";
        $response = wp_remote_get( $url );        
        $body = wp_remote_retrieve_body( $response );
        $countries = json_decode( $body );
        
        $headers = array(
            "headers" => array(            
                "Content-Type"  => "application/json",
                "cache-control" => "no-cache",
            )
        );

        $url = "https://ipinfo.io/". $ip ."/json";
        $response = wp_remote_get($url, $headers);
        $body     = wp_remote_retrieve_body( $response );
        
        if( empty( $body ) ){
            $from = $ip;
        }else{
            $body = json_decode( $body );
            
            $from = array();
            
            if( !empty( $body->city ) ){
                $from[] = $body->city;
            }
            if( !empty( $body->region ) ){
                $from[] = $body->region;
            }
            if( !empty( $body->country ) ){
                $country = $body->country;
                $from[] = $countries->$country;
            }
            
            $from[] = $ip;
            
            $from = implode( ', ', $from );
        }
        
        return $from;
    }

    public static function ays_quiz_is_exists_needle_tag( $str, $needle ) {

        $exists_flag = false;

        if ( isset( $str ) && ! is_null( $str ) && $str != '' ) {

            if ( isset( $needle ) && ! is_null( $needle ) && $needle != '' ) {

                $is_exists_needle = strpos( $str, $needle );

                if ( $is_exists_needle !== false ) {
                    $exists_flag = true;
                }
            }
        }

        return $exists_flag;
    }

    /*
    ==========================================
        Sale Banner | Start
    ==========================================
    */

    public function ays_quiz_sale_baner(){
        if(isset($_POST['ays_quiz_sale_btn'])){
            update_option('ays_quiz_sale_btn', 1); 
            update_option('ays_quiz_sale_date', current_time( 'mysql' ));
        }

        if(isset($_POST['ays_quiz_sale_btn_for_two_months'])){
            update_option('ays_quiz_sale_btn_for_two_months', 1);
            update_option('ays_quiz_sale_date', current_time( 'mysql' ));
        }

        $ays_quiz_sale_date = get_option('ays_quiz_sale_date');
        $ays_quiz_sale_two_months = get_option('ays_quiz_sale_btn_for_two_months');

        $val = 60*60*24*5;
        if($ays_quiz_sale_two_months == 1){
            $val = 60*60*24*61;
        }

        $current_date = current_time( 'mysql' );
        $date_diff = strtotime($current_date) -  intval(strtotime($ays_quiz_sale_date));

        $days_diff = $date_diff / $val;

        if(intval($days_diff) > 0 ){
            update_option('ays_quiz_sale_btn', 0); 
            update_option('ays_quiz_sale_btn_for_two_months', 0);
        }

        $ays_quiz_ishmar = intval(get_option('ays_quiz_sale_btn'));
        $ays_quiz_ishmar += intval(get_option('ays_quiz_sale_btn_for_two_months'));
        if($ays_quiz_ishmar == 0 ){
            if (isset($_GET['page']) && strpos($_GET['page'], AYS_QUIZ_NAME) !== false) {
                $this->ays_quiz_sale_message($ays_quiz_ishmar);
                // $this->ays_quiz_spring_bundle_small_message($ays_quiz_ishmar);
            }
        }
    }

    public function ays_quiz_sale_message($ishmar){
        if($ishmar == 0 ){
            $content = array();

            $content[] = '<div id="ays-quiz-dicount-month-main" class="notice notice-success is-dismissible ays_quiz_dicount_info">';
                $content[] = '<div id="ays-quiz-dicount-month" class="ays_quiz_dicount_month">';
                    $content[] = '<a href="https://ays-pro.com/wordpress/quiz-maker/" target="_blank" class="ays-quiz-sale-banner-link"><img src="' . AYS_QUIZ_ADMIN_URL . '/images/icons/quiz-maker-logo.png"></a>';

                    $content[] = '<div class="ays-quiz-dicount-wrap-box">';

                        $content[] = '<strong style="font-weight: bold;">';
                            $content[] = __( "Limited Time <span class='ays-quiz-dicount-wrap-color'>20%</span> SALE on <br><span><a href='https://ays-pro.com/wordpress/quiz-maker/' target='_blank' class='ays-quiz-dicount-wrap-color ays-quiz-dicount-wrap-text-decoration' style='display:block;'>Quiz Maker Premium Versions</a></span>", AYS_QUIZ_NAME );
                        $content[] = '</strong>';

                        // $content[] = '<br>';

                        $content[] = '<strong>';
                                $content[] = __( "Hurry up! <a href='https://ays-pro.com/wordpress/quiz-maker' target='_blank'>Check it out!</a>", AYS_QUIZ_NAME );
                        $content[] = '</strong>';
                            
                    $content[] = '</div>';

                    $content[] = '<div class="ays-quiz-dicount-wrap-box">';

                        $content[] = '<div id="ays-quiz-maker-countdown-main-container">';
                            $content[] = '<div class="ays-quiz-maker-countdown-container">';

                                $content[] = '<div id="ays-quiz-countdown">';
                                    $content[] = '<ul>';
                                        $content[] = '<li><span id="ays-quiz-countdown-days"></span>days</li>';
                                        $content[] = '<li><span id="ays-quiz-countdown-hours"></span>Hours</li>';
                                        $content[] = '<li><span id="ays-quiz-countdown-minutes"></span>Minutes</li>';
                                        $content[] = '<li><span id="ays-quiz-countdown-seconds"></span>Seconds</li>';
                                    $content[] = '</ul>';
                                $content[] = '</div>';

                                $content[] = '<div id="ays-quiz-countdown-content" class="emoji">';
                                    $content[] = '<span>🚀</span>';
                                    $content[] = '<span>⌛</span>';
                                    $content[] = '<span>🔥</span>';
                                    $content[] = '<span>💣</span>';
                                $content[] = '</div>';

                            $content[] = '</div>';

                            // $content[] = '<form action="" method="POST">';
                            //     $content[] = '<div id="ays-quiz-dismiss-buttons-content">';
                            //         $content[] = '<button class="btn btn-link ays-button" name="ays_quiz_sale_btn" style="height: 32px; margin-left: 0;padding-left: 0">Dismiss ad</button>';
                            //         $content[] = '<button class="btn btn-link ays-button" name="ays_quiz_sale_btn_for_two_months" style="height: 32px; padding-left: 0">Dismiss ad for 2 months</button>';
                            //     $content[] = '</div>';
                            // $content[] = '</form>';

                        $content[] = '</div>';
                            
                    $content[] = '</div>';

                    $content[] = '<div class="ays-quiz-dicount-wrap-box ays-buy-now-button-box">';
                        $content[] = '<a href="https://ays-pro.com/wordpress/quiz-maker" class="button button-primary ays-buy-now-button" id="ays-button-top-buy-now" target="_blank" style="" >' . __( 'Buy Now !', AYS_QUIZ_NAME ) . '</a>';
                    $content[] = '</div>';

                    // $content[] = '<div class="ays-quiz-dicount-wrap-box ays-quiz-dicount-wrap-opacity-box">';
                    //     $content[] = '<a href="https://ays-pro.com/great-bundle" class="ays-buy-now-opacity-button" target="_blank">' . __( 'link', AYS_QUIZ_NAME ) . '</a>';
                    // $content[] = '</div>';

                $content[] = '</div>';

                $content[] = '<div style="position: absolute;right: 10px;bottom: 1px;" class="ays-quiz-dismiss-buttons-container-for-form">';
                    $content[] = '<form action="" method="POST">';
                        $content[] = '<div id="ays-quiz-dismiss-buttons-content">';
                            $content[] = '<button class="btn btn-link ays-button" name="ays_quiz_sale_btn" style="height: 32px; margin-left: 0;padding-left: 0">Dismiss ad</button>';
                            // $content[] = '<button class="btn btn-link ays-button" name="ays_quiz_sale_btn_for_two_months" style="height: 32px; padding-left: 0">Dismiss ad for 2 months</button>';
                        $content[] = '</div>';
                    $content[] = '</form>';
                $content[] = '</div>';

            $content[] = '</div>';

            $content = implode( '', $content );
            echo $content;
        }
    }

    // Christmas bundle
    public function ays_quiz_sale_message_christmas_bundle(){
        $content = array();

        $content = array();
        $content[] = '<div id="ays-quiz-winter-dicount-main">';
            $content[] = '<div id="ays-quiz-dicount-month-main" class="notice notice-success is-dismissible ays_quiz_dicount_info">';
                $content[] = '<div id="ays-quiz-dicount-month" class="ays_quiz_dicount_month">';
                    $content[] = '<a href="https://ays-pro.com/mega-bundle" target="_blank" class="ays-quiz-sale-banner-link"><img src="' . AYS_QUIZ_ADMIN_URL . '/images/mega_bundle_christmas_box.png"></a>';

                    $content[] = '<div class="ays-quiz-dicount-wrap-box">';

                        $content[] = '<strong>';
                            $content[] = __( "The BIGGEST <span class='ays-quiz-dicount-wrap-color' style='color:#000;'>50%</span> SALE on <br><span><a href='https://ays-pro.com/mega-bundle' target='_blank' class='ays-quiz-dicount-wrap-color ays-quiz-dicount-wrap-text-decoration' style='display:block;color:#000'>Christmas Bundle</a></span> (Quiz+Survey+Poll)!", AYS_QUIZ_NAME );
                        $content[] = '</strong>';

                        $content[] = '<br>';

                        $content[] = '<strong>';
                                $content[] = __( "Hurry up! Ending on. <a href='https://ays-pro.com/mega-bundle' target='_blank' style='color:#000;'>Check it out!</a>", AYS_QUIZ_NAME );
                        $content[] = '</strong>';
                            
                    $content[] = '</div>';

                    $content[] = '<div class="ays-quiz-dicount-wrap-box">';

                        $content[] = '<div id="ays-quiz-maker-countdown-main-container">';
                            $content[] = '<div class="ays-quiz-maker-countdown-container">';

                                $content[] = '<div id="ays-quiz-countdown">';
                                    $content[] = '<ul>';
                                        $content[] = '<li><span id="ays-quiz-countdown-days"></span>days</li>';
                                        $content[] = '<li><span id="ays-quiz-countdown-hours"></span>Hours</li>';
                                        $content[] = '<li><span id="ays-quiz-countdown-minutes"></span>Minutes</li>';
                                        $content[] = '<li><span id="ays-quiz-countdown-seconds"></span>Seconds</li>';
                                    $content[] = '</ul>';
                                $content[] = '</div>';

                                $content[] = '<div id="ays-quiz-countdown-content" class="emoji">';
                                    $content[] = '<span>🚀</span>';
                                    $content[] = '<span>⌛</span>';
                                    $content[] = '<span>🔥</span>';
                                    $content[] = '<span>💣</span>';
                                $content[] = '</div>';

                            $content[] = '</div>';

                            $content[] = '<form action="" method="POST">';
                                $content[] = '<button class="btn btn-link ays-button" name="ays_quiz_sale_btn_winter" style="height: 32px;color:#000" value="winter_bundle">Dismiss ad</button>';
                                $content[] = '<button class="btn btn-link ays-button" name="ays_quiz_sale_btn_winter_for_two_months" style="height: 32px; padding-left: 0;color:#000" value="winter_bundle">Dismiss ad for 2 months</button>';
                            $content[] = '</form>';

                        $content[] = '</div>';
                            
                    $content[] = '</div>';

                    
                    $content[] = '<a href="https://ays-pro.com/mega-bundle" class="button button-primary ays-button" id="ays-quiz-button-top-buy-now" target="_blank">' . __( 'Buy Now !', AYS_QUIZ_NAME ) . '</a>';
                $content[] = '</div>';
            $content[] = '</div>';
        // $content[] = '</div>';

        $content = implode( '', $content );
        echo $content;
    }

    public static function ays_quiz_spring_bundle_message($ishmar){
        if($ishmar == 0 ){
            $content = array();

            $content[] = '<div id="ays-quiz-dicount-month-main" class="notice notice-success is-dismissible ays_quiz_dicount_info">';
                $content[] = '<div id="ays-quiz-dicount-month" class="ays_quiz_dicount_month">';
                    $content[] = '<a href="https://ays-pro.com/spring-bundle" target="_blank" class="ays-quiz-sale-banner-link"><img src="' . AYS_QUIZ_ADMIN_URL . '/images/spring_bundle_logo.png"></a>';

                    $content[] = '<div class="ays-quiz-dicount-wrap-box">';
                        $content[] = '<p style="margin-right:25%;">';
                            $content[] = '<strong>';
                                $content[] = __( "Spring is here! <span class='ays-quiz-dicount-wrap-color'>40%</span> SALE on <br><span><a href='https://ays-pro.com/spring-bundle' target='_blank' class='ays-quiz-dicount-wrap-color ays-quiz-dicount-wrap-text-decoration' style='display:block;'>Spring Bundle</a></span> (Quiz+Copy+Popup) !", AYS_QUIZ_NAME );
                            $content[] = '</strong>';
                            $content[] = '<br>';
                            $content[] = '<strong>';
                                    $content[] = __( "Hurry up! Ending on. <a href='https://ays-pro.com/spring-bundle' target='_blank'>Check it out!</a>", AYS_QUIZ_NAME );
                            $content[] = '</strong>';
                        $content[] = '</p>';
                    $content[] = '</div>';

                    $content[] = '<div class="ays-quiz-dicount-wrap-box">';

                        $content[] = '<div id="ays-quiz-countdown-main-container">';
                            $content[] = '<div class="ays-quiz-countdown-container">';

                                $content[] = '<div id="ays-quiz-countdown">';
                                    $content[] = '<ul>';
                                        $content[] = '<li><span id="ays-quiz-countdown-days"></span>days</li>';
                                        $content[] = '<li><span id="ays-quiz-countdown-hours"></span>Hours</li>';
                                        $content[] = '<li><span id="ays-quiz-countdown-minutes"></span>Minutes</li>';
                                        $content[] = '<li><span id="ays-quiz-countdown-seconds"></span>Seconds</li>';
                                    $content[] = '</ul>';
                                $content[] = '</div>';

                                $content[] = '<div id="ays-quiz-countdown-content" class="emoji">';
                                    $content[] = '<span>🚀</span>';
                                    $content[] = '<span>⌛</span>';
                                    $content[] = '<span>🔥</span>';
                                    $content[] = '<span>💣</span>';
                                $content[] = '</div>';

                            $content[] = '</div>';

                            $content[] = '<form action="" method="POST" class="ays-quiz-btn-form">';
                                $content[] = '<button class="btn btn-link ays-button" name="ays_quiz_sale_btn_spring" style="height: 32px; margin-left: 0;padding-left: 0">Dismiss ad</button>';
                                $content[] = '<button class="btn btn-link ays-button" name="ays_quiz_sale_btn_spring_for_two_months" style="height: 32px; padding-left: 0">Dismiss ad for 2 months</button>';
                            $content[] = '</form>';

                        $content[] = '</div>';
                            
                    $content[] = '</div>';

                    $content[] = '<a href="https://ays-pro.com/spring-bundle" class="button button-primary ays-button" id="ays-button-top-buy-now" target="_blank">' . __( 'Buy Now !', AYS_QUIZ_NAME ) . '</a>';
                $content[] = '</div>';
            $content[] = '</div>';

            $content = implode( '', $content );
            echo $content;
        }
    }

    public static function ays_quiz_spring_bundle_small_message($ishmar){
        if($ishmar == 0 ){
            $content = array();

            $content[] = '<div id="ays-quiz-dicount-month-main" class="notice notice-success is-dismissible ays_quiz_dicount_info">';
                $content[] = '<div id="ays-quiz-dicount-month" class="ays_quiz_dicount_month">';
                    $content[] = '<a href="https://ays-pro.com/spring-bundle" target="_blank" class="ays-quiz-sale-banner-link"><img src="' . AYS_QUIZ_ADMIN_URL . '/images/spring_bundle_logo_box.png"></a>';

                    $content[] = '<div class="ays-quiz-dicount-wrap-box">';
                        $content[] = '<p>';
                            $content[] = '<strong>';
                                $content[] = __( "Spring is here! <span class='ays-quiz-dicount-wrap-color'>50%</span> SALE on <span><a href='https://ays-pro.com/spring-bundle' target='_blank' class='ays-quiz-dicount-wrap-color ays-quiz-dicount-wrap-text-decoration'>Spring Bundle</a></span><span style='display: block;'>Quiz + Popup + Copy</span>", AYS_QUIZ_NAME );
                            $content[] = '</strong>';
                        $content[] = '</p>';
                    $content[] = '</div>';

                    $content[] = '<div class="ays-quiz-dicount-wrap-box">';

                        $content[] = '<div id="ays-quiz-countdown-main-container">';

                            $content[] = '<form action="" method="POST" class="ays-quiz-btn-form">';
                                $content[] = '<button class="btn btn-link ays-button" name="ays_quiz_sale_spring_btn" style="height: 32px; margin-left: 0;padding-left: 0">Dismiss ad</button>';
                                $content[] = '<button class="btn btn-link ays-button" name="ays_quiz_sale_spring_btn_for_two_months" style="height: 32px; padding-left: 0">Dismiss ad for 2 months</button>';
                            $content[] = '</form>';

                        $content[] = '</div>';
                            
                    $content[] = '</div>';

                    $content[] = '<a href="https://ays-pro.com/spring-bundle" class="button button-primary ays-button" id="ays-button-top-buy-now" target="_blank">' . __( 'Buy Now !', AYS_QUIZ_NAME ) . '</a>';
                $content[] = '</div>';
            $content[] = '</div>';

            $content = implode( '', $content );
            echo $content;
        }
    }

    /*
    ==========================================
        Sale Banner | End
    ==========================================
    */

    public function ays_quiz_subscribe_email(){
        $subscribe_email = isset($_REQUEST['email']) && $_REQUEST['email'] != "" ? sanitize_email($_REQUEST['email']) : "";
        // $url = "http://localhost/add-on-email/quiz_test_email";
        $url = "https://ays-pro.com/add-on-email/";
        if($subscribe_email != ""){
            $current_date = date("Y-m-d");
            $current_user_ip = $this->get_user_ip();
            $send_request = wp_remote_post($url, array(
                'headers'     => array("Content-Type: application/json; charset=UTF-8"),
                'body'        => json_encode( array(
                    "email"   => $subscribe_email,
                    "user_ip" => $current_user_ip,
                    "subscirbe_date" => $current_date
                ) ),
            ) );
            $response = wp_remote_retrieve_body($send_request);
            $response = json_decode($response, true);
            if(isset($response) && is_array($response)){
                $response_code = isset($response['code']) && $response['code'] != "" ? $response['code'] : "";
                $response_message = isset($response['msg']) && $response['msg'] != "" ? $response['msg'] : "";
                $response_status = $response_code > 0 ? true : false;
                echo json_encode(array(
                    "status" => $response_status,
                    "message" => $response_message
                ));
                wp_die();
            }       
            else{
                echo json_encode(array(
                    "status" => false,
                    "message" => "Something went wrong. Please try again"
                ));
                wp_die();
            }
        }
    }

    public function get_user_ip(){
        $ipaddress = '';
        if (getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        elseif (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    public function add_tabs() {
        $screen = get_current_screen();
    
        if ( ! $screen) {
            return;
        }

        /*
        ==========================================
            General information Tab | Start
        ==========================================
        */
        
        $general_tab_title   = __( 'General Information', $this->plugin_name);

        $content = array();

        $content[] = '<div class="ays-quiz-help-tab-conatiner">';

            $content[] = '<div class="ays-quiz-help-tab-title">';
                $content[] = __( "Quiz Maker Information", AYS_QUIZ_NAME );
            $content[] = '</div>';


            $content[] = '<div class="ays-quiz-help-tab-row">';

                $content[] = '<div class="ays-quiz-help-tab-box">';
                    $content[] = '<span>';

                        $content[] = sprintf(
                            __( 'Create engaging quizzes, tests, and exams within a few minutes with the help of the WordPress Quiz Maker plugin. The Quiz Maker has a user-friendly interface and responsive design.%s With this plugin, you are free to add as many questions as needed with the following question types: %sRadio, Checkbox, Dropdown, Text, Short Text, Number, Date.%s In order, to activate Integrations, send Certificates via Email, or create Intervals for your quiz results you will need to download and install the Pro Versions of the WordPress Quiz plugin.', AYS_QUIZ_NAME ),
                            '<br>',
                            '<em>',
                            '</em><br><br>'
                        );

                    $content[] = '</span>';
                $content[] = '</div>';

            $content[] = '</div>';
        $content[] = '</div>';

        $content_genereal_info = implode( '', $content );

        /*
        ==========================================
            General information Tab | End
        ==========================================
        */

        /*
        ==========================================
            Premium information Tab | Start
        ==========================================
        */

        $premium_tab_title   = __( 'Premium version', $this->plugin_name);

        $content = array();

        $content[] = '<div class="ays-quiz-help-tab-conatiner">';

            $content[] = '<div class="ays-quiz-help-tab-title">';
                $content[] = __( "Premium versions' overview", AYS_QUIZ_NAME );
            $content[] = '</div>';

                $content[] = '<div class="ays-quiz-dicount-wrap-box">';
                    $content[] = '<span>';

                        $content[] = sprintf(
                            __( 'By activating the pro version, you will get all the features to strive your WordPress website’s quizzes to an advanced level.%sWith the WordPress Quiz plugin, it is easy to generate the quiz types like %sTrivia quiz, Assessment quiz, Personality test,  Multiple-choice quiz, Knowledge quiz, IQ test, Yes-or-no quiz, True-or-false quiz, This-or-that quiz(with images), Diagnostic quiz, Scored quiz, Buzzfeed quiz, Viral Quiz%s and etc.%sMotivate your visitors with Certificates and Advanced Leaderboards, prevent cheating during online exams with Timer-Based quizzes, earn money with Paid Quizzes.', $this->plugin_name ),
                            '<br>',
                            '<em>',
                            '</em>',
                            '</br></br>'
                        );

                    $content[] = '</span>';
            $content[] = '</div>';

        $content[] = '</div>';

        $content_premium_info = implode( '', $content );

        /*
        ==========================================
            Premium information Tab | End
        ==========================================
        */

        /*
        ==========================================
            Sidebar information | Start
        ==========================================
        */

        $sidebar_content = '
        <p><strong>' . __( 'For more information:', AYS_QUIZ_NAME ) . '</strong></p>' .
        '<p><a href="https://www.youtube.com/watch?v=oKPOdbZahK0" target="_blank">' . __( 'Youtube video tutorials' , AYS_QUIZ_NAME ) . '</a></p>' .
        '<p><a href="https://ays-pro.com/wordpress-quiz-maker-user-manual" target="_blank">' . __( 'Documentation', AYS_QUIZ_NAME ) . '</a></p>' .
        '<p><a href="https://ays-pro.com/wordpress/quiz-maker" target="_blank">' . __( 'Quiz Maker plugin premium version', AYS_QUIZ_NAME ) . '</a></p>' .
        '<p><a href="https://quiz-plugin.com/wordpress-quiz-plugin-free-demo" target="_blank">' . __( 'Quiz Maker plugin free demo', AYS_QUIZ_NAME ) . '</a></p>';

        /*
        ==========================================
            Sidebar information | End
        ==========================================
        */


        $general_tab_content = array(
            'id'      => 'quiz-maker-general-tab',
            'title'   => $general_tab_title,
            'content' => $content_genereal_info
        );

        $premium_tab_content = array(
            'id'      => 'quiz-maker-premium-tab',
            'title'   => $premium_tab_title,
            'content' => $content_premium_info
        );
        
        $screen->add_help_tab($general_tab_content);
        $screen->add_help_tab($premium_tab_content);

        $screen->set_help_sidebar($sidebar_content);
    }

    public function get_next_or_prev_row_by_id( $id, $type = "next", $table = "aysquiz_questions" ) {
        global $wpdb;

        if ( is_null( $table ) || empty( $table ) ) {
            return null;
        }

        $ays_table = esc_sql( $wpdb->prefix . $table );

        $where = array();
        $where_condition = "";

        $id     = (isset( $id ) && $id != "" && absint($id) != 0) ? absint( sanitize_text_field( $id ) ) : null;
        $type   = (isset( $type ) && $type != "") ? sanitize_text_field( $type ) : "next";

        if ( is_null( $id ) || $id == 0 ) {
            return null;
        }

        switch ( $type ) {
            case 'prev':
                $where[] = ' `id` < ' . $id . ' ORDER BY `id` DESC ';
                break;
            case 'next':
            default:
                $where[] = ' `id` > ' . $id;
                break;
        }

        if( ! empty($where) ){
            $where_condition = " WHERE " . implode( " AND ", $where );
        }

        $sql = "SELECT `id` FROM {$ays_table} ". $where_condition ." LIMIT 1;";

        $results = $wpdb->get_row( $sql, 'ARRAY_A' );

        return $results;

    }

    public static function ays_quiz_ays_quiz_get_quizzes(){
        global $wpdb;
        $quiz_table = esc_sql( $wpdb->prefix . "aysquiz_quizes" );

        $sql = "SELECT id,title
                FROM {$quiz_table} WHERE `published` = 1";

        $quizzes = $wpdb->get_results( $sql , "ARRAY_A" );

        return $quizzes;
    }

    public function ays_quiz_update_banner_time(){

        $date = time() + ( 3 * 24 * 60 * 60 ) + (int) ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS);
        // $date = time() + ( 60 ) + (int) ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS); // for testing | 1 min
        $next_3_days = date('M d, Y H:i:s', $date);

        $ays_quiz_banner_time = get_option('ays_quiz_banner_time');

        if ( !$ays_quiz_banner_time || is_null( $ays_quiz_banner_time ) ) {
            update_option('ays_quiz_banner_time', $next_3_days ); 
        }

        $get_ays_quiz_banner_time = get_option('ays_quiz_banner_time');

        $val = 60*60*24*0.5; // half day
        // $val = 60; // for testing | 1 min

        $current_date = current_time( 'mysql' );
        $date_diff = strtotime($current_date) - intval(strtotime($get_ays_quiz_banner_time));

        $days_diff = $date_diff / $val;
        if(intval($days_diff) > 0 ){
            update_option('ays_quiz_banner_time', $next_3_days);
        }

        return $get_ays_quiz_banner_time;
    }
}
