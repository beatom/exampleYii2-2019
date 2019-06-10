
$(document).on('click', '.change-password', function(e){
    if(!confirm('Вы действительно хотите задать новый пароль торговому счету?')){
        e.preventDefault();
    }
    return true;
});

$(document).ready(function() {
    var is_du = $('#is__du').val();
    var first_deposit = $('.first-deposit-user_id');
    var deposit_date = $('.deposit__date');

    $('.first__deposit').change(function() {
        if($(this).is(":checked")) {
            first_deposit.prop('disabled', true);
            delete_errors(first_deposit);
            deposit_date.prop('disabled', true);


        } else {
            $('.deposit__date').prop('disabled', false);
            if(is_du == 1) {
                $('.first-deposit-user_id').prop('disabled', false);
            }

        }
    });


    $('select#tradingaccount-is_du').on('change', function() {
        var type_acc = $('select#tradingaccount-type_account');
        if(this.value == 1) {
            type_acc.val(2);
            type_acc.prop('disabled', true);
        } else {
            type_acc.prop('disabled', false);
        }

    })
    

    //----------------/trade/account-------------------------------
    //export history
    $('[name="gethistoryterminal"]').click(function (e) {
        e.preventDefault();
        var date_from = $('[name="date_from"]').val();
        var date_to = $('[name="date_to"]').val();
        var valid_date = validation_date(date_from, date_to);
        if(!valid_date){
            return;
        }

        var input=document.createElement('input');
        input.type = 'hidden';
        input.name = 'gethistoryterminal';
        input.value = 1;

        var elem = document.getElementById ('historytrade');
        elem.appendChild(input);
        $('#historytrade').submit();
    });

    //export history
    $('[name="exporthistory"]').click(function (e) {
        e.preventDefault();
        var date_from = $('[name="date_from"]').val();
        var date_to = $('[name="date_to"]').val();
        var valid_date = validation_date(date_from, date_to);
        if(!valid_date){
            return;
        }

        var input=document.createElement('input');
        input.type = 'hidden';
        input.name = 'exporthistory';
        input.value = 1;

        var elem = document.getElementById ('historytrade');
        elem.appendChild(input);
        $('#historytrade').submit();
    });

    //del history
    $('[name="delhistory"]').click(function (e) {
        e.preventDefault();
        var date_from = $('[name="date_from"]').val();
        var date_to = $('[name="date_to"]').val();
        var mess = 'Вы ходите удалить историю c '+date_from;
        if(date_to){
            mess = mess +' по '+date_to;
        }
        mess = mess+'?';

        var valid_date = validation_date(date_from, date_to);
        if(!valid_date){
            return;
        }

        var res = confirm(mess);
        if(res){

            var input=document.createElement('input');
            input.type = 'hidden';
            input.name = 'delhistory';
            input.value = 1;

            var elem = document.getElementById ('historytrade');
            elem.appendChild(input);
            $('#historytrade').submit();
        }
    });
    //---------------------------------------------------

});

function validation_date(date_from, date_to) {
    if(!date_to || !date_from){
        alert('Укажите диапазон дат');
        return false;
    }
    if(date_to < date_from){
        alert('Не может конечная дата быть меньше начальной');
        return false;
    }
    return true;
}

function delete_errors(element) {
    var parent = element.closest('div.form-group');
    if(parent.hasClass('has-error')) {
        parent.removeClass('has-error');
        parent.find('div.help-block').hide();
    }
}

var traderOption = null;
var last_search_value = '';

$(".find-user__list").keyup(function (e) {
    var fieldValue = $(".find-user__list").val().toLowerCase();
    var list = $('#users__list');
    if(list.has('option.trader')) {
        traderOption = list.find('option.trader');
    }
    if (fieldValue !== '' && last_search_value != fieldValue) {
        last_search_value = fieldValue;
        $.ajax({
            url: window.location.protocol + "//" + window.location.host + "/site/find-users",
            type: "POST",
            data: {'search': fieldValue},
            success: function (data) {
                data = JSON.parse(data);
                list.empty();
                if (data.status === 'Ok') {
                    $.each(data.users, function (index, value) {
                        if(traderOption.val() == value.id) {
                            var newOption = '<option class="trader" value="'+value.id+'">'+value.string+' (управляющий)</option>';
                        } else {
                            var newOption = '<option value="'+value.id+'">'+value.string+'</option>';
                        }

                        list.append(newOption);
                    });
                } else {
                    list.append(traderOption);
                }
            }
        });
    }
});


$(document).on('click', ".start__solution", function (e) {
    e.preventDefault();
    var button = $(this);
    var solution_id = $('#solution_id').val();
        $.ajax({
            url: window.location.protocol + "//" + window.location.host + "/trade/check-solution-percent",
            type: "POST",
            data: {'solution_id': solution_id},
            success: function (data) {
                data = JSON.parse(data);
                if (data.status === 'Ok') {
                    window.location = button.attr('href');
                } else {
                    alert(data.message);
                }
            }
        });
});

$(document).on('click', ".edit__proportion", function (e) {
    e.preventDefault();
    var solution = $(this).closest('tr.solution-account');
    var solution_id = solution.data('id');
    var solution_proportion = solution.data('proportion');

    $('#solutiontrading-trading_account_id').val(solution_id);
    $('#solutiontrading-proportion').val(solution_proportion);
});

