var socket_host = window.location.host + '/wss2/',
    socket_prefix = 'wss://';

if (window.location.host == 'invest.local') {
    socket_host = '127.0.0.1:8088';
    socket_prefix = 'ws://';
}

var user_id = $('#chat__user_id').val(),
    session_id = $('#chat__session_id').val(),
    chat = $('#chat_list'),
    chat_div = $('.chat'),
    is_moderator = $('#chat__moderator');

var notification_stack = [];

var conn = new ab.Session(socket_prefix + socket_host,
    function (b) {
        console.log(b);
        // eventMonitoring идентификатор, который мы передаём в push класс.
        conn.subscribe('eventMonitoring', function (topic, data) {
            data = data.data;
            console.log(data);
            if (data.type == 'add') {
                addNewMessage(data);
            }
            ;
            if (data.type == 'delete') {
                removeMessages(data);
            }
            if (data.type == 'update') {
                updateMessage(data);
            }

        }, 'test');

        conn._
    },
    function () {
        console.warn('WebSocket connection closed');
    },
    {'skipSubprotocolCheck': true}
);


function sendMessage() {
    if (!checkAuthenticate()) {
        if (confirm('Для отправки сообщения необходимо авторизироватся')) {
            window.location = window.location.protocol + '//' + window.location.hostname + '/login';
        }
        return false;
    }
    var message = $('#chat__message_input').val(),
        parent_id = $('#chat__parent_id').val();
    if (message == '' || message == undefined) {
        console.log('Message empty');
        return false;
    }

    if (!parent_id) {
        parent_id = null;
    }
    $('#chat__message_input').val('');
    $('#chat__parent_id').val('');
    $.ajax({
        url: window.location.protocol + "//" + window.location.host + "/user/send-chat-message",
        type: "POST",
        data: {'message': message, 'parent_id': parent_id}
    });
    //  send('message', {'message' : message, 'parent_id' : parent_id});
}

function markMessage(message_id, mark) {
    if (!checkAuthenticate()) {
        if (confirm('Для оценки комментария необходимо авторизироватся')) {
            window.location = window.location.protocol + '//' + window.location.hostname + '/login';
        }
        return false;
    }
    if (!mark) {
        mark = false;
    } else {
        mark = true;
    }
    $.ajax({
        url: window.location.protocol + "//" + window.location.host + "/user/mark-chat-message",
        type: "POST",
        data: {'message_id': message_id, 'like': mark}
    });
    // send('mark', {'message_id': message_id, 'like': mark});
}

function updateMessage(message_id, mark) {
    if (!mark) {
        mark = false;
    } else {
        mark = true;
    }
    send('update', {'message_id': message_id, 'like': mark});
}

function deleteMessage(message_id) {
    send('delete', {'message_id': message_id});
}

function send(type, message) {

    data = {};
    data.user_id = user_id;
    data.session_id = session_id;
    data.data = message;
    console.log(data);
    if (!conn._websocket_connected) {
        return false;
    }
    conn.publish(type, data);
}


function checkAuthenticate() {
    if (!user_id) {
        return false;
    }
    return true;
}
// conn.publish('eventMonitoring', {'test' : '1'})

const social_icos = {
    'facebook': 'facebook-ico',
    'vk': 'vk-ico',
    'instagram': '',
    'skype': '',
    'whatsapp': '',
    'twitter': '',
    'telegram': 'telegram',
};

const instagram_ico = '<div class="instagram-ico"></div>';


