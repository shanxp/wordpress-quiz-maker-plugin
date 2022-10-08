<?php
ob_start();
class Questions_List_Table extends WP_List_Table{
    private $plugin_name;
    private $title_length;
    /** Class constructor */
    public function __construct($plugin_name) {
        $this->plugin_name = $plugin_name;
        $this->title_length = Quiz_Maker_Admin::get_listtables_title_length('questions');
        parent::__construct( array(
            'singular' => __( 'Question', $this->plugin_name ), //singular name of the listed records
            'plural'   => __( 'Questions', $this->plugin_name ), //plural name of the listed records
            'ajax'     => false //does this table support ajax?
        ) );

        add_action( 'admin_notices', array( $this, 'question_notices' ) );
    }


    /**
     * Override of table nav to avoid breaking with bulk actions & according nonce field
     */
    public function display_tablenav( $which ) {
        ?>
        <div class="tablenav <?php echo esc_attr( $which ); ?>">
            
            <div class="alignleft actions">
                <?php $this->bulk_actions( $which ); ?>
            </div>
             
            <?php
            $this->extra_tablenav( $which );
            $this->pagination( $which );
            ?>
            <br class="clear" />
        </div>
        <?php
    }
    
    public function extra_tablenav( $which ){
        global $wpdb;
        $titles_sql = "SELECT {$wpdb->prefix}aysquiz_categories.title,{$wpdb->prefix}aysquiz_categories.id FROM {$wpdb->prefix}aysquiz_categories";
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

        $question_types = array(
            "radio"             => __("Radio", $this->plugin_name),
            "checkbox"          => __("Checkbox( Multiple )", $this->plugin_name),
            "select"            => __("Dropdown", $this->plugin_name),
            "text"              => __("Text", $this->plugin_name),
            "short_text"        => __("Short Text", $this->plugin_name),
            "number"            => __("Number", $this->plugin_name),
            "date"              => __("Date", $this->plugin_name),
            "true_or_false"     => __("True/False", $this->plugin_name),
        );

        $selected_question_type = (isset( $_GET['type'] ) && sanitize_text_field( $_GET['type'] ) != "") ? sanitize_text_field( $_GET['type'] ) : "";

        ?>
        <div id="category-filter-div" class="alignleft actions bulkactions">
            <select name="filterby-<?php echo esc_attr( $which ); ?>" id="bulk-action-category-selector-<?php echo esc_attr( $which ); ?>">
                <option value=""><?php echo __('Select Category',$this->plugin_name)?></option>
                <?php
                    foreach($categories_select as $key => $cat_title){
                        echo "<option ".$cat_title['selected']." value='".$cat_title['id']."'>".$cat_title['title']."</option>";
                    }
                ?>
            </select>
            <input type="button" id="doaction-<?php echo esc_attr( $which ); ?>" class="cat-filter-apply-<?php echo esc_attr( $which ); ?> button" value="<?php echo __( "Filter", $this->plugin_name ); ?>">
        </div>

        <div id="question-type-filter-div" class="alignleft actions bulkactions">
            <select name="type-<?php echo esc_attr( $which ); ?>" id="bulk-action-question-type-selector-<?php echo esc_attr( $which ); ?>">
                <option value=""><?php echo __('Select question type',$this->plugin_name)?></option>
                <?php
                    $question_type_html = array();
                    foreach($question_types as $option_value => $question_type){
                        $selected_type = ($selected_question_type == $option_value ) ? "selected" : "";

                        $question_type_html[] = "<option value='".$option_value."' ". $selected_type .">".$question_type."</option>";
                    }
                    $question_type_html = implode( '' , $question_type_html);
                    echo $question_type_html;
                ?>
            </select>
            <input type="button" id="question-type-doaction-<?php echo esc_attr( $which ); ?>" class="question-type-filter-apply-<?php echo esc_attr( $which ); ?> button" value="<?php echo __( "Filter", $this->plugin_name ); ?>">
        </div>
        
        <a style="" href="?page=<?php echo esc_attr( $_REQUEST['page'] ); ?>" class="button"><?php echo __( "Clear filters", $this->plugin_name ); ?></a>
        <?php
    }
    
    
    protected function get_views() {
        $published_count = $this->published_questions_count();
        $unpublished_count = $this->unpublished_questions_count();
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
        $query_str = Quiz_Maker_Admin::ays_query_string(array("status", "fstatus"));
        $status_links = array(
            "all" => "<a ".$selected_all." href='?".esc_attr( $query_str )."'>". __( 'All', $this->plugin_name )." (".$all_count.")</a>",
            "published" => "<a ".$selected_1." href='?".esc_attr( $query_str )."&fstatus=1'>". __( 'Published', $this->plugin_name )." (".$published_count.")</a>",
            "unpublished"   => "<a ".$selected_0." href='?".esc_attr( $query_str )."&fstatus=0'>". __( 'Unpublished', $this->plugin_name )." (".$unpublished_count.")</a>"
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
    public static function get_questions( $per_page = 20, $page_number = 1, $search = '' ) {

        global $wpdb;
        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_questions";
        $where = array();
        if( $search != '' ){
            $where[] = $search;
        }
        
        if(! empty( $_REQUEST['filterby'] ) && absint( intval( $_REQUEST['filterby'] ) ) > 0){
            $cat_id = absint( intval( $_REQUEST['filterby'] ) );
            $where[] = ' category_id = '.$cat_id.'';
        }
        if( isset( $_REQUEST['type'] ) ){
            $where[] = ' type = "'. esc_sql( $_REQUEST['type'] ).'" ';
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

            $order_by  = ( isset( $_REQUEST['orderby'] ) && sanitize_text_field( $_REQUEST['orderby'] ) != '' ) ? sanitize_text_field( $_REQUEST['orderby'] ) : 'id';
            $order_by .= ( ! empty( $_REQUEST['order'] ) && strtolower( $_REQUEST['order'] ) == 'asc' ) ? ' ASC' : ' DESC';

            $sql_orderby = sanitize_sql_orderby($order_by);

            if ( $sql_orderby ) {
                $sql .= ' ORDER BY ' . $sql_orderby;
            } else {
                $sql .= ' ORDER BY id DESC';
            }

        }else{
            $sql .= ' ORDER BY id DESC';
        }
        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


        $result = $wpdb->get_results( $sql, 'ARRAY_A' );

        return $result;
    }

    /**
     * Delete a customer record.
     *
     * @param int $id customer ID
     */
    public static function delete_questions( $id ) {
        global $wpdb;
        $wpdb->delete(
            "{$wpdb->prefix}aysquiz_questions",
            array( 'id' => $id ),
            array( '%d' )
        );

        $wpdb->delete(
            "{$wpdb->prefix}aysquiz_answers",
            array('question_id' => $id),
            array('%d')
        );

        $sql = "SELECT `question_ids` ,`id` FROM {$wpdb->prefix}aysquiz_quizes";
        $quizzes = $wpdb->get_results($sql);
        if(!empty($quizzes)) {
            foreach ($quizzes as $quiz) {
                $quiz_questions = explode(',', $quiz->question_ids);
                if (($key = array_search($id, $quiz_questions)) !== false) {
                    unset($quiz_questions[$key]);
                }
                $quiz_questions_implode = implode(',', $quiz_questions);
                $update_sql = "UPDATE {$wpdb->prefix}aysquiz_quizes SET question_ids='{$quiz_questions_implode}' WHERE id={$quiz->id}";
                $wpdb->get_var($update_sql);
            }
        }
    }

    public static function ays_quiz_published_unpublished_questions( $id, $status = 'published' ) {
        global $wpdb;
        $questions_table = $wpdb->prefix . "aysquiz_questions";

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

        $question_result = $wpdb->update(
            $questions_table,
            array(
                'published' => $published,

            ),
            array( 'id' => $id ),
            array(
                '%d'
            ),
            array( '%d' )
        );
    }

    public function get_question_categories() {
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_categories";

        $result = $wpdb->get_results($sql, 'ARRAY_A');

        return $result;
    }

    public function get_question( $id ) {
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_questions WHERE id=" . absint( intval( $id ) );

        $result = $wpdb->get_row($sql, 'ARRAY_A');

        return $result;
    }

    public function get_question_answers( $question_id ) {
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}aysquiz_answers WHERE question_id=" . absint( intval( $question_id ) );

        $result = $wpdb->get_results( $sql, 'ARRAY_A' );

        return $result;
    }

    public function add_edit_questions(){
        global $wpdb;
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $answers_table = $wpdb->prefix . "aysquiz_answers";
        $ays_change_type = isset( $_POST['ays_change_type'] ) ? sanitize_text_field( $_POST['ays_change_type'] ) : '';
        if( isset($_POST["question_action"]) && wp_verify_nonce( sanitize_text_field( $_POST["question_action"] ), 'question_action' ) ){
            
            $id = absint( intval( $_POST['id'] ) );

            $question = '';
            if ( isset( $_POST['ays_question'] ) ) {

                $is_exists_ruby = Quiz_Maker_Admin::ays_quiz_is_exists_needle_tag( $_POST['ays_question'] , '<ruby>' );

                if ( $is_exists_ruby ) {
                    $question = $_POST['ays_question'];
                } else {
                    $question = wp_kses_post( $_POST['ays_question'] );
                }
            }

            $question_hint      = wp_kses_post( $_POST['ays_question_hint'] );
            $question_image     = (isset( $_POST['ays_question_image'] ) && $_POST['ays_question_image'] != '') ? sanitize_text_field( $_POST['ays_question_image'] ) : NULL;
            $category_id        = absint( sanitize_text_field( $_POST['ays_question_category'] ) );
            $published          = absint( sanitize_text_field( $_POST['ays_publish'] ) );
            $type               = sanitize_text_field( $_POST['ays_question_type'] );
            $correct_answers    = array_map( 'sanitize_text_field', $_POST['ays-correct-answer'] );
            $answer_values      = array_map( 'wp_kses_post', $_POST['ays-correct-answer-value'] );
            $answer_placeholders = isset($_POST['ays-answer-placeholder']) ? array_map( 'sanitize_text_field', $_POST['ays-answer-placeholder'] ) : array();
            $wrong_answer_text  = wp_kses_post( $_POST['wrong_answer_text'] );
            $right_answer_text  = wp_kses_post( $_POST['right_answer_text'] );
            $explanation        = wp_kses_post( $_POST['explanation'] );
            $not_influence_to_score = (isset($_POST['ays_not_influence_to_score']) && $_POST['ays_not_influence_to_score'] == 'on') ? 'on' : 'off';

            $quest_create_date  = !isset($_POST['ays_question_ctrate_date']) ? '0000-00-00 00:00:00' : sanitize_text_field( $_POST['ays_question_ctrate_date'] );
            $author = isset($_POST['ays_question_author']) ? stripslashes( sanitize_text_field( $_POST['ays_question_author'] ) ) : '';
            $author = json_decode($author, true);
            
            // Use HTML for answers
            $use_html = (isset($_POST['ays-use-html']) && $_POST['ays-use-html'] == 'on') ? 'on' : 'off';

            // Maximum length of a text field
            $enable_question_text_max_length = (isset($_POST['ays_enable_question_text_max_length']) && sanitize_text_field( $_POST['ays_enable_question_text_max_length'] ) == 'on') ? 'on' : 'off';

            // Length
            $question_text_max_length = ( isset($_POST['ays_question_text_max_length']) && sanitize_text_field( $_POST['ays_question_text_max_length'] ) != '' ) ? absint( intval( sanitize_text_field ( $_POST['ays_question_text_max_length'] ) ) ) : '';

            // Limit by
            $question_limit_text_type = ( isset($_POST['ays_question_limit_text_type']) && sanitize_text_field( $_POST['ays_question_limit_text_type'] ) != '' ) ? sanitize_text_field( $_POST['ays_question_limit_text_type'] ) : 'characters';

            // Show the counter-message
            $question_enable_text_message = ( isset($_POST['ays_question_enable_text_message']) && sanitize_text_field( $_POST['ays_question_enable_text_message'] ) == 'on' ) ? 'on' : 'off';


            // Maximum length of a text field
            $enable_question_number_max_length = (isset($_POST['ays_enable_question_number_max_length']) && sanitize_text_field( $_POST['ays_enable_question_number_max_length'] ) == 'on') ? 'on' : 'off';

            // Length
            $question_number_max_length = ( isset($_POST['ays_question_number_max_length']) && sanitize_text_field( $_POST['ays_question_number_max_length'] ) != '' ) ? intval( sanitize_text_field ( $_POST['ays_question_number_max_length'] ) ) : '';

            // Hide question text on the front-end
            $quiz_hide_question_text = ( isset($_POST['ays_quiz_hide_question_text']) && sanitize_text_field( $_POST['ays_quiz_hide_question_text'] ) == 'on' ) ? 'on' : 'off';

            // Enable maximum selection number
            $enable_max_selection_number = (isset($_POST['ays_enable_max_selection_number']) && sanitize_text_field( $_POST['ays_enable_max_selection_number'] ) == 'on') ? 'on' : 'off';

            // Max value
            $max_selection_number = ( isset($_POST['ays_max_selection_number']) && $_POST['ays_max_selection_number'] != '' ) ? intval( sanitize_text_field ( $_POST['ays_max_selection_number'] ) ) : '';

            // Note text
            $quiz_question_note_message = ( isset($_POST['ays_quiz_question_note_message']) && $_POST['ays_quiz_question_note_message'] != '' ) ? wp_kses_post( $_POST['ays_quiz_question_note_message'] ) : '';

            // Enable case sensitive text
            $enable_case_sensitive_text = (isset($_POST['ays_enable_case_sensitive_text']) && sanitize_text_field( $_POST['ays_enable_case_sensitive_text'] ) == 'on') ? 'on' : 'off';

            // Minimum length of a text field
            $enable_question_number_min_length = (isset($_POST['ays_enable_question_number_min_length']) && sanitize_text_field( $_POST['ays_enable_question_number_min_length'] ) == 'on') ? 'on' : 'off';

            // Length
            $question_number_min_length = ( isset($_POST['ays_question_number_min_length']) && sanitize_text_field( $_POST['ays_question_number_min_length'] ) != '' ) ? intval( sanitize_text_field ( $_POST['ays_question_number_min_length'] ) ) : '';

            // Show error message
            $enable_question_number_error_message = (isset($_POST['ays_enable_question_number_error_message']) && sanitize_text_field( $_POST['ays_enable_question_number_error_message'] ) == 'on') ? 'on' : 'off';

            // Message
            $question_number_error_message = ( isset($_POST['ays_question_number_error_message']) && sanitize_text_field( $_POST['ays_question_number_error_message'] ) != '' ) ? stripslashes( sanitize_text_field ( $_POST['ays_question_number_error_message'] ) ) : '';

            // Enable minimum selection number
            $enable_min_selection_number = (isset($_POST['ays_enable_min_selection_number']) && sanitize_text_field( $_POST['ays_enable_min_selection_number'] ) == 'on') ? 'on' : 'off';

            // Min value
            $min_selection_number = ( isset($_POST['ays_min_selection_number']) && $_POST['ays_min_selection_number'] != '' ) ? absint( sanitize_text_field ( $_POST['ays_min_selection_number'] ) ) : '';

            $options = array(
                'author'                                => json_encode($author),
                'use_html'                              => $use_html,
                'enable_question_text_max_length'       => $enable_question_text_max_length,
                'question_text_max_length'              => $question_text_max_length,
                'question_limit_text_type'              => $question_limit_text_type,
                'question_enable_text_message'          => $question_enable_text_message,
                'enable_question_number_max_length'     => $enable_question_number_max_length,
                'question_number_max_length'            => $question_number_max_length,
                'quiz_hide_question_text'               => $quiz_hide_question_text,
                'enable_max_selection_number'           => $enable_max_selection_number,
                'max_selection_number'                  => $max_selection_number,
                'quiz_question_note_message'            => $quiz_question_note_message,
                'enable_case_sensitive_text'            => $enable_case_sensitive_text,
                'enable_question_number_min_length'     => $enable_question_number_min_length,
                'question_number_min_length'            => $question_number_min_length,
                'enable_question_number_error_message'  => $enable_question_number_error_message,
                'question_number_error_message'         => $question_number_error_message,
                'enable_min_selection_number'           => $enable_min_selection_number,
                'min_selection_number'                  => $min_selection_number,
            );

            $text_types = array('text', 'short_text', 'number');
            if($id == 0) {
                $question_result = $wpdb->insert(
                    $questions_table,
                    array(
                        'category_id'               => $category_id,
                        'question'                  => $question,
                        'question_image'            => $question_image,
                        'type'                      => $type,
                        'published'                 => $published,
                        'wrong_answer_text'         => $wrong_answer_text,
                        'right_answer_text'         => $right_answer_text,
                        'question_hint'             => $question_hint,
                        'explanation'               => $explanation,
                        'create_date'               => $quest_create_date,
                        'not_influence_to_score'    => $not_influence_to_score,
                        'options'                   => json_encode($options),
                    ),
                    array(
                        '%d', // category_id
                        '%s', // question
                        '%s', // question_image
                        '%s', // type
                        '%d', // published
                        '%s', // wrong_answer_text
                        '%s', // right_answer_text
                        '%s', // question_hint
                        '%s', // explanation
                        '%s', // create_date
                        '%s', // not_influence_to_score
                        '%s', // options
                    )
                );

                $answers_results = array();
                $question_id = $wpdb->insert_id;
                $flag = true;
                foreach ($answer_values as $index => $answer_value) {
                    $answer_value = stripslashes($answer_value);
                    if(in_array( $type, $text_types )){
                        $correct = 1;
                        $answer_value = htmlspecialchars_decode($answer_value, ENT_QUOTES );
                    }else{
                        $correct = (in_array(($index + 1), $correct_answers)) ? 1 : 0;
                    }
                    if (!in_array( $type, $text_types ) && trim($answer_value) == '') {
                        continue;
                    }
                    $placeholder = '';
                    if(isset($answer_placeholders[$index])){
                        $placeholder = $answer_placeholders[$index];
                    }
                    $answers_results[] = $wpdb->insert(
                        $answers_table,
                        array(
                            'question_id'   => $question_id,
                            'answer'        => (trim($answer_value)),
                            'correct'       => $correct,
                            'ordering'      => ($index + 1),
                            'placeholder'   => $placeholder
                        ),
                        array(
                            '%d', // question_id
                            '%s', // answer
                            '%d', // correct
                            '%d', // ordering
                            '%s'  // placeholder
                        )
                    );
                }

                foreach ($answers_results as $answers_result) {
                    if ($answers_result >= 0) {
                        $flag = true;
                    } else {
                        $flag = false;
                        break;
                    }
                }
                $message = 'created';
            }else{
                $question_result = $wpdb->update(
                    $questions_table,
                    array(
                        'category_id'               => $category_id,
                        'question'                  => $question,
                        'question_image'            => $question_image,
                        'type'                      => $type,
                        'published'                 => $published,
                        'wrong_answer_text'         => $wrong_answer_text,
                        'right_answer_text'         => $right_answer_text,
                        'question_hint'             => $question_hint,
                        'explanation'               => $explanation,
                        'create_date'               => $quest_create_date,
                        'not_influence_to_score'    => $not_influence_to_score,
                        'options'                   => json_encode($options),

                    ),
                    array( 'id' => $id ),
                    array(
                        '%d', // category_id
                        '%s', // question
                        '%s', // question_image
                        '%s', // type
                        '%d', // published
                        '%s', // wrong_answer_text
                        '%s', // right_answer_text
                        '%s', // question_hint
                        '%s', // explanation
                        '%s', // create_date
                        '%s', // not_influence_to_score
                        '%s', // options
                    ),
                    array( '%d' )
                );

                $answers_results = array();
                $flag = true;
                $old_answers = $this->get_question_answers( $id );
                $old_answers_count = count( $old_answers );

                if($old_answers_count == count($answer_values)){
                    foreach ($answer_values as $index => $answer_value) {
                        $answer_value = stripslashes($answer_value);
                        if(in_array( $type, $text_types )){
                            $correct = 1;
                            $answer_value = htmlspecialchars_decode($answer_value, ENT_QUOTES );
                        }else{
                            $correct = (in_array(($index + 1), $correct_answers)) ? 1 : 0;
                        }
                        if (!in_array( $type, $text_types ) && trim($answer_value) == '') {
                            continue;
                        }
                        $placeholder = '';
                        if(isset($answer_placeholders[$index])){
                            $placeholder = $answer_placeholders[$index];
                        }
                        $answers_results[] = $wpdb->update(
                            $answers_table,
                            array(
                                'question_id'   => $id,
                                'answer'        => (trim($answer_value)),
                                'correct'       => $correct,
                                'ordering'      => ($index + 1),
                                'placeholder'   => $placeholder
                            ),
                            array('id' => $old_answers[$index]["id"]),
                            array(
                                '%d', // question_id
                                '%s', // answer
                                '%d', // correct
                                '%d', // ordering
                                '%s'  // placeholder
                            ),
                            array('%d')
                        );
                    }
                }

                if($old_answers_count < count($answer_values)){
                    foreach ($answer_values as $index => $answer_value) {
                        $answer_value = stripslashes($answer_value);
                        if(in_array( $type, $text_types )){
                            $correct = 1;
                            $answer_value = htmlspecialchars_decode($answer_value, ENT_QUOTES );
                        }else{
                            $correct = (in_array(($index + 1), $correct_answers)) ? 1 : 0;
                        }
                        if (!in_array( $type, $text_types ) && trim($answer_value) == '') {
                            continue;
                        }
                        $placeholder = '';
                        if(isset($answer_placeholders[$index])){
                            $placeholder = $answer_placeholders[$index];
                        }
                        if( $old_answers_count < ( $index + 1) ){
                            $answers_results[] = $wpdb->insert(
                                $answers_table,
                                array(
                                    'question_id'   => $id,
                                    'answer'        => (trim($answer_value)),
                                    'correct'       => $correct,
                                    'ordering'      => ($index + 1),
                                    'placeholder'   => $placeholder
                                ),
                                array(
                                    '%d', // question_id
                                    '%s', // answer
                                    '%d', // correct
                                    '%d', // ordering
                                    '%s'  // placeholder
                                )
                            );
                        }else{
                            $answers_results[] = $wpdb->update(
                                $answers_table,
                                array(
                                    'question_id'   => $id,
                                    'answer'        => (trim($answer_value)),
                                    'correct'       => $correct,
                                    'ordering'      => ($index + 1),
                                    'placeholder'   => $placeholder
                                ),
                                array('id' => $old_answers[$index]["id"]),
                                array(
                                    '%d', // question_id
                                    '%s', // answer
                                    '%d', // correct
                                    '%d', // ordering
                                    '%s'  // placeholder
                                ),
                                array('%d')
                            );
                        }
                    }
                }

                if($old_answers_count > count($answer_values)){
                    $answer_value = stripslashes($answer_value);
                    $diff = $old_answers_count - count($answer_values);

                    $removeable_answers = array_slice( $old_answers, -$diff, $diff );

                    foreach ( $removeable_answers as $removeable_answer ){
                        $delete_result = $wpdb->delete( $answers_table, array('id' => intval( $removeable_answer["id"] )) );
                    }

                    foreach ($answer_values as $index => $answer_value) {
                        if(in_array( $type, $text_types )){
                            $correct = 1;
                            $answer_value = htmlspecialchars_decode($answer_value, ENT_QUOTES );
                        }else{
                            $correct = (in_array(($index + 1), $correct_answers)) ? 1 : 0;
                        }
                        if (!in_array( $type, $text_types ) && trim($answer_value) == '') {
                            continue;
                        }
                        $placeholder = '';
                        if(isset($answer_placeholders[$index])){
                            $placeholder = $answer_placeholders[$index];
                        }
                        $answers_results[] = $wpdb->update(
                            $answers_table,
                            array(
                                'question_id'   => $id,
                                'answer'        => (trim($answer_value)),
                                'correct'       => $correct,
                                'ordering'      => ($index + 1),
                                'placeholder'   => $placeholder
                            ),
                            array('id' => $old_answers[$index]["id"]),
                            array(
                                '%d', // question_id
                                '%s', // answer
                                '%d', // correct
                                '%d', // ordering
                                '%s'  // placeholder
                            ),
                            array('%d')
                        );
                    }
                }

                foreach ($answers_results as $answers_result) {
                    if ($answers_result >= 0) {
                        $flag = true;
                    } else {
                        $flag = false;
                        break;
                    }
                }
                $message = "updated";
            }

            $ays_question_tab = isset($_POST['ays_question_tab']) ? sanitize_text_field( $_POST['ays_question_tab'] ) : 'tab1';
            
            if( $question_result >= 0 && $flag == true ) {
                if($ays_change_type == 'apply'){
                    if($id == null){
                        $url = esc_url_raw( add_query_arg( array(
                            "action"    => "edit",
                            "question"  => $question_id,
                            "tab"       => $ays_question_tab,
                            "status"    => $message
                        ) ) );
                    }else{
                        // $url = esc_url_raw( add_query_arg( array(
                        //     "tab"       => $ays_question_tab,
                        //     "status"    => $message
                        // ) ) );
                        $url = esc_url_raw( remove_query_arg(false) ) . '&status=' . $message;
                    }
                    wp_redirect( $url );
                }elseif($ays_change_type == 'save_new'){
                    $url = remove_query_arg( array('question') );
                    $url = add_query_arg( array(
                        "action"    => "add",
                        "status"    => $message
                    ), $url );
                    wp_redirect( $url );
                }else{
                    $url = esc_url_raw( remove_query_arg( array('action', 'question') ) ) . '&status=' . $message;
                    wp_redirect( $url );
                }
            }

        }
    }

    public function duplicate_question($id){
        global $wpdb;
        $questions_table = $wpdb->prefix . "aysquiz_questions";
        $answers_table = $wpdb->prefix . "aysquiz_answers";

        $questionDup = $this->get_question($id);
        $asnwers = $this->get_question_answers($id);
        $user_id = get_current_user_id();
        $user = get_userdata($user_id);
        $author = array(
            'id' => $user->ID,
            'name' => $user->data->display_name
        );
        $question_options = (isset($questionDup['options']) && $questionDup['options'] != '') ? json_decode($questionDup['options'] ,true) : array();

        // Use HTML
        $question_options['use_html'] = isset($question_options['use_html']) ? sanitize_text_field($question_options['use_html']) : 'off';
        $use_html = (isset($question_options['use_html']) && sanitize_text_field( $question_options['use_html'] ) == 'on') ? 'on' : 'off';

        // Maximum length of a text field
        $question_options['enable_question_text_max_length'] = isset($question_options['enable_question_text_max_length']) ? sanitize_text_field($question_options['enable_question_text_max_length']) : 'off';
        $enable_question_text_max_length = (isset($question_options['enable_question_text_max_length']) && sanitize_text_field( $question_options['enable_question_text_max_length'] ) == 'on') ? 'on' : 'off';

        // Length
        $question_text_max_length = ( isset($question_options['question_text_max_length']) && sanitize_text_field( $question_options['question_text_max_length'] ) != '' ) ? absint( intval( sanitize_text_field( $question_options['question_text_max_length'] ) ) ) : '';

        // Limit by
        $question_limit_text_type = ( isset($question_options['question_limit_text_type']) && sanitize_text_field( $question_options['question_limit_text_type'] ) != '' ) ? sanitize_text_field( $question_options['question_limit_text_type'] ) : 'characters';

        // Show the counter-message
        $question_options['question_enable_text_message'] = isset($question_options['question_enable_text_message']) ? sanitize_text_field( $question_options['question_enable_text_message'] ) : 'off';
        $question_enable_text_message = (isset($question_options['question_enable_text_message']) && $question_options['question_enable_text_message'] == 'on') ? 'on' : 'off';

        // Maximum length of a number field
        $question_options['enable_question_number_max_length'] = isset($question_options['enable_question_number_max_length']) ? sanitize_text_field( $question_options['enable_question_number_max_length'] ) : 'off';
        $enable_question_number_max_length = (isset($question_options['enable_question_number_max_length']) && sanitize_text_field( $question_options['enable_question_number_max_length'] ) == 'on') ? 'on' : 'off';

        // Length
        $question_number_max_length = ( isset($question_options['question_number_max_length']) && sanitize_text_field( $question_options['question_number_max_length'] ) != '' ) ? intval( sanitize_text_field( $question_options['question_number_max_length'] ) ) : '';

        // Hide question text on the front-end
        $question_options['quiz_hide_question_text'] = isset($question_options['quiz_hide_question_text']) ? sanitize_text_field( $question_options['quiz_hide_question_text'] ) : 'off';
        $quiz_hide_question_text = (isset($question_options['quiz_hide_question_text']) && $question_options['quiz_hide_question_text'] == 'on') ? 'on' : 'off';

        // Enable maximum selection number
        $question_options['enable_max_selection_number'] = isset($question_options['enable_max_selection_number']) ? sanitize_text_field( $question_options['enable_max_selection_number'] ) : 'off';
        $enable_max_selection_number = (isset($question_options['enable_max_selection_number']) && sanitize_text_field( $question_options['enable_max_selection_number'] ) == 'on') ? 'on' : 'off';

        // Max value
        $max_selection_number = ( isset($question_options['max_selection_number']) && $question_options['max_selection_number'] != '' ) ? intval( sanitize_text_field ( $question_options['max_selection_number'] ) ) : '';

        // Note text
        $quiz_question_note_message = ( isset($question_options['quiz_question_note_message']) && $question_options['quiz_question_note_message'] != '' ) ? wp_kses_post( $question_options['quiz_question_note_message'] ) : '';

        // Enable case sensitive text
        $enable_case_sensitive_text = (isset($question_options['enable_case_sensitive_text']) && sanitize_text_field( $question_options['enable_case_sensitive_text'] ) == 'on') ? 'on' : 'off';

        // Minimum length of a number field
        $question_options['enable_question_number_min_length'] = isset($question_options['enable_question_number_min_length']) ? sanitize_text_field( $question_options['enable_question_number_min_length'] ) : 'off';
        $enable_question_number_min_length = (isset($question_options['enable_question_number_min_length']) && sanitize_text_field( $question_options['enable_question_number_min_length'] ) == 'on') ? 'on' : 'off';

        // Length
        $question_number_min_length = ( isset($question_options['question_number_min_length']) && sanitize_text_field( $question_options['question_number_min_length'] ) != '' ) ? intval( sanitize_text_field( $question_options['question_number_min_length'] ) ) : '';

        // Show error message
        $question_options['enable_question_number_error_message'] = isset($question_options['enable_question_number_error_message']) ? sanitize_text_field( $question_options['enable_question_number_error_message'] ) : 'off';
        $enable_question_number_error_message = (isset($question_options['enable_question_number_error_message']) && sanitize_text_field( $question_options['enable_question_number_error_message'] ) == 'on') ? 'on' : 'off';

        // Message
        $question_number_error_message = ( isset($question_options['question_number_error_message']) && sanitize_text_field( $question_options['question_number_error_message'] ) != '' ) ? stripslashes( sanitize_text_field( $question_options['question_number_error_message'] ) ) : '';

        // Enable minimum selection number
        $question_options['enable_min_selection_number'] = isset($question_options['enable_min_selection_number']) ? sanitize_text_field( $question_options['enable_min_selection_number'] ) : 'off';
        $enable_min_selection_number = (isset($question_options['enable_min_selection_number']) && sanitize_text_field( $question_options['enable_min_selection_number'] ) == 'on') ? 'on' : 'off';

        // Min value
        $min_selection_number = ( isset($question_options['min_selection_number']) && $question_options['min_selection_number'] != '' ) ? intval( sanitize_text_field ( $question_options['min_selection_number'] ) ) : '';

        $options = array(
            'author'                            => $author,
            'use_html'                          => $use_html,
            'enable_question_text_max_length'   => $enable_question_text_max_length,
            'question_text_max_length'          => $question_text_max_length,
            'question_limit_text_type'          => $question_limit_text_type,
            'question_enable_text_message'      => $question_enable_text_message,
            'enable_question_number_max_length' => $enable_question_number_max_length,
            'question_number_max_length'        => $question_number_max_length,
            'quiz_hide_question_text'           => $quiz_hide_question_text,
            'enable_max_selection_number'       => $enable_max_selection_number,
            'max_selection_number'              => $max_selection_number,
            'quiz_question_note_message'        => $quiz_question_note_message,
            'enable_case_sensitive_text'        => $enable_case_sensitive_text,
            'enable_question_number_min_length' => $enable_question_number_min_length,
            'question_number_min_length'        => $question_number_min_length,
            'enable_question_number_error_message' => $enable_question_number_error_message,
            'question_number_error_message'     => $question_number_error_message,
            'enable_min_selection_number'       => $enable_min_selection_number,
            'min_selection_number'              => $min_selection_number,
        );
        
        $question_result = $wpdb->insert(
            $questions_table,
            array(
                'category_id'       => $questionDup['category_id'],
                'question'          => "Copy - " . $questionDup['question'],
                'question_image'    => $questionDup['question_image'],
                'type'              => $questionDup['type'],
                'published'         => $questionDup['published'],
                'wrong_answer_text' => $questionDup['wrong_answer_text'],
                'right_answer_text' => $questionDup['right_answer_text'],
                'question_hint'     => $questionDup['question_hint'],
                'explanation'       => $questionDup['explanation'],
                'create_date'       => current_time( 'mysql' ),
                'options'           => json_encode($options),
            ),
            array(
                '%d', // category_id
                '%s', // question
                '%s', // question_image
                '%s', // type
                '%d', // published
                '%s', // wrong_answer_text
                '%s', // right_answer_text
                '%s', // question_hint
                '%s', // explanation
                '%s', // create_date
                '%s', // options
            )
        );

        $question_id = $wpdb->insert_id;
        $answers_results = array();
        $flag = true;
        foreach ($asnwers as $key => $answer){
            $answers_results[] = $wpdb->insert(
                $answers_table,
                array(
                    'question_id'   => $question_id,
                    'answer'        => $answer['answer'],
                    'correct'       => intval( $answer['correct'] ),
                    'ordering'      => ($key + 1),
                    'placeholder'   => $answer['placeholder']
                ),
                array(
                    '%d', // question_id
                    '%s', // answer
                    '%d', // correct
                    '%d', // ordering
                    '%s'  // placeholder
                )
            );
        }

        foreach ($answers_results as $answers_result) {
            if ($answers_result >= 0) {
                $flag = true;
            } else {
                $flag = false;
                break;
            }
        }
        $message = 'duplicated';
        if( $question_result >= 0 && $flag == true ) {
            $url = esc_url_raw( remove_query_arg(array('action', 'question')  ) ) . '&status=' . $message;
            wp_redirect( $url );
        }
    }
    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public static function record_count() {
        global $wpdb;
        
        $filter = array();
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_questions";
        
        if( isset( $_GET['filterby'] ) && absint( intval( $_GET['filterby'] ) ) > 0){
            $cat_id = absint( intval( $_GET['filterby'] ) );
            $filter[] = ' category_id = '.$cat_id.' ';
        }
        if( isset( $_REQUEST['fstatus'] ) && is_numeric( $_REQUEST['fstatus'] ) && ! is_null( sanitize_text_field( $_REQUEST['fstatus'] ) ) ){
            if( esc_sql( $_REQUEST['fstatus'] ) != '' ){
                $fstatus  = absint( esc_sql( $_REQUEST['fstatus'] ) );
                $filter[] = " published = ".$fstatus." ";
            }
        }
        
        if( isset($_REQUEST['type']) ){
            $filter[] = " type ='". esc_sql( $_REQUEST['type'] ) ."' ";
        }

        $search = ( isset( $_REQUEST['s'] ) ) ? esc_sql( sanitize_text_field( $_REQUEST['s'] ) ) : false;
        if( $search ){
            $filter[] = sprintf(" question LIKE '%%%s%%' ", esc_sql( $wpdb->esc_like( $search ) ) );
        }
        
        if(count($filter) !== 0){
            $sql .= " WHERE ".implode(" AND ", $filter);
        }

        return $wpdb->get_var( $sql );
    }

    public static function all_record_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_questions WHERE 1=1";

        if( isset( $_GET['filterby'] ) && absint( intval( $_GET['filterby'] ) ) > 0){
            $cat_id = absint( intval( $_GET['filterby'] ) );
            $sql .= ' AND category_id = '.$cat_id.' ';
        }
        if( isset($_REQUEST['type']) ){
            $sql .= " AND type ='". esc_sql( $_REQUEST['type'] ) ."' ";
        }
        
        return $wpdb->get_var( $sql );
    }

