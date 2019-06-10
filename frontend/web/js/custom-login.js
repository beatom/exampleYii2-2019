var theLanguage = $('html').attr('lang');

jQuery(document).ready(function($) {

    $('.go_more').click(function () {
        var href = $(this).find('a.u-btn-more').attr('href');
        if(href)
            window.location.href = href;
    });

    var hash = window.location.hash;
    if(hash.indexOf('email') > 0 ) {
        var csrfToken = $('meta[name="csrf-token"]').attr("content");
        $.ajax({
            url: '/site/ajax',
            data: {
                action: 'vk_login',
                hash:   hash,
                _csrf: csrfToken
            },
            type: 'POST',
            success: function (data) {
                data = JSON.parse(data);

                if (data.success == false ) {
                    alert(data.message);
                }
                else  {
                    window.location.href = '/user/index';
                }
            }
        });
    }


    /*------------------------------------------------------------*/
    /*загрузка документов при редактировании пользователя паспорта*/
    $('#edituserfrom-pasport_1').change(function (e) {
        setimage( 'edituserfrom-pasport_1' );
    });
    $('#edituserfrom-pasport_2').change(function (e) {
        setimage( 'edituserfrom-pasport_2' );
    });

    function setimage( id_input ) {
        var $input = $("#" + id_input );
        var fd = new FormData;

        fd.append(id_input, $input.prop('files')[0]);

        $.ajax({
            url: '/user/ajax',
            data: fd,
            processData: false,
            contentType: false,
            type: 'POST',
            beforeSend: function (data) {
                $('.loading').show();
                $('[name="mydata"]').hide();
            },
            success: function (data) {
                $('.loading').hide();
                $('[name="mydata"]').show();
                data = JSON.parse(data);
                if(data.success){
                    $('#'+ id_input + '_img').attr('src', data.name);
                }
            }
        });
    }

    /*----------------------------------------------*/
    /*смс код при регистрации*/
    $('#get-registration-sms').click(function () {
        if($(this).hasClass('sms_blocked')) {
            return false;
        }
        var phone = $('#signupformstep2form-phone').val();
        var syte = window.location.protocol + '//' + window.location.hostname;

        $.ajax({
            url: syte + '/ajax',
            type: "post",
            data: {
                data: phone,
                action: 'sendSMS'
            },
            // beforeSend: function (data) {
            //     $('.loading').show();
            //     $('[name="signup-button"]').hide();
            //     $('#get-registration-sms').hide();
            //
            // },hide
            success: function(data){  //функция ответа
                data = JSON.parse(data);
                $('#sms_code_info').html(data.message);
                $('#sms_code_info').show();
                if(data.block_sms_button) {
                    $('#get-registration-sms').addClass('sms_blocked');
                }
            }
        });
    });

    $('#go_step_two').click(function (e) {
        e.preventDefault();

        var phone = $('#signupformstep2form-phone').val();
        var sms_code = $('#signupformstep2form-sms_code').val();
        var username = $('#signupformstep2form-username').val();

        var syte = window.location.protocol + '//' + window.location.hostname;

        $.ajax({
            url: syte + '/signup_step',
            type: "post",
            data: {
                phone: phone,
                sms_code: sms_code,
                username: username,
            },
            success: function(data){  //функция ответа
                data = JSON.parse(data);
                if(!data.success){
                    $.each(data.errors, function (key, value) {
                        $(document).find('.field-signupformstep2form-' + key).addClass('has-error');
                        $(document).find('.field-signupformstep2form-' + key + ' p.help-block-error').html(value)
                    })
                } else {
                    $('.form-item--title').html('Завершение регистрации');
                    $('.registration.step-one').hide();
                    $('.registration.step-two').show();
                }
            }
        });
    });

    $('#signupform-sms_code').keyup(function () {
        $('.loading').hide();
        $('[name="signup-button"]').show();
        $('[name="get-sms-code"]').show();
    });
    /*----------------------------------------*/

    /* для логина только английские, цифры и _ */
    $('#loginform-username, #signupform-username').keyup(function (e) {
        var text = $(this).val();
        text = coolLogin(text);
        $(this).val(text);
    });
    $('#loginform-username, #signupform-username').change(function (e) {
        var text = $(this).val();
        text = coolLogin(text);
        $(this).val(text);
    });
    function coolLogin( text ) {
        text = text.match(/[\w]/gi);
        text = text.join('');
        return text;
    }

    $('#loginform-username').keydown(function (e) {
        var code = e.keyCode || e.which;
        if (code == '9') {
            var tab_target = $('form#login-form input#loginform-password');
            tab_target.focus();
        }

    });

    $('form#login-form input#loginform-username').focus();
});
