<?php
ob_start();
class Quizes_List_Table extends WP_List_Table{
    private $plugin_name;
    private $title_length;
    /** Class constructor */
    public function __construct($plugin_name) {
        $this->plugin_name = $plugin_name;
        $this->title_length = Quiz_Maker_Admin::get_listtables_title_length('quizzes');
        parent::__construct( array(
            'singular' => __( 'Quiz', $this->plugin_name ), //singular name of the listed records
            'plural'   => __( 'Quizzes', $this->plugin_name ), //plural name of the listed records
            'ajax'     => false //does this table support ajax?
        ) );
        add_action( 'admin_notices', array( $this, 'quiz_notices' ) );
    }


    /**
     * Override of table nav to avoid breaking with bulk actions & according nonce field
     */
    public function display_tablenav( $which ) {
        ?>
        <div class="tablenav <?php echo esc_attr( $which ); ?>">
            
            <div class="alignleft actions">
                <?php  $this->bulk_actions( $which ); ?>
            </div>
            <?php
            $this->extra_tablenav( $which );
            $this->pagination( $which );
            ?>
            <br class="clear" />
        </div>
        <?php
    }

    /**
     * Disables the views for 'side' context as there's not enough free space in the UI
     * Only displays them on screen/browser refresh. Else we'd have to do this via an AJAX DB update.
     *
     * @see WP_List_Table::extra_tablenav()
     */
    public function extra_tablenav($which) {        
        global $wpdb;
        $titles_sql = "SELECT {$wpdb->prefix}aysquiz_quizcategories.title,{$wpdb->prefix}aysquiz_quizcategories.id FROM {$wpdb->prefix}aysquiz_quizcategories";
        $cat_titles = $wpdb->get_results($titles_sql);
        $cat_id = null;
        if( isset( $_GET['filterby'] )){
            $cat_id = absint( intval( $_GET['filterby'] ) );
        }
        $categories_select = array();
        foreach($cat_titles as $key => $cat_title){
            $selected = "";
            if($cat_id === intval($cat_title->id)){
                $selected = "selected";
            }
            $categories_select[$cat_title->id]['title'] = $cat_title->title;
            $categories_select[$cat_title->id]['selected'] = $selected;
            $categories_select[$cat_title->id]['id'] = $cat_title->id;
        }
        sort($categories_select);
        ?>
        <div id="category-filter-div-quizlist" class="alignleft actions bulkactions">
            <select name="filterby-<?php echo esc_attr( $which ); ?>" id="bulk-action-category-selector-<?php echo esc_attr( $which ); ?>">
                <option value=""><?php echo __('Select Category',$this->plugin_name)?></option>
                <?php
                    foreach($categories_select as $key => $cat_title){
                        echo "<option ".$cat_title['selected']." value='".$cat_title['id']."'>".$cat_title['title']."</option>";
                    }
                ?>
            </select>
            <input type="button" id="doaction-<?php echo esc_attr( $which ); ?>" class="cat-filter-apply-<?php echo esc_attr( $which ); ?> button" value="Filter">
        </div>
        
        <a style="" href="?page=<?php echo esc_attr( $_REQUEST['page'] ); ?>" class="button"><?php echo __( "Clear filters", $this->plugin_name ); ?></a>
        <?php
    }
    
    protected function get_views() {
        $published_count = $this->published_quizzes_count();
        $unpublished_count = $this->unpublished_quizzes_count();
        $all_count = $this->all_record_count();
        $selected_all = "";
        $selected_0 = "";
        $selected_1 = "";
        if( isset( $_REQUEST['fstatus'] ) && is_numeric( $_REQUEST['fstatus'] ) && ! is_null( sanitize_text_field( $_REQUEST['fstatus'] ) ) ){

            $fstatus  = absint( $_REQUEST['fstatus'] );

            switch( $fstatus ){
                case 0:
                    $selected_0 = " style='font-weight:bold;' ";
                    break;
                case 1:
                    $selected_1 = " style='font-weight:bold;' ";
                    break;
                default:
                    $selected_all = " style='font-weight:bold;' ";
                    break;
            }
        }else{
            $selected_all = " style='font-weight:bold;' ";
        }
        $status_links = array(
            "all" => "<a ".$selected_all." href='?page=".esc_attr( $_REQUEST['page'] )."'>". __( 'All', $this->plugin_name )." (".$all_count.")</a>",
            "published" => "<a ".$selected_1." href='?page=".esc_attr( $_REQUEST['page'] )."&fstatus=1'>". __( 'Published', $this->plugin_name )." (".$published_count.")</a>",
            "unpublished"   => "<a ".$selected_0." href='?page=".esc_attr( $_REQUEST['page'] )."&fstatus=0'>". __( 'Unpublished', $this->plugin_name )." (".$unpublished_count.")</a>"
        );
        return $status_links;
    }

    /**
     * Retrieve customers data from the database
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */
    public static function get_quizes( $per_page = 20, $page_number = 1, $search = '' ) {

        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_quizes";

        $where = array();

        if( $search != '' ){
            $where[] = $search;
        }

        if(! empty( $_REQUEST['filterby'] ) && absint( intval( $_REQUEST['filterby'] ) ) > 0){
            $cat_id = absint( intval( sanitize_text_field( $_REQUEST['filterby'] ) ) );
            $where[] = ' quiz_category_id = '.$cat_id.'';
        }

        if( isset( $_REQUEST['fstatus'] ) && is_numeric( $_REQUEST['fstatus'] ) && ! is_null( sanitize_text_field( $_REQUEST['fstatus'] ) ) ){
            if( esc_sql( $_REQUEST['fstatus'] ) != '' ){
                $fstatus  = absint( esc_sql( $_REQUEST['fstatus'] ) );
                $where[] = " published = ".$fstatus." ";
            }
        }
        
        if( ! empty($where) ){
            $sql .= " WHERE " . implode( " AND ", $where );
        }

        if ( ! empty( $_REQUEST['orderby'] ) ) {

            $order_by  = ( isset( $_REQUEST['orderby'] ) && sanitize_text_field( $_REQUEST['orderby'] ) != '' ) ? sanitize_text_field( $_REQUEST['orderby'] ) : 'ordering';
            $order_by .= ( ! empty( $_REQUEST['order'] ) && strtolower( $_REQUEST['order'] ) == 'asc' ) ? ' ASC' : ' DESC';

            $sql_orderby = sanitize_sql_orderby($order_by);

            if ( $sql_orderby ) {
                $sql .= ' ORDER BY ' . $sql_orderby;
            } else {
                $sql .= ' ORDER BY ordering DESC';
            }
        }else{
            $sql .= ' ORDER BY ordering DESC';
        }
        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

        $result = $wpdb->get_results( $sql, 'ARRAY_A' );

        return $result;
    }


    public function get_published_questions(){
        global $wpdb;
        $sql = "SELECT 
                    {$wpdb->prefix}aysquiz_questions.id, 
                    {$wpdb->prefix}aysquiz_questions.question, 
                    {$wpdb->prefix}aysquiz_questions.type, 
                    {$wpdb->prefix}aysquiz_questions.create_date,
                    {$wpdb->prefix}aysquiz_questions.question_image,
                    {$wpdb->prefix}aysquiz_questions.options,
                    {$wpdb->prefix}aysquiz_categories.title
                FROM {$wpdb->prefix}aysquiz_questions
                INNER JOIN {$wpdb->prefix}aysquiz_categories
                ON {$wpdb->prefix}aysquiz_questions.category_id={$wpdb->prefix}aysquiz_categories.id
                WHERE {$wpdb->prefix}aysquiz_questions.published = 1 ORDER BY id DESC;";

        $results = $wpdb->get_results( $sql, 'ARRAY_A' );

        return $results;

    }

    public function get_quiz_categories(){
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_quizcategories";

        $result = $wpdb->get_results($sql, 'ARRAY_A');

        return $result;
    }

    public function get_question_categories(){
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_categories";

        $result = $wpdb->get_results($sql, 'ARRAY_A');

        return $result;
    }

    public function get_published_questions_by($key, $value) {
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_questions WHERE {$key} = {$value};";

        $results = $wpdb->get_row( $sql, 'ARRAY_A' );

        return $results;

    }

    public function get_quiz_by_id( $id ){
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_quizes WHERE id=" . absint( intval( $id ) );

        $result = $wpdb->get_row($sql, 'ARRAY_A');

        return $result;
    }