    public static function published_questions_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_questions WHERE published=1";
        
        if( isset( $_GET['filterby'] ) && absint( intval( $_GET['filterby'] ) ) > 0){
            $cat_id = absint( intval( $_GET['filterby'] ) );
            $sql .= ' AND category_id = '.$cat_id.' ';
        }
        if( isset($_REQUEST['type']) ){
            $sql .= " AND type ='". esc_sql( $_REQUEST['type'] ) ."' ";
        }
        
        return $wpdb->get_var( $sql );
    }
    
    public static function unpublished_questions_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_questions WHERE published=0";

        if( isset( $_GET['filterby'] ) && absint( intval( $_GET['filterby'] ) ) > 0){
            $cat_id = absint( intval( $_GET['filterby'] ) );
            $sql .= ' AND category_id = '.$cat_id.' ';
        }
        if( isset($_REQUEST['type']) ){
            $sql .= " AND type ='". esc_sql( $_REQUEST['type'] ) ."' ";
        }
        
        return $wpdb->get_var( $sql );
    }

    /** Text displayed when no customer data is available */
    public function no_items() {
        echo __( 'There are no questions yet.', $this->plugin_name );
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
            case 'question':
            case 'category_id':
            case 'type':
            case 'items_count':
            case 'create_date':
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
    function column_question( $item ) {
        $current_page = $this->get_pagenum();
        $delete_nonce = wp_create_nonce( $this->plugin_name . '-delete-question' );
        $question_title = '';
        if(isset($item['question']) && strlen($item['question']) != 0){

            $is_exists_ruby = Quiz_Maker_Admin::ays_quiz_is_exists_needle_tag( $item['question'] , '<ruby>' );

            if ( $is_exists_ruby ) {
                $question_title = strip_tags( stripslashes($item['question']), '<ruby><rbc><rtc><rb><rt>' );
            } else {
                $question_title = strip_tags(stripslashes($item['question']));
            }

        }elseif ((isset($item['question_image']) && $item['question_image'] !='')){
            $question_title = 'Image question';
        }

        $question_title = esc_attr( $question_title );

        $q = esc_attr($question_title);
        $question_title_length = intval( $this->title_length );

        $question_title = Quiz_Maker_Admin::ays_restriction_string("word", $question_title, $question_title_length);
        // $title = sprintf( '<a href="?page=%s&action=%s&question=%d" title="%s" >%s</a>', esc_attr( $_REQUEST['page'] ), 'edit', absint( $item['id'] ), $q, $question_title );

        $url = remove_query_arg( array('status') );

        $url_args = array(
            "page"    => esc_attr( $_REQUEST['page'] ),
            "question"    => absint( $item['id'] ),
        );
        $url_args['action'] = "edit";

        if( isset( $_GET['paged'] ) && sanitize_text_field( $_GET['paged'] ) != '' ){
            $url_args['paged'] = $current_page;
        }

        $url = add_query_arg( $url_args, $url );

        $title = sprintf( '<a href="%s" title="%s">%s</a>', $url, $q, $question_title );

        $actions = array(
            'edit' => sprintf( '<a href="%s">'. __('Edit', $this->plugin_name) .'</a>', $url ),
            'duplicate' => sprintf( '<a href="?page=%s&action=%s&question=%d">'. __('Duplicate', $this->plugin_name) .'</a>', esc_attr( $_REQUEST['page'] ), 'duplicate', absint( $item['id'] ) ),
            'delete' => sprintf( '<a class="ays_confirm_del" data-message="%s" href="?page=%s&action=%s&question=%s&_wpnonce=%s">'. __('Delete', $this->plugin_name) .'</a>', $question_title, esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
        );

        return $title . $this->row_actions( $actions );
    }

    function column_category_id( $item ) {
        global $wpdb;

        $question_categories_table = esc_sql( $wpdb->prefix . "aysquiz_categories" );

        $category_id = ( isset( $item['category_id'] ) && $item['category_id'] != "" ) ? absint( sanitize_text_field( $item['category_id'] ) ) : 0;

        $sql = "SELECT * FROM {$question_categories_table} WHERE id=" . $category_id;

        $result = $wpdb->get_row($sql, 'ARRAY_A');

        $results = "";
        if($result !== null){

            $category_title = ( isset( $result['title'] ) && $result['title'] != "" ) ? sanitize_text_field( $result['title'] ) : "";

            if ( $category_title != "" ) {
                $results = sprintf( '<a href="?page=%s&action=edit&question_category=%d" target="_blank">%s</a>', 'quiz-maker-question-categories', $category_id, $category_title);
            }
        }else{
            $results = "";
        }

        return $results;
    }

    function column_published( $item ) {

        $status = (isset( $item['published'] ) && $item['published'] != '') ? absint( sanitize_text_field( $item['published'] ) ) : '';

        $status_html = '';

        switch( $status ) {
            case 1:
                $status_html = '<span class="ays-publish-status"><i class="ays_fa ays_fa_check_square_o" aria-hidden="true"></i>'. __('Published',$this->plugin_name) . '</span>';
                break;
            case 0:
                $status_html = '<span class="ays-publish-status"><i class="ays_fa ays_fa_square_o" aria-hidden="true"></i>'. __('Unpublished',$this->plugin_name) . '</span>';
                break;
             default:
                $status_html = '<span class="ays-publish-status"><i class="ays_fa ays_fa_square_o" aria-hidden="true"></i>'. __('Unpublished',$this->plugin_name) . '</span>';
                break;
        }

        return $status_html;
    }

    function column_create_date( $item ) {
        $options = isset($item['options']) && $item['options'] != '' ? json_decode($item['options'], true) : array();
        $date = isset($item['create_date']) && $item['create_date'] != '' ? $item['create_date'] : "0000-00-00 00:00:00";
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
        if($author['name'] !== "Unknown"){
            $text .= "<p><b>Author:</b> ".$author['name']."</p>";
        }
        return $text;
    }

    function column_type( $item ) {
        $query_str = Quiz_Maker_Admin::ays_query_string(array("status", "type"));

        switch ( $item['type'] ) {
            case 'short_text':
                $question_type = 'short text';
                break;
            case 'true_or_false':
                $question_type = 'true/false';
                break;
            default:
                $question_type = $item['type'];
                break;
        }

        $type = "<a href='?".$query_str."&type=".$item['type']."' >".ucfirst( $question_type )."</a>";
        return $type;
    }


    function column_items_count( $item ) {
        global $wpdb;

        $result = '';
        if ( isset( $item['id'] ) && absint( $item['id'] ) > 0 && ! is_null( sanitize_text_field( $item['id'] ) ) ) {
            $id = absint( esc_sql( $item['id'] ) );

            $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}aysquiz_answers WHERE question_id = " . $id;

            $result = $wpdb->get_var($sql);
        }

        return "<p style='text-align:center;font-size:14px;'>" . $result . "</p>";
    }

    function column_question_image( $item ) {
        global $wpdb;
        $current_page = $this->get_pagenum();

        $question_image = (isset( $item['question_image'] ) && $item['question_image'] != '') ? esc_url( $item['question_image'] ) : '';

        $image_html     = array();
        $edit_page_url  = '';

        if($question_image != ''){

            if ( isset( $item['id'] ) && absint( $item['id'] ) > 0 ) {
                $edit_page_url = sprintf( 'href="?page=%s&paged=%d&action=%s&question=%d"', esc_attr( $_REQUEST['page'] ), $current_page, 'edit', absint( $item['id'] ) );
            }

            $question_image_url = $question_image;
            $this_site_path = trim( get_site_url(), "https:" );
            if( strpos( trim( $question_image_url, "https:" ), $this_site_path ) !== false ){ 
                $query = "SELECT * FROM `" . $wpdb->prefix . "posts` WHERE `post_type` = 'attachment' AND `guid` = '" . $question_image_url . "'";
                $result_img =  $wpdb->get_results( $query, "ARRAY_A" );
                if( ! empty( $result_img ) ){
                    $url_img = wp_get_attachment_image_src( $result_img[0]['ID'], 'thumbnail' );
                    if( $url_img !== false ){
                        $question_image_url = $url_img[0];
                    }
                }
            }

            $image_html[] = '<div class="ays-question-image-list-table-column">';
                $image_html[] = '<a '. $edit_page_url .' class="ays-question-image-list-table-link-column">';
                    $image_html[] = '<img src="'. $question_image_url .'" class="ays-question-image-list-table-img-column">';
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
            'question'          => __( 'Question', $this->plugin_name ),
            'question_image'    => __( 'Image', $this->plugin_name ),
            'category_id'       => __( 'Category', $this->plugin_name ),
            'type'              => __( 'Type', $this->plugin_name ),
            'items_count'       => __( 'Answers Count', $this->plugin_name ),
            'create_date'       => __( 'Created', $this->plugin_name ),
            'published'         => __( 'Status', $this->plugin_name ),
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
            'question'      => array( 'question', true ),
            'category_id'   => array( 'category_id', true ),
            'type'          => array( 'type', true ),
            'id'            => array( 'id', true ),
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

        $per_page     = $this->get_items_per_page( 'questions_per_page', 20 );
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();

        $this->set_pagination_args( array(
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ));

        $search = ( isset( $_REQUEST['s'] ) ) ? esc_sql( sanitize_text_field( $_REQUEST['s'] ) ) : false;

        $do_search = ( $search ) ? sprintf(" question LIKE '%%%s%%' ", esc_sql( $wpdb->esc_like( $search ) ) ) : '';


        $this->items = self::get_questions( $per_page, $current_page, $do_search );
    }

    public function process_bulk_action() {
        //Detect when a bulk action is being triggered...
        if ( 'delete' === $this->current_action() ) {

            // In our file that handles the request, verify the nonce.
            $nonce = esc_attr( $_REQUEST['_wpnonce'] );

            if ( ! wp_verify_nonce( $nonce, $this->plugin_name . '-delete-question' ) ) {
                die( 'Go get a life script kiddies' );
            }
            else {
                self::delete_questions( absint( $_GET['question'] ) );

                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
                // add_query_arg() return the current url

                $url = esc_url_raw( remove_query_arg(array('action', 'question', '_wpnonce')  ) ) . '&status=deleted';
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
                self::delete_questions( $id );

            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url
            $url = esc_url_raw( remove_query_arg(array('action', 'question', '_wpnonce')  ) ) . '&status=deleted';
            wp_redirect( $url );
        } elseif ((isset($_POST['action']) && $_POST['action'] == 'bulk-published')
                  || (isset($_POST['action2']) && $_POST['action2'] == 'bulk-published')
        ) {

            $published_ids = ( isset( $_POST['bulk-delete'] ) && ! empty( $_POST['bulk-delete'] ) ) ? esc_sql( $_POST['bulk-delete'] ) : array();

            // loop over the array of record IDs and mark as read them

            foreach ( $published_ids as $id ) {
                self::ays_quiz_published_unpublished_questions( $id , 'published' );
            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url
            $url = esc_url_raw( remove_query_arg(array('action', 'question', '_wpnonce')  ) ) . '&status=published';
            wp_redirect( $url );
        } elseif ((isset($_POST['action']) && $_POST['action'] == 'bulk-unpublished')
                  || (isset($_POST['action2']) && $_POST['action2'] == 'bulk-unpublished')
        ) {

            $unpublished_ids = ( isset( $_POST['bulk-delete'] ) && ! empty( $_POST['bulk-delete'] ) ) ? esc_sql( $_POST['bulk-delete'] ) : array();

            // loop over the array of record IDs and mark as read them

            foreach ( $unpublished_ids as $id ) {
                self::ays_quiz_published_unpublished_questions( $id , 'unpublished' );
            }

            // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
            // add_query_arg() return the current url
            $url = esc_url_raw( remove_query_arg(array('action', 'question', '_wpnonce')  ) ) . '&status=unpublished';
            wp_redirect( $url );
        }
    }

    public function question_notices(){
        $status = (isset($_REQUEST['status'])) ? sanitize_text_field( $_REQUEST['status'] ) : '';

        if ( empty( $status ) )
            return;

        if ( 'created' == $status )
            $updated_message = esc_html( __( 'Question created.', $this->plugin_name ) );
        elseif ( 'updated' == $status )
            $updated_message = esc_html( __( 'Question saved.', $this->plugin_name ) );
        elseif ( 'duplicated' == $status )
            $updated_message = esc_html( __( 'Question duplicated.', $this->plugin_name ) );
        elseif ( 'published' == $status )
            $updated_message = esc_html( __( 'Question(s) published.', $this->plugin_name ) );
        elseif ( 'unpublished' == $status )
            $updated_message = esc_html( __( 'Question(s) unpublished.', $this->plugin_name ) );
        elseif ( 'deleted' == $status )
            $updated_message = esc_html( __( 'Question deleted.', $this->plugin_name ) );

        if ( empty( $updated_message ) )
            return;

        ?>
        <div class="notice notice-success is-dismissible">
            <p> <?php echo $updated_message; ?> </p>
        </div>
        <?php
    }
}
