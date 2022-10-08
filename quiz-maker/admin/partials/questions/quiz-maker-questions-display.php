<?php
global $wpdb;

$action = ( isset($_GET['action']) ) ? sanitize_text_field( $_GET['action'] ) : '';
$id     = ( isset($_GET['question']) ) ? sanitize_text_field( $_GET['question'] ) : null;

if($action == 'duplicate'){
    $this->questions_obj->duplicate_question($id);
}
$example_export_path = AYS_QUIZ_ADMIN_URL . '/partials/questions/export_file/';

?>

<div class="wrap ays_questions_list_table">
    <div class="ays-quiz-heading-box">
        <div class="ays-quiz-wordpress-user-manual-box">
            <a href="https://ays-pro.com/wordpress-quiz-maker-user-manual" target="_blank"><?php echo __("View Documentation", $this->plugin_name); ?></a>
        </div>
    </div>
    <h1 class="wp-heading-inline">
        <?php
            echo __(esc_html(get_admin_page_title()),$this->plugin_name);
            echo sprintf( '<a href="?page=%s&action=%s" class="page-title-action button-primary ays-quiz-add-new-button">' . __('Add New', $this->plugin_name) . '</a>', esc_attr( $_REQUEST['page'] ), 'add');
        ?>
    </h1>


    <div class="question-action-butons">
        <!--<div class="ays-quiz-export-import-box">
            <div class="ays-quiz-export-import-video-tutorial">
                <a href="https://www.youtube.com/watch?v=RldosodJItI&" target="_blank">
                    <?php echo __('Video tutorial',$this->plugin_name); ?>
                </a>
            </div>
        </div> -->
        <div class="ays-quiz-export-import-tooltip">
            <a class="ays_help mr-2" style="font-size:20px;" data-toggle="tooltip" title="<?php echo __("For import XLSX file your version of PHP must be over than 5.6.", $this->plugin_name); ?>">
                <i class="ays_fa ays_fa_info_circle"></i>
            </a>
        </div>
        <div class="dropdown ays-export-dropdown" style="">
            <a href="javascript:void(0);" data-toggle="dropdown" class="button mr-2 dropdown-toggle">
                <span class="ays-wp-loading d-none"></span>
                <?php echo __('Example', $this->plugin_name); ?>
            </a>
            <div class="dropdown-menu dropdown-menu-right ays-dropdown-menu">
                <a href="<?php echo $example_export_path; ?>example_questions_export.csv"                    
                   download="example_questions_export.csv" class="dropdown-item">
                    CSV
                </a>
                <a href="<?php echo $example_export_path; ?>example_questions_export.xlsx"
                   download="example_questions_export.xlsx" class="dropdown-item">
                    XLSX
                </a>
                <a href="<?php echo $example_export_path; ?>example_questions_export.json"
                   download="example_questions_export.json" class="dropdown-item">
                    JSON
                </a>
                <a href="<?php echo $example_export_path; ?>example_questions_export_simple.xlsx"
                   download="example_questions_export_simple.xlsx" class="dropdown-item">
                    Simple XLSX
                </a>
            </div>
        </div>
        <a href="https://ays-pro.com/wordpress/quiz-maker/" target="_blank" title="<?php echo __('This property aviable only in pro version',$this->plugin_name); ?>" class="button disabled-button"><?php echo __('Export to', $this->plugin_name); ?></a>
        <a href="https://ays-pro.com/wordpress/quiz-maker/" target="_blank" title="<?php echo __('This property aviable only in pro version',$this->plugin_name); ?>" class="button disabled-button" aria-expanded="false"><?= __('Import', $this->plugin_name); ?></a>
    </div>
    <div class="ays-quiz-heading-box ays-quiz-unset-float">
        <div class="ays-quiz-wordpress-user-manual-box ays-quiz-wordpress-text-align">
            <a href="https://www.youtube.com/watch?v=RldosodJItI" target="_blank">
                <?php echo __("How to export/import questions - video", $this->plugin_name); ?>
            </a>
        </div>
    </div>

    <hr/>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <?php
                        $this->questions_obj->views();
                    ?>
                    <form method="post">
                        <?php
                            $this->questions_obj->prepare_items();
                            $this->questions_obj->search_box('Search', $this->plugin_name);
                            $this->questions_obj->display();
                        ?>
                    </form>
                </div>
            </div>
        </div>
        <br class="clear">
    </div>

    <h1 class="wp-heading-inline">
        <?php
            echo __(esc_html(get_admin_page_title()),$this->plugin_name);
            echo sprintf( '<a href="?page=%s&action=%s" class="page-title-action button-primary ays-quiz-add-new-button">' . __('Add New', $this->plugin_name) . '</a>', esc_attr( $_REQUEST['page'] ), 'add');
        ?>
    </h1>
</div>
