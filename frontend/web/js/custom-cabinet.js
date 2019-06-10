var withdraw_rate = 1;
var withdraw_currency = '';
var withdraw_fee = 0;

jQuery(document).ready(function($) {
    $(document).on('submit', 'form[enctype="multipart/form-data"]', function () {
        window.onbeforeunload = null;
    })
    goToTag();
   // getBets();
    $(document).on('click', '[data-date]', function () {
        window.location = '/user/bets/' + $(this).data('date');
    });

    $(document).on('click', 'button.copy_btn', function () {
        var cutTextarea = $(this).siblings('.copy_target').val();

        try {
            var successful = Clipboard.copy(cutTextarea);
            //document.execCommand('copy');
            $('.copy_target').blur();
        } catch (err) {
            console.log('Cannot copy target message');
        }
    });


    $(document).on('click', '.messages-item', function () {
        var time = $(this).find('.messages-item__user--info__date').html(),
            title = $(this).find('.messages-item__message--title').html(),
            text = $(this).find('.messages-item__message--text').html(),
            name = $(this).find('.messages-item__user--info__name').html(),
            avatar = $(this).find('.messages-item__user--avatar img').attr('src');

        $('.messages-read .messages-item__user--info__date').html(time);
        $('.messages-read .messages-read--incoming__message--item').empty();
        $('.messages-read .messages-read--incoming__message--item').append('<p><b class="messages-read--incoming__message--title">'+title+'</b><p><br>' + text);
        $('.messages-read .messages-item__user--info__name').html(name);
        $('.messages-read .messages-item__user--avatar img').attr('src', avatar);

        if($(this).hasClass('unread')) {
            var id = $(this).data('id');
            $(this).removeClass('unread');
            $(this).find('.messages-item__hav-message').empty();
            $(this).find('.messages-item__user--avatar__count').remove();

            $.ajax({
                url: window.location.protocol + '//' + window.location.hostname + '/user/read-message',
                type: "post",
                data: {
                    id: id,
                },
            });
            getBets();
        }
    });

    $(document).on('click', '.promo-content__video__item .video_local', function () {
        var video = $(document).find('#show-promo .modal-video');
            video.append('<video width="'+$(this).data('width')+'" height="'+$(this).data('height')+'" controls="controls" preload autoplay><source src="'+$(this).data('video')+'"></video>');
        $(document).find('#show-promo').modal('show');
    });

    $('#show-promo').on('hidden.bs.modal', function () {
        $(this).find('video').remove();
    })


    var modal_close_button = ' <button class="close" type="button" data-dismiss="modal" aria-label="Close"><svg width="14px" height="14px"><path fill-rule="evenodd" fill="rgb(49, 49, 49)" d="M13.660,13.216 L13.216,13.660 L7.000,7.444 L0.784,13.660 L0.340,13.216 L6.556,7.000 L0.340,0.783 L0.784,0.339 L7.000,6.556 L13.216,0.339 L13.660,0.783 L7.444,7.000 L13.660,13.216 Z"></path></svg></button>';

    $(document).on('click', '.to_promo_banner', function () {
        var modal_body = $(document).find('#show-promo .modal-content');
        modal_body.empty();
        modal_body.append('<div class="modal-body">' + modal_close_button + $(this).data('promo') + '</div>');
    });



    $(document).on('click', '.payments-item', function () {
        var system_id = $(this).data('system_id');
        if(system_id) {
            var system = payment_systems[system_id];
            var form = $(document).find('.deposit-payments-form');
            form.find('img.deposit-payments-form__payment').attr('src', system.image);
            form.find('span.deposit-payments-form__deposit__via').html(system.title);
            form.find('#investform-system_id').val(system.id);
            var min_max = number_format(system.sum_min, 0, '', ' ') + ' ' + system.currency.synonym + ' / ' + number_format(system.sum_max, 0, '', ' ') + ' ' + system.currency.synonym;
            form.find('.deposit-payments-form__info--item__amount.min_max').html(min_max);
            form.find('.deposit-payments-form__info--item__amount.fee').html(system.fee + '%');
            form.find('.deposit-payments-form__info--item__amount.fee_verified').html(system.fee_verified + '%');
        } else {
            system_id = $(this).data('withdraw_id');
            var system = withdraw_systems[system_id];
            withdraw_rate = system.currency_rate;
            withdraw_currency = system.currency_name;
            if(user_verified) {
                withdraw_fee = system.fee_verified;
            } else {
                withdraw_fee = system.fee;
            }



            if(selected_withdraw_method && selected_withdraw_method == system.id) {
                $('.selected_withdraw').show();
                $('.default_withdraw').hide();
            } else {
                $('.selected_withdraw').hide();
                $('.default_withdraw').show();
            }
            var form = $(document).find('.deposit-payments-form');
            form.find('img.withdraw-payments-form__payment').attr('src', system.image);
            form.find('span.withdraw-payments-form__deposit__via').html(system.title);
            form.find('.withdraw_fee_show').html(system.fee + ' %');
            form.find('.withdraw_sum_min').html('от ' +system.sum_min + '$');
            form.find('.withdraw_fee_verified').html(system.fee_verified  + ' %');
        }
    });


    var user_phone = $(document).find('#edituserfrom-phone').val();
    var new_phone = '';

    $(document).on('change', '#edituserfrom-phone', function () {
        var val = $(this).val();
        if(!val || val == '') {
            $('#change_phone_1').hide();
            return false;
        }
        if(val != user_phone) {
            $('#change_phone_1').show();
        } else {
            $('#change_phone_1').hide();
        }
    });

    $(document).on('click', '#change_phone_1', function (e) {
        e.preventDefault();
       $('#edituserfrom-phone').attr('disabled', true);
        new_phone = $('#edituserfrom-phone').val();
        $.ajax({
            url: window.location.protocol + "//" + window.location.host + "/user/change-phone-sms",
            type: "POST",
            data: {'new_phone' : new_phone},
            success: function (data) {
                data = JSON.parse(data);
                if(data.status == 1) {
                    $('#change_phone_1').hide();
                    $('#change_phone_2').find('input.sms-code').val('');
                    $('#change_phone_2').show();
                } else {
                    $('.field-edituserfrom-phone').addClass('has-error');
                    $('.field-edituserfrom-phone .help-block').html(data.message);
                    $('#edituserfrom-phone').attr('disabled', false);
                    $('#change_phone_1').hide();
                }
            }
        });

        return false;
    });

    $(document).on('click', '#change_phone_2 button', function (e) {
        e.preventDefault();
        var parent = $('#change_phone_2');
        var val = parent.find('input.sms-code').val();
        if(parent.find('.has-error').lenght > 0 || !val || val == '') {
            return false;
        }

        $.ajax({
            url: window.location.protocol + "//" + window.location.host + "/user/change-phone-confirm",
            type: "POST",
            data: {'sms_code' : val, 'phone' : new_phone},
            success: function (data) {
                data = JSON.parse(data);
                if(data.status == 0) {
                    parent.find('.field-edituserfrom-sms_code').addClass('has-error');
                    parent.find('.field-edituserfrom-sms_code .help-block').html(data.message);
                }
                if(data.status == 1) {
                    $('#change_phone_1').hide();
                    $('#change_phone_2').hide();
                    $('#change_phone_3').show();
                    $('#edituserfrom-phone').val(new_phone);

                }
            }
        });
    });

    $(document).on('DOMSubtreeModified', '.settings-withdrawal__item--title', function() {
       showChangeSystemButton();
    });

    $(document).on('change', '#edituserfrom-payment_address', function() {
        showChangeSystemButton();
    });

    function showChangeSystemButton() {
        var payment_system = $('.settings-withdrawal__item--list a.active');
        if(payment_system.length < 1) {
            return false;
        }
        var val = $('#edituserfrom-payment_address').val();
        if(!val || val == '') {
            $('#payment_step1').hide();
            return false;
        }
        if(val != user_phone) {
            $('#payment_step1').show();
        } else {
            $('#payment_step1').hide();
        }
    }

    var new_system = false,
        new_address = false;

    $(document).on('click', '#payment_step1', function (e) {
        e.preventDefault();
        new_system = $('.settings-withdrawal__item--list a.active').data('system_id');

        $('#edituserfrom-payment_address').attr('disabled', true);
        new_address = $('#edituserfrom-payment_address').val();
        $.ajax({
            url: window.location.protocol + "//" + window.location.host + "/user/change-payment-sms",
            type: "POST",
            success: function (data) {
                data = JSON.parse(data);
                $('#payment_step1').hide();
                if(data.status == 1) {
                    $('#payment_step2').show();
                } else {
                    $('.field-edituserfrom-payment_address').addClass('has-error');
                    $('.field-edituserfrom-payment_address .help-block').html(data.message);
                    $('#edituserfrom-payment_address').attr('disabled', false);
                }

            }
        });
        return false;
    });

    $(document).on('click', '#payment_step2', function (e) {
        e.preventDefault();
        var parent = $('#payment_step2');
        var val = parent.find('input.sms-code').val();
        if(parent.find('.has-error').lenght > 0 || !val || val == '') {
            return false;
        }

        $.ajax({
            url: window.location.protocol + "//" + window.location.host + "/user/change-payment-confirm",
            type: "POST",
            data: {'sms_code' : val, 'address' : new_address, 'system_id' : new_system},
            success: function (data) {
                data = JSON.parse(data);
                if(data.status == 0) {
                    parent.find('.field-edituserfrom-sms_code').addClass('has-error');
                    parent.find('.field-edituserfrom-sms_code .help-block').html(data.message);
                }
                if(data.status == 1) {
                    $('#payment_step2').hide();
                    $('#payment_step1').hide();
                    $('#payment_step3').show();

                }
            }
        });
    });

    $(document).on('click', 'a.payments__link-item', function (e) {
        e.preventDefault();
        if(!$(this).hasClass('active')) {
            $(document).find('.deposit_block').toggle();
        }
    });


    $(document).on('keyup', '#cashoutform-summ', function () {
        var summ = $(this).val();
        if($.isNumeric(summ)){
            var new_summ = number_format(summ - summ * (withdraw_fee / 100) ,2 , '.', ' ');
            $(document).find('.deposit-payments-form__info--item__amount.withdraw_fee').html(new_summ + ' $');
        } else {
            $(document).find('.deposit-payments-form__info--item__amount.withdraw_fee').html('0 $');
        }

    })

    if($(document).find('.selected_withdraw').length > 0) {
        var system_id = $(document).find('.payments-items.withdraws .active').data('withdraw_id');
        var system = withdraw_systems[system_id];
        withdraw_rate = system.currency_rate;
        withdraw_currency = system.currency_name;
        withdraw_fee = system.fee;
        if(user_verified) {
            withdraw_fee = system.fee_verified;
        }
    }

});


