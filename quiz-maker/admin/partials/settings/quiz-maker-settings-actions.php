<?php
class Quiz_Maker_Settings_Actions {
    private $plugin_name;

    public function __construct($plugin_name) {
        $this->plugin_name = $plugin_name;
    }

    public function store_data(){
        global $wpdb;
        $settings_table = $wpdb->prefix . "aysquiz_settings";
        if( isset($_REQUEST["settings_action"]) && wp_verify_nonce( sanitize_text_field( $_REQUEST["settings_action"] ), 'settings_action' ) ){
            $success = 0;

            $start_button           = (isset($_REQUEST['ays_start_button']) && $_REQUEST['ays_start_button'] != '') ? stripslashes( sanitize_text_field( $_REQUEST['ays_start_button'] ) ) : 'Start' ;
            $next_button            = (isset($_REQUEST['ays_next_button']) && $_REQUEST['ays_next_button'] != '') ? stripslashes( sanitize_text_field( $_REQUEST['ays_next_button'] ) ) : 'Next' ;
            $previous_button        = (isset($_REQUEST['ays_previous_button']) && $_REQUEST['ays_previous_button'] != '') ? stripslashes( sanitize_text_field( $_REQUEST['ays_previous_button'] ) ) : 'Prev' ;
            $clear_button           = (isset($_REQUEST['ays_clear_button']) && $_REQUEST['ays_clear_button'] != '') ? stripslashes( sanitize_text_field( $_REQUEST['ays_clear_button'] ) ) : 'Clear' ;
            $finish_button          = (isset($_REQUEST['ays_finish_button']) && $_REQUEST['ays_finish_button'] != '') ? stripslashes( sanitize_text_field( $_REQUEST['ays_finish_button'] ) ) : 'Finish' ;
            $see_result_button      = (isset($_REQUEST['ays_see_result_button']) && $_REQUEST['ays_see_result_button'] != '') ? stripslashes( sanitize_text_field( $_REQUEST['ays_see_result_button'] ) ) : 'See Result' ;
            $restart_quiz_button    = (isset($_REQUEST['ays_restart_quiz_button']) && $_REQUEST['ays_restart_quiz_button'] != '') ? stripslashes( sanitize_text_field( $_REQUEST['ays_restart_quiz_button'] ) ) : 'Restart quiz' ;
            $send_feedback_button   = (isset($_REQUEST['ays_send_feedback_button']) && $_REQUEST['ays_send_feedback_button'] != '') ? stripslashes( sanitize_text_field( $_REQUEST['ays_send_feedback_button'] ) ) : 'Send feedback' ;
            $load_more_button       = (isset($_REQUEST['ays_load_more_button']) && $_REQUEST['ays_load_more_button'] != '') ? stripslashes( sanitize_text_field( $_REQUEST['ays_load_more_button'] ) ) : 'Load more' ;
            $exit_button            = (isset($_REQUEST['ays_exit_button']) && $_REQUEST['ays_exit_button'] != '') ? stripslashes( sanitize_text_field( $_REQUEST['ays_exit_button'] ) ) : 'Exit' ;
            $check_button           = (isset($_REQUEST['ays_check_button']) && $_REQUEST['ays_check_button'] != '') ? stripslashes( sanitize_text_field( $_REQUEST['ays_check_button'] ) ) : 'Check' ;
            $login_button           = (isset($_REQUEST['ays_login_button']) && $_REQUEST['ays_login_button'] != '') ? stripslashes( sanitize_text_field( $_REQUEST['ays_login_button'] ) ) : 'Log In' ;

            $buttons_texts = array(
                'start_button'          => $start_button,
                'next_button'           => $next_button,
                'previous_button'       => $previous_button,
                'clear_button'          => $clear_button,
                'finish_button'         => $finish_button,
                'see_result_button'     => $see_result_button,
                'restart_quiz_button'   => $restart_quiz_button,
                'send_feedback_button'  => $send_feedback_button,
                'load_more_button'      => $load_more_button,
                'exit_button'           => $exit_button,
                'check_button'          => $check_button,
                'login_button'          => $login_button,
            );

            $quiz_fields_placeholder_name  = (isset($_REQUEST['ays_quiz_fields_placeholder_name']) && $_REQUEST['ays_quiz_fields_placeholder_name'] != '') ? stripslashes( sanitize_text_field( $_REQUEST['ays_quiz_fields_placeholder_name'] ) ) : 'Name' ;

            $quiz_fields_placeholder_eamil = (isset($_REQUEST['ays_quiz_fields_placeholder_eamil']) && $_REQUEST['ays_quiz_fields_placeholder_eamil'] != '') ? stripslashes( sanitize_text_field( $_REQUEST['ays_quiz_fields_placeholder_eamil'] ) ) : 'Email' ;

            $quiz_fields_placeholder_phone = (isset($_REQUEST['ays_quiz_fields_placeholder_phone']) && $_REQUEST['ays_quiz_fields_placeholder_phone'] != '') ? stripslashes( sanitize_text_field( $_REQUEST['ays_quiz_fields_placeholder_phone'] ) ) : 'Phone Number' ;

            $quiz_fields_label_name  = (isset($_REQUEST['ays_quiz_fields_label_name']) && $_REQUEST['ays_quiz_fields_label_name'] != '') ? stripslashes( sanitize_text_field( $_REQUEST['ays_quiz_fields_label_name'] ) ) : 'Name' ;

            $quiz_fields_label_eamil = (isset($_REQUEST['ays_quiz_fields_label_eamil']) && $_REQUEST['ays_quiz_fields_label_eamil'] != '') ? stripslashes( sanitize_text_field( $_REQUEST['ays_quiz_fields_label_eamil'] ) ) : 'Email' ;

            $quiz_fields_label_phone = (isset($_REQUEST['ays_quiz_fields_label_phone']) && $_REQUEST['ays_quiz_fields_label_phone'] != '') ? stripslashes( sanitize_text_field( $_REQUEST['ays_quiz_fields_label_phone'] ) ) : 'Phone Number' ;

            $fields_placeholders = array(
                'quiz_fields_placeholder_name'   => $quiz_fields_placeholder_name,
                'quiz_fields_placeholder_eamil'  => $quiz_fields_placeholder_eamil,
                'quiz_fields_placeholder_phone'  => $quiz_fields_placeholder_phone,
                'quiz_fields_label_name'         => $quiz_fields_label_name,
                'quiz_fields_label_eamil'        => $quiz_fields_label_eamil,
                'quiz_fields_label_phone'        => $quiz_fields_label_phone,
            );
                        
            $question_default_type = isset($_REQUEST['ays_question_default_type']) ? esc_sql( sanitize_text_field( $_REQUEST['ays_question_default_type'] ) ) : '';                        
            $ays_answer_default_count = isset($_REQUEST['ays_answer_default_count']) ? esc_sql( sanitize_text_field( $_REQUEST['ays_answer_default_count'] ) ) : '';
            $right_answer_sound = isset($_REQUEST['ays_right_answer_sound']) ? esc_sql( sanitize_text_field( $_REQUEST['ays_right_answer_sound'] ) ) : '';
            $wrong_answer_sound = isset($_REQUEST['ays_wrong_answer_sound']) ? esc_sql( sanitize_text_field( $_REQUEST['ays_wrong_answer_sound'] ) ) : '';

            // Questions title length
            $question_title_length = (isset($_REQUEST['ays_question_title_length']) && intval($_REQUEST['ays_question_title_length']) != 0) ? absint(intval($_REQUEST['ays_question_title_length'])) : 5;

            //Quizzes title length
            $quizzes_title_length = (isset($_REQUEST['ays_quizzes_title_length']) && intval($_REQUEST['ays_quizzes_title_length']) != 0) ? absint(intval($_REQUEST['ays_quizzes_title_length'])) : 5;

            //Results title length
            $results_title_length = (isset($_REQUEST['ays_results_title_length']) && intval($_REQUEST['ays_results_title_length']) != 0) ? absint(intval($_REQUEST['ays_results_title_length'])) : 5;

            // Question categories title length
            $question_categories_title_length = (isset($_REQUEST['ays_question_categories_title_length']) && intval($_REQUEST['ays_question_categories_title_length']) != 0) ? absint(sanitize_text_field($_REQUEST['ays_question_categories_title_length'])) : 5;

            // Quiz categories title length
            $quiz_categories_title_length = (isset($_REQUEST['ays_quiz_categories_title_length']) && intval($_REQUEST['ays_quiz_categories_title_length']) != 0) ? absint(sanitize_text_field($_REQUEST['ays_quiz_categories_title_length'])) : 5;

            // Reviews title length
            $quiz_reviews_title_length = (isset($_REQUEST['ays_quiz_reviews_title_length']) && intval($_REQUEST['ays_quiz_reviews_title_length']) != 0) ? absint(sanitize_text_field($_REQUEST['ays_quiz_reviews_title_length'])) : 5;

            // Do not store IP adressess
            $disable_user_ip = (isset( $_REQUEST['ays_disable_user_ip'] ) && sanitize_text_field( $_REQUEST['ays_disable_user_ip'] ) == 'on') ? 'on' : 'off';

            // Animation Top
            $quiz_animation_top = (isset($_REQUEST['ays_quiz_animation_top']) && $_REQUEST['ays_quiz_animation_top'] != '') ? absint(intval($_REQUEST['ays_quiz_animation_top'])) : 100;
            $quiz_enable_animation_top = (isset( $_REQUEST['ays_quiz_enable_animation_top'] ) && sanitize_text_field( $_REQUEST['ays_quiz_enable_animation_top'] ) == 'on') ? 'on' : 'off';

            // All results column
            $all_results_columns = (isset($_REQUEST['ays_all_results_columns']) && !empty($_REQUEST['ays_all_results_columns'])) ? array_map( 'sanitize_text_field', $_REQUEST['ays_all_results_columns'] ) : array();
            $all_results_columns_order = (isset($_REQUEST['ays_all_results_columns_order']) && !empty($_REQUEST['ays_all_results_columns_order'])) ? $_REQUEST['ays_all_results_columns_order'] : array();

            // Question Category
            $question_default_category = isset($_REQUEST['ays_question_default_category']) ? absint(intval($_REQUEST['ays_question_default_category'])) : 1; 

            // Show publicly ( All Results )
            $all_results_show_publicly = (isset( $_REQUEST['ays_all_results_show_publicly'] ) && sanitize_text_field( $_REQUEST['ays_all_results_show_publicly'] ) == 'on') ? 'on' : 'off';

            // Show publicly ( Single Quiz Results )
            $quiz_all_results_show_publicly = (isset( $_REQUEST['ays_quiz_all_results_show_publicly'] ) && sanitize_text_field( $_REQUEST['ays_quiz_all_results_show_publicly'] ) == 'on') ? 'on' : 'off';

            // Quiz All results column
            $quiz_all_results_columns = (isset($_REQUEST['ays_quiz_all_results_columns']) && !empty($_REQUEST['ays_quiz_all_results_columns'])) ? array_map( 'sanitize_text_field', $_REQUEST['ays_quiz_all_results_columns'] ) : array();
            $quiz_all_results_columns_order = (isset($_REQUEST['ays_quiz_all_results_columns_order']) && !empty($_REQUEST['ays_quiz_all_results_columns_order'])) ? array_map( 'sanitize_text_field', $_REQUEST['ays_quiz_all_results_columns_order'] ) : array();

            // Enable question allow HTML
            $quiz_enable_question_allow_html = (isset( $_REQUEST['ays_quiz_enable_question_allow_html'] ) && sanitize_text_field( $_REQUEST['ays_quiz_enable_question_allow_html'] ) == 'on') ? 'on' : 'off';

            // Start button activation
            $enable_start_button_loader = (isset( $_REQUEST['ays_enable_start_button_loader'] ) && sanitize_text_field( $_REQUEST['ays_enable_start_button_loader'] ) == 'on') ? 'on' : 'off';
            
            // WP Editor height
            $quiz_wp_editor_height = (isset($_REQUEST['ays_quiz_wp_editor_height']) && $_REQUEST['ays_quiz_wp_editor_height'] != '' && $_REQUEST['ays_quiz_wp_editor_height'] != 0) ? absint( sanitize_text_field($_REQUEST['ays_quiz_wp_editor_height']) ) : 100 ;

            // Textarea height (public)
            $quiz_textarea_height = (isset($_REQUEST['ays_quiz_textarea_height']) && $_REQUEST['ays_quiz_textarea_height'] != '' && $_REQUEST['ays_quiz_textarea_height'] != 0 ) ? absint( sanitize_text_field($_REQUEST['ays_quiz_textarea_height']) ) : 100 ;

            // Show quiz button to Admins only
            $quiz_show_quiz_button_to_admin_only = (isset( $_REQUEST['ays_quiz_show_quiz_button_to_admin_only'] ) && sanitize_text_field( $_REQUEST['ays_quiz_show_quiz_button_to_admin_only'] ) == 'on') ? 'on' : 'off';

            // General CSS File
            $quiz_exclude_general_css = (isset( $_REQUEST['ays_quiz_exclude_general_css'] ) && sanitize_text_field( $_REQUEST['ays_quiz_exclude_general_css'] ) == 'on') ? 'on' : 'off';

            // Enable question answers
            $quiz_enable_question_answers = (isset( $_REQUEST['ays_quiz_enable_question_answers'] ) && sanitize_text_field( $_REQUEST['ays_quiz_enable_question_answers'] ) == 'on') ? 'on' : 'off';

            $options = array(
                "question_default_type"                 => $question_default_type,
                "ays_answer_default_count"              => $ays_answer_default_count,
                "right_answer_sound"                    => $right_answer_sound,
                "wrong_answer_sound"                    => $wrong_answer_sound,
                "question_title_length"                 => $question_title_length,
                "quizzes_title_length"                  => $quizzes_title_length,
                "results_title_length"                  => $results_title_length,
                "disable_user_ip"                       => $disable_user_ip,
                "quiz_animation_top"                    => $quiz_animation_top,
                "quiz_enable_animation_top"             => $quiz_enable_animation_top,
                "question_default_category"             => $question_default_category,
                "all_results_show_publicly"             => $all_results_show_publicly,
                "quiz_all_results_show_publicly"        => $quiz_all_results_show_publicly,

                // All results
                "all_results_columns"                   => $all_results_columns,
                "all_results_columns_order"             => $all_results_columns_order,

                // Quiz All results
                "quiz_all_results_columns"              => $quiz_all_results_columns,
                "quiz_all_results_columns_order"        => $quiz_all_results_columns_order,

                "quiz_enable_question_allow_html"       => $quiz_enable_question_allow_html,
                "enable_start_button_loader"            => $enable_start_button_loader,
                "quiz_wp_editor_height"                 => $quiz_wp_editor_height,
                "quiz_textarea_height"                  => $quiz_textarea_height,

                "quiz_show_quiz_button_to_admin_only"   => $quiz_show_quiz_button_to_admin_only,
                "question_categories_title_length"      => $question_categories_title_length,
                "quiz_categories_title_length"          => $quiz_categories_title_length,
                "quiz_reviews_title_length"             => $quiz_reviews_title_length,
                "quiz_exclude_general_css"              => $quiz_exclude_general_css,
                "quiz_enable_question_answers"          => $quiz_enable_question_answers,
            );
            
            $del_stat = "";
            $month_count = isset($_REQUEST['ays_delete_results_by']) ? intval( sanitize_text_field( $_REQUEST['ays_delete_results_by'] ) ) : null;
            if($month_count !== null && $month_count > 0){
                $year = intval( date( 'Y', current_time('timestamp') ) );
                $dt = intval( date( 'n', current_time('timestamp') ) );
                $month = $dt - $month_count;
                if($month < 0){
                    $month = 12 - $month;
                    if($month > 12){
                        $mn = $month % 12;
                        $mnac = ($month - $mn) / 12;
                        $month = 12 - ($mn);
                        $year -= $mnac;
                    }
                }elseif($month == 0){        
                    $month = 12;
                    $year--;
                }
                $sql = "DELETE FROM " . esc_sql( $wpdb->prefix ) . "aysquiz_reports 
                        WHERE YEAR(end_date) = '". esc_sql( $year ) ."' 
                          AND MONTH(end_date) <= '". esc_sql( $month ) ."'";
                $res = $wpdb->query($sql);
                if($res >= 0){
                    $del_stat = "&del_stat=ok&mcount=" . $month_count;
                }
            }
            
            $result = $this->ays_update_setting('buttons_texts', json_encode($buttons_texts));
            if ($result) {
                $success++;
            }
            $result = $this->ays_update_setting('fields_placeholders', json_encode($fields_placeholders));
            if ($result) {
                $success++;
            }
            $result = $this->ays_update_setting('options', json_encode($options));
            if ($result) {
                $success++;
            }

            $message = "saved";
            if($success > 0){
                $tab = "";
                if(isset($_REQUEST['ays_quiz_tab'])){
                    $tab = "&ays_quiz_tab=". sanitize_text_field( $_REQUEST['ays_quiz_tab'] );
                }
                $url = admin_url('admin.php') . "?page=quiz-maker-settings" . $tab . '&status=' . $message . $del_stat;
                wp_redirect( esc_url_raw( $url ) );
            }
        }
        
    }

