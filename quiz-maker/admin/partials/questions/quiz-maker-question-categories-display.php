<?php
/**
 * Created by PhpStorm.
 * User: biggie18
 * Date: 6/15/18
 * Time: 3:34 PM
 */
?>

<div class="wrap">
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
    <div class="nav-tab-wrapper">
        <a href="#poststuff" class="nav-tab nav-tab-active">
            <?php echo __("Categories", $this->plugin_name);?>
        </a>
        <a href="#question_tags" class="nav-tab">
            <?php echo __("Tags", $this->plugin_name);?>
        </a>
    </div>
    <div id="poststuff" class="ays-quiz-tab-content ays-quiz-tab-content-active">
        <div id="post-body" class="metabox-holder">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <?php
                        $this->question_categories_obj->views();
                    ?>
                    <form method="post">
                        <?php
                            $this->question_categories_obj->prepare_items();
                            $this->question_categories_obj->search_box('Search', $this->plugin_name);
                            $this->question_categories_obj->display();
                        ?>
                    </form>
                </div>
            </div>
        </div>
        <br class="clear">
        <h1 class="wp-heading-inline">
            <?php
            echo __(esc_html(get_admin_page_title()),$this->plugin_name);
            echo sprintf( '<a href="?page=%s&action=%s" class="page-title-action button-primary ays-quiz-add-new-button">' . __('Add New', $this->plugin_name) . '</a>', esc_attr( $_REQUEST['page'] ), 'add');
            ?>
        </h1>
    </div>
    <div id="question_tags" class="ays-quiz-tab-content">
        <div class="row" style="margin: 0; margin-top:20px;">
            <div class="col-sm-12">
                <div class="pro_features">
                    <div>
                        <p>
                            <?php echo __("This feature is available only in ", $this->plugin_name); ?>
                            <a href="https://ays-pro.com/wordpress/quiz-maker/" target="_blank" title="PRO feature"><?php echo __("PRO version!!!", $this->plugin_name); ?></a>
                        </p>
                        <p class="ays-quiz-pro-features-text">
                            <?php echo __("This feature is available only in ", $this->plugin_name); ?>
                            <a href="https://ays-pro.com/wordpress/quiz-maker/" target="_blank" title="PRO feature"><?php echo __("PRO version!!!", $this->plugin_name); ?></a>
                        </p>
                    </div>
                </div>
                <img src="<?php echo AYS_QUIZ_ADMIN_URL.'/images/question_tags_screen.png'?>" alt="<?php echo __( "Question Tags", $this->plugin_name ) ?>" style="width:100%;" >
            </div>
        </div>
    </div>
    
</div>