$(".find-users__all").keyup(function (e) {
    var fieldValue = $(".find-users__all").val().toLowerCase();
    var list = $('div.find-users-all__list');

    if (fieldValue !== '' && last_search_value != fieldValue) {
        last_search_value = fieldValue;
        $.ajax({
            url: window.location.protocol + "//" + window.location.host + "/messages/find-users-all",
            type: "POST",
            data: {'search': fieldValue},
            success: function (data) {
                data = JSON.parse(data);
                list.empty();
                if (data.status === 'Ok') {
                    $.each(data.users, function (index, value) {
                        var newOption = '<a class="companion-chat" href="/messages/'+value.id+'">\n' +
                            '                    <div class="user chat">\n' +
                            '                        <div class="avatar">\n' +
                            '                            <img src="'+value.avatar+'">\n' +
                            '                        </div>\n' +
                            '                        <div class="info">\n' +
                            '                            <p><span class="companion-name">'+value.name+' </span><span class="companion-id">id:'+value.id+'</span> <span class="badge">'+value.unread+'</span></p>\n' +
                            '                        </div>\n' +
                            '                    </div>\n' +
                            '                </a>';

                        list.append(newOption);
                    });
                }
            }
        });
    }
});


$(".find-accounts__all").keyup(function (e) {
    var fieldValue = $(".find-accounts__all").val().toLowerCase();
    var list = $('tbody.find-accounts-all__list');

    if (fieldValue !== '' && last_search_value != fieldValue) {
        last_search_value = fieldValue;
        $.ajax({
            url: window.location.protocol + "//" + window.location.host + "/trade/find-accounts-all",
            type: "POST",
            data: {'search': fieldValue},
            success: function (data) {
                data = JSON.parse(data);
                list.empty();
                if (data.status === 'Ok') {
                    $.each(data.users, function (index, value) {
                        var newOption = '<tr>'+
                            '<td>'+value.id+'</td>'+
                            '<td> <a href="/trade/account/'+value.id+'">'+value.name+'</a></td>'+
                            '<td><a href="/user/'+value.user_id+'">'+value.user_name+'</a></td>'+
                            '<td>'+value.date_add+'</td>'+
                            '<td>'+value.type+'</td>'+
                            '<td>'+value.is_du+'</td>'+ 
                            '<td>'+value.profit+'%</td>'+
                            '<td><a href="/trade/account/'+value.id+'">Подробнее</a></td></tr>';
                        list.append(newOption);
                    });
                }
            }
        });
    }
});


$(".find-account__list").keyup(function (e) {
    var fieldValue = $(".find-account__list").val().toLowerCase();
    var list = $('#accounts__list');
    var ids = [];
    var accountRows = $(document).find('tr.solution-account');
    accountRows.each(function( index ) {
        ids.push($(this).data('id'));
    });
    if (fieldValue !== '' && last_search_value != fieldValue) {
        last_search_value = fieldValue;
        $.ajax({
            url: window.location.protocol + "//" + window.location.host + "/trade/find-accounts-solution",
            type: "POST",
            data: {'search': fieldValue, 'ids' : ids},
            success: function (data) {
                data = JSON.parse(data);
                list.empty();
                if (data.status === 'Ok') {
                    $.each(data.users, function (index, value) {
                        var newOption = '<option value="'+value.id+'">'+value.string+'</option>';
                        list.append(newOption);
                    });
                }
            }
        });
    }
});



//__________________________________________testdu.js
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



$('select.select__account').on('change', function() {
   var acc_id = this.value;
    var list = $('#managerreviews-traiding_period_log_id');
    $.ajax({
        url: window.location.protocol + "//" + window.location.host + "/reviews/get_periods",
        type: "POST",
        data: {'account_id': acc_id},
        success: function (data) {
            data = JSON.parse(data);
            list.empty();
            if (data.status === 'Ok') {
                $.each(data.periods, function (index, value) {
                    var newOption = '<option value="'+value.id+'" data-start="'+value.start+'" data-end="'+value.end+'">'+value.string+'</option>';
                    list.append(newOption);
                });
            } else {
                alert('Ошибка');
            }
        }
    });
})

$('select.select__period').on('change', function() {
    var option = $(this).find(":selected");
    var start_date = option.data('start');
    var end_date = option.data('end');
    $("#managerreviews-date_add").kvDatepicker({
        "format": "dd-mm-yyyy",
        "language": "ru"
    });
    $("#managerreviews-date_add").kvDatepicker("setStartDate", new Date(start_date));
    $("#managerreviews-date_add").kvDatepicker("setEndDate", new Date(end_date));
    console.log(start_date);
    console.log(end_date);

})

$(document).on('click', '.end_bonus', function (e) {
    e.preventDefault();
    var account_id = $(this).data('accountid');
    end_bonus(account_id, 0);

});

function end_bonus(account_id, confirmed) {
    var td = $(document).find('#a'+account_id);
    var error = td.find('p.error');
    $.ajax({
        url: window.location.protocol + "//" + window.location.host + "/trade/bonus-end",
        type: "POST",
        data: {'account_id': account_id, 'confirmed' : confirmed},
        success: function (data) {
            data = JSON.parse(data);
            $(document).find('#b'+account_id).html(data.balance + '$');
            if (data.status == 1) {
                alert(data.message);
            } else if(data.status == 2) {
                if(confirm(data.message)) {
                    end_bonus(account_id,1);
                }
            } else {
                td.empty();
                td.html('Бонус выведен');
            }
        }
    });
}
//confirm('wertwertwertwert');