    public function add_or_edit_quizes(){
        global $wpdb;
        $quiz_table = $wpdb->prefix . 'aysquiz_quizes';
        $ays_change_type = (isset($_POST['ays_change_type'])) ? sanitize_text_field( $_POST['ays_change_type'] ) : '';
        if( isset($_POST["quiz_action"]) && wp_verify_nonce( sanitize_text_field( $_POST["quiz_action"] ), 'quiz_action' ) ){
            
            $id                         = ( $_POST["id"] != NULL ) ? absint( intval( $_POST["id"] ) ) : null;
            $max_id                     = $this->get_max_id();
            $title                      = sanitize_text_field( $_POST['ays_quiz_title'] );
            $description                = wp_kses_post( $_POST['ays_quiz_description'] );
            $quiz_category_id           = absint( sanitize_text_field( $_POST['ays_quiz_category'] ) );
            $question_ids               = sanitize_text_field( $_POST['ays_added_questions'] );
            $published                  = absint( sanitize_text_field( $_POST['ays_publish'] ) );
            $ordering                   = ( $max_id != NULL ) ? ( $max_id + 1 ) : 1;
            $image                      = sanitize_text_field( $_POST['ays_quiz_image'] );
            
            if(isset($_POST['ays_enable_restriction_pass']) && sanitize_text_field( $_POST['ays_enable_restriction_pass'] ) == "on"){
                $ays_enable_logged_users = "on";
            }elseif(isset($_POST['ays_enable_logged_users']) && sanitize_text_field( $_POST['ays_enable_logged_users'] ) == "on"){
                $ays_enable_logged_users = "on";
            }else{
                $ays_enable_logged_users = "off";
            }
            
            $ays_form_name              = !isset($_POST['ays_form_name']) ? null : sanitize_text_field( $_POST['ays_form_name'] );
            $ays_form_email             = !isset($_POST['ays_form_email']) ? null : sanitize_text_field( $_POST['ays_form_email'] );
            $ays_form_phone             = !isset($_POST['ays_form_phone']) ? null : sanitize_text_field( $_POST['ays_form_phone'] );
            $enable_correction          = !isset($_POST['ays_enable_correction']) ? "off" : sanitize_text_field( $_POST['ays_enable_correction'] );
            $enable_progressbar         = !isset($_POST['ays_enable_progress_bar']) ? "off" : sanitize_text_field( $_POST['ays_enable_progress_bar'] );
            $enable_questions_result    = !isset($_POST['ays_enable_questions_result']) ? "off" : sanitize_text_field( $_POST['ays_enable_questions_result'] );
            $enable_random_questions    = !isset($_POST['ays_enable_randomize_questions']) ? "off" : sanitize_text_field( $_POST['ays_enable_randomize_questions'] );
            $enable_random_answers      = !isset($_POST['ays_enable_randomize_answers']) ? "off" : sanitize_text_field( $_POST['ays_enable_randomize_answers'] );
            $enable_questions_counter   = !isset($_POST['ays_enable_questions_counter']) ? "off" : sanitize_text_field( $_POST['ays_enable_questions_counter'] );
            $enable_questions_score     = !isset($_POST['ays_enable_questions_score']) ? "off" : sanitize_text_field( $_POST['ays_enable_questions_score'] );
            $enable_restriction_pass    = !isset($_POST['ays_enable_restriction_pass']) ? "off" : sanitize_text_field( $_POST['ays_enable_restriction_pass'] );
            $limit_users                = !isset($_POST['ays_limit_users']) ? "off" : sanitize_text_field( $_POST['ays_limit_users'] );
            $enable_rtl                 = !isset($_POST['ays_enable_rtl_direction']) ? "off" : sanitize_text_field( $_POST['ays_enable_rtl_direction'] );
            $question_bank              = !isset($_POST['ays_enable_question_bank']) ? "off" : sanitize_text_field( $_POST['ays_enable_question_bank'] );
            $live_progressbar           = !isset($_POST['ays_enable_live_progress_bar']) ? "off" : sanitize_text_field( $_POST['ays_enable_live_progress_bar'] );
            $percent_view               = !isset($_POST['ays_enable_percent_view']) ? "off" : sanitize_text_field( $_POST['ays_enable_percent_view'] );
            $avarage_statistical        = !isset($_POST['ays_enable_average_statistical']) ? "off" : sanitize_text_field( $_POST['ays_enable_average_statistical'] );
            $next_button                = !isset($_POST['ays_enable_next_button']) ? "off" : sanitize_text_field( $_POST['ays_enable_next_button'] );
            $prev_button                = !isset($_POST['ays_enable_previous_button']) ? "off" : sanitize_text_field( $_POST['ays_enable_previous_button'] );
            $enable_arrows              = !isset($_POST['ays_enable_arrows']) ? "off" : sanitize_text_field( $_POST['ays_enable_arrows'] );
            $quiz_theme                 = !isset($_POST['ays_quiz_theme']) ? null : sanitize_text_field( $_POST['ays_quiz_theme'] );
            $social_buttons             = !isset($_POST['ays_social_buttons']) ? "off" : sanitize_text_field( $_POST['ays_social_buttons'] );
            $enable_logged_users_mas    = !isset($_POST['ays_enable_logged_users_message']) ? null : wp_kses_post( $_POST['ays_enable_logged_users_message'] );
            $enable_pass_count          = !isset($_POST['ays_enable_pass_count']) ? "off" : sanitize_text_field( $_POST['ays_enable_pass_count'] );
            $hide_score                 = !isset($_POST['ays_hide_score']) ? "off" : sanitize_text_field( $_POST['ays_hide_score'] );
            $rate_form_title            = !isset($_POST['ays_rate_form_title']) ? '' : wp_kses_post( $_POST['ays_rate_form_title'] );
            $quiz_box_shadow_color      = !isset($_POST['ays_quiz_box_shadow_color']) ? '' : sanitize_text_field( $_POST['ays_quiz_box_shadow_color'] );
            $quiz_border_radius         = !isset($_POST['ays_quiz_border_radius']) ? '' : sanitize_text_field( $_POST['ays_quiz_border_radius'] );
            $quiz_bg_image              = !isset($_POST['ays_quiz_bg_image']) ? '' : sanitize_text_field( $_POST['ays_quiz_bg_image'] );
            $quiz_border_width          = !isset($_POST['ays_quiz_border_width']) ? '' : sanitize_text_field( $_POST['ays_quiz_border_width'] );
            $quiz_border_style          = !isset($_POST['ays_quiz_border_style']) ? '' : sanitize_text_field( $_POST['ays_quiz_border_style'] );
            $quiz_border_color          = !isset($_POST['ays_quiz_border_color']) ? '' : sanitize_text_field( $_POST['ays_quiz_border_color'] );          
            $quiz_loader                = !isset($_POST['ays_quiz_loader']) ? '' : sanitize_text_field( $_POST['ays_quiz_loader'] );
            
            // $quiz_create_date           = !isset($_POST['ays_quiz_ctrate_date']) ? '0000-00-00 00:00:00' : sanitize_text_field( $_POST['ays_quiz_ctrate_date'] );

            // Change current quiz creation date
            $quiz_create_date           = (isset($_POST['ays_quiz_change_creation_date']) && $_POST['ays_quiz_change_creation_date'] != '') ? sanitize_text_field($_POST['ays_quiz_change_creation_date']) : current_time( 'mysql' ) ;

            $quest_animation            = !isset($_POST['ays_quest_animation']) ? 'shake' : sanitize_text_field( $_POST['ays_quest_animation'] );
            $author = ( isset($_POST['ays_quiz_author']) && $_POST['ays_quiz_author'] != "" ) ? stripcslashes( sanitize_text_field( $_POST['ays_quiz_author'] ) ) : '';

            // Change the author of the current quiz
            $quiz_create_author = ( isset($_POST['ays_quiz_create_author']) && $_POST['ays_quiz_create_author'] != "" ) ? absint( sanitize_text_field( $_POST['ays_quiz_create_author'] ) ) : '';

            if ( $quiz_create_author != "" && $quiz_create_author > 0 ) {
                $user = get_userdata($quiz_create_author);
                if ( ! is_null( $user ) && $user ) {
                    $quiz_author = array(
                        'id' => $user->ID."",
                        'name' => $user->data->display_name
                    );

                    $author = json_encode($quiz_author, JSON_UNESCAPED_SLASHES);
                } else {
                    $author_data = json_decode($author, true);
                    $quiz_create_author = (isset( $author_data['id'] ) && $author_data['id'] != "") ? absint( sanitize_text_field( $author_data['id'] ) ) : get_current_user_id();
                }
            }
            
            $form_title                 = !isset($_POST['ays_form_title']) ? '' : wp_kses_post( $_POST['ays_form_title'] );
            $limit_user_roles           = !isset($_POST['ays_users_roles']) ? array() : array_map( 'sanitize_text_field', $_POST['ays_users_roles'] );
            
            $enable_bg_music            = (isset($_POST['ays_enable_bg_music']) && sanitize_text_field( $_POST['ays_enable_bg_music'] ) == "on") ? "on" : "off";
            $quiz_bg_music              = (isset($_POST['ays_quiz_bg_music']) && sanitize_text_field( $_POST['ays_quiz_bg_music'] ) != "") ? sanitize_text_field( $_POST['ays_quiz_bg_music'] ) : "";
            $answers_font_size          = (isset($_POST['ays_answers_font_size']) && sanitize_text_field( $_POST['ays_answers_font_size'] ) != "" && absint( sanitize_text_field( $_POST['ays_answers_font_size'] ) ) > 0) ? absint( sanitize_text_field( $_POST['ays_answers_font_size'] ) ) : "15";
            
            $show_create_date = (isset($_POST['ays_show_create_date']) && sanitize_text_field( $_POST['ays_show_create_date'] ) == "on") ? "on" : "off";
            $show_author = (isset($_POST['ays_show_author']) && sanitize_text_field( $_POST['ays_show_author'] ) == "on") ? "on" : "off";
            $enable_early_finish = (isset($_POST['ays_enable_early_finish']) && sanitize_text_field( $_POST['ays_enable_early_finish'] ) == "on") ? "on" : "off";
            $answers_rw_texts = isset($_POST['ays_answers_rw_texts']) ? sanitize_text_field( $_POST['ays_answers_rw_texts'] ) : 'on_passing';
            $disable_store_data = (isset($_POST['ays_disable_store_data']) && sanitize_text_field( $_POST['ays_disable_store_data'] ) == "on") ? "on" : "off";
            
            // Background gradient
            $enable_background_gradient = ( isset( $_POST['ays_enable_background_gradient'] ) && sanitize_text_field( $_POST['ays_enable_background_gradient'] ) == 'on' ) ? 'on' : 'off';
            $quiz_background_gradient_color_1 = !isset($_POST['ays_background_gradient_color_1']) ? '' : sanitize_text_field( $_POST['ays_background_gradient_color_1'] );
            $quiz_background_gradient_color_2 = !isset($_POST['ays_background_gradient_color_2']) ? '' : sanitize_text_field( $_POST['ays_background_gradient_color_2'] );
            $quiz_gradient_direction = !isset($_POST['ays_quiz_gradient_direction']) ? '' : sanitize_text_field( $_POST['ays_quiz_gradient_direction'] );
            
            // Redirect after submit
            $redirect_after_submit = ( isset( $_POST['ays_redirect_after_submit'] ) && sanitize_text_field( $_POST['ays_redirect_after_submit'] ) == 'on' ) ? 'on' : 'off';
            $submit_redirect_url = !isset($_POST['ays_submit_redirect_url']) ? '' : sanitize_text_field( $_POST['ays_submit_redirect_url'] );
            $submit_redirect_delay = !isset($_POST['ays_submit_redirect_delay']) ? '' : sanitize_text_field( absint( $_POST['ays_submit_redirect_delay'] ) );

            // Progress bar
            $progress_bar_style = (isset($_POST['ays_progress_bar_style']) && sanitize_text_field( $_POST['ays_progress_bar_style'] ) != "") ? sanitize_text_field( $_POST['ays_progress_bar_style'] ) : 'first';

            // EXIT button in finish page
            $enable_exit_button = (isset($_POST['ays_enable_exit_button']) && sanitize_text_field( $_POST['ays_enable_exit_button'] ) == 'on') ? "on" : "off";
            $exit_redirect_url = isset($_POST['ays_exit_redirect_url']) ? sanitize_text_field( $_POST['ays_exit_redirect_url'] ) : '';

            // Question image sizing
            $image_sizing = (isset($_POST['ays_image_sizing']) && sanitize_text_field( $_POST['ays_image_sizing'] ) != "") ? sanitize_text_field( $_POST['ays_image_sizing'] ) : 'cover';

            // Quiz background image position
            $quiz_bg_image_position = (isset($_POST['ays_quiz_bg_image_position']) && sanitize_text_field( $_POST['ays_quiz_bg_image_position'] ) != "") ? sanitize_text_field( $_POST['ays_quiz_bg_image_position'] ) : 'center center';

            // Custom class for quiz container
            $custom_class = (isset($_POST['ays_custom_class']) && sanitize_text_field( $_POST['ays_custom_class'] ) != "") ? sanitize_text_field( $_POST['ays_custom_class'] ) : '';

            // Social Media links
            $enable_social_links = (isset($_POST['ays_enable_social_links']) && sanitize_text_field( $_POST['ays_enable_social_links'] ) == "on") ? 'on' : 'off';
            $ays_social_links = (isset($_POST['ays_social_links'])) ? array_map( 'sanitize_text_field', $_POST['ays_social_links'] ) : array(
                'linkedin_link' => '',
                'facebook_link' => '',
                'twitter_link' => '',
                'vkontakte_link' => '',
                'instagram_link' => '',
                'youtube_link' => '',
            );
            
            $linkedin_link = isset($ays_social_links['ays_linkedin_link']) && sanitize_text_field( $ays_social_links['ays_linkedin_link'] ) != '' ? sanitize_text_field( $ays_social_links['ays_linkedin_link'] ) : '';
            $facebook_link = isset($ays_social_links['ays_facebook_link']) && sanitize_text_field( $ays_social_links['ays_facebook_link'] ) != '' ? sanitize_text_field( $ays_social_links['ays_facebook_link'] ) : '';
            $twitter_link = isset($ays_social_links['ays_twitter_link']) && sanitize_text_field( $ays_social_links['ays_twitter_link'] ) != '' ? sanitize_text_field( $ays_social_links['ays_twitter_link'] ) : '';
            $vkontakte_link = isset($ays_social_links['ays_vkontakte_link']) && sanitize_text_field( $ays_social_links['ays_vkontakte_link'] ) != '' ? sanitize_text_field( $ays_social_links['ays_vkontakte_link'] ) : '';
            $instagram_link = isset($ays_social_links['ays_instagram_link']) && sanitize_text_field( $ays_social_links['ays_instagram_link'] ) != '' ? sanitize_text_field( $ays_social_links['ays_instagram_link'] ) : '';
            $youtube_link = isset($ays_social_links['ays_youtube_link']) && sanitize_text_field( $ays_social_links['ays_youtube_link'] ) != '' ? sanitize_text_field( $ays_social_links['ays_youtube_link'] ) : '';
            
            $social_links = array(
                'linkedin_link'     => $linkedin_link,
                'facebook_link'     => $facebook_link,
                'twitter_link'      => $twitter_link,
                'vkontakte_link'    => $vkontakte_link,
                'instagram_link'    => $instagram_link,
                'youtube_link'      => $youtube_link,
            );

            // Show quiz head information. Quiz title and description            
            $show_quiz_title = (isset($_POST['ays_show_quiz_title']) && sanitize_text_field( $_POST['ays_show_quiz_title'] ) == "on") ? 'on' : 'off';
            $show_quiz_desc = (isset($_POST['ays_show_quiz_desc']) && sanitize_text_field( $_POST['ays_show_quiz_desc'] ) == "on") ? 'on' : 'off';

            // Show login form for not logged in users
            $show_login_form = (isset($_POST['ays_show_login_form']) && sanitize_text_field( $_POST['ays_show_login_form'] ) == "on") ? 'on' : 'off';

            // Quiz container max-width for mobile
            $mobile_max_width = (isset($_POST['ays_mobile_max_width']) && sanitize_text_field( $_POST['ays_mobile_max_width'] ) != "") ? sanitize_text_field( $_POST['ays_mobile_max_width'] ) : '';

            // Limit users by option
            $limit_users_by = (isset($_POST['ays_limit_users_by']) && sanitize_text_field( $_POST['ays_limit_users_by'] ) != '') ? sanitize_text_field( $_POST['ays_limit_users_by'] ) : 'ip';

            // Schedule quiz
			$active_date_check = (isset($_POST['active_date_check']) && sanitize_text_field( $_POST['active_date_check'] ) == "on") ? 'on' : 'off';
			$activeInterval = isset($_POST['ays-active']) ? sanitize_text_field( $_POST['ays-active'] ) : "";
			$deactiveInterval = isset($_POST['ays-deactive']) ? sanitize_text_field( $_POST['ays-deactive'] ) : "";
            $active_date_pre_start_message = wp_kses_post( $_POST['active_date_pre_start_message'] );
            $active_date_message = wp_kses_post( $_POST['active_date_message'] );
            
            // Right/wrong answer text showing time option
            $explanation_time = (isset($_POST['ays_explanation_time']) && sanitize_text_field( $_POST['ays_explanation_time'] ) != '') ? sanitize_text_field( $_POST['ays_explanation_time'] ) : '4';

            // Enable claer answer button
            $enable_clear_answer = (isset($_POST['ays_enable_clear_answer']) && sanitize_text_field( $_POST['ays_enable_clear_answer'] ) == "on") ? 'on' : 'off';

            // Show quiz category
            $show_category = (isset($_POST['ays_show_category']) && sanitize_text_field( $_POST['ays_show_category'] ) == "on") ? 'on' : 'off';

            // Show question category
            $show_question_category = (isset($_POST['ays_show_question_category']) && sanitize_text_field( $_POST['ays_show_question_category'] ) == "on") ? 'on' : 'off';

            // Display score option
            $display_score = (isset($_POST['ays_display_score']) && sanitize_text_field( $_POST['ays_display_score'] ) != "") ? sanitize_text_field( $_POST['ays_display_score'] ) : 'by_percantage';

            // Right / Wrong answers sound option
            $enable_rw_asnwers_sounds = (isset($_POST['ays_enable_rw_asnwers_sounds']) && sanitize_text_field( $_POST['ays_enable_rw_asnwers_sounds'] ) == "on") ? 'on' : 'off';

            // Answers right/wrong answers icons
            $ans_right_wrong_icon = (isset($_POST['ays_ans_right_wrong_icon']) && sanitize_text_field( $_POST['ays_ans_right_wrong_icon'] ) != '') ? sanitize_text_field( $_POST['ays_ans_right_wrong_icon'] ) : 'default';

            // Hide quiz background image on the result page
            $quiz_bg_img_in_finish_page = (isset($_POST['ays_quiz_bg_img_in_finish_page']) && sanitize_text_field( $_POST['ays_quiz_bg_img_in_finish_page'] ) == "on") ? 'on' : 'off';

            // Finish the quiz after making one wrong answer
            $finish_after_wrong_answer = (isset($_POST['ays_finish_after_wrong_answer']) && sanitize_text_field( $_POST['ays_finish_after_wrong_answer'] ) == "on") ? 'on' : 'off';
            
            // Text after timer ends
            $after_timer_text = (isset($_POST['ays_after_timer_text']) && $_POST['ays_after_timer_text'] != '') ? wp_kses_post( $_POST['ays_after_timer_text'] ) : '';
            
            // Enable to go next by pressing Enter key
            $enable_enter_key = (isset($_POST['ays_enable_enter_key']) && sanitize_text_field( $_POST['ays_enable_enter_key'] ) == "on") ? 'on' : 'off';
            
            // Buttons text color
            $buttons_text_color = (isset($_POST['ays_buttons_text_color']) && sanitize_text_field( $_POST['ays_buttons_text_color'] ) != "") ? sanitize_text_field( $_POST['ays_buttons_text_color'] ) : '#000';
            
            // Buttons position
            $buttons_position = (isset($_POST['ays_buttons_position']) && sanitize_text_field( $_POST['ays_buttons_position'] ) != "") ? sanitize_text_field( $_POST['ays_buttons_position'] ) : 'center';
            
            // Show questions explanation on
            $show_questions_explanation = (isset($_POST['ays_show_questions_explanation']) && sanitize_text_field( $_POST['ays_show_questions_explanation'] ) != '') ? sanitize_text_field( $_POST['ays_show_questions_explanation'] ) : 'on_results_page';

            // Enable audio autoplay
            $enable_audio_autoplay = (isset($_POST['ays_enable_audio_autoplay']) && sanitize_text_field( $_POST['ays_enable_audio_autoplay'] ) == 'on') ? 'on' : 'off';

            // =========== Buttons Styles Start ===========

            // Buttons size
            $buttons_size = (isset($_POST['ays_buttons_size']) && sanitize_text_field( $_POST['ays_buttons_size'] ) != "") ? sanitize_text_field( $_POST['ays_buttons_size'] ) : 'medium';

            // Buttons font size
            $buttons_font_size = (isset($_POST['ays_buttons_font_size']) && sanitize_text_field( $_POST['ays_buttons_font_size'] ) != "") ? sanitize_text_field( $_POST['ays_buttons_font_size'] ) : '17';

            // Buttons font size
            $buttons_width = (isset($_POST['ays_buttons_width']) && sanitize_text_field( $_POST['ays_buttons_width'] ) != "") ? sanitize_text_field( $_POST['ays_buttons_width'] ) : '';

            // Buttons Left / Right padding
            $buttons_left_right_padding = (isset($_POST['ays_buttons_left_right_padding']) && sanitize_text_field( $_POST['ays_buttons_left_right_padding'] ) != "") ? sanitize_text_field( $_POST['ays_buttons_left_right_padding'] ) : '20';

            // Buttons Top / Bottom padding
            $buttons_top_bottom_padding = (isset($_POST['ays_buttons_top_bottom_padding']) && sanitize_text_field( $_POST['ays_buttons_top_bottom_padding'] ) != "") ? sanitize_text_field( $_POST['ays_buttons_top_bottom_padding'] ) : '10';

            // Buttons padding
            $buttons_border_radius = (isset($_POST['ays_buttons_border_radius']) && sanitize_text_field( $_POST['ays_buttons_border_radius'] ) != "") ? sanitize_text_field( $_POST['ays_buttons_border_radius'] ) : '3';

            // =========== Buttons Styles End ===========

            // Enable leave page
            $enable_leave_page = (isset($_POST['ays_enable_leave_page']) && sanitize_text_field( $_POST['ays_enable_leave_page'] ) == "on") ? 'on' : 'off';

            // Limitation tackers of quiz
            $enable_tackers_count = (isset($_POST['ays_enable_tackers_count']) && sanitize_text_field( $_POST['ays_enable_tackers_count'] ) == 'on') ? 'on' : 'off';
            $tackers_count = (isset($_POST['ays_tackers_count']) && sanitize_text_field( $_POST['ays_tackers_count'] ) != '') ? sanitize_text_field( $_POST['ays_tackers_count'] ) : '';

            // Pass Score
            $pass_score = (isset($_POST['ays_pass_score']) && $_POST['ays_pass_score'] != '') ? absint(intval($_POST['ays_pass_score'])) : '0';
            $pass_score_message = isset($_POST['ays_pass_score_message']) ? wp_kses_post( $_POST['ays_pass_score_message'] ) : '<h4 style="text-align: center;">'. __("Congratulations!", $this->plugin_name) .'</h4><p style="text-align: center;">'. __("You passed the quiz!", $this->plugin_name) .'</p>';
            $fail_score_message = isset($_POST['ays_fail_score_message']) ? wp_kses_post( $_POST['ays_fail_score_message'] ) : '<h4 style="text-align: center;">'. __("Oops!", $this->plugin_name) .'</h4><p style="text-align: center;">'. __("You have not passed the quiz! <br> Try again!", $this->plugin_name) .'</p>';

            // Question Font Size
            $question_font_size = (isset($_POST['ays_question_font_size']) && $_POST['ays_question_font_size'] != '' && absint(sanitize_text_field($_POST['ays_question_font_size'])) > 0) ? absint(sanitize_text_field($_POST['ays_question_font_size'])) : '16';

            // Quiz Width by percentage or pixels
            $quiz_width_by_percentage_px = (isset($_POST['ays_quiz_width_by_percentage_px']) && $_POST['ays_quiz_width_by_percentage_px'] != '') ? sanitize_text_field( $_POST['ays_quiz_width_by_percentage_px'] ) : 'pixels';

            // Text instead of question hint
            $questions_hint_icon_or_text = (isset($_POST['ays_questions_hint_icon_or_text']) && $_POST['ays_questions_hint_icon_or_text'] != '') ? sanitize_text_field( $_POST['ays_questions_hint_icon_or_text'] ) : 'default';
            $questions_hint_value = (isset($_POST['ays_questions_hint_value']) && $_POST['ays_questions_hint_value'] != '') ? sanitize_text_field( $_POST['ays_questions_hint_value'] ) : '';

            // Enable Finish Button Comfirm Box 
            $enable_early_finsh_comfirm_box = (isset($_POST['ays_enable_early_finsh_comfirm_box']) && sanitize_text_field( $_POST['ays_enable_early_finsh_comfirm_box'] ) == "on") ? 'on' : 'off';

            // Enable questions ordering by category 
            $enable_questions_ordering_by_cat = (isset($_POST['ays_enable_questions_ordering_by_cat']) && sanitize_text_field( $_POST['ays_enable_questions_ordering_by_cat'] ) == "on") ? 'on' : 'off';

            // Show schedule timer
            $show_schedule_timer = (isset($_POST['ays_quiz_show_timer']) && sanitize_text_field( $_POST['ays_quiz_show_timer'] ) == 'on') ? 'on' : 'off';
            $show_timer_type = (isset($_POST['ays_show_timer_type']) && sanitize_text_field( $_POST['ays_show_timer_type'] ) != '') ? sanitize_text_field( $_POST['ays_show_timer_type'] ) : 'countdown';

            // Quiz loader text value
            $quiz_loader_text_value = (isset($_POST['ays_quiz_loader_text_value']) && $_POST['ays_quiz_loader_text_value'] != '') ? sanitize_text_field( $_POST['ays_quiz_loader_text_value'] ) : '';

            // Hide correct answers
            $hide_correct_answers = (isset($_POST['ays_hide_correct_answers']) && sanitize_text_field( $_POST['ays_hide_correct_answers'] ) == 'on') ? 'on' : 'off';

            // Show information form to logged in users
            $show_information_form = (isset($_POST['ays_show_information_form']) && sanitize_text_field( $_POST['ays_show_information_form'] ) == 'on') ? 'on' : 'off';
            
            // Quiz loader text value
            $quiz_loader_custom_gif = (isset($_POST['ays_quiz_loader_custom_gif']) && $_POST['ays_quiz_loader_custom_gif'] != '') ? sanitize_text_field( $_POST['ays_quiz_loader_custom_gif'] ) : '';

            if ($quiz_loader_custom_gif != '' && exif_imagetype( $quiz_loader_custom_gif ) != IMAGETYPE_GIF) {
                $quiz_loader_custom_gif = '';
            }

            // Disable answer hover
            $disable_hover_effect = (isset($_POST['ays_disable_hover_effect']) && sanitize_text_field( $_POST['ays_disable_hover_effect'] ) == 'on') ? 'on' : 'off';

            //  Quiz loader custom gif width
            $quiz_loader_custom_gif_width = (isset($_POST['ays_quiz_loader_custom_gif_width']) && sanitize_text_field( $_POST['ays_quiz_loader_custom_gif_width'] ) != '') ? absint( intval( $_POST['ays_quiz_loader_custom_gif_width'] ) ) : 100;

            // Progress live bar style
            $progress_live_bar_style = (isset($_POST['ays_progress_live_bar_style']) && sanitize_text_field( $_POST['ays_progress_live_bar_style'] ) != "") ? sanitize_text_field( $_POST['ays_progress_live_bar_style'] ) : 'default';

            // Quiz title transformation
            $quiz_title_transformation = (isset($_POST['ays_quiz_title_transformation']) && sanitize_text_field( $_POST['ays_quiz_title_transformation'] ) != "") ? sanitize_text_field( $_POST['ays_quiz_title_transformation'] ) : 'uppercase';

            // Show questions numbering 
            $show_answers_numbering = (isset($_POST['ays_show_answers_numbering']) && sanitize_text_field( $_POST['ays_show_answers_numbering']) != '') ? sanitize_text_field( $_POST['ays_show_answers_numbering'] ) : 'none';

            // Image Width(px)
            $image_width = (isset($_POST['ays_image_width']) && sanitize_text_field($_POST['ays_image_width']) != '') ? absint( sanitize_text_field($_POST['ays_image_width']) ) : '';

            // Quiz image width percentage/px
            $quiz_image_width_by_percentage_px = (isset($_POST['ays_quiz_image_width_by_percentage_px']) && sanitize_text_field( $_POST['ays_quiz_image_width_by_percentage_px']) != '') ? sanitize_text_field( $_POST['ays_quiz_image_width_by_percentage_px'] ) : 'pixels';

            // Quiz image height
            $quiz_image_height = (isset($_POST['ays_quiz_image_height']) && sanitize_text_field( $_POST['ays_quiz_image_height']) != '') ? absint( sanitize_text_field( $_POST['ays_quiz_image_height'] ) ) : '';

            // Hide background image on start page
            $quiz_bg_img_on_start_page = (isset($_POST['ays_quiz_bg_img_on_start_page']) && sanitize_text_field( $_POST['ays_quiz_bg_img_on_start_page'] ) == 'on') ? 'on' : 'off';

            if( function_exists( 'sanitize_textarea_field' ) ){
                $custom_css = stripslashes( esc_attr( sanitize_textarea_field( $_POST['ays_custom_css'] ) ) );
            }else{
                $custom_css = stripslashes( esc_attr( sanitize_text_field( $_POST['ays_custom_css'] ) ) );
            }

            // Box Shadow X offset
            $quiz_box_shadow_x_offset = (isset($_POST['ays_quiz_box_shadow_x_offset']) && sanitize_text_field( $_POST['ays_quiz_box_shadow_x_offset'] ) != '') ? intval( sanitize_text_field( $_POST['ays_quiz_box_shadow_x_offset'] ) ) : 0;

            // Box Shadow Y offset
            $quiz_box_shadow_y_offset = (isset($_POST['ays_quiz_box_shadow_y_offset']) && sanitize_text_field( $_POST['ays_quiz_box_shadow_y_offset'] ) != '') ? intval( sanitize_text_field( $_POST['ays_quiz_box_shadow_y_offset'] ) ) : 0;

            // Box Shadow Z offset
            $quiz_box_shadow_z_offset = (isset($_POST['ays_quiz_box_shadow_z_offset']) && sanitize_text_field( $_POST['ays_quiz_box_shadow_z_offset'] ) != '') ? intval( sanitize_text_field( $_POST['ays_quiz_box_shadow_z_offset'] ) ) : 15;

            // Question text alignment
            $quiz_question_text_alignment = (isset($_POST['ays_quiz_question_text_alignment']) && sanitize_text_field( $_POST['ays_quiz_question_text_alignment']) != '') ? sanitize_text_field( $_POST['ays_quiz_question_text_alignment'] ) : 'center';

            // Quiz arrows option arrows
            $quiz_arrow_type = (isset($_POST['ays_quiz_arrow_type']) && sanitize_text_field( $_POST['ays_quiz_arrow_type']) != '') ? sanitize_text_field( $_POST['ays_quiz_arrow_type'] ) : 'default';

            // Show wrong answers first
            $quiz_show_wrong_answers_first = (isset($_POST['ays_quiz_show_wrong_answers_first']) && sanitize_text_field( $_POST['ays_quiz_show_wrong_answers_first'] ) == 'on') ? 'on' : 'off';

            // Display all questions on one page
            $quiz_display_all_questions = (isset($_POST['ays_quiz_display_all_questions']) && sanitize_text_field( $_POST['ays_quiz_display_all_questions'] ) == 'on') ? 'on' : 'off';

            // Turn red warning
            $quiz_timer_red_warning = (isset($_POST['ays_quiz_timer_red_warning']) && sanitize_text_field( $_POST['ays_quiz_timer_red_warning'] ) == 'on') ? 'on' : 'off';

            // Timezone | Schedule the quiz | Start
            $quiz_schedule_timezone = (isset($_POST['ays_quiz_schedule_timezone']) && $_POST['ays_quiz_schedule_timezone'] != '') ? sanitize_text_field( $_POST['ays_quiz_schedule_timezone'] ) : get_option( 'timezone_string' );

            // Remove old Etc mappings. Fallback to gmt_offset.
            if ( strpos( $quiz_schedule_timezone, 'Etc/GMT' ) !== false ) {
                $quiz_schedule_timezone = '';
            }

            $current_offset = get_option( 'gmt_offset' );
            if ( empty( $quiz_schedule_timezone ) ) { // Create a UTC+- zone if no timezone string exists.

                if ( 0 == $current_offset ) {
                    $quiz_schedule_timezone = 'UTC+0';
                } elseif ( $current_offset < 0 ) {
                    $quiz_schedule_timezone = 'UTC' . $current_offset;
                } else {
                    $quiz_schedule_timezone = 'UTC+' . $current_offset;
                }
            }

            // Timezone | Schedule the quiz | End

            // Hint icon | Button | Text Value
            $questions_hint_button_value = (isset($_POST['ays_questions_hint_button_value']) && sanitize_text_field( $_POST['ays_questions_hint_button_value']) != '') ? stripcslashes( sanitize_text_field( $_POST['ays_questions_hint_button_value'] ) ) : '';

            // Quiz takers message
            $quiz_tackers_message = ( isset($_POST['ays_quiz_tackers_message']) && $_POST['ays_quiz_tackers_message'] != '' ) ? wp_kses_post( $_POST['ays_quiz_tackers_message'] ) : __( "This quiz is expired!", $this->plugin_name );

            // Enable Linkedin button
            $quiz_enable_linkedin_share_button = (isset($_POST['ays_quiz_enable_linkedin_share_button']) && sanitize_text_field( $_POST['ays_quiz_enable_linkedin_share_button'] ) == 'on') ? 'on' : 'off';

            // Enable Facebook button
            $quiz_enable_facebook_share_button = (isset($_POST['ays_quiz_enable_facebook_share_button']) && sanitize_text_field( $_POST['ays_quiz_enable_facebook_share_button'] ) == 'on') ? 'on' : 'off';

            // Enable Twitter button
            $quiz_enable_twitter_share_button = (isset($_POST['ays_quiz_enable_twitter_share_button']) && sanitize_text_field( $_POST['ays_quiz_enable_twitter_share_button'] ) == 'on') ? 'on' : 'off';

            // Make responses anonymous
            $quiz_make_responses_anonymous = (isset($_POST['ays_quiz_make_responses_anonymous']) && sanitize_text_field( $_POST['ays_quiz_make_responses_anonymous'] ) == 'on') ? 'on' : 'off';

            // Add all reviews link
            $quiz_make_all_review_link = (isset($_POST['ays_quiz_make_all_review_link']) && sanitize_text_field( $_POST['ays_quiz_make_all_review_link'] ) == 'on') ? 'on' : 'off';

            // Show questions numbering
            $show_questions_numbering = (isset($_POST['ays_show_questions_numbering']) && $_POST['ays_show_questions_numbering'] != '') ? sanitize_text_field( $_POST['ays_show_questions_numbering'] ) : 'none';

            // Show questions numbering
            $quiz_message_before_timer = (isset($_POST['ays_quiz_message_before_timer']) && $_POST['ays_quiz_message_before_timer'] != '') ? stripcslashes( sanitize_text_field( $_POST['ays_quiz_message_before_timer'] ) ) : '';

            // Password quiz
            $enable_password = (isset($_POST['ays_enable_password']) && sanitize_text_field( $_POST['ays_enable_password'] ) == 'on') ? 'on' : 'off';

            // Password for passing quiz | Password
            $password_quiz = (isset($_POST['ays_password_quiz']) && $_POST['ays_password_quiz'] != '') ? stripcslashes( sanitize_text_field( $_POST['ays_password_quiz'] ) ) : '';

            // Password for passing quiz | Message
            $quiz_password_message = ( isset($_POST['ays_quiz_password_message']) && $_POST['ays_quiz_password_message'] != '' ) ? wp_kses_post( $_POST['ays_quiz_password_message'] ) : '';

            // Enable confirmation box for the See Result button
            $enable_see_result_confirm_box = ( isset($_POST['ays_enable_see_result_confirm_box']) && sanitize_text_field( $_POST['ays_enable_see_result_confirm_box'] ) == 'on' ) ? 'on' : 'off';

            // Display form fields labels
            $display_fields_labels = ( isset($_POST['ays_display_fields_labels']) && sanitize_text_field( $_POST['ays_display_fields_labels'] ) == 'on' ) ? 'on' : 'off';

            //Enable full screen mode
            $enable_full_screen_mode = (isset($_POST['ays_enable_full_screen_mode']) && $_POST['ays_enable_full_screen_mode'] == 'on') ? 'on' : 'off';

            // Enable toggle password visibility
            $quiz_enable_password_visibility = (isset($_POST['ays_quiz_enable_password_visibility']) && $_POST['ays_quiz_enable_password_visibility'] == 'on') ? 'on' : 'off';

            // Question font size | On mobile
            $question_mobile_font_size = ( isset($_POST['ays_question_mobile_font_size']) && $_POST['ays_question_mobile_font_size'] != "" && absint( sanitize_text_field( $_POST['ays_question_mobile_font_size'] ) ) > 0) ? absint( sanitize_text_field( $_POST['ays_question_mobile_font_size'] ) ) : 16;

            // Answer font size | On mobile
            $answers_mobile_font_size = ( isset($_POST['ays_answers_mobile_font_size']) && $_POST['ays_answers_mobile_font_size'] != "" && absint( sanitize_text_field( $_POST['ays_answers_mobile_font_size'] ) ) > 0 ) ? absint( sanitize_text_field( $_POST['ays_answers_mobile_font_size'] ) ) : 15;

            // Heading for social buttons
            $social_buttons_heading = (isset($_POST['ays_social_buttons_heading']) && $_POST['ays_social_buttons_heading'] != '') ? wp_kses_post( $_POST['ays_social_buttons_heading'] ) : "";

            // Enable VKontakte button
            $quiz_enable_vkontakte_share_button = (isset($_POST['ays_quiz_enable_vkontakte_share_button']) && sanitize_text_field( $_POST['ays_quiz_enable_vkontakte_share_button'] ) == 'on') ? 'on' : 'off';

            // Answers border options
            $answers_border = (isset($_POST['ays_answers_border']) && $_POST['ays_answers_border'] == 'on') ? 'on' : 'off';
            $answers_border_width = (isset($_POST['ays_answers_border_width']) && $_POST['ays_answers_border_width'] != '') ? absint( sanitize_text_field( $_POST['ays_answers_border_width'] ) ) : '1';
            $answers_border_style = (isset($_POST['ays_answers_border_style']) && $_POST['ays_answers_border_style'] != '') ? sanitize_text_field( $_POST['ays_answers_border_style'] ) : 'solid';
            $answers_border_color = (isset($_POST['ays_answers_border_color']) && $_POST['ays_answers_border_color'] != '') ? sanitize_text_field( $_POST['ays_answers_border_color'] ) : '#444';

            // Heading for social media links
            $social_links_heading = (isset($_POST['ays_social_links_heading']) && $_POST['ays_social_links_heading'] != '') ? wp_kses_post( $_POST['ays_social_links_heading'] ) : "";

            // Show question category description
            $quiz_enable_question_category_description = ( isset($_POST['ays_quiz_enable_question_category_description']) && sanitize_text_field( $_POST['ays_quiz_enable_question_category_description'] ) == 'on' ) ? 'on' : 'off';

            // Answers margin option
            $answers_margin = ( isset($_POST['ays_answers_margin']) && sanitize_text_field( $_POST['ays_answers_margin'] ) != '' ) ? absint( sanitize_text_field( $_POST['ays_answers_margin'] ) ) : '10';

            // Show questions numbering
            $quiz_message_before_redirect_timer = (isset($_POST['ays_quiz_message_before_redirect_timer']) && $_POST['ays_quiz_message_before_redirect_timer'] != '') ? stripcslashes( sanitize_text_field( $_POST['ays_quiz_message_before_redirect_timer'] ) ) : '';

            // Button font-size (px) | Mobile
            $buttons_mobile_font_size = ( isset($_POST['ays_buttons_mobile_font_size']) && sanitize_text_field( $_POST['ays_buttons_mobile_font_size'] ) != '' ) ? absint( sanitize_text_field( $_POST['ays_buttons_mobile_font_size'] ) ) : 17;

            // Answers box shadow
            $answers_box_shadow = (isset($_POST['ays_answers_box_shadow']) && sanitize_text_field($_POST['ays_answers_box_shadow']) == 'on') ? 'on' : 'off';

            // Answer box-shadow color
            $answers_box_shadow_color = (isset($_POST['ays_answers_box_shadow_color']) && sanitize_text_field($_POST['ays_answers_box_shadow_color']) != '') ? sanitize_text_field($_POST['ays_answers_box_shadow_color']) : '#000';

            // Answer Box Shadow X offset
            $quiz_answer_box_shadow_x_offset = (isset($_POST['ays_quiz_answer_box_shadow_x_offset']) && sanitize_text_field( $_POST['ays_quiz_answer_box_shadow_x_offset'] ) != '') ? intval( sanitize_text_field( $_POST['ays_quiz_answer_box_shadow_x_offset'] ) ) : 0;

            // Answer Box Shadow Y offset
            $quiz_answer_box_shadow_y_offset = (isset($_POST['ays_quiz_answer_box_shadow_y_offset']) && sanitize_text_field( $_POST['ays_quiz_answer_box_shadow_y_offset'] ) != '') ? intval( sanitize_text_field( $_POST['ays_quiz_answer_box_shadow_y_offset'] ) ) : 0;

            // Answer Box Shadow Z offset
            $quiz_answer_box_shadow_z_offset = (isset($_POST['ays_quiz_answer_box_shadow_z_offset']) && sanitize_text_field( $_POST['ays_quiz_answer_box_shadow_z_offset'] ) != '') ? intval( sanitize_text_field( $_POST['ays_quiz_answer_box_shadow_z_offset'] ) ) : 10;

            // Answers box shadow
            $quiz_enable_title_text_shadow = (isset($_POST['ays_quiz_enable_title_text_shadow']) && sanitize_text_field($_POST['ays_quiz_enable_title_text_shadow']) == 'on') ? 'on' : 'off';

            // Answer box-shadow color
            $quiz_title_text_shadow_color = (isset($_POST['ays_quiz_title_text_shadow_color']) && sanitize_text_field($_POST['ays_quiz_title_text_shadow_color']) != '') ? sanitize_text_field($_POST['ays_quiz_title_text_shadow_color']) : '#333';

            // Quiz Title Text Shadow X offset
            $quiz_title_text_shadow_x_offset = (isset($_POST['ays_quiz_title_text_shadow_x_offset']) && sanitize_text_field( $_POST['ays_quiz_title_text_shadow_x_offset'] ) != '') ? intval( sanitize_text_field( $_POST['ays_quiz_title_text_shadow_x_offset'] ) ) : 2;

            // Quiz Title Text Shadow Y offset
            $quiz_title_text_shadow_y_offset = (isset($_POST['ays_quiz_title_text_shadow_y_offset']) && sanitize_text_field( $_POST['ays_quiz_title_text_shadow_y_offset'] ) != '') ? intval( sanitize_text_field( $_POST['ays_quiz_title_text_shadow_y_offset'] ) ) : 2;

            // Quiz Title Text Shadow Z offset
            $quiz_title_text_shadow_z_offset = (isset($_POST['ays_quiz_title_text_shadow_z_offset']) && sanitize_text_field( $_POST['ays_quiz_title_text_shadow_z_offset'] ) != '') ? intval( sanitize_text_field( $_POST['ays_quiz_title_text_shadow_z_offset'] ) ) : 2;

            // Show only wrong answers
            $quiz_show_only_wrong_answers = (isset($_POST['ays_quiz_show_only_wrong_answers']) && sanitize_text_field($_POST['ays_quiz_show_only_wrong_answers']) == 'on') ? 'on' : 'off';

            // Quiz Title font size
            $quiz_title_font_size = (isset($_POST['ays_quiz_title_font_size']) && sanitize_text_field( $_POST['ays_quiz_title_font_size'] ) != '') ? intval( sanitize_text_field( $_POST['ays_quiz_title_font_size'] ) ) : 21;

            // Quiz title font size | On mobile
            $quiz_title_mobile_font_size = ( isset($_POST['ays_quiz_title_mobile_font_size']) && sanitize_text_field( $_POST['ays_quiz_title_mobile_font_size'] ) != '' ) ? absint( sanitize_text_field( $_POST['ays_quiz_title_mobile_font_size'] ) ) : 21;

            // Quiz password width
            $quiz_password_width = ( isset($_POST['ays_quiz_password_width']) && sanitize_text_field( $_POST['ays_quiz_password_width'] ) != '' && sanitize_text_field( $_POST['ays_quiz_password_width'] ) != 0) ? absint( sanitize_text_field( $_POST['ays_quiz_password_width'] ) ) : "";

            // Enable quiz assessment | Placeholder text
            $quiz_review_placeholder_text = (isset($_POST['ays_quiz_review_placeholder_text']) && $_POST['ays_quiz_review_placeholder_text'] != '') ? stripcslashes( sanitize_text_field( $_POST['ays_quiz_review_placeholder_text'] ) ) : '';

            // Make review required
            $quiz_make_review_required = (isset($_POST['ays_quiz_make_review_required']) && sanitize_text_field($_POST['ays_quiz_make_review_required']) == 'on') ? 'on' : 'off';

            // Enable the Show/Hide toggle
            $quiz_enable_results_toggle = (isset($_POST['ays_quiz_enable_results_toggle']) && sanitize_text_field($_POST['ays_quiz_enable_results_toggle']) == 'on') ? 'on' : 'off';


            $options = array(
                'quiz_version'                          => AYS_QUIZ_VERSION,
                'core_version'                          => get_bloginfo( 'version' ),
                'php_version'                           => phpversion(),
                'color'                                 => sanitize_text_field( $_POST['ays_quiz_color'] ),
                'bg_color'                              => sanitize_text_field( $_POST['ays_quiz_bg_color'] ),
                'text_color'                            => sanitize_text_field( $_POST['ays_quiz_text_color'] ),
                'height'                                => absint( intval( $_POST['ays_quiz_height'] ) ),
                'width'                                 => absint( intval( $_POST['ays_quiz_width'] ) ),
                'enable_logged_users'                   => $ays_enable_logged_users,
                'information_form'                      => sanitize_text_field( $_POST['ays_information_form'] ),
                'form_name'                             => $ays_form_name,
                'form_email'                            => $ays_form_email,
                'form_phone'                            => $ays_form_phone,
                'image_width'                           => $image_width,
                'image_height'                          => sanitize_text_field( $_POST['ays_image_height'] ),
                'enable_correction'                     => $enable_correction,
                'enable_progress_bar'                   => $enable_progressbar,
                'enable_questions_result'               => $enable_questions_result,
                'randomize_questions'                   => $enable_random_questions,
                'randomize_answers'                     => $enable_random_answers,
                'enable_questions_counter'              => $enable_questions_counter,
                'enable_questions_score'                => $enable_questions_score,
                'enable_restriction_pass'               => $enable_restriction_pass,
                'restriction_pass_message'              => wp_kses_post( $_POST['restriction_pass_message'] ),
                'user_role'                             => $limit_user_roles,                
                'custom_css'                            => $custom_css,
                'limit_users'                           => $limit_users,
                'limitation_message'                    => wp_kses_post( $_POST['ays_limitation_message'] ),
                'redirect_url'                          => sanitize_text_field( $_POST['ays_redirect_url'] ),
                'redirection_delay'                     => intval( sanitize_text_field( $_POST['ays_redirection_delay'] ) ),
                'answers_view'                          => sanitize_text_field( $_POST['ays_answers_view'] ),
                'enable_rtl_direction'                  => $enable_rtl,
                'enable_logged_users_message'           => $enable_logged_users_mas,
                'questions_count'                       => sanitize_text_field( $_POST['ays_questions_count'] ),
                'enable_question_bank'                  => $question_bank,
                'enable_live_progress_bar'              => $live_progressbar,
                'enable_percent_view'                   => $percent_view,
                'enable_average_statistical'            => $avarage_statistical,
                'enable_next_button'                    => $next_button,
                'enable_previous_button'                => $prev_button,
                'enable_arrows'                         => $enable_arrows,
                'timer_text'                            => wp_kses_post( $_POST['ays_timer_text'] ),
                'quiz_theme'                            => $quiz_theme,
                'enable_social_buttons'                 => $social_buttons,
                'result_text'                           => wp_kses_post( $_POST['ays_result_text'] ),
                'enable_pass_count'                     => $enable_pass_count,
                'hide_score'                            => $hide_score,
                'rate_form_title'                       => $rate_form_title,
                'box_shadow_color'                      => $quiz_box_shadow_color,
                'quiz_border_radius'                    => $quiz_border_radius,
                'quiz_bg_image'                         => $quiz_bg_image,
                'quiz_border_width'                     => $quiz_border_width,
                'quiz_border_style'                     => $quiz_border_style,
                'quiz_border_color'                     => $quiz_border_color,
                'quiz_loader'                           => $quiz_loader,
                'create_date'                           => $quiz_create_date,
                'author'                                => $author,
                'quest_animation'                       => $quest_animation,
                'form_title'                            => $form_title,
                'enable_bg_music'                       => $enable_bg_music,
                'quiz_bg_music'                         => $quiz_bg_music,
                'answers_font_size'                     => $answers_font_size,
                'show_create_date'                      => $show_create_date,
                'show_author'                           => $show_author,
                'enable_early_finish'                   => $enable_early_finish,
                'answers_rw_texts'                      => $answers_rw_texts,
                'disable_store_data'                    => $disable_store_data,
                'enable_background_gradient'            => $enable_background_gradient,
                'background_gradient_color_1'           => $quiz_background_gradient_color_1,
                'background_gradient_color_2'           => $quiz_background_gradient_color_2,
                'quiz_gradient_direction'               => $quiz_gradient_direction,
                'redirect_after_submit'                 => $redirect_after_submit,
                'submit_redirect_url'                   => $submit_redirect_url,
                'submit_redirect_delay'                 => $submit_redirect_delay,
                'progress_bar_style'                    => $progress_bar_style,
                'enable_exit_button'                    => $enable_exit_button,
                'exit_redirect_url'                     => $exit_redirect_url,
                'image_sizing'                          => $image_sizing,
                'quiz_bg_image_position'                => $quiz_bg_image_position,
                'custom_class'                          => $custom_class,
                'enable_social_links'                   => $enable_social_links,
                'social_links'                          => $social_links,
                'show_quiz_title'                       => $show_quiz_title,
                'show_quiz_desc'                        => $show_quiz_desc,
                'show_login_form'                       => $show_login_form,
                'mobile_max_width'                      => $mobile_max_width,
                'limit_users_by'                        => $limit_users_by,
				'active_date_check'                     => $active_date_check,
				'activeInterval'                        => $activeInterval,
				'deactiveInterval'                      => $deactiveInterval,
				'active_date_pre_start_message'         => $active_date_pre_start_message,
                'active_date_message'                   => $active_date_message,
				'explanation_time'                      => $explanation_time,
				'enable_clear_answer'                   => $enable_clear_answer,
				'show_category'                         => $show_category,
				'show_question_category'                => $show_question_category,
				'display_score'                         => $display_score,
				'enable_rw_asnwers_sounds'              => $enable_rw_asnwers_sounds,
                'ans_right_wrong_icon'                  => $ans_right_wrong_icon,
                'quiz_bg_img_in_finish_page'            => $quiz_bg_img_in_finish_page,
                'finish_after_wrong_answer'             => $finish_after_wrong_answer,
                'after_timer_text'                      => $after_timer_text,
                'enable_enter_key'                      => $enable_enter_key,
                'buttons_text_color'                    => $buttons_text_color,
                'buttons_position'                      => $buttons_position,
                'show_questions_explanation'            => $show_questions_explanation,
                'enable_audio_autoplay'                 => $enable_audio_autoplay,
                'buttons_size'                          => $buttons_size,
                'buttons_font_size'                     => $buttons_font_size,
                'buttons_width'                         => $buttons_width,
                'buttons_left_right_padding'            => $buttons_left_right_padding,
                'buttons_top_bottom_padding'            => $buttons_top_bottom_padding,
                'buttons_border_radius'                 => $buttons_border_radius,
                'enable_leave_page'                     => $enable_leave_page,
                'enable_tackers_count'                  => $enable_tackers_count,
                'tackers_count'                         => $tackers_count,
                'pass_score'                            => $pass_score,
                'pass_score_message'                    => $pass_score_message,
                'fail_score_message'                    => $fail_score_message,
                'question_font_size'                    => $question_font_size,
                'quiz_width_by_percentage_px'           => $quiz_width_by_percentage_px,
                'questions_hint_icon_or_text'           => $questions_hint_icon_or_text,
                'questions_hint_value'                  => $questions_hint_value,
                'enable_early_finsh_comfirm_box'        => $enable_early_finsh_comfirm_box,
                'enable_questions_ordering_by_cat'      => $enable_questions_ordering_by_cat,
                'show_schedule_timer'                   => $show_schedule_timer,
                'show_timer_type'                       => $show_timer_type,
                'quiz_loader_text_value'                => $quiz_loader_text_value,
                'hide_correct_answers'                  => $hide_correct_answers,
                'show_information_form'                 => $show_information_form,
                'quiz_loader_custom_gif'                => $quiz_loader_custom_gif,
                'disable_hover_effect'                  => $disable_hover_effect,
                'quiz_loader_custom_gif_width'          => $quiz_loader_custom_gif_width,
                'progress_live_bar_style'               => $progress_live_bar_style,
                'quiz_title_transformation'             => $quiz_title_transformation,
                'show_answers_numbering'                => $show_answers_numbering,
                'quiz_image_width_by_percentage_px'     => $quiz_image_width_by_percentage_px,
                'quiz_image_height'                     => $quiz_image_height,
                'quiz_bg_img_on_start_page'             => $quiz_bg_img_on_start_page,
                'quiz_box_shadow_x_offset'              => $quiz_box_shadow_x_offset,
                'quiz_box_shadow_y_offset'              => $quiz_box_shadow_y_offset,
                'quiz_box_shadow_z_offset'              => $quiz_box_shadow_z_offset,
                'quiz_question_text_alignment'          => $quiz_question_text_alignment,
                'quiz_arrow_type'                       => $quiz_arrow_type,
                'quiz_show_wrong_answers_first'         => $quiz_show_wrong_answers_first,
                'quiz_display_all_questions'            => $quiz_display_all_questions,
                'quiz_timer_red_warning'                => $quiz_timer_red_warning,
                'quiz_schedule_timezone'                => $quiz_schedule_timezone,
                'questions_hint_button_value'           => $questions_hint_button_value,
                'quiz_tackers_message'                  => $quiz_tackers_message,
                'quiz_enable_linkedin_share_button'     => $quiz_enable_linkedin_share_button,
                'quiz_enable_facebook_share_button'     => $quiz_enable_facebook_share_button,
                'quiz_enable_twitter_share_button'      => $quiz_enable_twitter_share_button,
                'quiz_make_responses_anonymous'         => $quiz_make_responses_anonymous,
                'quiz_make_all_review_link'             => $quiz_make_all_review_link,
                'show_questions_numbering'              => $show_questions_numbering,
                'quiz_message_before_timer'             => $quiz_message_before_timer,
                'enable_password'                       => $enable_password,
                'password_quiz'                         => $password_quiz,
                'quiz_password_message'                 => $quiz_password_message,
                'enable_see_result_confirm_box'         => $enable_see_result_confirm_box,
                'display_fields_labels'                 => $display_fields_labels,
                'enable_full_screen_mode'               => $enable_full_screen_mode,
                'quiz_enable_password_visibility'       => $quiz_enable_password_visibility,
                'question_mobile_font_size'             => $question_mobile_font_size,
                'answers_mobile_font_size'              => $answers_mobile_font_size,
                'social_buttons_heading'                => $social_buttons_heading,
                'quiz_enable_vkontakte_share_button'    => $quiz_enable_vkontakte_share_button,
                'answers_border'                        => $answers_border,
                'answers_border_width'                  => $answers_border_width,
                'answers_border_style'                  => $answers_border_style,
                'answers_border_color'                  => $answers_border_color,
                'social_links_heading'                  => $social_links_heading,
                'quiz_enable_question_category_description' => $quiz_enable_question_category_description,
                'answers_margin'                        => $answers_margin,
                'quiz_message_before_redirect_timer'    => $quiz_message_before_redirect_timer,
                'buttons_mobile_font_size'              => $buttons_mobile_font_size,
                'answers_box_shadow'                    => $answers_box_shadow,
                'answers_box_shadow_color'              => $answers_box_shadow_color,
                'quiz_answer_box_shadow_x_offset'       => $quiz_answer_box_shadow_x_offset,
                'quiz_answer_box_shadow_y_offset'       => $quiz_answer_box_shadow_y_offset,
                'quiz_answer_box_shadow_z_offset'       => $quiz_answer_box_shadow_z_offset,
                'quiz_create_author'                    => $quiz_create_author,
                'quiz_enable_title_text_shadow'         => $quiz_enable_title_text_shadow,
                'quiz_title_text_shadow_color'          => $quiz_title_text_shadow_color,
                'quiz_title_text_shadow_x_offset'       => $quiz_title_text_shadow_x_offset,
                'quiz_title_text_shadow_y_offset'       => $quiz_title_text_shadow_y_offset,
                'quiz_title_text_shadow_z_offset'       => $quiz_title_text_shadow_z_offset,
                'quiz_show_only_wrong_answers'          => $quiz_show_only_wrong_answers,
                'quiz_title_font_size'                  => $quiz_title_font_size,
                'quiz_title_mobile_font_size'           => $quiz_title_mobile_font_size,
                'quiz_password_width'                   => $quiz_password_width,
                'quiz_review_placeholder_text'          => $quiz_review_placeholder_text,
                'quiz_make_review_required'             => $quiz_make_review_required,
                'quiz_enable_results_toggle'            => $quiz_enable_results_toggle,
            );
            $options['required_fields'] = !isset($_POST['ays_required_field']) ? null : array_map( 'sanitize_text_field', $_POST['ays_required_field'] );
            if( isset( $_POST['ays_enable_timer'] ) && sanitize_text_field( $_POST['ays_enable_timer'] ) == 'on' ){
                $options['enable_timer'] = 'on';
            }else{                
                $options['enable_timer'] = 'off';
            }
            $options['enable_quiz_rate'] = ( isset( $_POST['ays_enable_quiz_rate'] ) && sanitize_text_field( $_POST['ays_enable_quiz_rate'] ) == 'on' ) ? 'on' : 'off';
            $options['enable_rate_avg'] = ( isset( $_POST['ays_enable_rate_avg'] ) && sanitize_text_field( $_POST['ays_enable_rate_avg'] ) == 'on' ) ? 'on' : 'off';
            $options['enable_box_shadow'] = ( isset( $_POST['ays_enable_box_shadow'] ) && sanitize_text_field( $_POST['ays_enable_box_shadow'] ) == 'on' ) ? 'on' : 'off';
            $options['enable_border'] = ( isset( $_POST['ays_enable_border'] ) && sanitize_text_field( $_POST['ays_enable_border'] ) == 'on' ) ? 'on' : 'off';
            $options['quiz_timer_in_title'] = ( isset( $_POST['ays_quiz_timer_in_title'] ) && sanitize_text_field( $_POST['ays_quiz_timer_in_title'] ) == 'on' ) ? 'on' : 'off';
            
            $options['enable_rate_comments'] = ( isset( $_POST['ays_enable_rate_comments'] ) && sanitize_text_field( $_POST['ays_enable_rate_comments'] ) == 'on' ) ? 'on' : 'off';
            
            $options['enable_restart_button'] = ( isset( $_POST['ays_enable_restart_button'] ) && sanitize_text_field( $_POST['ays_enable_restart_button'] ) == 'on' ) ? 'on' : 'off';
            
            $options['autofill_user_data'] = ( isset( $_POST['ays_autofill_user_data'] ) && sanitize_text_field( $_POST['ays_autofill_user_data'] ) == 'on' ) ? 'on' : 'off';
            
            if( isset( $_POST['ays_quiz_timer'] ) && intval($_POST['ays_quiz_timer']) != 0 ){
                $options['timer'] = absint( sanitize_text_field( $_POST['ays_quiz_timer'] ) );
            }else{
                $options['timer'] = 100;
            }
            if($id == null) {
                $quiz_result = $wpdb->insert(
                    $quiz_table,
                    array(
                        'title'             => $title,
                        'description'       => $description,
                        'quiz_image'        => $image,
                        'quiz_category_id'  => $quiz_category_id,
                        'question_ids'      => $question_ids,
                        'published'         => $published,
                        'ordering'          => $ordering,
                        'options'           => json_encode($options)
                    ),
                    array(
                        '%s', // title
                        '%s', // description
                        '%s', // quiz_image
                        '%d', // quiz_category_id
                        '%s', // question_ids
                        '%d', // published
                        '%d', // ordering
                        '%s'  // options
                    )
                );
                $message = 'created';
            }else{
                $quiz_result = $wpdb->update(
                    $quiz_table,
                    array(
                        'title'             => $title,
                        'description'       => $description,
                        'quiz_image'        => $image,
                        'quiz_category_id'  => $quiz_category_id,
                        'question_ids'      => $question_ids,
                        'published'         => $published,
                        'options'           => json_encode($options)
                    ),
                    array( 'id' => $id ),
                    array(
                        '%s', // title
                        '%s', // description
                        '%s', // quiz_image
                        '%d', // quiz_category_id
                        '%s', // question_ids
                        '%d', // published
                        '%s'  // options
                    ),
                    array( '%d' )
                );
                $message = 'updated';
            }
            
            $ays_quiz_tab = isset($_POST['ays_quiz_tab']) ? sanitize_text_field( $_POST['ays_quiz_tab'] ) : 'tab1';
            if( $quiz_result >= 0 ){
                if($ays_change_type != ''){
                    if($id == null){
                        $url = esc_url_raw( add_query_arg( array(
                            "action"        => "edit",
                            "quiz"          => $wpdb->insert_id,
                            "ays_quiz_tab"  => $ays_quiz_tab,
                            "status"        => $message
                        ) ) );
                    }else{
                        $url = esc_url_raw( add_query_arg( array(
                            "ays_quiz_tab"  => $ays_quiz_tab,
                            "status"        => $message
                        ) ) );
                    }
                    wp_redirect( $url );
                }else{
                    $url = esc_url_raw( remove_query_arg( array('action', 'quiz') ) ) . '&status=' . $message;
                    wp_redirect( $url );
                }
            }
        }
    }

