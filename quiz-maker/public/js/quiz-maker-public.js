(function ($) {
    'use strict';
    $(document).ready(function () {
        $.fn.goTo = function(myOptions) {
            var QuizAnimationTop = (myOptions.quiz_animation_top && myOptions.quiz_animation_top != 0) ? parseInt(myOptions.quiz_animation_top) : 100;
            myOptions.quiz_enable_animation_top = myOptions.quiz_enable_animation_top ? myOptions.quiz_enable_animation_top : 'on';
            var EnableQuizAnimationTop = ( myOptions.quiz_enable_animation_top && myOptions.quiz_enable_animation_top == 'on' ) ? true : false;
            if( EnableQuizAnimationTop ){
                $('html, body').animate({
                    scrollTop: $(this).offset().top - QuizAnimationTop + 'px'
                }, 'slow');
            }
            return this; // for chaining...
        }
        if (!String.prototype.trim) {
            (function() {
                String.prototype.trim = function() {
                    return this.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, '');
                };
            })();
        }
        $(document).find('.for_quiz_rate_avg.ui.rating').rating('disable');
        
        var ays_quiz_container, ays_quiz_container_id; //flag to prevent quick multi-click glitches
        var myOptions, myQuizOptions, explanationTimeout, aysTimerInterval;
        var emailValivatePattern = /^[a-zA-Z0-9\._-]+@[a-zA-Z0-9\._-]+\.\w{2,}$/;

        if( typeof window.aysSeeResultConfirmBox == 'undefined' ){
            window.aysSeeResultConfirmBox = [];
        }

        if( typeof window.aysEarlyFinishConfirmBox == 'undefined' ){
            window.aysEarlyFinishConfirmBox = [];
        }

        window.aysTimerIntervalFlag = null;
        
        function time_limit(e) {
            var quizId = $(e.target).parents('.ays-quiz-container').find('input[name="ays_quiz_id"]').val();
            myOptions = JSON.parse(atob(window.aysQuizOptions[quizId]));

            if(checkQuizPassword(e, myOptions, false) === false){
                return false;
            }

            if(typeof myOptions.answers_rw_texts == 'undefined'){
                myOptions.answers_rw_texts = 'on_passing';
            }
            var quizOptionsName = 'quizOptions_'+quizId;
            myQuizOptions = [];
            if(typeof window[quizOptionsName] !== 'undefined'){
                for(var i in window[quizOptionsName]){
                    if(window[quizOptionsName].hasOwnProperty(i)){
                         myQuizOptions[i] = (JSON.parse(window.atob(window[quizOptionsName][i])));
                    }
                }
            }

            if(typeof window.aysSeeResultConfirmBox !== 'undefined'){
                window.aysSeeResultConfirmBox[ quizId ] = false;
            }

            if(typeof window.aysEarlyFinishConfirmBox !== 'undefined'){
                window.aysEarlyFinishConfirmBox[ quizId ] = false;
            }

            var container = $(e.target).parents('.ays-quiz-container');
            if ($(this).parents('.step').next().find('.information_form').length === 0 ){
                var quizMusic = container.find('.ays_quiz_music');
                if(quizMusic.length !== 0){                
                    var soundEls = $(document).find('.ays_music_sound');
                    container.find('.ays_music_sound').removeClass('ays_display_none');                
                    if(!isPlaying(quizMusic.get(0))){
                        container.find('.ays_quiz_music')[0].play();
                        audioVolumeIn(container.find('.ays_quiz_music')[0]);
                    }
                }
                container.find('.ays-live-bar-wrap').css({'display': 'block'});
                container.find('.ays-live-bar-percent').css({'display': 'inline-block'});
                container.find('input.ays-start-date').val(GetFullDateTime());
            }
            if ($(this).parents('.step').next().find('.information_form').length === 0 && myOptions.enable_timer == 'on') {
                container.find('div.ays-quiz-timer').hide(800);
                var timer = parseInt(container.find('div.ays-quiz-timer').attr('data-timer'));
                var pageTitle = $(document).find('title');
                var pageTitleText = $(document).find('title').html();
                var timeForShow = "";

                // Display all questions on one page
                myOptions.quiz_timer_red_warning = ( myOptions.quiz_timer_red_warning ) ? myOptions.quiz_timer_red_warning : 'off';
                var quiz_timer_red_warning = (myOptions.quiz_timer_red_warning && myOptions.quiz_timer_red_warning == "on") ? true : false;

                if (!isNaN(timer) && myOptions.timer !== undefined) {
                    if (myOptions.timer === timer && timer !== 0) {
                        timer += 2;
                        if (timer !== undefined) {
                            var countDownDate = new Date().getTime() + (timer * 1000);
                            var timerFlag = false;

                            // Message before timer
                            var quiz_message_before_timer = (myOptions.quiz_message_before_timer && myOptions.quiz_message_before_timer != "") ? ( myOptions.quiz_message_before_timer ) : '';

                            if ( quiz_message_before_timer != '' ) {
                                quiz_message_before_timer = quiz_message_before_timer.replace(/(["'])/g, "\\$1") + " ";

                                $(document).find('html > head').append('<style> #ays-quiz-container-'+ quizId +' div.ays-quiz-timer.ays-quiz-message-before-timer:before{content: "'+ quiz_message_before_timer +'"; }</style>');
                            }

                            aysTimerInterval = setInterval(function () {
                                var now = new Date().getTime();
                                var distance = countDownDate - Math.ceil(now/1000)*1000;
                                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                                var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                                var sec = seconds;
                                var min = minutes;
                                var hour = hours;
                                if(hours <= 0){
                                    hours = null;
                                }else if (hours < 10) {
                                    hours = '0' + hours;
                                }
                                if (minutes < 10) {
                                    minutes = '0' + minutes;
                                }
                                if (seconds < 10) {
                                    seconds = '0' + seconds;
                                }
                                timeForShow =  ((hours==null)? "" : (hours + ":")) + minutes + ":" + seconds;
                                if(distance <=1000){
                                    timeForShow =  ((hours==null) ? "" : "00:") + "00:00";
                                    container.find('div.ays-quiz-timer').html(timeForShow);
                                    if(myOptions.quiz_timer_in_title == 'on'){
                                        pageTitle.html( timeForShow + " - " + pageTitleText );
                                    }
                                }else{
                                    container.find('div.ays-quiz-timer').html(timeForShow);
                                    if(myOptions.quiz_timer_in_title == 'on'){
                                        pageTitle.html( timeForShow + " - " + pageTitleText );
                                    }
                                }

                                if ( quiz_timer_red_warning ) {
                                    var distanceSec = Math.floor(distance / 1000);
                                    var timerPercentage = Math.floor(( timer - distanceSec ) * 100);
                                    var percentage = Math.floor( timerPercentage / timer );

                                    if ( percentage >= 90 && ! timerFlag ) {
                                        var timerContainer = container.find('section.ays_quiz_timer_container');
                                        timerFlag = true;

                                        if ( ! timerContainer.hasClass( 'ays_quiz_timer_red_warning' ) ) {
                                            timerContainer.addClass( 'ays_quiz_timer_red_warning' );
                                        }
                                    }
                                }
                                
                                container.find('.ays_quiz_timer_container').show();
                                container.find('div.ays-quiz-timer').show(500);
                                if(container.find('.ays-quiz-timer').length === 0){
                                    clearInterval(aysTimerInterval);
                                    if(myOptions.quiz_timer_in_title == 'on'){
                                        pageTitle.html( pageTitleText );
                                    }
                                    container.find('.ays_quiz_timer_container').slideUp(500);
                                }
                                
                                if(container.find('.ays_finish.action-button').hasClass("ays_timer_end") || 
                                    container.find('.ays_next.action-button').hasClass("ays_timer_end")){
                                    clearInterval(aysTimerInterval);
                                    if(myOptions.quiz_timer_in_title == 'on'){
                                        pageTitle.html( pageTitleText );
                                    }
                                    container.find('.ays_quiz_timer_container').slideUp(500);
                                }

                                if(hour == 0 && min == 0 && sec < 1){
                                    container.find('.ays_buttons_div > *:not(input.ays_finish)').off('click');
                                }

                                if (distance <= 1) {
                                    clearInterval(aysTimerInterval);                                    
                                    if(! container.find('div.ays-quiz-after-timer').hasClass('empty_after_timer_text')){
                                        container.find('.ays_quiz_timer_container').css({
                                            'position': 'static',
                                            'height': '100%',
                                        });
                                        container.find('div.ays-quiz-timer').slideUp();
                                        container.find('div.ays-quiz-after-timer').slideDown(500);
                                    }else{
                                        container.find('.ays_quiz_timer_container').slideUp(500);                                        
                                    }
                                    if(myOptions.quiz_timer_in_title == 'on'){
                                        pageTitle.html( pageTitleText );
                                    }
                                    var totalSteps = container.find('div.step').length;
                                    var currentStep = container.find('div.step.active-step');
                                    var thankYouStep = container.find('div.step.ays_thank_you_fs');
                                    var infoFormLast = thankYouStep.prev().find('div.information_form');
                                    if(infoFormLast.length == 0){
                                        if (currentStep.hasClass('ays_thank_you_fs') === false) {
                                            var steps = totalSteps - 3;
                                            container.find('div.step').each(function (index) {
                                                if ($(this).hasClass('ays_thank_you_fs')) {
                                                    $(this).addClass('active-step')
                                                }else{
                                                    $(this).css('display', 'none');                                                
                                                }
                                            });
                                            window.aysTimerIntervalFlag = true;

                                            var ays_finish_button = container.find('input.ays_finish');
                                            if(ays_finish_button.prop('disabled')){
                                                ays_finish_button.prop('disabled', false);
                                            }
                                            ays_finish_button.addClass('ays-quiz-after-timer-end');
                                            ays_finish_button.trigger('click');
                                        }
                                    }else{
                                        currentStep.parents('.ays-quiz-container').find('.ays-quiz-timer').parent().slideUp(500);
                                        container.find('.ays-live-bar-wrap').removeClass('rubberBand').addClass('bounceOut');
                                        container.find('.ays-live-bar-percent').removeClass('rubberBand').addClass('bounceOut');
                                        setTimeout(function () {
                                            container.find('.ays-live-bar-wrap').css('display','none');
                                            container.find('.ays-live-bar-percent').css('display','none');
                                        },300);
                                        
                                        container.find('div.step').each(function (index) {
                                            $(this).css('display', 'none');
                                            $(this).removeClass('active-step')
                                        });
                                        aysAnimateStep(ays_quiz_container.data('questEffect'), currentStep, infoFormLast.parent());
                                       // infoFormLast.parent().css('display', 'flex');
                                        infoFormLast.parent().addClass('active-step'); 
                                    }
                                }
                            }, 1000);
                        }
                    } else {
                        alert('Wanna cheat??');
                        window.location.reload();
                    }
                }

            }else{
                $(this).parents('.step').next().find('.information_form').find('.ays_next.action-button').on('click', function () {
                    if($(this).parents('.step').find('.information_form').find('.ays_next.action-button').hasClass('ays_start_allow')){
                        time_limit(e);
                    }
                });
            }
        }
        
        $(document).find('.ays_next.start_button').on('click', time_limit);
        
        $(document).find('.ays_next.start_button').on('click', function(e){

            if(checkQuizPassword(e, myOptions, false) === false){
                return false;
            }

            ays_quiz_container_id = $(this).parents(".ays-quiz-container").attr("id");
            ays_quiz_container = $('#'+ays_quiz_container_id);
            aysResetQuiz( ays_quiz_container );

            $(this).parents('div.step').removeClass('active-step');
            $(this).parents('div.step').next().addClass('active-step');

            if (typeof $(this).attr("data-enable-leave-page") !== 'undefined') {
                $(this).attr("data-enable-leave-page",true);
            }
            ays_quiz_container.css('padding-bottom', '0px');
            var ancnoxneriQanak = $(this).parents('.ays-questions-container').find('.ays_quizn_ancnoxneri_qanak');
            var aysQuizReteAvg = $(this).parents('.ays-questions-container').find('.ays_quiz_rete_avg');

            if( ays_quiz_container.find('.enable_min_selection_number').length > 0 ){
                ays_quiz_container.find('.enable_min_selection_number').each(function(){
                    var thisStep = $(this).parents('.step');
                    thisStep.find('input.ays_next').attr('disabled', 'disabled');
                    thisStep.find('i.ays_next_arrow').attr('disabled', 'disabled');

                    thisStep.find('input.ays_early_finish').attr('disabled', 'disabled');
                    thisStep.find('i.ays_early_finish').attr('disabled', 'disabled');
                });
            }
            
            setTimeout(function(){
                ays_quiz_container.css('border-radius', myOptions.quiz_border_radius + 'px');
                ays_quiz_container.find('.step').css('border-radius', myOptions.quiz_border_radius + 'px');
            }, 400);

            ays_quiz_container.find('iframe').removeAttr('style').css({
                width: '100%'
            });
            aysAnimateStep(ays_quiz_container.data('questEffect'), aysQuizReteAvg);
            aysAnimateStep(ays_quiz_container.data('questEffect'), ancnoxneriQanak);
            
            if ($(this).parents('.step').next().find('.information_form').length === 0 ){
                var questions_count = $(this).parents('form').find('div[data-question-id]').length;
                var curent_number = $(this).parents('form').find('div[data-question-id]').index($(this).parents('div[data-question-id]')) + 1;
                var next_sibilings_count = $(this).parents('form').find('.ays_question_count_per_page').val();
                if(parseInt(next_sibilings_count) > 0 &&
                   ($(this).parents('.step').attr('data-question-id') || 
                    $(this).parents('.step').next().attr('data-question-id'))){
                    var final_width = ((parseInt(next_sibilings_count)) / questions_count * 100) + "%";
                    if($(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').hasClass('ays-live-bar-count')){
                        $(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').text(parseInt(parseInt(next_sibilings_count)));
                    }else{                
                        $(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').text(parseInt(final_width));
                    }
                    $(this).parents('.ays-quiz-container').find('.ays-live-bar-fill').animate({'width': final_width}, 1000);
                }else{
                    var final_width = ((curent_number+1) / questions_count * 100) + "%";
                    if($(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').hasClass('ays-live-bar-count')){
                        $(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').text(parseInt(curent_number+1));
                    }else{                
                        $(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').text(parseInt(final_width));
                    }
                    $(this).parents('.ays-quiz-container').find('.ays-live-bar-fill').animate({'width': final_width}, 1000);
                }
            }

            if ( ays_quiz_container.hasClass('ays_quiz_hide_bg_on_start_page') ) {
                ays_quiz_container.removeClass('ays_quiz_hide_bg_on_start_page');
            }
        });

        $(document).find('.ays-quiz-container input').on('focus', function () {
            $(window).on('keydown', function (event) {
                if (event.keyCode === 13) {
                    return false;
                }
            });
        });

        $(document).find('.ays-quiz-container input').on('blur', function () {
            $(window).off('keydown');
        });
        
        $.each($(document).find('.ays_block_content'), function () {
            if ($(document).find('.ays_block_content').length != 0) {
                var ays_block_element = $(this).parents().eq(2);
                ays_block_element.find('input.ays-start-date').val(GetFullDateTime());
                ays_block_element.find('div.ays-quiz-timer').slideUp(500);
                var timer = parseInt(ays_block_element.find('div.ays-quiz-timer').attr('data-timer'));
                var timerInTitle = ays_block_element.find('div.ays-quiz-timer').data('showInTitle');
                var tabTitle = document.title;
                setTimeout(function(){
                if (timer !== NaN) {
                    timer += 2;
                    if (timer !== undefined) {
                        var countDownDate = new Date().getTime() + (timer * 1000);
                        var x = setInterval(function () {
                            var now = new Date().getTime();
                            var distance = countDownDate - Math.ceil(now/1000)*1000;
                            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                            var timeForShow = "";
                            if(hours <= 0){
                                hours = null;
                            }else if (hours < 10) {
                                hours = '0' + hours;
                            }
                            if (minutes < 10) {
                                minutes = '0' + minutes;
                            }
                            if (seconds < 10) {
                                seconds = '0' + seconds;
                            }
                            timeForShow =  ((hours==null)? "" : (hours + ":")) + minutes + ":" + seconds;
                            if(distance <=1000){
                                timeForShow = ((hours==null) ? "" : "00:") + "00:00";
                                ays_block_element.find('div.ays-quiz-timer').html(timeForShow);
                                if(timerInTitle){
                                    document.title = timeForShow + " - " + tabTitle ;
                                }
                            }else{
                                ays_block_element.find('div.ays-quiz-timer').html(timeForShow);
                                if(timerInTitle){
                                    document.title = timeForShow + " - " + tabTitle ;
                                }
                            }
                            ays_block_element.find('div.ays-quiz-timer').slideDown(500);
                            var ays_block_element_redirect_url = ays_block_element.find('.ays_redirect_url').text();
                            if (distance <= 1) {
                                clearInterval(x);
                                var totalSteps = ays_block_element.find('div.step').length;
                                var currentStep = ays_block_element.eq(2).find('div.step.active-step');
                                var currentStepIndex = ays_block_element.eq(2).find('div.step.active-step').index();
                                if (currentStep.hasClass('ays_thank_you_fs') === false) {
                                    var steps = totalSteps - 3;
                                    ays_block_element.find('div.step').each(function (index) {
                                        if (index >= (currentStepIndex - 1) && index <= steps) {
                                            $(this).remove();
                                        }
                                    });
                                    window.location = ays_block_element_redirect_url;
                                }
                            }
                        }, 1000);
                    }
                }
                }, 1000);
            }
        });
        
        $(document).find('button.ays_check_answer').on('click', function (e) {
            var thisAnswerOptions;
            var quizContainer = $(e.target).parents('.ays-quiz-container');
            var right_answer_sound = quizContainer.find('.ays_quiz_right_ans_sound').get(0);
            var wrong_answer_sound = quizContainer.find('.ays_quiz_wrong_ans_sound').get(0);
            var questionId = $(this).parents('.step').data('questionId');
            var finishAfterWrongAnswer = (myOptions.finish_after_wrong_answer && myOptions.finish_after_wrong_answer == "on") ? true : false;
            thisAnswerOptions = myQuizOptions[questionId];
            if($(this).parent().find('.ays-text-input').val() !== ""){
                if ($(e.target).parents('form[id^="ays_finish_quiz"]').hasClass('enable_correction')) {
                    if($(e.target).parents('.step').hasClass('not_influence_to_score')){
                        return false;
                    }
                    $(this).css({
                        animation: "bounceOut .5s",
                    });
                    setTimeout(function(){
                        $(e.target).parent().find('.ays-text-input').css('width', '100%');
                        $(e.target).css("display", "none");
                    },480);
                    $(e.target).parent().find('.ays-text-input').css('background-color', '#eee');
                    $(this).parent().find('.ays-text-input').attr('disabled', 'disabled');
                    $(this).attr('disabled', 'disabled');
                    $(this).off('change');
                    $(this).off('click');
                    $(this).parents('.ays-field').addClass('ays-answered-text-input');
                    var input = $(this).parent().find('.ays-text-input');
                    var type = input.attr('type');
                    var userAnsweredText = input.val().trim();

                    // Enable case sensitive text
                    var enable_case_sensitive_text = (thisAnswerOptions.enable_case_sensitive_text && thisAnswerOptions.enable_case_sensitive_text != "") ? thisAnswerOptions.enable_case_sensitive_text : false;
                    
                    var trueAnswered = false;
                    var thisQuestionAnswer = thisAnswerOptions.question_answer.toLowerCase();
                    var displayingQuestionAnswer = thisAnswerOptions.question_answer;

                    if( type == 'text' || type == 'short_text' ){
                        if ( enable_case_sensitive_text ) {
                            thisQuestionAnswer = thisAnswerOptions.question_answer;
                        }
                    }
                    
                    if(type == 'date'){
                        var correctDate = new Date(thisAnswerOptions.question_answer),
                            correctDateYear = correctDate.getFullYear(),
                            correctDateMonth = correctDate.getMonth(),
                            correctDateDay = correctDate.getDate();
                        var userDate = new Date(userAnsweredText),
                            userDateYear = userDate.getFullYear(),
                            userDateMonth = userDate.getMonth(),
                            userDateDay = userDate.getDate();
                        if(correctDateYear == userDateYear && correctDateMonth == userDateMonth && correctDateDay == userDateDay){
                            trueAnswered = true;
                        }
                    }else if(type != 'number'){
                        thisQuestionAnswer = thisQuestionAnswer.split('%%%');
                        displayingQuestionAnswer = displayingQuestionAnswer.split('%%%');
                        for(var i = 0; i < thisQuestionAnswer.length; i++){
                            if ( enable_case_sensitive_text ) {
                                if(userAnsweredText == thisQuestionAnswer[i].trim()){
                                    trueAnswered = true;
                                    break;
                                }
                            } else {
                                if(userAnsweredText.toLowerCase() == thisQuestionAnswer[i].trim()){
                                    trueAnswered = true;
                                    break;
                                }
                            }
                        }
                    }else{
                        if(userAnsweredText.toLowerCase() == thisQuestionAnswer.trim()){
                            trueAnswered = true;
                        }
                    }
                    
                    if(trueAnswered){
                        if((right_answer_sound)){
                            resetPlaying([right_answer_sound, wrong_answer_sound]);
                            setTimeout(function(){
                                right_answer_sound.play();
                            }, 10);
                        }
                        $(this).parent().find('.ays-text-input').css('background-color', 'rgba(39,174,96,0.5)');
                        $(this).parent().find('input[name="ays_answer_correct[]"]').val(1);
                        if(! $(this).parents('.step').hasClass('not_influence_to_score')){
                            $(this).parents('.step').find('.right_answer_text').slideDown(250);
                        }
                    }else{
                        if((wrong_answer_sound)){
                            resetPlaying([right_answer_sound, wrong_answer_sound]);
                            setTimeout(function(){
                                wrong_answer_sound.play();
                            }, 10);
                        }
                        $(this).parent().find('.ays-text-input').css('background-color', 'rgba(243,134,129,0.8)');
                        $(this).parent().find('input[name="ays_answer_correct[]"]').val(0);
                        var rightAnswerText = '<div class="ays-text-right-answer">';
                            
                        if(type == 'date'){
                            var correctDate = new Date(thisAnswerOptions.question_answer),
                                correctDateYear = correctDate.getFullYear(),
                                correctDateMonth = (correctDate.getMonth() + 1) < 10 ? "0"+(correctDate.getMonth() + 1) : (correctDate.getMonth() + 1),
                                correctDateDay = (correctDate.getDate() < 10) ? "0"+correctDate.getDate() : correctDate.getDate();
                            rightAnswerText += [correctDateMonth, correctDateDay, correctDateYear].join('/');
                        }else if(type != 'number'){
                            // rightAnswerText += thisQuestionAnswer[0];
                            rightAnswerText += displayingQuestionAnswer[0];
                        }else{
                            // rightAnswerText += thisQuestionAnswer;
                            rightAnswerText += displayingQuestionAnswer;
                        }

                        rightAnswerText += '</div>';
                        $(this).parents('.ays-quiz-answers').append(rightAnswerText);
                        $(this).parents('.ays-quiz-answers').find('.ays-text-right-answer').slideDown(500);
                        if(! $(this).parents('.step').hasClass('not_influence_to_score')){
                            $(this).parents('.step').find('.wrong_answer_text').slideDown(250);
                        }
                        if(finishAfterWrongAnswer){
                            goToLastPage(e);
                        }
                    }
                    var showExplanationOn = (myOptions.show_questions_explanation && myOptions.show_questions_explanation != "") ? myOptions.show_questions_explanation : "on_results_page";
                    if(showExplanationOn == 'on_passing' || showExplanationOn == 'on_both'){
                        if(! $(this).parents('.step').hasClass('not_influence_to_score')){
                            $(this).parents('.step').find('.ays_questtion_explanation').slideDown(250);
                        }
                    }
                }
            }
        });

        $(document).find('textarea.ays_question_limit_length, input.ays_question_limit_length').on('keyup keypress', function(e) {
            var $this = $(this);
            var questionId = $this.attr('data-question-id');
            var container = $this.parents('.ays-field').next('.ays_quiz_question_text_conteiner');
            var box = container.find('.ays_quiz_question_text_message');
            var questionTextMessage = container.find('.ays_quiz_question_text_message_span');

            if (questionId !== null && questionId != '') {

                // Maximum length of a text field
                var enable_question_text_max_length = (myQuizOptions[questionId].enable_question_text_max_length && myQuizOptions[questionId].enable_question_text_max_length != "") ? myQuizOptions[questionId].enable_question_text_max_length : false;

                // Length
                var question_text_max_length = (myQuizOptions[questionId].question_text_max_length && myQuizOptions[questionId].question_text_max_length != "") ? parseInt(myQuizOptions[questionId].question_text_max_length) : '';

                // Limit by
                var question_limit_text_type = (myQuizOptions[questionId].question_limit_text_type && myQuizOptions[questionId].question_limit_text_type != "") ? myQuizOptions[questionId].question_limit_text_type : 'characters';

                // Show word/character counter
                var question_enable_text_message = (myQuizOptions[questionId].question_enable_text_message && myQuizOptions[questionId].question_enable_text_message != '') ? myQuizOptions[questionId].question_enable_text_message : false;

                var remainder = '';
                if(question_text_max_length != '' && question_text_max_length != 0){
                    switch ( question_limit_text_type ) {
                        case 'characters':
                            var tval = $this.val();
                            var tlength = tval.length;
                            var set = question_text_max_length;
                            var remain = parseInt(set - tlength);
                            if (remain <= 0 && e.which !== 0 && e.charCode !== 0) {
                                $this.val((tval).substring(0, tlength - 1));
                            }
                            if (e.type=="keyup") {
                                var tval = $this.val().trim();
                                if(tval.length > 0 && tval != null){
                                    var wordsLength = this.value.split('').length;
                                    if (wordsLength > question_text_max_length) {
                                        var trimmed = tval.split('', question_text_max_length).join("");
                                        $this.val(trimmed);
                                    }
                                }
                            }
                            remainder = remain;
                            break;
                        case 'words':
                            if (e.type=="keyup") {
                                var tval = $this.val().trim();
                                if(tval.length > 0 && tval != null){
                                    var wordsLength = this.value.match(/\S+/g).length;
                                    if (wordsLength > question_text_max_length) {
                                        var trimmed = tval.split(/\s+/, question_text_max_length).join(" ");
                                        $this.val(trimmed + " ");
                                    }
                                    remainder = question_text_max_length - wordsLength;
                                }
                            }
                            break;
                        default:
                            break;
                    }
                    if (e.type=="keyup") {
                        if ( question_enable_text_message ) {
                            if(question_text_max_length != '' && question_text_max_length != 0){
                                if (remainder <= 0) {
                                    remainder = 0;
                                    if (! box.hasClass('ays_quiz_question_text_error_message') ) {
                                        box.addClass('ays_quiz_question_text_error_message')
                                    }
                                }else{
                                    if ( box.hasClass('ays_quiz_question_text_error_message') ) {
                                        box.removeClass('ays_quiz_question_text_error_message')
                                    }
                                }
                                if (tval.length == 0 || tval == null) {
                                    if ( box.hasClass('ays_quiz_question_text_error_message') ) {
                                        box.removeClass('ays_quiz_question_text_error_message')
                                    }
                                    remainder = question_text_max_length;
                                }

                                questionTextMessage.html( remainder );
                            }
                        }
                    }
                }
            }
        });

        $(document).on('click', '.enable_max_selection_number input[type="checkbox"]', function(e) {
            var $this = $(this);

            var parent = $this.parents('.step');
            var questionId = parent.attr('data-question-id');
            questionId = parseInt( questionId );

            var checkedCount = parent.find('.ays-field input[type="checkbox"]:checked').length;

            if (questionId !== null && questionId != '' && typeof myQuizOptions[questionId] != 'undefined') {

                // Maximum length of a text field
                var enable_max_selection_number = (myQuizOptions[questionId].enable_max_selection_number && myQuizOptions[questionId].enable_max_selection_number != "") ? myQuizOptions[questionId].enable_max_selection_number : false;

                // Length
                var max_selection_number = (myQuizOptions[questionId].max_selection_number && myQuizOptions[questionId].max_selection_number != "") ? parseInt(myQuizOptions[questionId].max_selection_number) : '';

                if( enable_max_selection_number === true && max_selection_number != '' ){

                    if( max_selection_number < checkedCount ){
                        return false;
                    }
                }
            }

        });

        $(document).on('click', '.enable_min_selection_number input[type="checkbox"]', function(e) {
            var $this = $(this);
            var this_current_fs = $this.parents('.step[data-question-id]');
            var enableArrows = $this.parents(".ays-questions-container").find(".ays_qm_enable_arrows").val();
            var questions = $(this).parents('form').find('.step[data-question-id]');
            var next_sibilings_count = $(this).parents('form').find('.ays_question_count_per_page').val();
            var current_fs_indexes = [];

            var thisIndex = 0;
            for( var j = 0; j < Math.floor( questions.length / parseInt( next_sibilings_count ) ); j++ ){
                thisIndex += parseInt( next_sibilings_count );
                current_fs_indexes.push( thisIndex - 1 );
            }

            var thisStepIndex = 0;
            var thisQuestionStepIndex = questions.index( $this.parents('.step[data-question-id]') );
            for( var k = 0; k < current_fs_indexes.length; k++ ){
                if( thisQuestionStepIndex <= current_fs_indexes[k] ){
                    thisStepIndex = current_fs_indexes[k];
                    break;
                }
            }

            if( thisStepIndex == 0 ){
                thisStepIndex = questions.length - 1;
            }

            var current_fs_index = questions.index( questions.eq( thisStepIndex ) );

            // Display all questions on one page
            myOptions.quiz_display_all_questions = ( myOptions.quiz_display_all_questions ) ? myOptions.quiz_display_all_questions : 'off';
            var quiz_display_all_questions = (myOptions.quiz_display_all_questions && myOptions.quiz_display_all_questions == "on") ? true : false;

            if ( quiz_display_all_questions ) {
                next_sibilings_count = questions.length;
            }


            var buttonsDiv = $(this).parents('form').find('.step[data-question-id]').eq( thisStepIndex ).find('.ays_buttons_div');
            if($(this).parents('.step').attr('data-question-id')){
                if( parseInt( next_sibilings_count ) > 0 && ( $(this).parents('.step').attr('data-question-id') || $(this).parents('.step').next().attr('data-question-id') ) ){
                    var sliceStart = current_fs_index - parseInt( next_sibilings_count ) < 0 ? 0 : current_fs_index - parseInt( next_sibilings_count ) + 1;
                    // this_current_fs = questions.slice( sliceStart, current_fs_index + 1 );
                    this_current_fs = $(this).parents('.step[data-question-id]');
                }else{
                    this_current_fs = $(this).parents('.step[data-question-id]');
                    buttonsDiv = this_current_fs.find('.ays_buttons_div');
                }
            }else{
                this_current_fs = $(this).parents('.step[data-question-id]');
                buttonsDiv = this_current_fs.find('.ays_buttons_div');
            }

            this_current_fs.each(function(){
                var checkedMinSelCount = aysCheckMinimumCountCheckbox( $(this), myQuizOptions );

                if( ays_quiz_is_question_min_count( $(this), !checkedMinSelCount ) === true ){
                    if( checkedMinSelCount == true ){
                        if(enableArrows){
                            buttonsDiv.find('i.ays_next_arrow').removeAttr('disabled');
                            buttonsDiv.find('i.ays_next_arrow').prop('disabled', false);
                        }else{
                            buttonsDiv.find('input.ays_next').removeAttr('disabled');
                            buttonsDiv.find('input.ays_next').prop('disabled', false);
                        }
                    }else{
                        if(enableArrows){
                            buttonsDiv.find('i.ays_next_arrow').attr('disabled', 'disabled');
                            buttonsDiv.find('i.ays_next_arrow').prop('disabled', true);
                        }else{
                            buttonsDiv.find('input.ays_next').attr('disabled', 'disabled');
                            buttonsDiv.find('input.ays_next').prop('disabled', true);
                        }
                    }
                }else{
                    if(enableArrows){
                        buttonsDiv.find('i.ays_next_arrow').attr('disabled', 'disabled');
                        buttonsDiv.find('i.ays_next_arrow').prop('disabled', true);
                    }else{
                        buttonsDiv.find('input.ays_next').attr('disabled', 'disabled');
                        buttonsDiv.find('input.ays_next').prop('disabled', true);
                        return false;
                    }
                }
            });
        });

        $(document).find('input.ays_question_number_limit_length').on('keyup keypress', function(e) {
            var $this = $(this);
            var questionId = $this.attr('data-question-id');
            var parent = $this.parents('.ays-abs-fs');

            if (questionId !== null && questionId != '') {
                var questionOptions = myQuizOptions[questionId];

                // Maximum length of a number field
                var enable_question_number_max_length = (questionOptions.enable_question_number_max_length && questionOptions.enable_question_number_max_length != "") ? questionOptions.enable_question_number_max_length : false;

                // Length
                var question_number_max_length = (typeof questionOptions.question_number_max_length != 'undefined' && questionOptions.question_number_max_length !== "") ? parseInt(questionOptions.question_number_max_length) : '';

                // Minimum length of a number field
                var enable_question_number_min_length = (questionOptions.enable_question_number_min_length && questionOptions.enable_question_number_min_length != "") ? questionOptions.enable_question_number_min_length : false;

                // Length
                var question_number_min_length = (typeof questionOptions.question_number_min_length != 'undefined' && questionOptions.question_number_min_length !== "") ? parseInt(questionOptions.question_number_min_length) : '';

                // Show error message
                var enable_question_number_error_message = (questionOptions.enable_question_number_error_message && questionOptions.enable_question_number_error_message != "") ? questionOptions.enable_question_number_error_message : false;

                // Message
                var question_number_error_message = (questionOptions.question_number_error_message && questionOptions.question_number_error_message != "") ? questionOptions.question_number_error_message : '';


                if ( enable_question_number_max_length ) {
                    if(question_number_max_length !== ''){
                        var tval = $this.val().trim();
                        var inputVal = parseInt( tval );
                        if( ! isNaN(inputVal) ){
                            if ( inputVal > question_number_max_length ) {
                                $this.val(question_number_max_length);
                            }

                            if (e.type=="keyup") {
                                if ( inputVal > question_number_max_length ) {
                                    $this.val(question_number_max_length);
                                }
                            }
                        }
                    }
                }

                if ( enable_question_number_min_length ) {
                    if(question_number_min_length !== ''){
                        var tval = $this.val().trim();
                        var inputVal = parseInt( tval );
                        if( ! isNaN(inputVal) ){
                            if ( inputVal < question_number_min_length ) {
                                $this.val(question_number_min_length);
                            }

                            if (e.type=="keyup") {
                                if ( inputVal < question_number_min_length ) {
                                    $this.val(question_number_min_length);
                                }
                            }
                        }
                    }
                }

                if ( enable_question_number_error_message ) {
                    if ( question_number_error_message != "" ) {
                        var tval = $this.val().trim();
                        var inputVal = tval;

                        var errorMessageBox = parent.find('.ays-quiz-number-error-message');

                        if ( tval != "" ) {
                            
                            if ( isNaN( +inputVal ) ) {
                                if ( errorMessageBox.hasClass('ays_display_none') ) {
                                    errorMessageBox.removeClass('ays_display_none');
                                }
                            } else if ( tval.indexOf("e") > -1 ) {
                                if ( errorMessageBox.hasClass('ays_display_none') ) {
                                    errorMessageBox.removeClass('ays_display_none');
                                }
                            } else if ( tval.slice(-1) == "e" ) {
                                if ( errorMessageBox.hasClass('ays_display_none') ) {
                                    errorMessageBox.removeClass('ays_display_none');
                                }
                            } else {
                                if ( ! errorMessageBox.hasClass('ays_display_none') ) {
                                    errorMessageBox.addClass('ays_display_none');
                                }
                            }
                        } else {
                            if ( ! errorMessageBox.hasClass('ays_display_none') ) {
                                errorMessageBox.addClass('ays_display_none');
                            }
                        }
                    }
                }
            }
        });
        var totalCorrectAnswers = 0;
        $(document).on('change', 'input[name^="ays_questions"]', function (e) {
            var checked_inputs = $(e.target).parents().eq(1).find('input:checked');
            var quizContainer = $(e.target).parents('.ays-quiz-container');
            if($(e.target).parents('.step').hasClass('not_influence_to_score')){
                if($(e.target).attr('type') === 'radio') {
                    $(e.target).parents('.ays-quiz-answers').find('.checked_answer_div').removeClass('checked_answer_div');
                    $(e.target).parents('.ays-field').addClass('checked_answer_div');
                }
                if($(e.target).attr('type') === 'checkbox') {
                    if(!$(e.target).parents('.ays-field').hasClass('checked_answer_div')){
                        $(e.target).parents('.ays-field').addClass('checked_answer_div');
                    }else{
                        $(e.target).parents('.ays-field').removeClass('checked_answer_div');
                    }
                } 
                
                if (checked_inputs.parents('div[data-question-id]').find('input.ays_next').hasClass('ays_display_none') && checked_inputs.parents('div[data-question-id]').find('i.ays_next_arrow').hasClass('ays_display_none')) {
                    if (checked_inputs.attr('type') === 'radio') {
                        checked_inputs.parents('div[data-question-id]').find('.ays_next').trigger('click');                    
                    }
                }
                return false;
            }
            //@Shan
            if(checked_inputs.prev().val() == 1) {
                totalCorrectAnswers++;
            }
            $('.ays-question-score .ays-question-score-number').text(totalCorrectAnswers);

            if ($(e.target).parents().eq(4).hasClass('enable_correction')) {
                var right_answer_sound = quizContainer.find('.ays_quiz_right_ans_sound').get(0);
                var wrong_answer_sound = quizContainer.find('.ays_quiz_wrong_ans_sound').get(0);
                var finishAfterWrongAnswer = (myOptions.finish_after_wrong_answer && myOptions.finish_after_wrong_answer == "on") ? true : false;
                var showExplanationOn = (myOptions.show_questions_explanation && myOptions.show_questions_explanation != "") ? myOptions.show_questions_explanation : "on_results_page";
                if ($(e.target).parents().eq(1).find('input[name="ays_answer_correct[]"]').length !== 0) {
                    var checked_inputs = $(e.target).parents().eq(1).find('input:checked');
                    if (checked_inputs.attr('type') === "radio") {
                        checked_inputs.next().addClass('answered');
                        (checked_inputs.prev().val() == 1) ? checked_inputs.next().addClass('correct') : checked_inputs.next().addClass('wrong');
                        if (checked_inputs.prev().val() == 1) {
                            $(e.target).parents('.ays-field').addClass('correct_div checked_answer_div');
                            $(e.target).next('label').addClass('correct answered');

                            if(myOptions.answers_rw_texts && (myOptions.answers_rw_texts == 'on_passing' || myOptions.answers_rw_texts == 'on_both')){
                                var explanationTime = myOptions.explanation_time && myOptions.explanation_time != "" ? parseInt(myOptions.explanation_time) : 4;
                                if(! $(e.target).parents('.step').hasClass('not_influence_to_score')){
                                    $(e.target).parents().eq(3).find('.right_answer_text').slideDown(250);
                                }
                                explanationTimeout = setTimeout(function(){
                                    if (checked_inputs.parents('div[data-question-id]').find('input.ays_next').hasClass('ays_display_none') && checked_inputs.parents('div[data-question-id]').find('i.ays_next_arrow').hasClass('ays_display_none')) {
                                        checked_inputs.parents('div[data-question-id]').find('.ays_next').trigger('click');
                                    }
                                }, explanationTime*1000);
                            }else{
                                if (checked_inputs.parents('div[data-question-id]').find('input.ays_next').hasClass('ays_display_none') && checked_inputs.parents('div[data-question-id]').find('i.ays_next_arrow').hasClass('ays_display_none')) {
                                    checked_inputs.parents('div[data-question-id]').find('.ays_next').trigger('click');
                                }
                            }
                            if((right_answer_sound)){
                                resetPlaying([right_answer_sound, wrong_answer_sound]);
                                setTimeout(function(){
                                    right_answer_sound.play();
                                }, 10);
                            }
                        }
                        else {
                            $(e.target).parents('.ays-quiz-answers').find('input[name="ays_answer_correct[]"][value="1"]').parent().addClass('correct_div checked_answer_div');
                            $(e.target).parents('.ays-quiz-answers').find('input[name="ays_answer_correct[]"][value="1"]').next().next().addClass('correct answered');
                            $(e.target).parents('.ays-field').addClass('wrong_div');
                            
                            if(myOptions.answers_rw_texts && (myOptions.answers_rw_texts == 'on_passing' || myOptions.answers_rw_texts == 'on_both')){
                                var explanationTime = myOptions.explanation_time && myOptions.explanation_time != "" ? parseInt(myOptions.explanation_time) : 4;
                                if(! $(e.target).parents('.step').hasClass('not_influence_to_score')){
                                    $(e.target).parents().eq(3).find('.wrong_answer_text').slideDown(250);
                                }
                                explanationTimeout = setTimeout(function(){
                                    if (checked_inputs.parents('div[data-question-id]').find('input.ays_next').hasClass('ays_display_none') && 
                                        checked_inputs.parents('div[data-question-id]').find('i.ays_next_arrow').hasClass('ays_display_none')) {
                                        if(finishAfterWrongAnswer){
                                            goToLastPage(e);
                                        }else{
                                            checked_inputs.parents('div[data-question-id]').find('.ays_next').trigger('click');
                                        }
                                    }else{
                                        if(finishAfterWrongAnswer){
                                            goToLastPage(e);
                                        }
                                    }
                                }, explanationTime * 1000);
                            }else{
                                if (checked_inputs.parents('div[data-question-id]').find('input.ays_next').hasClass('ays_display_none') && 
                                    checked_inputs.parents('div[data-question-id]').find('i.ays_next_arrow').hasClass('ays_display_none')) {                                    
                                    if(finishAfterWrongAnswer){
                                        goToLastPage(e);
                                    }else{
                                        checked_inputs.parents('div[data-question-id]').find('.ays_next').trigger('click');
                                    }
                                }else{
                                    if(finishAfterWrongAnswer){
                                        goToLastPage(e);
                                    }
                                }
                            }
                            if((wrong_answer_sound)){
                                resetPlaying([right_answer_sound, wrong_answer_sound]);
                                setTimeout(function(){
                                    wrong_answer_sound.play();
                                }, 10);
                            }
                        }
                        if(showExplanationOn == 'on_passing' || showExplanationOn == 'on_both'){
                            if(! $(e.target).parents('.step').hasClass('not_influence_to_score')){
                                $(e.target).parents().eq(3).find('.ays_questtion_explanation').slideDown(250);
                            }
                        }
                        $(e.target).parents('div[data-question-id]').find('input[name^="ays_questions"]').attr('disabled', true);
                        $(e.target).parents('div[data-question-id]').find('input[name^="ays_questions"]').off('change');
                        $(e.target).parents('div[data-question-id]').find('.ays-field').css({
                            'pointer-events': 'none'
                        });
                    }else if(checked_inputs.attr('type') === "checkbox"){
                        checked_inputs = $(e.target);
                        if (checked_inputs.length === 1) {
                            if(checked_inputs.prev().val() == 1){
                                checked_inputs.parents('.ays-field').addClass('correct_div checked_answer_div');
                                checked_inputs.next().addClass('correct answered');
                                if((right_answer_sound)){
                                    resetPlaying([right_answer_sound, wrong_answer_sound]);
                                    setTimeout(function(){
                                        right_answer_sound.play();
                                    }, 10);
                                }
                            }else{
                                checked_inputs.parents('.ays-field').addClass('wrong_div');
                                checked_inputs.next().addClass('wrong answered');  
                                if((wrong_answer_sound)){
                                    resetPlaying([right_answer_sound, wrong_answer_sound]);
                                    setTimeout(function(){
                                        wrong_answer_sound.play();
                                    }, 10);
                                }
                                if(finishAfterWrongAnswer){
                                    goToLastPage(e);
                                }
                            }
                        }else{
                            for (var i = 0; i < checked_inputs.length; i++) {
                                if(checked_inputs.eq(i).prev().val() == 1){
                                    checked_inputs.eq(i).next().addClass('correct answered');
                                    if((right_answer_sound)){
                                        resetPlaying([right_answer_sound, wrong_answer_sound]);
                                        setTimeout(function(){
                                            right_answer_sound.play();
                                        }, 10);
                                    }
                                }else{
                                    checked_inputs.eq(i).next().addClass('wrong answered');
                                    if((wrong_answer_sound)){
                                        resetPlaying([right_answer_sound, wrong_answer_sound]);
                                        setTimeout(function(){
                                            wrong_answer_sound.play();
                                        }, 10);
                                    }
                                    if(finishAfterWrongAnswer){
                                        goToLastPage(e);
                                    }
                                }
                            }
                        }
                        $(e.target).attr('disabled', true);
                        $(e.target).off('change');
                    }
                }
            }else{                
                if($(e.target).attr('type') === 'radio') {
                    $(e.target).parents('.ays-quiz-answers').find('.checked_answer_div').removeClass('checked_answer_div');
                    $(e.target).parents('.ays-field').addClass('checked_answer_div');
                }
                if($(e.target).attr('type') === 'checkbox') {
                    if(!$(e.target).parents('.ays-field').hasClass('checked_answer_div')){
                        $(e.target).parents('.ays-field').addClass('checked_answer_div');
                    }else{
                        $(e.target).parents('.ays-field').removeClass('checked_answer_div');
                    }
                } 
                var checked_inputs = $(e.target).parents().eq(1).find('input:checked');
                if (checked_inputs.parents('div[data-question-id]').find('input.ays_next').hasClass('ays_display_none') && checked_inputs.parents('div[data-question-id]').find('i.ays_next_arrow').hasClass('ays_display_none')) {
                    if (checked_inputs.attr('type') === 'radio') {
                        checked_inputs.parents('div[data-question-id]').find('.ays_next').trigger('click');                    
                    }
                }
            }
        });

        $(document).on('input', '.information_form input[name="ays_user_phone"]', function(){
            if ($(this).attr('type') !== 'hidden') {
                $(this).removeClass('ays_red_border');
                $(this).removeClass('ays_green_border');
                if($(this).val() != ''){
                    if (!validatePhoneNumber($(this).get(0))) {
                        $(this).addClass('ays_red_border');
                    }else{
                        $(this).addClass('ays_green_border');
                    }
                }
            }
        });
        
        $(document).on('input', '.information_form input[name="ays_user_email"]', function(){
            if ($(this).attr('type') !== 'hidden') {
                $(this).removeClass('ays_red_border');
                $(this).removeClass('ays_green_border');
                if($(this).val() != ''){
                    if (!(emailValivatePattern.test($(this).val()))) {
                        $(this).addClass('ays_red_border');
                    }else{
                        $(this).addClass('ays_green_border');
                    }
                }
            }
        });

        $(document).on('input', 'input.ays_quiz_password', function(e){
            var $this = $(this);
            var startButton = $this.parents('.ays-quiz-container').find('input.start_button');
            if($this.val() != ''){
                startButton.removeAttr('disabled');
            }else{
                startButton.attr('disabled', 'disabled');
            }
        });

        $(document).on('click', '.ays-quiz-password-toggle', function(e){
            var $this  = $(this);
            
            var parent = $this.parents('.ays-quiz-password-toggle-visibility-box');
            var passwordInput = parent.find('.ays_quiz_password');

            var visibilityOn  = parent.find('.ays-quiz-password-toggle-visibility');
            var visibilityOff = parent.find('.ays-quiz-password-toggle-visibility-off');

            if( $this.hasClass('ays-quiz-password-toggle-visibility-off') ) {
                passwordInput.attr('type', 'text');
                    
                if ( visibilityOn.hasClass('ays_display_none') ) {
                    visibilityOn.removeClass('ays_display_none');
                }

                if ( ! visibilityOff.hasClass('ays_display_none') ) {
                    visibilityOff.addClass('ays_display_none');
                }

            } else if( $this.hasClass('ays-quiz-password-toggle-visibility') ) {
                passwordInput.attr('type', 'password');

                if ( ! visibilityOn.hasClass('ays_display_none') ) {
                    visibilityOn.addClass('ays_display_none');
                }

                if ( visibilityOff.hasClass('ays_display_none') ) {
                    visibilityOff.removeClass('ays_display_none');
                }                
            }
        });
        

        setTimeout(function(){
            $(document).find('input.ays_quiz_password').val('');
        }, 500);
        
        $(document).find('.ays-text-field .ays-text-input').each(function(ev){
            $(this).on('keydown', function(e){
                myOptions.enable_enter_key = !( myOptions.enable_enter_key ) ? "on" : myOptions.enable_enter_key;
                var enableEnterKey = (myOptions.enable_enter_key && myOptions.enable_enter_key == "on") ? true : false;
                if(enableEnterKey){
                    if (e.keyCode === 13 && !e.shiftKey) {
                        if(animating){
                            return false;
                        }

                        if($(this).parents('.step').find('input.ays_finish.action-button').length > 0){
                            $(this).parents('.step').find('input.ays_finish.action-button').trigger('click');
                        }else{
                            $(this).parents('.step').find('input.ays_next.action-button').trigger('click');
                        }
                        return false;
                    }
                }
            });
        });

        $(document).find('.ays_next').on('click', function(e){
            e.preventDefault();
            var quizId = $(this).parents('.ays-quiz-container').find('input[name="ays_quiz_id"]').val();

            if(checkQuizPassword(e, myOptions, true) === false){
                return false;
            }

            if ( typeof window.aysSeeResultConfirmBox[ quizId ] != 'undefined' && window.aysSeeResultConfirmBox[ quizId ] ) {
                window.aysSeeResultConfirmBox[ quizId ] = false;
                return false;
            }

            if(typeof explanationTimeout != 'undefined'){
                clearTimeout(explanationTimeout);
            }
            ays_quiz_container = $(this).parents(".ays-quiz-container");
            if (!($(this).hasClass('start_button'))) {
                if ($(this).parents('.step').find('input[required]').length !== 0) {
                    var empty_inputs = 0;
                    var required_inputs = $(this).parents('.step').find('input[required]');
                    $(this).parents('.step').find('.ays_red_border').removeClass('ays_red_border');
                    $(this).parents('.step').find('.ays_green_border').removeClass('ays_green_border');
                    for (var i = 0; i < required_inputs.length; i++) {
                        switch(required_inputs.eq(i).attr('name')){
                            case "ays_user_phone": {
                                if (!validatePhoneNumber(required_inputs.eq(i).get(0))) {
                                    required_inputs.eq(i).addClass('ays_red_border');
                                    required_inputs.eq(i).addClass('shake');
                                    empty_inputs++;
                                }
                                break;
                            }
                            case "ays_user_email": {
                                if (!(emailValivatePattern.test(required_inputs.eq(i).val()))) {
                                    required_inputs.eq(i).addClass('ays_red_border');
                                    required_inputs.eq(i).addClass('shake');
                                    empty_inputs++;
                                }
                                break;
                            }
                            default:{
                                if (required_inputs.eq(i).val() === '' &&
                                    required_inputs.eq(i).attr('type') !== 'hidden') {
                                    required_inputs.eq(i).addClass('ays_red_border');
                                    required_inputs.eq(i).addClass('shake');
                                    empty_inputs++;
                                }
                                break;
                            }
                        }
                    }
                    var empty_inputs2 = 0;
                    var phoneInput = $(this).parents('.step').find('input[name="ays_user_phone"]');
                    var emailInput = $(this).parents('.step').find('input[name="ays_user_email"]');
                    if(phoneInput.val() != ''){
                        phoneInput.removeClass('ays_red_border');
                        phoneInput.removeClass('ays_green_border');
                        if (!validatePhoneNumber(phoneInput.get(0))) {
                            if (phoneInput.attr('type') !== 'hidden') {
                                phoneInput.addClass('ays_red_border');
                                phoneInput.addClass('shake');
                                empty_inputs2++;
                            }
                        }else{
                            phoneInput.addClass('ays_green_border');
                        }
                    }
                    if(emailInput.val() != ''){
                        emailInput.removeClass('ays_red_border');
                        emailInput.removeClass('ays_green_border');
                        if (!(emailValivatePattern.test(emailInput.val()))) {
                            if (emailInput.attr('type') !== 'hidden') {
                                emailInput.addClass('ays_red_border');
                                emailInput.addClass('shake');
                                empty_inputs2++;
                            }
                        }else{
                            emailInput.addClass('ays_green_border');
                        }
                    }
                    var errorFields = $(this).parents('.step').find('.ays_red_border');
                    if (empty_inputs2 !== 0 || empty_inputs !== 0) {
                        setTimeout(function(){
                            errorFields.each(function(){
                                $(this).removeClass('shake');
                            });
                        }, 500);
                        setTimeout(function(){
                            required_inputs.each(function(){
                                $(this).removeClass('shake');
                            });
                        }, 500);
                        return false;
                    }else{
                        $(this).addClass('ays_start_allow');
                    }
                }else{
                    if ($(this).parents('.step').find('.information_form').length !== 0 ){
                        var empty_inputs = 0;
                        var phoneInput = $(this).parents('.step').find('input[name="ays_user_phone"]');
                        var emailInput = $(this).parents('.step').find('input[name="ays_user_email"]');
                        if(phoneInput.val() != ''){
                            phoneInput.removeClass('ays_red_border');
                            phoneInput.removeClass('ays_green_border');
                            if (!validatePhoneNumber(phoneInput.get(0))) {
                                if (phoneInput.attr('type') !== 'hidden') {
                                    phoneInput.addClass('ays_red_border');
                                    phoneInput.addClass('shake');
                                    empty_inputs++;
                                }
                            }else{
                                phoneInput.addClass('ays_green_border');
                            }
                        }
                        if(emailInput.val() != ''){
                            emailInput.removeClass('ays_red_border');
                            emailInput.removeClass('ays_green_border');
                            if (!(emailValivatePattern.test(emailInput.val()))) {
                                if (emailInput.attr('type') !== 'hidden') {
                                    emailInput.addClass('ays_red_border');
                                    emailInput.addClass('shake');
                                    empty_inputs++;
                                }
                            }else{
                                emailInput.addClass('ays_green_border');
                            }
                        }
                        var errorFields = $(this).parents('.step').find('.ays_red_border');
                        if (empty_inputs !== 0) {
                            setTimeout(function(){
                                errorFields.each(function(){
                                    $(this).removeClass('shake');
                                });
                            }, 500);
                            return false;
                        }
                        $(this).addClass('ays_start_allow');
                    }
                }
            }

            if (animating) return false;
            animating = true;
            current_fs = $(this).parents('.step');
            next_fs = $(this).parents('.step').next();
            var questions_count = $(this).parents('form').find('div[data-question-id]').length;
            var curent_number = $(this).parents('form').find('div[data-question-id]').index($(this).parents('div[data-question-id]')) + 1;
            var next_sibilings_count = $(this).parents('form').find('.ays_question_count_per_page').val();

            // Display all questions on one page
            myOptions.quiz_display_all_questions = ( myOptions.quiz_display_all_questions ) ? myOptions.quiz_display_all_questions : 'off';
            var quiz_display_all_questions = (myOptions.quiz_display_all_questions && myOptions.quiz_display_all_questions == "on") ? true : false;

            if ( quiz_display_all_questions ) {
                next_sibilings_count = questions_count;
            }

            if(parseInt(next_sibilings_count)>0 && ($(this).parents('.step').attr('data-question-id') || $(this).parents('.step').next().attr('data-question-id'))){

                if(parseInt(next_sibilings_count) >= questions_count){
                    next_sibilings_count = questions_count;
                }

                var current_fs_index = $(this).parents('form').find('.step').index($(this).parents('.step'));
                if($(this).parents('.step').attr('data-question-id')){
                    current_fs = $(this).parents('form').find('.step').slice(current_fs_index-parseInt(next_sibilings_count),current_fs_index+1);
                }else{
                    current_fs = $(this).parents('.step');
                }
                if(questions_count === curent_number){
                    if(current_fs.hasClass('.information_form').length !== 0){
                        current_fs.find('.ays_next').eq(current_fs.find('.ays_next').length-1).addClass('ays_timer_end');
                        current_fs.parents('.ays-quiz-container').find('.ays-quiz-timer').slideUp(500);
                        // setTimeout(function () {
                        //     current_fs.parents('.ays-quiz-container').find('.ays-quiz-timer').parent().hide();
                        // },500);
                    }
                }
                
                if(curent_number != questions_count){
                    if(($(this).hasClass('ays_finish')) == false){
                        if (!($(this).hasClass('start_button'))) {
                            var count_per_page = Math.floor(questions_count/parseInt(next_sibilings_count));
                            var nextCountQuestionsPerPage = questions_count-curent_number;
                            var current_width = $(this).parents('.ays-quiz-container').find('.ays-live-bar-fill').width();
                            var final_width = ((curent_number+parseInt(next_sibilings_count)) / questions_count * 100) + "%";
                            if(nextCountQuestionsPerPage < parseInt(next_sibilings_count)){
                                final_width = ((curent_number+nextCountQuestionsPerPage) / questions_count * 100) + "%";
                            }
                            if($(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').hasClass('ays-live-bar-count')){
                                if(nextCountQuestionsPerPage < parseInt(next_sibilings_count)){
                                    $(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').text(parseInt(curent_number+parseInt(nextCountQuestionsPerPage)));
                                }else{
                                    $(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').text(parseInt(curent_number+parseInt(next_sibilings_count)));
                                }
                            }else{
                                $(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').text(parseInt(final_width));
                            }
                            $(this).parents('.ays-quiz-container').find('.ays-live-bar-fill').animate({'width': final_width}, 1000);
                        }
                    }
                }else{
                    $(this).parents('.ays-quiz-container').find('.ays-live-bar-wrap').removeClass('rubberBand').addClass('bounceOut');
                    setTimeout(function () {
                        $(e.target).parents('.ays-quiz-container').find('.ays-live-bar-wrap').css('display','none');
                    },300)
                }
                var next_siblings = $(this).parents('.step').nextAll('.step').slice(0,parseInt(next_sibilings_count));

                if($(this).parents('form').find('div[data-question-id]').index($(this).parents('.step'))+1 !== $(this).parents('form').find('div[data-question-id]').length) {
                    for (var z = 0; z < next_siblings.length; z++) {
                        if (next_siblings.eq(z).attr('data-question-id') === undefined) {
                            next_siblings.splice(z);
                        }
                    }
                }else{
                    if(next_siblings.length !== 1) {
                        next_siblings.splice(next_siblings.length - 1);
                    }
                }
                $(e.target).parents().eq(3).find('input[name^="ays_questions"]').attr('disabled', false);
                for(var i=0 ;i<next_siblings.length-1;i++){
                    var nextQuestionType = next_siblings.eq(i).find('input[name^="ays_questions"]').attr('type');
                    var buttonsDiv = next_siblings.eq(i).find('.ays_buttons_div');
                    next_siblings.eq(i).find('.ays_previous').remove();
                    if(i === next_siblings.length-1 && next_siblings.eq(i).find('textarea[name^="ays_questions"]').attr('type')==='text'){
                        buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                        continue;
                    }
                    if(i === next_siblings.length-1 && nextQuestionType === 'checkbox'){
                        buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                        continue;
                    }
                    if(i === next_siblings.length-1 && nextQuestionType === 'number'){
                        buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                        continue;
                    }
                    if(i === next_siblings.length-1 && nextQuestionType === 'text'){
                        buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                        continue;
                    }
                    if(i === next_siblings.length-1 && nextQuestionType === 'date'){
                        buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                        continue;
                    }
                    next_siblings.eq(i).find('.ays_next').remove();
                    next_siblings.eq(i).find('.ays_early_finish').remove();
                }

                next_siblings.find('.ays_previous').remove();

                if(current_fs.hasClass('ays-abs-fs')){
                    current_fs = $(this).parents('.step');
                    next_fs = $(this).parents('.step').next();
                    current_fs.removeClass('active-step');
                    var counterClass = "";
                    switch(ays_quiz_container.data('questEffect')){
                        case "shake":
                            counterClass = ays_quiz_container.data('questEffect');
                        break;
                        case "fade":
                            counterClass = "fadeIn";
                        break;
                        case "none":
                            counterClass = "";
                        break;
                        default:
                            counterClass = ays_quiz_container.data('questEffect');
                        break;
                    }
                    next_fs.find('.ays-question-counter').addClass(counterClass);

                }

                var nextQuestionType = next_siblings.eq(next_siblings.length-1).find('input[name^="ays_questions"]').attr('type');
                var buttonsDiv = next_siblings.eq(next_siblings.length-1).find('.ays_buttons_div');
                var enableArrows = $(document).find(".ays-questions-container .ays_qm_enable_arrows").val();
                if(myOptions.enable_arrows){
                    enableArrows = myOptions.enable_arrows == 'on' ? true : false;
                }else{
                    enableArrows = parseInt(enableArrows) == 1 ? true : false;
                }

                buttonsDiv.find('i.ays_early_finish').addClass('ays_display_none');
                buttonsDiv.find('input.ays_early_finish').addClass('ays_display_none');

                var nextArrowIsDisabled = buttonsDiv.find('.ays_fa_arrow_right').hasClass('ays_display_none');
                var nextButtonIsDisabled = buttonsDiv.find('.ays_next').hasClass('ays_display_none');

                // if(nextArrowIsDisabled && nextButtonIsDisabled){
                    // buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                // }
                if( enableArrows ){
                   buttonsDiv.find('input.ays_next').addClass('ays_display_none');
                   buttonsDiv.find('input.ays_finish').addClass('ays_display_none');

                   buttonsDiv.find('.ays_fa_arrow_right').removeClass('ays_display_none');
                   buttonsDiv.find('.ays_arrow.ays_finish').removeClass('ays_display_none');
                } else {
                   buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                   buttonsDiv.find('input.ays_finish').removeClass('ays_display_none');
                }


                if(nextQuestionType === 'checkbox' && nextArrowIsDisabled && nextButtonIsDisabled){
                   buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                }
                if(nextQuestionType === 'checkbox' && enableArrows){
                   buttonsDiv.find('input.ays_next').addClass('ays_display_none');
                   buttonsDiv.find('.ays_fa_arrow_right').removeClass('ays_display_none');
                }
                if(nextQuestionType === 'number' && nextArrowIsDisabled && nextButtonIsDisabled){
                   buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                }
                if(nextQuestionType === 'number' && enableArrows){
                   buttonsDiv.find('input.ays_next').addClass('ays_display_none');
                   buttonsDiv.find('.ays_fa_arrow_right').removeClass('ays_display_none');
                }
                if(nextQuestionType === 'text' && nextArrowIsDisabled && nextButtonIsDisabled){
                   buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                }
                if(nextQuestionType === 'text' && enableArrows){
                   buttonsDiv.find('input.ays_next').addClass('ays_display_none');
                   buttonsDiv.find('.ays_fa_arrow_right').removeClass('ays_display_none');
                }
                if(nextQuestionType === 'date' && nextArrowIsDisabled && nextButtonIsDisabled){
                   buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                }
                if(nextQuestionType === 'date' && enableArrows){
                   buttonsDiv.find('input.ays_next').addClass('ays_display_none');
                   buttonsDiv.find('.ays_fa_arrow_right').removeClass('ays_display_none');
                }

                if (!($(this).hasClass('start_button')) && window.aysTimerIntervalFlag == null) {
                    var minSelHasError = 0;
                    var minSelQuestions = next_siblings;
                    // if(($(this).hasClass('ays_finish')) == false){
                    //    minSelQuestions = next_siblings;
                    // }else{
                        minSelQuestions = current_fs;
                    // }
                    for( var k = 0; k < minSelQuestions.length; k++ ){
                        if( $( minSelQuestions[k] ).find('.enable_min_selection_number').length > 0 ){
                            var checkedMinSelCount = aysCheckMinimumCountCheckbox( $( minSelQuestions[k] ), myQuizOptions );
                            if( ays_quiz_is_question_min_count( $( minSelQuestions[k] ), !checkedMinSelCount ) === true ){
                                if( checkedMinSelCount == true ){
                                    if(enableArrows){
                                        buttonsDiv.find('i.ays_next_arrow').removeAttr('disabled');
                                        buttonsDiv.find('i.ays_next_arrow').prop('disabled', false);
                                    }else{
                                        buttonsDiv.find('input.ays_next').removeAttr('disabled');
                                        buttonsDiv.find('input.ays_next').prop('disabled', false);
                                    }
                                }else{
                                    if(enableArrows){
                                        buttonsDiv.find('i.ays_next_arrow').attr('disabled', 'disabled');
                                        buttonsDiv.find('i.ays_next_arrow').prop('disabled', true);
                                        buttonsDiv.find('i.ays_next_arrow').removeClass('ays_display_none');
                                    }else{
                                        buttonsDiv.find('input.ays_next').attr('disabled', 'disabled');
                                        buttonsDiv.find('input.ays_next').prop('disabled', true);
                                        buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                                    }
                                    minSelHasError++;
                                }
                            }else{
                                if(enableArrows){
                                    buttonsDiv.find('i.ays_next_arrow').attr('disabled', 'disabled');
                                    buttonsDiv.find('i.ays_next_arrow').prop('disabled', true);
                                    buttonsDiv.find('i.ays_next_arrow').removeClass('ays_display_none');
                                }else{
                                    buttonsDiv.find('input.ays_next').attr('disabled', 'disabled');
                                    buttonsDiv.find('input.ays_next').prop('disabled', true);
                                    buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                                }
                                minSelHasError++;
                            }
                        }
                    }
                    if( minSelHasError > 0 ){
                        return false;
                    } 
                }

                ays_quiz_container.find('.active-step').removeClass('active-step');
                next_siblings.eq(0).addClass('active-step');
                aysAnimateStep(ays_quiz_container.data('questEffect'), current_fs, next_siblings);

                next_siblings.eq(0).find('.ays-text-input').trigger( "focus" );
                if ( ! next_siblings.eq(0).find('.ays-text-input').is(":focus") ) {
                    setTimeout(function(e){
                        next_siblings.eq(0).find('.ays-text-input').trigger( "focus" );
                    },1001);
                }
                            
                setTimeout(function(){
                    if(next_siblings.find('.ays-text-field').length > 0){
                        if(next_siblings.find('.ays-text-field').width() < 250){
                            next_siblings.find('.ays-text-field').css({
                                'flex-wrap': 'wrap',
                                'justify-content': 'center',
                                'padding': '5px'
                            });
                            next_siblings.find('.ays-text-field').find('input.ays-text-input').css('margin-bottom', '5px');
                        }
                    }
                },2000);
            }else{
                current_fs = $(this).parents('.step');
                next_fs = $(this).parents('.step').next();
                var questions_count = $(this).parents('form').find('div[data-question-id]').length;
                var curent_number = $(this).parents('form').find('div[data-question-id]').index($(this).parents('div[data-question-id]'))+1;
                if(questions_count === curent_number){
                    if(current_fs.hasClass('.information_form').length !== 0){
                        current_fs.find('.ays_next').addClass('ays_timer_end');
                        current_fs.parents('.ays-quiz-container').find('.ays-quiz-timer').slideUp(500);                        
                        // setTimeout(function () {
                        //     current_fs.parents('.ays-quiz-container').find('.ays-quiz-timer').parent().hide();
                        // },500);
                    }
                }
                if(curent_number != questions_count){
                    if(($(this).hasClass('ays_finish')) == false){
                        if (!($(this).hasClass('start_button'))) {
                            var current_width = $(this).parents('.ays-quiz-container').find('.ays-live-bar-fill').width();
                            var final_width = ((curent_number+1) / questions_count * 100) + "%";
                            if($(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').hasClass('ays-live-bar-count')){
                                $(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').text(parseInt(curent_number+1));
                            }else{
                                $(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').text(parseInt(final_width));
                            }
                            $(this).parents('.ays-quiz-container').find('.ays-live-bar-fill').animate({'width': final_width}, 1000);
                        }
                    }
                }else{
                    $(this).parents('.ays-quiz-container').find('.ays-live-bar-wrap').removeClass('rubberBand').addClass('bounceOut');
                    setTimeout(function () {
                        $(e.target).parents('.ays-quiz-container').find('.ays-live-bar-wrap').css('display','none');
                    },300)
                }
                if ($(this).parents('form').hasClass('enable_correction')) {
                    if (next_fs.find('.correct').length === 0 &&
                        next_fs.find('.wrong').length === 0 &&
                        next_fs.find('.ays-answered-text-input').length === 0) {
                        next_fs.find('input[name^="ays_questions"]').attr('disabled', false);
                    }
                }
                if (current_fs.hasClass('ays-abs-fs')) {
                    current_fs = $(this).parents('.step');
                    next_fs = $(this).parents('.step').next();
                    var counterClass = "";
                    switch(ays_quiz_container.data('questEffect')){
                        case "shake":
                            counterClass = ays_quiz_container.data('questEffect');
                        break;
                        case "fade":
                            counterClass = "fadeIn";
                        break;
                        case "none":
                            counterClass = "";
                        break;
                        default:
                            counterClass = ays_quiz_container.data('questEffect');
                        break;
                    }
                    next_fs.find('.ays-question-counter').addClass(counterClass);
                }
                current_fs.removeClass('active-step');
                next_fs.addClass('active-step');
                var nextQuestionType = next_fs.find('input[name^="ays_questions"]').attr('type');
                var buttonsDiv = next_fs.find('.ays_buttons_div');
                var enableArrows = $(document).find(".ays-questions-container .ays_qm_enable_arrows").val();
                if(myOptions.enable_arrows){
                    enableArrows = myOptions.enable_arrows == 'on' ? true : false;
                }else{
                    enableArrows = parseInt(enableArrows) == 1 ? true : false;
                }
                var nextArrowIsDisabled = buttonsDiv.find('.ays_next_arrow').hasClass('ays_display_none');
                var nextButtonIsDisabled = buttonsDiv.find('.ays_next').hasClass('ays_display_none');
                

                if(nextQuestionType === 'checkbox' && nextArrowIsDisabled && nextButtonIsDisabled){
                    buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                 }
                if(nextQuestionType === 'checkbox' && enableArrows){
                    buttonsDiv.find('input.ays_next').addClass('ays_display_none');
                    buttonsDiv.find('.ays_next_arrow').removeClass('ays_display_none');
                }


                if(nextQuestionType === 'number' && nextArrowIsDisabled && nextButtonIsDisabled){
                    buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                }
                if(nextQuestionType === 'number' && enableArrows){
                    buttonsDiv.find('input.ays_next').addClass('ays_display_none');
                    buttonsDiv.find('.ays_next_arrow').removeClass('ays_display_none');
                }
 
                if(next_fs.find('textarea[name^="ays_questions"]').attr('type')==='text' && nextArrowIsDisabled && nextButtonIsDisabled){
                    buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                }
                if(next_fs.find('textarea[name^="ays_questions"]').attr('type')==='text' && enableArrows){
                    buttonsDiv.find('input.ays_next').addClass('ays_display_none');
                    buttonsDiv.find('.ays_next_arrow').removeClass('ays_display_none');
                }

                if(nextQuestionType === 'text' && nextArrowIsDisabled && nextButtonIsDisabled){
                    buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                }
                if(nextQuestionType === 'text' && enableArrows){
                    buttonsDiv.find('input.ays_next').addClass('ays_display_none');
                    buttonsDiv.find('.ays_next_arrow').removeClass('ays_display_none');
                }

                if(nextQuestionType === 'date' && nextArrowIsDisabled && nextButtonIsDisabled){
                    buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                }
                if(nextQuestionType === 'date' && enableArrows){
                    buttonsDiv.find('input.ays_next').addClass('ays_display_none');
                    buttonsDiv.find('.ays_next_arrow').removeClass('ays_display_none');
                }
                
                aysAnimateStep(ays_quiz_container.data('questEffect'), current_fs, next_fs);
                next_fs.find('.ays-text-input').trigger( "focus" );
                if ( ! next_fs.find('.ays-text-input').is(":focus") ) {
                    setTimeout(function(e){
                        next_fs.find('.ays-text-input').trigger( "focus" );
                    },1001);
                }

                setTimeout(function(){
                    if(next_fs.find('.ays-text-field').length > 0){
                        if(next_fs.find('.ays-text-field').width() < 250){
                            next_fs.find('.ays-text-field').css({
                                'flex-wrap': 'wrap',
                                'justify-content': 'center',
                                'padding': '5px'
                            });
                            next_fs.find('.ays-text-field').find('input.ays-text-input').css('margin-bottom', '5px');
                        }
                    }
                },2000);
            }
            if($(document).scrollTop() >= $(this).parents('.ays-questions-container').offset().top){
                ays_quiz_container.goTo(myOptions);
            }
            if(current_fs.find('audio').length > 0){
                current_fs.find('audio').each(function(e, el){
                    el.pause();
                });
            }
            if(current_fs.find('video').length > 0){
                current_fs.find('video').each(function(e, el){
                    el.pause();
                });
            }

            //Current
            if(current_fs.find('audio').length > 0){
                var sound_src = next_fs.find('audio').attr('src');
                if (typeof sound_src !== 'undefined'){
                    var audio = next_fs.find('audio').get(0);
                    audio.pause();
                    audio.currentTime = 0;
                }
            }
            //Next
            var enableAudioAutoplay = (myOptions.enable_audio_autoplay && myOptions.enable_audio_autoplay == 'on') ? 'on' : 'off';
            if(next_fs.find('audio').length > 0){
                if(enableAudioAutoplay === 'on'){
                    var sound_src = next_fs.find('audio').attr('src');             
                    if (typeof sound_src !== 'undefined'){
                        var audio = next_fs.find('audio').get(0);
                        audio.currentTime = 0;
                        audio.play();
                    }
                }
            }
                
        });
         
        $(document).find('.ays_previous').on("click", function(e){
            ays_quiz_container = $(this).parents(".ays-quiz-container");
            if(typeof explanationTimeout != 'undefined'){
                clearTimeout(explanationTimeout);
                var thisButtonsDiv = $(this).parents(".ays_buttons_div");
                setTimeout(function(){
                    if (thisButtonsDiv.find('input.ays_next').hasClass('ays_display_none') &&
                        thisButtonsDiv.find('i.ays_next_arrow').hasClass('ays_display_none')) {
                        if(enableArrows){
                            thisButtonsDiv.find('i.ays_next_arrow').removeClass('ays_display_none');
                        }else{
                            thisButtonsDiv.find('input.ays_next').removeClass('ays_display_none');
                        }
                    }
                }, 1000);
            }
            
            if(animating) return false;
            animating = true;
            var next_sibilings_count = $(this).parents('form').find('.ays_question_count_per_page').val();
            if(parseInt(next_sibilings_count)>0 && ($(this).parents('.step').attr('data-question-id') || $(this).parents('.step').next().attr('data-question-id'))){
                var questions_count = $(this).parents('form').find('div[data-question-id]').length;
                var curent_number_of_this = $(this).parents('form').find('div[data-question-id]').index($(this).parents('div[data-question-id]')) + 1;
                var curent_number = $(this).parents('form').find('div[data-question-id]').index($(this).parents('div[data-question-id]')) - parseInt(next_sibilings_count) + 1;
                var count_per_page = questions_count%parseInt(next_sibilings_count);
                var nextCountQuestionsPerPage = questions_count-curent_number;
                if(count_per_page > 0 && curent_number_of_this == questions_count){
                    curent_number = $(this).parents('form').find('div[data-question-id]').index($(this).parents('div[data-question-id]')) - count_per_page + 1;
                }
                if (!($(this).hasClass('start_button'))) {
                    var current_width = $(this).parents('.ays-quiz-container').find('.ays-live-bar-fill').width();
                    var final_width = ((curent_number) / questions_count * 100) + "%";
                    if($(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').hasClass('ays-live-bar-count')){
                        $(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').text(parseInt(curent_number));
                    }else{
                        $(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').text(parseInt(final_width));
                    }
                    $(this).parents('.ays-quiz-container').find('.ays-live-bar-fill').animate({'width': final_width}, 1000);
                }
                var current_fs_index = $(this).parents('form').find('div[data-question-id]').index($(this).parents('form').find('.active-step').eq(0));
                if($(this).parents('.step').attr('data-question-id')){
                    current_fs = $(this).parents('form').find('div[data-question-id]').slice(current_fs_index,current_fs_index+parseInt(next_sibilings_count));
                }else{
                    current_fs = $(this).parent();
                }

                var current_first_fs_index = $(this).parents('form').find('div[data-question-id]').index($(this).parents('form').find('.active-step').eq(0));
                var next_fs = $('div[data-question-id]').slice((current_first_fs_index - parseInt(next_sibilings_count)), current_first_fs_index);
                
                var buttonsDiv = next_fs.find('.ays_buttons_div');
                var enableArrows = $(document).find(".ays-questions-container .ays_qm_enable_arrows").val();
                if(myOptions.enable_arrows){
                    enableArrows = myOptions.enable_arrows == 'on' ? true : false;
                }else{
                    enableArrows = parseInt(enableArrows) == 1 ? true : false;
                }

                if (buttonsDiv.find('input.ays_next').hasClass('ays_display_none') &&
                    buttonsDiv.find('i.ays_next_arrow').hasClass('ays_display_none')) {
                    if(enableArrows){
                        buttonsDiv.find('i.ays_next_arrow').removeClass('ays_display_none');
                    }else{
                        buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                    }
                }
                
                $(this).parents('form').find('div[data-question-id]').eq(current_fs_index).removeClass('active-step');
                next_fs.eq(0).addClass('active-step')
                if ($(this).parents('form').hasClass('enable_correction')) {
                    if (next_fs.find('.correct').length !== 0 || $(this).parents('div[data-question-id]').prev().find('.wrong').length !== 0) {
                        next_fs.find('input[name^="ays_questions"]').on('click',function () {
                            return false;
                        });
                    }
                }

                $(e.target).parents().eq(3).find('input[name^="ays_questions"]').attr('disabled', false);                
                aysAnimateStep(ays_quiz_container.data('questEffect'), current_fs, next_fs);

            }else{
                if ($(this).parents('form').hasClass('enable_correction')) {
                    if ($(this).parents('div[data-question-id]').prev().find('.correct').length === 0 &&
                        $(this).parents('div[data-question-id]').prev().find('.wrong').length === 0 &&
                        $(this).parents('div[data-question-id]').prev().find('.ays-answered-text-input').length === 0) {
                        $(this).parents('div[data-question-id]').prev().find('input[name^="ays_questions"]').attr('disabled', false);
                    }else{
                        $(this).parents('div[data-question-id]').prev().find('input[name^="ays_questions"]').attr('disabled', true);
                        if( $(this).parents('div[data-question-id]').prev().find('input[name^="ays_questions"]').attr('type') == 'checkbox' ){
                            $(this).parents('div[data-question-id]').prev().find('input[name^="ays_questions"]').attr('disabled', false);
                            $(this).parents('div[data-question-id]').prev().find('input[name^="ays_questions"][type="radio"]').on('click',function () {
                                return false;
                            });
                        }
                    }
                }
                current_fs = $(this).parents('.step');
                next_fs = $(this).parents('.step').prev();
                
                var buttonsDiv = next_fs.find('.ays_buttons_div');
                var enableArrows = $(document).find(".ays-questions-container .ays_qm_enable_arrows").val();
                if(myOptions.enable_arrows){
                    enableArrows = myOptions.enable_arrows == 'on' ? true : false;
                }else{
                    enableArrows = parseInt(enableArrows) == 1 ? true : false;
                }

                if (buttonsDiv.find('input.ays_next').hasClass('ays_display_none') &&
                    buttonsDiv.find('i.ays_next_arrow').hasClass('ays_display_none')) {
                    if(enableArrows){
                        buttonsDiv.find('i.ays_next_arrow').removeClass('ays_display_none');
                    }else{
                        buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                    }
                }

                if (current_fs.hasClass('ays-abs-fs')) {
                    current_fs = $(this).parent().parent().parent();
                    next_fs = $(this).parent().parent().parent().prev();
                    var counterClass = "";
                    switch(ays_quiz_container.data('questEffect')){
                        case "shake":
                            counterClass = ays_quiz_container.data('questEffect');
                        break;
                        case "fade":
                            counterClass = "fadeIn";
                        break;
                        case "none":
                            counterClass = "";
                        break;
                        default:
                            counterClass = ays_quiz_container.data('questEffect');
                        break;
                    }
                    next_fs.find('.ays-question-counter').addClass(counterClass);
                }
                current_fs.removeClass('active-step');
                next_fs.addClass('active-step');

                var questions_count = $(this).parents('form').find('div[data-question-id]').length;
                var curent_number = $(this).parents('form').find('div[data-question-id]').index($(this).parents('div[data-question-id]'))-1;
                if(curent_number != questions_count){
                    if(($(this).hasClass('ays_finish')) == false){
                        if (!($(this).hasClass('start_button'))) {
                            var current_width = $(this).parents('.ays-quiz-container').find('.ays-live-bar-fill').width();
                            var final_width = ((curent_number+1) / questions_count * 100) + "%";
                            if($(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').hasClass('ays-live-bar-count')){
                                $(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').text(parseInt(curent_number+1));
                            }else{
                                $(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').text(parseInt(final_width));
                            }
                            $(this).parents('.ays-quiz-container').find('.ays-live-bar-fill').animate({'width': final_width}, 1000);
                        }
                    }
                }else{
                    $(this).parents('.ays-quiz-container').find('.ays-live-bar-wrap').removeClass('rubberBand').addClass('bounceOut');
                    setTimeout(function () {
                        $(e.target).parents('.ays-quiz-container').find('.ays-live-bar-wrap').css('display','none');
                    },300)
                }
                
                aysAnimateStep(ays_quiz_container.data('questEffect'), current_fs, next_fs);
                
                next_fs.find('.ays-text-input').trigger( "focus" );
                if ( ! next_fs.find('.ays-text-input').is(":focus") ) {
                    setTimeout(function(e){
                        next_fs.find('.ays-text-input').trigger( "focus" );
                    },1001);
                }
            }
            if($(document).scrollTop() >= $(this).parents('.ays-questions-container').offset().top){
                ays_quiz_container.goTo(myOptions);
            }
            if(current_fs.find('audio').length > 0){
                current_fs.find('audio').each(function(e, el){
                    el.pause();
                });
            }
            if(current_fs.find('video').length > 0){
                current_fs.find('video').each(function(e, el){
                    el.pause();
                });
            }

            //Current
            if(current_fs.find('audio').length > 0){  
                var sound_src = next_fs.find('audio').attr('src');
                if (typeof sound_src !== 'undefined'){
                    var audio = next_fs.find('audio').get(0);
                    audio.pause();
                    audio.currentTime = 0;
                }     
            }
            //Previous
            var enableAudioAutoplay = (myOptions.enable_audio_autoplay && myOptions.enable_audio_autoplay == 'on') ? 'on' : 'off';
            if(next_fs.find('audio').length > 0){
                if(enableAudioAutoplay === 'on'){
                    var sound_src = next_fs.find('audio').attr('src');             
                    if (typeof sound_src !== 'undefined'){
                        var audio = next_fs.find('audio').get(0);
                        audio.currentTime = 0;
                        audio.play();
                    }
                }
            }
        });
        
        $(document).on('click', '.ays-quiz-container .ays_question_hint', function (e) {
            e.preventDefault();
            
            $(e.target).parents('.ays-quiz-container').find('.ays_music_sound').toggleClass('z_index_0');
            $(e.target).parent().find('.ays_question_hint_text').toggleClass('show_hint');
            if($(e.target).parent().find('.ays_question_hint_text').hasClass('show_hint')){
                $(window).on('click', function(ev){
                    if( ! ( $(ev.target).hasClass('ays_question_hint_text') || $(ev.target).hasClass('ays_question_hint') ) ){
                        $(e.target).parent().find('.ays_question_hint_text').removeClass('show_hint')
                        $(e.target).parents('.ays-quiz-container').find('.ays_music_sound').removeClass('z_index_0');
                    }
                });
            }
        });

        $(document).on('click', '.ays-field', function() {
            if ($(this).find(".select2").hasClass('select2-container--open')) {
                $(this).find('b[role="presentation"]').removeClass('ays_fa ays_fa_chevron_down');
                $(this).find('b[role="presentation"]').addClass('ays_fa ays_fa_chevron_up');
            } else {
                $(this).find('b[role="presentation"]').removeClass('ays_fa ays_fa_chevron_up');
                $(this).find('b[role="presentation"]').addClass('ays_fa ays_fa_chevron_down');
            }
        });

        $(document).find('select.ays-select').on("select2:selecting", function(e){
            $(this).parents('.ays-quiz-container').find('b[role="presentation"]').addClass('ays_fa ays_fa_chevron_down');
        });

        $(document).find('select.ays-select').on("select2:opening", function(e){
            $(this).parents('.ays-quiz-container').css('z-index', 1);
            $(this).parents('.step').css('z-index', 1);
        });
        
        $(document).find('select.ays-select').on("select2:closing", function(e){
            $(this).parents('.ays-quiz-container').find('b[role="presentation"]').addClass('ays_fa ays_fa_chevron_down');
            $(this).parents('.ays-quiz-container').css('z-index', 'initial');
            $(this).parents('.step').css('z-index', 'initial');
        });
        
        $(document).find('select.ays-select').on("select2:select", function(e){
            var quizContainer = $(e.target).parents('.ays-quiz-container');
            var right_answer_sound = quizContainer.find('.ays_quiz_right_ans_sound').get(0);
            var wrong_answer_sound = quizContainer.find('.ays_quiz_wrong_ans_sound').get(0);
            var finishAfterWrongAnswer = (myOptions.finish_after_wrong_answer && myOptions.finish_after_wrong_answer == "on") ? true : false;
            $(this).parent().find('.ays-select-field-value').attr("value", $(this).val());
            if($(this).parents(".ays-questions-container").find('form[id^="ays_finish_quiz"]').hasClass('enable_correction')) {
                var chishtPatasxan = $(this).find('option:selected').data("chisht");
                if (chishtPatasxan == 1) {
                    if((right_answer_sound)){
                        resetPlaying([right_answer_sound, wrong_answer_sound]);
                        setTimeout(function(){
                            right_answer_sound.play();
                        }, 10);
                    }
                    $(this).parents('.ays-field').addClass('correct correct_div');
                    $(this).parents('.ays-field').find('.select2-selection.select2-selection--single').css("border-bottom-color", "green");
                } else {
                    if((wrong_answer_sound)){
                        resetPlaying([right_answer_sound, wrong_answer_sound]);
                        setTimeout(function(){
                            wrong_answer_sound.play();
                        }, 10);
                    }
                    $(this).parents('.ays-field').addClass('wrong wrong_div');
                    $(this).parents('.ays-field').find('.select2-selection.select2-selection--single').css("border-bottom-color", "red");
                    var rightAnswerText = '<div class="ays-text-right-answer">'+
                        $(this).find('option[data-chisht="1"]').html()+
                        '</div>';
                    $(this).parents('.ays-quiz-answers').append(rightAnswerText);
                    $(this).parents('.ays-quiz-answers').find('.ays-text-right-answer').css("text-align", "left");
                    $(this).parents('.ays-quiz-answers').find('.ays-text-right-answer').slideDown(500);
                }
                if(myOptions.answers_rw_texts && (myOptions.answers_rw_texts == 'on_passing' || myOptions.answers_rw_texts == 'on_both')){
                    if (chishtPatasxan == 1) {
                        $(e.target).parents().eq(3).find('.right_answer_text').slideDown(500);
                    } else {
                        $(e.target).parents().eq(3).find('.wrong_answer_text').slideDown(500);
                    }
                }                
                var explanationTime = myOptions.explanation_time && myOptions.explanation_time != "" ? parseInt(myOptions.explanation_time) : 4;
                if(finishAfterWrongAnswer && chishtPatasxan != 1){
                    $(e.target).parents('div[data-question-id]').find('.ays_next').attr('disabled', 'disabled');
                    $(e.target).parents('div[data-question-id]').find('.ays_early_finish').attr('disabled', 'disabled');
                }
                explanationTimeout = setTimeout(function(){
                    if (chishtPatasxan == 1) {
                        if ($(e.target).parents('div[data-question-id]').find('input.ays_next').hasClass('ays_display_none') &&
                            $(e.target).parents('div[data-question-id]').find('i.ays_next_arrow').hasClass('ays_display_none')) {
                            $(e.target).parents('div[data-question-id]').find('.ays_next').trigger('click');
                        }
                    }else{
                        if(finishAfterWrongAnswer){
                            goToLastPage(e);
                        }else{
                            if ($(e.target).parents('div[data-question-id]').find('input.ays_next').hasClass('ays_display_none') &&
                                $(e.target).parents('div[data-question-id]').find('i.ays_next_arrow').hasClass('ays_display_none')) {
                                $(e.target).parents('div[data-question-id]').find('.ays_next').trigger('click');
                            }
                        }
                    }
                }, explanationTime*1000);
                
                var showExplanationOn = (myOptions.show_questions_explanation && myOptions.show_questions_explanation != "") ? myOptions.show_questions_explanation : "on_results_page";
                if(showExplanationOn == 'on_passing' || showExplanationOn == 'on_both'){
                    if(! $(this).parents('.step').hasClass('not_influence_to_score')){
                        $(this).parents('.step').find('.ays_questtion_explanation').slideDown(250);
                    }
                }
                
                $(this).attr("disabled", true);
                $(e.target).next().css("background-color", "#777");
                $(e.target).next().find('.selection').css("background-color", "#777");
                $(e.target).next().find('.select2-selection').css("background-color", "#777");
            }else{
                if ($(this).parents('div[data-question-id]').find('input.ays_next').hasClass('ays_display_none') && $(this).parents('div[data-question-id]').find('i.ays_next_arrow').hasClass('ays_display_none')) {
                    $(this).parents('div[data-question-id]').find('.ays_next').trigger('click');
                }
            }
            var this_select_value = $(this).val();
            $(this).find("option").removeAttr("selected");
            $(this).find("option[value='"+this_select_value+"']").attr("selected", true);
        });

        var shareButtons = document.querySelectorAll(".ays-share-btn.ays-to-share");

        if (shareButtons) {
            [].forEach.call(shareButtons, function(button) {
                button.addEventListener("click", function(event) {
                    var width = 650,
                        height = 450;

                    event.preventDefault();

                    window.open(this.href, quizLangObj.shareDialog, 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,width='+width+',height='+height+',top='+(screen.height/2-height/2)+',left='+(screen.width/2-width/2));
                });
            });
        }

        $(document).find('.ays-quiz-container').map(function () {
            $(this).find('div[data-question-id]').eq(0).find('.ays_previous').css({'display':'none'});
            var next_sibilings_count = parseInt($(this).find('.ays_question_count_per_page').val());
            if(next_sibilings_count>0){
                $(this).find('div[data-question-id]').eq(next_sibilings_count-1).find('.ays_previous').css({'display':'none'});
            }
        });

        $(document).find('.ays_finish.action-button.ays_arrow').on('click', function () {
            $(this).parents('.ays_buttons_div').find('input.ays_next.action-button').trigger('click');
        });

        $(document).find('div[data-question-id]').map(function () {
            $(this).find('.ays-quiz-answers .ays-field').map(function () {
                if($(this).find('label[for^="ays-answer"]').eq(1).find('img').length !== 0){
                    $(this).find('label[for^="ays-answer"]').eq(0).addClass('ays_empty_before_content');
                    if($(this).find('label[for^="ays-answer"]').eq(0).text().length === 0){
                        $(this).find('label[for^="ays-answer"]').eq(0).css('background','transparent');
                    }
                }
            });
        });
        
        $(document).find('.ays_finish.action-button').on('click', function () {
            var quizId = $(this).parents(".ays-quiz-container").find('input[name="ays_quiz_id"]').val();
            if ( typeof window.aysEarlyFinishConfirmBox != 'undefined') {
                if ( typeof window.aysEarlyFinishConfirmBox[ quizId ] != 'undefined' ) {
                    if( window.aysSeeResultConfirmBox[ quizId ] == true ){
                        $(this).addClass("ays_timer_end");
                    }
                }
            }
            if (typeof $(this).parents('.ays-quiz-container').find('.ays_next.start_button').attr("data-enable-leave-page") !== 'undefined') {
                if(! $(this).parents('.ays-quiz-container').find('.step.active-step .ays-abs-fs.ays-end-page').hasClass('information_form')){
                    $(this).parents('.ays-quiz-container').find('.ays_next.start_button').attr("data-enable-leave-page",false);
                }
            }
        });        
        
        $(document).on('click', '.ays_early_finish.action-button', function (e) {
            e.preventDefault();
            var quizId = $(this).parents('.ays-quiz-container').find('input[name="ays_quiz_id"]').val();

            myOptions.enable_early_finsh_comfirm_box = ! myOptions.enable_early_finsh_comfirm_box ? 'on' : myOptions.enable_early_finsh_comfirm_box;
            var enable_early_finsh_comfirm_box = (myOptions.enable_early_finsh_comfirm_box && myOptions.enable_early_finsh_comfirm_box == 'on') ? true : false;
            if (enable_early_finsh_comfirm_box) {
                var confirm = window.confirm(quizLangObj.areYouSure);
            }else{
                var confirm = true;
            }
            if(confirm){
                clearTimeout(explanationTimeout);
                window.aysEarlyFinishConfirmBox[ quizId ] = true;
                var totalSteps = $(e.target).parents().eq(3).find('div.step').length;
                var currentStep = $(e.target).parents().eq(3).find('div.step.active-step');
                var thankYouStep = $(e.target).parents().eq(3).find('div.step.ays_thank_you_fs');
                var infoFormLast = thankYouStep.prev().find('div.information_form');
                var questions_count = $(e.target).parents('form').find('div[data-question-id]').length;
                $(this).parents('.ays-quiz-container').find('.ays_finish.action-button').addClass("ays_timer_end");
                if (typeof $(this).parents('.ays-quiz-container').find('.ays_next.start_button').attr("data-enable-leave-page") !== 'undefined') {
                    if(! $(this).parents('.ays-quiz-container').find('.step .ays-abs-fs.ays-end-page').hasClass('information_form')){
                        $(this).parents('.ays-quiz-container').find('.ays_next.start_button').attr("data-enable-leave-page",false);
                    }
                }
                if($(e.target).parents('.ays-quiz-container').find('.ays-live-bar-percent').hasClass('ays-live-bar-count')){
                    $(e.target).parents('.ays-quiz-container').find('.ays-live-bar-percent').text(questions_count);
                }else{
                    $(e.target).parents('.ays-quiz-container').find('.ays-live-bar-fill').animate({
                        width: '100%'
                    });
                    $(e.target).parents('.ays-quiz-container').find('.ays-live-bar-percent').text(100);
                }
                currentStep.parents('.ays-quiz-container').find('.ays-quiz-timer').slideUp();
                setTimeout(function () {                                        
                    currentStep.parents('.ays-quiz-container').find('.ays-quiz-timer').parent().hide();
                },300);
                if(infoFormLast.length == 0){
                    if (currentStep.hasClass('ays_thank_you_fs') === false) {
                        var steps = totalSteps - 3;
                        $(e.target).parents().eq(3).find('div.step').each(function (index) {
                            if ($(this).hasClass('ays_thank_you_fs')) {
                                $(this).addClass('active-step')
                            }else{
                                $(this).css('display', 'none');                                                
                            }
                        });
                        $(e.target).parents().eq(3).find('input.ays_finish').trigger('click');
                    }
                }else{
                    currentStep.parents('.ays-quiz-container').find('.ays-quiz-timer').parent().hide();
                    $(e.target).parents('.ays-quiz-container').find('.ays-live-bar-wrap').removeClass('rubberBand').addClass('bounceOut');
                    setTimeout(function () {
                        $(e.target).parents('.ays-quiz-container').find('.ays-live-bar-wrap').css('display','none');
                    },300);
                    aysAnimateStep($(e.target).parents('.ays-quiz-container').data('quest-effect'), currentStep, infoFormLast.parent());
                    $(e.target).parents().eq(3).find('div.step').each(function (index) {
                        $(this).css('display', 'none');
                        $(this).removeClass('active-step')
                    });
                    infoFormLast.parent().css('display', 'flex');
                    infoFormLast.parent().addClass('active-step'); 
                }
            }
        });

        function goToLastPage(e){
            clearTimeout(explanationTimeout);
            if ( typeof aysTimerInterval !== "undefined" ) {
                clearInterval(aysTimerInterval);
            }
            var container = $(e.target).parents('.ays-quiz-container');
            var totalSteps = container.find('div.step').length;
            var currentStep = container.find('div.step.active-step');
            var thankYouStep = container.find('div.step.ays_thank_you_fs');
            var infoFormLast = thankYouStep.prev().find('div.information_form');
            var questions_count = $(e.target).parents('form').find('div[data-question-id]').length;
            if(container.find('.ays-live-bar-percent').hasClass('ays-live-bar-count')){
                container.find('.ays-live-bar-percent').text(questions_count);
            }else{
                container.find('.ays-live-bar-fill').animate({
                    width: '100%'
                });
                container.find('.ays-live-bar-percent').text(100);
            }
            container.find('.ays-quiz-timer').slideUp();
            setTimeout(function () {                                        
                container.find('.ays-quiz-timer').parent().hide();
            },300);
            if(infoFormLast.length == 0){
                if (currentStep.hasClass('ays_thank_you_fs') === false) {
                    var steps = totalSteps - 3;
                    container.find('div.step').each(function (index) {
                        if ($(this).hasClass('ays_thank_you_fs')) {
                            $(this).addClass('active-step')
                        }else{
                            $(this).css('display', 'none');                                                
                        }
                    });
                    container.find('input.ays_finish').trigger('click');
                }
            }else{
                container.find('.ays-quiz-timer').parent().hide();
                container.find('.ays-live-bar-wrap').removeClass('rubberBand').addClass('bounceOut');
                setTimeout(function () {
                    container.find('.ays-live-bar-wrap').css('display','none');
                },300);
                aysAnimateStep(container.data('quest-effect'), currentStep, infoFormLast.parent());
                container.find('div.step').each(function (index) {
                    $(this).css('display', 'none');
                    $(this).removeClass('active-step')
                });
                infoFormLast.parent().css('display', 'flex');
                infoFormLast.parent().addClass('active-step'); 
            }
        }
        
        $(document).find('.action-button.ays_restart_button').on('click', function () {
            window.location.reload();
        });
        
        $(document).on('click', '.action-button.ays_clear_answer', function () {
            var $this = $(this);
            var activeStep = $this.parents('.step');
            var inputs = activeStep.find('input[name^="ays_questions[ays-question-"]:checked');
            var checked_answer_divs = activeStep.find('div.ays-field.checked_answer_div');
            var ays_text_field = activeStep.find('div.ays-field.ays-text-field');
            var ays_select_field = activeStep.find('div.ays-field.ays-select-field');
            checked_answer_divs.removeClass('checked_answer_div');
            ays_text_field.find('.ays-text-input').val('');
            if(ays_select_field.find('select.ays-select').length > 0){
                ays_select_field.find('select.ays-select').val(null).trigger('change');
            }
            inputs.removeAttr('checked');
        });
        
        $(document).on('click', '.ays_music_sound', function() {
            var $this = $(this);
            var quizCoutainer = $this.parents('.ays-quiz-container');
            var audioEls = $(document).find('.ays_quiz_music');
            var soundEls = $(document).find('.ays_music_sound');
            var audioEl = quizCoutainer.find('.ays_quiz_music').get(0);
            if($this.hasClass('ays_sound_active')){
                audioEl.volume = 0;
                $this.find('.ays_fa').addClass('ays_fa_volume_off').removeClass('ays_fa_volume_up');
                $this.removeClass('ays_sound_active');
            } else {
                audioEl.volume = 1;
                $this.find('.ays_fa').addClass('ays_fa_volume_up').removeClass('ays_fa_volume_off');
                $this.addClass('ays_sound_active');
            }
        });
        
        $(document).find('.ays-quiz-container').each(function(){
            var $this = $(this);
            var selectEl = $this.find('select.ays-select');
            selectEl.each(function(){
                $(this).select2({
                    placeholder: quizLangObj.selectPlaceholder,
                    dropdownParent: $(this).parents('.ays-abs-fs')
                });
            });
        });

        $(document).find('b[role="presentation"]').addClass('ays_fa ays_fa_chevron_down');

        function aysResetQuiz ($quizContainer){
            var cont = $quizContainer.find('div[data-question-id]');
            cont.find('input[type="text"], textarea, input[type="number"], input[type="url"], input[type="email"]').each(function(){
                $(this).val('');
            });
            cont.find('select').each(function(){
                $(this).val('');
            });
            cont.find('select.ays-select').each(function(){
                $(this).val(null).trigger('change');
            });
            cont.find('select option').each(function(){
                $(this).removeAttr('selected');
            });
            cont.find('input[type="radio"], input[type="checkbox"]').each(function(){
                $(this).removeAttr('checked');
            });
        }
       
        window.onbeforeunload =  function (e) {
            var startButton = $(document).find('.ays-quiz-container .ays_next.start_button');
            var flag = false;
            for (var i = 0; i < startButton.length; i++) {
                var startBtn = startButton.eq(i).attr('data-enable-leave-page');
                if(typeof startBtn != undefined && startBtn === 'true'){
                    flag = true;
                    break;
                }
            }

            if(flag){
                return true;
            }else{
                return null;
            }
        }

        $(document).find('.ays_next.start_button.ays_quiz_enable_loader').each(function(e){
            var $this = $(this);
            var container = $(this).parents('.ays-quiz-container');

            var ays_quiz = setInterval( function() {
                if (document.readyState === 'complete') {
                    var startButtonText = quizLangObj.startButtonText;
                    if (startButtonText == null || startButtonText == '' ) {
                        startButtonText = quizLangObj.defaultStartButtonText;
                    }

                    container.find('.ays_quiz_start_button_loader').addClass('ays_display_none');
                    if ( $this.hasClass('ays_quiz_enable_loader') ) {
                        $this.removeClass('ays_quiz_enable_loader');
                    }

                    var passwordQuizInput = container.find("input.ays_quiz_password");
                    if(passwordQuizInput.length > 0){
                        $this.prop('disabled', true);
                    } else {
                        $this.prop('disabled', false);
                    }

                    $this.val( startButtonText );
                    clearInterval(ays_quiz);
                }
            } , 500);
        });

        $(document).find('.show_timer_countdown').each(function(e){
            // Countdown date
            var countDownEndDate = $(this).data('timer_countdown');
            var quiz_id = $(this).parents(".ays-quiz-container").attr("id");
            if (countDownEndDate != '' && countDownEndDate != undefined) {
                var showM = $(this).parents('.step').data('messageExist');
                ays_countdown_datetime(countDownEndDate, !showM , quiz_id);
            }
        });

        function toggleFullscreen(elem) {
            elem = elem || document.documentElement;
            if (!document.fullscreenElement && !document.mozFullScreenElement &&
                !document.webkitFullscreenElement && !document.msFullscreenElement) {
                aysQuizFullScreenActivate( elem );
                if (elem.requestFullscreen) {
                    elem.requestFullscreen();
                }else if (elem.msRequestFullscreen) {
                    elem.msRequestFullscreen();
                }else if (elem.mozRequestFullScreen) {
                    elem.mozRequestFullScreen();
                }else if (elem.webkitRequestFullscreen) {
                    elem.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
                }
            }else{
                aysQuizFullScreenDeactivate( elem );
                if(document.exitFullscreen) {
                    document.exitFullscreen();
                }else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                }else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                }
            }
        }

        function aysQuizFullScreenActivate( elem ){
            $(elem).find('.ays-quiz-full-screen-container > .ays-quiz-close-full-screen').css({'display':'block'});
            $(elem).find('.ays-quiz-full-screen-container > .ays-quiz-open-full-screen').css('display','none');
            //$(elem).find('.step:not(:first-of-type,.ays_thank_you_fs)').css({'height':'100vh'});
            $(elem).css({'overflow':'auto'});

            if( $(elem).find('.ays_quiz_reports').length > 0 ){
                $(elem).find('.ays_quiz_reports').css({
                    'position': 'fixed',
                    'z-index': '1',
                });
            }else{
                if( $(elem).find('.ays_quiz_rete_avg').length > 0 ){
                    $(elem).find('.ays_quiz_rete_avg').css({
                        'position': 'fixed',
                        'z-index': '1',
                    });
                }

                if( $(elem).find('.ays_quizn_ancnoxneri_qanak').length > 0 ){
                    $(elem).find('.ays_quizn_ancnoxneri_qanak').css({
                        'position': 'fixed',
                        'z-index': '1',
                    });
                }
            }
        }

        function aysQuizFullScreenDeactivate( elem ){
            $(elem).find('.ays-quiz-full-screen-container > svg.ays-quiz-open-full-screen').css({'display':'block'});
            $(elem).find('.ays-quiz-full-screen-container > svg.ays-quiz-close-full-screen').css('display','none');
            //$(elem).find('.step:not(:first-of-type)').css({'height':'auto'});
            $(elem).css({'overflow':'initial'});

            if( $(elem).find('.ays_quiz_reports').length > 0 ){
                $(elem).find('.ays_quiz_reports').css({
                    'position': 'absolute',
                    'z-index': '1',
                });
            }else{
                if( $(elem).find('.ays_quiz_rete_avg').length > 0 ){
                    $(elem).find('.ays_quiz_rete_avg').css({
                        'position': 'absolute',
                        'z-index': '1',
                    });
                }

                if( $(elem).find('.ays_quizn_ancnoxneri_qanak').length > 0 ){
                    $(elem).find('.ays_quizn_ancnoxneri_qanak').css({
                        'position': 'absolute',
                        'z-index': '1',
                    });
                }
            }
        }

        document.addEventListener('fullscreenchange', function(event) {
            if (!document.fullscreenElement) {
                var eventTarget = event.target
                if( $( eventTarget ).hasClass('ays-quiz-container') ){
                    aysQuizFullScreenDeactivate( eventTarget );
                }
            }
        }, false);

        $(document).find('.ays-quiz-open-full-screen, .ays-quiz-close-full-screen').on('click', function() {
            var quiz_container = $(this).parents('.ays-quiz-container').get(0);
            toggleFullscreen(quiz_container);
        });

        $(document).on('change', '.ays-quiz-res-toggle-checkbox', function(){
            var _this  = $(this);
            var parent = _this.parents('.ays_quiz_results');
            var elements = parent.find('.step.ays_question_result');

            if (_this.prop('checked')) {
                if (  elements.hasClass('ays_display_none') ) {
                    elements.removeClass('ays_display_none');
                }
            }else{
                elements.addClass('ays_display_none');             
            }
        });

    });

})( jQuery );
