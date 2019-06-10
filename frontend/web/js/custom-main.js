var new_news_notification = false;


function getData() {
    $.ajax({
        url: window.location.protocol + "//" + window.location.host + "/site/get-data",
        type: "POST",
        headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr("content")},
        success: function (data) {
            data = JSON.parse(data);
            if(data.new_news !== '0' ) {
                new_news_notification.show();
                new_news_notification.find('.news__count').html(data.new_news);
            } else {
                new_news_notification.hide();
            }
        }
    });
}

$( document ).ready(function() {

    new_news_notification = $(document).find('#new_news_notification');
    getData();
    goToTag();
    $(document).on('click', '#invest_video_presentation .show-video__close,#invest_video_presentation .show-video__already-seen', function () {
        $.cookie('invest_video_presentation', $.now(), {
            expires: 1,
            path: '/',
        });
    });

    $(document).on('click', '[data-day]', function () {
        var id = $(this).data('day');
        var list = $(document).find('#day_events_list');
        list.empty();

        if(days_events[id].events.length === 0) {
            list.append('<tr><td colspan="6" style="text-align: center;">Новых событий пока нет</td></tr>');
        } else {
            var row;
            $.each(days_events[id].events, function (index, value) {
                row = '<tr>' +
                    '<td>' + value.title + '</td>' +
                    '<td>' + value.bank_percent + '</td>' +
                    '<td>' + value.bet + '</td>' +
                    '<td>' + value.coefficient + '</td>' +
                 //   '<td>' + value.bookmaker + '</td>' +
                    '<td class="result ' + value.result_class + '"><div class="result-position"></div></td>' +
                    '<td><a class="result-title nothing" href="#">';
                if(value.free) {
                    row +='<div class="unlock-rates" data-toggle="tooltip" data-placement="bottom" title="Ставка опубликована абсолютно бесплатно!"></div>';
                } else {
                    row +='<div class="lock-rates" data-toggle="tooltip" data-placement="bottom" title="Ставка доступна только для клиентов invest"></div>';
                }
                row +='</a></td>' +
                    '</tr>';
                list.append(row);
            });
        }

        var profit = days_events[id].symbol + '<div class="profit">' + days_events[id].sum + '</div><div class="persent">%</div>';
        $(document).find('#current_profit').html(profit);

        $(document).find('.profit__date').html(date('d.m',days_events[id].date + 10000));
    });

    setInterval(getData, 5000);
});

$(document).on('click', 'a.read-more', function (e) {
    e.preventDefault();
    var p = $(this).closest('.carousel__item--info__article'),
        t = p.data('text'),
        i = '';
    if(p.find('img').length > 0) {
        i = p.find('img');
    }
    p.empty();
    p.append(t);
    if(i !== '') {
        p.append(i);
    }
});

$(document).on('click', '.navbar-toggler', function (e) {
    e.preventDefault();
    var parent = $(this).closest('nav.navbar');
    if(parent.hasClass('nobg_header')) {
        if($(this).hasClass('collapsed')) {
            parent.removeClass('white__header');
        } else {
            parent.addClass('white__header');
        }
    }

});
