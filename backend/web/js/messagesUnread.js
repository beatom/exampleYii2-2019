    function unread() {
        var unreadBadge = $("li.messages-menu a span.unread");
        var curUnread = 0;
        if (unreadBadge.length) {
            curUnread = unreadBadge.html();
            badgeExists = true;
        }

        $.ajax({
            url: window.location.protocol + "//" + window.location.host + "/messages/unread-messages",
            type: "POST",
            data: {},
            success: function (data) {
                if (data != curUnread) {
                    if (data > 0) {
                        unreadBadge.html(data);
                    } else  {
                        unreadBadge.html('');
                    }
                }

            }
        });
    }

    setInterval(unread, 5000);

    jQuery(document).ready(function ($) {
        unread();
        var companionChats = $('.chats a.companion-chat');
        $("#companionSearch").keyup(function (e) {
            companionChats.each(function () {
                $(this).hide();
            });

            $('#find-no-matches').hide();
            var fieldValue = $("#companionSearch").val().toLowerCase();

            if (fieldValue !== '') {
                $.ajax({
                    url: window.location.protocol + "//" + window.location.host + "/messages/find-companions",
                    type: "POST",
                    data: {'sender' : senderId, 'search' : fieldValue},
                    success: function (data) {
                        $('.chats').find('a.search-companion').each(function () {
                            $(this).remove();
                        });
                        var response = JSON.parse(data);
                        if (response.status === 'Ok') {
                            $.each(response.chats, function (index, value) {
                                var unread = '';
                                var lastmsg = ' ';
                                if(value.unreadmessages > 0) {
                                    unread =value.unreadmessages;
                                }
                                if(value.lastmessage) {
                                    lastmsg = value.lastmessage;
                                }
                                var companionName = '';
                                if (value.companion.firstname != null) {
                                    companionName = value.companion.firstname;
                                }
                                if (value.companion.lastname != null) {
                                    companionName = companionName + ' ' + value.companion.lastname;
                                }
                                if(value.companion.firstname == null && value.companion.lastname == null) {
                                    companionName = value.companion.username
                                }


                                $(".chats.col-lg-5").append('<a class="companion-chat search-companion" href="/messages/'+senderId+'/'+value.id+'" style=""><div class="user chat">' +
                                    '<div class="avatar">' +
                                    '<img src="'+frontendUrl+value.companion.avatar+'">' +
                                    '</div><div class="info">' +
                                    '<p><span class="companion-name">'+companionName +' </span>' +
                                    '<span class="companion-id">id:'+value.companion.id+'</span>' +
                                    '<span class="badge">'+unread+'</span></p>'+
                                    '<span class="last-message">'+lastmsg+' ('+value.last_update+')</span>' +
                                    '</div></div></a>');
                            });
                        } else {
                            $('#find-no-matches').show();
                        }

                    }
                });
            } else {
                companionChats.each(function () {
                    $(this).show();
                });
            }
        });

        $("#usersSearch").keyup(function (e) {
            $('#modal-users-search').find('a.search-user').each(function () {
                $(this).remove();
            });
            $('#find-users-no-matches').hide();
            var fieldValue = $("#usersSearch").val().toLowerCase();

            if (fieldValue !== '') {
                $.ajax({
                    url: window.location.protocol + "//" + window.location.host + "/messages/find-users",
                    type: "POST",
                    data: {'sender' : senderId, 'search' : fieldValue},
                    success: function (data) {
                        var response = JSON.parse(data);
                        if (response.status === 'Ok') {
                            $('#modal-users-search').find('a.search-user').each(function () {
                                $(this).remove();
                            });

                            $.each(response.users, function (index, value) {

                                var companionName = '';
                                if (value.firstname != null) {
                                    companionName = value.firstname;
                                }
                                if (value.lastname != null) {
                                    companionName = companionName + ' ' + value.lastname;
                                }
                                if(value.firstname == null && value.lastname == null) {
                                    companionName = value.username
                                }

                                $("#modal-users-search").append('<a class="companion-chat search-user" href="/messages/make-chat/'+senderId+'/'+value.id+'" style=""><div class="user chat">' +
                                    '<div class="avatar">' +
                                    '<img src="'+frontendUrl+value.avatar+'">' +
                                    '</div><div class="info">' +
                                    '<p><span class="companion-name">'+companionName +' id:'+value.id+'</span></p>'+
                                    // '<span class="last-message">'+value.lastmessage+' '+value.last_update+'</span>' +
                                    '</div></div></a>');
                            });
                        } else {
                            $('#find-users-no-matches').show();
                        }

                    }
                });
            }
        });

    })