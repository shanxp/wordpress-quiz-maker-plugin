(function( $ ) {
    'use strict';
    var emailValidatePattern = /^[a-zA-Z0-9\._-]+@[a-zA-Z0-9\._-]+\.\w{2,}$/;
    $.fn.serializeFormJSON = function () {
        let o = {},
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
    
    $.fn.aysModal = function(action){
        let $this = $(this);
        switch(action){
            case 'hide':
                $(this).find('.ays-modal-content').css('animation-name', 'zoomOut');
                setTimeout(function(){
                    $(document.body).removeClass('modal-open');
                    $(document).find('.ays-modal-backdrop').remove();
                    $this.hide();
                }, 250);
            break;
            case 'show': 
            default:
                $this.show();
                $(this).find('.ays-modal-content').css('animation-name', 'zoomIn');
                $(document).find('.modal-backdrop').remove();
                $(document.body).append('<div class="ays-modal-backdrop"></div>');
                $(document.body).addClass('modal-open');
            break;
        }
    }

    $(document).find('form#ays_add_question_rows').on( 'submit', function(e) {
        $(document).find('div.ays-quiz-preloader').css('display', 'flex');
        $(document).find('td.empty_quiz_td').parent().remove();
        let data = $(this).serializeFormJSON();
        data.action = 'add_question_rows';
        data['ays_questions_ids[]'] = window.aysQuestSelected;
        $.ajax({
            url: quiz_maker_ajax.ajax_url,
            method: 'post',
            dataType: 'json',
            data: data,
            success: function(response){
                if( response.status === true ) {
                    $(document).find("table#ays-questions-table").find('.dataTables_empty').parents('tr').remove();
                    $(document).find('div.ays-quiz-preloader').css('display', 'none');
                    let table = $('table#ays-questions-table tbody'),
                        id_container = $(document).find('input#ays_already_added_questions'),
                        existing_ids = ( id_container.val().split(',')[0] === "" ) ? [] : id_container.val().split(','),
                        new_ids = [];
                    for(let i = 0; i < response.ids.length; i++) {
                        if( $.inArray( response.ids[i], existing_ids ) === -1 ) {
                            new_ids.push(response.ids[i]);
                            table.append(response.rows[i]);
                            let table_rows = $('table#ays-questions-table tbody tr'),
                                table_rows_length = table_rows.length;
                            if( table_rows_length % 2 === 0 ) {
                                table_rows.eq( ( table_rows_length - 1 ) ).addClass('even');
                            }
                        }else{
                            let position = $.inArray( response.ids[i], existing_ids );
                        }
                    }
                    
                    let table_rows = $('table#ays-questions-table tbody tr');
                    // new_ids = new_ids.reverse();
                    for(var i = 0; i < new_ids.length; i++){
                        existing_ids.push(new_ids[i]);
                    }                    
                    table_rows.each(function(){
                        let id = $(this).data('id');
                        if($.inArray( id.toString(), existing_ids ) === -1){
                            $(this).remove();
                        }
                    });
                    id_container.val( existing_ids );
                }
                $(document).find('#ays-questions-modal').aysModal('hide');
                let questions_count = response.ids.length;

                let table_rows = $('table#ays-questions-table tbody tr');
                $(document).find('.questions_count_number').html(response.ids.length);

                let pagination = $('.ays-question-pagination');
                if (pagination.length > 0) {
                    let trCount = $(document).find('#ays-questions-table tbody tr').length;
                    let pagesCount = 1;
                    let pageCount = Math.ceil(trCount/5);
                    createPagination(pagination, pageCount, pagesCount);

                    let page = 1; // set page 1
                    $('ul.ays-question-nav-pages').removeAttr('style');//moves pagination to first
                    let pages = $('ul.ays-question-nav-pages li');
                    pages.each(function () {
                        $(this).removeClass('active'); //remove active pages
                    });
                    pages.eq(0).addClass('active'); // assigning to first page element active
                    show_hide_rows(page); // show count of rows
                }
            }
        });
        e.preventDefault();
    } );
    
    $(document).find('#ays_quick_submit_button').on('click',function (e) {
        // deactivate_questions();
        $(document).find('div.ays-quiz-preloader').css('display', 'flex');
        var questions =  $(document).find('.ays_modal_question');
        if($(e.target).parents('#ays-quick-modal-content').find('#ays-quiz-title').val() == ''){            
            swal.fire({
                type: 'error',
                text: "Quiz title can't be empty"
            });
            $(document).find('div.ays-quiz-preloader').css('display', 'none');
            return false;
        }
        var qqanswers = $(e.target).parents('#ays-quick-modal-content').find('.ays_answer');
        var emptyAnswers = 0;
        for(var j = 0; j < qqanswers.length; j++){
            var parent =  qqanswers.eq(j).parents('.ays_modal_question');
            var questionType = parent.find('.ays_quick_question_type').val();

            if ( questionType == 'text' ) {
                var answerVal = parent.find('textarea.ays-correct-answer-value.ays-text-question-type-value').val();

                if(answerVal == ''){
                    emptyAnswers++;
                    break;
                }
            } else if( questionType == 'short_text' || questionType == 'number' || questionType == 'date') {
                var answerVal = parent.find('input.ays-correct-answer-value.ays-text-question-type-value').val();

                if(answerVal == ''){
                    emptyAnswers++;
                    break;
                }
            } else {
                if(qqanswers.eq(j).val() == ''){
                    emptyAnswers++;
                    break;
                }
            }
        }
        if(emptyAnswers > 0){
            swal.fire({
                type: 'error',
                text: "You must fill all answers"
            });
            $(document).find('div.ays-quiz-preloader').css('display', 'none');
            return false;
        }
        
        for(var i=0;i<questions.length;i++){
            var question_text = aysEscapeHtml( questions.eq(i).find('.ays_question_input').val() );
            var question_type = questions.eq(i).find('.ays_quick_question_type').val();

            questions.eq(i).find('.ays_question_input').after('<input type="hidden" name="ays_quick_question[]" value="'+question_text+'">');

            if ( question_type == 'text' ) {
                var question_answers = questions.eq(i).find('.ays-correct-answer-value');

                question_answers.append('<input type="hidden" name="ays_quick_answer['+i+'][]" value="'+ aysEscapeHtml( question_answers.val() ) +'">');
                question_answers.append('<input type="hidden" name="ays_quick_answer_correct['+i+'][]" value="true">');
            } else if( question_type == 'short_text' ||  question_type == 'number' || question_type == 'date' ){

                var question_answers = questions.eq(i).find('input.ays-correct-answer-value.ays-text-question-type-value');

                question_answers.after('<input type="hidden" name="ays_quick_answer['+i+'][]" value="'+ aysEscapeHtml( question_answers.val() )+'">');
                question_answers.after('<input type="hidden" name="ays_quick_answer_correct['+i+'][]" value="true">');
            } else {
                var question_answers = questions.eq(i).find('.ays_answer');
                var question_answers_correct = questions.eq(i).find('input.ays_answer_unique_id');
                for(var a=0;a<question_answers.length;a++){
                    question_answers.eq(a).after('<input type="hidden" name="ays_quick_answer['+i+'][]" value="'+question_answers.eq(a).val()+'">');
                }
                for(var z=0;z<question_answers_correct.length;z++){
                    if(question_answers_correct.eq(z).prop('checked')){
                        question_answers_correct.eq(z).parents().eq(0).append('<input type="hidden" name="ays_quick_answer_correct['+i+'][]" value="true">');
                    }else{
                        question_answers_correct.eq(z).parents().eq(0).append('<input type="hidden" name="ays_quick_answer_correct['+i+'][]" value="false">');
                    }
                }
            }
        }

        var data = $('#ays_quick_popup').serializeFormJSON();
        data.action = 'ays_quick_start';

        $.ajax({
            url: quiz_maker_ajax.ajax_url,
            method: 'post',
            dataType: 'json',
            data: data,
            success: function (response) {
                $(document).find('div.ays-quiz-preloader').css('display', 'none');
                if(response.status === true){
                    $(document).find('#ays_quick_popup')[0].reset();
                    $(document).find('#ays-quick-modal .ays-modal-content').addClass('animated bounceOutRight');
                    $(document).find('#ays-quick-modal').aysModal('hide');
                    swal({
                        title: '<strong>Great job</strong>',
                        type: 'success',
                        html: '<p>You can use this shortcode to show your quiz.</p><input type="text" id="quick_quiz_shortcode" onClick="this.setSelectionRange(0, this.value.length)" readonly value="[ays_quiz id=\'' + response.quiz_id + '\']" /><p style="margin-top:1rem;">For more detailed configuration visit <a href="admin.php?page=quiz-maker&action=edit&quiz=' + response.quiz_id + '">edit quiz page</a>.</p>',
                        showCloseButton: true,
                        focusConfirm: false,
                        confirmButtonText:
                          '<i class="ays_fa ays_fa_thumbs_up"></i> Great!',
                        confirmButtonAriaLabel: 'Thumbs up, great!',
                        onAfterClose: function() {
                            $(document).find('#ays-quick-modal').removeClass('animated bounceOutRight');
                            $(document).find('#ays-quick-modal').css('display', 'none');
                            window.location.href = "admin.php?page=quiz-maker";
                        }
                    });
                    var modalQuestion = $('.ays_modal_element.ays_modal_question');
                    modalQuestion.each(function(){
                        if($('.ays_modal_element.ays_modal_question').length !== 1){
                            $(this).remove();
                        }
                    });
                }
            }
        });
    });

    // Open results more information popup window
    $(document).on('click', '.ays_quiz_read_result', function(e){
        var where = 'row';
        ays_show_results(e, $(this).find('.ays-show-results').eq(0), where);
    });

    $(document).on("click", ".ays-quiz-subscribe-button", function(e){
        var $this = $(this);
        var thisParent = $this.parents(".ays-quiz-subscribe-email-page");
        var emailInput = thisParent.find(".ays-quiz-subscribe-email-address");
        var emailInputVal = emailInput.val();
        var flag = false;
        var data = {
            email: emailInputVal,
            action: 'ays_quiz_subscribe_email'
        };

        if(emailInputVal != ""){
            if(!(emailValidatePattern.test(emailInputVal))){
                emailInput.addClass("ays-quiz-subscribe-email-error");
                thisParent.find(".ays-quiz-subscribe-email-error-message").css("visibility", "visible");
                thisParent.find(".ays-quiz-subscribe-email-error-message span.ays-quiz-subscribe-email-errors").text(quiz_maker_ajax.invalidEmailError);
            }
            else{
                flag = true;
            }
        }
        else{
            thisParent.find(".ays-quiz-subscribe-email-error-message").css("visibility", "visible");
            thisParent.find(".ays-quiz-subscribe-email-error-message span.ays-quiz-subscribe-email-errors").text(quiz_maker_ajax.emptyEmailError);
        }
        if(flag){
            thisParent.find(".ays-quiz-subscribe-email-loader").show();
            thisParent.find(".ays-quiz-subscribe-email-error-message").css("visibility", "hidden");
            $.ajax({
                url: quiz_maker_ajax.ajax_url,
                method: 'post',
                dataType: 'json',
                data: data,
                success: function (response) {
                    thisParent.find(".ays-quiz-subscribe-email-loader").hide();
                    var messageBox = thisParent.find(".ays-quiz-subscribe-email-success-message").clone(true, true);
                    messageBox.css("display" , "flex");
                    if(response.status){
                        messageBox.find(".ays-quiz-subscribe-email-success-message-true").css("display" , "block");
                    }
                    else{
                        messageBox.find(".ays-quiz-subscribe-email-success-message-false").css("display" , "block");
                    }
                    messageBox.find(".ays-quiz-subscribe-email-success-message-text").text(response.message);
                    thisParent.find(".ays-quiz-subscribe-email-page-box").css("width","initial").html(messageBox);
                }
            });
        }
    });

    function ays_show_results(e, this_element, where){        
        if($(e.target).hasClass('ays_confirm_del') || $(e.target).hasClass('ays_result_delete')){
            
        }else{
            e.preventDefault();
            $(document).find('div.ays-quiz-preloader').css('display', 'flex');
            $(document).find('#ays-results-modal').aysModal('show');
            let result_id = this_element.data('result');
            let action = 'ays_show_results';
            $.ajax({
                url: quiz_maker_ajax.ajax_url,
                method: 'post',
                dataType: 'json',
                data: {
                    result: result_id,
                    action: action
                },
                success: function(response){
                    if(response.status === true){
                        $('div#ays-results-body').html(response.rows);
                        $(document).find('div.ays-quiz-preloader').css('display', 'none');
                        if($(this_element).parents('tr').hasClass('ays_read_result')){
                            $(this_element).parents('tr').removeClass('ays_read_result');
                            $(this_element).parents('tr').find('a.ays-show-results').css('font-weight', 'initial');
                            $(document).find('.ays_results_bage').each(function(){
                                $(this).text(parseInt($(this).text())-1);
                                if(parseInt($(this).text()) == 0){
                                    $(this).remove();
                                }
                            });
                        }
                    }else{
                        swal.fire({
                            type: 'info',
                            html: "<h2>Can't load resource.</h2><br><h4>Maybe the data has been deleted.</h4>",
                            
                        }).then( function(res) {
                            $(document).find('div.ays-quiz-preloader').css('display', 'none');
                            if($(this_element).parents('tr').hasClass('ays_read_result')){
                                $(this_element).parents('tr').removeClass('ays_read_result');
                                $(this_element).parents('tr').find('a.ays-show-results').css('font-weight', 'initial');
                                $(document).find('.ays_results_bage').each(function(){
                                    $(this).text(parseInt($(this).text())-1);
                                    if(parseInt($(this).text()) == 0){
                                        $(this).remove();
                                    }
                                });
                            }
                            $(document).find('#ays-results-modal').aysModal('hide');
                        });
                    }
                },
                error: function(){
                    swal.fire({
                        type: 'info',
                        html: "<h2>Can't load resource.</h2><br><h6>Maybe the data has been deleted.</h46>"
                    }).then( function(res) {
                        $(document).find('div.ays-quiz-preloader').css('display', 'none');
                        if($(this_element).parents('tr').hasClass('ays_read_result')){
                            $(this_element).parents('tr').removeClass('ays_read_result');
                            $(this_element).parents('tr').find('a.ays-show-results').css('font-weight', 'initial');                        
                            $(document).find('.ays_results_bage').each(function(){
                                $(this).text(parseInt($(this).text())-1);
                                if(parseInt($(this).text()) == 0){
                                    $(this).remove();
                                }
                            });
                            // $(document).find('.ays_results_bage').text(
                            //     parseInt($(document).find('.ays_results_bage').text())-1
                            // );
                            // if(parseInt($(document).find('.ays_results_bage').text()) == 0){
                            //     $(document).find('.ays_results_bage').remove();
                            // }
                        }
                        $(document).find('#ays-results-modal').aysModal('hide');
                    });
                }
            });
        }
    }

    function deactivate_questions() {
        if ($('.active_question').length !== 0) {
            var question = $('.active_question').eq(0);
            if(!$(question).find('input[name^="ays_answer_radio"]:checked').length){
                $(question).find('input[name^="ays_answer_radio"]').eq(0).attr('checked',true)
            }
            $(question).find('.ays_add_answer').parents().eq(1).addClass('show_add_answer');
            $(question).find('.fa.fa-times').parent().removeClass('active_remove_answer').addClass('show_remove_answer');

            var question_text = $(question).find('.ays_question_input').val();
            $(question).find('.ays_question_input').remove();
            $(question).prepend('<p class="ays_question">' + question_text + '</p>');
            var answers_tr = $(question).find('.ays_answers_table tr');
            for (var i = 0; i < answers_tr.length; i++) {
                var answer_text = ($(answers_tr.eq(i)).find('.ays_answer').val()) ? $(answers_tr.eq(i)).find('.ays_answer').val() : '';
                $(answers_tr.eq(i)).find('.ays_answer_td').empty();
                let answer_html = '<p class="ays_answer">' + answer_text + '</p>'+((answer_text == '')?'<p>Answer</p>':'');
                $(answers_tr.eq(i)).find('.ays_answer_td').append(answer_html)
            }
            $('.active_question').find('.ays_question_overlay').removeClass('display_none');
            $('.active_question').removeClass('active_question');
        }
    }    
    
    function show_hide_rows(page) {
        let rows = $('table.ays-questions-table tbody tr');
        rows.each(function (index) {
            $(this).css('display', 'none');
        });
        let counter = page * 5 - 4;
        for (let i = counter; i < (counter + 5); i++) {
            rows.eq(i - 1).css('display', 'table-row');
        }
    }

    function createPagination(pagination, pagesCount, pageShow) {
        (function (baseElement, pages, pageShow) {
            let pageNum = 0, pageOffset = 0;

            function _initNav() {
                let appendAble = '';
                for (let i = 0; i < pagesCount; i++) {
                    let activeClass = (i === 0) ? 'active' : '';
                    appendAble += '<li class="' + activeClass + ' button ays-question-page" data-page="' + (i + 1) + '">' + (i + 1) + '</li>';
                }
                $('ul.ays-question-nav-pages').html(appendAble);
                let pagePos = ($('div.ays-question-pagination').width()/2) - (parseInt($('ul.ays-question-nav-pages>li:first-child').css('width'))/2);
                $('ul.ays-question-nav-pages').css({
                    'margin-left': pagePos,
                });
                //init events
                let toPage;
                let pagesCountExists = $('ul.ays-question-nav-pages li').length;
                baseElement.on('click', '.ays-question-nav-pages li, .ays-question-nav-btn', function (e) {
                    if ($(e.target).is('.ays-question-nav-btn')) {
                        toPage = $(this).hasClass('ays-question-prev') ? pageNum - 1 : pageNum + 1;
                    } else {
                        toPage = $(this).index();
                    }
                    let page = Number(toPage) + 1;
                    
                    if(page > pagesCountExists){
                        page = pagesCountExists;
                    }
                    if(page <= 0){
                        page = 1;
                    }
                    show_hide_rows(page);
                    _navPage(toPage);
                });
            }

            function _navPage(toPage) {
                let sel = $('.ays-question-nav-pages li', baseElement), w = sel.first().outerWidth(),
                    diff = toPage - pageNum;

                if (toPage >= 0 && toPage <= pages - 1) {
                    sel.removeClass('active').eq(toPage).addClass('active');
                    pageNum = toPage;
                } else {
                    return false;
                }

                if (toPage <= (pages - (pageShow + (diff > 0 ? 0 : 1))) && toPage >= 0) {
                    pageOffset = pageOffset + -w * diff;
                } else {
                    pageOffset = (toPage > 0) ? -w * (pages - pageShow) : 0;
                }
                sel.parent().css('left', pageOffset + 'px');
            }

            _initNav();

        })(pagination, pagesCount, pageShow);
    }

    /**
     * @return {string}
     */
    function aysEscapeHtml(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        if ( typeof text !== 'undefined' ) {
            return text.replace(/[&<>\"']/g, function(m) { return map[m]; });
        }
    }
    
})( jQuery );