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
class Quiz_Theme_Rect_Light extends Quiz_Maker_Public{

    protected $plugin_name;

    protected $version;

    protected $theme_name;

    protected $settings;

    protected $buttons_texts;


    public function __construct($plugin_name,$plugin_version,$theme_name,$settings,$buttons_texts) {
        $this->version = $plugin_version;
        $this->plugin_name = $plugin_name;
        $this->theme_name = $theme_name;
        $this->settings = $settings;
        $this->buttons_texts = $buttons_texts;
        
        $this->define_theme_styles();
        $this->define_theme_scripts();
    }

    protected function define_theme_styles(){
        wp_enqueue_style($this->plugin_name.'rect_light_css',dirname(plugin_dir_url(__FILE__)) . '/css/theme_rect_light.css', array(), time(), 'all');
    }

    protected function define_theme_scripts(){
        wp_enqueue_script(
            $this->plugin_name.'-rect_light_js',
            dirname(plugin_dir_url(__FILE__)) . '/js/theme_rect_light.js',
            array('jquery'),
            $this->version,
            false
        );
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
            <style>";
        if (! $disable_hover_effect ) {
            $additional_css .= "
            #ays-quiz-container-".$quiz_id.".ays_quiz_rect_light .ays-quiz-answers .ays-field:hover {
                background: " . $this->hex2rgba($quiz->quizColors['Color'], '0.3') . ";
            }";
        }

        $additional_css .= "
                #ays-quiz-container-".$quiz_id.".ays_quiz_rect_light .checked_answer_div{
                    background: ".$quiz->quizColors['Color'].";
                }                               
            </style>";
        
        $quiz->quizParts['quiz_additional_styles'] = $additional_css;
        
        $container = implode("", $quiz->quizParts);
        
        return $container;
    }
    
    public function ays_default_answer_html($question_id, $quiz_id, $answers, $options){
        $show_answers_numbering = (isset($options['show_answers_numbering']) && $options['show_answers_numbering'] != '') ? $options['show_answers_numbering'] : 'none';
        $numbering_type_arr = $this->ays_answer_numbering($show_answers_numbering);
        $numbering_type = '';
        $answer_container = "";
        foreach ($answers as $key => $answer) {
            $answer_image = (isset($answer['image']) && $answer['image'] != '') ? "<img src='{$answer["image"]}' alt='answer_image' class='ays-answer-image'>" : "";

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
            
            if($options['useHTML']){
                $answer_content = do_shortcode((stripslashes($answer["answer"])));
            }else{
                $answer_content = do_shortcode(htmlspecialchars(stripslashes($answer["answer"])));
            }

            if ( ! empty($numbering_type_arr) ) {
                $numbering_type = (isset($numbering_type_arr[$key]) && $numbering_type_arr[$key] != '') ? $numbering_type_arr[$key] :'';
                $numbering_type = $numbering_type . ' ';
            }

            $answer_container .= "
            <div class='ays-field ays_".$options['answersViewClass']."_view_item'>
                <input type='hidden' name='ays_answer_correct[]' value='{$answer["correct"]}'/>

                <input type='{$options["questionType"]}' name='ays_questions[ays-question-{$question_id}]' id='ays-answer-{$answer["id"]}-{$quiz_id}' value='{$answer["id"]}'/>
                    <label for='ays-answer-{$answer["id"]}-{$quiz_id}'>
                        ". $numbering_type . $answer_content . "
                    </label>
                    <label for='ays-answer-{$answer["id"]}-{$quiz_id}'>{$answer_image}</label>

                    {$answer_container_script}
            </div>";

        }  
        return $answer_container;
    }
    
}

?>