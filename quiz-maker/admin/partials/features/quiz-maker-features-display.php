<?php
/**
 * Created by PhpStorm.
 * User: biggie18
 * Date: 7/30/18
 * Time: 12:08 PM
 */
// $url = "https://ays-pro.com/wordpress/quiz-maker";
// wp_redirect( $url );
// exit;
?>

<div class="wrap">
    <div class="ays-quiz-heading-box">
        <div class="ays-quiz-wordpress-user-manual-box">
            <a href="https://ays-pro.com/wordpress-quiz-maker-user-manual" target="_blank"><?php echo __("View Documentation", $this->plugin_name); ?></a>
        </div>
    </div>
    <h1 class="wp-heading-inline">
        <?php echo __(esc_html(get_admin_page_title()), $this->plugin_name); ?>
    </h1>
    <div class="ays-quiz-features-wrap">
        <div class="comparison">
            <table>
                <thead>
                    <tr>
                        <th class="tl tl2"></th>
                        <th class="product" style="background:#69C7F1; border-top-left-radius: 5px; border-left:0px;">
                            <span style="display: block"><?php echo __('Personal',$this->plugin_name)?></span>
                            <img src="<?php echo AYS_QUIZ_ADMIN_URL . '/images/avatars/personal_avatar.png'; ?>" alt="Free" title="Free" width="100"/>
                        </th>
                        <th class="product" style="background:#69C7F1;">
                            <span style="display: block"><?php echo  __('Business',$this->plugin_name)?></span>
                            <img src="<?php echo AYS_QUIZ_ADMIN_URL . '/images/avatars/business_avatar.png'; ?>" alt="Business" title="Business" width="100"/>
                        </th>
                        <th class="product" style="border-top-right-radius: 5px; border-right:0px; background:#69C7F1;">
                            <span style="display: block"><?php echo __('Developer',$this->plugin_name)?></span>
                            <img src="<?php echo AYS_QUIZ_ADMIN_URL . '/images/avatars/pro_avatar.png'; ?>" alt="Developer" title="Developer" width="100"/>
                        </th>
                    </tr>
                    <tr>
                        <th></th>
                        <th class="price-info">
                            <div class="price-now"><span><?php echo __('Free',$this->plugin_name)?></span></div>
                        </th>
                        <th class="price-info">
                            <div class="price-now"><span style="text-decoration: line-through; color: red;">$49</span>
                            </div>
                            <div class="price-now"><span>$39</span>
                            </div>
                            <!-- <div class="price-now"><span style="color: red; font-size: 12px;">Until January 1</span>
                            </div> -->
                        </th>
                        <th class="price-info">
                            <div class="price-now"><span span style="text-decoration: line-through; color: red;">$129</span>
                            </div>
                            <div class="price-now"><span>$103</span>
                            </div>
                            <!-- <div class="price-now"><span style="color: red; font-size: 12px;">Until January 1</span>
                            </div> -->
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td></td>
                        <td colspan="4"><?php echo __('Support for',$this->plugin_name)?></td>
                    </tr>
                    <tr>
                        <td><?php echo __('Support for',$this->plugin_name)?></td>
                        <td><?php echo __('1 site',$this->plugin_name)?></td>
                        <td><?php echo __('5 site',$this->plugin_name)?></td>
                        <td><?php echo __('Unlimited sites',$this->plugin_name)?></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="3"><?php echo __('Upgrade for',$this->plugin_name)?></td>
                    </tr>
                    <tr class="compare-row">
                        <td><?php echo __('Upgrade for',$this->plugin_name)?></td>
                        <td><?php echo __('1 months',$this->plugin_name)?></td>
                        <td><?php echo __('12 months',$this->plugin_name)?></td>
                        <td><?php echo __('Lifetime',$this->plugin_name)?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="4"><?php echo __('Support for',$this->plugin_name)?></td>
                    </tr>
                    <tr>
                        <td><?php echo __('Support for',$this->plugin_name)?></td>
                        <td><?php echo __('1 months',$this->plugin_name)?></td>
                        <td><?php echo __('12 months',$this->plugin_name)?></td>
                        <td><?php echo __('Lifetime',$this->plugin_name)?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="4"><?php echo __('Usage for',$this->plugin_name)?></td>
                    </tr>
                    <tr>
                        <td><?php echo __('Usage for',$this->plugin_name)?></td>
                        <td><?php echo __('Lifetime',$this->plugin_name)?></td>
                        <td><?php echo __('Lifetime',$this->plugin_name)?></td>
                        <td><?php echo __('Lifetime',$this->plugin_name)?></td>
                    </tr>
                    <tr>
                        <td> </td>
                        <td colspan="3"><?php echo __('Install on unlimited sites',$this->plugin_name)?></td>
                    </tr>
                    <tr>
                        <td><?php echo __('Install on unlimited sites',$this->plugin_name)?></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                        <td><i class="ays_fa ays_fa_check"></i></td>
                    </tr>
                <tr>
                    <td> </td>
                    <td colspan="4"><?php echo __('Reports in dashboard',$this->plugin_name)?></td>
                </tr>
                <tr>
                    <td><?php echo __('Reports in dashboard',$this->plugin_name)?></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="4"><?php echo __('Export and import questions',$this->plugin_name)?></td>
                </tr>
                <tr>
                    <td><?php echo __('Export and import questions',$this->plugin_name)?></td>
                    <td><span>–</span></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="4"><?php echo __('Export results',$this->plugin_name)?></td>
                </tr>
                <tr>
                    <td><?php echo __('Export results',$this->plugin_name)?></td>
                    <td><span>–</span></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="4"><?php echo __('Image answers',$this->plugin_name)?></td>
                </tr>
                <tr>
                    <td><?php echo __('Image answers',$this->plugin_name)?></td>
                    <td><span>–</span></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="4"><?php echo __('Flash cards',$this->plugin_name)?></td>
                </tr>
                <tr>
                    <td><?php echo __('Flash cards',$this->plugin_name)?></td>
                    <td><span>–</span></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="4"><?php echo __('Send mail to user',$this->plugin_name)?></td>
                </tr>
                <tr>
                    <td><?php echo __('Send mail to user',$this->plugin_name)?></td>
                    <td><span>–</span></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="4"><?php echo __('Send mail to admin',$this->plugin_name)?></td>
                </tr>
                <tr>
                    <td><?php echo __('Send mail to admin',$this->plugin_name)?></td>
                    <td><span>–</span></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="4"><?php echo __('Result text according to result',$this->plugin_name)?></td>
                </tr>
                <tr>
                    <td><?php echo __('Result text according to result',$this->plugin_name)?></td>
                    <td><span>–</span></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="4"><?php echo __('Results with charts',$this->plugin_name)?></td>
                </tr>
                <tr>
                    <td><?php echo __('Results with charts',$this->plugin_name)?></td>
                    <td><span>–</span></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="4"><?php echo __('Send certificate',$this->plugin_name)?></td>
                </tr>
                <tr>
                    <td><?php echo __('Send certificate',$this->plugin_name)?></td>
                    <td><span>–</span></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="4"><?php echo __('Custom Form Fields',$this->plugin_name)?></td>
                </tr>
                <tr>
                    <td><?php echo __('Custom Form Fields',$this->plugin_name)?></td>
                    <td><span>–</span></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="4"><?php echo __('Google sheet integration',$this->plugin_name)?></td>
                </tr>
                <tr>
                    <td><?php echo __('Google sheet integration',$this->plugin_name)?></td>
                    <td><span>–</span></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="4"><?php echo __('Mailchimp integration',$this->plugin_name)?></td>
                </tr>
                <tr>
                    <td><?php echo __('Mailchimp integration',$this->plugin_name)?></td>
                    <td><span>–</span></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="4"><?php echo __('Quiz Widget',$this->plugin_name)?></td>
                </tr>
                <tr>
                    <td><?php echo __('Quiz Widget',$this->plugin_name)?></td>
                    <td><span>–</span></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="4"><?php echo __('Campaign Monitor integration',$this->plugin_name)?></td>
                </tr>
                <tr>
                    <td><?php echo __('Campaign Monitor integration',$this->plugin_name)?></td>
                    <td><span>–</span></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="4"><?php echo __('Zapier integration',$this->plugin_name)?></td>
                </tr>
                <tr>
                    <td><?php echo __('Zapier integration',$this->plugin_name)?></td>
                    <td><span>–</span></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="4"><?php echo __('Slack integration',$this->plugin_name)?></td>
                </tr>
                <tr>
                    <td><?php echo __('Slack integration',$this->plugin_name)?></td>
                    <td><span>–</span></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="4"><?php echo __('ActiveCampaign integration',$this->plugin_name)?></td>
                </tr>
                <tr>
                    <td><?php echo __('ActiveCampaign integration',$this->plugin_name)?></td>
                    <td><span>–</span></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="4"><?php echo __('Mad Mimi integration',$this->plugin_name)?></td>
                </tr>
                <tr>
                    <td><?php echo __('Mad Mimi integration',$this->plugin_name)?></td>
                    <td><span>–</span></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="4"><?php echo __('ConvertKit integration',$this->plugin_name)?></td>
                </tr>
                <tr>
                    <td><?php echo __('ConvertKit integration',$this->plugin_name)?></td>
                    <td><span>–</span></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="4"><?php echo __('GetResponse integration',$this->plugin_name)?></td>
                </tr>
                <tr>
                    <td><?php echo __('GetResponse integration',$this->plugin_name)?></td>
                    <td><span>–</span></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="4"><?php echo __('User page shortcode',$this->plugin_name)?></td>
                </tr>
                <tr>
                    <td><?php echo __('User page shortcode',$this->plugin_name)?></td>
                    <td><span>–</span></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="4"><?php echo __('Email configuration',$this->plugin_name)?></td>
                </tr>
                <tr>
                    <td><?php echo __('Email configuration',$this->plugin_name)?></td>
                    <td><span>–</span></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="4"><?php echo __('Question weight/points',$this->plugin_name)?></td>
                </tr>
                <tr>
                    <td><?php echo __('Question weight/points',$this->plugin_name)?></td>
                    <td><span>–</span></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="4"><?php echo __('Answer weight/points',$this->plugin_name)?></td>
                </tr>
                <tr>
                    <td><?php echo __('Answer weight/points',$this->plugin_name)?></td>
                    <td><span>–</span></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                </tr>
                <!-- //////////////// -->
                <tr>
                    <td></td>
                    <td colspan="4"><?php echo __('Leaderboards',$this->plugin_name)?></td>
                </tr>
                <tr>
                    <td><?php echo __('Leaderboards',$this->plugin_name)?></td>
                    <td><span>–</span></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="4"><?php echo __('Make questions required',$this->plugin_name)?></td>
                </tr>
                <tr>
                    <td><?php echo __('Make questions required',$this->plugin_name)?></td>
                    <td><span>–</span></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="4"><?php echo __('Password protected quiz',$this->plugin_name)?></td>
                </tr>
                <tr>
                    <td><?php echo __('Password protected quiz',$this->plugin_name)?></td>
                    <td><span>–</span></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="4"><?php echo __('Navigation bar',$this->plugin_name)?></td>
                </tr>
                <tr>
                    <td><?php echo __('Navigation bar',$this->plugin_name)?></td>
                    <td><span>–</span></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="4"><?php echo __('Personality quiz',$this->plugin_name)?></td>
                </tr>
                <tr>
                    <td><?php echo __('Personality quiz',$this->plugin_name)?></td>
                    <td><span>–</span></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                </tr>
                <!-- //////////////// -->
                <tr>
                    <td></td>
                    <td colspan="4"><?php echo __('Copy content protection',$this->plugin_name)?></td>
                </tr>
                <tr>
                    <td><?php echo __('Copy content protection',$this->plugin_name)?></td>
                    <td><span>–</span></td>
                    <td><span>–</span></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="4"><?php echo __('PayPal integration',$this->plugin_name)?></td>
                </tr>
                <tr>
                    <td><?php echo __('PayPal integration',$this->plugin_name)?></td>
                    <td><span>–</span></td>
                    <td><span>–</span></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="4"><?php echo __('Stripe integration',$this->plugin_name)?></td>
                </tr>
                <tr>
                    <td><?php echo __('Stripe integration',$this->plugin_name)?></td>
                    <td><span>–</span></td>
                    <td><span>–</span></td>
                    <td><i class="ays_fa ays_fa_check"></i></td>
                </tr>
                <tr>
                    <td> </td>
                </tr>
                <tr>
                    <td></td>
                    <td><a href="https://wordpress.org/plugins/quiz-maker/" class="price-buy"><?php echo __('Download',$this->plugin_name)?><span class="hide-mobile"></span></a></td>
                    <td><a href="https://ays-pro.com/wordpress/quiz-maker/" class="price-buy"><?php echo __('Buy now',$this->plugin_name)?><span class="hide-mobile"></span></a></td>
                    <td><a href="https://ays-pro.com/wordpress/quiz-maker/" class="price-buy"><?php echo __('Buy now',$this->plugin_name)?><span class="hide-mobile"></span></a></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