    public function get_data(){
        $data = get_option( "ays_quiz_integrations" );
        if($data == null || $data == ''){
            return array();
        }else{
            return json_decode( get_option( "ays_quiz_integrations" ), true );
        }
    }

    public function get_db_data(){
        global $wpdb;
        $settings_table = $wpdb->prefix . "aysquiz_settings";
        $sql = "SELECT * FROM ".$settings_table;
        $results = $wpdb->get_results($sql, ARRAY_A);
        if(count($results) > 0){
            return $results;
        }else{
            return array();
        }
    }    
    
    public function check_settings_meta($metas){
        global $wpdb;
        $settings_table = $wpdb->prefix . "aysquiz_settings";
        foreach($metas as $meta_key){
            $sql = "SELECT COUNT(*) FROM ". esc_sql( $settings_table ) ." WHERE meta_key = '". esc_sql( sanitize_text_field( $meta_key ) )."'";
            $result = $wpdb->get_var($sql);
            if(intval($result) == 0){
                $this->ays_add_setting($meta_key, "", "", "");
            }
        }
        return false;
    }
    
    public function check_setting_user_roles(){
        global $wpdb;
        $settings_table = $wpdb->prefix . "aysquiz_settings";
        $sql = "SELECT COUNT(*) FROM ".$settings_table." WHERE meta_key = 'user_roles'";
        $result = $wpdb->get_var($sql);
        if(intval($result) == 0){
            $roles = json_encode(array('administrator'));
            $this->ays_add_setting("user_roles", $roles, "", "");
        }
        return false;
    }
        
