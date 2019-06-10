var days = 7;
var investments = $('tbody.investors');
var invests = $('tbody.investments');
var days_table = $('tbody.days_table');
var investors = investments.length;
var newLine = $(document).find('.new-investment');

var trader_end = $(document).find('tr.investment.trader .balance');

$('select#trading_period').on('change', function () {
    days = this.value * 7;
})

function addnewinvestor() {

    investors++;
    var newInvestor = '<tr class="investment trader" data-id="' + investors + '">' +
        '<td class="name">Инвестор ' + investors + '</td>' +
        '<td class="first__day">' + days + '</td>' +
        '<td class="invested">0</td>' +
        '<td class="balance">0</td>' +
        '<td class="profit">0</td>' +
        '<td class="trader_fee">0</td>' +
        '<td class="partner_fee">0</td>' +
        '</tr>';
    investments.append(newInvestor);
    generateInvestors()
}

function generateDays() {
    var string = '<select class="day_picker">';
    for (var i = 1; i <= days; i++) {
        string = string + '<option value="' + i + '">День ' + i + '</option>';
    }
    ;
    string = string + '</select>';
    return string;
}

function generateInvestors() {
    var string = '<select>';
    investments.find('tr').each(function (index) {
        var name = $(this).find('.name').html();
        string = string + '<option value="' + (index + 1) + '">' + name + '</option>';
    });
    string = string + '</select>';
    var fields = $(document).find('.investor-select');
    fields.each(function (index) {
        $(this).empty();
        $(this).append(string);
    });
    newLine = $(document).find('.new-investment');

}

function generateDaysTable() {
    days_table.empty();
    for (var i = 1; i <= days; i++) {
        var row = '<tr data-day="' + i + '">' +
            '<td>' + i + '</td>' +
            '<td class="day__start">' + 0 + '</td>' +
            '<td class="day__add">' + 0 + '</td>' +
            '<td class="day__invest">' + 0 + '</td>' +
            '<td class="day__profit"><input value="0" class="day_profit"></td>' +
            '<td class="day__end">0</td>' +
            '<td class="percent">0</td>' +

            '</tr>';

        days_table.append(row);
    }
    ;
    updateDaysPickers();
    newLine = $(document).find('.new-investment');
}


function regenerateDaysTable() {
    if (days_table.find('tr').length == 0) {
        generateDaysTable();
    }
    var prev_end = 0;
    var prev_invest = 0;
    days_table.find('tr').each(function (i) {
        var rowDay = $(this).data('day');
        var profit = $(this).find('input.day_profit').val();
        var day_start = parseFloat(prev_end) + parseFloat(prev_invest);
        var day_add = 0;
        var percent = 0;
        var day_invest = 0;
        var day_end = 0;
        invests.find('tr').each(function (index) {
            if ($(this).data('day') == rowDay) {
                if ($(this).data('investor') == 1) {
                    day_add += parseFloat($(this).data('summ'));
                } else {
                    day_invest += parseFloat($(this).data('summ'));
                }
            }
        });
        day_end = parseFloat(day_start) + parseFloat(profit) + parseFloat(day_add);
        percent = parseFloat(day_end) / ((parseFloat(day_start) + parseFloat(day_add)) / 100) - 100;
        prev_end = day_end;
        prev_invest = parseFloat(day_invest);
        $(this).find('.day__start').html(day_start);
        $(this).find('.day__add').html(day_add);
        $(this).find('.day__invest').html(day_invest);
        $(this).find('.day__end').html(day_end);
        $(this).find('.percent').html(percent);

    });
    updateDaysPickers();
    newLine = $(document).find('.new-investment');
}

function updateDaysPickers() {
    var fields = $(document).find('.daypicker');
    var day_picker = generateDays();
    fields.each(function (index) {
        $(this).empty();
        $(this).append(day_picker);
    });
}


