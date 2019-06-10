jQuery(document).ready(function ($) {
    var d = $('#chat');
    d.scrollTop(d.prop("scrollHeight"));

    $("#chatmessage-text").keypress(function (e) {
        if (e.which == 13) {
            sendmessage();
        }
    });


    function reciveMessages() {
        $.ajax({
            url: window.location.protocol + "//" + window.location.host + "/messages/recive-messages",
            type: "POST",
            data: {'chat_id': chatId, 'sender': senderId},
            success: function (data) {
                var response = JSON.parse(data);
                if (response.status === 'Ok') {
                    if(response.messages) {
                        $.each(response.messages, function (index, value) {
                            $("#chat").append('<div class="row message ">' +
                                '<div class="col-md-12">' +
                                '<span class="text">' + value.text + '</span>' +
                                '<span class="time">' + value.date_send + '</span></div></div>');
                        });
                        unread();
                        var d = $('#chat');
                        d.scrollTop(d.prop("scrollHeight"));
                    }
                }

            }
        });
    }

    setInterval(reciveMessages, 5000);

    $("#chat").on('click', "#more-messages", function () {
        $(this).remove();
        var messagesCount = $('#chat').find('div.message').length;
        var lastMessageId = $('#chat').find("div.message").first().data("messageid");
        $.ajax({
            url: window.location.protocol + "//" + window.location.host + "/messages/load-messages",
            type: "POST",
            data: {'messagesCount': messagesCount, 'chat_id': chatId, 'lastMessage': lastMessageId, 'sender': senderId},
            success: function (data) {
                var response = JSON.parse(data)
                if (response.status === 'Ok') {
                    var olddayBage = '';
                    $.each(response.messages, function (index, value) {
                        if (value.dayBadge) {
                            var dayBage = '<p class="time-badge" data-datecode="' + value.dayBadge.datecode + '">' + value.dayBadge.word + '</p>';
                            if (dayBage !== olddayBage) {
                                var lastBage = $('#chat').find("p.time-badge").first();
                                if (lastBage.data("datecode") == value.dayBadge.datecode) {
                                    lastBage.remove();
                                }
                                $("#chat").prepend(olddayBage);
                                olddayBage = dayBage;
                            }
                        }

                        if (value.user_id == senderId) {
                            $("#chat").prepend('<div class="row message right" data-messageid="' + value.id + '">' +
                                '<div class="col-md-12">' +
                                '<span class="time">' + value.date_send + '</span>' +
                                '<span class="text">' + value.text + '</span>' +
                                '</div></div>');
                        } else {
                            $("#chat").prepend('<div class="row message " data-messageid="' + value.id + '">' +
                                '<div class="col-md-12">' +
                                '<span class="text">' + value.text + '</span>' +
                                '<span class="time">' + value.date_send + '</span></div></div>');
                        }

                    });
                    $("#chat").prepend(olddayBage);
                    if (response.hasMore) {
                        $("#chat").prepend('<a id="more-messages" class="time-badge">Больше сообщений</a>');
                    }
                }
            }
        });
    });


})

function sendmessage() {
    var text = $("#chatmessage-text").val();
    if (text == '') {
        return false;
    }
    $.ajax({
        url: window.location.protocol + "//" + window.location.host + "/messages/send-message",
        type: "POST",
        data: {'text': text, 'chat_id': chatId, 'sender': senderId},
        success: function (data) {
            var response = JSON.parse(data)
            if (response.status === 'Ok') {
                $('#noMessagesInChat').remove();
                $("#chatmessage-text").val('');
                $("#chat").append('<div class="row message right" data-messageid="' + response.message_id + '"> ' +
                    '<div class="col-md-12">' +
                    '<span class="time">' + response.time + '</span>' +
                    '<span class="text">' + response.message + '</span></div></div>');
                var d = $('#chat');
                d.scrollTop(d.prop("scrollHeight"));
                $("#chatmessage-text").val('');
                $("#chatmessage-text").parent().find('div.redactor-editor').empty();
            }
        }
    });
    return false;
}


$("#chat").on('click', " .col-md-12 span.text", function () {
   $(this).toggleClass('selected_message');
    countSelected();
});
var delete_button = $(document).find('#delete_messages');
countSelected();
var selected_messages = 0;
function countSelected() {
    selected_messages = $(document).find('.col-md-12 span.text.selected_message').length;
    if(selected_messages > 0) {
        delete_button.prop( "disabled", false );
    } else {
        delete_button.prop( "disabled", true );
    }
}

function deleteMessages() {
    countSelected();
    if(selected_messages <= 0) {
        return;
    }
    if(!confirm('Вы дейстивтельно хотите удалить '+selected_messages+' сообщний(е)?')) {
        return;
    }
    var messages = [];
    var ids = [];
    $(document).find('.col-md-12 span.text.selected_message').each(function( index ) {
        var parent = $(this).parents('.row.message');
        messages.push(parent);
        ids.push(parent.data('messageid'));
    });

    $.ajax({
        url: window.location.protocol + "//" + window.location.host + "/messages/delete-messages",
        type: "POST",
        data: {'chat_id': chatId, 'ids': ids},
        success: function (data) {
            var response = JSON.parse(data);
            if (response.status === 'Ok') {
                $.each(messages, function( index ) {
                      $(this).remove();
                });
            }
            countSelected();
        }
    });

}