    public function get_reports_titles(){
        global $wpdb;

        $sql = "SELECT {$wpdb->prefix}aysquiz_quizes.id,{$wpdb->prefix}aysquiz_quizes.title FROM {$wpdb->prefix}aysquiz_quizes";

        $result = $wpdb->get_results($sql, 'ARRAY_A');

        return $result;
    }
    
    public function ays_get_setting($meta_key){
        global $wpdb;
        $settings_table = $wpdb->prefix . "aysquiz_settings";

        if($wpdb->get_var("SHOW TABLES LIKE '$settings_table'") != $settings_table) {
            return false;
        }
        
        $sql = "SELECT meta_value FROM ".$settings_table." WHERE meta_key = '".$meta_key."'";
        $result = $wpdb->get_var($sql);
        if($result != ""){
            return $result;
        }
        return false;
    }
    
    public function ays_add_setting($meta_key, $meta_value, $note = "", $options = ""){
        global $wpdb;
        $settings_table = $wpdb->prefix . "aysquiz_settings";
        $result = $wpdb->insert(
            $settings_table,
            array(
                'meta_key'    => esc_sql( $meta_key ),
                'meta_value'  => esc_sql( $meta_value ),
                'note'        => esc_sql( $note ),
                'options'     => esc_sql( $options )
            ),
            array( '%s', '%s', '%s', '%s' )
        );
        if($result >= 0){
            return true;
        }
        return false;
    }
    