    private function get_max_id() {
        global $wpdb;
        $quiz_table = $wpdb->prefix . 'aysquiz_quizes';

        $sql = "SELECT max(id) FROM {$quiz_table}";

        $result = $wpdb->get_var($sql);

        return $result;
    }

    /**
     * Delete a customer record.
     *
     * @param int $id customer ID
     */
    public static function delete_quizes( $id ) {
        global $wpdb;
        $reports_table = $wpdb->prefix . "aysquiz_reports";
        $wpdb->delete(
            "{$wpdb->prefix}aysquiz_quizes",
            array( 'id' => $id ),
            array( '%d' )
        );
        $wpdb->delete(
            $reports_table,
            array( 'quiz_id' => $id ),
            array( '%d' )
        );
    }

    public static function ays_quiz_published_unpublished_quiz( $id, $status = 'published' ) {
        global $wpdb;
        $quizzes_table = esc_sql( $wpdb->prefix . "aysquiz_quizes" );

        switch ( $status ) {
            case 'published':
                $published = 1;
                break;
            case 'unpublished':
                $published = 0;
                break;
            default:
                $published = 1;
                break;
        }

        $quiz_result = $wpdb->update(
            $quizzes_table,
            array(
                'published' => $published,

            ),
            array( 'id' => absint( $id ) ),
            array(
                '%d'
            ),
            array( '%d' )
        );
    }


    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public static function record_count() {
        global $wpdb;

        $filter = array();
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_quizes";

        if( isset( $_GET['filterby'] ) && absint( intval( $_GET['filterby'] ) ) > 0){
            $cat_id = absint( sanitize_text_field( $_GET['filterby'] ) );
            $filter[] = ' quiz_category_id = '.$cat_id.' ';
        }

        if( isset( $_REQUEST['fstatus'] ) && is_numeric( $_REQUEST['fstatus'] ) && ! is_null( sanitize_text_field( $_REQUEST['fstatus'] ) ) ){
            if( esc_sql( $_REQUEST['fstatus'] ) != '' ){
                $fstatus  = absint( esc_sql( $_REQUEST['fstatus'] ) );
                $filter[] = " published = ".$fstatus." ";
            }
        }
        
        $search = ( isset( $_REQUEST['s'] ) ) ? esc_sql( sanitize_text_field( $_REQUEST['s'] ) ) : false;
        if( $search ){
            $filter[] = sprintf(" title LIKE '%%%s%%' ", esc_sql( $wpdb->esc_like( $search ) ) );
        }
        
        if(count($filter) !== 0){
            $sql .= " WHERE ".implode(" AND ", $filter);
        }

        return $wpdb->get_var( $sql );
    }