function addNewMessage(data) {

    var row = '<div id="m' + data.id + '" class="chat-items chat__message" data-message_id="' + data.id + '" data-name="' + data.user.name + '">' +
        '<div class="chat-item__user-info"><img class="chat-item__user-info__avatar rounded-circle" src="' + data.user.avatar + '" alt="">' +
        '<div class="chat-item__user-info__message-count">сообщений:<span>' + data.user.messages_count + '</span></div>' +
        '</div>' +
        '<div class="chat-item__body-items">' +
        '<div class="chat-item__header">' +
        '<div class="chat-item__user-name">' + data.user.name_string + '</div>';
    if (data.parent_name && data.parent_name != '') {
        row += '<div class="chat-item__reply-to">@' + data.parent_name + '</div>';
    }
    row += '<div class="chat-item__social-list">';
    row += addSocialToString(data.user.social) + '</div>';
    if (data.user.verified) {
        row += '<div class="chat-item__registration-info" data-toggle="tooltip" data-placement="top" title="Данный пользователь является клиентом invest"><img src="/img/svg/verification-ico.svg" alt=""></div>';
    }

    row += '<div class="chat-item__balance">Баланс депозита:<span>' + data.user.balance + '$</span></div></div>' +
        '<div class="chat-item__body"><p>' + data.text + '</p>' +
        '<div class="chat-item__body--links">' +
        '<div class="chat-item__body--rating-items">' +
        '<div class="chat-item__body--rating like">' +
        '<svg class="ico">' +
        '<use xlink:href="/img/sprites/sprite.svg#like"></use>' +
        '</svg>' + data.likes +
        '</div>' +
        '<div class="chat-item__body--rating dislike">' +
        '<svg class="ico">' +
        '<use xlink:href="/img/sprites/sprite.svg#dislike"></use>' +
        '</svg>' + data.dislikes +
        '</div>' +
        '</div>' +
        '<div class="chat-item__body--posted">только что</div>';
    if (user_id != data.user.id) {
        row += '<a class="chat-item__body--edit chat__reply" href="#">Ответить</a>';
    }
    row += '</div></div></div>';
    if (data.parent_id) {
        if ($(document).find('#b' + data.branch_id).length < 1) {
            $(document).find('#m' + data.branch_id).after('<div id="b' + data.branch_id + '" class="reply-list">');
            $(document).find('#b' + data.branch_id).append(row);
        } else {
            if ($(document).find('#b' + data.branch_id + ' .show-answer').length > 0) {
                $(document).find('#b' + data.branch_id + ' .show-answer').append(row);
            } else {
                $(document).find('#b' + data.branch_id).append(row);
            }

        }
    } else {
        if(chat.find('.chat-items.chat__message').lenght < 1) {
            chat.empty();
        }
        chat.append(row);
    }

    if (!data.branch_id) {
        notification_stack.push(truncateString(data.text, 40));
    }
    if (data.user.id == user_id && !data.parent_id) {
        chat_div.scrollTop(chat_div.prop('scrollHeight'));
    }
}

function updateMessage(data) {
    var message = $(document).find('#m' + data.id);
    if (message.length < 1) {
        return false;
    }
    message.find('.chat-item__body--rating.like').html('<svg class="ico">' +
        '<use xlink:href="/img/sprites/sprite.svg#like"></use>' +
        '</svg>' + data.likes +
        '</div>');
    message.find('.chat-item__body--rating.dislike').html('<svg class="ico">' +
        '<use xlink:href="/img/sprites/sprite.svg#dislike"></use>' +
        '</svg>' + data.dislikes +
        '</div>');
    message.find('.chat-item__body p').html(data.text);

}

function removeMessages(ids) {
    $.each(ids, function (key, value) {
        $(document).find('#m' + value).remove();
        $(document).find('#b' + value).remove();
    });
}


//////////=================== chat
var notification_id = 1;
function showNotifications() {
    if (notification_stack.length > 0) {
        if ($(document).find('.chat-notification--item .chat-notification').length > 1) {
            return;
        }
        $.each(notification_stack, function (key, value) {
            if (notification_id % 2 == 0) {
                var row = '<div id="n' + notification_id + '" class="chat-notification wow animated fadeInUp" data-wow-delay="0.5s"><p>' + value + '</p></div>';
            } else {
                var row = '<div id="n' + notification_id + '" class="chat-notification wow next-message animated fadeInUp" data-wow-delay="0.5s"><p>' + value + '</p></div>';
            }

            $(document).find('.chat-notification--item').append(row);
            $("#n" + notification_id).delay(10000).fadeOut(1000, function () {
                $(this).remove();
            });
            notification_stack.splice($.inArray(value, notification_stack), 1);
            notification_id++;
            return false;
        })
    }
}

setInterval(showNotifications, 500);