    public function ays_update_setting($meta_key, $meta_value, $note = null, $options = null){
        global $wpdb;
        $settings_table = $wpdb->prefix . "aysquiz_settings";
        $value = array(
            'meta_value'  => esc_sql( $meta_value ),
        );
        $value_s = array( '%s' );
        if($note != null){
            $value['note'] = esc_sql( $note );
            $value_s[] = '%s';
        }
        if($options != null){
            $value['options'] = esc_sql( $options );
            $value_s[] = '%s';
        }
        $result = $wpdb->update(
            $settings_table,
            $value,
            array( 'meta_key' => esc_sql( $meta_key ) ),
            $value_s,
            array( '%s' )
        );
        if($result >= 0){
            return true;
        }
        return false;
    }
    
    public function ays_delete_setting($meta_key){
        global $wpdb;
        $settings_table = $wpdb->prefix . "aysquiz_settings";
        $wpdb->delete(
            $settings_table,
            array( 'meta_key' => esc_sql( $meta_key ) ),
            array( '%s' )
        );
    }

    public function quiz_settings_notices($status){

        if ( empty( $status ) )
            return;

        if ( 'saved' == $status )
            $updated_message = esc_html( __( 'Changes saved.', $this->plugin_name ) );
        elseif ( 'updated' == $status )
            $updated_message = esc_html( __( 'Quiz attribute .', $this->plugin_name ) );
        elseif ( 'deleted' == $status )
            $updated_message = esc_html( __( 'Quiz attribute deleted.', $this->plugin_name ) );

        if ( empty( $updated_message ) )
            return;

        ?>
        <div class="notice notice-success is-dismissible">
            <p> <?php echo $updated_message; ?> </p>
        </div>
        <?php
    }
    
}
