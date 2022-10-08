<?php

$tab_url = "?page=".$this->plugin_name."-results&ays_result_tab=";

?>
<div class="wrap ays_reviews_table">
    <div class="ays-quiz-heading-box">
        <div class="ays-quiz-wordpress-user-manual-box">
            <a href="https://ays-pro.com/wordpress-quiz-maker-user-manual" target="_blank"><?php echo __("View Documentation", $this->plugin_name); ?></a>
        </div>
    </div>
    <h1 class="wp-heading-inline">
        <?php
        echo __(esc_html(get_admin_page_title()),$this->plugin_name);
        ?>
    </h1>
    <div class="ays-quiz-export-import-box">
        <div>
            <a href="https://ays-pro.com/wordpress/quiz-maker/" target="_blank" class="ays-quiz-export-button-link">
                <button class="disabled-button" title="<?php echo __( "This property aviable only in pro version", $this->plugin_name ); ?>" ><?php echo __('Export',$this->plugin_name)?></button>
            </a>
        </div>
    </div>
    <div class="nav-tab-wrapper">
        <a href="<?php echo $tab_url . "poststuff"; ?>" class="no-js nav-tab"><?php echo __('Results',$this->plugin_name)?></a>
        <a href="<?php echo $tab_url . "statistics"; ?>" class="no-js nav-tab"><?php echo __('Statistics',$this->plugin_name)?></a>
        <a href="<?php echo $tab_url . "leaderboard"; ?>" class="no-js nav-tab"><?php echo __('Leaderboard',$this->plugin_name)?></a>
        <a href="<?php echo "?page=".$this->plugin_name."-all-reviews&tab=" . "reviews"; ?>" class="no-js nav-tab nav-tab-active"><?php echo __('Reviews',$this->plugin_name)?></a>
    </div>
    <div id="reviews" class="ays-quiz-tab-content ays-quiz-tab-content-active">
        <div id="review-poststuff">
            <div id="post-body" class="metabox-holder">
                <div id="post-body-content">
                    <div class="meta-box-sortables ui-sortable">
                        <?php
                            $this->all_reviews_obj->views();
                        ?>
                        <form method="post">
                            <?php
                            $this->all_reviews_obj->prepare_items();
                            $this->all_reviews_obj->search_box( 'Search', $this->plugin_name );
                            $this->all_reviews_obj->display();
                            ?>
                        </form>
                    </div>
                </div>
            </div>
            <br class="clear">
        </div>
    </div>

    <div id="ays-reviews-modal" class="ays-modal">
        <div class="ays-modal-content">
            <div class="ays-quiz-preloader">
                <img class="loader" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/loaders/3-1.svg">
            </div>
            <div class="ays-modal-header">
                <span class="ays-close" id="ays-close-reviews">&times;</span>
                <h2><?php echo __("Reviews for", $this->plugin_name); ?></h2>
            </div>
            <div class="ays-modal-body" id="ays-reviews-body">
            </div>
        </div>
    </div>

    <div id="ays-results-modal" class="ays-modal">
        <div class="ays-modal-content">
            <div class="ays-quiz-preloader">
                <img class="loader" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/loaders/3-1.svg">
            </div>
            <div class="ays-modal-header">
                <span class="ays-close" id="ays-close-results">&times;</span>
                <h2><?php echo __("Detailed report", $this->plugin_name); ?></h2>
            </div>
            <div class="ays-modal-body" id="ays-results-body">
            </div>
        </div>
    </div>
    <div class="ays-modal" id="export-answers-filters">
        <div class="ays-modal-content">
            <div class="ays-quiz-preloader">
                <img class="loader" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/loaders/3-1.svg">
            </div>
          <!-- Modal Header -->
            <div class="ays-modal-header">
                <span class="ays-close">&times;</span>
                <h2><?=__('Export Filter', $this->plugin_name)?></h2>
            </div>

          <!-- Modal body -->
            <div class="ays-modal-body">
                <form method="post" id="ays_export_answers_filter">
                    <div class="filter-col">
                        <label for="user_id-answers-filter"><?=__("Users", $this->plugin_name)?></label>
                        <button type="button" class="ays_userid_clear button button-small wp-picker-default"><?=__("Clear", $this->plugin_name)?></button>
                        <select name="user_id-select[]" id="user_id-answers-filter" multiple="multiple"></select>
                        <input type="hidden" name="quiz_id-answers-filter" id="quiz_id-answers-filter">
                    </div>
                    <div class="filter-block">
                        <div class="filter-block filter-col">
                            <label for="start-date-answers-filter"><?=__("Start Date from", $this->plugin_name)?></label>
                            <input type="date" name="start-date-filter" id="start-date-answers-filter">
                        </div>
                        <div class="filter-block filter-col">
                            <label for="end-date-answers-filter"><?=__("Start Date to", $this->plugin_name)?></label>
                            <input type="date" name="end-date-filter" id="end-date-answers-filter">
                        </div>
                    </div>
                </form>
            </div>

          <!-- Modal footer -->
            <div class="ays-modal-footer">
                <div class="export_results_count">
                    <p>Matched <span></span> results</p>
                </div>
                <span><?php echo __('Export to', $this->plugin_name); ?></span>
                <button type="button" class="button button-primary export-anwers-action" data-type="xlsx"><?=__('XLSX', $this->plugin_name)?></button>
                <a download="" id="downloadFile" hidden href=""></a>
            </div>

        </div>
    </div>
</div>