    public static function published_questions_record_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_questions WHERE published=1";

        return $wpdb->get_var( $sql );
    }
    
    public static function all_record_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_quizes WHERE 1=1";

        if( isset( $_GET['filterby'] ) && absint( intval( $_GET['filterby'] ) ) > 0){
            $cat_id = absint( intval( $_GET['filterby'] ) );
            $sql .= ' AND quiz_category_id = '.$cat_id.' ';
        }

        return $wpdb->get_var( $sql );
    }
    
    public static function published_quizzes_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_quizes WHERE published=1";

        if( isset( $_GET['filterby'] ) && absint( intval( $_GET['filterby'] ) ) > 0){
            $cat_id = absint( intval( $_GET['filterby'] ) );
            $sql .= ' AND quiz_category_id = '.$cat_id.' ';
        }

        return $wpdb->get_var( $sql );
    }
    
    public static function unpublished_quizzes_count() {
        global $wpdb;
        
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_quizes WHERE published=0";

        if( isset( $_GET['filterby'] ) && absint( intval( $_GET['filterby'] ) ) > 0){
            $cat_id = absint( intval( $_GET['filterby'] ) );
            $sql .= ' AND quiz_category_id = '.$cat_id.' ';
        }

        return $wpdb->get_var( $sql );
    }

    public static function get_quiz_pass_count($id) {
        global $wpdb;
        $quiz_id = intval($id);
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_reports WHERE quiz_id=".$quiz_id;

        return $wpdb->get_var( $sql );
    }

    public function duplicate_quizzes( $id ){
        global $wpdb;
        $quizzes_table = $wpdb->prefix."aysquiz_quizes";
        $quiz = $this->get_quiz_by_id($id);
        
        $user_id = get_current_user_id();
        $user = get_userdata($user_id);
        $author = array(
            'id' => $user->ID,
            'name' => $user->data->display_name
        );
        
        $max_id = $this->get_max_id();
        $ordering = ( $max_id != NULL ) ? ( $max_id + 1 ) : 1;
        
        $options = json_decode($quiz['options'], true);
        
        $options['create_date'] = current_time( 'mysql' );
        $options['author'] = $author;
        
        $result = $wpdb->insert(
            $quizzes_table,            
            array(
                'title'             => "Copy - ".$quiz['title'],
                'description'       => $quiz['description'],
                'quiz_image'        => $quiz['quiz_image'],
                'quiz_category_id'  => intval($quiz['quiz_category_id']),
                'question_ids'      => $quiz['question_ids'],
                'published'         => intval($quiz['published']),
                'ordering'          => $ordering,
                'options'           => json_encode($options)
            ),
            array(
                '%s', // title
                '%s', // description
                '%s', // quiz_image
                '%d', // quiz_category_id
                '%s', // question_ids
                '%d', // published
                '%d', // ordering
                '%s'  // options
            )
        );
        if( $result >= 0 ){
            $message = "duplicated";
            $url = esc_url_raw( remove_query_arg(array('action', 'question')  ) ) . '&status=' . $message;
            wp_redirect( $url );
        }
        
    }

    /** Text displayed when no customer data is available */
    public function no_items() {
        echo __( 'There are no quizzes yet.', $this->plugin_name );
    }


    /**
     * Render a column when no column specific method exist.
     *
     * @param array $item
     * @param string $column_name
     *
     * @return mixed
     */
    public function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'title':
            case 'quiz_image':
            case 'quiz_category_id':
            case 'shortcode':
            case 'code_include':
            case 'items_count':
            case 'create_date':
            case 'completed_count':
            case 'id':
                return $item[ $column_name ];
                break;
            default:
                return print_r( $item, true ); //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
        );
    }


    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_title( $item ) {
        $delete_nonce = wp_create_nonce( $this->plugin_name . '-delete-quiz' );

        $quiz_title = esc_attr(stripcslashes($item['title']));

        $q = esc_attr($quiz_title);
        $quizzes_title_length = intval( $this->title_length );

        $restitle = Quiz_Maker_Admin::ays_restriction_string("word", $quiz_title, $quizzes_title_length);
        $title = sprintf( '<a href="?page=%s&action=%s&quiz=%d" title="%s">%s</a>', esc_attr( $_REQUEST['page'] ), 'edit', absint( $item['id'] ), $q, $restitle);

        $actions = array(
            'edit' => sprintf( '<a href="?page=%s&action=%s&quiz=%d">'. __('Edit', $this->plugin_name) .'</a>', esc_attr( $_REQUEST['page'] ), 'edit', absint( $item['id'] ) ),
            'duplicate' => sprintf( '<a href="?page=%s&action=%s&quiz=%d">'. __('Duplicate', $this->plugin_name) .'</a>', esc_attr( $_REQUEST['page'] ), 'duplicate', absint( $item['id'] ) ),
            'results' => sprintf( '<a href="?page=%s&filterby=%d">'. __('View Results', $this->plugin_name) .'</a>', esc_attr( $_REQUEST['page'] ) . '-results', absint( $item['id'] ) ),
            'delete' => sprintf( '<a class="ays_confirm_del" data-message="%s" href="?page=%s&action=%s&quiz=%s&_wpnonce=%s">'. __('Delete', $this->plugin_name) .'</a>', $restitle, esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
        );

        return $title . $this->row_actions( $actions );
    }

    function column_quiz_category_id( $item ) {
        global $wpdb;

        $quiz_categories_table = esc_sql( $wpdb->prefix . "aysquiz_quizcategories" );

        $quiz_category_id = ( isset( $item['quiz_category_id'] ) && $item['quiz_category_id'] != "" ) ? absint( sanitize_text_field( $item['quiz_category_id'] ) ) : 0;

        $sql = "SELECT * FROM {$quiz_categories_table} WHERE id=" . $quiz_category_id;

        $result = $wpdb->get_row($sql, 'ARRAY_A');

        $results = "";
        if($result !== null){

            $category_title = ( isset( $result['title'] ) && $result['title'] != "" ) ? sanitize_text_field( $result['title'] ) : "";

            if ( $category_title != "" ) {
                $results = sprintf( '<a href="?page=%s&action=edit&quiz_category=%d" target="_blank">%s</a>', esc_attr( $_REQUEST['page'] ) . '-quiz-categories', $quiz_category_id, $category_title);
            }
        }else{
            $results = "";
        }

        return $results;
    }

    function column_code_include( $item ) {
        $shortcode = htmlentities('\'[ays_quiz id="'.$item["id"].'"]\'');
        return sprintf('<input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="<?php echo do_shortcode('.$shortcode.'); ?>" style="max-width:100%%;" />', $item["id"]);
    }

    function column_published( $item ) {
        switch( $item['published'] ) {
            case "1":
                return '<span class="ays-publish-status"><i class="ays_fa ays_fa_check_square_o" aria-hidden="true"></i> '. __('Published',$this->plugin_name) . '</span>';
                break;
            case "0":
                return '<span class="ays-publish-status"><i class="ays_fa ays_fa_square_o" aria-hidden="true"></i> '. __('Unpublished',$this->plugin_name) . '</span>';
                break;
        }
    }

    function column_shortcode( $item ) {
        return sprintf('<input type="text" onClick="this.setSelectionRange(0, this.value.length)" readonly value="[ays_quiz id=\'%s\']" style="max-width:100%%;" />', $item["id"]);
    }

    function column_create_date( $item ) {
        
        $options = json_decode($item['options'], true);
        $date = isset($options['create_date']) && $options['create_date'] != '' ? $options['create_date'] : "0000-00-00 00:00:00";
        if(isset($options['author'])){
            if(is_array($options['author'])){
                $author = $options['author'];
            }else{
                $author = json_decode($options['author'], true);
            }
        }else{
            $author = array("name"=>"Unknown");
        }
        $text = "";
        if(Quiz_Maker_Admin::validateDate($date)){
            $text .= "<p><b>Date:</b> ".$date."</p>";
        }
        if( isset( $author['name'] ) && $author['name'] !== "Unknown"){
            $text .= "<p><b>Author:</b> ".$author['name']."</p>";
        }
        return $text;
    }

    function column_completed_count( $item ) {
        $id = $item['id'];
        $passed_count = $this->get_quiz_pass_count($id);

        if ( $passed_count != 0 ) {

            $passed_count = sprintf( '<a href="?page=%s&filterby=%d" target="_blank">%s</a>', esc_attr( $_REQUEST['page'] ) . '-results', absint( sanitize_text_field( $item['id'] ) ), $passed_count );
        }

        $text = "<p style='font-size:14px;'>".$passed_count."</p>";
        return $text;
    }

    function column_items_count( $item ) {
        if(empty($item['question_ids'])){
            $count = 0;
        }else{
            $count = explode(',', $item['question_ids']);
            $count = count($count);
        }
        return "<p style='text-align:center;font-size:14px;'>" . $count . "</p>";
    }

    function column_quiz_image( $item ) {
        global $wpdb;
        
        $quiz_image = (isset( $item['quiz_image'] ) && $item['quiz_image'] != '') ? esc_url( $item['quiz_image'] ) : '';

        $image_html     = array();
        $edit_page_url  = '';

        if($quiz_image != ''){

            if ( isset( $item['id'] ) && absint( $item['id'] ) > 0 ) {
                $edit_page_url = sprintf( 'href="?page=%s&action=%s&quiz=%d"', esc_attr( $_REQUEST['page'] ), 'edit', absint( $item['id'] ) );
            }

            $quiz_image_url = $quiz_image;
            $this_site_path = trim( get_site_url(), "https:" );
            if( strpos( trim( $quiz_image_url, "https:" ), $this_site_path ) !== false ){ 
                $query = "SELECT * FROM `" . $wpdb->prefix . "posts` WHERE `post_type` = 'attachment' AND `guid` = '" . $quiz_image_url . "'";
                $result_img =  $wpdb->get_results( $query, "ARRAY_A" );
                if( ! empty( $result_img ) ){
                    $url_img = wp_get_attachment_image_src( $result_img[0]['ID'], 'thumbnail' );
                    if( $url_img !== false ){
                        $quiz_image_url = $url_img[0];
                    }
                }
            }

            $image_html[] = '<div class="ays-quiz-image-list-table-column">';
                $image_html[] = '<a '. $edit_page_url .' class="ays-quiz-image-list-table-link-column">';
                    $image_html[] = '<img src="'. $quiz_image_url .'" class="ays-quiz-image-list-table-img-column">';
                $image_html[] = '</a>';
            $image_html[] = '</div>';
        }

        $image_html = implode('', $image_html);

        return $image_html;
    }

    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns() {
        $columns = array(
            'cb'                => '<input type="checkbox" />',
            'title'             => __( 'Title', $this->plugin_name ),
            'quiz_image'        => __( 'Image', $this->plugin_name ),
            'quiz_category_id'  => __( 'Category', $this->plugin_name ),
            'shortcode'         => __( 'Shortcode', $this->plugin_name ),
            'code_include'      => __( 'Code include', $this->plugin_name ),
            'items_count'       => __( 'Count', $this->plugin_name ),
            'create_date'       => __( 'Created', $this->plugin_name ),
            'completed_count'   => __( 'Completed count', $this->plugin_name ),
            'id'                => __( 'ID', $this->plugin_name ),
        );

        if( isset( $_GET['action'] ) && ( $_GET['action'] == 'add' || $_GET['action'] == 'edit' ) ){
            return array();
        }

        return $columns;
    }


    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_sortable_columns() {
        $sortable_columns = array(
            'title'                 => array( 'title', true ),
            'quiz_category_id'      => array( 'quiz_category_id', true ),
            'id'                    => array( 'id', true ),
        );

        return $sortable_columns;
    }

    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions() {
        $actions = array(
            'bulk-published'    => __('Publish', $this->plugin_name),
            'bulk-unpublished'  => __('Unpublish', $this->plugin_name),
            'bulk-delete'       => __('Delete', $this->plugin_name),
        );

        return $actions;
    }

    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepare_items() {
        global $wpdb;

        $this->_column_headers = $this->get_column_info();

        /** Process bulk action */
        $this->process_bulk_action();

        $per_page     = $this->get_items_per_page( 'quizes_per_page', 20 );
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();

        $this->set_pagination_args( array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ) );

        $search = ( isset( $_REQUEST['s'] ) ) ? esc_sql( sanitize_text_field( $_REQUEST['s'] ) ) : false;

        $do_search = ( $search ) ? sprintf(" title LIKE '%%%s%%' ", esc_sql( $wpdb->esc_like( $search ) ) ) : '';

        $this->items = self::get_quizes( $per_page, $current_page, $do_search );
    }

    public function process_bulk_action() {
        //Detect when a bulk action is being triggered...
        $message = 'deleted';
        if ( 'delete' === $this->current_action() ) {

            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr( $_REQUEST['_wpnonce'] );

            if ( ! wp_verify_nonce( $nonce, $this->plugin_name . '-delete-quiz' ) ) {
                die( 'Go get a life script kiddies' );
            }
            else {
                self::delete_quizes( absint( $_GET['quiz'] ) );

                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
                // add_query_arg() return the current url

                $url = esc_url_raw( remove_query_arg( array('action', 'quiz', '_wpnonce') ) ) . '&status=' . $message;
                wp_redirect( $url );
            }

        }

        // If the delete bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
            || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
        ) {

            $delete_ids = ( isset( $_POST['bulk-delete'] ) && ! empty( $_POST['bulk-delete'] ) ) ? esc_sql( $_POST['bulk-delete'] ) : array();

            // loop over the array of record IDs and delete them
            foreach ( $delete_ids as $id ) {
                self::delete_quizes( $id );

            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url

            $url = esc_url_raw( remove_query_arg( array('action', 'quiz', '_wpnonce') ) ) . '&status=' . $message;
            wp_redirect( $url );
        } elseif ((isset($_POST['action']) && $_POST['action'] == 'bulk-published')
                  || (isset($_POST['action2']) && $_POST['action2'] == 'bulk-published')
        ) {

            $published_ids = ( isset( $_POST['bulk-delete'] ) && ! empty( $_POST['bulk-delete'] ) ) ? esc_sql( $_POST['bulk-delete'] ) : array();

            // loop over the array of record IDs and mark as read them

            foreach ( $published_ids as $id ) {
                self::ays_quiz_published_unpublished_quiz( $id , 'published' );
            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url
            $url = esc_url_raw( remove_query_arg(array('action', 'quiz', '_wpnonce')  ) ) . '&status=published';
            wp_redirect( $url );
        } elseif ((isset($_POST['action']) && $_POST['action'] == 'bulk-unpublished')
                  || (isset($_POST['action2']) && $_POST['action2'] == 'bulk-unpublished')
        ) {

            $unpublished_ids = ( isset( $_POST['bulk-delete'] ) && ! empty( $_POST['bulk-delete'] ) ) ? esc_sql( $_POST['bulk-delete'] ) : array();

            // loop over the array of record IDs and mark as read them

            foreach ( $unpublished_ids as $id ) {
                self::ays_quiz_published_unpublished_quiz( $id , 'unpublished' );
            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url
            $url = esc_url_raw( remove_query_arg(array('action', 'quiz', '_wpnonce')  ) ) . '&status=unpublished';
            wp_redirect( $url );
        }
    }

    public function quiz_notices(){
        $status = (isset($_REQUEST['status'])) ? sanitize_text_field( $_REQUEST['status'] ) : '';

        if ( empty( $status ) )
            return;

        if ( 'created' == $status )
            $updated_message = esc_html( __( 'Quiz created.', $this->plugin_name ) );
        elseif ( 'updated' == $status )
            $updated_message = esc_html( __( 'Quiz saved.', $this->plugin_name ) );
        elseif ( 'duplicated' == $status )
            $updated_message = esc_html( __( 'Quiz duplicated.', $this->plugin_name ) );
        elseif ( 'deleted' == $status )
            $updated_message = esc_html( __( 'Quiz deleted.', $this->plugin_name ) );
        elseif ( 'published' == $status )
            $updated_message = esc_html( __( 'Quiz(s) published.', $this->plugin_name ) );
        elseif ( 'unpublished' == $status )
            $updated_message = esc_html( __( 'Quiz(s) unpublished.', $this->plugin_name ) );

        if ( empty( $updated_message ) )
            return;

        ?>
        <div class="notice notice-success is-dismissible">
            <p> <?php echo $updated_message; ?> </p>
        </div>
        <?php
    }
}