function newInvestment() {
    var summ = $(document).find('#newinvestment').val();
    if (summ == 0) {
        alert('Сумма инвестиции не может быть равна 0');
    } else {
        var investor = $(document).find('.investor-select select').val();
        var invest_day = $('.new-investment').find('.daypicker select').val();
        addInvestment(investor, invest_day, summ);

    }

}

function addInvestment(investor, day, summ) {
    var invest_investor = investments.find('[data-id="' + investor + '"] .name').html();
    var newinline = '<tr data-day="' + day + '" data-investor="' + investor + '" data-summ="' + summ + '"><td>' + invest_investor + '</td><td>День ' + day + '</td><td>' + summ + '</td></tr>';
    invests.append(newinline);
    invests.append(newLine);
    regenerateDaysTable();

    investments.find('tr').each(function (index) {
        if ($(this).data('id') == investor) {
            var invested = parseFloat($(this).find('.invested').html());
            $(this).find('.invested').html(parseFloat(invested) + parseFloat(summ));
            if ($(this).find('.first__day').html() > day) {
                $(this).find('.first__day').html(day);
            }
        }
    });
}

updateDaysPickers();


function rollover() {
    regenerateDaysTable();
    var honorar = $(document).find('#honorar').val();
    var honorar_partner = $(document).find('#honorar_partner').val();
    var responsibility = $(document).find('#responsibility').val();
    var manager_revard = 0;
    investments.find('tr').each(function (index) {
        var inv_id = $(this).data('id');
        var summ_start = 0;
        var prev_add = 0;
        var summ_add = 0;
        var summ_end = 0;
        var start_date = $(this).find('.first__day').val();
        days_table.find('tr').each(function (index) {
            if ($(this).data('day') >= start_date) {
                var profit_terminal = $(this).find('.percent').html();
                summ_add = countDay(inv_id, $(this).data('day'));
                if (inv_id == 1) {
                    summ_start = parseFloat(summ_end) + parseFloat(summ_add);
                } else {
                    summ_start = parseFloat(summ_end) + parseFloat(prev_add);
                }
                prev_add = parseFloat(summ_add);
                summ_end = parseFloat(summ_start) + parseFloat(summ_start) * parseFloat((parseFloat(profit_terminal) / 100));
            }
        });
        var invested = $(this).find('.invested').html();
        var real_profit = 0;
        var trader_fee = 0;
        var partner_fee = 0;
        if (inv_id != 1) {
            if (summ_end > invested) {
                real_profit = parseFloat(summ_end) - parseFloat(invested);
                trader_fee = parseFloat(real_profit) * parseFloat((parseFloat(honorar)/100));
                partner_fee = parseFloat(trader_fee) * parseFloat((parseFloat(honorar_partner)/100));
                trader_fee = parseFloat(trader_fee) - parseFloat(partner_fee);
                var dfgdfg = trader_end.html();
                trader_end.html(parseFloat(dfgdfg) + parseFloat(trader_fee));
                summ_end = parseFloat(invested) + parseFloat(real_profit) - parseFloat(trader_fee) - parseFloat(partner_fee);
            } else {
                real_profit = parseFloat(invested) - parseFloat(summ_end);
                var compensation = parseFloat(real_profit) * parseFloat((parseFloat(responsibility)/100));
                var dfgdfg = trader_end.html();
                trader_end.html(parseFloat(dfgdfg) - parseFloat(compensation));
                summ_end = parseFloat(invested) - parseFloat(real_profit) + parseFloat(compensation);
            }
        }


        $(this).find('.balance').html(summ_end)
        $(this).find('.trader_fee').html(trader_fee);
        $(this).find('.partner_fee').html(partner_fee);


    });


}


function countDay(user, day) {
    var ret = 0;
    invests.find('tr').each(function (index) {
        if ($(this).data('day') == day && $(this).data('investor') == user) {
            ret += $(this).data('summ');
        }
    });
    return ret;
}

