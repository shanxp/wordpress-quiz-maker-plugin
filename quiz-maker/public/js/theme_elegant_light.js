jQuery(document).ready(function ($) {
    $(document).find('input[name^="ays_questions"]').on('change', function (e) {
        if($(e.target).parents('.ays-quiz-container').hasClass('ays_quiz_elegant_light')){
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
                var checked_inputs = $(e.target).parents().eq(1).find('input:checked');
                if (checked_inputs.parents('div[data-question-id]').find('input.ays_next').hasClass('ays_display_none') && checked_inputs.parents('div[data-question-id]').find('i.ays_next_arrow').hasClass('ays_display_none')) {
                    if (checked_inputs.attr('type') === 'radio') {
                        checked_inputs.parents('div[data-question-id]').find('.ays_next').trigger('click');                    
                    }
                }
                return false;
            }
        if ($(e.target).parents().eq(4).hasClass('enable_correction')) {
            if ($(e.target).parents().eq(1).find('input[name="ays_answer_correct[]"]').length !== 0) {
                var checked_inputs = $(e.target).parents().eq(1).find('input:checked');
                if (checked_inputs.length === 1) {
                    if ($(this).parent().find('input[name="ays_answer_correct[]"]').val() == 1) {
                        $(this).parent().find('label[for^="ays-answer"]').addClass('correct');
                        $(this).parent().find('label[for^="ays-answer"]').parent().addClass('correct_div checked_answer_div');
                    } else {
                        $(this).parent().find('label[for^="ays-answer"]').addClass('wrong');
                        $(this).parent().find('label[for^="ays-answer"]').parent().addClass('wrong_div checked_answer_div');
                    }
                    if (checked_inputs.attr('type') === "radio") {
                        $(e.target).parents('div[data-question-id]').find('input[name^="ays_questions"]').attr('disabled', true);
                        $(e.target).parents('div[data-question-id]').find('input[name^="ays_questions"]').off('change');
                        $(e.target).parent().find('label[for^="ays-answer"]').css('padding',"0 10px 0 10px");
                    }
                    $(e.target).parent().find('label[for^="ays-answer"]').css('padding',"0 10px 0 10px");
                }else{
                    for(var i = 0; i < checked_inputs.length; i++){
                        if ($(this).parent().find('input[name="ays_answer_correct[]"]').val() == 1) {
                            $(this).parent().find('label[for^="ays-answer"]').addClass('correct');
                            $(this).parent().find('label[for^="ays-answer"]').parent().addClass('correct_div checked_answer_div');
                        } else {
                            $(this).parent().find('label[for^="ays-answer"]').addClass('wrong');
                            $(this).parent().find('label[for^="ays-answer"]').parent().addClass('wrong_div checked_answer_div');
                        }
                        if (checked_inputs.attr('type') === "radio") {
                            $(e.target).parents('div[data-question-id]').find('input[name^="ays_questions"]').attr('disabled', true);
                            $(e.target).parents('div[data-question-id]').find('input[name^="ays_questions"]').off('change');
                            $(e.target).parent().find('label[for^="ays-answer"]').css('padding',"0 10px 0 10px");
                        }
                    }
                    $(e.target).parent().find('label[for^="ays-answer"]').css('padding',"0 10px 0 10px");
                }                
                $(e.target).parents('div[data-question-id]').find('input[name^="ays_questions"]').next().css('background-color', "transparent");
            }
        }else{
            if($(this).attr('type') === 'radio') {
                $(this).parents('.ays-quiz-answers').find('.checked_answer_div').removeClass('checked_answer_div');
                $(this).parents('.ays-quiz-answers').find('.ays-field label[for^="ays-answer"]').css('padding',"8px 10px 8px 10px");
                $(this).parents('.ays-field').addClass('checked_answer_div');
                $(e.target).parent().find('label[for^="ays-answer"]').css('padding',"0 10px 0 10px");
            }
            // if($(this).attr('type') === 'checkbox') {
            //     if($(this).prop('checked')){
            //         $(e.target).parent().find('label[for^="ays-answer"]').css('padding',"0 10px 0 10px");
            //     }else{
            //         $(e.target).parent().find('label[for^="ays-answer"]').css('padding',"8px 10px 8px 10px");
            //     }
            // }   
        }
        }
    });

    $(document).find('.ays_next.start_button').on('click', function(e){
        var _this = $(this);
        var container = _this.parents('.ays-quiz-container');

        container.find('section.ays_quiz_timer_container').addClass('ays_quiz_timer_bg_container');
    });
});
