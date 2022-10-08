<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Quiz_Maker
 * @subpackage Quiz_Maker/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Quiz_Maker
 * @subpackage Quiz_Maker/public
 * @author     AYS Pro LLC <info@ays-pro.com>
 */
class Quiz_Maker_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    protected $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    protected $version;

    protected $settings;
    
    protected $buttons_texts;
    protected $fields_placeholders;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $plugin_name The name of the plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct($plugin_name, $version){
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->settings = new Quiz_Maker_Settings_Actions($this->plugin_name);
        $this->buttons_texts = $this->ays_set_quiz_texts();
        add_shortcode('ays_quiz', array($this, 'ays_generate_quiz_method'));
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles(){

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
        wp_enqueue_style($this->plugin_name.'-font-awesome', plugin_dir_url(__FILE__) . 'css/quiz-maker-font-awesome.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name.'-sweetalert-css', plugin_dir_url(__FILE__) . 'css/quiz-maker-sweetalert2.min.css', array(), $this->version, 'all' );
        wp_enqueue_style($this->plugin_name.'-animate', plugin_dir_url(__FILE__) . 'css/animate.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name.'-animations', plugin_dir_url(__FILE__) . 'css/animations.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name.'-rating', plugin_dir_url(__FILE__) . 'css/rating.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name.'-select2', plugin_dir_url(__FILE__) . 'css/quiz-maker-select2.min.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name.'-loaders', plugin_dir_url(__FILE__) . 'css/loaders.css', array(), $this->version, 'all');

    }

    public function enqueue_styles_early(){

        $settings_options = $this->settings->ays_get_setting('options');
        if($settings_options){
            $settings_options = json_decode(stripcslashes($settings_options), true);
        }else{
            $settings_options = array();
        }

        // General CSS File
        $settings_options['quiz_exclude_general_css'] = isset($settings_options['quiz_exclude_general_css']) ? esc_attr( $settings_options['quiz_exclude_general_css'] ) : 'off';
        $quiz_exclude_general_css = (isset($settings_options['quiz_exclude_general_css']) && esc_attr( $settings_options['quiz_exclude_general_css'] ) == "on") ? true : false;

        if ( ! $quiz_exclude_general_css ) {
            wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/quiz-maker-public.css', array(), $this->version, 'all');
        }else {
            if ( ! is_front_page() ) {
                wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/quiz-maker-public.css', array(), $this->version, 'all');
            }

        }

        $is_elementor_exists = $this->ays_quiz_is_elementor();
        if ( $is_elementor_exists ) {
            wp_enqueue_style($this->plugin_name.'-font-awesome', plugin_dir_url(__FILE__) . 'css/quiz-maker-font-awesome.min.css', array(), $this->version, 'all');
        }

    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts(){

        $is_elementor_exists = $this->ays_quiz_is_elementor();

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
        if ( ! $is_elementor_exists ) {
            wp_enqueue_script("jquery-effects-core");
            wp_enqueue_script($this->plugin_name .'-select2js', plugin_dir_url(__FILE__) . 'js/quiz-maker-select2.min.js', array('jquery'), $this->version, true);
            wp_enqueue_script($this->plugin_name .'-sweetalert-js', plugin_dir_url(__FILE__) . 'js/quiz-maker-sweetalert2.all.min.js', array('jquery'), $this->version, true );
            wp_enqueue_script($this->plugin_name .'-rate-quiz', plugin_dir_url(__FILE__) . 'js/rating.min.js', array('jquery'), $this->version, true);
            wp_enqueue_script($this->plugin_name .'-functions.js', plugin_dir_url(__FILE__) . 'js/quiz-maker-functions.js', array('jquery'), $this->version, true);
            wp_enqueue_script($this->plugin_name .'-ajax-public', plugin_dir_url(__FILE__) . 'js/quiz-maker-public-ajax.js', array('jquery'), time(), true);
            wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/quiz-maker-public.js', array('jquery'), time(), true);
            wp_localize_script($this->plugin_name . '-ajax-public', 'quiz_maker_ajax_public', array(
                'ajax_url'      => admin_url('admin-ajax.php'),
                'warningIcon'   => plugin_dir_url(__FILE__) . "images/warning.svg",
            ));
            wp_localize_script($this->plugin_name, 'quizLangObj', array(
                'notAnsweredText'       => __( 'You have not answered this question', $this->plugin_name ),
                'areYouSure'            => __( 'Do you want to finish the quiz? Are you sure?', $this->plugin_name ),
                'selectPlaceholder'     => __( 'Select an answer', $this->plugin_name ),
                'correctAnswerVariants' => __( 'Variants of the correct answer', $this->plugin_name ),
                'shareDialog'           => __( 'Share Dialog', $this->plugin_name ),
                'expiredMessage'        => __( 'The quiz has expired!', $this->plugin_name ),
                'day'                   => __( 'day', $this->plugin_name ),
                'days'                  => __( 'days', $this->plugin_name ),
                'hour'                  => __( 'hour', $this->plugin_name ),
                'hours'                 => __( 'hours', $this->plugin_name ),
                'minute'                => __( 'minute', $this->plugin_name ),
                'minutes'               => __( 'minutes', $this->plugin_name ),
                'second'                => __( 'second', $this->plugin_name ),
                'seconds'               => __( 'seconds', $this->plugin_name ),
                'startButtonText'       => $this->buttons_texts['startButton'],
                'defaultStartButtonText'=> __( 'Start', $this->plugin_name ),
                'loadResource'          => __( "Can't load resource.", $this->plugin_name ),
                'somethingWentWrong'    => __( "Maybe something went wrong.", $this->plugin_name ),
                'passwordIsWrong'       => __( 'Password is wrong!', $this->plugin_name ),
                'requiredError'         => __( 'This is a required question', $this->plugin_name ),
                'show'                  => __( 'Show', $this->plugin_name ),
                'hide'                  => __( 'Hide', $this->plugin_name ),
            ) );
        }
    }

    public function ays_generate_quiz_method($attr){
        $id = (isset($attr['id']) && $attr['id'] != '') ? absint(intval($attr['id'])) : null;

        if (is_null($id)) {
            $quiz_content = "<p class='wrong_shortcode_text' style='color:red;'>" . __('Wrong shortcode initialized', $this->plugin_name) . "</p>";
            return str_replace(array("\r\n", "\n", "\r"), "\n", $quiz_content);
        }
        
        $this->enqueue_styles();
        $this->enqueue_scripts();
        
        $quiz_content = $this->show_quiz($id);
        return str_replace(array("\r\n", "\n", "\r"), '', $quiz_content);
    }
    
    public function ays_set_quiz_texts(){

        /*
         * Get Quiz buttons texts from database
         */

        $settings_buttons_texts = $this->settings->ays_get_setting('buttons_texts');
        if($settings_buttons_texts){
            $settings_buttons_texts = json_decode(stripcslashes($settings_buttons_texts), true);
        }else{
            $settings_buttons_texts = array();
        }

        $ays_start_button           = (isset($settings_buttons_texts['start_button']) && $settings_buttons_texts['start_button'] != '') ? stripslashes( esc_attr( $settings_buttons_texts['start_button'] ) ) : 'Start' ;
        $ays_next_button            = (isset($settings_buttons_texts['next_button']) && $settings_buttons_texts['next_button'] != '') ? stripslashes( esc_attr( $settings_buttons_texts['next_button'] ) ) : 'Next' ;
        $ays_previous_button        = (isset($settings_buttons_texts['previous_button']) && $settings_buttons_texts['previous_button'] != '') ? stripslashes( esc_attr( $settings_buttons_texts['previous_button'] ) ) : 'Prev' ;
        $ays_clear_button           = (isset($settings_buttons_texts['clear_button']) && $settings_buttons_texts['clear_button'] != '') ? stripslashes( esc_attr( $settings_buttons_texts['clear_button'] ) ) : 'Clear' ;
        $ays_finish_button          = (isset($settings_buttons_texts['finish_button']) && $settings_buttons_texts['finish_button'] != '') ? stripslashes( esc_attr( $settings_buttons_texts['finish_button'] ) ) : 'Finish' ;
        $ays_see_result_button      = (isset($settings_buttons_texts['see_result_button']) && $settings_buttons_texts['see_result_button'] != '') ? stripslashes( esc_attr( $settings_buttons_texts['see_result_button'] ) ) : 'See Result' ;
        $ays_restart_quiz_button    = (isset($settings_buttons_texts['restart_quiz_button']) && $settings_buttons_texts['restart_quiz_button'] != '') ? stripslashes( esc_attr( $settings_buttons_texts['restart_quiz_button'] ) ) : 'Restart quiz' ;
        $ays_send_feedback_button   = (isset($settings_buttons_texts['send_feedback_button']) && $settings_buttons_texts['send_feedback_button'] != '') ? stripslashes( esc_attr( $settings_buttons_texts['send_feedback_button'] ) ) : 'Send feedback' ;
        $ays_load_more_button       = (isset($settings_buttons_texts['load_more_button']) && $settings_buttons_texts['load_more_button'] != '') ? stripslashes( esc_attr( $settings_buttons_texts['load_more_button'] ) ) : 'Load more' ;
        $ays_exit_button            = (isset($settings_buttons_texts['exit_button']) && $settings_buttons_texts['exit_button'] != '') ? stripslashes( esc_attr( $settings_buttons_texts['exit_button'] ) ) : 'Exit' ;
        $ays_check_button           = (isset($settings_buttons_texts['check_button']) && $settings_buttons_texts['check_button'] != '') ? stripslashes( esc_attr( $settings_buttons_texts['check_button'] ) ) : 'Check' ;
        $ays_login_button           = (isset($settings_buttons_texts['login_button']) && $settings_buttons_texts['login_button'] != '') ? stripslashes( esc_attr( $settings_buttons_texts['login_button'] ) ) : 'Log In' ;

        if ($ays_start_button === 'Start') {
            $ays_start_button_text = __('Start', $this->plugin_name);
        }else{
            $ays_start_button_text = $ays_start_button;
        }

        if ($ays_next_button === 'Next') {
            $ays_next_button_text = __('Next', $this->plugin_name);
        }else{
            $ays_next_button_text = $ays_next_button;
        }

        if ($ays_previous_button === 'Prev') {
            $ays_previous_button_text = __('Prev', $this->plugin_name);
        }else{
            $ays_previous_button_text = $ays_previous_button;
        }

        if ($ays_clear_button === 'Clear') {
            $ays_clear_button_text = __('Clear', $this->plugin_name);
        }else{
            $ays_clear_button_text = $ays_clear_button;
        }
        
        if ($ays_finish_button === 'Finish') {
            $ays_finish_button_text = __('Finish', $this->plugin_name);
        }else{
            $ays_finish_button_text = $ays_finish_button;
        }

        if ($ays_see_result_button === 'See Result') {
            $ays_see_result_button_text = __('See Result', $this->plugin_name);
        }else{
            $ays_see_result_button_text = $ays_see_result_button;
        }

        if ($ays_restart_quiz_button === 'Restart quiz') {
            $ays_restart_quiz_button_text = __('Restart quiz', $this->plugin_name);
        }else{
            $ays_restart_quiz_button_text = $ays_restart_quiz_button;
        }

        if ($ays_send_feedback_button === 'Send feedback') {
            $ays_send_feedback_button_text = __('Send feedback', $this->plugin_name);
        }else{
            $ays_send_feedback_button_text = $ays_send_feedback_button;
        }

        if ($ays_load_more_button === 'Load more') {
            $ays_load_more_button_text = __('Load more', $this->plugin_name);
        }else{
            $ays_load_more_button_text = $ays_load_more_button;
        }

        if ($ays_exit_button === 'Exit') {
            $ays_exit_button_text = __('Exit', $this->plugin_name);
        }else{
            $ays_exit_button_text = $ays_exit_button;
        }

        if ($ays_check_button === 'Check') {
            $ays_check_button_text = __('Check', $this->plugin_name);
        }else{
            $ays_check_button_text = $ays_check_button;
        }

        if ($ays_login_button === 'Log In') {
            $ays_login_button_text = __('Log In', $this->plugin_name);
        }else{
            $ays_login_button_text = $ays_login_button;
        }

        $texts = array(
            'startButton'        => $ays_start_button_text,
            'nextButton'         => $ays_next_button_text,
            'previousButton'     => $ays_previous_button_text,
            'clearButton'        => $ays_clear_button_text,
            'finishButton'       => $ays_finish_button_text,
            'seeResultButton'    => $ays_see_result_button_text,
            'restartQuizButton'  => $ays_restart_quiz_button_text,
            'sendFeedbackButton' => $ays_send_feedback_button_text,
            'loadMoreButton'     => $ays_load_more_button_text,
            'exitButton'         => $ays_exit_button_text,
            'checkButton'        => $ays_check_button_text,
            'loginButton'        => $ays_login_button_text,
        );
        return $texts;
    }

    public function ays_set_quiz_fields_placeholders_texts(){

        /*
         * Get Quiz fields placeholders from database
         */

        $settings_placeholders_texts = $this->settings->ays_get_setting('fields_placeholders');
        if($settings_placeholders_texts){
            $settings_placeholders_texts = json_decode(stripcslashes($settings_placeholders_texts), true);
        }else{
            $settings_placeholders_texts = array();
        }

        $quiz_fields_placeholder_name  = (isset($settings_placeholders_texts['quiz_fields_placeholder_name']) && $settings_placeholders_texts['quiz_fields_placeholder_name'] != '') ? stripslashes( esc_attr( $settings_placeholders_texts['quiz_fields_placeholder_name'] ) ) : 'Name';

        $quiz_fields_placeholder_eamil = (isset($settings_placeholders_texts['quiz_fields_placeholder_eamil']) && $settings_placeholders_texts['quiz_fields_placeholder_eamil'] != '') ? stripslashes( esc_attr( $settings_placeholders_texts['quiz_fields_placeholder_eamil'] ) ) : 'Email';

        $quiz_fields_placeholder_phone = (isset($settings_placeholders_texts['quiz_fields_placeholder_phone']) && $settings_placeholders_texts['quiz_fields_placeholder_phone'] != '') ? stripslashes( esc_attr( $settings_placeholders_texts['quiz_fields_placeholder_phone'] ) ) : 'Phone Number';

        $quiz_fields_label_name  = (isset($settings_placeholders_texts['quiz_fields_label_name']) && $settings_placeholders_texts['quiz_fields_label_name'] != '') ? stripslashes( esc_attr( $settings_placeholders_texts['quiz_fields_label_name'] ) ) : 'Name';

        $quiz_fields_label_eamil = (isset($settings_placeholders_texts['quiz_fields_label_eamil']) && $settings_placeholders_texts['quiz_fields_label_eamil'] != '') ? stripslashes( esc_attr( $settings_placeholders_texts['quiz_fields_label_eamil'] ) ) : 'Email';

        $quiz_fields_label_phone = (isset($settings_placeholders_texts['quiz_fields_label_phone']) && $settings_placeholders_texts['quiz_fields_label_phone'] != '') ? stripslashes( esc_attr( $settings_placeholders_texts['quiz_fields_label_phone'] ) ) : 'Phone Number';

        if ($quiz_fields_placeholder_name === 'Name') {
            $quiz_fields_placeholder_name_text = __('Name', $this->plugin_name);
        }else{
            $quiz_fields_placeholder_name_text = $quiz_fields_placeholder_name;
        }

        if ($quiz_fields_placeholder_eamil === 'Email') {
            $quiz_fields_placeholder_eamil_text = __('Email', $this->plugin_name);
        }else{
            $quiz_fields_placeholder_eamil_text = $quiz_fields_placeholder_eamil;
        }

        if ($quiz_fields_placeholder_phone === 'Phone Number') {
            $quiz_fields_placeholder_phone_text = __('Phone Number', $this->plugin_name);
        }else{
            $quiz_fields_placeholder_phone_text = $quiz_fields_placeholder_phone;
        }

        if ($quiz_fields_label_name === 'Name') {
            $quiz_fields_label_name_text = __('Name', $this->plugin_name);
        }else{
            $quiz_fields_label_name_text = $quiz_fields_label_name;
        }

        if ($quiz_fields_label_eamil === 'Email') {
            $quiz_fields_label_eamil_text = __('Email', $this->plugin_name);
        }else{
            $quiz_fields_label_eamil_text = $quiz_fields_label_eamil;
        }

        if ($quiz_fields_label_phone === 'Phone Number') {
            $quiz_fields_label_phone_text = __('Phone Number', $this->plugin_name);
        }else{
            $quiz_fields_label_phone_text = $quiz_fields_label_phone;
        }

        $texts = array(
            'namePlaceholder'       => $quiz_fields_placeholder_name_text,
            'emailPlaceholder'      => $quiz_fields_placeholder_eamil_text,
            'phonePlaceholder'      => $quiz_fields_placeholder_phone_text,
            'nameLabel'             => $quiz_fields_label_name_text,
            'emailLabel'            => $quiz_fields_label_eamil_text,
            'phoneLabel'            => $quiz_fields_label_phone_text,
        );

        return $texts;
    }

    public function ays_set_quiz_message_variables_data( $id, $quiz ){

        /*
         * Quiz message variables for Start Page
         */

        // Quiz options 
        $options = ( json_decode($quiz['options'], true) != null ) ? json_decode($quiz['options'], true) : array();

        // Quiz Timer
        $quiz_timer = (isset( $options['timer'] ) && intval($options['timer']) != 0 && $options['timer'] != "") ? absint( sanitize_text_field( $options['timer'] ) ) : 100;

        // Quiz title
        $quiz_title = (isset( $quiz['title'] ) && $quiz['title'] != "") ? stripslashes($quiz['title']) : "";

        $question_ids = $this->get_quiz_questions_count($id);

        $questions_count = 0;
        if ( ! empty( $question_ids ) ) {
            $questions_count = count($question_ids);
        }

        $user_first_name = '';
        $user_last_name = '';
        $user_nickname = '';
        $user_display_name = '';
        $user_id = get_current_user_id();
        if($user_id != 0){
            $usermeta = get_user_meta( $user_id );
            if($usermeta !== null){
                $user_first_name = (isset($usermeta['first_name'][0]) && sanitize_text_field( $usermeta['first_name'][0] != '') ) ? sanitize_text_field( $usermeta['first_name'][0] ) : '';
                $user_last_name  = (isset($usermeta['last_name'][0]) && sanitize_text_field( $usermeta['last_name'][0] != '') ) ? sanitize_text_field( $usermeta['last_name'][0] ) : '';
                $user_nickname   = (isset($usermeta['nickname'][0]) && sanitize_text_field( $usermeta['nickname'][0] != '') ) ? sanitize_text_field( $usermeta['nickname'][0] ) : '';
            }

            $current_user_data = get_userdata( $user_id );
            if ( ! is_null( $current_user_data ) && $current_user_data ) {
                $user_display_name = ( isset( $current_user_data->data->display_name ) && $current_user_data->data->display_name != '' ) ? sanitize_text_field( $current_user_data->data->display_name ) : "";
            }
        }

        $message_data = array(
            'quiz_name'         => $quiz_title,
            'time'              => $this->secondsToWords($quiz_timer),
            'user_first_name'   => $user_first_name,
            'user_last_name'    => $user_last_name,
            'questions_count'   => $questions_count,
            'user_nickname'     => $user_nickname,
            'user_display_name' => $user_display_name,
        );

        return $message_data;
    }
    
    public function show_quiz($id){
        $quiz = $this->get_quiz_by_id($id);
        $content = '';
        
        if (is_null($quiz)) {
            $content = "<p class='wrong_shortcode_text' style='color:red;'>" . __('Wrong shortcode initialized', $this->plugin_name) . "</p>";
            return $content;
        }
        if (intval($quiz['published']) === 0) {
            return $content;
        }
        $options = ( json_decode($quiz['options'], true) != null ) ? json_decode($quiz['options'], true) : array();
        $options['quiz_theme'] = (array_key_exists('quiz_theme', $options)) ? $options['quiz_theme'] : '';
        
        // $this->buttons_texts = $this->ays_set_quiz_texts();
        $this->fields_placeholders = $this->ays_set_quiz_fields_placeholders_texts();
        $quiz_parts = $this->ays_quiz_parts($id);
        
        $settings_for_theme = $this->settings;
        $buttons_texts_for_theme = $this->buttons_texts;
        
        switch ($options['quiz_theme']) {
            case 'elegant_dark':
                include_once('partials/class-quiz-theme-elegant-dark.php');
                $theme_obj = new Quiz_Theme_Elegant_Dark(AYS_QUIZ_NAME, AYS_QUIZ_NAME_VERSION, 'elegant_dark', $settings_for_theme, $buttons_texts_for_theme);
                $content = $theme_obj->ays_generate_quiz($quiz_parts);
                break;
            case 'elegant_light':
                include_once('partials/class-quiz-theme-elegant-light.php');
                $theme_obj = new Quiz_Theme_Elegant_Light(AYS_QUIZ_NAME, AYS_QUIZ_NAME_VERSION, 'elegant_light', $settings_for_theme, $buttons_texts_for_theme);
                $content = $theme_obj->ays_generate_quiz($quiz_parts);
                break;
            case 'rect_light':
                include_once('partials/class-quiz-theme-rect-light.php');
                $theme_obj = new Quiz_Theme_Rect_Light(AYS_QUIZ_NAME, AYS_QUIZ_NAME_VERSION, 'rect_light', $settings_for_theme, $buttons_texts_for_theme);
                $content = $theme_obj->ays_generate_quiz($quiz_parts);
                break;
            case 'rect_dark':
                include_once('partials/class-quiz-theme-rect-dark.php');
                $theme_obj = new Quiz_Theme_Rect_Dark(AYS_QUIZ_NAME, AYS_QUIZ_NAME_VERSION, 'rect_dark', $settings_for_theme, $buttons_texts_for_theme);
                $content = $theme_obj->ays_generate_quiz($quiz_parts);
                break;
            default:
                $content = $this->ays_generate_quiz($quiz_parts);
        }
        return $content;
    }

    public function ays_quiz_parts($id){
        
        global $wpdb;
        global $wp_embed;
        
    /*******************************************************************************************************/
        
        /*
         * Get Quiz data from database by id
         * Separation options from quiz data
         */
        $quiz = $this->get_quiz_by_id($id);
        $options = json_decode($quiz['options'], true);
        
        $settings_options = $this->settings->ays_get_setting('options');
        if($settings_options){
            $settings_options = json_decode(stripcslashes($settings_options), true);
        }else{
            $settings_options = array();
        }

        /*
         * Quiz message variables for Start Page
         */

        $message_variables_data = $this->ays_set_quiz_message_variables_data( $id, $quiz );

        
    /*******************************************************************************************************/
                
        $randomize_answers = false;
        $questions = null;
        $randomize_questions = false;
        $questions_ordering_by_cat = false;
        $quiz_questions_ids = "";
        
        $arr_questions = ($quiz["question_ids"] == "") ? array() : explode(',', $quiz["question_ids"]);
        $arr_questions = (count($arr_questions) == 1 && $arr_questions[0] == '') ? array() : $arr_questions;
        $quiz_questions_ids = implode(',', $arr_questions);
        if (isset($options['randomize_questions']) && $options['randomize_questions'] == 'on') {
            $randomize_questions = true;
            shuffle($arr_questions);
        }
        if (isset($options['enable_question_bank']) && $options['enable_question_bank'] == 'on' && 
            isset($options['questions_count']) && intval($options['questions_count']) > 0 &&
            $options['questions_count'] <= count($arr_questions)) {
            $random_questions = array_rand($arr_questions, intval($options['questions_count']));
            if (!is_array($random_questions)) {
               $random_questions = array($random_questions);
            }       

            foreach ($random_questions as $key => $question) {
                $random_questions[$key] = strval($arr_questions[$question]);
            }
            $arr_questions = $random_questions;
            $quiz_questions_ids = join(',', $random_questions);
        }

        if(isset($options['enable_questions_ordering_by_cat']) && $options['enable_questions_ordering_by_cat'] == "on"){
            $questions_ordering_by_cat = true;
        }

        $quest_s = $this->get_quiz_questions_by_ids($arr_questions);
        $quests = array();
        foreach($quest_s as $quest){
            $quests[$quest['id']] = $quest;
        }

        $question_bank_categories = $this->get_question_bank_categories($quiz_questions_ids);

        if(count($arr_questions) > 0){
            if($questions_ordering_by_cat){
                $question_bank_questions = array();
                $question_bank_cats = array();
                $quiz_questions_ids = array();

                foreach($arr_questions as $key => $val){
                    $question_bank_questions[$val] = $quests[$val];
                    if(isset($question_bank_categories[$quests[$val]['category_id']])){
                        $question_bank_cats[$quests[$val]['category_id']][] = strval($val);
                    }
                }

                if ($randomize_questions) {
                    $question_bank_cats = $this->ays_shuffle_assoc($question_bank_cats);

                    foreach ($question_bank_cats as $key => $value) {
                        shuffle($question_bank_cats[$key]);
                    }
                }

                $arr_questions = array();
                foreach($question_bank_cats as $key => $value){
                    $arr_questions = array_merge($arr_questions, $value);
                }

                $quiz_questions_ids = implode(',', $arr_questions);
            }
        }

        $questions_count = count($arr_questions);
        
        if (isset($options['randomize_answers']) && $options['randomize_answers'] == 'on') {
            $randomize_answers = true;
        }else{
            $randomize_answers = false;
        }

        if(isset($options['enable_correction']) && $options['enable_correction'] == "on"){
            $enable_correction = true;
        }else{
            $enable_correction = false;
        }
        

    /*******************************************************************************************************/
        
        /*
         * Quiz information form fields
         *
         * Checking required filelds
         *
         * Creating HTML code for printing
         */
        
        $form_inputs = null;
        $show_form = null;
        $required_fields = (array_key_exists('required_fields', $options) && !is_null($options['required_fields'])) ? $options['required_fields'] : array();
        
        $name_required = (in_array('ays_user_name', $required_fields)) ? 'required' : '';
        $email_required = (in_array('ays_user_email', $required_fields)) ? 'required' : '';
        $phone_required = (in_array('ays_user_phone', $required_fields)) ? 'required' : '';
        
        $form_title = "";
        if(isset($options['form_title']) && $options['form_title'] != ''){
            $form_title = $this->ays_autoembed($options['form_title']);
        }

        // Display form fields labels
        $options['display_fields_labels'] = isset($options['display_fields_labels']) ? $options['display_fields_labels'] : 'on';
        $display_fields_labels = (isset($options['display_fields_labels']) && $options['display_fields_labels'] == 'on') ? true : false;

        if($options['form_name'] == "on"){
            $show_form = "show";
            if( $display_fields_labels ){
                $form_inputs .= "<label for='ays_form_field_user_name_". $id ."'>". $this->fields_placeholders['nameLabel'] ."</label>";
            }
            $form_inputs .= "<input type='text' id='ays_form_field_user_name_". $id ."' name='ays_user_name' placeholder='". $this->fields_placeholders['namePlaceholder'] ."' class='ays_quiz_form_input ays_animated_x5ms' " . $name_required . ">";
        }else{
            $form_inputs .= "<input type='hidden' name='ays_user_name' placeholder='". $this->fields_placeholders['namePlaceholder'] ."' value=''>";
        }
        if($options['form_email'] == "on"){
            $show_form = "show";
            if( $display_fields_labels ){
                $form_inputs .= "<label for='ays_form_field_user_email_". $id ."'>". $this->fields_placeholders['emailLabel'] ."</label>";
            }
            $form_inputs .= "<input type='text' id='ays_form_field_user_email_". $id ."' name='ays_user_email' placeholder='". $this->fields_placeholders['emailPlaceholder'] ."' class='ays_quiz_form_input ays_animated_x5ms' " . $email_required . ">";
        }else{
            $form_inputs .= "<input type='hidden' name='ays_user_email' placeholder='". $this->fields_placeholders['emailPlaceholder'] ."' value=''>";
        }
        if($options['form_phone'] == "on"){
            $show_form = "show";
            if( $display_fields_labels ){
                $form_inputs .= "<label for='ays_form_field_user_phone_". $id ."'>". $this->fields_placeholders['phoneLabel'] ."</label>";
            }
            $form_inputs .= "<input type='text' id='ays_form_field_user_phone_". $id ."' name='ays_user_phone' placeholder='". $this->fields_placeholders['phonePlaceholder'] ."' class='ays_quiz_form_input ays_animated_x5ms' " . $phone_required . ">";
        }else{
            $form_inputs .= "<input type='hidden' name='ays_user_phone' placeholder='". $this->fields_placeholders['phonePlaceholder'] ."' value=''>";
        }
        
        // Show information form to logged in users
        $options['show_information_form'] = isset($options['show_information_form']) ? $options['show_information_form'] : 'on';
        $show_information_form = (isset($options['show_information_form']) && $options['show_information_form'] == 'on') ? true : false;
        
    /*******************************************************************************************************/
        
        /*
         * Quiz colors
         * 
         * Quiz container colors
         */
        
        // Quiz container background color
        
        if(isset($options['bg_color']) && $options['bg_color'] != ''){
            $bg_color = $options['bg_color'];
        }else{
            $bg_color = "#fff";
        }
        
        // Color of elements inside quiz container
        
        if(isset($options['color']) && $options['color'] != ''){
            $color = $options['color'];
        }else{
            $color = "#27ae60";
        }
        
        // Color of text inside quiz container
        
        if(isset($options['text_color']) && $options['text_color'] != ''){
            $text_color = $options['text_color'];
        }else{
            $text_color = "#333";
        }
        
        // Color of text of buttons inside quiz container
        
        if(isset($options['buttons_text_color']) && $options['buttons_text_color'] != ''){
            $buttons_text_color = $options['buttons_text_color'];
        }else{
            $buttons_text_color = $text_color;
        }
        
        // Quiz container shadow color
        
        // CHecking exists box shadow option
        $options['enable_box_shadow'] = (!isset($options['enable_box_shadow'])) ? 'on' : $options['enable_box_shadow'];
        
        if(isset($options['box_shadow_color']) && $options['box_shadow_color'] != ''){
            $box_shadow_color = $options['box_shadow_color'];
        }else{
            $box_shadow_color = "#333";
        }
        
        // Quiz container border color
        
        if(isset($options['quiz_border_color']) && $options['quiz_border_color'] != ''){
            $quiz_border_color = $options['quiz_border_color'];
        }else{
            $quiz_border_color = '#000';
        }
                
        
    /*******************************************************************************************************/ 
        
        /*
         * Quiz styles
         *
         * Quiz container styles
         */
        
        
        // Quiz container minimal height
        
        if(isset($options['height']) && $options['height'] != ''){
            $quiz_height = $options['height'];
        }else{
            $quiz_height = '400';
        }
        
        // Quiz container width
        
        if(isset($options['width']) && $options['width'] != '' && absint( $options['width'] ) > 0){
            if (isset($options['quiz_width_by_percentage_px']) && $options['quiz_width_by_percentage_px'] == 'percentage') {
                if (absint(intval($options['width'])) > 100 ) {
                    $quiz_width = '100%';
                }else{
                    $quiz_width = $options['width'] . '%';
                }
            }else{
                $quiz_width = $options['width'] . 'px';
            }
        }else{
            $quiz_width = '100%';
        }
        
        
        // Quiz container max-width for mobile
        if(isset($options['mobile_max_width']) && $options['mobile_max_width'] != '' && absint( $options['mobile_max_width'] ) > 0){
            $mobile_max_width = $options['mobile_max_width'] . '%';
        }else{
            $mobile_max_width = '100%';
        }

        // Quiz title transformation
        $quiz_title_transformation = (isset($options['quiz_title_transformation']) && sanitize_text_field( $options['quiz_title_transformation'] ) != "") ? sanitize_text_field( $options['quiz_title_transformation'] ) : 'uppercase';

        // Quiz title font size
        $quiz_title_font_size = (isset($options['quiz_title_font_size']) && ( $options['quiz_title_font_size'] ) != '' && ( $options['quiz_title_font_size'] ) != 0) ? esc_attr( absint( $options['quiz_title_font_size'] ) ) : 21;

        // Quiz title font size | On mobile
        $quiz_title_mobile_font_size = (isset($options['quiz_title_mobile_font_size']) && sanitize_text_field($options['quiz_title_mobile_font_size']) != '') ? esc_attr( absint($options['quiz_title_mobile_font_size']) ) : 21;

        // Quiz title text shadow
        $options['quiz_enable_title_text_shadow'] = isset($options['quiz_enable_title_text_shadow']) ? esc_attr($options['quiz_enable_title_text_shadow']) : 'off';
        $quiz_enable_title_text_shadow = (isset($options['quiz_enable_title_text_shadow']) && $options['quiz_enable_title_text_shadow'] == 'on') ? true : false;

        // Quiz title text shadow color
        $quiz_title_text_shadow_color = (isset($options['quiz_title_text_shadow_color']) && $options['quiz_title_text_shadow_color'] != '') ? esc_attr($options['quiz_title_text_shadow_color']) : '#333';

        // Quiz Title Text Shadow X offset
        $quiz_title_text_shadow_x_offset = (isset($options['quiz_title_text_shadow_x_offset']) && ( $options['quiz_title_text_shadow_x_offset'] ) != '' && ( $options['quiz_title_text_shadow_x_offset'] ) != 0) ? esc_attr( intval( $options['quiz_title_text_shadow_x_offset'] ) ) : 2;

        // Quiz Title Text Shadow Y offset
        $quiz_title_text_shadow_y_offset = (isset($options['quiz_title_text_shadow_y_offset']) && ( $options['quiz_title_text_shadow_y_offset'] ) != '' && ( $options['quiz_title_text_shadow_y_offset'] ) != 0) ? esc_attr( intval( $options['quiz_title_text_shadow_y_offset'] ) ) : 2;

        // Quiz Title Text Shadow Z offset
        $quiz_title_text_shadow_z_offset = (isset($options['quiz_title_text_shadow_z_offset']) && ( $options['quiz_title_text_shadow_z_offset'] ) != '' && ( $options['quiz_title_text_shadow_z_offset'] ) != 0) ? esc_attr( intval( $options['quiz_title_text_shadow_z_offset'] ) ) : 2;

        $title_text_shadow_offsets = $quiz_title_text_shadow_x_offset . 'px ' . $quiz_title_text_shadow_y_offset . 'px ' . $quiz_title_text_shadow_z_offset . 'px ';

        // Quiz image height
        $quiz_image_height = (isset($options['quiz_image_height']) && sanitize_text_field( $options['quiz_image_height'] ) != '') ? absint( sanitize_text_field( $options['quiz_image_height'] ) ) : '';

        
        // Quiz container border radius
        
        // Modified border radius for Pass count option and Rate avg option
        $quiz_modified_border_radius = "";
        
        if(isset($options['quiz_border_radius']) && $options['quiz_border_radius'] != ''){
            $quiz_border_radius = $options['quiz_border_radius'];
        }else{
            $quiz_border_radius = '3px';
        }
        
        // Quiz container shadow enabled/disabled
        
        if(isset($options['enable_box_shadow']) && $options['enable_box_shadow'] == 'on'){
            $enable_box_shadow = true;
        }else{
            $enable_box_shadow = false;
        }

        //  Box Shadow X offset
        $quiz_box_shadow_x_offset = (isset($options['quiz_box_shadow_x_offset']) && sanitize_text_field( $options['quiz_box_shadow_x_offset'] ) != '' && sanitize_text_field( $options['quiz_box_shadow_x_offset'] ) != 0) ? intval( sanitize_text_field( $options['quiz_box_shadow_x_offset'] ) ) : 0;

        //  Box Shadow Y offset
        $quiz_box_shadow_y_offset = (isset($options['quiz_box_shadow_y_offset']) && sanitize_text_field( $options['quiz_box_shadow_y_offset'] ) != '' && sanitize_text_field( $options['quiz_box_shadow_y_offset'] ) != 0) ? intval( sanitize_text_field( $options['quiz_box_shadow_y_offset'] ) ) : 0;

        //  Box Shadow Z offset
        $quiz_box_shadow_z_offset = (isset($options['quiz_box_shadow_z_offset']) && sanitize_text_field( $options['quiz_box_shadow_z_offset'] ) != '' && sanitize_text_field( $options['quiz_box_shadow_z_offset'] ) != 0) ? intval( sanitize_text_field( $options['quiz_box_shadow_z_offset'] ) ) : 15;

        $box_shadow_offsets = $quiz_box_shadow_x_offset . 'px ' . $quiz_box_shadow_y_offset . 'px ' . $quiz_box_shadow_z_offset . 'px ';
        
        // Quiz container background image
        
        if(isset($options['quiz_bg_image']) && $options['quiz_bg_image'] != ''){
            $ays_quiz_bg_image = $options['quiz_bg_image'];
        }else{
            $ays_quiz_bg_image = null;
        }
        
        // Quiz container background image position
        $quiz_bg_image_position = "center center";

        if(isset($options['quiz_bg_image_position']) && $options['quiz_bg_image_position'] != ""){
            $quiz_bg_image_position = $options['quiz_bg_image_position'];
        }

		// Hide quiz background image on the result page
        $quiz_bg_img_in_finish_page = "false";

        if(isset($options['quiz_bg_img_in_finish_page']) && $options['quiz_bg_img_in_finish_page'] == "on"){
            $quiz_bg_img_in_finish_page = "true";
        }

        // Hide background image on start page
        $options['quiz_bg_img_on_start_page'] = isset($options['quiz_bg_img_on_start_page']) ? $options['quiz_bg_img_on_start_page'] : 'off';
        $quiz_bg_img_on_start_page = (isset($options['quiz_bg_img_on_start_page']) && $options['quiz_bg_img_on_start_page'] == 'on') ? true : false;

        $quiz_bg_img_class = '';
        if ( $quiz_bg_img_on_start_page ) {
            $quiz_bg_img_class = 'ays_quiz_hide_bg_on_start_page';
        }

        
        /*
         * Quiz container border enabled/disabled
         *
         * Quiz container border width
         *
         * Quiz container border style
         */
        
        if(isset($options['enable_border']) && $options['enable_border'] == 'on'){
            $enable_border = true;
        }else{
            $enable_border = false;
        }
        
        if(isset($options['quiz_border_width']) && $options['quiz_border_width'] != ''){
            $quiz_border_width = $options['quiz_border_width'];
        }else{
            $quiz_border_width = '1';
        }
        
        if(isset($options['quiz_border_style']) && $options['quiz_border_style'] != ''){
            $quiz_border_style = $options['quiz_border_style'];
        }else{
            $quiz_border_style = 'solid';
        }
        
        // Questions image width, height and sizing
        
        // Image Width(px)
        $image_width = (isset($options['image_width']) && sanitize_text_field($options['image_width']) != '' && absint( sanitize_text_field($options['image_width']) ) > 0) ? absint( sanitize_text_field($options['image_width']) ) : '';

        // Quiz image width percentage/px
        $quiz_image_width_by_percentage_px = (isset($options['quiz_image_width_by_percentage_px']) && sanitize_text_field( $options['quiz_image_width_by_percentage_px'] ) != '') ? sanitize_text_field( $options['quiz_image_width_by_percentage_px'] ) : 'pixels';

        if($image_width != ''){
            if ($quiz_image_width_by_percentage_px == 'percentage') {
                if ($image_width > 100 ) {
                    $question_image_width = '100%';
                }else{
                    $question_image_width = $image_width . '%';
                }
            }else{
                $question_image_width = $image_width . 'px';
            }
        }else{
            $question_image_width = "100%";
        }

        if(isset($options['image_height']) && $options['image_height'] != ''){
            $question_image_height = $options['image_height'] . 'px';
        }else{
            $question_image_height = "auto";
        }
        
        if(isset($options['image_sizing']) && $options['image_sizing'] != ''){
            $question_image_sizing = $options['image_sizing'];
        }else{
            $question_image_sizing = "cover";
        }
        
        // Answers font size
        
        $answers_font_size = '15';
        if(isset($options['answers_font_size']) && $options['answers_font_size'] != ""){
            $answers_font_size = $options['answers_font_size'];
        }

        // Answer font size | On mobile
        $answers_mobile_font_size = ( isset($options['answers_mobile_font_size']) && $options['answers_mobile_font_size'] != "" ) ? absint( sanitize_text_field( $options['answers_mobile_font_size'] ) ) : 15;

        // Question Font Size
        
        $question_font_size = '16';
        if(isset($options['question_font_size']) && $options['question_font_size'] != ""){
            $question_font_size = $options['question_font_size'];
        }

        // Question font size | On mobile
        $question_mobile_font_size = ( isset($options['question_mobile_font_size']) && $options['question_mobile_font_size'] != "" ) ? absint( sanitize_text_field( $options['question_mobile_font_size'] ) ) : 16;


        // Disable answer hover
        $options['disable_hover_effect'] = isset($options['disable_hover_effect']) ? $options['disable_hover_effect'] : 'off';
        $disable_hover_effect = (isset($options['disable_hover_effect']) && $options['disable_hover_effect'] == "on") ? true : false;

        // Question text alignment
        $quiz_question_text_alignment = (isset($options['quiz_question_text_alignment']) && sanitize_text_field( $options['quiz_question_text_alignment'] ) != '') ? sanitize_text_field( $options['quiz_question_text_alignment'] ) : 'center';
        

        // Answers border options
        $options['answers_border'] = (isset($options['answers_border'])) ? $options['answers_border'] : 'on';
        $answers_border = false;
        if(isset($options['answers_border']) && $options['answers_border'] == 'on'){
            $answers_border = true;
        }
        $answers_border_width = '1';
        if(isset($options['answers_border_width']) && $options['answers_border_width'] != ''){
            $answers_border_width = $options['answers_border_width'];
        }
        $answers_border_style = 'solid';
        if(isset($options['answers_border_style']) && $options['answers_border_style'] != ''){
            $answers_border_style = $options['answers_border_style'];
        }
        $answers_border_color = '#444';
        if(isset($options['answers_border_color']) && $options['answers_border_color'] != ''){
            $answers_border_color = $options['answers_border_color'];
        }

        // Answers margin option
        $answers_margin = 10;
        if(isset($options['answers_margin']) && $options['answers_margin'] != ''){
            $answers_margin = intval( $options['answers_margin'] );
        }

        /* 
         * Quiz container background gradient
         * 
         */
        
        // Checking exists background gradient option
        $options['enable_background_gradient'] = (!isset($options['enable_background_gradient'])) ? "off" : $options['enable_background_gradient'];
        
        if(isset($options['background_gradient_color_1']) && $options['background_gradient_color_1'] != ''){
            $background_gradient_color_1 = $options['background_gradient_color_1'];
        }else{
            $background_gradient_color_1 = "#000";
        }

        if(isset($options['background_gradient_color_2']) && $options['background_gradient_color_2'] != ''){
            $background_gradient_color_2 = $options['background_gradient_color_2'];
        }else{
            $background_gradient_color_2 = "#fff";
        }

        if(isset($options['quiz_gradient_direction']) && $options['quiz_gradient_direction'] != ''){
            $quiz_gradient_direction = $options['quiz_gradient_direction'];
        }else{
            $quiz_gradient_direction = 'vertical';
        }
        switch($quiz_gradient_direction) {
            case "horizontal":
                $quiz_gradient_direction = "to right";
                break;
            case "diagonal_left_to_right":
                $quiz_gradient_direction = "to bottom right";
                break;
            case "diagonal_right_to_left":
                $quiz_gradient_direction = "to bottom left";
                break;
            default:
                $quiz_gradient_direction = "to bottom";
        }

        // Quiz container background gradient enabled/disabled
        
        if(isset($options['enable_background_gradient']) && $options['enable_background_gradient'] == "on"){
            $enable_background_gradient = true;
        }else{
            $enable_background_gradient = false;
        }

        // Answers box shadow
        $answers_box_shadow = false;
        $answers_box_shadow_color = '#000';
        if(isset($options['answers_box_shadow']) && $options['answers_box_shadow'] == 'on'){
            $answers_box_shadow = true;
        }
        if(isset($options['answers_box_shadow_color']) && $options['answers_box_shadow_color'] != ''){
            $answers_box_shadow_color = $options['answers_box_shadow_color'];
        }

        //  Box Shadow X offset
        $quiz_answer_box_shadow_x_offset = (isset($options['quiz_answer_box_shadow_x_offset']) && sanitize_text_field( $options['quiz_answer_box_shadow_x_offset'] ) != '' && sanitize_text_field( $options['quiz_answer_box_shadow_x_offset'] ) != 0) ? intval( sanitize_text_field( $options['quiz_answer_box_shadow_x_offset'] ) ) : 0;

        //  Box Shadow Y offset
        $quiz_answer_box_shadow_y_offset = (isset($options['quiz_answer_box_shadow_y_offset']) && sanitize_text_field( $options['quiz_answer_box_shadow_y_offset'] ) != '' && sanitize_text_field( $options['quiz_answer_box_shadow_y_offset'] ) != 0) ? intval( sanitize_text_field( $options['quiz_answer_box_shadow_y_offset'] ) ) : 0;

        //  Box Shadow Z offset
        $quiz_answer_box_shadow_z_offset = (isset($options['quiz_answer_box_shadow_z_offset']) && sanitize_text_field( $options['quiz_answer_box_shadow_z_offset'] ) != '' && sanitize_text_field( $options['quiz_answer_box_shadow_z_offset'] ) != 0) ? intval( sanitize_text_field( $options['quiz_answer_box_shadow_z_offset'] ) ) : 10;

        $answer_box_shadow_offsets = $quiz_answer_box_shadow_x_offset . 'px ' . $quiz_answer_box_shadow_y_offset . 'px ' . $quiz_answer_box_shadow_z_offset . 'px ';


        // Answers right/wrong icons
        $ans_right_wrong_icon = 'default';
        if(isset($options['ans_right_wrong_icon']) && $options['ans_right_wrong_icon'] != ''){
            $ans_right_wrong_icon = $options['ans_right_wrong_icon'];
        }


        // Buttons position
        $buttons_position = 'center';
        if(isset($options['buttons_position']) && $options['buttons_position'] != ''){
            $buttons_position = $options['buttons_position'];
        }

         /*
        ==========================================
            Buttons styles
        ==========================================
        */

        // Buttons font size
        $buttons_font_size = '17px';
        if(isset($options['buttons_font_size']) && $options['buttons_font_size'] != ''){
            $buttons_font_size = $options['buttons_font_size'] . 'px';
        }

        // Button font-size (px) | Mobile
        $buttons_mobile_font_size = (isset($options['buttons_mobile_font_size']) && $options['buttons_mobile_font_size'] != '') ? absint( esc_attr( $options['buttons_mobile_font_size'] ) ) : 17;

        // Buttons font size
        $buttons_width = '';
        if(isset($options['buttons_width']) && $options['buttons_width'] != ''){
            $buttons_width = $options['buttons_width'] . 'px';
        }

        $buttons_width_html = '';
        if( $buttons_width != ''){
            $buttons_width_html = "width:" . $buttons_width;
        }

        // Buttons Left / Right padding
        $buttons_left_right_padding = '20px';
        if(isset($options['buttons_left_right_padding']) && $options['buttons_left_right_padding'] != ''){
            $buttons_left_right_padding = $options['buttons_left_right_padding'] . 'px';
        }

        // Buttons Top / Bottom padding
        $buttons_top_bottom_padding = '10px';
        if(isset($options['buttons_top_bottom_padding']) && $options['buttons_top_bottom_padding'] != ''){
            $buttons_top_bottom_padding = $options['buttons_top_bottom_padding'] . 'px';
        }

        // Buttons border radius
        $buttons_border_radius = '3px';
        if(isset($options['buttons_border_radius']) && $options['buttons_border_radius'] != ''){
            $buttons_border_radius = $options['buttons_border_radius'] . 'px';
        }
        
    /*******************************************************************************************************/
        
        /*
         * Quiz start page
         *
         * Quiz title
         * Quiz desctiption
         * Quiz image
         *
         * Quiz Start button
         */
        
        $title = do_shortcode(stripslashes($quiz['title']));
        
        $description = $this->ays_autoembed( $quiz['description'] );
        
        $quiz_image = $quiz['quiz_image'];
        
        
        $quiz_rate_reports = '';
        $quiz_result_reports = '';
        
        
        if($questions_count == 0){
            $empty_questions_notification = '<p id="ays_no_questions_message" style="color:red">' . __('You need to add questions', $this->plugin_name) . '</p>';
            $empty_questions_button = "disabled";
        }else{
            $empty_questions_notification = "";
            $empty_questions_button = "";
        }

        $password_message = "";
        $start_button_disabled = "";
        $quiz_password_message_html = "";
        $password_message_with_toggle = "";

        // Password quiz
        $quiz_password = ( isset( $options['password_quiz']) && $options['password_quiz'] != '' ) ? stripslashes( $options['password_quiz'] ) : '';

        // Quiz password width
        $quiz_password_width = (isset($options['quiz_password_width']) && ( $options['quiz_password_width'] ) != '' && ( $options['quiz_password_width'] ) != 0) ? esc_attr( absint( $options['quiz_password_width'] ) ) : "";

        $quiz_password_width_css = "";
        if ( $quiz_password_width != "" ) {
            $quiz_password_width_css = $quiz_password_width . "px";
        } else {
            $quiz_password_width_css = "100%";
        }

        if(isset($options['enable_password']) && $options['enable_password'] == 'on' && $quiz_password != ""){

            // Password for passing quiz | Message
            $quiz_password_message = ( isset( $options['quiz_password_message']) && $options['quiz_password_message'] != '' ) ? stripslashes( $options['quiz_password_message'] ) : '';

            // Enable toggle password visibility
            $options['quiz_enable_password_visibility'] = isset($options['quiz_enable_password_visibility']) ? $options['quiz_enable_password_visibility'] : 'off';
            $quiz_enable_password_visibility = (isset($options['quiz_enable_password_visibility']) && $options['quiz_enable_password_visibility'] == 'on') ? true : false;

            if ( $quiz_password_message != '' ) {
                $quiz_password_message_html .= '<div class="ays-quiz-password-message-box">';
                    $quiz_password_message_html .= $this->ays_autoembed($quiz_password_message);
                $quiz_password_message_html .= '</div>';
            }

            $password_message = "<input type='password' autocomplete='no' id='ays_quiz_password_val_". $id ."' class='ays_quiz_password' placeholder='". __( "Please enter password", $this->plugin_name) ."'>";

            if ( $quiz_enable_password_visibility ) {
                $password_message_with_toggle .= "<div class='ays-quiz-password-toggle-visibility-box'>";
                    $password_message_with_toggle .= $password_message;
                    $password_message_with_toggle .= "<img src='". AYS_QUIZ_PUBLIC_URL ."/images/quiz-maker-eye-visibility-off.svg' class='ays-quiz-password-toggle ays-quiz-password-toggle-visibility-off'>";
                    $password_message_with_toggle .= "<img src='". AYS_QUIZ_PUBLIC_URL ."/images/quiz-maker-eye-visibility.svg' class='ays-quiz-password-toggle ays-quiz-password-toggle-visibility ays_display_none'>";
                $password_message_with_toggle .= "</div>";

                $password_message = $password_message_with_toggle;
            }

            $start_button_disabled = " disabled='disabled' ";
        }

        // Checking confirmation box for leaving the page enabled or diabled
        if (isset($options['enable_leave_page']) && $options['enable_leave_page'] == 'on') {
            $enable_leave_page = 'data-enable-leave-page="false"';
        }elseif (! isset($options['enable_leave_page'])) {
            $enable_leave_page = 'data-enable-leave-page="false"';
        }
        else{
            $enable_leave_page = '';
        }

        // Disable answer hover
        $settings_options['enable_start_button_loader'] = isset($settings_options['enable_start_button_loader']) ? sanitize_text_field($settings_options['enable_start_button_loader']) : 'off';
        $enable_start_button_loader = (isset($settings_options['enable_start_button_loader']) && sanitize_text_field($settings_options['enable_start_button_loader']) == "on") ? true : false;
        
        $quiz_start_button = "<input type='button' $empty_questions_button $start_button_disabled class='ays_next start_button action-button' value='". $this->buttons_texts['startButton'] ."' ". $enable_leave_page ." />" . $empty_questions_notification;


        if ( $enable_start_button_loader ) {
            $is_elementor_exists = $this->ays_quiz_is_elementor();
            if ( $is_elementor_exists ) {
                $enable_start_button_loader = false;
            }
        }

        if ( $enable_start_button_loader ) {
            if ($questions_count != 0) {
                $quiz_start_butto_html = "<input type='button' $empty_questions_button class='ays_next start_button action-button ays_quiz_enable_loader' disabled='disabled' value='". __('Loading ...', $this->plugin_name) ."' ". $enable_leave_page ." />".$empty_questions_notification;

                $quiz_start_button = '
                <div class="ays-quiz-start-button-preloader">
                    '. $quiz_start_butto_html .'
                    <img src="'. AYS_QUIZ_ADMIN_URL .'/images/loaders/tail-spin.svg" class="ays_quiz_start_button_loader">
                </div>';
            }
        }
        
        
        /*
         * Show quiz head information
         * Show quiz title and description
         */
        
        $options['show_quiz_title'] = isset($options['show_quiz_title']) ? $options['show_quiz_title'] : 'on';
        $options['show_quiz_desc'] = isset($options['show_quiz_desc']) ? $options['show_quiz_desc'] : 'on';
        $show_quiz_title = (isset($options['show_quiz_title']) && $options['show_quiz_title'] == "on") ? true : false;
        $show_quiz_desc = (isset($options['show_quiz_desc']) && $options['show_quiz_desc'] == "on") ? true : false;

        
        /* 
         * Quiz passed users count
         *
         * Generate HTML code
         */
        
        if(isset($options['enable_pass_count']) && $options['enable_pass_count'] == 'on'){
            $enable_pass_count = true;
            $quiz_result_reports = $this->get_quiz_results_count_by_id($id);
            $quiz_result_reports = "<qm_users_count class='ays_quizn_ancnoxneri_qanak'><i class='ays_fa ays_fa_users'></i> ".$quiz_result_reports['res_count']."</qm_users_count>";
            $quiz_modified_border_radius = "border-radius:" . $quiz_border_radius . "px " . $quiz_border_radius . "px 0px " . $quiz_border_radius . "px;";
        }else{
            $enable_pass_count = false;
        }
        
        
        
        /* 
         * Quiz average rate
         *
         * Generate HTML code
         */
        
        $quiz_rates_avg = round($this->ays_get_average_of_rates($id), 1);
        $quiz_rates_count = $this->ays_get_count_of_rates($id);
        if(isset($options['enable_rate_avg']) && $options['enable_rate_avg'] == 'on'){
            $enable_rate_avg = true;
            $quiz_rate_reports = "<div class='ays_quiz_rete_avg'>
                <div class='for_quiz_rate_avg ui star rating' data-rating='".round($quiz_rates_avg)."' data-max-rating='5'></div>
                <qm_votes>$quiz_rates_count " . __( "votes", $this->plugin_name ) . ", $quiz_rates_avg " . __( "avg", $this->plugin_name ) . "</qm_votes>
            </div>";
            $quiz_modified_border_radius = "border-radius:" . $quiz_border_radius . "px " . $quiz_border_radius . "px " . $quiz_border_radius . "px 0px;";
        }else{
            $enable_rate_avg = false;
        }
        
        
        
        /* 
         * Generate HTML code when passed users count and average rate both are enabled
         */
        
        if($enable_rate_avg && $enable_pass_count){
            $quiz_modified_border_radius = "border-radius:" . $quiz_border_radius . "px " . $quiz_border_radius . "px 0px 0px;";
            $ays_quiz_reports = "<div class='ays_quiz_reports'>$quiz_rate_reports $quiz_result_reports</div>";
        }else{
            $ays_quiz_reports = $quiz_rate_reports.$quiz_result_reports;
        }
        
        /* 
         * Generate HTML code when passed users count and average rate both are enabled
         * 
         * Show quiz author and create date
         */
        
        // Show quiz category
        if(isset($options['show_category']) && $options['show_category'] == "on"){
            $show_category = true;
        }else{
            $show_category = false;
        }
        
        // Show question category
        if(isset($options['show_question_category']) && $options['show_question_category'] == "on"){
            $show_question_category = true;
        }else{
            $show_question_category = false;
        }

        // Show question category description
        $options['quiz_enable_question_category_description'] = isset($options['quiz_enable_question_category_description']) ? $options['quiz_enable_question_category_description'] : 'off';
        $quiz_enable_question_category_description = (isset($options['quiz_enable_question_category_description']) && $options['quiz_enable_question_category_description'] == 'on') ? true : false;
        
        if(isset($options['show_create_date']) && $options['show_create_date'] == "on"){
            $show_create_date = true;
        }else{
            $show_create_date = false;
        }
        
        if(isset($options['show_author']) && $options['show_author'] == "on"){
            $show_author = true;
        }else{
            $show_author = false;
        }
        
        $show_cd_and_author = "<div class='ays_cb_and_a'>";
        if($show_create_date){
            $quiz_create_date = (isset($options['create_date']) && $options['create_date'] != '') ? $options['create_date'] : "0000-00-00 00:00:00";
            if(Quiz_Maker_Admin::validateDate($quiz_create_date)){
                $show_cd_and_author .= "<span>".__("Created on",$this->plugin_name)." </span><strong><time>".date_i18n("F d, Y", strtotime($quiz_create_date))."</time></strong>";
            }else{
                $show_cd_and_author .= "";
            }
        }
        if($show_author){
            if(isset($options['author'])){
                if(is_array($options['author'])){
                    $author = $options['author'];
                }else{
                    $author = json_decode($options['author'], true);
                }
            }else{
                $author = array("name"=>"Unknown");
            }
            $user_id = 0;
            if(isset($author['id']) && intval($author['id']) != 0){
                $user_id = intval($author['id']);
            }
            $image = get_avatar($user_id, 32);
            if($author['name'] !== "Unknown"){
                if($show_create_date){
                    $text = __("By", $this->plugin_name);
                }else{
                    $text = __("Created by", $this->plugin_name);
                }
                $show_cd_and_author .= "<span>   ".$text." </span>".$image."<strong>".$author['name']."</strong>";
            }else{
                $show_cd_and_author .= "";
            }
        }
        if($show_category){
            $category_id = isset($quiz['quiz_category_id']) ? intval($quiz['quiz_category_id']) : null;
            if($category_id !== null){
                $quiz_category = $this->get_quiz_category_by_id($category_id);
                $show_cd_and_author .= "<p style='margin:0!important;'><strong>".$quiz_category['title']."</strong></p>";
            }else{
                $show_cd_and_author .= "";
            }
        }
        $show_cd_and_author .= "</div>";
        
        if($show_create_date == false && $show_author == false && $show_category == false){
            $show_cd_and_author = "";
        }
        
        
        
    /*******************************************************************************************************/
        
        /* 
         * Quiz passing options
         *
         * Generate HTML code
         */
        
        $live_progress_bar = "";
        $timer_row = "";
        $answer_view_class = "";
        $correction_class = "";
        $ie_container_css = "";
        $rtl_style = "";
        $filling_type_wrap = '';
        $filling_type = '';
        $quiz_message_before_timer = '';

        // Progress live bar style
        $options['enable_live_progress_bar'] = isset($options['enable_live_progress_bar']) ? $options['enable_live_progress_bar'] : 'off';
        $enable_live_progress_bar = (isset($options['enable_live_progress_bar']) && $options['enable_live_progress_bar'] == 'on') ? true : false;
            
        
        /*
         * Generating Quiz timer
         *
         * Checking timer enabled or diabled
         */
        
        $timer_enabled = false;
        if (isset($options['enable_timer']) && $options['enable_timer'] == 'on') {
            $timer_enabled = true;
            $timer_text = (isset($options['timer_text'])) ? $options['timer_text'] : '';
            $timer_text = $this->replace_message_variables($timer_text, $message_variables_data);
            $timer_text = $this->ays_autoembed( $timer_text );
            $after_timer_text = (isset($options['after_timer_text'])) ? $options['after_timer_text'] : '';
            $after_timer_text = $this->replace_message_variables($after_timer_text, $message_variables_data);
            $after_timer_text = $this->ays_autoembed( $after_timer_text );

            // Message before timer
            $quiz_message_before_timer = (isset($options['quiz_message_before_timer']) && $options['quiz_message_before_timer'] != '') ? esc_attr( sanitize_text_field( $options['quiz_message_before_timer'] ) ) : '';

            $quiz_message_before_timer_class = '';
            if ( $quiz_message_before_timer != '' ) {
                $quiz_message_before_timer_class = 'ays-quiz-message-before-timer';
            }

            $hide_timer_cont = "";
            $empty_after_timer_text_class = "";
            if($timer_text == ""){
                $hide_timer_cont = " style='display:none;' ";
            }
            if($after_timer_text == ""){
                $empty_after_timer_text_class = " empty_after_timer_text ";
            }
            $timer_row = "<section {$hide_timer_cont} class='ays_quiz_timer_container'>
                <div class='ays-quiz-timer ". $quiz_message_before_timer_class ."' data-timer='" . $options['timer'] . "'>{$timer_text}</div>
                <div class='ays-quiz-after-timer ".$empty_after_timer_text_class."'>{$after_timer_text}</div>
                <hr style='height:1px;'>
            </section>";
        }
        
        /*
         * Quiz live progress bar
         *
         * Checking enabled or diabled
         *
         * Checking percent view or not
         */
        
        if($enable_live_progress_bar){
            $live_preview_view = isset($options['progress_live_bar_style']) && $options['progress_live_bar_style'] != '' ? $options['progress_live_bar_style'] : 'default';
            
            if(isset($options['enable_percent_view']) && $options['enable_percent_view'] == 'on'){
                $live_progress_bar_percent = "<span class='ays-live-bar-percent'>0</span>%";
            }else{
                $live_progress_bar_percent = "<span class='ays-live-bar-percent ays-live-bar-count'></span>/$questions_count";
            }
            switch ($live_preview_view) {
                case 'second':
                    $filling_type_wrap = 'ays-live-second-wrap';
                    $filling_type = 'ays-live-second';
                    break;
                case 'third':
                    $filling_type_wrap = 'ays-live-third-wrap';
                    $filling_type = 'ays-live-third';
                    break;
                case 'fourth':
                    $filling_type_wrap = 'ays-live-fourth-wrap';
                    $filling_type = 'ays-live-fourth';
                    break;
                default:
                    $filling_type_wrap = '';
                    $filling_type = '';
                    break;
            }
            
            $live_progress_bar = "<div class='ays-live-bar-wrap ". $filling_type_wrap ."'><div class='ays-live-bar-fill ". $filling_type ."' style='width: 0%;'><span>". $live_progress_bar_percent ."</span></div></div>";          
        }
        
        
        
        /*
         * Quiz questions answers view
         *
         * Generate HTML class for answers view
         */
        
        if(isset($options['answers_view']) && $options['answers_view'] != ''){
            $answer_view_class = $options['answers_view'];
        }
        
        
        /*
         * Get site url for social sharing buttons
         *
         * Generate HTML class for answers view
         */
        
        $actual_link = "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'){
            $actual_link = "https" . $actual_link;
        }else{
            $actual_link = "http" . $actual_link;
        }        
        
        /*
         * Show correct answers
         *
         * Generate HTML class for answers view
         */
        
        if($enable_correction){
            $correction_class = "enable_correction";
        }

        /*
         * Answeres numbering 
         */
        $show_answers_numbering = (isset($options['show_answers_numbering']) && sanitize_text_field( $options['show_answers_numbering'] ) !== '') ?  sanitize_text_field( $options['show_answers_numbering'] ) : 'none';

        // Questions numbering
        $show_questions_numbering = (isset($options['show_questions_numbering']) && $options['show_questions_numbering'] !== '') ?  sanitize_text_field( $options['show_questions_numbering'] ) : 'none';
              
        
        /*
         * Show correct answers
         *
         * Generate HTML class for answers view
         */
        if(isset($options['enable_questions_counter']) && $options['enable_questions_counter'] == 'on'){
            $questions_counter = true;
        }else{
            $questions_counter = false;
        }
        if(isset($options['enable_questions_score']) && $options['enable_questions_score'] == 'on'){
            $questions_score = true;
        }else{
            $questions_score = false;
        }
           
        
        /*
         * Get Browser data for Internet Explorer
         */
        
        $useragent = htmlentities($_SERVER['HTTP_USER_AGENT'], ENT_QUOTES, 'UTF-8');
        if(preg_match('~MSIE|Internet Explorer~i', $useragent) || 
           (strpos($useragent, 'Trident/7.0; rv:11.0') !== false)){
            $ie_container_css = 'display:flex;flex-wrap:wrap;';
        }


        /*
         * Question hint value 
         */
        $questions_hint_type = (isset($options['questions_hint_icon_or_text']) && $options['questions_hint_icon_or_text'] != '' ) ? sanitize_text_field( $options['questions_hint_icon_or_text'] ) : 'default';
        $questions_hint_value = (isset($options['questions_hint_value']) && $options['questions_hint_value'] != '') ? stripslashes(esc_attr($options['questions_hint_value'])) : '';
        $questions_hint_button_value = (isset($options['questions_hint_button_value']) && $options['questions_hint_button_value'] != '') ? stripslashes(esc_attr($options['questions_hint_button_value'])) : '';

        $questions_hint_arr = array(
            'questionsHintType' => $questions_hint_type,
            'questionsHintValue' => $questions_hint_value,
            'questionsHintButtonValue' => $questions_hint_button_value,
        );
        
        /*
         * Quiz buttons
         * 
         * Next button
         * Previous button
         * Arrows instead buttons
         */
        if(isset($options['enable_previous_button']) && $options['enable_previous_button'] == "on"){
            $prev_button = true;
        }else{
            $prev_button = false;
        }
        
        if(isset($options['enable_next_button']) && $options['enable_next_button'] == "on"){
            $next_button = true;
        }else{
            $next_button = false;
        }
        
        if(isset($options['enable_arrows']) && $options['enable_arrows'] == "on"){
            $enable_arrows = true;
        }else{
            $enable_arrows = false;
        }
        
        // Quiz arrows option arrows
        if(isset($options['quiz_arrow_type']) && $options['quiz_arrow_type'] != ""){
            $quiz_arrow_type = $options['quiz_arrow_type'];
        }else{
            $quiz_arrow_type = 'default';
        }

        if(isset($options['enable_early_finish']) && $options['enable_early_finish'] == 'on'){
            $enable_early_finish = true;
        }else{
            $enable_early_finish = false;
        }
        
        if($enable_arrows){
            $arrows_visibility = "";
        }else{
            $arrows_visibility = 'ays_display_none';
        }
        
        if($prev_button && $enable_arrows){
            $prev_arrow_visibility = "";
        }else{
            $prev_arrow_visibility = 'ays_display_none';
        }
        
        if($prev_button && !$enable_arrows){
            $prev_button_visibility = "";
        }else{
            $prev_button_visibility = 'ays_display_none';
        }
        
        if($next_button && $enable_arrows){
            $next_arrow_visibility = "";
        }else{
            $next_arrow_visibility = 'ays_display_none';
        }
        
        if($next_button == true && $enable_arrows == false){
            $next_button_visibility = "";
        }else{
            $next_button_visibility = 'ays_display_none';
        }

        
        /*
         * Clear answer button
         */
        $enable_clear_answer = false;
        if(isset($options['enable_clear_answer']) && $options['enable_clear_answer'] == 'on'){
            $enable_clear_answer = true;
        }
        
        
        $buttons = array(
            "enableArrows" => $enable_arrows,
            "arrows" => $arrows_visibility,
            "nextArrow" => $next_arrow_visibility,
            "prevArrow" => $prev_arrow_visibility,
            "nextButton" => $next_button_visibility,
            "prevButton" => $prev_button_visibility,
            "earlyButton" => $enable_early_finish,
            "clearAnswerButton" => $enable_clear_answer,
            "quizArrowType" => $quiz_arrow_type,
        );
        
        /*
         * Quiz restart button
         */
        $enable_restart_button = false;
        if(isset($options['enable_restart_button']) && $options['enable_restart_button'] == 'on'){
            $enable_restart_button = true;
        }

        if($enable_restart_button){
            $restart_button = "<button type='button' class='action-button ays_restart_button'>
                    <i class='ays_fa ays_fa_undo'></i>
                    <span>". $this->buttons_texts['restartQuizButton'] ."</span>
                </button>";
        }else{
            $restart_button = "";
        }

        
        /*
         * EXIT button in finish page
         */

        $enable_exit_button = false;
        $exit_redirect_url = null;
        if(isset($options['enable_exit_button']) && $options['enable_exit_button'] == 'on'){
            $enable_exit_button = true;
        }
        if(isset($options['exit_redirect_url']) && $options['exit_redirect_url'] != ''){
            $exit_redirect_url = $options['exit_redirect_url'];
        }


        if($enable_exit_button && $exit_redirect_url !== null){

            $exit_button = "<a style='width:auto;' href='".$exit_redirect_url."' class='action-button ays_restart_button' target='_top'>
                        <span>".$this->buttons_texts['exitButton']."</span>
                        <i class='ays_fa ays_fa_sign_out'></i>
                    </a>";
        }else{
            $exit_button = "";
        }
                        
        /*
         * Quiz questions per page count
         */
        
        if(isset($options['enable_rtl_direction']) && $options['enable_rtl_direction'] == "on"){
            $rtl_direction = true;
            $rtl_style = "
                #ays-quiz-container-" . $id . " p {
                    direction:rtl;
                    text-align:right;   
                }
                #ays-quiz-container-" . $id . " p.ays_score {
                    text-align: center;   
                }
                #ays-quiz-container-" . $id . " p.ays-question-counter {
                    right: unset;
                    left: 8px;
                }
                #ays-quiz-container-" . $id . " .ays_question_hint_container {
                    left:unset;
                    right:10px;
                }
                #ays-quiz-container-" . $id . " .ays_question_hint_text {
                    left:unset;
                    right:20px;
                }
                #ays-quiz-container-" . $id . " .select2-container--default .select2-results__option {
                    direction:rtl;
                    text-align:right;
                }
                #ays-quiz-container-" . $id . " .select2-container--default .select2-selection--single .select2-selection__placeholder,
                #ays-quiz-container-" . $id . " .select2-container--default .select2-selection--single .select2-selection__rendered {
                    direction:rtl;
                    text-align:right;
                    display: inline-block;
                    width: 100%;
                }

                #ays-quiz-container-" . $id . " .select2-container .select2-selection--single .select2-selection__rendered {
                    padding-right: 30px;
                }
                #ays-quiz-container-" . $id . " .ays-field.ays-select-field {
                    margin: 0;
                }

                #ays-quiz-container-" . $id . " label[for^=\"ays-answer-\"]{
                    direction:rtl;
                    text-align:right;
                    padding-left: 0px;
                    padding-right: 10px;
                    position: relative;
                    text-overflow: ellipsis;
                }                        
                #ays-quiz-container-" . $id . " label[for^=\"ays-answer-\"]:last-child {
                    padding-right: 0;
                }
                #ays-quiz-container-" . $id . " label[for^=\"ays-answer-\"]::before {
                    margin-left: 5px;
                    margin-right: 5px;
                }
                #ays-quiz-container-" . $id . " label[for^=\"ays-answer-\"]::after {
                    margin-left: 0px;
                    margin-right: 10px;
                }
                ";
        }else{
            $rtl_direction = false;
        }
        
        
        
        /*
         * Quiz background music 
         */
        
        $enable_bg_music = false;
        $quiz_bg_music = "";
        $ays_quiz_music_html = "";
        $ays_quiz_music_sound = "";
        
        if(isset($options['enable_bg_music']) && $options['enable_bg_music'] == "on"){
            $enable_bg_music = true;
        }
        
        if(isset($options['quiz_bg_music']) && $options['quiz_bg_music'] != ""){
            $quiz_bg_music = $options['quiz_bg_music'];
        }

        if($enable_bg_music && $quiz_bg_music != ""){
            $ays_quiz_music_html = "<audio id='ays_quiz_music_".$id."' loop class='ays_quiz_music' src='".$quiz_bg_music."'></audio>";
            $with_timer = "";
            if($timer_enabled){
                $with_timer = " ays_sound_with_timer ";
            }
            $ays_quiz_music_sound = "<span class='ays_music_sound ".$with_timer." ays_sound_active ays_display_none'><i class='ays_fa ays_fa_volume_up'></i></span>";
        }
        
        /*
         * Quiz Right / Wrong answers sounds 
         */
        
        $enable_rw_asnwers_sounds = false;
        $rw_answers_sounds_status = false;
        $right_answer_sound_status = false;
        $wrong_answer_sound_status = false;
        $right_answer_sound = "";
        $wrong_answer_sound = "";
        $rw_asnwers_sounds_html = "";


        if(isset($settings_options['right_answer_sound']) && $settings_options['right_answer_sound'] != ''){
            $right_answer_sound_status = true;
            $right_answer_sound = $settings_options['right_answer_sound'];
        }

        if(isset($settings_options['wrong_answer_sound']) && $settings_options['wrong_answer_sound'] != ''){
            $wrong_answer_sound_status = true;
            $wrong_answer_sound = $settings_options['wrong_answer_sound'];
        }

        if($right_answer_sound_status && $wrong_answer_sound_status){
            $rw_answers_sounds_status = true;
        }
        
        if(isset($options['enable_rw_asnwers_sounds']) && $options['enable_rw_asnwers_sounds'] == "on"){
            if($rw_answers_sounds_status){
                $enable_rw_asnwers_sounds = true;
            }
        }

        if($enable_rw_asnwers_sounds){
            $rw_asnwers_sounds_html = "<audio id='ays_quiz_right_ans_sound_".$id."' class='ays_quiz_right_ans_sound' src='".$right_answer_sound."'></audio>";
            $rw_asnwers_sounds_html .= "<audio id='ays_quiz_wrong_ans_sound_".$id."' class='ays_quiz_wrong_ans_sound' src='".$wrong_answer_sound."'></audio>";
        }


        /*
         * Text quetion type
         * Textarea height (public)
         */

        // Textarea height (public)
        $quiz_textarea_height = (isset($settings_options['quiz_textarea_height']) && $settings_options['quiz_textarea_height'] != '' && $settings_options['quiz_textarea_height'] != 0) ? absint( sanitize_text_field($settings_options['quiz_textarea_height']) ) : 100;

        // Show question explanation
        $show_questions_explanation = (isset($options['show_questions_explanation']) && $options['show_questions_explanation'] != '') ? sanitize_text_field( $options['show_questions_explanation'] ) : 'on_results_page';

        // Show messages for right/wrong answers
        $answers_rw_texts = (isset($options['answers_rw_texts']) && $options['answers_rw_texts'] != '') ? sanitize_text_field( $options['answers_rw_texts'] ) : 'on_passing';

        
    /*******************************************************************************************************/
        
        /* 
         * Quiz finish page
         *
         * Generating some HTML code for finish page
         */
        
        $progress_bar = false;
        $progress_bar_style = "first";
        $progress_bar_html = "";
        $show_average = "";
        $show_score_html = "";
        $enable_questions_result = "";
        $rate_form_title = "";
        $quiz_rate_html = "";
        $ays_social_buttons = "";
        $pass_score_html = "";
        
        /*
         * Quiz progress bar for finish page
         *
         * Checking enabled or diabled
         */
        
        if(isset($options['enable_progress_bar']) && $options['enable_progress_bar'] == 'on'){
            $progress_bar = true;
        }

        if(isset($options['progress_bar_style']) && $options['progress_bar_style'] != ""){
            $progress_bar_style = $options['progress_bar_style'];
        }

        if($progress_bar){
            $progress_bar_html = "<div class='ays-progress " . $progress_bar_style . "'>
                <span class='ays-progress-value " . $progress_bar_style . "'>0%</span>
                <div class='ays-progress-bg " . $progress_bar_style . "'>
                    <div class='ays-progress-bar " . $progress_bar_style . "' style='width:0%;'></div>
                </div>
            </div>";
        }


        /*
         * Average statistical of quiz
         *
         * Checking enabled or diabled
         */
        if (isset($options['enable_average_statistical']) && $options['enable_average_statistical'] == "on") {
            $sql = "SELECT AVG(`score`) FROM {$wpdb->prefix}aysquiz_reports WHERE quiz_id= $id";

            $quiz_avg_result = $wpdb->get_var($sql);
            if ( is_null( $quiz_avg_result ) || empty( $quiz_avg_result ) ) {
                $result = 0;
            } else { 
                $result = round($wpdb->get_var($sql));
            }
            $show_average = "<p class='ays_average'>" . __('The average score is ', $this->plugin_name) . " " . $result . "%</p>";
        }
        
        
        /*
         * Passed quiz score
         *
         * Checking enabled or diabled
         */
                
        if(array_key_exists('hide_score',$options) && $options['hide_score'] != 'on'){
            $show_score_html = "<p class='ays_score ays_score_display_none animated'>" . __( 'Your score is ', $this->plugin_name ) . "</p>";
        }
        
        
        /*
         * Show quiz results after passing quiz
         *
         * Checking enabled or diabled
         */
              
        if(isset($options['enable_questions_result']) && $options['enable_questions_result'] == 'on'){
            $enable_questions_result = 'enable_questions_result';
        }

        // Add all reviews link
        $options['quiz_make_all_review_link'] = isset($options['quiz_make_all_review_link']) ? sanitize_text_field($options['quiz_make_all_review_link']) : 'off';
        $quiz_make_all_review_link = (isset($options['quiz_make_all_review_link']) && $options['quiz_make_all_review_link'] == 'on') ? true : false;
        
        $all_review_link_html = '';
        if ( $quiz_make_all_review_link ) {
            if ( $this->ays_get_count_of_reviews(0, 5, $id) > 0 ) {
                $all_review_link_html = "<div class='ays-quiz-rate-link-box'><span class='ays-quiz-rate-link'>". __( "See review", $this->plugin_name ) ."</span></div>";
            }
        }

        // Enable quiz assessment | Placeholder text
        $quiz_review_placeholder_text = (isset($options['quiz_review_placeholder_text']) && $options['quiz_review_placeholder_text'] != '') ? stripslashes( esc_attr( $options['quiz_review_placeholder_text'] ) ) : "";

        // Make review required
        $options['quiz_make_review_required'] = isset($options['quiz_make_review_required']) ? sanitize_text_field($options['quiz_make_review_required']) : 'off';
        $quiz_make_review_required = (isset($options['quiz_make_review_required']) && $options['quiz_make_review_required'] == 'on') ? "true" : "false";

        /*
         * Passed or Failed quiz score html
         */
        $pass_score_html = "<div class='ays_score_message'></div>";
        
        
        /*
         * Quiz rate
         *
         * Generating HTML code
         */
        
        if(isset($options['rate_form_title'])){
            $rate_form_title = $this->ays_autoembed($options['rate_form_title']);
        }
        
        if(isset($options['enable_quiz_rate']) && $options['enable_quiz_rate'] == 'on'){
            $quiz_rate_html = "<div class='ays_quiz_rete'>
                <div>$rate_form_title</div>
                <div class='for_quiz_rate ui huge star rating' data-rating='0' data-max-rating='5'></div>
                <div class='ays-quiz-lds-spinner-box'><div class='lds-spinner-none'><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div>
                ". $all_review_link_html ."
                <div class='for_quiz_rate_reason'>
                    <textarea id='quiz_rate_reason_".$id."' class='quiz_rate_reason' data-required='". $quiz_make_review_required ."' placeholder='". $quiz_review_placeholder_text ."'></textarea>
                    <div class='ays_feedback_button_div'>
                        <button type='button' class='action-button'>". $this->buttons_texts['sendFeedbackButton'] ."</button>
                    </div>
                </div>
                <div><div class='lds-spinner2-none'><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div></div>
                <div class='quiz_rate_reasons_body'></div>
            </div>";
        }
        
        
        
        /*
         * Quiz social sharing buttons
         *
         * Generating HTML code
         */

        // Heading for social buttons
        $social_buttons_heading = (isset($options['social_buttons_heading']) && $options['social_buttons_heading'] != '') ? stripslashes( wpautop( $options['social_buttons_heading'] ) ) : "";

        // Enable Linkedin button
        $options['quiz_enable_linkedin_share_button'] = isset($options['quiz_enable_linkedin_share_button']) ? sanitize_text_field($options['quiz_enable_linkedin_share_button']) : 'on';
        $quiz_enable_linkedin_share_button = (isset($options['quiz_enable_linkedin_share_button']) && $options['quiz_enable_linkedin_share_button'] == 'on') ? true : false;

        // Enable Facebook button
        $options['quiz_enable_facebook_share_button'] = isset($options['quiz_enable_facebook_share_button']) ? sanitize_text_field($options['quiz_enable_facebook_share_button']) : 'on';
        $quiz_enable_facebook_share_button = (isset($options['quiz_enable_facebook_share_button']) && $options['quiz_enable_facebook_share_button'] == 'on') ? true : false;

        // Enable Twitter button
        $options['quiz_enable_twitter_share_button'] = isset($options['quiz_enable_twitter_share_button']) ? sanitize_text_field($options['quiz_enable_twitter_share_button']) : 'on';
        $quiz_enable_twitter_share_button = (isset($options['quiz_enable_twitter_share_button']) && $options['quiz_enable_twitter_share_button'] == 'on') ? true : false;

        // Enable VKontakte button
        $options['quiz_enable_vkontakte_share_button'] = isset($options['quiz_enable_vkontakte_share_button']) ? sanitize_text_field($options['quiz_enable_vkontakte_share_button']) : 'on';
        $quiz_enable_vkontakte_share_button = (isset($options['quiz_enable_vkontakte_share_button']) && $options['quiz_enable_vkontakte_share_button'] == 'on') ? true : false;

        if ( ! $quiz_enable_linkedin_share_button && ! $quiz_enable_facebook_share_button && ! $quiz_enable_twitter_share_button && ! $quiz_enable_vkontakte_share_button) {
            $quiz_enable_linkedin_share_button = true;
            $quiz_enable_facebook_share_button = true;
            $quiz_enable_twitter_share_button = true;
            $quiz_enable_vkontakte_share_button = true;
        }
        
        
        if(isset($options['enable_social_buttons']) && $options['enable_social_buttons'] == 'on'){
            $ays_social_buttons .= "<div class='ays-quiz-social-shares'>";
                $ays_social_buttons .= "<div class='ays-quiz-social-shares-heading'>";
                    $ays_social_buttons .= $social_buttons_heading;
                $ays_social_buttons .= "</div>";
            
            if ( $quiz_enable_linkedin_share_button ) {
                $ays_social_buttons .= "
                    <!-- Branded LinkedIn button -->
                    <a class='ays-share-btn ays-to-share ays-share-btn-branded ays-share-btn-linkedin'
                       href='https://www.linkedin.com/shareArticle?mini=true&url=" . $actual_link . "'
                       title='Share on LinkedIn'>
                        <span class='ays-quiz-share-btn-icon'></span>
                        <span class='ays-share-btn-text'>LinkedIn</span>
                    </a>";
            }

            if ( $quiz_enable_facebook_share_button ) {
                $ays_social_buttons .= "
                    <!-- Branded Facebook button -->
                    <a class='ays-share-btn ays-to-share ays-share-btn-branded ays-share-btn-facebook'
                       href='https://www.facebook.com/sharer/sharer.php?u=" . $actual_link . "'
                       title='Share on Facebook'>
                        <span class='ays-quiz-share-btn-icon'></span>
                        <span class='ays-share-btn-text'>Facebook</span>
                    </a>";
            }

            if ( $quiz_enable_twitter_share_button ) {
                $ays_social_buttons .= "
                <!-- Branded Twitter button -->
                <a class='ays-share-btn ays-to-share ays-share-btn-branded ays-share-btn-twitter'
                   href='https://twitter.com/share?url=" . $actual_link . "'
                   title='Share on Twitter'>
                    <span class='ays-quiz-share-btn-icon'></span>
                    <span class='ays-share-btn-text'>Twitter</span>
                </a>";
            }

            if ( $quiz_enable_vkontakte_share_button ) {
                $ays_social_buttons .= "
                <!-- Branded VK button -->
                <a class='ays-share-btn ays-to-share ays-share-btn-branded ays-share-btn-vkontakte'
                   href='https://vk.com/share.php?url=" . $actual_link . "'
                   title='Share on VKontakte'>
                    <span class='ays-quiz-share-btn-icon'></span>
                    <span class='ays-share-btn-text'>VKontakte</span>
                </a>";
            }

            $ays_social_buttons .= "</div>";
        }
        
        
        
        /*
         * Quiz social media links
         *
         * Generating HTML code
         */

        // Heading for social media links
        $social_links_heading = (isset($options['social_links_heading']) && $options['social_links_heading'] != '') ? stripslashes( wpautop( $options['social_links_heading'] ) ) : "";

        // Social Media links

        $enable_social_links = (isset($options['enable_social_links']) && $options['enable_social_links'] == "on") ? true : false;
        $social_links = (isset($options['social_links'])) ? $options['social_links'] : array(
            'linkedin_link' => '',
            'facebook_link' => '',
            'twitter_link' => '',
            'vkontakte_link' => '',
            'instagram_link' => '',
            'youtube_link' => '',
        );
        $ays_social_links_array = array();

        $linkedin_link = isset($social_links['linkedin_link']) && $social_links['linkedin_link'] != '' ? $social_links['linkedin_link'] : '';
        $facebook_link = isset($social_links['facebook_link']) && $social_links['facebook_link'] != '' ? $social_links['facebook_link'] : '';
        $twitter_link = isset($social_links['twitter_link']) && $social_links['twitter_link'] != '' ? $social_links['twitter_link'] : '';
        $vkontakte_link = isset($social_links['vkontakte_link']) && $social_links['vkontakte_link'] != '' ? $social_links['vkontakte_link'] : '';
        $instagram_link = isset($social_links['instagram_link']) && $social_links['instagram_link'] != '' ? $social_links['instagram_link'] : '';
        $youtube_link = isset($social_links['youtube_link']) && $social_links['youtube_link'] != '' ? $social_links['youtube_link'] : '';
        if($linkedin_link != ''){
            $ays_social_links_array['Linkedin'] = $linkedin_link;
        }
        if($facebook_link != ''){
            $ays_social_links_array['Facebook'] = $facebook_link;
        }
        if($twitter_link != ''){
            $ays_social_links_array['Twitter'] = $twitter_link;
        }
        if($vkontakte_link != ''){
            $ays_social_links_array['VKontakte'] = $vkontakte_link;
        }
        if($instagram_link != ''){
            $ays_social_links_array['Instagram'] = $instagram_link;
        }
        if($youtube_link != ''){
            $ays_social_links_array['Youtube'] = $youtube_link;
        }
        $ays_social_links = '';
        
        if($enable_social_links){
            $ays_social_links .= "<div class='ays-quiz-social-shares'>";
            
            if( $social_links_heading != "" ) {
                $ays_social_links .= "<div class='ays-quiz-social-links-heading'>";
                    $ays_social_links .= $social_links_heading;
                $ays_social_links .= "</div>";
            }

            foreach($ays_social_links_array as $media => $link){
                $ays_social_links .= "<!-- Branded " . $media . " button -->
                    <a class='ays-share-btn ays-share-btn-branded ays-share-btn-rounded ays-share-btn-" . strtolower($media) . "'
                        href='" . $link . "'
                        target='_blank'
                        title='" . $media . " link'>
                        <span class='ays-quiz-share-btn-icon'></span>
                    </a>";
            }
                    
                    // "<!-- Branded Facebook button -->
                    // <a class='ays-share-btn ays-share-btn-branded ays-share-btn-facebook'
                    //     href='" . . "'
                    //     title='Share on Facebook'>
                    //     <span class='ays-quiz-share-btn-icon'></span>
                    // </a>
                    // <!-- Branded Twitter button -->
                    // <a class='ays-share-btn ays-share-btn-branded ays-share-btn-twitter'
                    //     href='" . . "'
                    //     title='Share on Twitter'>
                    //     <span class='ays-quiz-share-btn-icon'></span>
                    // </a>";
            $ays_social_links .= "</div>";
        }
        
        
        /*
         * Quiz loader
         *
         * Generating HTML code
         */
                
        $quiz_loader = 'default';
        
        if(isset($options['quiz_loader']) && $options['quiz_loader'] != ''){
            $quiz_loader = $options['quiz_loader'];
        }
        // Custom Text
        $quiz_loader_text_value = (isset($options['quiz_loader_text_value']) && $options['quiz_loader_text_value'] != '') ? stripslashes($options['quiz_loader_text_value']) : '';

        // Custom Gif
        $quiz_loader_custom_gif = (isset($options['quiz_loader_custom_gif']) && $options['quiz_loader_custom_gif'] != '') ? stripslashes($options['quiz_loader_custom_gif']) : '';

        //  Quiz loader custom gif width
        $quiz_loader_custom_gif_width = (isset($options['quiz_loader_custom_gif_width']) && $options['quiz_loader_custom_gif_width'] != '') ? absint( intval( $options['quiz_loader_custom_gif_width'] ) ) : 100;

        $quiz_loader_custom_gif_width_css = '';
        if ( $quiz_loader_custom_gif_width != '' ) {
            $quiz_loader_custom_gif_width_css = 'width: '. $quiz_loader_custom_gif_width .'px; height: auto; max-width: 100%;';
        }
        
        switch($quiz_loader){
            case 'default':
                $quiz_loader_html = "<div data-class='lds-ellipsis' data-role='loader' class='ays-loader'><div></div><div></div><div></div><div></div></div>";
                break;
            case 'circle':
                $quiz_loader_html = "<div data-class='lds-circle' data-role='loader' class='ays-loader'></div>";
                break;
            case 'dual_ring':
                $quiz_loader_html = "<div data-class='lds-dual-ring' data-role='loader' class='ays-loader'></div>";
                break;
            case 'facebook':
                $quiz_loader_html = "<div data-class='lds-facebook' data-role='loader' class='ays-loader'><div></div><div></div><div></div></div>";
                break;
            case 'hourglass':
                $quiz_loader_html = "<div data-class='lds-hourglass' data-role='loader' class='ays-loader'></div>";
                break;
            case 'ripple':
                $quiz_loader_html = "<div data-class='lds-ripple' data-role='loader' class='ays-loader'><div></div><div></div></div>";
                break;
            case 'text':
                if ($quiz_loader_text_value != '') {
                    $quiz_loader_html = "
                    <div class='ays-loader' data-class='text' data-role='loader'>
                        <p class='ays-loader-content'>". $quiz_loader_text_value ."</p>
                    </div>";
                }else{
                    $quiz_loader_html = "<div data-class='lds-ellipsis' data-role='loader' class='ays-loader'><div></div><div></div><div></div><div></div></div>";
                }
                break;
            case 'custom_gif':
                if ($quiz_loader_custom_gif != '') {
                    $quiz_loader_html = "
                    <div class='ays-loader' data-class='text' data-role='loader' style='text-align: center;'>
                        <img src='". $quiz_loader_custom_gif ."' class='ays-loader-content ays-loader-custom-gif-content' style='". $quiz_loader_custom_gif_width_css ."'>
                    </div>";
                }else{
                    $quiz_loader_html = "<div data-class='lds-ellipsis' data-role='loader' class='ays-loader'><div></div><div></div><div></div><div></div></div>";
                }
                break;    
            default:
                $quiz_loader_html = "<div data-class='lds-ellipsis' data-role='loader' class='ays-loader'><div></div><div></div><div></div><div></div></div>";
                break;
        }
        
        
    /*******************************************************************************************************/
        
        /*
         * Quiz limitations
         *
         * Blocking content
         *
         * Generating HTML code
         */
        
        $limit_users_html = "";
        $limit_users = null;
        
        /*
         * Quiz timer in tab title
         */
        
        if(isset($options['quiz_timer_in_title']) && $options['quiz_timer_in_title'] == "on"){
            $show_timer_in_title = "true";
        }else{
            $show_timer_in_title = "false";
        }        
        
        /*
         * Quiz one time passing
         *
         * Generating HTML code
         */        

        // Limit users by option
        $limit_users_by = 'ip';
        $limit_users_attr = array();
        $check_cookie = null;

        if(isset($options['limit_users_by']) && $options['limit_users_by'] != ''){
            $limit_users_by = $options['limit_users_by'];
        }

        $limit_users_attr = array(
            'id' => $id,
            'name' => 'ays_quiz_cookie_',
            'title' => $title,
        );
        $check_cookie = $this->ays_quiz_check_cookie( $limit_users_attr );

        if (isset($options['limit_users']) && $options['limit_users'] == "on") {

            switch ( $limit_users_by ) {
                case 'ip':
                    $result = $this->get_user_by_ip($id);
                    if ( $check_cookie ) {
                        $remove_cookie = $this->ays_quiz_remove_cookie( $limit_users_attr );
                    }
                    break;
                case 'user_id':
                    if(is_user_logged_in()){
                        $user_id = get_current_user_id();
                        $result = $this->get_limit_user_by_id($id, $user_id);
                    }else{
                        $result = 0;
                    }

                    if ( $check_cookie ) {
                        $remove_cookie = $this->ays_quiz_remove_cookie( $limit_users_attr );
                    }
                    break;
                case 'cookie':
                    if ( ! $check_cookie ) {
                        $result = 0;
                    }else{
                        $result = 1;
                    }
                    break;
                case 'ip_cookie':
                    $check_user_by_ip = $this->get_user_by_ip($id);
                    if($check_cookie || $check_user_by_ip > 0){
                        $result = 1;
                    }
                    elseif(! $check_cookie || $check_user_by_ip <= 0){
                        $result = 0;
                    }
                    break;
                default:
                    $result = 0;
                    if ( $check_cookie ) {
                        $remove_cookie = $this->ays_quiz_remove_cookie( $limit_users_attr );
                    }
                    break;
            }


            if ($result != 0) {
                $limit_users = true;
                $timer_row = "";
                if(isset($options['redirection_delay']) && $options['redirection_delay'] != ''){
                    if(isset($options['redirect_url']) && $options['redirect_url'] != ''){
                        $timer_row = "<qm_rurl class='ays_redirect_url' style='display:none'>" . 
                                $options['redirect_url'] . 
                            "</qm_rurl>                                
                            <div class='ays-quiz-timer' data-show-in-title='".$show_timer_in_title."' data-timer='" . $options['redirection_delay'] . "'>". 
                                __( "Redirecting after", $this->plugin_name ). " " . 
                                $this->secondsToWords($options['redirection_delay']) . 
                                "<EXTERNAL_FRAGMENT></EXTERNAL_FRAGMENT>                                
                            </div>";
                    }
                }

                $limitation_message = (isset($options['limitation_message']) && $options['limitation_message'] != '') ? $this->ays_autoembed($options['limitation_message']) : __( 'You have already passed this quiz.', $this->plugin_name );
                $limit_users_html = $timer_row . "<div style='color:" . $text_color . "' class='ays_block_content'>" .  $limitation_message . "</div><style>form{min-height:0 !important;}</style>";
            }
        }else{
            $limit_users = false;
            if ( $check_cookie ) {
                $remove_cookie = $this->ays_quiz_remove_cookie( $limit_users_attr );
            }
        }
        
        
        
        /*
         * Quiz only for logged in users
         *
         * Generating HTML code
         */  
        
        // Show login form for not logged in users
        $options['show_login_form'] = isset($options['show_login_form']) ? $options['show_login_form'] : 'off';
        $show_login_form = (isset($options['show_login_form']) && $options['show_login_form'] == "on") ? true : false;
        $quiz_login_form = "";
        if($show_login_form){
            $ays_login_button_text = $this->buttons_texts['loginButton'];
            $args = array(
                'echo' => false,
                'id_username' => 'ays_user_login',
                'id_password' => 'ays_user_pass',
                'id_remember' => 'ays_rememberme',
                'id_submit' => 'ays-submit',
                'label_log_in' => $ays_login_button_text,
            );
            $quiz_login_form = "<div class='ays_quiz_login_form'>" . wp_login_form( $args ) . "</div>";
        }

        global $wp_roles;
        
        if(isset($options['enable_logged_users']) && $options['enable_logged_users'] == 'on' && !is_user_logged_in()){
            $enable_logged_users = 'only_logged_users';
            if(isset($options['enable_logged_users_message']) && $options['enable_logged_users_message'] != ""){
                $logged_users_message = $this->ays_autoembed($options['enable_logged_users_message']);
            }else{
                $logged_users_message =  __('You must log in to pass this quiz.', $this->plugin_name);
            }
            if($logged_users_message !== null){
                $user_massage = '<div class="logged_in_message">' . $logged_users_message . '</div>';
            }else{
                $user_massage = null;
            }
        }else{
            $user_massage = null;
            $enable_logged_users = '';
            $user_role = (isset($options['user_role']) && $options['user_role'] != '') ? $options['user_role'] : ''; 
            if (isset($options['enable_restriction_pass']) && $options['enable_restriction_pass'] == 'on' && !empty( $user_role )) {
                $user = wp_get_current_user();
                $user_roles   = $wp_roles->role_names;
                $message = (isset($options['restriction_pass_message']) && $options['restriction_pass_message'] != '') ? $options['restriction_pass_message'] : __('Permission Denied', $this->plugin_name);
                $user_massage = '<div class="logged_in_message">' . $this->ays_autoembed($message) . '</div>';
                
                if (is_array($user_role)) {
                    foreach($user_role as $key => $role){
                        if(in_array($role, $user_roles)){
                            $user_role[$key] = array_search($role, $user_roles);
                        }                        
                    }
                }else{
                    if(in_array($user_role, $user_roles)){
                        $user_role = array_search($user_role, $user_roles);
                    }
                }

                if(is_array($user_role)){
                    foreach($user_role as $role){                        
                        if (in_array(strtolower($role), (array)$user->roles)) {
                            $user_massage = null;
                            break;
                        }
                    }                    
                }else{
                    if (in_array(strtolower($user_role), (array)$user->roles)) {
                        $user_massage = null;
                    }
                }
            }
        }
        
        if($user_massage !== null){
            if( !is_user_logged_in() ){
                $user_massage .= $quiz_login_form;
            }
        }

        // Limitation tackers of quiz
        $enable_tackers_count = false;
        $tackers_count = 0;
        $tackers_message = "<div style='padding:50px;'><p>" . __( "This quiz is expired!", $this->plugin_name ) . "</p></div>";
        $options['enable_tackers_count'] = !isset($options['enable_tackers_count']) ? 'off' : $options['enable_tackers_count'];
        if(isset($options['enable_tackers_count']) && $options['enable_tackers_count'] == 'on'){
            $enable_tackers_count = true;
        }
        if(isset($options['tackers_count']) && $options['tackers_count'] != ''){
            $tackers_count = intval($options['tackers_count']);
        }

        // Quiz takers message
        $quiz_tackers_message = ( isset($options['quiz_tackers_message']) && $options['quiz_tackers_message'] != '' ) ? stripslashes( wpautop( $options['quiz_tackers_message'] ) ) : __( "This quiz is expired!", $this->plugin_name );

        if ( $quiz_tackers_message != __( "This quiz is expired!", $this->plugin_name ) ) {
            $tackers_message = "<div class='ays-quiz-limitation-count-of-takers'>". $quiz_tackers_message ."</div>";
        } else {
            $tackers_message = "<div class='ays-quiz-limitation-count-of-takers'><p>" . __( "This quiz is expired!", $this->plugin_name ) . "</p></div>";
        }
        
        
    /*******************************************************************************************************/
        
        /*
         * Schedule quiz
		 * Check is quiz expired
         */
        
        $is_expired = false;
        $active_date_check = false;
        $UTC_seconds = null;
        $startDate = '';
        $endDate = '';
        $startDate_atr = '';
        $endDate_atr = '';
        $current_time = strtotime(current_time( "Y:m:d H:i:s" ));
        $activeInterval = isset( $options['activeInterval'] ) && $options['activeInterval'] != '' ? $options['activeInterval'] : current_time( 'mysql' );
        $deactiveInterval = isset( $options['deactiveInterval'] ) && $options['deactiveInterval'] != '' ? $options['deactiveInterval'] : current_time( 'mysql' );
        $startDate = strtotime( $activeInterval );
        $endDate   = strtotime( $deactiveInterval );

        // Timezone | Schedule the quiz
        $ays_quiz_schedule_timezone = (isset($options['quiz_schedule_timezone']) && $options['quiz_schedule_timezone'] != '') ? sanitize_text_field( $options['quiz_schedule_timezone'] ) : get_option( 'timezone_string' );

        if ( class_exists( 'DateTimeZone' )) {

            // Remove old Etc mappings. Fallback to gmt_offset.
            if ( strpos( $ays_quiz_schedule_timezone, 'Etc/GMT' ) !== false ) {
                $ays_quiz_schedule_timezone = '';
            }

            $current_offset = get_option( 'gmt_offset' );
            if ( empty( $ays_quiz_schedule_timezone ) ) { // Create a UTC+- zone if no timezone string exists.

                if ( 0 == $current_offset ) {
                    $ays_quiz_schedule_timezone = 'UTC+0';
                } elseif ( $current_offset < 0 ) {
                    $ays_quiz_schedule_timezone = 'UTC' . $current_offset;
                } else {
                    $ays_quiz_schedule_timezone = 'UTC+' . $current_offset;
                }
            }

            $if_timezone_UTC = false;
            if ( strpos($ays_quiz_schedule_timezone, 'UTC+') !== false ) {
                $if_timezone_UTC = true;

                $UTC_val_arr = explode('+', $ays_quiz_schedule_timezone );

                $UTC_val     = ( isset( $UTC_val_arr[1] ) && $UTC_val_arr[1] != '' ) ? $UTC_val_arr[1] : 0;

                $UTC_seconds = (int) ($UTC_val * 3600);

            } elseif ( strpos($ays_quiz_schedule_timezone, 'UTC-') !== false ) {
                $if_timezone_UTC = true;

                $UTC_val_arr = explode('-', $ays_quiz_schedule_timezone );

                $UTC_val     = ( isset( $UTC_val_arr[1] ) && $UTC_val_arr[1] != '' ) ? $UTC_val_arr[1] : 0;

                $UTC_seconds =  (int) ( -1 * ( $UTC_val * 3600 ) );
            }

            if (in_array( $ays_quiz_schedule_timezone , DateTimeZone::listIdentifiers()) && ! $if_timezone_UTC ) {

                $Date_Time_Zone = new DateTime("now", new DateTimeZone( $ays_quiz_schedule_timezone ));
                $current_time   = strtotime( $Date_Time_Zone->format( "Y:m:d H:i:s" ) );
            } else {
                if ( ! is_null( $UTC_seconds ) && ! empty( $UTC_seconds ) ) {
                    $Date_Time_Zone = new DateTime("now", new DateTimeZone( 'UTC' ));
                    $current_time   = strtotime( $Date_Time_Zone->format( "Y:m:d H:i:s" ) ) + ( $UTC_seconds );
                } else {
                    $current_time = strtotime(current_time( "Y:m:d H:i:s" ));
                }
            }
        }

        $expired_quiz_message = "<p class='ays-fs-subtitle'>" . __('The quiz has expired.', $this->plugin_name) . "</p>";

        if (isset($options['active_date_check']) && $options['active_date_check'] == "on") {
            $active_date_check = true;

            if (isset($options['activeInterval']) && !empty($options['activeInterval'])) {
                $startDate_atr = $startDate - $current_time;
            }elseif (isset($options['deactiveInterval']) && !empty($options['deactiveInterval'])) {
                $endDate_atr = $endDate - $current_time;
            }

            // show timer
            $activeDateCheck =  isset($options['active_date_check']) && !empty($options['active_date_check']) ? true : false;
            $activeDeactiveDateCheck =  isset($options['deactiveInterval']) && !empty($options['deactiveInterval']) ? true : false;
            $show_timer_type = isset($options['show_timer_type']) && !empty($options['show_timer_type']) ? $options['show_timer_type'] : 'countdown';
            $activeActiveDateCheck =  isset($options['activeInterval']) && !empty($options['activeInterval']) ? true : false;

            $show_timer = '';
            if ($activeDateCheck && $activeActiveDateCheck && $active_date_check) {
                if (isset($options['show_schedule_timer']) && $options['show_schedule_timer'] == 'on') {
                    $show_timer .= "<div class='ays_quiz_show_timer'>";
                    if ($show_timer_type == 'countdown') {
                        $show_timer .= '<p class="show_timer_countdown" data-timer_countdown="'.$startDate_atr.'"></p>';
                    }else if ($show_timer_type == 'enddate') {
                        $show_timer .= '<p class="show_timer_countdown">' . __('This Quiz will start on', $this->plugin_name);
                        $show_timer .= ' ' . date_i18n('H:i:s F jS, Y', intval($startDate));
                        $show_timer .= '</p>';
                    }
                    $show_timer .= "</div>";
                }
            }

            if ($startDate > $current_time) {
                $is_expired = true;
                if(isset($options['active_date_pre_start_message'])){
                    $expired_quiz_message = "<div class='step active-step'>
                        <div class='ays-abs-fs'>
                            ".$show_timer."
                            " . $this->ays_autoembed( $options['active_date_pre_start_message'] ) . "
                        </div>
                    </div>";
                }else{
                    $expired_quiz_message = "<div class='step active-step'>
                        <div class='ays-abs-fs'>
                            ".$show_timer."
                            <p class='ays-fs-subtitle'>" . __('The quiz will be available soon.', $this->plugin_name) . "</p>
                        </div>
                    </div>";
                }
            }elseif ($endDate < $current_time) {
                $is_expired = true;
                if(isset($options['active_date_message']) && $options['active_date_message'] != ''){
                    $expired_quiz_message = "<div class='step active-step' data-message-exist='true'>
                        <div class='ays-abs-fs'>
                            " . $this->ays_autoembed( $options['active_date_message'] ) . "
                        </div>
                    </div>";
                }else{
                    $expired_quiz_message = "<div class='step active-step'>
                        <div class='ays-abs-fs'>
                            <p class='ays-fs-subtitle'>" . __('The quiz has expired.', $this->plugin_name) . "</p>
                        </div>
                    </div>";
                }
            }
        }
        
        
    /*******************************************************************************************************/

        /*
         * Quiz main content
         *
         * Generating HTML code
         */
        
        
        if($quiz_image != ""){
            $quiz_image_alt_text = $this->ays_quiz_get_image_id_by_url($quiz_image);

            $quiz_image = "<img src='{$quiz_image}' alt='". $quiz_image_alt_text ."' class='ays_quiz_image'>";
        }else{
            $quiz_image = "";
        }

        $ays_protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
         
        $quiz_current_page_link = esc_url( $ays_protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
        
        if($show_quiz_title){
            $title = "<p class='ays-fs-title'>" . $title . "</p>";
        }else{
            $title = "";
        }

        if($show_quiz_desc){
            $description = "<div class='ays-fs-subtitle'>" . $description . "</div>";
        }else{
            $description = "";
        }


        $main_content_first_part = "{$timer_row}
            {$rw_asnwers_sounds_html}
            {$ays_quiz_music_sound}
            <div class='step active-step'>
                <div class='ays-abs-fs ays-start-page'>
                    {$show_cd_and_author}
                    {$quiz_image}
                    {$title}
                    {$description}
                    <input type='hidden' name='ays_quiz_id' value='{$id}'/>
                    <input type='hidden' name='ays_quiz_curent_page_link' class='ays-quiz-curent-page-link' value='{$quiz_current_page_link}'/>
                    " . (isset($quiz_questions_ids) ? "<input type='hidden' name='ays_quiz_questions' value='{$quiz_questions_ids}'>" : "") . "
                    {$quiz_password_message_html}
                    {$password_message}
                    {$quiz_start_button}
                    </div>
                </div>";
        
        if($limit_users === false || $limit_users === null){
            $restart_button_html = $restart_button;
        }else{
            $restart_button_html = "";
        }
            
        $main_content_last_part = "<div class='step ays_thank_you_fs'>
            <div class='ays-abs-fs ays-end-page'>".
            $quiz_loader_html .
            "<div class='ays_quiz_results_page'>".
                $pass_score_html .
                "<div class='ays_message'></div>" .
                $show_score_html .
                $show_average .
                $ays_social_buttons .
                $ays_social_links .
                $progress_bar_html .
                "<p class='ays_restart_button_p'>".
                    $restart_button_html .
                    $exit_button .
                "</p>".
                $quiz_rate_html .
                "</div>
            </div>
        </div>";

        if (! $show_information_form) {
            if(is_user_logged_in()){
                $show_form = null;
            }
        }

        switch( $quiz_arrow_type ){
            case 'default':
                $quiz_arrow_type_class_right = "ays_fa_arrow_right";
                break;
            case 'long_arrow':
                $quiz_arrow_type_class_right = "ays_fa_long_arrow_right";
                break;
            case 'arrow_circle_o':
                $quiz_arrow_type_class_right = "ays_fa_arrow_circle_o_right";
                break;
            case 'arrow_circle':
                $quiz_arrow_type_class_right = "ays_fa_arrow_circle_right";
                break;
            default:
                $quiz_arrow_type_class_right = "ays_fa_arrow_right";
                break;
        }

        if($show_form != null){
            if ($options['information_form'] == "after") {
                $main_content_last_part = "<div class='step'>
                                <div class='ays-abs-fs ays-end-page information_form'>
                                <div class='ays-form-title'>{$form_title}</div>
                                    " . $form_inputs . "
                                    <div class='ays_buttons_div'>
                                        <i class='" . ($enable_arrows ? '' : 'ays_display_none') . " ays_fa ays_fa_flag_checkered ays_finish action-button ays_arrow ays_next_arrow'></i>
                                        <input type='submit' name='ays_finish_quiz' class='" . ($enable_arrows ? 'ays_display_none' : '') . " ays_next ays_finish action-button' value='" . $this->buttons_texts['seeResultButton'] . "'/>
                                    </div>
                                </div>
                              </div>" . $main_content_last_part;

            } elseif ($options['information_form'] == "before") {
                $main_content_first_part = $main_content_first_part . "<div class='step'>
                                    <div class='ays-abs-fs ays-start-page information_form'>
                                    <div class='ays-form-title'>{$form_title}</div>
                                        " . $form_inputs . "
                                        <div class='ays_buttons_div'>
                                            <i class='ays_fa " . $quiz_arrow_type_class_right . " ays_next action-button ays_arrow ays_next_arrow " . ($enable_arrows ? '' : 'ays_display_none') . "'></i>
                                            <input type='button' class='ays_next action-button " . ($enable_arrows ? 'ays_display_none' : '') . "' value='" . $this->buttons_texts['nextButton'] . "' />
                                        </div>
                                    </div>
                                  </div>" ;

            }
        }else{
            $options['information_form'] = "disable";
        }
        
        
    /*******************************************************************************************************/
        
        /*
         * Script for getting quiz options
         *
         * Script for question type dropdown
         *
         * Generating HTML code
         */
        
        
        if(isset($options['submit_redirect_delay'])){
            if($options['submit_redirect_delay'] == ''){
                $options['submit_redirect_delay'] = 0;
            }
            $options['submit_redirect_after'] = $this->secondsToWords( absint($options['submit_redirect_delay']) );
        }
        
        $options['rw_answers_sounds'] = $enable_rw_asnwers_sounds;
        
        $quiz_content_script = "<script>";
        unset($quiz['options']);
        $quiz_options = $options;
        foreach($quiz as $k => $q){
            $quiz_options[$k] = $q;
        }

        // Animation Top (px)
        $quiz_animation_top = (isset($settings_options['quiz_animation_top']) && $settings_options['quiz_animation_top'] != 0) ? absint(intval($settings_options['quiz_animation_top'])) : 100;
        $settings_options['quiz_enable_animation_top'] = isset($settings_options['quiz_enable_animation_top']) ? $settings_options['quiz_enable_animation_top'] : 'on';
        $quiz_enable_animation_top = (isset($settings_options['quiz_enable_animation_top']) && $settings_options['quiz_enable_animation_top'] == "on") ? 'on' : 'off';

        $quiz_options['quiz_animation_top'] = $quiz_animation_top;
        $quiz_options['quiz_enable_animation_top'] = $quiz_enable_animation_top;
        
        if ($limit_users) {
            if($limit_users_by == 'ip'){
                $result = $this->get_user_by_ip($id);
            }elseif($limit_users_by == 'user_id'){
                if(is_user_logged_in()){
                    $user_id = get_current_user_id();
                    $result = $this->get_limit_user_by_id($id, $user_id);
                }else{
                    $result = 0;
                }
            }else{
                $result = 0;
            }
            if ($result == 0) {
                $quiz_content_script .= "
                    if(typeof aysQuizOptions === 'undefined'){
                        var aysQuizOptions = [];
                    }
                    aysQuizOptions['".$id."']  = '" . base64_encode(json_encode($quiz_options)) . "';";
            }
        }else{
            $quiz_content_script .= "
                if(typeof aysQuizOptions === 'undefined'){
                    var aysQuizOptions = [];
                }
                aysQuizOptions['".$id."']  = '" . base64_encode(json_encode($quiz_options)) . "';";
        }
        $quiz_content_script .= "
        </script>";
        
    /*******************************************************************************************************/
        
        /*
         * Styles for quiz
         *
         * Generating HTML code
         */
        
        
        $quest_animation = 'shake';
        
        if(isset($options['quest_animation']) && $options['quest_animation'] != ''){
            $quest_animation = $options['quest_animation'];
        }
        
        $quiz_styles = "<style>
            div#ays-quiz-container-" . $id . " * {
                box-sizing: border-box;
            }

            /* Styles for Internet Explorer start */
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " {
                " . $ie_container_css . "
            }";

        if($ie_container_css != ''){
            // $quiz_styles .= "#ays-quiz-container-" . $id . " .ays_next.action-button,
            //                 #ays-quiz-container-" . $id . " .ays_previous.action-button{
            //                     margin: 10px 5px;
            //                 }";

            $quiz_styles .= "
            /*
            #ays-quiz-container-" . $id . " .ays_next.action-button,
            #ays-quiz-container-" . $id . " .ays_previous.action-button{
                margin: 10px 5px;
            }
            */

            #ays-quiz-container-" . $id . " .ays_block_content{
                margin: 0 auto;
                word-break: break-all;
            }

            ";

        }
                
        $quiz_styles .= "

            /* Styles for Quiz container */
            #ays-quiz-container-" . $id . "{
                min-height: " . $quiz_height . "px;
                width:" . $quiz_width . ";
                background-color:" . $bg_color . ";
                background-position:" . $quiz_bg_image_position . ";";

        if($ays_quiz_bg_image !== null){
            $quiz_styles .=  "background-image: url('$ays_quiz_bg_image');";
        } elseif($enable_background_gradient) {
            $quiz_styles .=  "background-image: linear-gradient($quiz_gradient_direction, $background_gradient_color_1, $background_gradient_color_2);";
        }

        if($quiz_modified_border_radius != ""){
            $quiz_styles .= $quiz_modified_border_radius;
        }else{
            $quiz_styles .=  "border-radius:" . $quiz_border_radius . "px;";
        }

        if($enable_box_shadow){
            $quiz_styles .=  "box-shadow: ". $box_shadow_offsets ." 1px " . $this->hex2rgba($box_shadow_color, '0.4') . ";";
        }else{
            $quiz_styles .=  "box-shadow: none;";
        }
        if($enable_border){
            $quiz_styles .=  "border-width: " . $quiz_border_width.'px;'.
                           "border-style: " . $quiz_border_style.';'.
                           "border-color: " . $quiz_border_color.';';
        }else{
            $quiz_styles .=  "border: none;";
        }

        $quiz_styles .= "}

            /* Styles for questions */
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " div.step {
                min-height: " . $quiz_height . "px;
            }

            /* Styles for text inside quiz container */
            #ays-quiz-container-" . $id . " .ays-start-page *:not(input),
            #ays-quiz-container-" . $id . " .ays_question_hint,
            #ays-quiz-container-" . $id . " label[for^=\"ays-answer-\"],
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " p,
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .ays-fs-title,
            #ays-quiz-container-" . $id . " .ays-fs-subtitle,
            #ays-quiz-container-" . $id . " .logged_in_message,
            #ays-quiz-container-" . $id . " .ays_score_message,
            #ays-quiz-container-" . $id . " .ays_message{
               color: " . $text_color . ";
               outline: none;
            }

            #ays-quiz-container-" . $id . " .ays-quiz-password-message-box,
            #ays-quiz-container-" . $id . " .ays-quiz-question-note-message-box,
            #ays-quiz-container-" . $id . " .ays_quiz_question,
            #ays-quiz-container-" . $id . " .ays_quiz_question *:not([class^='enlighter']) {
                color: " . $text_color . ";
            }

            #ays-quiz-container-" . $id . " textarea,
            #ays-quiz-container-" . $id . " input::first-letter,
            #ays-quiz-container-" . $id . " select::first-letter,
            #ays-quiz-container-" . $id . " option::first-letter {
                color: initial !important;
            }
            
            #ays-quiz-container-" . $id . " p::first-letter:not(.ays_no_questions_message) {
                color: " . $text_color . " !important;
                background-color: transparent !important;
                font-size: inherit !important;
                font-weight: inherit !important;
                float: none !important;
                line-height: inherit !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            
            
            #ays-quiz-container-" . $id . " .select2-container,
            #ays-quiz-container-" . $id . " .ays-field * {
                font-size: ".$answers_font_size."px !important;
            }
    
            #ays-quiz-container-" . $id . " .ays_quiz_question p {
                font-size: ".$question_font_size."px;
                
            }

            #ays-quiz-container-" . $id . " .ays-fs-subtitle p {
                text-align:  ". $quiz_question_text_alignment ." ;
            }

            #ays-quiz-container-" . $id . " .ays_quiz_question {
                text-align:  ". $quiz_question_text_alignment ." ;
                margin-bottom: 10px;
            }

            #ays-quiz-container-" . $id . " .ays_quiz_question pre {
                max-width: 100%;
                white-space: break-spaces;
            }

            #ays-quiz-container-" . $id . " .ays-quiz-timer p {
                font-size: 16px;
            }

            #ays-quiz-container-" . $id . " section.ays_quiz_redirection_timer_container hr,
            #ays-quiz-container-" . $id . " section.ays_quiz_timer_container hr {
                margin: 0;
            }

            #ays-quiz-container-" . $id . " section.ays_quiz_timer_container.ays_quiz_timer_red_warning .ays-quiz-timer {
                color: red;
            }

            #ays-quiz-container-" . $id . " .ays_thank_you_fs p {
                text-align: center;
            }

            #ays-quiz-container-" . $id . " input[type='button'],
            #ays-quiz-container-" . $id . " input[type='submit'] {
                color: " . $buttons_text_color . " !important;
                outline: none;
            }

            #ays-quiz-container-" . $id . " .information_form input[type='text'],
            #ays-quiz-container-" . $id . " .information_form input[type='url'],
            #ays-quiz-container-" . $id . " .information_form input[type='number'],
            #ays-quiz-container-" . $id . " .information_form input[type='email'],
            #ays-quiz-container-" . $id . " .information_form input[type='checkbox'],
            #ays-quiz-container-" . $id . " .information_form input[type='tel'],
            #ays-quiz-container-" . $id . " .information_form textarea,
            #ays-quiz-container-" . $id . " .information_form select,
            #ays-quiz-container-" . $id . " .information_form option {
                color: initial !important;
                outline: none;
                background-image: unset;
            }

            #ays-quiz-container-" . $id . " .wrong_answer_text{
                color:#ff4d4d;
            }
            #ays-quiz-container-" . $id . " .right_answer_text{
                color:#33cc33;
            }
            #ays-quiz-container-" . $id . " .ays_cb_and_a,
            #ays-quiz-container-" . $id . " .ays_cb_and_a * {
                color: " . $this->hex2rgba($text_color) . ";
                text-align: center;
            }

            /* Quiz textarea height */
            #ays-quiz-container-" . $id . " textarea {
                height: ". $quiz_textarea_height ."px;
                min-height: ". $quiz_textarea_height ."px;
            }

            /* Quiz rate and passed users count */
            #ays-quiz-container-" . $id . " .ays_quizn_ancnoxneri_qanak,
            #ays-quiz-container-" . $id . " .ays_quiz_rete_avg {
                color:" . $bg_color . " !important;
                background-color:" . $text_color . ";   
            }

            #ays-quiz-container-" . $id . " .ays-questions-container > .ays_quizn_ancnoxneri_qanak {
                padding: 5px 20px;
            }
            #ays-quiz-container-" . $id . " div.for_quiz_rate.ui.star.rating .icon {
                color: " . $this->hex2rgba($text_color, '0.35') . ";
            }
            #ays-quiz-container-" . $id . " .ays_quiz_rete_avg div.for_quiz_rate_avg.ui.star.rating .icon {
                color: " . $this->hex2rgba($bg_color, '0.5') . ";
            }

            #ays-quiz-container-" . $id . " .ays_quiz_rete .ays-quiz-rate-link-box .ays-quiz-rate-link {
                color: " . $text_color . ";
            }

            /* Loaders */            
            #ays-quiz-container-" . $id . " div.lds-spinner,
            #ays-quiz-container-" . $id . " div.lds-spinner2 {
                color: " . $text_color . ";
            }
            #ays-quiz-container-" . $id . " div.lds-spinner div:after,
            #ays-quiz-container-" . $id . " div.lds-spinner2 div:after {
                background-color: " . $text_color . ";
            }
            #ays-quiz-container-" . $id . " .lds-circle,
            #ays-quiz-container-" . $id . " .lds-facebook div,
            #ays-quiz-container-" . $id . " .lds-ellipsis div{
                background: " . $text_color . ";
            }
            #ays-quiz-container-" . $id . " .lds-ripple div{
                border-color: " . $text_color . ";
            }
            #ays-quiz-container-" . $id . " .lds-dual-ring::after,
            #ays-quiz-container-" . $id . " .lds-hourglass::after{
                border-color: " . $text_color . " transparent " . $text_color . " transparent;
            }

            /* Stars */
            #ays-quiz-container-" . $id . " .ui.rating .icon,
            #ays-quiz-container-" . $id . " .ui.rating .icon:before {
                font-family: Rating !important;
            }

            /* Progress bars */
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .ays-progress {
                border-color: " . $this->hex2rgba($text_color, '0.8') . ";
            }
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .ays-progress-bg {
                background-color: " . $this->hex2rgba($text_color, '0.3') . ";
            }";

        if ($enable_live_progress_bar) {
            $quiz_styles .= "
            #ays-quiz-container-" . $id . " ." . $filling_type . " {
                background-color: " . $color . ";
            }
            #ays-quiz-container-" . $id . " ." . $filling_type_wrap . " {
                background-color: " . $text_color . ";
            }";
        }

        if ($quiz_image_height != '' && $quiz_image_height > 0) {
            $quiz_styles .= "
            /* Quiz image */
            #ays-quiz-container-" . $id . " .ays_quiz_image{
                height: " . $quiz_image_height . "px;
            }";
        }

        if ($quiz_bg_img_on_start_page) {
            if($enable_background_gradient) {
                $ays_quiz_bg_style_value = "background-image: linear-gradient(". $quiz_gradient_direction .", ". $background_gradient_color_1 .", ". $background_gradient_color_2 .");";
            }else {
                $ays_quiz_bg_style_value = "background-image: unset";
            }

            $quiz_styles .= "
            div#ays-quiz-container-" . $id . ".ays_quiz_hide_bg_on_start_page {
                " . $ays_quiz_bg_style_value . ";
            }";
        }

        $quiz_styles .= "    
            #ays-quiz-container-" . $id . " .ays-progress-value {
                color: " . $text_color . ";
                text-align: center;
            }
            #ays-quiz-container-" . $id . " .ays-progress-bar {
                background-color: " . $color . ";
            }
            #ays-quiz-container-" . $id . " .ays-question-counter .ays-live-bar-wrap {
                direction:ltr !important;
            }
            #ays-quiz-container-" . $id . " .ays-live-bar-fill{
                color: " . $text_color . ";
                border-bottom: 2px solid " . $this->hex2rgba($text_color, '0.8') . ";
                text-shadow: 0px 0px 5px " . $bg_color . ";
            }
            #ays-quiz-container-" . $id . " .ays-live-bar-fill.ays-live-fourth,
            #ays-quiz-container-" . $id . " .ays-live-bar-fill.ays-live-third,
            #ays-quiz-container-" . $id . " .ays-live-bar-fill.ays-live-second {
                text-shadow: unset;
            }
            #ays-quiz-container-" . $id . " .ays-live-bar-percent{
                display:none;
            }
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .ays_average {
                text-align: center;
            }
            
            /* Music, Sound */
            #ays-quiz-container-" . $id . " .ays_music_sound {
                color:" . $this->hex2rgba($text_color) . ";
            }

            /* Dropdown questions scroll bar */
            #ays-quiz-container-" . $id . " blockquote {
                border-left-color: " . $text_color . " !important;                                      
            }

            /* Quiz Password */
            #ays-quiz-container-" . $id . " .ays-start-page > input[id^='ays_quiz_password_val_'],
            #ays-quiz-container-" . $id . " .ays-quiz-password-toggle-visibility-box {
                width: ". $quiz_password_width_css .";
            }


            /* Question hint */
            #ays-quiz-container-" . $id . " .ays_question_hint_container .ays_question_hint_text {
                background-color:" . $bg_color . ";
                box-shadow: 0 0 15px 3px " . $this->hex2rgba($box_shadow_color, '0.6') . ";
                max-width: 270px;
            }

            #ays-quiz-container-" . $id . " .ays_question_hint_container .ays_question_hint_text p {
                max-width: unset;
            }

            #ays-quiz-container-" . $id . " .ays_questions_hint_max_width_class {
                max-width: 80%;
            }

            /* Information form */
            #ays-quiz-container-" . $id . " .ays-form-title{
                color:" . $this->hex2rgba($text_color) . ";
            }

            /* Quiz timer */
            #ays-quiz-container-" . $id . " div.ays-quiz-redirection-timer,
            #ays-quiz-container-" . $id . " div.ays-quiz-timer{
                color: " . $text_color . ";
                text-align: center;
            }

            #ays-quiz-container-" . $id . " div.ays-quiz-timer.ays-quiz-message-before-timer:before {
                font-weight: 500;
            }

            /* Quiz title / transformation */
            #ays-quiz-container-" . $id . " .ays-fs-title{
                text-transform: " . $quiz_title_transformation . ";
                font-size: " . $quiz_title_font_size . "px;
                text-align: center;";

            if($quiz_enable_title_text_shadow){
                $quiz_styles .= "
                    text-shadow: " . $title_text_shadow_offsets . " " . $quiz_title_text_shadow_color . ";";
            }else{
                $quiz_styles .= "
                    text-shadow: none;";
            }

            $quiz_styles .= "
            }
            
            /* Quiz buttons */
            #ays-quiz-container-" . $id . " .ays_arrow {
                color:". $buttons_text_color ."!important;
            }
            #ays-quiz-container-" . $id . " input#ays-submit,
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .action-button,
            div#ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .action-button.ays_restart_button {
                background: none;
                background-color: " . $color . ";
                color:" . $buttons_text_color . ";
                font-size: " . $buttons_font_size . ";
                padding: " . $buttons_top_bottom_padding . " " . $buttons_left_right_padding . ";
                border-radius: " . $buttons_border_radius . ";
                height: auto;
                letter-spacing: 0;
                box-shadow: unset;
            }
            #ays-quiz-container-" . $id . " input#ays-submit,
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " input.action-button {
                " . $buttons_width_html . "
            }

            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .action-button.ays_check_answer {
                padding: 5px 10px;
                font-size: " . $buttons_font_size . " !important;
            }
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .action-button.ays_restart_button {
                white-space: nowrap;
                padding: 5px 10px;
                white-space: normal;
            }
            #ays-quiz-container-" . $id . " input#ays-submit:hover,
            #ays-quiz-container-" . $id . " input#ays-submit:focus,
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .action-button:hover,
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .action-button:focus {
                background: none;
                box-shadow: 0 0 0 2px $buttons_text_color;
                background-color: " . $color . ";
            }
            #ays-quiz-container-" . $id . " .ays_restart_button {
                color: " . $buttons_text_color . ";
            }
            
            #ays-quiz-container-" . $id . " .ays_restart_button_p,
            #ays-quiz-container-" . $id . " .ays_buttons_div {
                justify-content: " . $buttons_position . ";
            }";

            if($buttons_position == "flex-start"){
                $quiz_styles .= "
                #ays-quiz-container-" . $id . " .action-button.start_button{
                    float: left;
                    clear: both;
                }";
            }elseif ($buttons_position == "flex-end") {
                $quiz_styles .= "
                #ays-quiz-container-" . $id . " .action-button.start_button{
                    float: right;
                    clear: both;
                }";
            }

            $quiz_styles .= "

            #ays-quiz-container-" . $id . " .ays_finish.action-button{
                margin: 10px 5px;
            }

            #ays-quiz-container-" . $id . " .ays-share-btn.ays-share-btn-branded {
                color: #fff;
            }
                        
            /* Question answers */
            #ays-quiz-container-".$id." .ays-field {";

            if($answers_border){
                $quiz_styles .= "
                    border-color: " . $answers_border_color . ";
                    border-style: " . $answers_border_style . ";
                    border-width: " . $answers_border_width . "px;";
            }else{
                $quiz_styles .= "
                    border-color: transparent;
                    border-style: none;
                    border-width: 0;";
            }

            if($answers_box_shadow){
                $quiz_styles .= "
                    box-shadow: " . $answer_box_shadow_offsets . " 1px " . $answers_box_shadow_color . ";";
            }else{
                $quiz_styles .= "
                    box-shadow: none;";
            }

            $quiz_styles .= "
            }

            /* Answer maximum length of a text field */
            #ays-quiz-container-" . $id . " .ays_quiz_question_text_message{
                color: " . $text_color . ";
                text-align: left;
                font-size: 12px;
            }

            div#ays-quiz-container-" . $id . " div.ays_quiz_question_text_error_message {
                color: #ff0000;
            }
            ";

        if (! $disable_hover_effect) {
            $quiz_styles .= "
            #ays-quiz-container-" . $id . " .ays-quiz-answers .ays-field:hover{
                opacity: 1;
            }";
        } else{
            $quiz_styles .= "
            #ays-quiz-container-" . $id . " .ays-quiz-answers .ays-field:hover,
            #ays-quiz-container-" . $id . " .ays-quiz-answers .ays-field{
                opacity: 1;
            }

            #ays-quiz-container-" . $id . ".ays_quiz_elegant_light .ays-quiz-answers .ays-field:hover,
            #ays-quiz-container-" . $id . ".ays_quiz_elegant_light .ays-quiz-answers .ays-field,
            #ays-quiz-container-" . $id . ".ays_quiz_elegant_dark .ays-quiz-answers .ays-field:hover,
            #ays-quiz-container-" . $id . ".ays_quiz_elegant_dark .ays-quiz-answers .ays-field{
                opacity: 0.6;
            }";
        }

        $quiz_styles .= "
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .ays-field {
                margin-bottom: " . ($answers_margin) . "px;
            }
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .ays-field.ays_grid_view_item {
                width: calc(50% - " . ($answers_margin / 2) . "px);
            }
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .ays-field.ays_grid_view_item:nth-child(odd) {
                margin-right: " . ($answers_margin / 2) . "px;
            }
            
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .ays-field input:checked+label:before {
                border-color: " . $color . ";
                background: " . $color . ";
                background-clip: content-box;
            }
            #ays-quiz-container-" . $id . " .ays-quiz-answers div.ays-text-right-answer {
                color: " . $text_color . ";
            }
            
            /* Questions answer image */
            #ays-quiz-container-" . $id . " .ays-answer-image {
                width:" . (isset($options['answers_view']) && ($options['answers_view'] == "grid") ? "90%" : "50%") . ";
            }
            
            /* Questions answer right/wrong icons */
            ";
        if($ans_right_wrong_icon == 'default'){
            $quiz_styles .= "#ays-quiz-container-" . $id . " .ays-field input~label.answered.correct:after{
                content: url('".AYS_QUIZ_PUBLIC_URL."/images/correct.png');          }
            #ays-quiz-container-" . $id . " .ays-field input~label.answered.wrong:after{
                content: url('".AYS_QUIZ_PUBLIC_URL."/images/wrong.png');
            }";
        }else{
            $quiz_styles .= "#ays-quiz-container-" . $id . " .ays-field input~label.answered.correct:after{
                content: url('".AYS_QUIZ_PUBLIC_URL."/images/correct-".$ans_right_wrong_icon.".png');
            }
            #ays-quiz-container-" . $id . " .ays-field input~label.answered.wrong:after{
                content: url('".AYS_QUIZ_PUBLIC_URL."/images/wrong-".$ans_right_wrong_icon.".png');
            }";
            
            if ( $ans_right_wrong_icon == 'style-9' ) {
               $quiz_styles .= "#ays-quiz-container-" . $id . " .ays-field input+label.answered:after{
                    width: unset;
                    height: unset;
                }";
            }
        }

        $quiz_styles .= "
            /* Dropdown questions */            
            #ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .ays-field .select2-container--default .select2-selection--single {
                border-bottom: 2px solid " . $color . ";
                background-color: " . $color . ";
            }
            
            #ays-quiz-container-" . $id . " .ays-field .select2-container--default .select2-selection--single .select2-selection__placeholder,
            #ays-quiz-container-" . $id . " .ays-field .select2-container--default .select2-selection--single .select2-selection__rendered,
            #ays-quiz-container-" . $id . " .ays-field .select2-container--default .select2-selection--single .select2-selection__arrow {
                color: " . $this->ays_color_inverse( $text_color ) . ";
            }

            #ays-quiz-container-" . $id . " .select2-container--default .select2-search--dropdown .select2-search__field:focus,
            #ays-quiz-container-" . $id . " .select2-container--default .select2-search--dropdown .select2-search__field {
                outline: unset;
                padding: 0.75rem;
            }

            #ays-quiz-container-" . $id . " .ays-field .select2-container--default .select2-selection--single .select2-selection__rendered,
            #ays-quiz-container-" . $id . " .select2-container--default .select2-results__option--highlighted[aria-selected] {
                background-color: " . $color . ";
            }

            #ays-quiz-container-" . $id . " .ays-field .select2-container--default,
            #ays-quiz-container-" . $id . " .ays-field .select2-container--default .selection,
            #ays-quiz-container-" . $id . " .ays-field .select2-container--default .dropdown-wrapper,
            #ays-quiz-container-" . $id . " .ays-field .select2-container--default .select2-selection--single .select2-selection__rendered,
            #ays-quiz-container-" . $id . " .ays-field .select2-container--default .select2-selection--single .select2-selection__rendered .select2-selection__placeholder,
            #ays-quiz-container-" . $id . " .ays-field .select2-container--default .select2-selection--single .select2-selection__arrow,
            #ays-quiz-container-" . $id . " .ays-field .select2-container--default .select2-selection--single .select2-selection__arrow b[role='presentation'] {
                font-size: 16px !important;
            }

            #ays-quiz-container-" . $id . " .select2-container--default .select2-results__option {
                padding: 6px;
            }
            
            /* Dropdown questions scroll bar */
            #ays-quiz-container-" . $id . " .select2-results__options::-webkit-scrollbar {
                width: 7px;
            }
            #ays-quiz-container-" . $id . " .select2-results__options::-webkit-scrollbar-track {
                background-color: " . $this->hex2rgba($bg_color, '0.35') . ";
            }
            #ays-quiz-container-" . $id . " .select2-results__options::-webkit-scrollbar-thumb {
                transition: .3s ease-in-out;
                background-color: " . $this->hex2rgba($text_color, '0.55') . ";
            }
            #ays-quiz-container-" . $id . " .select2-results__options::-webkit-scrollbar-thumb:hover {
                transition: .3s ease-in-out;
                background-color: " . $this->hex2rgba($text_color, '0.85') . ";
            }

            /* Audio / Video */
            #ays-quiz-container-" . $id . " .mejs-container .mejs-time{
                box-sizing: unset;
            }
            #ays-quiz-container-" . $id . " .mejs-container .mejs-time-rail {
                padding-top: 15px;
            }

            #ays-quiz-container-" . $id . " .mejs-container .mejs-mediaelement video {
                margin: 0;
            }

            /* Limitation */
            #ays-quiz-container-" . $id . " .ays-quiz-limitation-count-of-takers {
                padding: 50px;
            }

            #ays-quiz-container-" . $id . " div.ays-quiz-results-toggle-block span.ays-show-res-toggle.ays-res-toggle-show,
            #ays-quiz-container-" . $id . " div.ays-quiz-results-toggle-block span.ays-show-res-toggle.ays-res-toggle-hide{
                color: ". $text_color .";
            }

            #ays-quiz-container-" . $id . " div.ays-quiz-results-toggle-block input:checked + label.ays_switch_toggle {
                border: 1px solid ". $text_color .";
            }

            #ays-quiz-container-" . $id . " div.ays-quiz-results-toggle-block input:checked + label.ays_switch_toggle {
                border: 1px solid ". $text_color .";
            }

            #ays-quiz-container-" . $id . " div.ays-quiz-results-toggle-block input:checked + label.ays_switch_toggle:after{
                background: ". $text_color .";
            }

            /* Hestia theme (Version: 3.0.16) | Start */
            #ays-quiz-container-" . $id . " .mejs-container .mejs-inner .mejs-controls .mejs-button > button:hover,
            #ays-quiz-container-" . $id . " .mejs-container .mejs-inner .mejs-controls .mejs-button > button {
                box-shadow: unset;
                background-color: transparent;
            }
            #ays-quiz-container-" . $id . " .mejs-container .mejs-inner .mejs-controls .mejs-button > button {
                margin: 10px 6px;
            }
            /* Hestia theme (Version: 3.0.16) | End */

            /* Go theme (Version: 1.4.3) | Start */
            #ays-quiz-container-" . $id . " label[for^='ays-answer']:before,
            #ays-quiz-container-" . $id . " label[for^='ays-answer']:before {
                -webkit-mask-image: unset;
                mask-image: unset;
            }

            #ays-quiz-container-" . $id . ".ays_quiz_classic_light .ays-field input:checked+label.answered.correct:before,
            #ays-quiz-container-" . $id . ".ays_quiz_classic_dark .ays-field input:checked+label.answered.correct:before {
                background-color: ". $color ." !important;
            }
            /* Go theme (Version: 1.4.3) | End */

            #ays-quiz-container-" . $id . " .ays_quiz_results fieldset.ays_fieldset .ays_quiz_question .wp-video {
                width: 100% !important;
                max-width: 100%;
            }

            /* Classic Dark / Classic Light */
            /* Dropdown questions right/wrong styles */
            #ays-quiz-container-" . $id . ".ays_quiz_classic_dark .correct_div,
            #ays-quiz-container-" . $id . ".ays_quiz_classic_light .correct_div{
                border-color:green !important;
                opacity: 1 !important;
                background-color: rgba(39,174,96,0.4) !important;
            }
            #ays-quiz-container-" . $id . ".ays_quiz_classic_dark .correct_div .selected-field,
            #ays-quiz-container-" . $id . ".ays_quiz_classic_light .correct_div .selected-field {
                padding: 0px 10px 0px 10px;
                color: green !important;
            }

            #ays-quiz-container-" . $id . ".ays_quiz_classic_dark .wrong_div,
            #ays-quiz-container-" . $id . ".ays_quiz_classic_light .wrong_div{
                border-color:red !important;
                opacity: 1 !important;
                background-color: rgba(243,134,129,0.4) !important;
            }
            #ays-quiz-container-" . $id . ".ays_quiz_classic_dark .ays-field,
            #ays-quiz-container-" . $id . ".ays_quiz_classic_light .ays-field {
                text-align: left;
                /*margin-bottom: 10px;*/
                padding: 0;
                transition: .3s ease-in-out;
            }

            #ays-quiz-container-" . $id . " .ays-quiz-close-full-screen {
                fill: $text_color;
            }

            #ays-quiz-container-" . $id . " .ays-quiz-open-full-screen {
                fill: $text_color;
            }

            @media screen and (max-width: 768px){
                #ays-quiz-container-" . $id . "{
                    max-width: $mobile_max_width;
                }

                #ays-quiz-container-" . $id . " .ays_quiz_question p {
                    font-size: ".$question_mobile_font_size."px;
                }

                #ays-quiz-container-" . $id . " .select2-container,
                #ays-quiz-container-" . $id . " .ays-field * {
                    font-size: ".$answers_mobile_font_size."px !important;
                }

                div#ays-quiz-container-" . $id . " input#ays-submit,
                div#ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .action-button,
                div#ays-quiz-container-" . $id . " #ays_finish_quiz_" . $id . " .action-button.ays_restart_button {
                    font-size: ".$buttons_mobile_font_size."px;
                }

                /* Quiz title / mobile font size */
                div#ays-quiz-container-" . $id . " .ays-fs-title {
                    font-size: " . $quiz_title_mobile_font_size . "px;
                }
            }
            /* Custom css styles */
            " . stripslashes( htmlspecialchars_decode( $options['custom_css'] ) ) . "
            
            /* RTL direction styles */
            " . $rtl_style . "
        </style>";


        
    /*******************************************************************************************************/
        
        /*
         * Quiz container
         *
         * Generating HTML code
         */
        
        $quiz_theme = "";
        $options['quiz_theme'] = (array_key_exists('quiz_theme', $options)) ? $options['quiz_theme'] : '';
        switch ($options['quiz_theme']) {
            case 'elegant_dark':
                $quiz_theme = "ays_quiz_elegant_dark";
                break;
            case 'elegant_light':
                $quiz_theme = "ays_quiz_elegant_light";
                break;
            case 'rect_dark':
                $quiz_theme = "ays_quiz_rect_dark";
                break;
            case 'rect_light':
                $quiz_theme = "ays_quiz_rect_light";
                break;
            case 'classic_dark':
                $quiz_theme = "ays_quiz_classic_dark";
                break;
            case 'classic_light':
                $quiz_theme = "ays_quiz_classic_light";
                break;
        }
        
        $custom_class = isset($options['custom_class']) && $options['custom_class'] != "" ? $options['custom_class'] : "";
		$quiz_gradient = '';
		if($enable_background_gradient){
			$quiz_gradient = " data-bg-gradient='linear-gradient($quiz_gradient_direction, $background_gradient_color_1, $background_gradient_color_2)' ";
		}


        $options['enable_full_screen_mode'] = isset($options['enable_full_screen_mode']) ? $options['enable_full_screen_mode'] : 'off';
        $enable_full_screen_mode = (isset($options['enable_full_screen_mode']) && $options['enable_full_screen_mode'] == "on") ? true : false;

        $fullcsreen_mode = '';

        if($enable_full_screen_mode){
            $fullcsreen_mode = '<div class="ays-quiz-full-screen-wrap">
                <a class="ays-quiz-full-screen-container">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24" fill="#fff" viewBox="0 0 24 24" width="24" class="ays-quiz-close-full-screen">
                        <path d="M0 0h24v24H0z" fill="none"/>
                        <path d="M5 16h3v3h2v-5H5v2zm3-8H5v2h5V5H8v3zm6 11h2v-3h3v-2h-5v5zm2-11V5h-2v5h5V8h-3z"/>
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" height="24" fill="#fff" viewBox="0 0 24 24" width="24" class="ays-quiz-open-full-screen">
                        <path d="M0 0h24v24H0z" fill="none"/>
                        <path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/>
                    </svg>
                </a>
            </div>';
        }else {
            $fullcsreen_mode = '';
        }

        $quiz_container_first_part = "
            <div class='ays-quiz-container ".$quiz_theme." ".$quiz_bg_img_class." ".$custom_class."' data-quest-effect='".$quest_animation."' ".$quiz_gradient." data-hide-bg-image='".$quiz_bg_img_in_finish_page."' id='ays-quiz-container-" . $id . "'>
                {$live_progress_bar}
                {$ays_quiz_music_html}
                <div class='ays-questions-container'>
                    {$fullcsreen_mode}
                    $ays_quiz_reports
                    <form action='' method='post' id='ays_finish_quiz_" . $id . "' 
                        class='" . $correction_class . " " . $enable_questions_result . " " . $enable_logged_users . "'
                    >";
        
        $quiz_container_first_part .= "
            <input type='hidden' value='" . $answer_view_class . "' class='answer_view_class'>
            <input type='hidden' value='" . $enable_arrows . "' class='ays_qm_enable_arrows'>";
        
        $quiz_container_middle_part = "";
        if($is_expired){
            $quiz_container_middle_part = $expired_quiz_message;
            $main_content_first_part = "";
            $main_content_last_part = "";
        }
        if($enable_tackers_count){
            $quiz_tackers_count = $this->get_quiz_tackers_count($id);
            if($quiz_tackers_count >= $tackers_count){
                $quiz_container_middle_part = $tackers_message;
                $main_content_first_part = "";
                $main_content_last_part = "";
            }
        }
        if($limit_users === true){
            $quiz_container_middle_part = $limit_users_html;
            $main_content_first_part = "";
            $main_content_last_part = "";
        }
        if($user_massage !== null){
            $quiz_container_middle_part = "<!-- This content is empty -->";
            $main_content_first_part = "";
            $main_content_last_part = "";
        }
        

        $quiz_container_last_part = $quiz_content_script;
        $quiz_container_last_part .= "
                    <input type='hidden' name='quiz_id' value='" . $id . "'/>
                    <input type='hidden' name='start_date' class='ays-start-date'/>
                </form>";
        if($user_massage !== null){
            $quiz_container_last_part .= $user_massage;
        }
        $quiz_container_last_part .= "</div>
                                </div>";
        
        
    /*******************************************************************************************************/
        
        /*
         * Generating Quiz parts array
         */
        
        $quiz_parts = array(
            "container_first_part" => $quiz_container_first_part,
            "main_content_first_part" => $main_content_first_part,
            "main_content_middle_part" => $quiz_container_middle_part,
            "main_content_last_part" => $main_content_last_part,
            "quiz_styles" => $quiz_styles,
            "quiz_additional_styles" => "",
            "container_last_part" => $quiz_container_last_part,
        );
        
        $quizOptions = array(
            'buttons' => $buttons,
            'correction' => $enable_correction,
            'randomizeAnswers' => $randomize_answers,
            'questionImageWidth' => $question_image_width,
            'questionImageHeight' => $question_image_height,
            'questionImageSizing' => $question_image_sizing,
            'questionsCounter' => $questions_counter,
            'questions_score' => $questions_score,
            'informationForm' => $options['information_form'],
            'answersViewClass' => $answer_view_class,
            'quizTheme' => $options['quiz_theme'],
            'rtlDirection' => $rtl_direction,
            'showQuestionCategory' => $show_question_category,
            'showQuestionCategoryDescription' => $quiz_enable_question_category_description,
            'questionsHint' => $questions_hint_arr,
            'disable_hover_effect' => $disable_hover_effect,
            'show_answers_numbering' => $show_answers_numbering,
            'show_questions_numbering' => $show_questions_numbering,
            'show_questions_explanation' => $show_questions_explanation,
            'answers_rw_texts' => $answers_rw_texts,
        );
        
        $ays_quiz = (object)array(
            "quizID" => $id,
            "quizOptions" => $quizOptions,
            "questions" => $arr_questions,
            "questionsCount" => $questions_count,
            "quizParts" => $quiz_parts,
            "quizColors" => array(
                "Color" => $color,
                "textColor" => $text_color,
                "bgColor" => $bg_color,
                "boxShadowColor" => $box_shadow_color,
                "borderColor" => $quiz_border_color
            )
        );
            
        return $ays_quiz;
    }

    public function ays_generate_quiz($quiz){
        
        $quiz_id = $quiz->quizID;
        $arr_questions = $quiz->questions;
        $questions_count = $quiz->questionsCount;
        $options = $quiz->quizOptions;

        // Disable answer hover
        $options['disable_hover_effect'] = isset($options['disable_hover_effect']) ? $options['disable_hover_effect'] : 'off';
        $disable_hover_effect = (isset($options['disable_hover_effect']) && $options['disable_hover_effect'] == "on") ? true : false;

        $questions = "";
        $questions = $this->get_quiz_questions($arr_questions, $quiz_id, $options, false);
        
        if($quiz->quizParts['main_content_middle_part'] == ""){
            $quiz->quizParts['main_content_middle_part'] = $questions;
        }
        $additional_css = "
            <style>
                #ays-quiz-container-" . $quiz_id . " p {
                    margin: 0.625em;
                }
                
                #ays-quiz-container-" . $quiz_id . " .ays-field.checked_answer_div input:checked+label {
                    background-color: " . $this->hex2rgba($quiz->quizColors['Color'], '0.6') . ";
                }

                #ays-quiz-container-" . $quiz_id . ".ays_quiz_classic_light  .enable_correction .ays-field.checked_answer_div input:checked+label,
                #ays-quiz-container-" . $quiz_id . ".ays_quiz_classic_dark  .enable_correction .ays-field.checked_answer_div input:checked+label {
                    background-color: transparent;
                }";
        if (! $disable_hover_effect) {
            $additional_css .= "
                #ays-quiz-container-" . $quiz_id . " .ays-field.checked_answer_div input:checked+label:hover {
                    background-color: " . $this->hex2rgba($quiz->quizColors['Color'], '0.8') . ";
                }

                #ays-quiz-container-" . $quiz_id . " .ays-field:hover label{
                    background: " . $this->hex2rgba($quiz->quizColors['Color'], '0.8') . ";
                    /* border-radius: 4px; */
                    color: #fff;
                    transition: all .3s;
                }";
        }

         $additional_css .= "
                #ays-quiz-container-" . $quiz_id . " #ays_finish_quiz_" . $quiz_id . " .action-button:hover,
                #ays-quiz-container-" . $quiz_id . " #ays_finish_quiz_" . $quiz_id . " .action-button:focus {
                    box-shadow: 0 0 0 2px white, 0 0 0 3px " . $quiz->quizColors['Color'] . ";
                    background: " . $quiz->quizColors['Color'] . ";
                }
            </style>";
        
        $quiz->quizParts['quiz_additional_styles'] = $additional_css;
        
        $container = implode("", $quiz->quizParts);
        
        return $container;
    }

    public function get_quiz_by_id($id){
        global $wpdb;

        $sql = "SELECT *
                FROM {$wpdb->prefix}aysquiz_quizes
                WHERE id=" . $id;

        $quiz = $wpdb->get_row($sql, 'ARRAY_A');

        return $quiz;
    }
    
    public static function get_quiz_category_by_id($id){
        global $wpdb;

        $sql = "SELECT *
                FROM {$wpdb->prefix}aysquiz_quizcategories
                WHERE id=" . $id;

        $category = $wpdb->get_row($sql, 'ARRAY_A');

        return $category;
    }
    
    public static function get_question_category_by_id($id){
        global $wpdb;

        $sql = "SELECT *
                FROM {$wpdb->prefix}aysquiz_categories
                WHERE id=" . $id;

        $category = $wpdb->get_row($sql, 'ARRAY_A');

        return $category;
    }
    
    public function get_quiz_results_count_by_id($id){
        global $wpdb;

        $sql = "SELECT COUNT(*) AS res_count
                FROM {$wpdb->prefix}aysquiz_reports
                WHERE quiz_id=" . $id;

        $quiz = $wpdb->get_row($sql, 'ARRAY_A');

        return $quiz;
    }

    public function get_quiz_attributes_by_id($id){
        global $wpdb;
        $quiz_attrs = isset(json_decode($this->get_quiz_by_id($id)['options'])->quiz_attributes) ? json_decode($this->get_quiz_by_id($id)['options'])->quiz_attributes : array();
        $quiz_attributes = implode(',', $quiz_attrs);
        if (!empty($quiz_attributes)) {
            $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_attributes WHERE `id` in ($quiz_attributes)";
            $results = $wpdb->get_results($sql);
            return $results;
        }
        return array();

    }

    public function get_quiz_questions($ids, $quiz_id, $options, $per_page){
        
        $container = $this->ays_questions_parts($ids, $quiz_id, $options, $per_page);
        $questions_container = array();
        foreach($container as $key => $question){
            $answer_container = '';
            $use_html = $this->in_question_use_html($question['questionID']);
            switch ($question["questionType"]) {
                case "select":
                    $ans_options = array(
                        'correction' => $options['correction'],
                        'show_answers_numbering' => $options['show_answers_numbering'],
                    );
                    $answer_container .= $this->ays_dropdown_answer_html($question['questionID'], $quiz_id, $question['questionAnswers'], $ans_options);
                    break;
                case "text":
                    $question_max_length_array = $this->ays_quiz_get_question_max_length_array($question['questionID']);
                    $ans_options = array(
                        'correction' => $options['correction'],
                        'questionMaxLengthArray' => $question_max_length_array,
                        'enable_case_sensitive_text' => $question['enable_case_sensitive_text'],
                    );
                    $answer_container .= $this->ays_text_answer_html($question['questionID'], $quiz_id, $question['questionAnswers'], $ans_options);
                    break;
                case "short_text":
                    $question_max_length_array = $this->ays_quiz_get_question_max_length_array($question['questionID']);
                    $ans_options = array(
                        'correction' => $options['correction'],
                        'questionMaxLengthArray' => $question_max_length_array,
                        'enable_case_sensitive_text' => $question['enable_case_sensitive_text'],
                    );
                    $answer_container .= $this->ays_short_text_answer_html($question['questionID'], $quiz_id, $question['questionAnswers'], $ans_options);
                    break;
                case "number":
                    $question_max_length_array = $this->ays_quiz_get_question_max_length_array($question['questionID']);
                    $ans_options = array(
                        'correction' => $options['correction'],
                        'questionMaxLengthArray' => $question_max_length_array,
                    );
                    $answer_container .= $this->ays_number_answer_html($question['questionID'], $quiz_id, $question['questionAnswers'], $ans_options);
                    break;
                case "date":
                    $ans_options = array(
                        'correction' => $options['correction']
                    );
                    $answer_container .= $this->ays_date_answer_html($question['questionID'], $quiz_id, $question['questionAnswers'], $ans_options);
                    break;
                case "true_or_false":
                default:
                    $ans_options = array(
                        'correction' => $options['correction'],
                        'rtlDirection' => $options['rtlDirection'],
                        'questionType' => $question["questionType"],
                        'answersViewClass' => $options['answersViewClass'],
                        'show_answers_numbering' => $options['show_answers_numbering'],
                        'useHTML' => $use_html,
                        'enable_max_selection_number' => $question['enable_max_selection_number'],
                        'max_selection_number' => $question['max_selection_number'],
                        'enable_min_selection_number' => $question['enable_min_selection_number'],
                        'min_selection_number' => $question['min_selection_number'],
                    );
                    $answer_container .= $this->ays_default_answer_html($question['questionID'], $quiz_id, $question['questionAnswers'], $ans_options);
                    break;
            }
            $question['questionParts']['question_middle_part'] = $answer_container;
            $questions_container[] = implode("", $question['questionParts']);
        }
        $container = implode("", $questions_container);
        return $container;
    }
    
    public function ays_questions_parts($ids, $quiz_id, $options, $per_page){
        global $wpdb;
        $total = count($ids);
        $container = array();
        $buttons = $options['buttons'];
        $enable_arrows = $buttons['enableArrows'];
        $quiz_arrow_type = $buttons['quizArrowType'];
        $settings_buttons_texts = $this->buttons_texts;

        foreach($ids as $key => $id){
            $current = $key + 1;
            if($total == $current){
                $last = true;
            }else{
                $last = false;
            }
            $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_questions WHERE id = " . $id;
            $question = $wpdb->get_row($sql, 'ARRAY_A');
            
            if (!empty($question)) {
                $answers = $this->get_answers_with_question_id($question["id"]);
                $question_options = (isset($question['options']) && sanitize_text_field( $question['options'] ) != '') ? json_decode( $question['options'], true ) : array();
                $question_image = '';
                $question_image_style = '';
                $question_category = '';
                $question_category_description = '';
                $question_category_description_html = '';
                $show_question_category = $options['showQuestionCategory'];
                $show_question_category_description = $options['showQuestionCategoryDescription'];
                $show_questions_explanation = $options['show_questions_explanation'];
                $show_answers_rw_texts = $options['answers_rw_texts'];
                if($show_question_category){
                    $question_category_data = self::get_question_category_by_id($question['category_id']);
                    $question_category = $question_category_data['title'];
                    $question_category_description = ( isset( $question_category_data['description'] ) && $question_category_data['description'] != "" ) ? $question_category_data['description'] : "";
                    
                    $question_category = "<p style='margin:0!important;text-align:left;'>
                        <em style='font-style:italic;font-size:0.8em;'>". __("Category", $this->plugin_name) .":</em>
                        <strong style='font-size:0.8em;'>{$question_category}</strong>
                    </p>";

                    if ( $show_question_category_description && $question_category_description != "" ) {
                        $question_category_description_html .= '<div class="ays-quiz-category-description-box">';
                            $question_category_description_html .= $this->ays_autoembed($question_category_description);
                        $question_category_description_html .= '</div>';

                        $question_category .= $question_category_description_html;
                    }
                }

                if ( $question["type"] == 'true_or_false' ) {
                    $question["type"] = 'radio';
                }
                
                $question['not_influence_to_score'] = ! isset($question['not_influence_to_score']) ? 'off' : $question['not_influence_to_score'];
                $not_influence_to_score = (isset($question['not_influence_to_score']) && $question['not_influence_to_score'] == 'on') ? true : false;

                // Hide question text on the front-end
                $question_options['quiz_hide_question_text'] = isset($question_options['quiz_hide_question_text']) ? sanitize_text_field( $question_options['quiz_hide_question_text'] ) : 'off';
                $quiz_hide_question_text = (isset($question_options['quiz_hide_question_text']) && $question_options['quiz_hide_question_text'] == 'on') ? true : false;

                $question_image_style = "style='width:{$options['questionImageWidth']};height:{$options['questionImageHeight']};object-fit:{$options['questionImageSizing']};object-position:center center;'";

                // Enable maximum selection number
                $question_options['enable_max_selection_number'] = isset($question_options['enable_max_selection_number']) ? sanitize_text_field( $question_options['enable_max_selection_number'] ) : 'off';
                $enable_max_selection_number = (isset($question_options['enable_max_selection_number']) && sanitize_text_field( $question_options['enable_max_selection_number'] ) == 'on') ? true : false;

                // Max value
                $max_selection_number = ( isset($question_options['max_selection_number']) && $question_options['max_selection_number'] != '' ) ? intval( sanitize_text_field( $question_options['max_selection_number'] ) ) : '';

                // Enable minimum selection number
                $question_options['enable_min_selection_number'] = isset($question_options['enable_min_selection_number']) ? sanitize_text_field( $question_options['enable_min_selection_number'] ) : 'off';
                $enable_min_selection_number = (isset($question_options['enable_min_selection_number']) && sanitize_text_field( $question_options['enable_min_selection_number'] ) == 'on') ? true : false;

                // Min value
                $min_selection_number = ( isset($question_options['min_selection_number']) && $question_options['min_selection_number'] != '' ) ? intval( sanitize_text_field( $question_options['min_selection_number'] ) ) : '';

                $max_selection_number_class = '';
                $min_selection_number_class = '';
                if ( $question["type"] == 'checkbox' ) {

                    if ( $enable_max_selection_number && ! empty( $max_selection_number ) && $max_selection_number != 0 ) {
                        $max_selection_number_class = 'enable_max_selection_number';
                    }
                    if ( $enable_min_selection_number && ! empty( $min_selection_number ) && $min_selection_number != 0 ) {
                        $min_selection_number_class = 'enable_min_selection_number';
                    }
                }

                $enable_case_sensitive_text = false;
                if ( $question["type"] == 'text' || $question["type"] == 'short_text' ) {

                    // Enable case sensitive text
                    $question_options['enable_case_sensitive_text'] = isset($question_options['enable_case_sensitive_text']) ? sanitize_text_field( $question_options['enable_case_sensitive_text'] ) : 'off';
                    $enable_case_sensitive_text = (isset($question_options['enable_case_sensitive_text']) && sanitize_text_field( $question_options['enable_case_sensitive_text'] ) == 'on') ? true : false;
                }
                
                if ($question['question_image'] != NULL) {
                    $question_image_alt_text = $this->ays_quiz_get_image_id_by_url($question['question_image']);

                    $question_image = '<div class="ays-image-question-img"><img src="' . $question['question_image'] . '" alt="'. $question_image_alt_text .'" ' . $question_image_style . '></div>';
                }
                $answer_view_class = "";
                $question_hint = '';
                $user_explanation = "";
                if ($options['randomizeAnswers']) {
                    shuffle($answers);
                }
                if (isset($question['question_hint']) && strlen($question['question_hint']) !== 0) {
                    $question_hint_arr = $options['questionsHint'];
                    $questions_hint_type = $options['questionsHint']['questionsHintType'];
                    $question_text_value = $options['questionsHint']['questionsHintValue'];
                    $questions_hint_button_value = $options['questionsHint']['questionsHintButtonValue'];

                    $questions_hint_content = "<i class='ays_fa ays_fa_info_circle ays_question_hint' aria-hidden='true'></i>";
                    $questions_hint_max_width_class = '';
                    switch ( $questions_hint_type ) {
                        case 'text':
                            if ($question_text_value != '') {
                                $questions_hint_content = '<p class="ays_question_hint">'. $question_text_value .'</p>';
                            }
                            break;
                        case 'button':
                            if ($questions_hint_button_value != '') {
                                $questions_hint_max_width_class = 'ays_questions_hint_max_width_class';

                                $questions_hint_content = '<button class="ays_question_hint action-button ays_question_hint_button_type">'. $questions_hint_button_value .'</button>';
                            }
                            break;
                        case 'hide':
                            $questions_hint_content = '';
                            break;
                        case 'default':
                        default:
                            $questions_hint_content = "<i class='ays_fa ays_fa_info_circle ays_question_hint' aria-hidden='true'></i>";
                            break;
                    }

                    $question_hint = $this->ays_autoembed($question['question_hint']);
                    $question_hint = "
                    <div class='ays_question_hint_container ". $questions_hint_max_width_class ."'>
                        ".$questions_hint_content."
                        <span class='ays_question_hint_text'>" . $question_hint . "</span>
                    </div>";

                    if ( $questions_hint_type == "hide" ) {
                        $question_hint = "";
                    }
                }
                if(isset($question['user_explanation']) && $question['user_explanation'] == 'on'){
                    $user_explanation = "<div class='ays_user_explanation'>
                        <textarea placeholder='".__('You can enter your answer explanation',$this->plugin_name)."' class='ays_user_explanation_text' name='user-answer-explanation[{$id}]'></textarea>
                    </div>";
                }

                if($question['wrong_answer_text'] == ''){
                    $wrong_answer_class = 'ays_do_not_show';
                }else{
                    $wrong_answer_class = '';
                }
                if($question['right_answer_text'] == ''){
                    $right_answer_class = 'ays_do_not_show';
                }else{
                    $right_answer_class = '';
                }

                // Note text
                $quiz_question_note_message = ( isset( $question_options['quiz_question_note_message']) && $question_options['quiz_question_note_message'] != '' ) ? stripslashes( $question_options['quiz_question_note_message'] ) : '';

                $quiz_question_note_message_html = '';
                if ( $quiz_question_note_message != '' ) {
                    $quiz_question_note_message_html .= '<div class="ays-quiz-question-note-message-box">';
                        $quiz_question_note_message_html .= $this->ays_autoembed($quiz_question_note_message);
                    $quiz_question_note_message_html .= '</div>';
                }
                //@Shan
                $questions_score = '';
                if(isset($options['questions_score']) && $options['questions_score'] == 'on'){
                    $questions_score = "<p style='position:absolute; top:10px; left:5px;' class='ays-question-score'> Score: <span style='color:#41b70b;font-weight:bold;' class='ays-question-score-number'>0</span></p>";
                }
                $questions_counter = $questions_score;
                if($options['questionsCounter']){
                    $questions_counter .= "<p class='ays-question-counter animated'>{$current} / {$total}</p>";
                }else{
                    $questions_counter .= "";
                }
                
                $early_finish = "";                
                if($buttons['earlyButton']){
                    $early_finish = "<i class='" . ($enable_arrows ? '' : 'ays_display_none'). " ays_fa ays_fa_flag_checkered ays_early_finish action-button ays_arrow'></i><input type='button' class='" . ($enable_arrows ? 'ays_display_none' : '') . " ays_early_finish action-button' value='" . $settings_buttons_texts['finishButton'] . "'/>";
                }
                
                $clear_answer = "";                
                if($buttons['clearAnswerButton']){
                    $clear_answer = "<i class='" . ($enable_arrows ? '' : 'ays_display_none'). " ays_fa ays_fa_eraser ays_clear_answer action-button ays_arrow'></i><input type='button' class='" . ($enable_arrows ? 'ays_display_none' : '') . " ays_clear_answer action-button' value='" . $settings_buttons_texts['clearButton'] . "'/>";
                }
                if($options['correction']){
                    $clear_answer = "";
                }
                
                switch( $quiz_arrow_type ){
                    case 'default':
                        $quiz_arrow_type_class_right = "ays_fa_arrow_right";
                        $quiz_arrow_type_class_left = "ays_fa_arrow_left";
                        break;
                    case 'long_arrow':
                        $quiz_arrow_type_class_right = "ays_fa_long_arrow_right";
                        $quiz_arrow_type_class_left = "ays_fa_long_arrow_left";
                        break;
                    case 'arrow_circle_o':
                        $quiz_arrow_type_class_right = "ays_fa_arrow_circle_o_right";
                        $quiz_arrow_type_class_left = "ays_fa_arrow_circle_o_left";
                        break;
                    case 'arrow_circle':
                        $quiz_arrow_type_class_right = "ays_fa_arrow_circle_right";
                        $quiz_arrow_type_class_left = "ays_fa_arrow_circle_left";
                        break;
                    default:
                        $quiz_arrow_type_class_right = "ays_fa_arrow_right";
                        $quiz_arrow_type_class_left = "ays_fa_arrow_left";
                        break;
                }

                if ($last) {
                    switch($options['informationForm']){
                        case "disable":
                            $input = "<i class='" . $buttons['nextArrow'] . " ays_fa ays_fa_flag_checkered ays_finish action-button ays_arrow ays_next_arrow'></i><input type='submit' name='ays_finish_quiz' class=' " . $buttons['nextButton'] . " ays_next ays_finish action-button' value='" . $settings_buttons_texts['seeResultButton'] . "'/>";
                            break;
                        case "before":
                            $input = "<i class='" . $buttons['nextArrow'] . " ays_fa ays_fa_flag_checkered ays_finish action-button ays_arrow ays_next_arrow'></i><input type='submit' name='ays_finish_quiz' class=' " . $buttons['nextButton'] . " ays_next ays_finish action-button' value='" . $settings_buttons_texts['seeResultButton'] . "'/>";
                            break;
                        case "after":
                            $input = "<i class='" . $buttons['nextArrow'] . " ays_fa ". $quiz_arrow_type_class_right ." ays_finish action-button ays_arrow ays_next_arrow'></i><input type='button' class=' " . $buttons['nextButton'] . " ays_next action-button' value='" . $settings_buttons_texts['finishButton'] . "' />";
                            break;
                        default:
                            $input = "<i class='" . $buttons['nextArrow'] . " ays_fa ays_fa_flag_checkered ays_finish action-button ays_arrow ays_next_arrow'></i><input type='submit' name='ays_finish_quiz' class=' " . $buttons['nextButton'] . " ays_next ays_finish action-button' value='" . $settings_buttons_texts['seeResultButton'] . "'/>";
                            break;                        
                    }
                    $buttons_div = "<div class='ays_buttons_div'>
                            {$clear_answer}
                            <i class=\"ays_fa ". $quiz_arrow_type_class_left ." ays_previous action-button ays_arrow " . $buttons['prevArrow'] . "\"></i>
                            <input type='button' class='ays_previous action-button " . $buttons['prevButton'] . "'  value='".$settings_buttons_texts['previousButton']."' />
                            {$input}
                        </div>";
                }else{
                    $buttons_div = "<div class='ays_buttons_div'>
                        {$clear_answer}
                        <i class=\"ays_fa ". $quiz_arrow_type_class_left ." ays_previous action-button ays_arrow " . $buttons['prevArrow'] . "\"></i>
                        <input type='button' class='ays_previous action-button " . $buttons['prevButton'] . "' value='".$settings_buttons_texts['previousButton']."' />
                        " . $early_finish . "
                        <i class=\"ays_fa ". $quiz_arrow_type_class_right ." ays_next action-button ays_arrow ays_next_arrow " . $buttons['nextArrow'] . "\"></i>
                        <input type='button' class='ays_next action-button " . $buttons['nextButton'] . "' value='" . $settings_buttons_texts['nextButton'] . "' />
                    </div>";
                }
                
                $additional_css = "";
                $answer_view_class = $options['answersViewClass'];

                $show_questions_numbering = $options['show_questions_numbering'];
                $question_numering_type = $this->ays_question_numbering( $show_questions_numbering, $total );

                $question_title = $question['question'];
                $question_numering_value = "";
                if( isset( $question_numering_type[$key] ) && $question_numering_type[$key] != '' ){
                    $question_numering_value = $question_numering_type[$key] . " ";
                    $question_title = $question_numering_value . $question['question'];
                }
                
                $question_content = $this->ays_autoembed( $question_title );

                if ( $quiz_hide_question_text ) {
                    $question_content = '';
                }

                switch ($options['quizTheme']) {
                    case 'elegant_dark':
                    case 'elegant_light':
                    case 'rect_dark':
                    case 'rect_light':
                        $question_html = "<div class='ays_quiz_question'>
                                " . $question_content . "
                            </div>
                            {$question_image}";
                        $answer_view_class = "ays_".$answer_view_class."_view_container";
                        break;
                    default:
                        $question_html = "<div class='ays_quiz_question'>
                                " . $question_content . "
                            </div>
                            {$question_image}";
                        $answer_view_class = "ays_".$answer_view_class."_view_container";
                        break;
                }
                $not_influence_to_score_class = $not_influence_to_score ? 'not_influence_to_score' : '';
                $container_first_part = "<div class='step ".$not_influence_to_score_class."' data-question-id='" . $question["id"] . "' data-type='" . $question["type"] . "'>
                    {$question_hint}
                    {$questions_counter}
                    <div class='ays-abs-fs'>
                        {$question_category}
                        {$question_html}
                        <div class='ays-quiz-answers $answer_view_class $max_selection_number_class $min_selection_number_class'>";

                $required_question_message = '';
                if( $enable_min_selection_number ){
                    $required_question_message = '<div class="ays-quiz-question-validation-error" role="alert"></div>';
                }

                $ays_questtion_explanation_html = "";
                if ( $show_questions_explanation != "" && $show_questions_explanation != "disable") {
                    $ays_questtion_explanation_html = $this->ays_autoembed($question["explanation"]);
                }

                $wrong_answer_text_html = "";
                $right_answer_text_html = "";
                if ( $show_answers_rw_texts != "" && $show_answers_rw_texts != "disable") {
                    $wrong_answer_text_html = $this->ays_autoembed($question["wrong_answer_text"]);
                    $right_answer_text_html = $this->ays_autoembed($question["right_answer_text"]);
                }
                                            
                $container_last_part = "</div>                        
                        {$quiz_question_note_message_html}
                        {$user_explanation}
                        {$buttons_div}
                        {$required_question_message}
                        <div class='wrong_answer_text $wrong_answer_class' style='display:none'>
                            " . $wrong_answer_text_html . "
                        </div>
                        <div class='right_answer_text $right_answer_class' style='display:none'>
                            " . $right_answer_text_html . "
                        </div>
                        <div class='ays_questtion_explanation' style='display:none'>
                            " . $ays_questtion_explanation_html . "
                        </div>
                        {$additional_css}
                    </div>
                </div>";
                
                $container[] = array(
                    'quizID' => $quiz_id,
                    'questionID' => $question['id'],
                    'questionAnswers' => $answers,
                    'questionType' => $question["type"],
                    'enable_max_selection_number' => $enable_max_selection_number,
                    'max_selection_number' => $max_selection_number,
                    'enable_min_selection_number' => $enable_min_selection_number,
                    'min_selection_number' => $min_selection_number,
                    'enable_case_sensitive_text' => $enable_case_sensitive_text,
                    'questionParts' => array(
                        'question_first_part' => $container_first_part,
                        'question_middle_part' => "",
                        'question_last_part' => $container_last_part
                    )
                );
            }
        }
        return $container;
    }
    
    protected function get_answers_with_question_id($id){
        global $wpdb;

        $sql = "SELECT *
                FROM {$wpdb->prefix}aysquiz_answers
                WHERE question_id=" . $id;

        $answer = $wpdb->get_results($sql, 'ARRAY_A');

        return $answer;
    }

    public function get_quiz_questions_count($id){
        global $wpdb;

        $sql = "SELECT `question_ids`
                FROM {$wpdb->prefix}aysquiz_quizes
                WHERE id=" . $id;

        $questions_str = $wpdb->get_row($sql, 'ARRAY_A');
        $questions = explode(',', $questions_str['question_ids']);
        return $questions;
    }

    public function get_question_bank_categories($q_ids){
        global $wpdb;
        
        if($q_ids == ''){
            return array();
        }
        $sql = "SELECT DISTINCT c.id, c.title 
                FROM {$wpdb->prefix}aysquiz_categories c
                JOIN {$wpdb->prefix}aysquiz_questions q
                ON c.id = q.category_id
                WHERE q.id IN ({$q_ids})";

        $result = $wpdb->get_results($sql, 'ARRAY_A');
        $cats = array();
        
        foreach($result as $res){
            $cats[$res['id']] = $res['title'];
        }
        
        return $cats;
    }

    public static function get_quiz_tackers_count($id){
        global $wpdb;

        $sql = "SELECT COUNT(*)
                FROM {$wpdb->prefix}aysquiz_reports
                WHERE quiz_id=" . $id;

        $count = intval($wpdb->get_var($sql));

        return $count;
    }

    public function sort_array_keys_by_array($array, $orderArray) {
        $ordered = array();
        foreach ($orderArray as $key) {
            if (array_key_exists('ays-question-'.$key, $array)) {
                $ordered['ays-question-'.$key] = $array['ays-question-'.$key];
                unset($array['ays-question-'.$key]);
            }
        }
        return $ordered + $array;
    }

    public function ays_finish_quiz(){
        ob_start();
        $quiz_id = isset($_REQUEST['ays_quiz_id']) ? absint( sanitize_text_field( $_REQUEST['ays_quiz_id'] ) ) : 0;

        if($quiz_id === 0){            
            ob_end_clean();
            $ob_get_clean = ob_get_clean();
            echo json_encode(array("status" => false, "message" => "No no no" ));
            wp_die();
        } else {
            global $wpdb;

            // $quiz_id = absint(intval($_REQUEST['ays_quiz_id']));
            $questions_answers = (isset($_REQUEST["ays_questions"])) ? Quiz_Maker_Admin::recursive_sanitize_text_field( $_REQUEST['ays_questions'] ) : array();

            $questions_ids = preg_split('/,/', sanitize_text_field( $_REQUEST['ays_quiz_questions'] ) );
            $questions_answers = $this->sort_array_keys_by_array($questions_answers, $questions_ids);

            $quiz = $this->get_quiz_by_id($quiz_id);
            $quiz_intervals_data = (isset( $quiz['intervals'] ) && $quiz['intervals'] != "") ? $quiz['intervals'] : "";
            $quiz_intervals = array();
            if ( $quiz_intervals_data != "" ) {
                $quiz_intervals = json_decode($quiz_intervals_data);
            }
            $options = json_decode($quiz['options']);
            $quiz_questions_count = $this->get_quiz_questions_count($quiz_id);

            if (isset($options->enable_question_bank) && $options->enable_question_bank == 'on' && isset($options->questions_count) && intval($options->questions_count) > 0 && count($quiz_questions_count) > intval($options->questions_count)) {
                $question_ids = preg_split('/,/', sanitize_text_field( $_REQUEST['ays_quiz_questions'] ) );
            } else {
                $question_ids = $this->get_quiz_questions_count($quiz_id);
            }
            // Strong calculation of checkbox answers
            $options->checkbox_score_by = ! isset($options->checkbox_score_by) ? 'on' : $options->checkbox_score_by;
            $strong_count_checkbox = (isset($options->checkbox_score_by) && $options->checkbox_score_by == "on") ? true : false;
            
            // Calculate the score
            $options->calculate_score = ! isset($options->calculate_score) ? 'by_correctness' : $options->calculate_score;
            $calculate_score = (isset($options->calculate_score) && $options->calculate_score != "") ? $options->calculate_score : 'by_correctness';

            // Disable store data 
            $options->disable_store_data = ! isset( $options->disable_store_data ) ? 'off' : $options->disable_store_data;
            $disable_store_data = (isset($options->disable_store_data) && $options->disable_store_data == 'off') ? true : false;

            // Display score option
            $display_score = (isset($options->display_score) && $options->display_score != "") ? $options->display_score : 'by_percantage';

            //Pass score count
            $pass_score_count = (isset($options->pass_score) && $options->pass_score != '') ? absint(intval($options->pass_score)) : 0;

            // Information form
            $information_form = (isset($options->information_form) && $options->information_form != '') ? $options->information_form : 'disable';

            // Show information form to logged in users
            $options->show_information_form = isset($options->show_information_form) ? $options->show_information_form : 'on';
            $show_information_form = (isset($options->show_information_form) && $options->show_information_form == 'on') ? true : false;

            // Pass Score Text
            $pass_score_message = '';
            if(isset($options->pass_score_message) && $options->pass_score_message != ''){
                $pass_score_message = $this->ays_autoembed($options->pass_score_message);
            }else{
                $pass_score_message = '<h4 style="text-align: center;">'. __("Congratulations!", $this->plugin_name) .'</h4><p style="text-align: center;">'. __("You passed the quiz!", $this->plugin_name) .'</p>';
            }

            // Fail Score Text
            $fail_score_message = '';
            if(isset($options->fail_score_message) && $options->fail_score_message != ''){
                $fail_score_message = $this->ays_autoembed($options->fail_score_message);
            }else{
                $fail_score_message = '<h4 style="text-align: center;">'. __("Oops!", $this->plugin_name) .'</h4><p style="text-align: center;">'. __("You have not passed the quiz! <br> Try again!", $this->plugin_name) .'</p>';
            }

            if(! $show_information_form){
                if($information_form !== 'disable'){
                    $user = wp_get_current_user();
                    if($user->ID != 0){
                        $_REQUEST['ays_user_email'] = $user->data->user_email;
                        $_REQUEST['ays_user_name'] = $user->data->display_name;
                    }
                }
            }

            // Quiz create date
            $quiz_creation_date = (isset($options->create_date) && $options->create_date != '') ? sanitize_text_field( $options->create_date ) : "";

            // Quiz Author ID
            if ( isset( $options->author ) && is_string($options->author) ) {
                $quiz_current_author_data = (isset( $options->author ) && $options->author != '') ? json_decode($options->author, true) : array();
            } else {
                $options_author = isset( $options->author ) ? (array)$options->author : array();
                $quiz_current_author_data = (is_array( $options_author ) && empty( $options_author )) ? $options_author : array();
            }

            // General Setting's Options
            $quiz_settings = $this->settings;
            $general_settings_options = ($quiz_settings->ays_get_setting('options') === false) ? json_encode(array()) : $quiz_settings->ays_get_setting('options');
            $settings_options = json_decode(stripcslashes($general_settings_options), true);

            // Do not store IP adressess 
            $disable_user_ip = (isset($settings_options['disable_user_ip']) && $settings_options['disable_user_ip'] == 'on') ? true : false;

            // Limit user
            $options->limit_users = isset($options->limit_users) ? $options->limit_users : 'off';
            $limit_users = (isset($options->limit_users) && $options->limit_users == 'on') ? true : false;

            // Limit user by
            $limit_users_by = (isset($options->limit_users_by) && $options->limit_users_by != '') ? $options->limit_users_by : 'ip';

            // Quiz Title
            $quiz_title = (isset($quiz['title']) && $quiz['title'] != '') ? stripslashes( $quiz['title'] ) : '';

            $limit_users_attr = array(
                'id' => $quiz_id,
                'name' => 'ays_quiz_cookie_',
                'title' => $quiz_title,
            );
            $check_cookie = $this->ays_quiz_check_cookie( $limit_users_attr );
            $return_false_status_arr = array(
                "status" => false,
                "flag" => false,
                "text" => __( 'You have already passed this quiz.', $this->plugin_name ),
            );

            if ( $check_cookie ) {
                echo json_encode( $return_false_status_arr );
                wp_die();
            }
            if ( $limit_users ) {
                switch ( $limit_users_by ) {
                    case 'ip':
                        break;
                    case 'user_id':
                        break;
                    case 'cookie':
                        if ( ! $check_cookie ) {
                            $set_cookie = $this->ays_quiz_set_cookie( $limit_users_attr );
                        }
                        break;
                    case 'ip_cookie':
                        $check_user_by_ip = $this->get_user_by_ip($quiz_id);
                        if ( ! $check_cookie || $check_user_by_ip <= 0 ) {
                            if ( ! $check_cookie ) {
                                $set_cookie = $this->ays_quiz_set_cookie( $limit_users_attr );
                            }
                        } else {
                            echo json_encode( $return_false_status_arr );
                            wp_die();
                        }
                        break;
                    default:
                        break;
                }
            }

            $questions_count = count($question_ids);
            $correctness = array();
            $user_answered = array();
            $correctness_results = array();
            $answer_max_weights = array();
            if (is_array($questions_answers)) {
                $quests = array();
                $questions_cats = array();
                $quiz_questions_ids = array();
                $question_bank_by_categories1 = array();

                foreach($questions_answers as $key => $val){
                    $question_id = explode('-', $key)[2];
                    $quiz_questions_ids[] = strval($question_id);
                }

                $questions_categories = $this->get_questions_categories( implode( ',', $quiz_questions_ids ) );
                $quest_s = $this->get_quiz_questions_by_ids($quiz_questions_ids);
                foreach($quest_s as $quest){
                    $quests[$quest['id']] = $quest;
                }

                foreach($quiz_questions_ids as $key => $question_id){
                    $questions_cats[$quests[$question_id]['category_id']][$question_id] = null;
                }
                foreach ($questions_answers as $key => $questions_answer) {
                    $continue = false;
                    $question_id = explode('-', $key)[2];
                    if($this->is_question_not_influence($question_id)){
                        $questions_count--;
                        $continue = true;
                    }
                    $multiple_correctness = array();
                    $has_multiple = $this->has_multiple_correct_answers($question_id);
                    $answer_max_weights[] = $this->get_answers_max_weight($question_id, $has_multiple);
                    
                    $user_answered["question_id_" . $question_id] = $questions_answer;
                    if ($has_multiple) {                        
                        if (is_array($questions_answer)) {
                            foreach ($questions_answer as $answer_id) {
                                $multiple_correctness[] = $this->check_answer_correctness($question_id, $answer_id, $calculate_score);
                            }
                            
                            if($calculate_score == 'by_points'){
                                if(!$continue){
                                    $correctness[$question_id] = array_sum($multiple_correctness);
                                }
                                $correctness_results["question_id_" . $question_id] = array_sum($multiple_correctness);
                                continue;
                            }
                            
                            if($strong_count_checkbox === false){
                                if(!$continue){
                                    $correctness[$question_id] = $this->isHomogenousStrong($multiple_correctness, $question_id);
                                }
                                $correctness_results["question_id_" . $question_id] = $this->isHomogenousStrong($multiple_correctness, $question_id);
                            }else{
                                if ($this->isHomogenous($multiple_correctness, $question_id)) {
                                    if(!$continue){
                                        $correctness[$question_id] = true;
                                    }
                                    $correctness_results["question_id_" . $question_id] = true;
                                } else {
                                    if(!$continue){
                                        $correctness[$question_id] = false;
                                    }
                                    $correctness_results["question_id_" . $question_id] = false;
                                }
                            }
                        } else {
                            if($calculate_score == 'by_points'){
                                if(!$continue){
                                    $correctness[$question_id] = $this->check_answer_correctness($question_id, $questions_answer, $calculate_score);
                                }
                                $correctness_results["question_id_" . $question_id] = $this->check_answer_correctness($question_id, $questions_answer, $calculate_score);
                                continue;
                            }
                            if($strong_count_checkbox === false){
                                if($this->check_answer_correctness($question_id, $questions_answer, $calculate_score)){
                                    if(!$continue){
                                        $correctness[$question_id] = 1 / intval($this->count_multiple_correct_answers($question_id));
                                    }
                                }else{
                                    if(!$continue){
                                        $correctness[$question_id] = false;
                                    }
                                }
                                $correctness_results["question_id_" . $question_id] = $this->check_answer_correctness($question_id, $questions_answer, $calculate_score);
                            }else{
                                if(!$continue){
                                    $correctness[$question_id] = false;
                                }
                                $correctness_results["question_id_" . $question_id] = false;
                            }
                        }
                    } elseif($this->has_text_answer($question_id)) {
                        $quests_data = ( isset( $quests[$question_id] ) && ! empty( $quests[$question_id] ) ) ? $quests[$question_id] : array();
                        $quests_data_options = isset( $quests_data['options'] ) ? json_decode( $quests_data['options'], true ) : array(); 
                        if(!$continue){
                            $correctness[$question_id] = $this->check_text_answer_correctness($question_id, $questions_answer, $calculate_score, $quests_data_options);
                        }
                        $correctness_results["question_id_" . $question_id] = $this->check_text_answer_correctness($question_id, $questions_answer, $calculate_score, $quests_data_options);
                    } else {
                        if(!$continue){
                            $correctness[$question_id] = $this->check_answer_correctness($question_id, $questions_answer, $calculate_score);
                        }
                        $correctness_results["question_id_" . $question_id] = $this->check_answer_correctness($question_id, $questions_answer, $calculate_score);
                    }
                }
                
                $new_correctness = array();
                $quiz_weight = array();
                $corrects_count = 0;
                $quiz_weight_correctness = array();
                $corrects_count_by_cats = array();
                foreach($questions_cats as $cat_id => &$q_ids){
                    $corrects_count_by_cats[$cat_id] = 0;
                    foreach($correctness as $question_id => $item){
                        if( array_key_exists( strval($question_id), $q_ids ) ){
                            switch($calculate_score){
                                case "by_correctness":
                                    if($item){
                                        $corrects_count_by_cats[$cat_id]++;
                                    }
                                break;
                                default:
                                    if($item){
                                        $corrects_count_by_cats[$cat_id]++;
                                    }
                                break;
                            }
                        }
                    }
                }


                foreach($correctness as $question_id => $item){
                    $question_weight = $this->get_question_weight($question_id);
                    $new_correctness[strval($question_id)] = $question_weight * floatval($item);
                    $quiz_weight[] = $question_weight;
                    $quiz_weight_correctness[strval($question_id)] = $question_weight;
                    switch($calculate_score){
                        case "by_correctness":
                            if($item){
                                $corrects_count++;
                            }
                        break;
                        default:
                            if($item){
                                $corrects_count++;
                            }
                        break;
                    }
                }


                $quiz_weight_new_correctness_by_cats = array();
                $quiz_weight_correctness_by_cats = array();

                $questions_count_by_cats = array();
                foreach($questions_cats as $cat_id => &$q_ids){
                    foreach($q_ids as $q_id => &$val){
                        $val = array_key_exists($q_id, $new_correctness) ? $new_correctness[$q_id] : false;
                        $quiz_weight_new_correctness_by_cats[$cat_id][$q_id] = $val;
                        if( $this->is_question_not_influence($q_id) ){
                            continue;
                        }

                        if ( isset( $quiz_weight_correctness[$q_id] ) && sanitize_text_field( $quiz_weight_correctness[$q_id] ) != '' ) {
                            $quiz_weight_correctness_by_cats[$cat_id][$q_id] = $quiz_weight_correctness[$q_id];
                        }
                    }
                    $questions_count_by_cats[$cat_id] = count($q_ids);
                }

                $final_score_by_cats = array();
                $quiz_weight_cats = array();
                $correct_answered_count_cats = array();
                foreach($quiz_weight_new_correctness_by_cats as $cat_id => $q_ids){

                    if ( ! isset( $quiz_weight_correctness_by_cats[$cat_id] ) ) {
                        continue;
                    }
                    $quiz_weight_correctness_by_cats[$cat_id] = array_filter($quiz_weight_correctness_by_cats[$cat_id], "strlen");

                    switch($calculate_score){
                        case "by_correctness":
                            $quiz_weight_cat = array_sum($quiz_weight_correctness_by_cats[$cat_id]);
                            $quiz_weight_cats[$cat_id] = array_sum($quiz_weight_correctness_by_cats[$cat_id]);
                        break;
                        default:
                            $quiz_weight_cat = array_sum($quiz_weight_correctness_by_cats[$cat_id]);
                            $quiz_weight_cats[$cat_id] = array_sum($quiz_weight_correctness_by_cats[$cat_id]);
                        break;
                    }

                    $correct_answered_count_cat = array_sum($q_ids);

                    if($quiz_weight_cat == 0){
                        $final_score_by_cats[$cat_id] = floatval(0);
                    }else{
                        $final_score_by_cats[$cat_id] = floatval(floor(($correct_answered_count_cat / $quiz_weight_cat) * 100));
                    }
                }
//                $average_percent = 100 / $questions_count;
                
                switch($calculate_score){
                    case "by_correctness":
                        $quiz_weight = array_sum($quiz_weight);
                    break;
                    case "by_points":
                        $quiz_weight = array_sum($answer_max_weights);
                    break;
                    default:
                        $quiz_weight = array_sum($quiz_weight);
                    break;
                }
                $correct_answered_count = array_sum($new_correctness);
                
                if( $quiz_weight > 0 ){
                    $final_score = intval(floor(($correct_answered_count / $quiz_weight) * 100));
                }else{
                    $final_score = 0;
                }

                $score_by_cats = array();
                foreach($final_score_by_cats as $cat_id => $cat_score){
                    switch($display_score){
                        case "by_correctness":
                            $score_by_cats[$cat_id] = array(
                                'score' => $corrects_count_by_cats[$cat_id] . " / " . $questions_count_by_cats[$cat_id],
                                'categoryName' => $questions_categories[$cat_id],
                            );
                        break;
                        case "by_percentage":
                            $score_by_cats[$cat_id] = array(
                                'score' => $cat_score . "%",
                                'categoryName' => $questions_categories[$cat_id],
                            );
                        break;
                        default:
                            $score_by_cats[$cat_id] = array(
                                'score' => $cat_score . "%",
                                'categoryName' => $questions_categories[$cat_id],
                            );
                        break;
                    }
                }

                if(empty($score_by_cats)){
                    $result_score_by_categories = '';
                }else{
                    $result_score_by_categories = '<div class="ays_result_by_cats">';
                    foreach($score_by_cats as $cat_id => $cat){
                        $result_score_by_categories .= '<p class="ays_result_by_cat">
                            <strong class="ays_result_by_cat_name">'. $cat['categoryName'] .':</strong>
                            <span class="ays_result_by_cat_score">'. $cat['score'] .'</span>
                        </p>';
                    }
                    $result_score_by_categories .= '</div>';
                    $result_score_by_categories = str_replace(array("\r\n", "\n", "\r"), "", $result_score_by_categories);
                } 

                switch($display_score){
                    case "by_correctness":
                        $score = $corrects_count . " / " . $questions_count;
                    break;
                    case "by_percentage":
                        $score = $final_score . "%";
                    break;
                    default:
                        $score = $final_score . "%";
                    break;
                }

                $wrong_answered_count = $questions_count - $corrects_count;

                $skipped_questions_count = 0;
                foreach ($user_answered as $q_id => $user_answered_val) {
                    $question_id_val = explode('_', $q_id)[2];
                    if($this->is_question_not_influence($question_id_val)){
                        continue;
                    }

                    if ( $user_answered_val == '') {
                        $skipped_questions_count++;
                    }
                }

                $only_wrong_answers_count = $questions_count - ( $corrects_count + $skipped_questions_count );

                $answered_questions_count = $questions_count - $skipped_questions_count;
                $user_failed_questions_count = $corrects_count + ( $questions_count - ($corrects_count + $skipped_questions_count) );

                if ( ! empty( $user_failed_questions_count ) || $user_failed_questions_count != 0) {
                    $score_by_answered_questions = round( ( $corrects_count * 100 ) / $user_failed_questions_count , 1 );
                } else {
                    $score_by_answered_questions = 0;
                }

                $user_first_name      = '';
                $user_last_name       = '';
                $user_nickname        = '';
                $user_display_name    = '';
                $user_wordpress_email = '';
                $user_wordpress_roles = '';
                $user_id = get_current_user_id();
                if($user_id != 0){
                    $usermeta = get_user_meta( $user_id );
                    if($usermeta !== null){
                        $user_first_name = (isset($usermeta['first_name'][0]) && sanitize_text_field( $usermeta['first_name'][0] != '') ) ? sanitize_text_field( $usermeta['first_name'][0] ) : '';
                        $user_last_name  = (isset($usermeta['last_name'][0]) && sanitize_text_field( $usermeta['last_name'][0] != '') ) ? sanitize_text_field( $usermeta['last_name'][0] ) : '';
                        $user_nickname   = (isset($usermeta['nickname'][0]) && sanitize_text_field( $usermeta['nickname'][0] != '') ) ? sanitize_text_field( $usermeta['nickname'][0] ) : '';
                    }

                    $current_user_data = get_userdata( $user_id );
                    if ( ! is_null( $current_user_data ) && $current_user_data ) {
                        $user_display_name    = ( isset( $current_user_data->data->display_name ) && $current_user_data->data->display_name != '' ) ? sanitize_text_field( $current_user_data->data->display_name ) : "";
                        $user_wordpress_email = ( isset( $current_user_data->data->user_email ) && $current_user_data->data->user_email != '' ) ? sanitize_text_field( $current_user_data->data->user_email ) : "";

                        $user_wordpress_roles = ( isset( $current_user_data->roles ) && ! empty( $current_user_data->roles ) ) ? $current_user_data->roles : "";

                        if ( !empty( $user_wordpress_roles ) && $user_wordpress_roles != "" ) {
                            if ( is_array( $user_wordpress_roles ) ) {
                                $user_wordpress_roles = implode(",", $user_wordpress_roles);
                            }
                        }
                    }
                }

                $current_quiz_author = __( "Unknown", $this->plugin_name );
                $current_quiz_author_email = "";
                $super_admin_email  = "";
                $wp_all_admins = get_users('role=Administrator');
                if(!empty($wp_all_admins)){
                    $super_admin_email = isset($wp_all_admins[0]) ? $wp_all_admins[0]->data->user_email : '';
                }
                if( !empty($quiz_current_author_data) ){
                    if( !is_array($quiz_current_author_data) ){
                        $quiz_current_author_data = json_decode($quiz_current_author_data, true);
                    }

                    $quiz_current_author = (isset($quiz_current_author_data['id']) && $quiz_current_author_data['id'] != "") ? absint(sanitize_text_field( $quiz_current_author_data['id'] )) : "";

                    $current_quiz_user_data = get_userdata( $quiz_current_author );
                    if ( ! is_null( $current_quiz_user_data ) && $current_quiz_user_data ) {
                        $current_quiz_author = ( isset( $current_quiz_user_data->data->display_name ) && $current_quiz_user_data->data->display_name != '' ) ? sanitize_text_field( $current_quiz_user_data->data->display_name ) : "";
                        $current_quiz_author_email = ( isset( $current_quiz_user_data->data->user_email ) && $current_quiz_user_data->data->user_email != '' ) ? sanitize_text_field( $current_quiz_user_data->data->user_email ) : "";
                    }
                }

                // $correct_answered_count = array_sum($correctness);

                // $final_score = floor(($average_percent * $correct_answered_count));

                if($disable_user_ip){
                    $user_ip = '';
                }else{
                    $user_ip = $this->get_user_ip();
                }

                $current_user_ip = $user_ip;
                
                $correctness_and_answers = array(
                    'correctness' => $correctness_results,
                    'user_answered' => $user_answered
                );
                $ays_user_name = isset( $_REQUEST['ays_user_name'] ) && $_REQUEST['ays_user_name'] != '' ? esc_sql( sanitize_text_field( $_REQUEST['ays_user_name'] ) ) : '';
                $ays_user_email = isset( $_REQUEST['ays_user_email'] ) && $_REQUEST['ays_user_email'] != '' ? esc_sql( sanitize_email( $_REQUEST['ays_user_email'] ) ) : '';
                $ays_user_phone = isset( $_REQUEST['ays_user_phone'] ) && $_REQUEST['ays_user_phone'] != '' ? esc_sql( sanitize_text_field( $_REQUEST['ays_user_phone'] ) ) : '';
                $start_date = isset( $_REQUEST['start_date'] ) && $_REQUEST['start_date'] != '' ? sanitize_text_field( $_REQUEST['start_date'] ) : current_time( 'mysql' );
                $end_date = isset( $_REQUEST['end_date'] ) && $_REQUEST['end_date'] != '' ? sanitize_text_field( $_REQUEST['end_date'] ) : current_time( 'mysql' );

                $quiz_curent_page_link = isset( $_REQUEST['ays_quiz_curent_page_link'] ) && $_REQUEST['ays_quiz_curent_page_link'] != '' ? sanitize_url( $_REQUEST['ays_quiz_curent_page_link'] ) : "";

                $quiz_current_page_link_html = "<a href='". esc_sql( $quiz_curent_page_link ) ."' target='_blank' class='ays-quiz-curent-page-link-a-tag'>". __( "Quiz link", $this->plugin_name ) ."</a>";
                
                $message_data = array(
                    'quiz_name'                     => stripslashes($quiz['title']),
                    'user_name'                     => $ays_user_name,
                    'user_email'                    => $ays_user_email,
                    'score'                         => $final_score . "%",
                    'current_date'                  => date_i18n( get_option( 'date_format' ), strtotime( $end_date ) ),
                    'results_by_cats'               => $result_score_by_categories,
                        
                    'avg_score'                     => $this->ays_get_average_of_scores($quiz_id) . "%",
                    'avg_rate'                      => round($this->ays_get_average_of_rates($quiz_id), 1),
                    'user_pass_time'                => $this->get_time_difference( $start_date, $end_date ),
                    'quiz_time'                     => $this->secondsToWords($options->timer),
                    'user_corrects_count'           => $corrects_count,
                    'wrong_answers_count'           => $wrong_answered_count,
                    'skipped_questions_count'       => $skipped_questions_count,
                    'answered_questions_count'      => $answered_questions_count,
                    'score_by_answered_questions'   => $score_by_answered_questions,
                    'user_first_name'               => $user_first_name,
                    'user_last_name'                => $user_last_name,
                    'questions_count'               => $questions_count,
                    'only_wrong_answers_count'      => $only_wrong_answers_count,
                    'user_nickname'                 => $user_nickname,
                    'user_display_name'             => $user_display_name,
                    'user_wordpress_email'          => $user_wordpress_email,
                    'user_wordpress_roles'          => $user_wordpress_roles,
                    'quiz_creation_date'            => date_i18n( get_option( 'date_format' ), strtotime( $quiz_creation_date ) ),
                    'current_quiz_author'           => $current_quiz_author,
                    'current_quiz_page_link'        => $quiz_current_page_link_html,
                    'current_user_ip'               => $current_user_ip,
                    'current_quiz_author_email'     => $current_quiz_author_email,
                    'admin_email'                   => $super_admin_email,
                );

                $data = array(
                    'user_ip'      => $user_ip,
                    'user_name'    => $ays_user_name,
                    'user_email'   => $ays_user_email,
                    'user_phone'   => $ays_user_phone,
                    'start_date'   => esc_sql( $start_date ),
                    'end_date'     => esc_sql( $end_date ),
                    'answered'     => $correctness_and_answers,
                    'score'        => $final_score,
                    'calc_method'  => $calculate_score,
                    'quiz_id'      => $quiz_id
                );

                // Disabling store data in DB
                if($disable_store_data){
                    $result = $this->add_results_to_db($data);
                }else{
                    $result = true;
                }

                $last_result_id = $wpdb->insert_id;

                $message_data['avg_score_by_category'] = $this->ays_get_average_score_by_category($quiz_id);

                if ($final_score >= $pass_score_count) {
                    $score_message = $pass_score_message;
                }else{
                    $score_message = $fail_score_message;
                }

                $final_score_message = "";
                if($pass_score_count > 0){
                    $final_score_message = $this->replace_message_variables($score_message, $message_data);
                }

                $result_text = '';
                if(isset($options->result_text) && $options->result_text != ''){
                    $result_text = $this->ays_autoembed($options->result_text);
                }

                $result_text = $this->replace_message_variables($result_text, $message_data);
                
                ob_end_clean();
                $ob_get_clean = ob_get_clean();
                if ($result) {
                    echo json_encode(array(
                        "status"        => true,
                        "score"         => $score,
                        "scoreMessage"  => $final_score_message,
                        "displayScore"  => $display_score,
                        "text"          => $result_text,
                        "result_id"     => $last_result_id,
                    ));
                    wp_die();                
                }else{
                    echo json_encode(array(
                        "status" => false, 
                        "text"   => "No no no"
                    ));
                    wp_die();
                }

            } else {
                $admin_mails = get_option('admin_email');
                
                ob_end_clean();
                $ob_get_clean = ob_get_clean();
                
                echo json_encode(array(
                    "status"     => false, 
                    "text"       => "No no no", 
                    "admin_mail" => $admin_mails 
                ));
                wp_die();
            }
        }
    }

    public function replace_message_variables($content, $data){
        foreach($data as $variable => $value){
            $content = str_replace("%%".$variable."%%", $value, $content);
        }
        return $content;
    }
    
    public function get_answers_max_weight($question_id, $has_multiple){
        global $wpdb;
        //$answers_table = $wpdb->prefix . "aysquiz_answers";
        //$question_id = absint(intval($question_id));
        //$query_part = "MAX(weight)";
        //if($has_multiple){
        //    $query_part = "SUM(weight)";
        //}
        //$sql = "SELECT {$query_part} FROM {$answers_table} WHERE question_id={$question_id}";
        //$checks = $wpdb->get_var($sql);
        //$answer_weight = floatval($checks);
        
        return 0;
    }

    public function check_answer_correctness($question_id, $answer_id, $calc_method){
        global $wpdb;
        $answers_table = $wpdb->prefix . "aysquiz_answers";
        $question_id = absint(intval($question_id));
        $answer_id = absint(intval($answer_id));
        $checks = $wpdb->get_row("SELECT * FROM {$answers_table} WHERE question_id={$question_id} AND id={$answer_id}", "ARRAY_A");
        if( empty( $checks ) ){
            return false;
        }
        $answer = false;
        switch($calc_method){
            case "by_correctness":
                if (absint(intval($checks["correct"])) == 1)
                    $answer = true;
                else
                    $answer = false;
            break;
            case "by_points":
                $answer_weight = isset($checks['weight']) ? floatval($checks['weight']) : 0;
                $answer = $answer_weight;
            break;
            default:
                if (absint(intval($checks["correct"])) == 1)
                    $answer = true;
                else
                    $answer = false;
            break;
        }
        return $answer;
    }

    public function check_text_answer_correctness($question_id, $answer, $calc_method, $options = array()){
        global $wpdb;
        $answers_table = $wpdb->prefix . "aysquiz_answers";
        $question_id = absint(intval($question_id));
        $checks = $wpdb->get_row("SELECT COUNT(*) AS qanak, answer, weight FROM {$answers_table} WHERE question_id={$question_id}", ARRAY_A);

        $checks['answer'] = (isset( $checks['answer'] ) && $checks['answer'] != "") ? $checks['answer'] : "";

        $correct_answers = mb_strtolower($checks['answer']);
        $answer = stripslashes($answer);
        $correct = false;
        $text_type = $this->text_answer_is($question_id);

        // Enable case sensitive text
        $options['enable_case_sensitive_text'] = isset($options['enable_case_sensitive_text']) ? sanitize_text_field( $options['enable_case_sensitive_text'] ) : 'off';
        $enable_case_sensitive_text = (isset($options['enable_case_sensitive_text']) && sanitize_text_field( $options['enable_case_sensitive_text'] ) == 'on') ? true : false;

        if( $text_type == 'text' || $text_type == 'short_text' ){
            if ( $enable_case_sensitive_text ) {
                $correct_answers = $checks['answer'];
            }
        }

        if($text_type == 'date'){
            // if(Quiz_Maker_Admin::validateDate($answer, 'Y-m-d')){
                if(date('Y-m-d', strtotime($correct_answers)) == date('Y-m-d', strtotime($answer))){
                    $correct = true;
                }
            // }
        }elseif($text_type != 'number'){
            $correct_answers = explode('%%%', $correct_answers);
            foreach($correct_answers as $c){
                if ($enable_case_sensitive_text) {
                    if(trim($c) == trim($answer)){
                        $correct = true;
                        break;
                    }
                } else {
                    if(trim($c) == mb_strtolower(trim($answer))){
                        $correct = true;
                        break;
                    }
                }
            }
        }else{
            if($correct_answers == mb_strtolower(trim($answer))){
                $correct = true;
            }
        }
        $answer_res = false;
        switch($calc_method){
            case "by_correctness":
                if($correct)
                    $answer_res = true;
                else
                    $answer_res = false;
            break;
            case "by_points":
                $answer_weight = floatval($checks['weight']);
                if($correct)
                    $answer_res = $answer_weight;
                else
                    $answer_res = 0;
            break;
            default:
                if($correct)
                    $answer_res = true;
                else
                    $answer_res = false;
            break;
        }
        return $answer_res;
    }

    public function count_multiple_correct_answers($question_id){
        global $wpdb;
        $answers_table = $wpdb->prefix . "aysquiz_answers";
        $question_id = absint(intval($question_id));

        $get_answers = $wpdb->get_var("SELECT COUNT(*) FROM {$answers_table} WHERE question_id={$question_id} AND correct=1");        
        return $get_answers;
    }
    
    public function has_multiple_correct_answers($question_id){
        global $wpdb;
        $answers_table = $wpdb->prefix . "aysquiz_answers";
        $question_id = absint(intval($question_id));

        $get_answers = $wpdb->get_var("SELECT COUNT(*) FROM {$answers_table} WHERE question_id={$question_id} AND correct=1");

        if (intval($get_answers) > 1) {
            return true;
        }
        return false;
    }
    
    public function is_question_not_influence($question_id){
        global $wpdb;
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $question_id = absint(intval($question_id));

        $question = $wpdb->get_row("SELECT * FROM {$questions_table} WHERE id={$question_id};", "ARRAY_A");
        $question['not_influence_to_score'] = ! isset($question['not_influence_to_score']) ? 'off' : $question['not_influence_to_score'];
        if(isset($question['not_influence_to_score']) && $question['not_influence_to_score'] == 'on'){
            return true;
        }
        return false;
    }
    
    public function in_question_use_html($question_id){
        global $wpdb;
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $question_id = absint(intval($question_id));

        $question = $wpdb->get_row("SELECT * FROM {$questions_table} WHERE id={$question_id};", "ARRAY_A");
        $options = ! isset($question['options']) ? array() : json_decode($question['options'], true);
        if(isset($options['use_html']) && $options['use_html'] == 'on'){
            return true;
        }
        return false;
    }

    public function has_text_answer($question_id){
        global $wpdb;
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $question_id = absint(intval($question_id));

        $text_types = array('text', 'short_text', 'number', 'date');
        $get_answers = $wpdb->get_var("SELECT type FROM {$questions_table} WHERE id={$question_id}");

        if (in_array($get_answers, $text_types)) {
            return true;
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
    
    public function ays_default_answer_html($question_id, $quiz_id, $answers, $options){
        $answer_container = "";
        $show_answers_numbering = (isset($options['show_answers_numbering']) && $options['show_answers_numbering'] != '') ? $options['show_answers_numbering'] : 'none';
        $numbering_type_arr = $this->ays_answer_numbering($show_answers_numbering);
        $numbering_type = '';
        foreach ($answers as $key => $answer) {
            $answer_image = (isset($answer['image']) && $answer['image'] != '') ? "<img src='{$answer["image"]}' alt='answer_image' class='ays-answer-image'>" : "";

            if($answer_image == ""){
                $ays_field_style = "";
                $answer_label_style = "";
            }else{
                if($options['rtlDirection']){
                    $ays_flex_dir = 'unset';
                    $ays_border_dir = "right";
                }else{
                    $ays_flex_dir = 'row-reverse';
                    $ays_border_dir = "left";
                }
                if($options['answersViewClass'] == 'grid'){
                    $ays_field_style = "style='display: block; height: fit-content; margin-bottom: 10px; width:200px;'";
                    $answer_label_style = "style='margin-bottom: 0; box-shadow: 0px 0px 10px; line-height: 1.5'";
                }else{
                    $ays_field_style = "style='margin-bottom: 10px; border-radius: 4px 4px 0 0; overflow: hidden; display: flex; box-shadow: 0px 0px 10px; flex-direction: {$ays_flex_dir};'";
                    $answer_label_style = "style='border-radius:0; border-{$ays_border_dir}:1px solid #ccc; line-height: 100px'";
                }
            }

            $answer_container_script    = '';
            if ( $options["questionType"] == 'checkbox' ) {

                $enable_max_selection_number = ( isset( $options['enable_max_selection_number'] ) && $options["enable_max_selection_number"] == 'on' ) ? true : false;
                $max_selection_number        = ( isset( $options["max_selection_number"] ) && $options["max_selection_number"] != '' ) ? absint($options["max_selection_number"]) : '';

                $enable_min_selection_number = ( isset( $options['enable_min_selection_number'] ) && $options["enable_min_selection_number"] == 'on' ) ? true : false;
                $min_selection_number        = ( isset( $options["min_selection_number"] ) && $options["min_selection_number"] != '' ) ? absint($options["min_selection_number"]) : '';

                if ( ( $enable_max_selection_number && ! empty( $max_selection_number ) && $max_selection_number != 0 ) || ( $enable_min_selection_number && ! empty( $min_selection_number ) && $min_selection_number != 0 ) ) {

                    $answer_container_script = "
                    <script>
                        if(typeof window.quizOptions_$quiz_id === 'undefined'){
                            window.quizOptions_$quiz_id = [];
                        }
                        window.quizOptions_".$quiz_id."['".$question_id."'] = '" . base64_encode(json_encode(array(
                            'enable_max_selection_number' => $enable_max_selection_number,
                            'max_selection_number' => $max_selection_number,
                            'enable_min_selection_number' => $enable_min_selection_number,
                            'min_selection_number' => $min_selection_number
                        ))) . "';
                    </script>";
                }
            }

            $label = "";
            if( $answer["answer"] != "" ){
                if($options['useHTML']){
                    $answer_content = do_shortcode((stripslashes($answer["answer"])));
                }else{
                    $answer_content = do_shortcode(htmlspecialchars(stripslashes($answer["answer"])));
                }

                if ( ! empty($numbering_type_arr) ) {
                    $numbering_type = (isset($numbering_type_arr[$key]) && $numbering_type_arr[$key] != '') ? $numbering_type_arr[$key] :'';
                    $numbering_type = $numbering_type . ' ';
                }

                $label .= "<label for='ays-answer-{$answer["id"]}-{$quiz_id}' $answer_label_style>" . $numbering_type  . $answer_content . "</label>";
            }
            if( $answer_image != "" ){
                $label .= "<label for='ays-answer-{$answer["id"]}-{$quiz_id}' style='border-radius:0;margin:0;padding:0;height:100px;'>{$answer_image}</label>";
            }
            $answer_container .= "
            <div class='ays-field ays_" . $options['answersViewClass'] . "_view_item' $ays_field_style>
                <input type='hidden' name='ays_answer_correct[]' value='{$answer["correct"]}'/>

                <input type='{$options["questionType"]}' name='ays_questions[ays-question-{$question_id}]' id='ays-answer-{$answer["id"]}-{$quiz_id}' value='{$answer["id"]}'/>

                {$label}

                {$answer_container_script}

            </div>";
        }
        
        return $answer_container;
    }
    
    protected function ays_text_answer_html($question_id, $quiz_id, $answers, $options){
        $enable_correction = $options['correction'] ? "display:inline-block;white-space: nowrap;" : "display:none";
        $enable_correction_textarea = $options['correction'] ? "width:80%;" : "width:100%;";
        $is_enable_question_max_length = $this->ays_quiz_is_enable_question_max_length( $question_id , 'text' );

        $question_not_influence_class  = "";
        if ( $this->is_question_not_influence( $question_id ) ) {
            $question_not_influence_class  = "ays_display_none";
            $enable_correction_textarea    = "width:100%;";
        }

        $question_text_max_length_array = (isset($options['questionMaxLengthArray']) && ! empty($options['questionMaxLengthArray'])) ? $options['questionMaxLengthArray'] : array();

        $ays_question_limit_length_class = '';
        $ays_quiz_question_text_message_html = '';
        $enable_question_text_max_length = false;
        $question_text_max_length = '';
        $question_limit_text_type = 'characters';
        $question_enable_text_message = false;
        if (! empty($question_text_max_length_array) ) {

            $enable_question_text_max_length = $question_text_max_length_array['enable_question_text_max_length'];

            $question_text_max_length = $question_text_max_length_array['question_text_max_length'];

            $question_limit_text_type = $question_text_max_length_array['question_limit_text_type'];

            $question_enable_text_message = $question_text_max_length_array['question_enable_text_message'];
        }

        if( $is_enable_question_max_length ){
            $ays_question_limit_length_class = 'ays_question_limit_length';

            if ($question_enable_text_message && $question_text_max_length != 0 && $question_text_max_length != '') {
                $ays_quiz_question_text_message_html .= '<div class="ays_quiz_question_text_conteiner">';
                    $ays_quiz_question_text_message_html .= '<div class="ays_quiz_question_text_message">';
                        $ays_quiz_question_text_message_html .= '<span class="ays_quiz_question_text_message_span">'. $question_text_max_length . '</span> ' . $question_limit_text_type . ' ' . __( 'left' , $this->plugin_name );
                    $ays_quiz_question_text_message_html .= '</div>';
                $ays_quiz_question_text_message_html .= '</div>';
            }
        }

        // Enable case sensitive text
        $enable_case_sensitive_text = ( isset($options['enable_case_sensitive_text']) && $options['enable_case_sensitive_text'] != '' ) ? $options['enable_case_sensitive_text'] : false;

        $answer_container = "<div class='ays-field ays-text-field'>";
            foreach ($answers as $answer) {
                $placeholder = isset($answer["placeholder"]) && $answer["placeholder"] != '' ? stripslashes(htmlentities($answer["placeholder"], ENT_QUOTES)) : '';
                $answer_image = (isset($answer['image']) && $answer['image'] != '') ? $answer["image"] : "";
                $answer_container .= "<textarea style='$enable_correction_textarea' type='text' placeholder='$placeholder' class='ays-text-input ". $ays_question_limit_length_class ."' name='ays_questions[ays-question-{$question_id}]' data-question-id='". $question_id ."' ></textarea>
                <input type='hidden' name='ays_answer_correct[]' value='0'/>
                <button type='button' style='$enable_correction' class='ays_check_answer action-button ". $question_not_influence_class ."'>".$this->buttons_texts['checkButton']."</button>";
                $answer_container .= "<script>
                        if(typeof window.quizOptions_$quiz_id === 'undefined'){
                            window.quizOptions_$quiz_id = [];
                        }
                        window.quizOptions_".$quiz_id."['".$question_id."'] = '" . base64_encode(json_encode(array(
                            'question_type' => 'text',
                            'question_answer' => htmlspecialchars_decode(stripslashes($answer["answer"]), ENT_QUOTES),
                            'enable_question_text_max_length' => $enable_question_text_max_length,
                            'question_text_max_length' => $question_text_max_length,
                            'question_limit_text_type' => $question_limit_text_type,
                            'question_enable_text_message' => $question_enable_text_message,
                            'enable_case_sensitive_text' => $enable_case_sensitive_text,
                        ))) . "';
                    </script>";
            }

        $answer_container .= "</div>";
        $answer_container .= $ays_quiz_question_text_message_html;
        return $answer_container;
    }
    
    protected function ays_short_text_answer_html($question_id, $quiz_id, $answers, $options){
        $enable_correction = $options['correction'] ? "display:inline-block;white-space: nowrap;" : "display:none";
        $enable_correction_textarea = $options['correction'] ? "width:80%;" : "width:100%;";
        $is_enable_question_max_length = $this->ays_quiz_is_enable_question_max_length( $question_id , 'short_text' );
        
        $question_not_influence_class  = "";
        if ( $this->is_question_not_influence( $question_id ) ) {
            $question_not_influence_class  = "ays_display_none";
            $enable_correction_textarea    = "width:100%;";
        }

        $question_text_max_length_array = (isset($options['questionMaxLengthArray']) && ! empty($options['questionMaxLengthArray'])) ? $options['questionMaxLengthArray'] : array();

        $ays_question_limit_length_class = '';
        $ays_quiz_question_text_message_html = '';
        $enable_question_text_max_length = false;
        $question_text_max_length = '';
        $question_limit_text_type = 'characters';
        $question_enable_text_message = false;
        if (! empty($question_text_max_length_array) ) {

            $enable_question_text_max_length = $question_text_max_length_array['enable_question_text_max_length'];

            $question_text_max_length = $question_text_max_length_array['question_text_max_length'];

            $question_limit_text_type = $question_text_max_length_array['question_limit_text_type'];

            $question_enable_text_message = $question_text_max_length_array['question_enable_text_message'];
        }

        if( $is_enable_question_max_length ){
            $ays_question_limit_length_class = 'ays_question_limit_length';

            if ($question_enable_text_message && $question_text_max_length != 0 && $question_text_max_length != '') {
                $ays_quiz_question_text_message_html .= '<div class="ays_quiz_question_text_conteiner">';
                    $ays_quiz_question_text_message_html .= '<div class="ays_quiz_question_text_message">';
                        $ays_quiz_question_text_message_html .= '<span class="ays_quiz_question_text_message_span">'. $question_text_max_length . '</span> ' . $question_limit_text_type . ' ' . __( 'left' , $this->plugin_name );
                    $ays_quiz_question_text_message_html .= '</div>';
                $ays_quiz_question_text_message_html .= '</div>';
            }
        }

        // Enable case sensitive text
        $enable_case_sensitive_text = ( isset($options['enable_case_sensitive_text']) && $options['enable_case_sensitive_text'] != '' ) ? $options['enable_case_sensitive_text'] : false;

        $answer_container = "<div class='ays-field ays-text-field'>";
            foreach ($answers as $answer) {
                $placeholder = isset($answer["placeholder"]) && $answer["placeholder"] != '' ? stripslashes(htmlentities($answer["placeholder"], ENT_QUOTES)) : '';
                $answer_image = (isset($answer['image']) && $answer['image'] != '') ? $answer["image"] : "";
                $answer_container .= "<input style='$enable_correction_textarea' type='text' placeholder='$placeholder' class='ays-text-input ". $ays_question_limit_length_class ."' name='ays_questions[ays-question-{$question_id}]' data-question-id='". $question_id ."'>
                <input type='hidden' name='ays_answer_correct[]' value='0'/>
                <button type='button' style='$enable_correction' class='ays_check_answer action-button ". $question_not_influence_class ."'>".$this->buttons_texts['checkButton']."</button>";
                $answer_container .= "<script>
                        if(typeof window.quizOptions_$quiz_id === 'undefined'){
                            window.quizOptions_$quiz_id = [];
                        }
                        window.quizOptions_".$quiz_id."['".$question_id."'] = '" . base64_encode(json_encode(array(
                            'question_type' => 'short_text',
                            'question_answer' => htmlspecialchars_decode(stripslashes($answer["answer"]), ENT_QUOTES),
                            'enable_question_text_max_length' => $enable_question_text_max_length,
                            'question_text_max_length' => $question_text_max_length,
                            'question_limit_text_type' => $question_limit_text_type,
                            'question_enable_text_message' => $question_enable_text_message,
                            'enable_case_sensitive_text' => $enable_case_sensitive_text
                        ))) . "';
                    </script>";
            }
        
        $answer_container .= "</div>";
        $answer_container .= $ays_quiz_question_text_message_html;
        return $answer_container;
    }
    
    protected function ays_number_answer_html($question_id, $quiz_id, $answers, $options){
        $enable_correction = $options['correction'] ? "display:inline-block;white-space: nowrap;" : "display:none";
        $enable_correction_textarea = $options['correction'] ? "width:80%;" : "width:100%;";
        $is_enable_question_max_length = $this->ays_quiz_is_enable_question_max_length( $question_id , 'number' );

        $question_not_influence_class  = "";
        if ( $this->is_question_not_influence( $question_id ) ) {
            $question_not_influence_class  = "ays_display_none";
            $enable_correction_textarea    = "width:100%;";
        }

        $question_text_max_length_array = (isset($options['questionMaxLengthArray']) && ! empty($options['questionMaxLengthArray'])) ? $options['questionMaxLengthArray'] : array();

        $ays_question_limit_length_class        = '';
        $ays_quiz_question_number_message_html  = '';
        $question_number_min_message_html       = '';
        $question_number_error_message          = '';
        $question_number_error_message_html     = '';

        $enable_question_number_max_length      = false;
        $enable_question_number_min_length      = false;
        $enable_question_number_error_message   = false;

        $question_number_max_length = '';
        $question_number_min_length = '';

        if (! empty($question_text_max_length_array) ) {

            $enable_question_number_max_length      = $question_text_max_length_array['enable_question_number_max_length'];
            $question_number_max_length             = $question_text_max_length_array['question_number_max_length'];

            $enable_question_number_min_length      = $question_text_max_length_array['enable_question_number_min_length'];
            $question_number_min_length             = $question_text_max_length_array['question_number_min_length'];

            $enable_question_number_error_message   = $question_text_max_length_array['enable_question_number_error_message'];
            $question_number_error_message          = $question_text_max_length_array['question_number_error_message'];
        }

        if( $is_enable_question_max_length ){
            $ays_question_limit_length_class = 'ays_question_number_limit_length';

            if ($question_number_max_length != 0 && $question_number_max_length != '') {
                $ays_quiz_question_number_message_html .= 'max="'. $question_number_max_length .'"';
            }
        }

        if ( $enable_question_number_min_length ) {
            $ays_question_limit_length_class = 'ays_question_number_limit_length';

            if ($question_number_min_length != 0 && $question_number_min_length != '') {
                $question_number_min_message_html .= 'min="'. $question_number_min_length .'"';
            }
        }

        if ( $enable_question_number_error_message ) {
            $ays_question_limit_length_class = 'ays_question_number_limit_length';

            if ( $question_number_error_message != "" ) {
                $question_number_error_message_html .= "<div class='ays-quiz-number-error-message ays_display_none'>";
                    $question_number_error_message_html .= $question_number_error_message;
                $question_number_error_message_html .= "</div>";
            }
        }

        $answer_container = "<div class='ays-field ays-text-field'>";
            foreach ($answers as $answer) {
                $placeholder = isset($answer["placeholder"]) && $answer["placeholder"] != '' ? stripslashes(htmlentities($answer["placeholder"], ENT_QUOTES)) : '';
                $answer_image = (isset($answer['image']) && $answer['image'] != '') ? $answer["image"] : "";
                $answer_container .= "<input style='$enable_correction_textarea' type='number' placeholder='$placeholder' class='ays-text-input ". $ays_question_limit_length_class ."' ". $ays_quiz_question_number_message_html ." ". $question_number_min_message_html ." name='ays_questions[ays-question-{$question_id}]' data-question-id='". $question_id ."'>
                <input type='hidden' name='ays_answer_correct[]' value='0'/>
                <button type='button' style='$enable_correction' class='ays_check_answer action-button ". $question_not_influence_class ."'>".$this->buttons_texts['checkButton']."</button>";
                $answer_container .= "<script>
                        if(typeof window.quizOptions_$quiz_id === 'undefined'){
                            window.quizOptions_$quiz_id = [];
                        }
                        window.quizOptions_".$quiz_id."['".$question_id."'] = '" . base64_encode(json_encode(array(
                            'question_type' => 'text',
                            'question_answer' => htmlspecialchars(stripslashes($answer["answer"])),
                            'enable_question_number_max_length' => $enable_question_number_max_length,
                            'question_number_max_length' => $question_number_max_length,
                            'enable_question_number_min_length' => $enable_question_number_min_length,
                            'question_number_min_length' => $question_number_min_length,
                            'enable_question_number_error_message' => $enable_question_number_error_message,
                            'question_number_error_message' => $question_number_error_message,
                        ))) . "';
                    </script>";
            }
        
        $answer_container .= "</div>";

        $answer_container .= $question_number_error_message_html;

        return $answer_container;
    }
    
    protected function ays_date_answer_html($question_id, $quiz_id, $answers, $options){
        $enable_correction = $options['correction'] ? "display:inline-block;white-space: nowrap;" : "display:none";
        $enable_correction_textarea = $options['correction'] ? "width:80%;" : "width:100%;";

        $question_not_influence_class  = "";
        if ( $this->is_question_not_influence( $question_id ) ) {
            $question_not_influence_class  = "ays_display_none";
            $enable_correction_textarea    = "width:100%;";
        }
        $answer_container = "<div class='ays-field ays-text-field'>";
            foreach ($answers as $answer) {
                $placeholder = isset($answer["placeholder"]) && $answer["placeholder"] != '' ? stripslashes(htmlentities($answer["placeholder"], ENT_QUOTES)) : '';
                $answer_image = (isset($answer['image']) && $answer['image'] != '') ? $answer["image"] : "";
                $answer_container .= "<input style='$enable_correction_textarea' type='date' autocomplete='off' placeholder='$placeholder' class='ays-text-input' name='ays_questions[ays-question-{$question_id}]'>
                <input type='hidden' name='ays_answer_correct[]' value='0'/>
                <button type='button' style='$enable_correction' class='ays_check_answer action-button ". $question_not_influence_class ."'>".$this->buttons_texts['checkButton']."</button>";
                $answer_container .= "<script>
                        if(typeof window.quizOptions_$quiz_id === 'undefined'){
                            window.quizOptions_$quiz_id = [];
                        }
                        window.quizOptions_".$quiz_id."['".$question_id."'] = '" . base64_encode(json_encode(array(
                            'question_type' => 'date',
                            'question_answer' => htmlspecialchars(stripslashes($answer["answer"]))
                        ))) . "';
                    </script>";
            }
        
        $answer_container .= "</div>";
        return $answer_container;
    }

    protected function ays_dropdown_answer_html($question_id, $quiz_id, $answers, $options){
        $show_answers_numbering = (isset($options['show_answers_numbering']) && $options['show_answers_numbering'] != '') ? $options['show_answers_numbering'] : 'none';
        $numbering_type_arr = $this->ays_answer_numbering($show_answers_numbering);
        $numbering_type = ''; 
        $answer_container = "<div class='ays-field ays-select-field'>            
            <select class='ays-select'>                
                <option value=''>".__('Select an answer', $this->plugin_name)."</option>";
            foreach ($answers as $key => $answer) {
                if ( ! empty($numbering_type_arr) ) {
                    $numbering_type = (isset($numbering_type_arr[$key]) && $numbering_type_arr[$key] != '') ? $numbering_type_arr[$key] :'';
                    $numbering_type = $numbering_type . ' ';
                }

                $answer_image = (isset($answer['image']) && $answer['image'] != '') ? $answer["image"] : "";
                $answer_container .= "<option data-nkar='{$answer_image}' data-chisht='{$answer["correct"]}' value='{$answer["id"]}'>" . $numbering_type . do_shortcode(htmlspecialchars(stripslashes($answer["answer"]))) . "</option>";
            }
        $answer_container .= "</select>";
        $answer_container .= "<input class='ays-select-field-value' type='hidden' name='ays_questions[ays-question-{$question_id}]' value=''/>";

            foreach ($answers as $answer) {
                $answer_container .= "<input type='hidden' name='ays_answer_correct[]' value='{$answer["correct"]}'/>";
            }
        $answer_container .= "</div>";
        
        return $answer_container;
    }

    protected function isHomogenousStrong($arr, $question_id){
        $arr_count = count( $arr );
        $arr_sum = array_sum( $arr );
        $count_correct = intval( $this->count_multiple_correct_answers($question_id) );
        $a = $arr_count - $arr_sum;
        $b = $arr_sum - $a;
        
        return $b / $count_correct;
    }
    
    protected function isHomogenous($arr, $question_id){
        $mustBe = true;
        $count = 0;
        foreach ($arr as $val) {
            if ($mustBe !== $val) {
                return false;
            }
            $count++;
        }
        $count_correct = intval( $this->count_multiple_correct_answers($question_id) );
        if($count !== $count_correct){
            return false;
        }
        return true;
    }
    
    protected function get_question_weight($id){
        //global $wpdb;
        //$sql = "SELECT weight FROM {$wpdb->prefix}aysquiz_questions WHERE id = $id";
        //$result = $wpdb->get_var($sql);
        //return intval($result);
        return 1;
    }

    protected function hex2rgba($color, $opacity = false){

        $default = 'rgb(0,0,0)';

        //Return default if no color provided
        if (empty($color))
            return $default;

        //Sanitize $color if "#" is provided
        if ($color[0] == '#') {
            $color = substr($color, 1);
        }else{
            return $color;
        }

        //Check if color has 6 or 3 characters and get values
        if (strlen($color) == 6) {
            $hex = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
        } elseif (strlen($color) == 3) {
            $hex = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
        } else {
            return $default;
        }

        //Convert hexadec to rgb
        $rgb = array_map('hexdec', $hex);

        //Check if opacity is set(rgba or rgb)
        if ($opacity) {
            if (abs($opacity) > 1)
                $opacity = 1.0;
            $output = 'rgba(' . implode(",", $rgb) . ',' . $opacity . ')';
        } else {
            $output = 'rgb(' . implode(",", $rgb) . ')';
        }

        //Return rgb(a) color string
        return $output;
    }

    public static function secondsToWords($seconds){
        $ret = "";

        /*** get the days ***/
        $days = intval(intval($seconds) / (3600 * 24));
        if ($days > 0) {
            $ret .= sprintf( __('%s days', AYS_QUIZ_NAME ), $days ) . " ";
        }

        /*** get the hours ***/
        $hours = (intval($seconds) / 3600) % 24;
        if ($hours > 0) {
            $ret .= sprintf( __('%s hours', AYS_QUIZ_NAME ), $hours ) . " ";
        }

        /*** get the minutes ***/
        $minutes = (intval($seconds) / 60) % 60;
        if ($minutes > 0) {
            $ret .= sprintf( __('%s minutes', AYS_QUIZ_NAME ), $minutes ) . " ";
        }

        /*** get the seconds ***/
        $seconds = intval($seconds) % 60;
        if ($seconds > 0) {
            $ret .= sprintf( __('%s seconds', AYS_QUIZ_NAME ), $seconds );
        }

        return $ret;
    }

    protected function add_results_to_db($data){
        global $wpdb;
        $results_table = $wpdb->prefix . 'aysquiz_reports';

        $user_ip = $data['user_ip'];
        $user_name = $data['user_name'];
        $user_email = $data['user_email'];
        $user_phone = $data['user_phone'];
        $quiz_id = $data['quiz_id'];
        $start_date = $data['start_date'];
        $end_date = $data['end_date'];
        $duration = strtotime($end_date) - strtotime($start_date);
        $score = $data['score'];
        $options = array();
        $options = $data['answered'];
        $calc_method = $data['calc_method'];
        $options['passed_time'] = $this->get_time_difference($start_date, $end_date);
        $options['calc_method'] = $calc_method;
        
        $quiz_attributes_information = array();
        $quiz_attributes = $this->get_quiz_attributes_by_id($quiz_id);
        
        $options['attributes_information'] = $quiz_attributes_information;
        $results = $wpdb->insert(
            $results_table,
            array(
                'quiz_id' => absint(intval($quiz_id)),
                'user_id' => get_current_user_id(),
                'user_name' => $user_name,
                'user_email' => $user_email,
                'user_phone' => $user_phone,
                'user_ip' => $user_ip,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'duration' => $duration,
                'score' => $score,
                'options' => json_encode($options)
            ),
            array(
                '%d', // quiz_id
                '%d', // user_id
                '%s', // user_name
                '%s', // user_email
                '%s', // user_phone
                '%s', // user_ip
                '%s', // start_date
                '%s', // end_date
                '%s', // duration
                '%d', // score
                '%s', // options
            )
        );

        if ($results >= 0) {
            return true;
        }

        return false;
    }
    
    protected function ays_get_count_of_rates($id){
        global $wpdb;
        $sql = "SELECT COUNT(`id`) AS count FROM {$wpdb->prefix}aysquiz_rates WHERE quiz_id= $id";
        $result = $wpdb->get_var($sql);
        return $result;
    }
    
    protected function ays_get_count_of_reviews($start, $limit, $quiz_id){
        global $wpdb;
        $sql = "SELECT COUNT(`id`) AS count FROM {$wpdb->prefix}aysquiz_rates WHERE (review<>'' OR options<>'') AND quiz_id = $quiz_id ORDER BY id DESC LIMIT $start, $limit";
        $result = $wpdb->get_var($sql);
        return $result;
    }
    
    protected function ays_set_rate_id_of_result($id , $last_result_id){
        global $wpdb;
        $results_table = $wpdb->prefix . 'aysquiz_reports';
        $sql = "SELECT * FROM $results_table WHERE id = ".intval($last_result_id);
        $res = $wpdb->get_row($sql, ARRAY_A);
        $options = json_decode($res['options'], true);
        $options['rate_id'] = $id;
        $results = $wpdb->update(
            $results_table,
            array( 'options' => json_encode($options) ),
            array( 'id' => intval($last_result_id) ),
            array( '%s' ),
            array( '%d' )
        );
        if($results !== false){
            return true;
        }
        return false;
    }
    
    protected function ays_get_average_of_rates($id){
        global $wpdb;
        $sql = "SELECT AVG(`score`) AS avg_score FROM {$wpdb->prefix}aysquiz_rates WHERE quiz_id= $id";
        $result = $wpdb->get_var($sql);

        if ( is_null( $result ) || empty( $result ) ) {
            $result = 0;
        }

        return $result;
    }

    protected function ays_get_average_of_scores($id){
        global $wpdb;
        $sql = "SELECT AVG(`score`) FROM {$wpdb->prefix}aysquiz_reports WHERE quiz_id= $id";

        $avg_result = $wpdb->get_var($sql);
        if ( is_null( $avg_result ) || empty( $avg_result ) ) {
            $avg_result = 0;
        }

        $result = round($avg_result);
        return $result;
    }
    
    protected function ays_get_reasons_of_rates($start, $limit, $quiz_id){
        global $wpdb;
        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_rates WHERE quiz_id=$quiz_id AND (review<>'' OR options<>'') ORDER BY id DESC LIMIT $start, $limit";
        $result = $wpdb->get_results($sql, "ARRAY_A");
        return $result;
    }
    
    protected function ays_get_full_reasons_of_rates($start, $limit, $quiz_id, $zuyga){
        $quiz_rate_reasons = $this->ays_get_reasons_of_rates($start, $limit, $quiz_id);
        $quiz_rate_html = "";
        foreach($quiz_rate_reasons as $key => $reasons){
            $user_name = !empty($reasons['user_name']) ? "<span>".$reasons['user_name']."</span>" : '';
            if($this->isJSON($reasons['options'])){
                $reason = json_decode($reasons['options'], true)['reason'];
            }elseif($reasons['options'] != ''){
                $reason = $reasons['options'];
            }else{
                $reason = $reasons['review'];                
            }
            if(intval($reasons['user_id']) != 0){
                $user_img = esc_url( get_avatar_url( intval($reasons['user_id']) ) );
            }else{
                $user_img = AYS_QUIZ_PUBLIC_URL . "/images/avatar_2x.png";
            }
            $score = $reasons['score'];
            $commented = date('M j, Y', strtotime($reasons['rate_date']));
            if($zuyga == 1){
                $row_reverse = ($key % 2 == 0) ? 'row_reverse' : '';
            }else{
                $row_reverse = ($key % 2 == 0) ? '' : 'row_reverse';
            }
            $quiz_rate_html .= "<div class='quiz_rate_reasons'>
                  <div class='rate_comment_row $row_reverse'>
                    <div class='rate_comment_user'>
                        <div class='thumbnail'>
                            <img class='img-responsive user-photo' src='".$user_img."'>
                        </div>
                    </div>
                    <div class='rate_comment'>
                        <div class='panel panel-default'>
                            <div class='panel-heading'>
                                <i class='ays_fa ays_fa_user'></i> <strong>$user_name</strong><br/>
                                <i class='ays_fa ays_fa_clock_o'></i> $commented<br/>
                                ".__("Rated", $this->plugin_name)." <i class='ays_fa ays_fa_star'></i> $score
                            </div>
                            <div class='panel-body'><div>". stripslashes($reason) ."</div></div>
                        </div>
                    </div>
                </div>
            </div>";
        }
        return $quiz_rate_html;
    }
    
    public function ays_get_rate_last_reviews(){
        
        $this->buttons_texts = $this->ays_set_quiz_texts();
        $ays_load_more_button_text = $this->buttons_texts['loadMoreButton'];
        
        $quiz_id = absint( sanitize_text_field( $_REQUEST["quiz_id"] ) );
        $quiz_rate_html = "<div class='quiz_rate_reasons_container'>";
        $quiz_rate_html .= $this->ays_get_full_reasons_of_rates(0, 5, $quiz_id, 0);
        $quiz_rate_html .= "</div>";
        if($this->ays_get_count_of_reviews(0, 5, $quiz_id) / 5 > 1){
            $quiz_rate_html .= "<div class='quiz_rate_load_more'>
                <div>
                    <div data-class='lds-spinner' data-role='loader' class='ays-loader'><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
                </div>
                <button type='button' zuyga='1' startfrom='5' class='action-button ays_load_more_review'><i class='ays_fa ays_fa_chevron_circle_down'></i> ".$ays_load_more_button_text."</button>
            </div>";
        }
        ob_end_clean();
        $ob_get_clean = ob_get_clean();
        echo json_encode(array(
            'status'         => true,
            'quiz_rate_html' => $quiz_rate_html
        ));
        wp_die();
    }
    
    public function ays_load_more_reviews(){
        $quiz_id = absint( sanitize_text_field( $_REQUEST["quiz_id"] ) );
        $start = absint( sanitize_text_field( $_REQUEST["start_from"] ) );
        $zuyga = absint( sanitize_text_field( $_REQUEST["zuyga"] ) );
        $limit = 5;
        $quiz_rate_html = "";
        $quiz_rate_html .= $this->ays_get_full_reasons_of_rates($start, $limit, $quiz_id, $zuyga);
        if($quiz_rate_html == ""){
            ob_end_clean();
            $ob_get_clean = ob_get_clean();
            echo "<p class='ays_no_more'>" . __( "No more reviews", $this->plugin_name ) . "</p>";
            wp_die();
        }else{       
            ob_end_clean();
            $ob_get_clean = ob_get_clean();     
            $quiz_rate_html = "<div class='quiz_rate_more_review'>".$quiz_rate_html."</div>";            
            echo $quiz_rate_html;
            wp_die();
        }
    }

    public function ays_shuffle_assoc($list) { 
        if (!is_array($list)) return $list; 

        $keys = array_keys($list); 
        shuffle($keys); 
        $random = array(); 
        foreach ($keys as $key) { 
            $random[$key] = $list[$key]; 
        }
        return $random; 
    }
    
    public function ays_rate_the_quiz(){
        global $wpdb;
        $rates_table = $wpdb->prefix . 'aysquiz_rates';

        $user_id = get_current_user_id();
        
        $report_id = (isset($_REQUEST['last_result_id']) && sanitize_text_field($_REQUEST['last_result_id']) != '' && ! is_null( sanitize_text_field($_REQUEST['last_result_id']) ) ) ? intval( sanitize_text_field( $_REQUEST['last_result_id'] ) ) : 0;

        // Make responses anonymous
        $quiz_make_responses_anonymous = (isset($_REQUEST['quiz_make_responses_anonymous']) && sanitize_text_field($_REQUEST['quiz_make_responses_anonymous']) == 'true' ) ? true : false;

        $user_ip = $this->get_user_ip();
        if(isset($_REQUEST['ays_user_name']) && sanitize_text_field( $_REQUEST['ays_user_name'] ) != ''){
            $user_name = esc_sql( sanitize_text_field( $_REQUEST['ays_user_name'] ) );
        }elseif(is_user_logged_in()){
            $user = wp_get_current_user();
            $user_name = $user->data->display_name;
        }else{
            $user_name = __( 'Anonymous' , AYS_QUIZ_NAME );
        }
        $user_email = isset($_REQUEST['ays_user_email']) ? esc_sql( sanitize_email( $_REQUEST['ays_user_email'] ) ) : '';
        $user_phone = isset($_REQUEST['ays_user_phone']) ? esc_sql( sanitize_text_field( $_REQUEST['ays_user_phone'] ) ) : '';
        $quiz_id = absint(intval($_REQUEST["quiz_id"]));
        $score = esc_sql( sanitize_text_field( $_REQUEST['rate_score'] ) );
        $rate_date = esc_sql( sanitize_text_field( $_REQUEST['rate_date'] ) );

        if ( $quiz_make_responses_anonymous ) {
            $user_id     = 0;
            $user_ip     = '';
            $user_name   = __( 'Anonymous' , AYS_QUIZ_NAME );
            $user_email  = '';
            $user_phone  = '';
        }

        $results = $wpdb->insert(
            $rates_table,
            array(
                'quiz_id'     => $quiz_id,
                'user_id'     => $user_id,
                'user_ip'     => $user_ip,
                'user_name'   => $user_name,
                'user_email'  => $user_email,
                'user_phone'  => $user_phone,
                'score'       => $score,
                'review'      => isset($_REQUEST['rate_reason']) ? stripslashes( esc_sql( sanitize_textarea_field( $_REQUEST['rate_reason'] ) ) ) : '',
                'options'     => '',
                'rate_date'   => $rate_date,
            ),
            array(
                '%d', //quiz_id
                '%d', //user_id
                '%s', //user_ip
                '%s', //user_name
                '%s', //user_email
                '%s', //user_phone
                '%s', //score
                '%s', //review
                '%s', //options
                '%s', //rate_date
            )
        );
        $rate_id = $wpdb->insert_id;
        $avg_score = $this->ays_get_average_of_rates($quiz_id);
        if ($results >= 0 && $this->ays_set_rate_id_of_result( $rate_id , $report_id )) {
            ob_end_clean();
            $ob_get_clean = ob_get_clean();
            echo json_encode(array(
                //'rate_id'     => $rate_id,
                //'result'      => $this->ays_set_rate_id_of_result($rate_id),
                'quiz_id'       => $quiz_id,
                'status'        => true,
                'avg_score'     => round($avg_score, 1),
                'score'         => intval(round($avg_score)),
                'rates_count'   => $this->ays_get_count_of_rates($quiz_id),
            ));
            wp_die();
        }
        ob_end_clean();
        $ob_get_clean = ob_get_clean();
        echo json_encode(array(
            'status'    => false,
        ));
        wp_die();
    }

    protected function get_user_by_ip($id){
        global $wpdb;
        $user_ip = $this->get_user_ip();
        $sql = "SELECT COUNT(*) FROM `{$wpdb->prefix}aysquiz_reports` WHERE `user_ip` = '$user_ip' AND `quiz_id` = $id";
        $result = $wpdb->get_var($sql);
        return $result;
    }

    protected function get_limit_user_by_id($quiz_id, $user_id){
        global $wpdb;
        $sql = "SELECT COUNT(*) FROM `{$wpdb->prefix}aysquiz_reports` WHERE `user_id` = '$user_id' AND `quiz_id` = $quiz_id";
        $result = intval($wpdb->get_var($sql));
        return $result;
    }

    protected function get_user_ip(){
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

    protected function get_time_difference($strStart, $strEnd){
        $dteStart = new DateTime($strStart);
        $dteEnd = new DateTime($strEnd);

        $interval = $dteStart->diff($dteEnd);
        $return = '';
        if ($v = $interval->y >= 1) $return .= $this->pluralize($interval->y, 'year') . ' ';
        if ($v = $interval->m >= 1) $return .= $this->pluralize($interval->m, 'month') . ' ';
        if ($v = $interval->d >= 1) $return .= $this->pluralize($interval->d, 'day') . ' ';
        if ($v = $interval->h >= 1) $return .= $this->pluralize($interval->h, 'hour') . ' ';
        if ($v = $interval->i >= 1) $return .= $this->pluralize($interval->i, 'minute') . ' ';
        $return .= $this->pluralize($interval->s, 'second');

        return $return;
    }
    
    protected function pluralize($count, $text){
        return $count . (($count == 1) ? (" $text") : (" ${text}s"));
    }
    
    protected function isJSON($string){
       return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }
    
    public function ays_get_user_information() {
        
        if(is_user_logged_in()) {
            $output = json_encode(wp_get_current_user());
        } else {
            $output = null;
        }
        ob_end_clean();
        $ob_get_clean = ob_get_clean();
        echo $output;
        wp_die();
    }

    public static function ays_quiz_set_cookie($attr){
        $cookie_name = $attr['name'].$attr['id'];
        $cookie_value = $attr['title'];
        $cookie_expiration =  current_time('timestamp') + (1 * 365 * 24 * 60 * 60);
        setcookie($cookie_name, $cookie_value, $cookie_expiration, '/');
    }

    public static function ays_quiz_remove_cookie($attr){
        $cookie_name = $attr['name'].$attr['id'];
        if(isset($_COOKIE[$cookie_name])){
            unset($_COOKIE[$cookie_name]);
            $cookie_expiration =  current_time('timestamp') - 1;   
            setcookie($cookie_name, null, $cookie_expiration, '/');
        }
    }

    public static function ays_quiz_check_cookie($attr){
        $cookie_name = $attr['name'].$attr['id'];
        if(isset($_COOKIE[$cookie_name])){
            return true;
        }
        return false;
    }
    
    public static function ays_autoembed( $content ) {
        global $wp_embed;

        if ( is_null( $content ) ) {
            return $content;
        }

        $content = stripslashes( wpautop( $content ) );
        $content = $wp_embed->autoembed( $content );
        if ( strpos( $content, '[embed]' ) !== false ) {
            $content = $wp_embed->run_shortcode( $content );
        }
        $content = do_shortcode( $content );
        return $content;
    }

    public static function get_questions_categories($q_ids){
        global $wpdb;

        if($q_ids == ''){
            return array();
        }
        $sql = "SELECT DISTINCT c.id, c.title
                FROM {$wpdb->prefix}aysquiz_categories c
                JOIN {$wpdb->prefix}aysquiz_questions q
                ON c.id = q.category_id
                WHERE q.id IN ({$q_ids})";

        $result = $wpdb->get_results($sql, 'ARRAY_A');
        $cats = array();

        foreach($result as $res){
            $cats[$res['id']] = $res['title'];
        }

        return $cats;
    }

    public static function get_quiz_questions_by_ids($ids){

        global $wpdb;

        $results = array();
        if(!empty($ids)){
            $ids = implode(",", $ids);
            $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_questions WHERE id IN (" . $ids . ")";

            $results = $wpdb->get_results($sql, "ARRAY_A");

        }

        return $results;

    }

    public static function ays_get_average_score_by_category($id){
        global $wpdb;
        $quizes_table = $wpdb->prefix . 'aysquiz_quizes';
        $quizes_questions_table = $wpdb->prefix . 'aysquiz_questions';
        $quizes_questions_cat_table = $wpdb->prefix . 'aysquiz_categories';
        $sql = "SELECT question_ids FROM {$quizes_table} WHERE id = ".$id;
        $results = $wpdb->get_var( $sql);
        $questions_ids = array();
        $questions_counts = array();
        $questions_cat_list = array();
        if($results != ''){
            $results = explode("," , $results);
            foreach ($results as $key){
                $questions_ids[$key] = 0;
                $questions_counts[$key] = 0;

                $sql = "SELECT q.category_id, c.title
                        FROM {$quizes_questions_table} AS q
                        JOIN {$quizes_questions_cat_table} AS c
                            ON q.category_id = c.id
                        WHERE q.id = {$key}; ";
                $questions_cat_list[$key] = $wpdb->get_row( $sql);
            }
        }

        $quizes_reports_table = $wpdb->prefix . 'aysquiz_reports';
        $sql = "SELECT options
                FROM {$quizes_reports_table}
                WHERE quiz_id =".$id;
        $report = $wpdb->get_results( $sql, ARRAY_A );
        if(! empty($report)){
            foreach ($report as $key){
                $report = json_decode($key["options"]);
                $questions = isset( $report->correctness ) ? $report->correctness : array();
                foreach ($questions as $i => $v){
                    $q = (int) substr($i ,12);
                    if(isset($questions_ids[$q])) {
                        if ($v) {
                            $questions_ids[$q]++;
                        }

                        $questions_counts[$q]++;
                    }
                }
            }
        }

        $q_cat_list = array();
        $q_cat_title = array();
        foreach ($questions_cat_list as $key_id => $val ) {
            $val_arr = (array) $val;
            if(isset($val_arr['category_id'])){
                if (!array_key_exists($val_arr['category_id'], $q_cat_list)) {
                    $q_cat_list[$val_arr['category_id']][] = $key_id;
                    $q_cat_title[$val_arr['category_id']] = $val_arr['title'];
                }else{
                    $q_cat_list[$val_arr['category_id']][] = $key_id;
                    $q_cat_title[$val_arr['category_id']] = $val_arr['title'];
                }
            }
        }

        $q_cat_lists = array('percent'=>'', 'cat_name'=>'');
        $q_cats_lists = array();
        foreach ($q_cat_list as $key1 => $value1) {
            $sum_min = 0;
            $sum_max = 0;
            foreach ($value1 as $key2 => $value2) {
                $sum_min += $questions_ids[$value2];
                $sum_max += $questions_counts[$value2];
            }
            if($sum_max == 0){
                $persentage = 0;
            }else{
                $persentage = round(($sum_min*100)/$sum_max, 1);
            }

            $passed_users_count = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_reports WHERE quiz_id=".$id;
            $passed_users_count_res = $wpdb->get_var($passed_users_count);
            $avg_score_by_cat = "0%";
            if ($passed_users_count_res > 0) {
                $avg_score_by_cat = round( $persentage , 1 ) . '%';
            }
            $q_cat_lists['percent'] = $avg_score_by_cat;
            $q_cat_lists['cat_name'] = $q_cat_title[$key1];
            $q_cats_lists[] = $q_cat_lists;

        }

        $avg_category = '';
        foreach ($q_cats_lists as $key => $q_cats_list) {
            $avg_category .= '<p class="">
                                <strong class="">'.$q_cats_list['cat_name']  .':</strong>
                                <span class="">'.$q_cats_list['percent'].'</span>
                             </p>';
        }
        return $avg_category;
    }

    public function ays_quiz_is_elementor(){
        // if ( defined( 'ELEMENTOR_PATH' ) && class_exists( 'Elementor\Widget_Base' ) ) {
        //     if ( class_exists( 'Elementor\Plugin' ) ) {
        //         if ( is_callable( 'Elementor\Plugin', 'instance' ) ) {
        //             $elementor = Elementor\Plugin::instance();
        //             if ( isset( $elementor->preview ) ) {
        //                 return \Elementor\Plugin::$instance->preview->is_preview_mode();
        //             }
        //         }
        //     }
        // }
        $is_elementor = isset( $_GET['action'] ) && $_GET['action'] == 'elementor' ? true : false;
        if ( ! $is_elementor ) {
            $is_elementor = isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'elementor_ajax' ? true : false;
        }
        return $is_elementor;
    }

    public static function ays_quiz_is_enable_question_max_length( $question_id , $question_type = 'text' ){
        global $wpdb;
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $question_id = absint(intval($question_id));

        $question = $wpdb->get_row("SELECT * FROM {$questions_table} WHERE id={$question_id};", "ARRAY_A");
        $options = ! isset($question['options']) ? array() : json_decode($question['options'], true);

        $res = false;
        switch ( $question_type ) {
            case 'number':
                if(isset($options['enable_question_number_max_length']) && sanitize_text_field( $options['enable_question_number_max_length'] ) == 'on'){
                    $res = true;
                }
                break;            
            default:
                if(isset($options['enable_question_text_max_length']) && sanitize_text_field( $options['enable_question_text_max_length'] ) == 'on'){
                    $res = true;
                }
                break;
        }
        return $res;
    }

    public static function ays_quiz_get_question_max_length_array( $question_id ){
        global $wpdb;
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $question_id = absint(intval($question_id));

        $question = $wpdb->get_row("SELECT * FROM {$questions_table} WHERE id={$question_id};", "ARRAY_A");
        $options = ! isset($question['options']) ? array() : json_decode($question['options'], true);

        $res = array();

        // Maximum length of a text field
        $options['enable_question_text_max_length'] = isset($options['enable_question_text_max_length']) ? sanitize_text_field( $options['enable_question_text_max_length'] ) : 'off';
        $res['enable_question_text_max_length'] = (isset($options['enable_question_text_max_length']) && sanitize_text_field( $options['enable_question_text_max_length'] ) == 'on') ? true : false;

        // Length
        $res['question_text_max_length'] = ( isset($options['question_text_max_length']) && sanitize_text_field( $options['question_text_max_length'] ) != '' ) ? absint( intval( sanitize_text_field( $options['question_text_max_length'] ) ) ) : '';

        // Limit by
        $res['question_limit_text_type'] = ( isset($options['question_limit_text_type']) && sanitize_text_field( $options['question_limit_text_type'] ) != '' ) ? sanitize_text_field( $options['question_limit_text_type'] ) : 'characters';

        // Show the counter-message
        $options['question_enable_text_message'] = isset($options['question_enable_text_message']) ? sanitize_text_field( $options['question_enable_text_message'] ) : 'off';
        $res['question_enable_text_message'] = (isset($options['question_enable_text_message']) && $options['question_enable_text_message'] == 'on') ? true : false;

        // Maximum length of a number field
        $options['enable_question_number_max_length'] = isset($options['enable_question_number_max_length']) ? sanitize_text_field( $options['enable_question_number_max_length'] ) : 'off';
        $res['enable_question_number_max_length'] = (isset($options['enable_question_number_max_length']) && sanitize_text_field( $options['enable_question_number_max_length'] ) == 'on') ? true : false;

        // Length
        $res['question_number_max_length'] = ( isset($options['question_number_max_length']) && sanitize_text_field( $options['question_number_max_length'] ) != '' ) ? intval( sanitize_text_field( $options['question_number_max_length'] ) ) : '';

        // Minimum length of a number field
        $options['enable_question_number_min_length'] = isset($options['enable_question_number_min_length']) ? sanitize_text_field( $options['enable_question_number_min_length'] ) : 'off';
        $res['enable_question_number_min_length'] = (isset($options['enable_question_number_min_length']) && sanitize_text_field( $options['enable_question_number_min_length'] ) == 'on') ? true : false;

        // Length
        $res['question_number_min_length'] = ( isset($options['question_number_min_length']) && sanitize_text_field( $options['question_number_min_length'] ) != '' ) ? intval( sanitize_text_field( $options['question_number_min_length'] ) ) : '';

        // Show error message
        $options['enable_question_number_error_message'] = isset($options['enable_question_number_error_message']) ? sanitize_text_field( $options['enable_question_number_error_message'] ) : 'off';
        $res['enable_question_number_error_message'] = (isset($options['enable_question_number_error_message']) && sanitize_text_field( $options['enable_question_number_error_message'] ) == 'on') ? true : false;

        // Message
        $res['question_number_error_message'] = ( isset($options['question_number_error_message']) && sanitize_text_field( $options['question_number_error_message'] ) != '' ) ? stripslashes( sanitize_text_field( $options['question_number_error_message'] ) ) : '';
            
        return $res;
    }

    public function ays_answer_numbering($numbering){
        $keyword_arr = array();
        switch ($numbering) {
            case '1.':
                $char_min_val = '1';
                $char_max_val = '100';
                for($x = $char_min_val; $x <= $char_max_val; $x++){
                    $keyword_arr[] = $x .".";
                }
                break;
            case '1)':
                $char_min_val = '1';
                $char_max_val = '100';
                for($x = $char_min_val; $x <= $char_max_val; $x++){
                    $keyword_arr[] = $x .")";
                }
                break;
            case 'A.':
                $char_min_val = 'A';
                $char_max_val = 'Z';
                for($x = $char_min_val; $x <= $char_max_val; $x++){
                    $keyword_arr[] = $x .".";
                }
                break;
            case 'A)':
                $char_min_val = 'A';
                $char_max_val = 'Z';
                for($x = $char_min_val; $x <= $char_max_val; $x++){
                    $keyword_arr[] = $x .")";
                }
                break;
            case 'a.':
                $char_min_val = 'a';
                $char_max_val = 'z';
                for($x = $char_min_val; $x <= $char_max_val; $x++){
                    $keyword_arr[] = $x .".";
                }
                break;
            case 'a)':
                $char_min_val = 'a';
                $char_max_val = 'z';
                for($x = $char_min_val; $x <= $char_max_val; $x++){
                    $keyword_arr[] = $x .")";
                }
                break;
            default:
                break;
        }
        return $keyword_arr;
    }

    public static function ays_question_numbering( $numbering , $total ){
        $keyword_arr = array();
        switch ($numbering) {
            case '1.':
                $char_min_val = '1';
                $char_max_val = $total;
                for($x = $char_min_val; $x <= $char_max_val; $x++){
                    $keyword_arr[] = $x .".";
                }

                break;
            case '1)':
                $char_min_val = '1';
                $char_max_val = $total;
                for($x = $char_min_val; $x <= $char_max_val; $x++){
                    $keyword_arr[] = $x .")";
                }
                break;
            case 'A.':

                $char_min_val = 'A';
                $char_max_val = 'Z';
                for($x = $char_min_val; $x <= $char_max_val; $x++){
                    $keyword_arr[] = $x .".";
                }

                break;
            case 'A)':

                $char_min_val = 'A';
                $char_max_val = 'Z';
                for($x = $char_min_val; $x <= $char_max_val; $x++){
                    $keyword_arr[] = $x .")";
                }

                break;
            case 'a.':
                $char_min_val = 'a';
                $char_max_val = 'z';
                for($x = $char_min_val; $x <= $char_max_val; $x++){
                    $keyword_arr[] = $x .".";
                }

                break;
            case 'a)':

                $char_min_val = 'a';
                $char_max_val = 'z';
                for($x = $char_min_val; $x <= $char_max_val; $x++){
                    $keyword_arr[] = $x .")";
                }

                break;

            default:

                break;
        }

        return $keyword_arr;
    }
    
    public function ays_color_inverse( $color ){
        $color = str_replace( '#', '', $color );
        if ( strlen( $color ) == 3 ){
            $color_short = str_split( $color );
            foreach( $color_short as $k => $c_short ){
                $color_short[$k] = $c_short . $c_short;
            }
            $color = implode( $color_short );
        }

        if ( strlen( $color ) != 6 ){
            return '#000000';
        }

        $rgb = '';
        for ( $x = 0; $x < 3; $x++ ){
            $c = 255 - hexdec( substr( $color, ( 2 * $x ), 2 ) );
            $c = ( $c < 0 ) ? 0 : dechex( $c );
            $rgb .= ( strlen( $c ) < 2 ) ? '0' . $c : $c;
        }

        return '#'.$rgb;
    }

    // Retrieves the attachment ID from the file URL
    public function ays_quiz_get_image_id_by_url( $image_url ) {
        global $wpdb;

        $image_alt_text = "";
        if ( !empty( $image_url ) ) {

            $re = '/-\d+[Xx]\d+\./';
            $subst = '.';

            $image_url = preg_replace($re, $subst, $image_url, 1);

            $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ));
            if ( !is_null( $attachment ) && !empty( $attachment ) ) {

                $image_id = (isset( $attachment[0] ) && $attachment[0] != "") ? absint(  $attachment[0] ) : "";
                if ( $image_id != "" ) {
                    $image_alt_text = $this->ays_quiz_get_image_alt_text_by_id( $image_id );
                }
            }
        }

        return $image_alt_text; 
    }

    public function ays_quiz_get_image_alt_text_by_id( $image_id ) {

        $image_data = "";
        if ( $image_id != "" ) {

            $result = get_post_meta($image_id, '_wp_attachment_image_alt', TRUE);
            if ( $result && $result != "" ) {
                $image_data = esc_attr( $result );
            }
        }

        return $image_data; 
    }
}
