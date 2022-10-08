(function ($) {
    'use strict';
    $.fn.serializeFormJSON = function () {
        var o = {},
            a = this.serializeArray();
        $.each(a, function () {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };    
    if (!String.prototype.trim) {
        (function() {
            String.prototype.trim = function() {
                return this.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, '');
            };
        })();
    }
    $(document).ready(function () {
        var current_fs, next_fs, previous_fs; //fieldsets
        var left, opacity, scale; //fieldset properties which we will animate
        var animating; //flag to prevent quick multi-click glitches
        var form, ays_quiz_container, ays_quiz_container_id;
        if(!$.fn.goTo){
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
        }
        if (!String.prototype.trim) {
            (function() {
                String.prototype.trim = function() {
                    return this.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, '');
                };
            })();
        }
        $(document).find(".ays-quiz-container .information_form").each(function(e){
            var $this = $(this);
            var cont = $(document).find(".ays-quiz-container");
            var thisCont = $this.parents('.ays-quiz-container');
            var quizId = thisCont.find('input[name="ays_quiz_id"]').val();
            var myOptions = JSON.parse(window.atob(window.aysQuizOptions[quizId]));
            if(myOptions.autofill_user_data && myOptions.autofill_user_data == "on"){
                var userData = {};
                userData.action = 'ays_get_user_information';
                $.ajax({
                    url: quiz_maker_ajax_public.ajax_url,
                    method: 'post',
                    dataType: 'json',
                    data: userData,
                    success: function (response) {
                        if(response !== null){
                            $this.find("input[name='ays_user_name']").val(response.data.display_name);
                            $this.find("input[name='ays_user_email']").val(response.data.user_email);
                        }
                    }
                });
            }
        });

        $(document).find('.ays-quiz-rate-link-box .ays-quiz-rate-link').on('click', function (e) {
            e.preventDefault();
            var _this  = $(this);
            var parent = _this.parents('.ays-quiz-container');
            var quizId = parent.find('input[name="ays_quiz_id"]').val();
            var form   = parent.find('form');

            var action = 'ays_get_rate_last_reviews';
            $.ajax({
                url: quiz_maker_ajax_public.ajax_url,
                method: 'post',
                dataType: 'json',
                data: {
                    quiz_id: quizId,
                    action: action
                },
                success: function(response){
                    if(response.status === true){
                        form.find('.quiz_rate_reasons_body').html(response.quiz_rate_html);
                        form.find('.lds-spinner2').addClass('lds-spinner2-none').removeClass('lds-spinner2');
                        form.find('.quiz_rate_reasons_container').slideDown(500);

                        _this.slideUp(500);

                        form.find('button.ays_load_more_review').on('click', function(e){
                            form.find('.quiz_rate_load_more [data-role="loader"]').addClass(form.find('.quiz_rate_load_more .ays-loader').data('class')).removeClass('ays-loader');
                            var startFrom = parseInt($(e.target).attr('startfrom'));
                            var zuyga = parseInt($(e.target).attr('zuyga'));
                            $.ajax({
                                url: quiz_maker_ajax_public.ajax_url,
                                method: 'post',
                                data:{
                                    action: 'ays_load_more_reviews',
                                    quiz_id: quizId,
                                    start_from: startFrom,
                                    zuyga: zuyga
                                },
                                success: function(resp){
                                    if(zuyga == 0){
                                        zuyga = 1;
                                    }else{
                                        zuyga = 0;
                                    }
                                    
                                    form.find('.quiz_rate_load_more [data-role="loader"]').addClass('ays-loader').removeClass(form.find('.quiz_rate_load_more .ays-loader').data('class'));
                                    form.find('.quiz_rate_reasons_container').append(resp);
                                    form.find('.quiz_rate_more_review:last-of-type').slideDown(500);
                                    $(e.target).attr('startfrom', startFrom + 5 );
                                    $(e.target).attr('zuyga', zuyga);
                                    if(form.find('.quiz_rate_reasons_container p.ays_no_more').length > 0){
                                        $(e.target).remove();
                                    }
                                }
                            });
                        });
                    } else {
                        swal.fire({
                            type: 'info',
                            html: "<h2>"+ quizLangObj.loadResource +"</h2><br><h6>"+ quizLangObj.somethingWentWrong +"</h6>"
                        });
                    }
                },
                error: function(){
                    swal.fire({
                        type: 'info',
                        html: "<h2>"+ quizLangObj.loadResource +"</h2><br><h6>"+ quizLangObj.somethingWentWrong +"</h6>"
                    });
                }
            });
        });

        $(document).find('input.ays_finish').on('click', function (e) {
            e.preventDefault();
            ays_quiz_container_id = $(this).parents(".ays-quiz-container").attr("id");
            ays_quiz_container = $('#'+ays_quiz_container_id);
            if(ays_quiz_container.find('.ays_music_sound').length !== 0){
                ays_quiz_container.find('.ays_music_sound').fadeOut();
                setTimeout(function() {
                    audioVolumeOut(ays_quiz_container.find('.ays_quiz_music').get(0));
                },4000);
                setTimeout(function() {
                    ays_quiz_container.find('.ays_quiz_music').get(0).pause();
                },6000);
            }
            if(ays_quiz_container.find('audio').length > 0){
                ays_quiz_container.find('audio').each(function(e, el){
                    el.pause();
                });
            }
            if(ays_quiz_container.find('video').length > 0){
                ays_quiz_container.find('video').each(function(e, el){
                    el.pause();
                });
            }
            ays_quiz_container.find('.ays-live-bar-wrap').addClass('bounceOut');            
            setTimeout(function () {
                ays_quiz_container.find('.ays-live-bar-wrap').css('display','none');
            },300);
            var quizId = ays_quiz_container.find('input[name="ays_quiz_id"]').val();
            var myOptions = JSON.parse(window.atob(window.aysQuizOptions[quizId]));
            var quizOptionsName = 'quizOptions_'+quizId;
            var myQuizOptions = [];            
            
            if(typeof window[quizOptionsName] !== 'undefined'){
                for(var i in window[quizOptionsName]){
                    if(window[quizOptionsName].hasOwnProperty(i)){
                         myQuizOptions[i] = (JSON.parse(window.atob(window[quizOptionsName][i])));
                    }
                }
            }

            if( ! $(this).hasClass('ays-quiz-after-timer-end') ){
                var confirm = true;
                myOptions.enable_see_result_confirm_box = ! myOptions.enable_see_result_confirm_box ? 'off' : myOptions.enable_see_result_confirm_box;
                var enable_see_result_confirm_box = (myOptions.enable_see_result_confirm_box && myOptions.enable_see_result_confirm_box == 'on') ? true : false;
                if (enable_see_result_confirm_box) {
                    if ( ! window.aysEarlyFinishConfirmBox[ quizId ] ) {
                        confirm = window.confirm(quizLangObj.areYouSure);
                        window.aysSeeResultConfirmBox[ quizId ] = false;
                    }
                }

                if ( ! confirm && window.aysTimerIntervalFlag == null ) {
                    window.aysSeeResultConfirmBox[ quizId ] = true;
                    return false;
                }
            }

            if($(document).scrollTop() >= $(this).parents('.ays-questions-container').offset().top){
                ays_quiz_container.goTo(myOptions);
            }

            var emailValivatePattern = /^[a-zA-Z0-9\._-]+@[a-zA-Z0-9\._-]+\.\w{2,}$/;

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
                    }
                }else{
                    if($(this).parents('.step').find('.information_form').length !== 0 ){
                        var empty_inputs = 0;
                        var phoneInput = $(this).parents('.step').find('input[name="ays_user_phone"]');
                        var emailInput = $(this).parents('.step').find('input[name="ays_user_email"]');
                        if(phoneInput.val() != ''){
                            phoneInput.removeClass('ays_red_border');
                            phoneInput.removeClass('ays_green_border');
                            if (!validatePhoneNumber(phoneInput.get(0))){
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
                    }
                }
            }

            var next_sibilings_count = $(this).parents('form').find('.ays_question_count_per_page').val();
            $(e.target).parents().eq(3).find('input[name^="ays_questions"]').attr('disabled', false);
            $(e.target).parents().eq(3).find('div.ays-quiz-timer').slideUp(500);
            if($(e.target).parents().eq(3).find('div.ays-quiz-after-timer').hasClass('empty_after_timer_text')){
                $(e.target).parents().eq(3).find('div.ays-quiz-timer').parent().slideUp(500);
            }
            // setTimeout(function () {
            //     $(e.target).parents().eq(3).find('div.ays-quiz-timer').parent().hide();
            // },500);

            next_fs = $(this).parents('.step').next();
            current_fs = $(this).parents('.step');
            next_fs.addClass('active-step');
            current_fs.removeClass('active-step');
            form = ays_quiz_container.find('form');

            if (!($(this).hasClass('start_button')) && window.aysTimerIntervalFlag == null && ! window.aysEarlyFinishConfirmBox[ quizId ]) {
                var minSelHasError = 0;
                var buttonsDiv = current_fs.find('.ays_buttons_div');
                var enableArrows = $(this).parents(".ays-questions-container").find(".ays_qm_enable_arrows").val();
                if( ays_quiz_container.find('.step[data-question-id] .enable_min_selection_number').length > 0 ){
                    ays_quiz_container.find('.step[data-question-id] .enable_min_selection_number').each(function(){
                        var MinSelQuestion = $(this).parents('.step[data-question-id]');
                        var checkedMinSelCount = aysCheckMinimumCountCheckbox( MinSelQuestion, myQuizOptions );
                        if( ays_quiz_is_question_min_count( MinSelQuestion, !checkedMinSelCount ) === true ){
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
                    });
                }
                if( minSelHasError > 0 ){
                    return false;
                }
            }
            
            var textAnswers = form.find('div.ays-text-field textarea.ays-text-input');            
            for(var i=0; i < textAnswers.length; i++){
                var userAnsweredText = textAnswers.eq(i).val().trim();
                var questionId = textAnswers.eq(i).parents('.step').data('questionId');
                
                var trueAnswered = false;

                // Enable case sensitive text
                var enable_case_sensitive_text = (myQuizOptions[questionId].enable_case_sensitive_text && myQuizOptions[questionId].enable_case_sensitive_text != "") ? myQuizOptions[questionId].enable_case_sensitive_text : false;
                
                var thisQuestionCorrectAnswer = myQuizOptions[questionId].question_answer == '' ? "" : myQuizOptions[questionId].question_answer;
                var thisQuestionAnswer = thisQuestionCorrectAnswer.toLowerCase();

                if ( enable_case_sensitive_text ) {
                    thisQuestionAnswer = thisQuestionCorrectAnswer;
                }

                thisQuestionAnswer = thisQuestionAnswer.split('%%%');
                for(var i_answer = 0; i_answer < thisQuestionAnswer.length; i_answer++){
                    if ( enable_case_sensitive_text ) {
                        if(userAnsweredText == thisQuestionAnswer[i_answer].trim()){
                            trueAnswered = true;
                            break;
                        }
                    } else {
                        if(userAnsweredText.toLowerCase() == thisQuestionAnswer[i_answer].trim()){
                            trueAnswered = true;
                            break;
                        }
                    }
                }
                
                if(trueAnswered){
                    textAnswers.eq(i).next().val(1);
                }else{
                    textAnswers.eq(i).next().val(0);
                    if(thisQuestionCorrectAnswer == ''){
                        textAnswers.eq(i).attr('chishtpatasxan', '-');
                    }else{
                        textAnswers.eq(i).attr('chishtpatasxan', thisQuestionCorrectAnswer);
                    }
                }
                textAnswers.eq(i).removeAttr('disabled');
            }
            
            var numberAnswers = form.find('div.ays-text-field input[type="number"].ays-text-input');            
            for(var i=0; i < numberAnswers.length; i++){
                var userAnsweredText = numberAnswers.eq(i).val().trim();
                var questionId = numberAnswers.eq(i).parents('.step').data('questionId');
                if(userAnsweredText.toLowerCase() === myQuizOptions[questionId].question_answer.toLowerCase()){
                    numberAnswers.eq(i).next().val(1);
                }else{
                    numberAnswers.eq(i).next().val(0);
                    numberAnswers.eq(i).attr('chishtpatasxan', myQuizOptions[questionId].question_answer);                    
                }
                numberAnswers.eq(i).removeAttr('disabled')
            }
            
            var shortTextAnswers = form.find('div.ays-text-field input[type="text"].ays-text-input');            
            for(var i=0; i < shortTextAnswers.length; i++){
                var userAnsweredText = shortTextAnswers.eq(i).val().trim();
                var questionId = shortTextAnswers.eq(i).parents('.step').data('questionId');
                
                var trueAnswered = false;

                // Enable case sensitive text
                var enable_case_sensitive_text = (myQuizOptions[questionId].enable_case_sensitive_text && myQuizOptions[questionId].enable_case_sensitive_text != "") ? myQuizOptions[questionId].enable_case_sensitive_text : false;
                
                var thisQuestionCorrectAnswer = myQuizOptions[questionId].question_answer == '' ? "" : myQuizOptions[questionId].question_answer;
                var thisQuestionAnswer = thisQuestionCorrectAnswer.toLowerCase();

                if ( enable_case_sensitive_text ) {
                    thisQuestionAnswer = thisQuestionCorrectAnswer;
                }

                thisQuestionAnswer = thisQuestionAnswer.split('%%%');
                for(var i_answer = 0; i_answer < thisQuestionAnswer.length; i_answer++){
                    if ( enable_case_sensitive_text ) {
                        if(userAnsweredText == thisQuestionAnswer[i_answer].trim()){
                            trueAnswered = true;
                            break;
                        }
                    } else {
                        if(userAnsweredText.toLowerCase() == thisQuestionAnswer[i_answer].trim()){
                            trueAnswered = true;
                            break;
                        }
                    }
                }
                
                if(trueAnswered){
                    shortTextAnswers.eq(i).next().val(1);
                }else{
                    shortTextAnswers.eq(i).next().val(0);
                    if(thisQuestionCorrectAnswer == ''){
                        shortTextAnswers.eq(i).attr('chishtpatasxan', '-');
                    }else{
                        shortTextAnswers.eq(i).attr('chishtpatasxan', thisQuestionCorrectAnswer);
                    }
                }
                
                shortTextAnswers.eq(i).removeAttr('disabled')
            }
            
            var dateAnswers = form.find('div.ays-text-field input[type="date"].ays-text-input');
            for(var i=0; i < dateAnswers.length; i++){
                var userAnsweredText = dateAnswers.eq(i).val();
                var questionId = dateAnswers.eq(i).parents('.step').data('questionId');
                var thisQuestionCorrectAnswer = myQuizOptions[questionId].question_answer == '' ? "" : myQuizOptions[questionId].question_answer;
                
                var trueAnswered = false;
                var correctDate = new Date(thisQuestionCorrectAnswer),
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
                
                if(trueAnswered){
                    dateAnswers.eq(i).next().val(1);
                }else{
                    dateAnswers.eq(i).next().val(0);
                    if(thisQuestionCorrectAnswer == ''){
                        dateAnswers.eq(i).attr('chishtpatasxan', '-');
                    }else{
                        dateAnswers.eq(i).attr('chishtpatasxan', thisQuestionCorrectAnswer);
                    }
                }
                
                dateAnswers.eq(i).removeAttr('disabled')
            }
            
            var data = form.serializeFormJSON();

            var questionsIds = data.ays_quiz_questions.split(',');
            for(var i = 0; i < questionsIds.length; i++){
                if(! isNaN(parseInt(questionsIds[i]))){
                    if(! data['ays_questions[ays-question-'+questionsIds[i]+']']){
                        data['ays_questions[ays-question-'+questionsIds[i]+']'] = "";
                    }
                }
            }

            data.action = 'ays_finish_quiz';
            data.end_date = GetFullDateTime();
            
            var aysQuizLoader = form.find('div[data-role="loader"]');
            aysQuizLoader.addClass(aysQuizLoader.data('class'));
            aysQuizLoader.removeClass('ays-loader');

            var animationOptions = {
                scale: scale,
                left: left,
                opacity: opacity,
                animating: animating
            }
            
            setTimeout(function () {
                sendQuizData(data, form, myOptions, animationOptions, $(e.target));
            },2000);

            if (parseInt(next_sibilings_count) > 0 && ($(this).parents('.step').attr('data-question-id') || $(this).parents('.step').next().attr('data-question-id'))) {
                current_fs = $(this).parents('form').find('div[data-question-id]');
            }
            
            // aysAnimateStep(ays_quiz_container.data('questEffect'), current_fs, next_fs);
        });

        $(document).find('.ays_next.start_button').on('click',function(e){
            var $this    = $(this);
            var thisCont = $this.parents('.ays-quiz-container');
            var quizId   = thisCont.find('input[name="ays_quiz_id"]').val();

            if ( window.aysQuizOptions[quizId] ) {
                var myOptions = JSON.parse(window.atob(window.aysQuizOptions[quizId]));
                
                if(myOptions.enable_password && myOptions.enable_password == 'on'){
                    var checkQuizGeneratedPassword = checkQuizPassword(e, myOptions, false);
                }
            }
        });
    });
    
    function sendQuizData(data, form, myOptions, options, element){
        if(typeof sendQuizData.counter == 'undefined'){
            sendQuizData.counter = 0;
        }
        if(window.navigator.onLine){
            sendQuizData.counter++;
            $.ajax({
                url: window.quiz_maker_ajax_public.ajax_url,
                method: 'post',
                dataType: 'json',
                data: data,
                success: function(response){
                    if(response.status === true){
                        doQuizResult(response, form, myOptions);
                    }else if( response.status === false && typeof response.flag !== 'undefined' && response.flag === false ){
                        var aysQuizContainer = element.parents('.ays-quiz-container');
                        var lastPageContent = '';

                        lastPageContent += '<p>';
                            lastPageContent += response.text;
                        lastPageContent += '</p>';

                        aysQuizContainer.find('.ays_thank_you_fs').html( lastPageContent );
                    }else{
                        if(sendQuizData.counter >= 5){
                            swal.fire({
                                type: 'error',
                                html: "Sorry.<br>We are unable to store your data."
                            });
                            goQuizFinishPage(form, options, element, myOptions);
                        }else{
                            if(window.navigator.onLine){
                                setTimeout(function(){
                                    sendQuizData(data, form, myOptions, options, element);
                                },3000);
                            }else{
                                sendQuizData(data, form, myOptions, options, element);
                            }
                        }
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    if(sendQuizData.counter >= 5){
                        swal.fire({
                            type: 'error',
                            html: "Sorry.<br>We are unable to store your data."
                        });
                        goQuizFinishPage(form, options, element, myOptions);
                    }else{
                        setTimeout(function(){
                            sendQuizData(data, form, myOptions, options, element);
                        },3000);
                    }
                }
            });
        }else{
            swal.fire({
                type: 'warning',
                html: "Connection is lost.<br>Please check your connection and try again."
            });
            sendQuizData.counter = 0;
            goQuizFinishPage(form, options, element, myOptions);
            var aysQuizContainer = element.parents('.ays-quiz-container');
            aysQuizContainer.find('.step').hide();
            aysQuizContainer.find('.ays_thank_you_fs').prev().removeAttr('style').css({
                'display':'flex',
                'position':'static',
                'transform':'scale(1)',
                'opacity': 1,
                'pointer-events': 'auto'
            });
            var show_result_button = element.parents('form').find('div[data-question-id] input[name="ays_finish_quiz"]');
            if (show_result_button.hasClass('ays_display_none')) {
                show_result_button.removeClass('ays_display_none');
            }
        }
    }
    
    function goQuizFinishPage(form, options, element, myOptions){        
        var currentFS = form.find('.step.active-step');        
        var next_sibilings_count = form.find('.ays_question_count_per_page').val();
        if (parseInt(next_sibilings_count) > 0 &&
            (element.parents('.step').attr('data-question-id') ||
             element.parents('.step').next().attr('data-question-id'))) {
            currentFS = form.find('div[data-question-id]');
        }
        currentFS.prev().css('display', 'flex');
        currentFS.animate({opacity: 0}, {
            step: function(now, mx) {
                options.scale = 1 - (1 - now) * 0.2;
                options.left = (now * 50)+"%";
                options.opacity = 1 - now;
                currentFS.css({
                    'transform': 'scale('+options.scale+')',
                    'position': '',
                    'pointer-events': 'none'
                });
                currentFS.prev().css({
                    'left': options.left,
                    'opacity': options.opacity,
                    'pointer-events': 'none'
                });
            },
            duration: 800,
            complete: function(){
                currentFS.hide();
                currentFS.css({
                    'pointer-events': 'auto',
                    'opacity': '1',
                });
                currentFS.prev().css({
                    'transform': 'scale(1)',
                    'position': 'relative',
                    'opacity': '1',
                    'pointer-events': 'auto'
                });
                options.animating = false;
            },
            easing: 'easeInOutBack'
        });
        if(myOptions.enable_correction == 'on'){
            if(currentFS.prev().find('input:checked').length > 0){
                currentFS.prev().find('.ays-field input').attr('disabled', 'disabled');
                currentFS.prev().find('.ays-field input').on('click', function(){
                    return false;
                });
                currentFS.prev().find('.ays-field input').on('change', function(){
                    return false;
                });
            }
            if(currentFS.prev().find('option:checked').length > 0){
                currentFS.prev().find('.ays-field select').attr('disabled', 'disabled');
                currentFS.prev().find('.ays-field select').on('click', function(){
                    return false;
                });
                currentFS.prev().find('.ays-field select').on('change', function(){
                    return false;
                });
            }
            if(currentFS.prev().find('textarea').length > 0){
                if(currentFS.prev().find('textarea').val() !== ''){
                    currentFS.prev().find('.ays-field textarea').attr('disabled', 'disabled');
                    currentFS.prev().find('.ays-field textarea').on('click', function(){
                        return false;
                    });
                    currentFS.prev().find('.ays-field textarea').on('change', function(){
                        return false;
                    });
                }
            }
        }
    }
    
    function doQuizResult(response, form, myOptions){
		var hideQuizBGImage = form.parents('.ays-quiz-container').data('hideBgImage');
		var QuizBGGragient = form.parents('.ays-quiz-container').data('bgGradient');
        var quizId = form.parents('.ays-quiz-container').find('input[name="ays_quiz_id"]').val();
		if(hideQuizBGImage){
			form.parents('.ays-quiz-container').css('background-image', 'none');
			if(typeof QuizBGGragient != 'undefined'){
				form.parents('.ays-quiz-container').css('background-image', QuizBGGragient);
			}
		}
        form.find('div.ays_message').css('display', 'none');
        form.find('.ays_average').css({'display': 'block'});
        var quizScore = '';
        switch(response.displayScore){
            case 'by_percantage':
                quizScore = parseInt(response.score);
            break;
            case 'by_correctness':
                quizScore = response.score.split('/');
            break;
        }
        if (response.hide_result) {
            form.find('div.ays_message').html(response.text);
        }
        else {
            form.find('div.ays_message').html(response.text);
            form.find('p.ays_score').removeClass('ays_score_display_none');
            form.find('p.ays_score').html(form.find('p.ays_score').text()+'<span class="ays_score_percent animated"> ' + response.score + '</span>');
        }
        form.find('div.ays_message').fadeIn(500);
        setTimeout(function () {
            form.find('p.ays_score').addClass('tada');
        }, 500);
        var numberOfPercent = 0;
        var percentAnimate = setInterval(function(){
            if(typeof quizScore == 'number'){
                form.find('.ays-progress-value').text(numberOfPercent + "%");
                if(numberOfPercent == quizScore){
                    clearInterval(percentAnimate);
                }
                numberOfPercent++;
            }else{
                var total = quizScore[1];
                var count = quizScore[0];
                total = parseInt(total.trim());
                count = parseInt(count.trim());
                form.find('.ays-progress-value').text(numberOfPercent + " / " + total);
                if(numberOfPercent == count){
                    clearInterval(percentAnimate);
                }
                numberOfPercent++;
            }
        }, 20);
        var score = quizScore;
        if(response.displayScore == 'by_correctness'){
            var total = parseInt(quizScore[1].trim());
            var count = parseInt(quizScore[0].trim());
            score = (count / total) * 100;
        }
        if(response.scoreMessage){
            form.find('div.ays_score_message').html(response.scoreMessage);
        }

        var last_result_id = null;
        if(response.result_id && response.result_id != ''){
            last_result_id = parseInt( response.result_id );
        }

        // Make responses anonymous
        myOptions.quiz_make_responses_anonymous = ( myOptions.quiz_make_responses_anonymous ) ? myOptions.quiz_make_responses_anonymous : 'off';
        var quiz_make_responses_anonymous = (myOptions.quiz_make_responses_anonymous && myOptions.quiz_make_responses_anonymous == "on") ? true : false;

        if(score > 0){
            form.find('.ays-progress-bar').css('padding-right', '7px');
            var progressBarStyle = myOptions.progress_bar_style ? myOptions.progress_bar_style : 'first';
            if(progressBarStyle == 'first' || progressBarStyle == 'second'){
                form.find('.ays-progress-value').css('width', 0);
                form.find('.ays-progress-value').css('transition', 'width ' + score*25 + 'ms linear');
                setTimeout(function(){
                    form.find('.ays-progress-value').css('width', score+'%');
                }, 1);
            }
            form.find('.ays-progress-bar').css('transition', 'width ' + score*25 + 'ms linear');
            setTimeout(function(){
                form.find('.ays-progress-bar').css('width', score+'%');
            }, 1);
        }

        if ( score == 0 ) {
            // Quiz background Color
            var quiz_make_bg_color = (myOptions.bg_color && myOptions.bg_color != "") ? myOptions.bg_color : '#fff';

            form.find('.ays-progress-value').css('color', quiz_make_bg_color);
        }

        form.append($("<div class='ays_quiz_results'></div>"));
        var formResults = form.find('.ays_quiz_results');
        formResults.css('padding-bottom', '20px');
        if (form.hasClass('enable_questions_result')) {

            // Enable the Show/Hide toggle
            myOptions.quiz_enable_results_toggle = ! myOptions.quiz_enable_results_toggle ? 'off' : myOptions.quiz_enable_results_toggle;
            var quiz_enable_results_toggle = (myOptions.quiz_enable_results_toggle && myOptions.quiz_enable_results_toggle == 'on') ? true : false;

            var resultToggleHTML = "";

            resultToggleHTML += '<div class="ays-quiz-results-toggle-block">';
                resultToggleHTML += '<span class="ays-show-res-toggle ays-res-toggle-show">'+ quizLangObj.show +'</span>';
                resultToggleHTML += '<input type="checkbox" class="ays_toggle ays_toggle_slide ays-quiz-res-toggle-checkbox" id="ays-quiz-show-results-toggle-'+ quizId +'" checked>';
                resultToggleHTML += '<label for="ays-quiz-show-results-toggle-'+ quizId +'" class="ays_switch_toggle">Toggle</label>';
                resultToggleHTML += '<span class="ays-show-res-toggle ays-res-toggle-hide quest-toggle-failed">'+ quizLangObj.hide +'</span>';
            resultToggleHTML += '</div>';

            if ( quiz_enable_results_toggle ) {
                formResults.append(resultToggleHTML);
            }

            var questions = form.find('div[data-question-id]');
            var answerIsRightArr = new Array();
            for (var z = 0; z < questions.length; z++) {
                
                if(questions.eq(z).hasClass('not_influence_to_score')){
                    continue;
                }
                var question = questions.eq(z).clone(true, true);
                var questionId = question.attr('data-question-id');
                var question_original_html = questions.eq(z).find('.ays_quiz_question');

                var ays_quiz_question_html    = questions.eq(z).find('.ays_quiz_question');
                var question_explanation_html = questions.eq(z).find('.ays_questtion_explanation');
                var wrong_answer_text_html    = questions.eq(z).find('.wrong_answer_text');
                var right_answer_text_html    = questions.eq(z).find('.right_answer_text');
                var note_message_box_html     = questions.eq(z).find('.ays-quiz-question-note-message-box');

                var question_parts_arr = new Array(
                    note_message_box_html,
                    question_explanation_html,
                    wrong_answer_text_html,
                    right_answer_text_html,
                );

                question.find('.ays_quiz_question').remove();
                question.find('.ays-abs-fs').prepend( questions.eq(z).find('.ays_quiz_question') );

                question.find('.ays_questtion_explanation').remove();
                question.find('.ays-abs-fs').append( questions.eq(z).find('.ays_questtion_explanation') );

                question.find('.wrong_answer_text').remove();
                question.find('.ays-abs-fs').append( questions.eq(z).find('.wrong_answer_text') );

                question.find('.right_answer_text').remove();
                question.find('.ays-abs-fs').append( questions.eq(z).find('.right_answer_text') );

                question.find('input[type="button"]').remove();
                question.find('input[type="submit"]').remove();
                question.find('.ays_arrow').remove();

                question.find('.ays-quiz-category-description-box').addClass('ays_display_none');

                question.addClass('ays_question_result');
                var checked_inputs = question.find('input:checked');
                var text_answer = question.find('textarea.ays-text-input');
                var number_answer = question.find('input[type="number"].ays-text-input');
                var short_text_answer = question.find('input[type="text"].ays-text-input');
                var date_answer = question.find('input[type="date"].ays-text-input');
                var selected_options = question.find('select');
                var answerIsRight = false;  

                var fieldset_html = "<fieldset class='ays_fieldset'>" + "<legend>" + quizLangObj.notAnsweredText + "</legend>" + "</fieldset>";
                var question_html = question.find('.ays-abs-fs > *:not(.ays_quiz_question)').clone(true, true);
                
                question.find('input[name="ays_answer_correct[]"][value="1"]').parent().find('label').addClass('correct answered');
                question.find('input[name="ays_answer_correct[]"][value="1"]').parents('div.ays-field').addClass('correct_div');
                
                if(checked_inputs.length === 0){
                    var emptyAnswer = false;
                    if(question.find('input[type="radio"]').length !== 0 ||
                       question.find('input[type="checkbox"]').length !== 0){
                        emptyAnswer = true;
                    }
                    if(emptyAnswer){

                        var q_answer_text_html = question.find('.ays-abs-fs .ays-quiz-answers');
                        question.find('.ays-abs-fs').html(fieldset_html);
                        question.find('.ays-abs-fs .ays_fieldset').append(ays_quiz_question_html);
                        question.find('.ays-abs-fs .ays_fieldset').append(q_answer_text_html );

                        for (var i = 0; i < question_parts_arr.length; i++) {
                            question.find('.ays-abs-fs .ays_fieldset').append( question_parts_arr[i] );
                        }

                        question.find('.ays-abs-fs').css({
                            'padding': '7px',
                            'width': '100%'
                        });
                    }
                }

                var aysAudio = $(document).find('.ays_question_result audio');
                if(aysAudio.length > 0){
                    aysAudio.each(function(e, el){
                        el.pause();
                    });
                }

                selected_options.each(function(element, item){
                    var selectOptions = $(item).children("option[data-chisht]");
                    var answerClass, answerDivClass, attrChecked, answerClassForSelected, answerClass_tpel, answerViewClass, attrCheckedStyle = "";
                    var correctAnswersDiv = '', rectAnswerBefore = "";
                    
                    
                    answerViewClass = form.find('.answer_view_class').val();
                    answerViewClass = "ays_"+form.find('.answer_view_class').val()+"_view_item";
                    for(var j = 0; j < selectOptions.length; j++){
                        if($(selectOptions[j]).attr("value") == '' ||
                           $(selectOptions[j]).attr("value") == undefined ||
                           $(selectOptions[j]).attr("value") == null){
                            continue;
                        }
                        if($(selectOptions[j]).prop('selected') == true){
                            if(parseInt($(selectOptions[j]).data("chisht")) === 1){
                                answerClassForSelected = " correct answered";
                                answerDivClass = "correct_div";
                                attrChecked = "checked='checked'";
                                answerIsRight = true;
                            }else{
                                answerClassForSelected = " wrong wrong_div answered";
                                attrChecked = "checked='checked'";
                                answerDivClass = "";
                            }
                        }else{
                            if(parseInt($(selectOptions[j]).data("chisht")) === 1){
                                answerClassForSelected = " correct answered";
                                answerDivClass = "correct_div";
                                attrChecked = "";
                            }else{
                                answerClassForSelected = "";
                                attrChecked = "";
                                answerDivClass = "";
                            }
                        }
                        if(form.parents('.ays-quiz-container').hasClass('ays_quiz_elegant_dark') ||
                           form.parents('.ays-quiz-container').hasClass('ays_quiz_elegant_light')){
                            if($(selectOptions[j]).prop('selected') == true){
                                if(parseInt($(selectOptions[j]).data("chisht")) === 1){
                                    answerDivClass = "correct_div checked_answer_div";
                                    attrCheckedStyle = "style='padding: 0!important;'";
                                }else{
                                    answerDivClass = "wrong_div checked_answer_div";
                                    attrCheckedStyle = "style='padding: 0!important;'";
                                }
                            }else{
                                if(parseInt($(selectOptions[j]).data("chisht")) === 1){
                                    answerDivClass = "correct_div";
                                }else{
                                    answerDivClass = "";
                                }
                                attrCheckedStyle = "";
                            }
                        }
                        if(form.parents('.ays-quiz-container').hasClass('ays_quiz_rect_dark') ||
                           form.parents('.ays-quiz-container').hasClass('ays_quiz_rect_light')){
                            if($(selectOptions[j]).prop('selected') == true){
                                if(parseInt($(selectOptions[j]).data("chisht")) === 1){
                                    answerDivClass = "correct_div checked_answer_div";
                                }else{
                                    answerDivClass = "wrong_div checked_answer_div";
                                }
                                rectAnswerBefore = "rect_answer_correct_before";
                            }else{
                                if(parseInt($(selectOptions[j]).data("chisht")) === 1){
                                    answerDivClass = "correct_div";
                                }else{
                                    answerDivClass = "";
                                }
                                rectAnswerBefore = "rect_answer_wrong_before";
                            }
                        }
                        correctAnswersDiv += '<div class="ays-field '+answerViewClass+' '+answerDivClass+'" '+attrCheckedStyle+'>'+
                                '<input type="radio" value="'+$(selectOptions[j]).attr("value")+'" name="'+$(item).parent().find('.ays-select-field-value').attr('name')+'" disabled="disabled" '+attrChecked+'>'+
                                '<label class="'+rectAnswerBefore+" "+answerClassForSelected+'" for="ays-answer-'+$(selectOptions[j]).attr("value")+'">'+aysEscapeHtml($(selectOptions[j]).text())+'</label>'+
                            '</div>';
                    }
                    $(item).parent().parent().find('.ays-text-right-answer').remove();
                    $(item).parent().parent().append(correctAnswersDiv);
                    $(item).parent().hide();
                    if($(item).find('option[data-chisht]:selected').length === 0){
                        var _parent_item = $(item).parents('.ays-abs-fs');

                        _parent_item.html(fieldset_html);
                        _parent_item.find('.ays_fieldset').append(ays_quiz_question_html);
                        _parent_item.find('.ays_fieldset').append($(item).parents('.ays-quiz-answers'));

                        for (var i = 0; i < question_parts_arr.length; i++) {
                            _parent_item.find('.ays_fieldset').append( question_parts_arr[i] );
                        }
                        
                        $(item).css({
                            'padding': '7px'
                        })
                    }
                    $(item).parents('.ays-abs-fs').find('.ays_buttons_div').remove();
                    $(item).parent().remove();
                });

                text_answer.next().next().remove();
                text_answer.css('width', '100%');
                text_answer.attr('disabled', 'disabled');
                number_answer.next().next().remove();
                number_answer.css('width', '100%');
                number_answer.attr('disabled', 'disabled');
                short_text_answer.next().next().remove();
                short_text_answer.css('width', '100%');
                short_text_answer.attr('disabled', 'disabled');
                date_answer.next().next().remove();
                date_answer.css('width', '100%');
                date_answer.attr('disabled', 'disabled');
                if(text_answer.val() == ''){
                    var rightAnswerText = '<div class="ays-text-right-answer">';
                    var thisQuestionAnswer = text_answer.attr('chishtpatasxan');
                    if(typeof thisQuestionAnswer != 'undefined'){
                        thisQuestionAnswer = thisQuestionAnswer.split('%%%');
                    }else{
                        thisQuestionAnswer = [''];
                    }
                    rightAnswerText += thisQuestionAnswer[0].trim();
                    
                    rightAnswerText += '</div>';
                    if(text_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').length == 0){
                        text_answer.parents('.ays-quiz-answers').append(rightAnswerText);
                    }
                    text_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').css({
                        'display': 'block'
                    });
                    text_answer.css('background-color', 'rgba(243,134,129,0.4)');

                    var _text_answer_parent = text_answer.parents('.ays-abs-fs');

                    _text_answer_parent.html(fieldset_html);
                    _text_answer_parent.find('.ays_fieldset').append(ays_quiz_question_html);
                    _text_answer_parent.find('.ays_fieldset').append(text_answer.parents('.ays-quiz-answers'));

                    for (var i = 0; i < question_parts_arr.length; i++) {
                        _text_answer_parent.find('.ays_fieldset').append( question_parts_arr[i] );
                    }
                    text_answer.parents('.ays-abs-fs').css({
                        'padding': '7px'
                    });
                }else{
                    if(parseInt(text_answer.next().val()) == 1){
                        text_answer.css('background-color', 'rgba(39,174,96,0.5)');
                        answerIsRight = true;
                    }else{
                        text_answer.css('background-color', 'rgba(243,134,129,0.4)');
                        var rightAnswerText = '<div class="ays-text-right-answer">';
                        var thisQuestionAnswer = text_answer.attr('chishtpatasxan');
                        if(typeof thisQuestionAnswer != 'undefined'){
                            thisQuestionAnswer = thisQuestionAnswer.split('%%%');
                        }else{
                            thisQuestionAnswer = [''];
                        }
                        rightAnswerText += thisQuestionAnswer[0].trim();

                        rightAnswerText += '</div>';                            
                        if(text_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').length == 0){
                            text_answer.parents('.ays-quiz-answers').append(rightAnswerText);
                        }
                        text_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').css({
                            'display': 'block'
                        });
                        text_answer.parents('.ays-abs-fs').find('.ays_quiz_question_text_conteiner').addClass('ays_display_none');
                    }
                }
                if(number_answer.val() == ''){
                    var rightAnswerText = '<div class="ays-text-right-answer">'+
                        number_answer.attr('chishtpatasxan')+
                    '</div>';
                    if(number_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').length == 0){
                        number_answer.parents('.ays-quiz-answers').append(rightAnswerText);
                    }
                    number_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').css({
                        'display': 'block'
                    });
                    number_answer.css('background-color', 'rgba(243,134,129,0.8)');
                    number_answer.parents('.ays-abs-fs').find('.ays-quiz-number-error-message').addClass('ays_display_none');

                    var _number_answer_parent = number_answer.parents('.ays-abs-fs');

                    _number_answer_parent.html(fieldset_html);
                    _number_answer_parent.find('.ays_fieldset').append(ays_quiz_question_html);
                    _number_answer_parent.find('.ays_fieldset').append(number_answer.parents('.ays-quiz-answers'));
                    
                    for (var i = 0; i < question_parts_arr.length; i++) {
                        _number_answer_parent.find('.ays_fieldset').append( question_parts_arr[i] );
                    }

                    number_answer.parents('.ays-abs-fs').css({
                        'padding': '7px'
                    });
                }else{
                    if(parseInt(number_answer.next().val()) == 1){
                        number_answer.css('background-color', 'rgba(39,174,96,0.5)');
                        answerIsRight = true;
                    }else{
                        number_answer.css('background-color', 'rgba(243,134,129,0.4)');
                        var rightAnswerText = '<div class="ays-text-right-answer">'+
                            number_answer.attr('chishtpatasxan')+
                            '</div>';
                        if(number_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').length == 0){
                            number_answer.parents('.ays-quiz-answers').append(rightAnswerText);
                        }
                        number_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').css({
                            'display': 'block'
                        });
                        number_answer.parents('.ays-abs-fs').find('.ays-quiz-number-error-message').addClass('ays_display_none');
                    }
                }
                if(short_text_answer.val() == ''){
                    var rightAnswerText = '<div class="ays-text-right-answer">';
                    var thisQuestionAnswer = short_text_answer.attr('chishtpatasxan');
                    if(typeof thisQuestionAnswer != 'undefined'){
                        thisQuestionAnswer = thisQuestionAnswer.split('%%%');
                    }else{
                        thisQuestionAnswer = [''];
                    }
                    rightAnswerText += thisQuestionAnswer[0].trim();
                    
                    rightAnswerText += '</div>';
                    if(short_text_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').length == 0){
                        short_text_answer.parents('.ays-quiz-answers').append(rightAnswerText);
                    }
                    short_text_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').css({
                        'display': 'block'
                    });
                    short_text_answer.css('background-color', 'rgba(243,134,129,0.8)');
                    short_text_answer.parents('.ays-abs-fs').find('.ays_quiz_question_text_conteiner').addClass('ays_display_none');

                    var _short_text_parent = short_text_answer.parents('.ays-abs-fs');

                    _short_text_parent.html(fieldset_html);
                    _short_text_parent.find('.ays_fieldset').append(ays_quiz_question_html);
                    _short_text_parent.find('.ays_fieldset').append(short_text_answer.parents('.ays-quiz-answers'));
                    
                    for (var i = 0; i < question_parts_arr.length; i++) {
                        _short_text_parent.find('.ays_fieldset').append( question_parts_arr[i] );
                    }

                    short_text_answer.parents('.ays-abs-fs').css({
                        'padding': '7px'
                    });
                }else{
                    if(parseInt(short_text_answer.next().val()) == 1){
                        short_text_answer.css('background-color', 'rgba(39,174,96,0.5)');
                        answerIsRight = true;
                    }else{
                        short_text_answer.css('background-color', 'rgba(243,134,129,0.4)');
                        var rightAnswerText = '<div class="ays-text-right-answer">';
                        var thisQuestionAnswer = short_text_answer.attr('chishtpatasxan');
                        if(typeof thisQuestionAnswer != 'undefined'){
                            thisQuestionAnswer = thisQuestionAnswer.split('%%%');
                        }else{
                            thisQuestionAnswer = [''];
                        }
                        rightAnswerText += thisQuestionAnswer[0].trim();
                        
                        rightAnswerText += '</div>';
                        if(short_text_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').length == 0){
                            short_text_answer.parents('.ays-quiz-answers').append(rightAnswerText);
                        }
                        short_text_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').css({
                            'display': 'block'
                        });
                        short_text_answer.parents('.ays-abs-fs').find('.ays_quiz_question_text_conteiner').addClass('ays_display_none');
                    }
                }
                if(date_answer.val() == ''){
                    var rightAnswerText = '<div class="ays-text-right-answer">';
                    var thisQuestionAnswer = date_answer.attr('chishtpatasxan');

                    var correctDate = new Date(thisQuestionAnswer),
                        correctDateYear = correctDate.getFullYear(),
                        correctDateMonth = (correctDate.getMonth() + 1) < 10 ? "0"+(correctDate.getMonth() + 1) : (correctDate.getMonth() + 1),
                        correctDateDay = (correctDate.getDate() < 10) ? "0"+correctDate.getDate() : correctDate.getDate();
                    rightAnswerText += [correctDateMonth, correctDateDay, correctDateYear].join('/');
                    
                    rightAnswerText += '</div>';
                    if(date_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').length == 0){
                        date_answer.parents('.ays-quiz-answers').append(rightAnswerText);
                    }
                    date_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').css({
                        'display': 'block'
                    });
                    date_answer.css('background-color', 'rgba(243,134,129,0.8)');

                    var _date_answer_parent = date_answer.parents('.ays-abs-fs');

                    _date_answer_parent.html(fieldset_html);
                    _date_answer_parent.find('.ays_fieldset').append(ays_quiz_question_html);
                    _date_answer_parent.find('.ays_fieldset').append(date_answer.parents('.ays-quiz-answers'));
                    
                    for (var i = 0; i < question_parts_arr.length; i++) {
                        _date_answer_parent.find('.ays_fieldset').append( question_parts_arr[i] );
                    }

                    date_answer.parents('.ays-abs-fs').css({
                        'padding': '7px'
                    });
                }else{
                    if(parseInt(date_answer.next().val()) == 1){
                        date_answer.css('background-color', 'rgba(39,174,96,0.5)');
                        answerIsRight = true;
                    }else{
                        date_answer.css('background-color', 'rgba(243,134,129,0.4)');
                        var rightAnswerText = '<div class="ays-text-right-answer">';
                        var thisQuestionAnswer = date_answer.attr('chishtpatasxan');
                        var correctDate = new Date(thisQuestionAnswer),
                            correctDateYear = correctDate.getFullYear(),
                            correctDateMonth = (correctDate.getMonth() + 1) < 10 ? "0"+(correctDate.getMonth() + 1) : (correctDate.getMonth() + 1),
                            correctDateDay = (correctDate.getDate() < 10) ? "0"+correctDate.getDate() : correctDate.getDate();
                        rightAnswerText += [correctDateMonth, correctDateDay, correctDateYear].join('/');
                        
                        rightAnswerText += '</div>';
                        if(date_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').length == 0){
                            date_answer.parents('.ays-quiz-answers').append(rightAnswerText);
                        }
                        date_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').css({
                            'display': 'block'
                        });
                    }
                }

                checked_inputs.each(function (element, item) {
                    if (checked_inputs.length === 1) {
                        if(parseInt(checked_inputs.prev().val()) === 1){
                            checked_inputs.next().addClass('correct answered');
                            answerIsRight = true;
                        }else{
                            checked_inputs.next().addClass('wrong wrong_div answered');
                        }
                        $( checked_inputs).parents().eq(3).find('input[name^="ays_questions"]').attr('disabled', true);

                    }else if(checked_inputs.length > 1){
                        var checked_right = 0;
                        checked_inputs.map(function() {
                            if(parseInt($(this).prev().val()) === 1){
                                $(this).next().addClass('correct answered');
                            } else {
                                $(this).next().addClass('wrong wrong_div answered');
                                checked_right++;
                            }
                            $(this).parents().eq(3).find('input[name^="ays_questions"]').attr('disabled', true);
                        });
                        if(checked_right == 0){
                            answerIsRight = true;
                        }
                    }

                });

                myOptions.hide_correct_answers = (myOptions.hide_correct_answers) ? myOptions.hide_correct_answers : 'off';
                var hideRightAnswers =(myOptions.hide_correct_answers && myOptions.hide_correct_answers == 'on') ? true : false;
                if (hideRightAnswers) {
                    question.find('.ays-text-right-answer').addClass("ays_quiz_display_none_important");
                    var aysFieldsets = question.find('fieldset.ays_fieldset');
                    if (aysFieldsets.length > 0) {
                        var aysFieldsetsField = aysFieldsets.find('.ays-field');
                        if (aysFieldsetsField.hasClass('correct_div')) {
                            aysFieldsetsField.removeClass('correct_div');
                        }
                        if (aysFieldsetsField.hasClass('checked_answer_div')) {
                            aysFieldsetsField.removeClass('checked_answer_div');
                        }
                        if (aysFieldsetsField.find('label').hasClass('correct')) {
                            aysFieldsetsField.find('label').removeClass('correct');
                        }
                    }
                    var answers_box = question.find('.ays-quiz-answers');
                    if (answers_box.length > 0) {
                        answers_box.each(function () {
                           var userWrongAnswered = $(this).find('.ays-field');
                           var userWrongAnsweredLabel = userWrongAnswered.find('label');
                           var questionTypeCheckbox = userWrongAnswered.find('input[type="checkbox"]');
                           if (userWrongAnsweredLabel.hasClass('wrong') || questionTypeCheckbox.length > 0) {
                                if (userWrongAnsweredLabel.hasClass('correct') && userWrongAnsweredLabel.hasClass('answered')) {
                                    var eachAnswers = userWrongAnswered.find('input[name^="ays_questions"]');
                                    if (eachAnswers.length > 0) {
                                        eachAnswers.each(function () {
                                            var parentBox = $(this).parents('.ays-field');
                                            if (! $(this).prop("checked")) {
                                                $(this).next().removeClass('correct');
                                                if (parentBox.hasClass('correct_div')) {
                                                    parentBox.removeClass('correct_div');
                                                }
                                                if (parentBox.hasClass('checked_answer_div')) {
                                                    parentBox.removeClass('checked_answer_div');
                                                }
                                            }
                                        });
                                    }
                                }
                           }
                        });
                    }
                }

                answerIsRightArr[ 'questionId_' + questionId ] = answerIsRight;

                if(myOptions.answers_rw_texts && (myOptions.answers_rw_texts == 'on_results_page' || myOptions.answers_rw_texts == 'on_both')){
                    if(answerIsRight){
                        question.find('.right_answer_text').css("display", "block");
                    }else{
                        question.find('.wrong_answer_text').css("display", "block");
                    }
                }else{
                    question.find('.right_answer_text').css("display", "none");
                    question.find('.wrong_answer_text').css("display", "none");
                }
                var showExplanationOn = (myOptions.show_questions_explanation && myOptions.show_questions_explanation != "") ? myOptions.show_questions_explanation : "on_results_page";
                if(showExplanationOn == 'on_results_page' || showExplanationOn == 'on_both'){
                    if(! question.hasClass('not_influence_to_score')){
                        question.find('.ays_questtion_explanation').css("display", "block");
                    }
                }else{
                    question.find('.ays_questtion_explanation').css("display", "none");
                }
                
                question.find('.ays_user_explanation').css("display", "none");
                question.css("pointer-events", "auto");
                question.find('.ays-quiz-answers').css("pointer-events", "none");
                formResults.append(question);
            }

            myOptions.quiz_show_wrong_answers_first = ! myOptions.quiz_show_wrong_answers_first ? 'off' : myOptions.quiz_show_wrong_answers_first;
            var quiz_show_wrong_answers_first = (myOptions.quiz_show_wrong_answers_first && myOptions.quiz_show_wrong_answers_first == 'on') ? true : false;

            myOptions.quiz_show_only_wrong_answers = ! myOptions.quiz_show_only_wrong_answers ? 'off' : myOptions.quiz_show_only_wrong_answers;
            var quiz_show_only_wrong_answers = (myOptions.quiz_show_only_wrong_answers && myOptions.quiz_show_only_wrong_answers == 'on') ? true : false;

            if ( quiz_show_wrong_answers_first || quiz_show_only_wrong_answers ) {
                var UserAnswered_true_arr  = new Array();
                var UserAnswered_false_arr = new Array();
                for (var question_ID in answerIsRightArr) {

                    var question_ID_arr = question_ID.split("_");
                    var questionDataID  = question_ID_arr[1];

                    var UserAnswered_true_or_false = answerIsRightArr[question_ID];
                    var questionHTML = form.find('.ays_quiz_results div.ays_question_result[data-question-id="'+ questionDataID +'"]').clone();

                    if ( UserAnswered_true_or_false ) {
                        UserAnswered_true_arr.push( questionHTML );
                    } else {
                        UserAnswered_false_arr.push( questionHTML );
                    }
                }

                if ( quiz_show_only_wrong_answers ) {
                    UserAnswered_true_arr = new Array();
                }

                var allQuestionHTML = UserAnswered_false_arr.concat( UserAnswered_true_arr );

                formResults.html('');

                if ( quiz_enable_results_toggle ) {
                    formResults.append(resultToggleHTML);
                }

                for (var ii = 0; ii < allQuestionHTML.length; ii++) {
                    formResults.append( allQuestionHTML[ii] );
                }
            }
        }
        form.find('.ays_quiz_results').slideDown(1000);
        form.find('.ays_quiz_rete').fadeIn(250);
        form.find('.for_quiz_rate').rating({
            onRate: function(res){
                $(this).rating('disable');
                $(this).parent().find('.for_quiz_rate_reason').slideDown(500);
                $(this).parents('.ays_quiz_rete').attr('data-rate_score', res);
            }
        });
        var aysQuizLoader = form.find('div[data-role="loader"]');
        aysQuizLoader.addClass('ays-loader');
        aysQuizLoader.removeClass(aysQuizLoader.data('class'));
        aysQuizLoader.find('.ays-loader-content').css('display','none');
        form.find('.ays_quiz_results_page').css({'display':'block'});
        form.css({'display':'block'});
        form.find('.ays_quiz_rete .for_quiz_rate_reason .action-button').on('click', function(){
            var _this = $(this);
            var _parent = _this.parents('.for_quiz_rate_reason');
            
            if(myOptions.quiz_make_review_required == 'on' && myOptions.quiz_make_review_required == 'on'){

                var _el = _parent.find('.quiz_rate_reason[data-required="true"]');

                if ( ! _this.hasClass('start_button') ) {
                    if ( _el.length !== 0 ) {
                        var empty_inputs = 0;

                        if (_el.val() === '' &&
                            _el.attr('type') !== 'hidden') {
                            _el.addClass('ays_red_border');
                            _el.addClass('ays_animated_x5ms');
                            _el.addClass('shake');
                            empty_inputs++;
                        }

                        var errorFields = _parent.find('.ays_red_border');
                        if ( empty_inputs !== 0 ) {
                            setTimeout(function(){
                                errorFields.each(function(){
                                    $(this).removeClass('shake');
                                });
                            }, 500);
                            return false;
                        }
                    }
                }
            }

            $(this).parents('.ays_quiz_rete').find('.lds-spinner-none').addClass('lds-spinner').removeClass('lds-spinner-none');
            if(myOptions.enable_quiz_rate == 'on' && myOptions.enable_rate_comments == 'on'){
                $(this).parents('.ays_quiz_rete').find('.lds-spinner2-none').addClass('lds-spinner2').removeClass('lds-spinner2-none');
            }
            var data = {};
            var quizId = form.parents('.ays-quiz-container').find('input[name="ays_quiz_id"]').val();
            var quizCusrrentPageLink = form.parents('.ays-quiz-container').find('input[name="ays_quiz_curent_page_link"]').val();
            data.action = 'ays_rate_the_quiz';
            data.rate_reason = $(this).parents('.for_quiz_rate_reason').find('.quiz_rate_reason').val();
            data.rate_score = $(this).parents('.ays_quiz_rete').data('rate_score');
            data.rate_date = GetFullDateTime();
            data.quiz_id = quizId;
            data.last_result_id = last_result_id;
            data.quiz_make_responses_anonymous = quiz_make_responses_anonymous;
            data.quiz_current_page_link = quizCusrrentPageLink;
            $.ajax({
                url: quiz_maker_ajax_public.ajax_url,
                method: 'post',
                dataType: 'json',
                data: data,
                success: function(response){
                    if(response.status === true){
                        form.find('.for_quiz_rate_reason').slideUp(800);
                        setTimeout(function(){
                            form.find('.ays_quiz_rete').find('.for_quiz_rate').attr('data-rating', response.score);
                            form.find('.ays_quiz_rete').find('.for_quiz_rate').rating({
                                initialRating: response.score
                            });
                            form.find('.ays_quiz_rete').find('.for_quiz_rate').rating('disable');
                            form.find('.lds-spinner').addClass('lds-spinner-none').removeClass('lds-spinner');
                            form.find('.for_quiz_rate_reason').html('<p>'+response.rates_count + ' votes, '+response.avg_score + ' avg </p>');
                            form.find('.for_quiz_rate_reason').fadeIn(250);     
                            if(myOptions.enable_quiz_rate == 'on' && myOptions.enable_rate_comments == 'on'){
                                var data = {};
                                data.action = 'ays_get_rate_last_reviews';
                                data.quiz_id = response.quiz_id;
                                $.ajax({
                                    url: quiz_maker_ajax_public.ajax_url,
                                    method: 'post',
                                    data: data,
                                    success: function(response){
                                        var response_arr = JSON.parse(response);
                                        var responseHTML = (response_arr.quiz_rate_html && response_arr.quiz_rate_html != '') ? response_arr.quiz_rate_html : '';

                                        form.find('.quiz_rate_reasons_body').html(responseHTML);
                                        form.find('.lds-spinner2').addClass('lds-spinner2-none').removeClass('lds-spinner2');
                                        form.find('.quiz_rate_reasons_container').slideDown(500);
                                        form.find('.ays-quiz-rate-link-box .ays-quiz-rate-link').slideUp(500);
                                        form.find('button.ays_load_more_review').on('click', function(e){
                                            form.find('.quiz_rate_load_more [data-role="loader"]').addClass(form.find('.quiz_rate_load_more .ays-loader').data('class')).removeClass('ays-loader');
                                            var startFrom = parseInt($(e.target).attr('startfrom'));
                                            var zuyga = parseInt($(e.target).attr('zuyga'));
                                            $.ajax({
                                                url: quiz_maker_ajax_public.ajax_url,
                                                method: 'post',
                                                data:{
                                                    action: 'ays_load_more_reviews',
                                                    quiz_id: quizId,
                                                    start_from: startFrom,
                                                    zuyga: zuyga
                                                },
                                                success: function(resp){
                                                    if(zuyga == 0){
                                                        zuyga = 1;
                                                    }else{
                                                        zuyga = 0;
                                                    }
                                                    
                                                    form.find('.quiz_rate_load_more [data-role="loader"]').addClass('ays-loader').removeClass(form.find('.quiz_rate_load_more .ays-loader').data('class'));
                                                    form.find('.quiz_rate_reasons_container').append(resp);
                                                    form.find('.quiz_rate_more_review:last-of-type').slideDown(500);
                                                    $(e.target).attr('startfrom', startFrom + 5 );
                                                    $(e.target).attr('zuyga', zuyga);
                                                    if(form.find('.quiz_rate_reasons_container p.ays_no_more').length > 0){
                                                        $(e.target).remove();
                                                    }
                                                }
                                            });
                                        });
                                    },
                                    error: function(jqXHR, textStatus, errorThrown) {}
                                });
                            }
                        },1000);
                    }
                }
            });
        });
        if (myOptions.redirect_after_submit && myOptions.redirect_after_submit == 'on') {            
            var ays_block_element = form.parents('.ays-quiz-container');
            var quizId = form.parents('.ays-quiz-container').find('input[name="ays_quiz_id"]').val();
            var redirectUrl = myOptions.submit_redirect_after ? myOptions.submit_redirect_after : '';
            if(redirectUrl == ''){
                return false;
            }
            var timer = parseInt(myOptions.submit_redirect_delay);
            if(timer === NaN){
                timer = 0;
            }
            var tabTitle = document.title;
            var timerText = $('<section class="ays_quiz_redirection_timer_container">'+
                '<div class="ays-quiz-redirection-timer">'+
                'Redirecting after ' + redirectUrl + 
                '</div><hr></section>');
            ays_block_element.prepend(timerText);
            ays_block_element.find('.ays_quiz_redirection_timer_container').css({
                height: 'auto'
            });
            setTimeout(function(){
                if (timer !== NaN) {
                    timer += 2;
                    if (timer !== undefined) {
                        var countDownDate = new Date().getTime() + (timer * 1000);
                        ays_block_element.find('div.ays-quiz-redirection-timer').slideUp(500);

                        // Message before redirect timer
                        var quiz_message_before_redirect_timer = (myOptions.quiz_message_before_redirect_timer && myOptions.quiz_message_before_redirect_timer != "") ? ( myOptions.quiz_message_before_redirect_timer ) : '';

                        if ( quiz_message_before_redirect_timer != '' ) {
                            quiz_message_before_redirect_timer = quiz_message_before_redirect_timer.replace(/(["'])/g, "\\$1") + " ";

                            $(document).find('html > head').append('<style> #ays-quiz-container-'+ quizId +' div.ays-quiz-redirection-timer:before{content: "'+ quiz_message_before_redirect_timer +'"; }</style>');
                        }

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
                                ays_block_element.find('div.ays-quiz-redirection-timer').html(timeForShow);
                                document.title = timeForShow + " - " + tabTitle;
                            }else{
                                ays_block_element.find('div.ays-quiz-redirection-timer').html(timeForShow);
                                document.title = timeForShow + " - " + tabTitle;
                            }
                            ays_block_element.find('div.ays-quiz-redirection-timer').slideDown(500);
                            var ays_block_element_redirect_url = myOptions.submit_redirect_url;
                            if (distance <= 1000) {
                                clearInterval(x);
                                window.location = ays_block_element_redirect_url;
                            }
                        }, 1000);
                    }
                }
            }, 2000);
        }
    }
    
})(jQuery);
