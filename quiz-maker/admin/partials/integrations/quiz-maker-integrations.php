<?php

    $loader_iamge = "<span class='display_none ays_quiz_loader_box'><img src='". AYS_QUIZ_ADMIN_URL ."/images/loaders/loading.gif'></span>";

    if(isset($_GET['ays_quiz_tab'])){
        $ays_quiz_tab = sanitize_text_field( $_GET['ays_quiz_tab'] );
    }else{
        $ays_quiz_tab = 'tab1';
    }

?>
<div class="wrap" style="position:relative;">
    <div class="container-fluid">
        <form method="post" class="ays-quiz-general-settings-form" id="ays-quiz-general-settings-form">
            <input type="hidden" name="ays_quiz_tab" value="<?php echo $ays_quiz_tab; ?>">
            <div class="ays-quiz-heading-box">
                <div class="ays-quiz-wordpress-user-manual-box">
                    <a href="https://ays-pro.com/wordpress-quiz-maker-user-manual" target="_blank"><?php echo __("View Documentation", $this->plugin_name); ?></a>
                </div>
            </div>
            <h1 class="wp-heading-inline">
            <?php
                echo __('Integrations',$this->plugin_name);
            ?>
            </h1>
            <hr/>
            <div class="form-group ays-settings-wrapper">
                <div class="ays-quiz-tabs-wrapper" style="border: unset;">
                    <div id="tab2" class="ays-quiz-tab-content ays-quiz-tab-content-active">
                        <fieldset>
                            <legend>
                                <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/mailchimp_logo.png" alt="">
                                <h5><?php echo __('MailChimp',$this->plugin_name)?></h5>
                            </legend>
                            <div class="form-group row" style="padding:0;margin:0;">
                                <div class="col-sm-12">
                                    <div class="ays-quiz-heading-box ays-quiz-unset-float ays-quiz-unset-margin">
                                        <div class="ays-quiz-wordpress-user-manual-box ays-quiz-wordpress-text-align">
                                            <a href="https://www.youtube.com/watch?v=joPQrsF0a60" target="_blank">
                                                <?php echo __("How to integrate MailChimp - video", $this->plugin_name); ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12" style="padding:20px;">
                                    <div class="pro_features" style="justify-content:flex-end;">
                                        <div>
                                            <p style="font-size:20px;">
                                                <?php echo __("This feature is available only in ", $this->plugin_name); ?>
                                                <a href="https://ays-pro.com/wordpress/quiz-maker/" target="_blank" title="PRO feature"><?php echo __("PRO version!!!", $this->plugin_name); ?></a>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="form-group row" aria-describedby="aaa">
                                        <div class="col-sm-3">
                                            <label for="ays_mailchimp_username">
                                                <?php echo __('MailChimp Username',$this->plugin_name)?>
                                            </label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" 
                                                class="ays-text-input" 
                                                id="ays_mailchimp_username" 
                                                name="ays_mailchimp_username"
                                            />
                                        </div>
                                    </div>
                                    <hr/>
                                    <div class="form-group row" aria-describedby="aaa">
                                        <div class="col-sm-3">
                                            <label for="ays_mailchimp_api_key">
                                                <?php echo __('MailChimp API Key',$this->plugin_name)?>
                                            </label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" 
                                                class="ays-text-input" 
                                                id="ays_mailchimp_api_key" 
                                                name="ays_mailchimp_api_key"
                                            />
                                        </div>
                                    </div>
                                    <blockquote>
                                        <?php echo sprintf( __( "You can get your API key from your ", $this->plugin_name ) . "<a href='%s' target='_blank'> %s.</a>", "https://us20.admin.mailchimp.com/account/api/", "Account Extras menu" ); ?>
                                    </blockquote>
                                </div>
                            </div>
                        </fieldset> <!-- MailChimp -->
                        <hr/>                    
                        <fieldset>
                            <legend>
                                <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/paypal_logo.png" alt="">
                                <h5><?php echo __('PayPal',$this->plugin_name)?></h5>
                            </legend>
                            <div class="form-group row" style="padding:0;margin:0;">
                                <div class="col-sm-12">
                                    <div class="ays-quiz-heading-box ays-quiz-unset-float ays-quiz-unset-margin">
                                        <div class="ays-quiz-wordpress-user-manual-box ays-quiz-wordpress-text-align">
                                            <a href="https://www.youtube.com/watch?v=IwT-2d9OE1g" target="_blank">
                                                <?php echo __("How to integrate PayPal - video", $this->plugin_name); ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12" style="padding:20px;">
                                    <div class="pro_features" style="justify-content:flex-end;">
                                        <div>
                                            <p style="font-size:20px;">
                                                <?php echo __("This feature is available only in ", $this->plugin_name); ?>
                                                <a href="https://ays-pro.com/wordpress/quiz-maker/" target="_blank" title="DEVELOPER feature"><?php echo __("DEVELOPER version!!!", $this->plugin_name); ?></a>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="form-group row" aria-describedby="aaa">
                                        <div class="col-sm-3">
                                            <label for="ays_paypal_client_id">
                                                <?php echo __('Paypal Client ID',$this->plugin_name)?>
                                            </label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" 
                                                class="ays-text-input" 
                                                id="ays_paypal_client_id" 
                                                name="ays_paypal_client_id"
                                            />
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group row" aria-describedby="aaa">
                                        <div class="col-sm-3">
                                            <label>
                                                <?php echo __('Payment terms',$this->plugin_name)?>
                                            </label>
                                        </div>
                                        <div class="col-sm-9">
                                            <label class="ays_quiz_loader" style="display:inline-block;">
                                                <input type="radio" />
                                                <span><?php echo __('Lifetime payment',$this->plugin_name); ?></span>
                                            </label>
                                            <label class="ays_quiz_loader" style="display:inline-block;">
                                                <input type="radio" />
                                                <span><?php echo __('Onetime payment',$this->plugin_name); ?></span>
                                            </label>
                                            <label class="ays_quiz_loader" style="display:inline-block;">
                                                <input type="radio" />
                                                <span><?php echo __('Subscribtion',$this->plugin_name); ?></span>
                                            </label>
                                            <label class="ays_toggle_target" style="display:inline-block;">
                                                <input type="checkbox" value="on"/>
                                                <span><?php echo __('Turn on extra security check', $this->plugin_name); ?></span>
                                                <a class="ays_help" data-toggle="tooltip" title="<?php echo __('When the user pays for the quiz and starts passing it but leaves without finishing, he/she has to pay again every time he wants to pass it.',$this->plugin_name)?>">
                                                    <i class="ays_fa ays_fa_info_circle"></i>
                                                </a>
                                            </label>
                                            <hr>
                                            <div class="ays_toggle_target_2" style="display:block;">
                                                <div class="form-group row">
                                                    <div class="col-sm-4">
                                                        <label class="form-check-label" for="ays-subscribtion-duration"> <?php echo __('Subscribtion duration', $this->plugin_name); ?> </label>
                                                    </div>
                                                    <div class="col-sm-8 d-flex">
                                                        <input type="text" class="ays-text-input ays-text-input-short" placeholder="30">
                                                        <select name="ays-subscribtion-duration-by" class="ays-text-input-short ml-3">
                                                            <option><?php echo __( "Day", $this->plugin_name ); ?></option>
                                                            <option><?php echo __( "Month", $this->plugin_name ); ?></option>
                                                            <option><?php echo __( "Year", $this->plugin_name ); ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <blockquote>
                                        <?php echo sprintf( __( "You can get your Client ID from", $this->plugin_name ) . "<a href='%s' target='_blank'> %s.</a>", "https://developer.paypal.com/developer/applications", "Developer Paypal" ); ?>
                                    </blockquote>
                                </div>
                            </div>
                        </fieldset> <!-- PayPal -->
                        <hr/>
                        <fieldset>
                            <legend>
                                <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/stripe_logo.png" alt="">
                                <h5><?php echo __('Stripe',$this->plugin_name)?></h5>
                            </legend>
                            <div class="form-group row" style="padding:0;margin:0;">
                                <div class="col-sm-12" style="padding:20px;">
                                    <div class="pro_features" style="justify-content:flex-end;">
                                        <div>
                                            <p style="font-size:20px;">
                                                <?php echo __("This feature is available only in ", $this->plugin_name); ?>
                                                <a href="https://ays-pro.com/wordpress/quiz-maker/" target="_blank" title="DEVELOPER feature"><?php echo __("DEVELOPER version!!!", $this->plugin_name); ?></a>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <div class="form-group row">
                                                <div class="col-sm-3">
                                                    <label for="ays_stripe_api_key">
                                                        Stripe <?php echo __('Publishable Key', $this->plugin_name); ?>
                                                    </label>
                                                </div>
                                                <div class="col-sm-9">
                                                    <input type="text" class="ays-text-input" value="" >
                                                </div>
                                            </div>
                                            <hr/>
                                            <div class="form-group row">
                                                <div class="col-sm-3">
                                                    <label for="ays_stripe_secret_key">
                                                        Stripe <?php echo __('Secret Key', $this->plugin_name); ?>
                                                    </label>
                                                </div>
                                                <div class="col-sm-9">
                                                    <input type="text" class="ays-text-input" value="" >
                                                </div>
                                            </div>
                                            <hr/>
                                            <div class="form-group row">
                                                <div class="col-sm-3">
                                                    <label>
                                                        <?php echo __('Payment terms',$this->plugin_name)?>
                                                    </label>
                                                </div>
                                                <div class="col-sm-9">
                                                    <label class="ays_quiz_loader" style="display:inline-block;">
                                                        <input type="radio" />
                                                        <span><?php echo __('Lifetime payment',$this->plugin_name);?></span>
                                                    </label>
                                                    <label class="ays_quiz_loader" style="display:inline-block;">
                                                        <input type="radio" />
                                                        <span><?php echo __('Onetime payment',$this->plugin_name);?></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <blockquote>
                                                <?php echo __("You can get your Publishable and Secret keys on API Keys page on your Stripe dashboard.", $this->plugin_name); ?>
                                            </blockquote>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset> <!-- Stripe -->
                        <hr/>
                        <fieldset>
                            <legend>
                                <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/campaignmonitor_logo.png" alt="">
                                <h5><?php echo __('Campaign Monitor',$this->plugin_name)?></h5>
                            </legend>
                            <div class="form-group row" style="padding:0;margin:0;">
                                <div class="col-sm-12" style="padding:20px;">
                                    <div class="pro_features" style="justify-content:flex-end;">
                                        <div>
                                            <p style="font-size:20px;">
                                                <?php echo __("This feature is available only in ", $this->plugin_name); ?>
                                                <a href="https://ays-pro.com/wordpress/quiz-maker/" target="_blank" title="PRO feature"><?php echo __("PRO version!!!", $this->plugin_name); ?></a>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="form-group row" aria-describedby="aaa">
                                        <div class="col-sm-3">
                                            <label for="ays_monitor_client">
                                                Campaign Monitor <?= __('Client ID', $this->plugin_name) ?>
                                            </label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" class="ays-text-input" id="ays_monitor_client" >
                                        </div>
                                    </div>
                                    <hr/>
                                    <div class="form-group row" aria-describedby="aaa">
                                        <div class="col-sm-3">
                                            <label for="ays_monitor_api_key">
                                                Campaign Monitor <?= __('API Key', $this->plugin_name) ?>
                                            </label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" class="ays-text-input" id="ays_monitor_api_key">
                                        </div>
                                    </div>
                                    <blockquote>
                                        <?= __("You can get your API key and Client ID from your Account Settings page", $this->plugin_name); ?>
                                    </blockquote>
                                </div>
                            </div>
                        </fieldset> <!-- Campaign Monitor -->
                        <hr/>
                        <fieldset>
                            <legend>
                                <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/zapier_logo.png" alt="">
                                <h5><?php echo __('Zapier',$this->plugin_name)?></h5>
                            </legend>
                            <div class="form-group row" style="padding:0;margin:0;">
                                <div class="col-sm-12" style="padding:20px;">
                                    <div class="pro_features" style="justify-content:flex-end;">
                                        <div>
                                            <p style="font-size:20px;">
                                                <?php echo __("This feature is available only in ", $this->plugin_name); ?>
                                                <a href="https://ays-pro.com/wordpress/quiz-maker/" target="_blank" title="PRO feature"><?php echo __("PRO version!!!", $this->plugin_name); ?></a>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="form-group row" aria-describedby="aaa">
                                        <div class="col-sm-3">
                                            <label for="ays_zapier_hook">
                                                <?= __('Zapier Webhook URL', $this->plugin_name) ?>
                                            </label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" class="ays-text-input" id="ays_zapier_hook">
                                        </div>
                                    </div>
                                    <blockquote>
                                        <?php echo sprintf(__("If you do not have any ZAP created, go " . "<a href='%s' target='_blank'>%s</a>" . ". Remember to choose Webhooks by Zapier as Trigger App.", $this->plugin_name), "https://zapier.com/app/editor/", "here"); ?>
                                    </blockquote>
                                    <blockquote>
                                        <?= __("We will send you all data from quiz information form with \"AysQuiz\" key by POST method", $this->plugin_name); ?>
                                    </blockquote>
                                </div>
                            </div>
                        </fieldset> <!-- Zapier -->
                        <hr/>
                        <fieldset>
                            <legend>
                                <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/activecampaign_logo.png" alt="">
                                <h5><?php echo __('ActiveCampaign',$this->plugin_name)?></h5>
                            </legend>
                            <div class="form-group row" style="padding:0;margin:0;">
                                <div class="col-sm-12" style="padding:20px;">
                                    <div class="pro_features" style="justify-content:flex-end;">
                                        <div>
                                            <p style="font-size:20px;">
                                                <?php echo __("This feature is available only in ", $this->plugin_name); ?>
                                                <a href="https://ays-pro.com/wordpress/quiz-maker/" target="_blank" title="PRO feature"><?php echo __("PRO version!!!", $this->plugin_name); ?></a>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <label for="ays_active_camp_url">
                                                <?= __('API Access URL', $this->plugin_name) ?>
                                            </label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" class="ays-text-input" id="ays_active_camp_url" >
                                        </div>
                                    </div>
                                    <hr/>
                                    <div class="form-group row" aria-describedby="aaa">
                                        <div class="col-sm-3">
                                            <label for="ays_active_camp_api_key">
                                                <?= __('API Access Key', $this->plugin_name) ?>
                                            </label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" class="ays-text-input" id="ays_active_camp_api_key">
                                        </div>
                                    </div>
                                    <blockquote>
                                        <?= __("Your API URL and Key can be found in your account on the My Settings page under the \"Developer\" tab", $this->plugin_name); ?>
                                    </blockquote>
                                </div>
                            </div>
                        </fieldset> <!-- ActiveCampaign -->
                        <hr/>
                        <fieldset>
                            <legend>
                                <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/slack_logo.png" alt="">
                                <h5><?php echo __('Slack',$this->plugin_name)?></h5>
                            </legend>
                            <div class="form-group row" style="padding:0;margin:0;">
                                <div class="col-sm-12" style="padding:20px;">
                                    <div class="pro_features" style="justify-content:flex-end;">
                                        <div>
                                            <p style="font-size:20px;">
                                                <?php echo __("This feature is available only in ", $this->plugin_name); ?>
                                                <a href="https://ays-pro.com/wordpress/quiz-maker/" target="_blank" title="PRO feature"><?php echo __("PRO version!!!", $this->plugin_name); ?></a>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <label for="ays_slack_client">
                                                <?= __('App Client ID', $this->plugin_name) ?>
                                            </label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" class="ays-text-input" id="ays_slack_client">
                                        </div>
                                    </div>
                                    <hr/>
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <label for="ays_slack_oauth">
                                                <?= __('Slack Authorization', $this->plugin_name) ?>
                                            </label>
                                        </div>
                                        <div class="col-sm-9">
                                            <button type="button" id="slackOAuth2" class="btn btn-outline-secondary disabled">
                                                <?= __("Authorize", $this->plugin_name) ?>
                                            </button>
                                        </div>
                                    </div>
                                    <hr/>
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <label for="ays_slack_secret">
                                                <?= __('App Client Secret', $this->plugin_name) ?>
                                            </label>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="text" class="ays-text-input" id="ays_slack_secret" >
                                        </div>
                                    </div>
                                    <hr/>
                                    <div class="form-group row">
                                        <div class="col-sm-3">
                                            <label for="ays_slack_oauth">
                                                <?= __('App Access Token', $this->plugin_name) ?>
                                            </label>
                                        </div>
                                        <div class="col-sm-9">
                                            <button type="button" class="btn btn-outline-secondary disabled">
                                                <?= __("Need Authorization", $this->plugin_name) ?>
                                            </button>
                                            <input type="hidden" id="ays_slack_token">
                                        </div>
                                    </div>
                                    <blockquote>
                                        <?= __("You can get your App Client ID and Client Secret from your App's the Basic Information page", $this->plugin_name); ?>
                                    </blockquote>
                                </div>
                            </div>
                        </fieldset> <!-- Slack -->
                        <hr/>
                        <!-- _________________________GOOGLE SHEETS START____________________ -->
                        <fieldset>
                            <legend>
                                <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/sheets_logo.png" alt="">
                                <h5><?php echo __('Google Sheets',$this->plugin_name)?></h5>
                            </legend>
                            <div class="form-group row" style="padding:0;margin:0;">
                                <div class="col-sm-12" style="padding:20px;">
                                    <div class="pro_features" style="justify-content:flex-end;">
                                        <div>
                                            <p style="font-size:20px;">
                                                <?php echo __("This feature is available only in ", $this->plugin_name); ?>
                                                <a href="https://ays-pro.com/wordpress/quiz-maker/" target="_blank" title="PRO feature"><?php echo __("PRO version!!!", $this->plugin_name); ?></a>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <div class="form-group row" aria-describedby="aaa">
                                                <div class="col-sm-3">
                                                    <button type="button" class="btn btn-info disabled" data-original-title="Google Integration Setup Instructions" disabled><?php echo __('Instructions', $this->plugin_name); ?></button>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-3">
                                                    <label for="ays_google_client">
                                                        <?= __('Google Client ID', $this->plugin_name) ?>
                                                    </label>
                                                </div>
                                                <div class="col-sm-9">
                                                    <input type="text" class="ays-text-input" id="ays_google_client" value="" >
                                                </div>
                                            </div>
                                            <hr/>
                                            <div class="form-group row">
                                                <div class="col-sm-3">
                                                    <label for="ays_google_secret">
                                                        <?= __('Google Client Secret', $this->plugin_name) ?>
                                                    </label>
                                                </div>
                                                <div class="col-sm-9">
                                                    <input type="text" class="ays-text-input" id="ays_google_secret" value="">
                                                    <input type="hidden" id="ays_google_redirect" value="">
                                                </div>
                                            </div>
                                            <hr/>
                                            <div class="form-group row">
                                                <div class="col-sm-3"></div>
                                                <div class="col-sm-9">
                                                    <button type="button" class="btn btn-outline-info disabled" disabled>
                                                        <?= __("Connect", $this->plugin_name) ?>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset> <!-- Google Sheets -->
                        <!-- __________________________GOOGLE SHEETS END_____________________ -->
                        <hr/>
                        <fieldset>
                            <legend>
                                <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/mad-mimi-logo.png" alt="">
                                <h5><?php echo __('Mad Mimi',$this->plugin_name)?></h5>
                            </legend>
                            <div class="form-group row" style="padding:0;margin:0;">
                                <div class="col-sm-12" style="padding:20px;">
                                    <div class="pro_features" style="justify-content:flex-end;">
                                        <div>
                                            <p style="font-size:20px;">
                                                <?php echo __("This feature is available only in ", $this->plugin_name); ?>
                                                <a href="https://ays-pro.com/wordpress/quiz-maker/" target="_blank" title="PRO feature"><?php echo __("PRO version!!!", $this->plugin_name); ?></a>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <div class="form-group row">
                                                <div class="col-sm-3">
                                                    <label><?php echo __('Username', $this->plugin_name); ?></label>
                                                </div>
                                                <div class="col-sm-9">
                                                    <input type="text" class="ays-text-input" value="" >
                                                </div>
                                            </div>
                                            <hr/>
                                            <div class="form-group row">
                                                <div class="col-sm-3">
                                                    <label><?php echo __('API Key', $this->plugin_name); ?></label>
                                                </div>
                                                <div class="col-sm-9">
                                                    <input type="text" class="ays-text-input" value="" >
                                                </div>
                                            </div>
                                            <blockquote>
                                                <?php echo sprintf( __( "You can get your API key from your ", $this->plugin_name ) . "<a href='%s' target='_blank'> %s.</a>", "https://madmimi.com/user/edit?account_info_tabs=account_info_personal", "Account" ); ?>
                                            </blockquote>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset> <!-- Mad Mimi -->
                        <hr/>
                        <fieldset>
                            <legend>
                                <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/convertkit_logo.png" alt="">
                                <h5><?php echo __('ConvertKit',$this->plugin_name)?></h5>
                            </legend>
                            <div class="form-group row" style="padding:0;margin:0;">
                                <div class="col-sm-12" style="padding:20px;">
                                    <div class="pro_features" style="justify-content:flex-end;">
                                        <div>
                                            <p style="font-size:20px;">
                                                <?php echo __("This feature is available only in ", $this->plugin_name); ?>
                                                <a href="https://ays-pro.com/wordpress/quiz-maker/" target="_blank" title="PRO feature"><?php echo __("PRO version!!!", $this->plugin_name); ?></a>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <div class="form-group row">
                                                <div class="col-sm-3">
                                                    <label><?php echo __('API Key', $this->plugin_name); ?></label>
                                                </div>
                                                <div class="col-sm-9">
                                                    <input type="text" class="ays-text-input" value="">
                                                </div>
                                            </div>
                                            <blockquote>
                                                <?php echo sprintf( __( "You can get your API key from your ", $this->plugin_name ) . "<a href='%s' target='_blank'> %s.</a>", "https://app.convertkit.com/account/edit", "Account" ); ?>
                                            </blockquote>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset> <!-- ConvertKit -->
                        <hr/>
                        <fieldset>
                            <legend>
                                <img class="ays_integration_logo" src="<?php echo AYS_QUIZ_ADMIN_URL; ?>/images/integrations/get_response.png" alt="">
                                <h5><?php echo __('GetResponse',$this->plugin_name)?></h5>
                            </legend>
                            <div class="form-group row" style="padding:0;margin:0;">
                                <div class="col-sm-12" style="padding:20px;">
                                    <div class="pro_features" style="justify-content:flex-end;">
                                        <div>
                                            <p style="font-size:20px;">
                                                <?php echo __("This feature is available only in ", $this->plugin_name); ?>
                                                <a href="https://ays-pro.com/wordpress/quiz-maker/" target="_blank" title="PRO feature"><?php echo __("PRO version!!!", $this->plugin_name); ?></a>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-12">
                                            <div class="form-group row">
                                                <div class="col-sm-3">
                                                    <label for="ays_quiz_getresponse_api_key"><?php echo __('GetResponse API Key', $this->plugin_name); ?></label>
                                                </div>
                                                <div class="col-sm-9">
                                                    <input type="text" class="ays-text-input" id="ays_quiz_getresponse_api_key" name="ays_quiz_getresponse_api_key" value="" >
                                                </div>
                                            </div>
                                            <blockquote>
                                            <?php echo sprintf( __( "You can get your API key from your ", $this->plugin_name ) . "<a href='%s' target='_blank'> %s.</a>", "https://app.getresponse.com/api", "account" ); ?>
                                            </blockquote>
                                            <blockquote>
                                            <?php echo __( "For security reasons, unused API keys expire after 90 days. When that happens, you’ll need to generate a new key.", $this->plugin_name ); ?>
                                            </blockquote>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset> <!-- GetResponse -->
                    </div>
                </div>
            </div>
            <hr/>
            <div style="padding:15px 0px;">
            <?php
                wp_nonce_field('settings_action', 'settings_action');
                $other_attributes = array();
                submit_button(__('Save changes', $this->plugin_name), 'primary ays-quiz-loader-banner', 'ays_submit', true, $other_attributes);
                echo $loader_iamge;
            ?>
            </div>
        </form>
    </div>
</div>