var new_bets_notification = $('.new_bets.notification-item');

function getBets() {
    var new_messages =  $(document).find('.new_message_notification');
    $.ajax({
        url: window.location.protocol + "//" + window.location.host + "/user/get-bets",
        type: "POST",
        success: function (data) {
            data = JSON.parse(data);
            if(data.new_bets) {
                new_bets_notification.show();
                new_bets_notification.html(data.new_bets + '<div class="notification"><span></span></div>');
                if(data.new_messages && data.new_messages != '0') {
                    new_messages.addClass('show_notification');
                    new_messages.find('span').html(data.new_messages);
                    $('span.new_message_notification').html(data.new_messages);
                } else {
                    new_messages.removeClass('show_notification');
                }
            }
        }
    });
}

setInterval(getBets, 5000);

$(document).on('click', '#yes_change_my_objective', function (e) {
    e.preventDefault();
    $('.current_objective').hide();
    $('.new_objective').show();
    $.ajax({
        url: window.location.protocol + "//" + window.location.host + "/user/delete-objective",
        type: "POST",
    });
    $('#changeMyObjective').modal('hide')
});

$(document).on('click', '#withdraw_partner_balance', function (e) {
    e.preventDefault();
    $.ajax({
        url: window.location.protocol + "//" + window.location.host + "/user/withdraw-balance-partner",
        type: "POST",
        success: function (data) {
            data = JSON.parse(data);
            if(data.status == 2) {
                $(document).find('.personal-balance__item--balance').html('0<span>$</span>');
            }
            $('#withdrawPartnerBalance').find('.change-item.message').html(data.message);
            $('#withdrawPartnerBalance').modal('show');
        }
    });
});

