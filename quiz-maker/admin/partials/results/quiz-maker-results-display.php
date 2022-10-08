<?php

if(isset($_GET['ays_result_tab'])){
    $tab = sanitize_text_field( $_GET['ays_result_tab'] );
}else{
    $tab = 'poststuff';
}

$reviews_page_url = sprintf('?page=%s', $this->plugin_name."-all-reviews");

?>
<div class="wrap ays_results_list_table">
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
        <a href="#tab1" class="nav-tab <?php echo ($tab == 'poststuff') ? 'nav-tab-active' : ''; ?>"><?php echo __('Results',$this->plugin_name)?></a>
        <a href="#tab2" class="nav-tab <?php echo ($tab == 'statistics') ? 'nav-tab-active' : ''; ?>"><?php echo __('Statistics',$this->plugin_name)?></a>
        <a href="#tab3" class="nav-tab <?php echo ($tab == 'leaderboard') ? 'nav-tab-active' : ''; ?>"><?php echo __('Leaderboard',$this->plugin_name)?></a>
        <a href="<?php echo $reviews_page_url; ?>" class="no-js nav-tab <?php echo ($tab == 'reviews') ? 'nav-tab-active' : ''; ?>"><?php echo __('Reviews',$this->plugin_name)?></a>
    </div>
    <div id="tab1" class="ays-quiz-tab-content <?php echo ($tab == 'poststuff') ? 'ays-quiz-tab-content-active' : ''; ?>">
        <div id="poststuff">
            <div id="post-body" class="metabox-holder">
                <div id="post-body-content">
                    <div class="meta-box-sortables ui-sortable">
                        <?php
                            $this->results_obj->views();
                        ?>
                        <form method="post">
                            <?php
                            $this->results_obj->prepare_items();
                            $this->results_obj->search_box('Search', $this->plugin_name);
                            $this->results_obj->display();
                            ?>
                        </form>
                    </div>
                </div>
            </div>
            <br class="clear">
        </div>
    </div>

    <div id="tab2" class="ays-quiz-tab-content <?php echo ($tab == 'statistics') ? 'ays-quiz-tab-content-active' : ''; ?>">
        <br>
        <div class="row" style="margin:0;">
            <div class="col-sm-12">
                <div class="pro_features">
                    <div>
                        <p style="font-size: 20px;">
                            <?php echo __("This feature is available only in ", $this->plugin_name); ?>
                            <a href="https://ays-pro.com/wordpress/quiz-maker/" target="_blank" title="PRO feature"><?php echo __("PRO version!!!", $this->plugin_name); ?></a>
                        </p>
                        <p class="ays-quiz-pro-features-text">
                            <?php echo __("This feature is available only in ", $this->plugin_name); ?>
                            <a href="https://ays-pro.com/wordpress/quiz-maker/" target="_blank" title="PRO feature"><?php echo __("PRO version!!!", $this->plugin_name); ?></a>
                        </p>
                    </div>
                </div>
                <img src="<?php echo AYS_QUIZ_ADMIN_URL.'/images/chart_screen.png'?>" alt="Statistics" style="width:100%;">
            </div>
        </div>
    </div>
    
    <div id="tab3" class="ays-quiz-tab-content <?php echo ($tab == 'leaderboard') ? 'ays-quiz-tab-content-active' : ''; ?>">
        <p class="ays-subtitle"><?php echo __('Leaderboard',$this->plugin_name)?></p>
        <hr>
        <?php 
            global $wpdb;
            $sql = "SELECT quiz_id, user_id, AVG(CAST(`score` AS DECIMAL(10))) AS avg_score
                    FROM {$wpdb->prefix}aysquiz_reports
                    WHERE user_id != 0
                    GROUP BY user_id
                    ORDER BY avg_score DESC
                    LIMIT 10";
            $result = $wpdb->get_results($sql, 'ARRAY_A');

            $c = 1;
            $content = "<div class='ays_lb_container'>
            <ul class='ays_lb_ul' style='width: 100%;'>
                <li class='ays_lb_li'>
                    <div class='ays_lb_pos'>Pos.</div>
                    <div class='ays_lb_user'>".__("Name", $this->plugin_name)."</div>
                    <div class='ays_lb_score'>".__("Score", $this->plugin_name)."</div>
                </li>";

            foreach ($result as $val) {
                $score = round($val['avg_score'], 2);
                $user = get_user_by('id', $val['user_id']);
                if ($user !== false) {
                    $user_name = $user->data->display_name ? $user->data->display_name : $user->user_login;

                    $content .= "<li class='ays_lb_li'>
                                    <div class='ays_lb_pos'>".$c.".</div>
                                    <div class='ays_lb_user'>".$user_name."</div>
                                    <div class='ays_lb_score'>".$score." %</div>
                                </li>";
                    $c++;   
                }
            }
            $content .= "</ul>
            </div>";
            echo $content;
        ?>
    </div>
    
    <div id="ays-results-modal" class="ays-modal">
        <div class="ays-modal-content">
            <div class="ays-quiz-preloader">
                <img class="loader" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/loaders/3-1.svg">
            </div>
            <div class="ays-modal-header">
                <span class="ays-close" id="ays-close-results">&times;</span>
                <h2><?php echo __("Results for", $this->plugin_name); ?></h2>
            </div>
            <div class="ays-modal-body" id="ays-results-body">
            </div>
        </div>
    </div>
    
</div>