function addSocialToString(social) {
    var row = '';
    $.each(social, function (key, value) {
        if (key == 'instagram') {
            row += '<li><a class="icons" target="_blank" href="' + value + '">' + instagram_ico + '</a></li>';

        } else {
            row += '<li><a class="icons" target="_blank" href="' + value + '"><svg><use xlink:href="/img/sprites/sprite.svg#' + social_icos[key] + '"></use></svg></a></li>';
        }
    });
    return row;
}

$(document).on('click', '#chat__message_send', function (e) {
    e.perventDefault;
    sendMessage();
    return false;
});

$(document).on('click', '#load_more_chat_items', function (e) {
    e.perventDefault;
    var button = $(this);
    var elements_count = $(document).find('#chat_list > .chat__message').length;
    $.ajax({
        url: window.location.protocol + "//" + window.location.host + "/site/get-more-chat-messages",
        type: "POST",
        data: {'e': elements_count},
        success: function (data) {
            data = JSON.parse(data);
            if (data.messages) {
                $(data.messages).insertAfter(button);
            }
            if (data.has_more == false) {
                console.log(1);
                button.remove();
            }
            console.log(data);
        }
    });
    return false;
});

$(document).on('click', '.chat__delete', function (e) {
    e.perventDefault;
    if(!confirm('Вы действительно хоитите удалить сообщение и все ответы на него?')){
        return false;
    }
    var message_id = $(this).closest('.chat__message').data('message_id');

    $.ajax({
        url: window.location.protocol + "//" + window.location.host + "/site/chat-delete",
        type: "POST",
        data: {'mi': message_id},
    });
    return false;
});

$(document).on('click', '.chat__reply', function (e) {
    e.perventDefault;
    var parent_id = $(this).closest('.chat__message').data('message_id'),
        parent_name = $(this).closest('.chat__message').data('name');
    $('#message_to span').html(parent_name);
    $('#message_to').show();
    $('#chat__parent_id').val(parent_id);
    return false;
});

$(document).on('click', '#message_to', function (e) {
    e.perventDefault;
    $(this).hide();
    $(this).find('span').html('');
    $('#chat__parent_id').val('');
    return false;
});


$(document).on('click', '.chat-item__body--rating', function () {
    if ($(this).hasClass('marked')) {
        return false;
    }
    var message_id = $(this).closest('.chat__message').data('message_id');
    if ($(this).hasClass('like')) {
        markMessage(message_id, 1);
    } else {
        markMessage(message_id, 0);
    }
    $(this).closest('.chat__message').find('.chat-item__body--rating').removeClass('marked');
    $(this).addClass('marked');
});

function getCaret(el) {
    if (el.selectionStart) {
        return el.selectionStart;
    } else if (document.selection) {
        el.focus();
        var r = document.selection.createRange();
        if (r == null) {
            return 0;
        }
        var re = el.createTextRange(), rc = re.duplicate();
        re.moveToBookmark(r.getBookmark());
        rc.setEndPoint('EndToStart', re);
        return rc.text.length;
    }
    return 0;
}

$('textarea#chat__message_input').keyup(function (event) {
    var isMobile = false; //initiate as false
// device detection
    if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
        || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0, 4))) {
        isMobile = true;
    }
    if (!isMobile && event.keyCode == 13) {
        var content = this.value;
        var caret = getCaret(this);
        if (event.ctrlKey) {
            this.value = content.substring(0, caret) + "\n" + content.substring(caret, content.length);
            event.stopPropagation();
        } else {
            this.value = content.substring(0, caret) + content.substring(caret, content.length);
            sendMessage();
        }
    }
});

$('.chat-notification--item').click(function () {
    $(".outer-indent, .container-fluid").css({"overflow":"hidden", 'position':'fixed'});
    $('.chat-item').addClass('active');
    $(document).find('body > jdiv').hide();
    chat_div.scrollTop(chat_div.prop('scrollHeight'));
});

$('html, body').on('touchstart touchmove', function(e) {
    if ($('#chat_window').hasClass('active')) {
        console.log(1111);
        e.preventDefault();

        return false;
    }
});