if($('.modal-steps-background').length > 0) {
    function showFirstBanner() {
        $('body').css({'overflow' : 'hidden'});
        $('.modal-steps-background').show();
        $.ajax({
            url: window.location.protocol + "//" + window.location.host + "/user/first-banner-shown",
            type: "POST",
        });
    }
    $('.modal-steps-background').on('click', 'button.close', function () {
        $('.modal-steps-background').hide();
        $('body').css({'overflow' : 'auto'});
    });
    setTimeout(showFirstBanner, 7000);

}


$(document).on('click', '.navbar-toggler', function () {
    if($(this).hasClass('collapsed')) {
        $("body").css({"overflow":"initial", 'position':'initial'});
    } else {
        $("body").css({"overflow":"hidden", 'position':'fixed'});

    }
});



$(document).on('submit', '#formSevenBonus', function (e) {
    e.preventDefault();
    var form = $(this),
        vk = form.find('#userbonusrequest-vk').val(),
        instagram = form.find('#userbonusrequest-instagram').val();
    if(!vk || !instagram) {
        return false;
    }

    
    $.ajax({
        url: window.location.protocol + "//" + window.location.host + "/user/get-seven-bonus",
        data: {'vk' : vk, 'instagram' : instagram},
        type: "POST",
        success: function (data) {
            data = JSON.parse(data);
            if(data.status == 2) {
                form.find('#bonusbuttons p').show();
                form.find('#bonusbuttons button').hide();
                $('#userbonusrequest-vk').attr('disabled', true);
                $('#userbonusrequest-instagram').attr('disabled', true);
            }
        }
    });

})


