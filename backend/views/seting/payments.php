<?php

use yii\helpers\Html;
use common\models\trade\TradingAccount;
use kartik\widgets\DatePicker;
use kartik\widgets\ActiveForm;
use common\models\User;
use common\service\Servis;

$service = Servis::getInstance();
$this->title = 'Платежные системы';
//$this->params['breadcrumbs'][] = ['label' => 'Настройки', 'url' => ['/seting/index']];
//$this->params['breadcrumbs'][] = $this->title;
?>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<?= $this->render('/seting/menu'); ?>
<div class="row">

    <div class="col-lg-8">
        <?= $this->render('_menu') ?>
        <span style="font-size: 11px">*Комиссии: отображаемая пользователям / добавляемая реально к сумме введенной пользователям при создании заявки</span>
        <hr>
        <ul id="sortable">
            <?php
            $i = 1;
            foreach ($systems as $system) {
                ?>
                <li class="ui-state-default <?= $system->show ? 'is_active' : null ?>" data-id="<?= $system->id ?>"
                    data-position="<?= $system->position ?>">

                    <div class="row">
                        <div class="col-sm-9">
                            <div class="row">
                                <div class="col-sm-1">
                                    <span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                                </div>
                                <div class="col-sm-7">
                                    Название: <b><?= $system->title ?></b><br>
                                    Система: <b><?= $system->system ?></b><br>
                                    min/max: <b><?= $system->sum_min . ' / ' . $system->sum_max . '  ' . $system->currency->synonym?></b><br>
                                    Коммисия: <b><?= $system->fee . '% / ' . $system->fee_add ?>%</b><br>
                                </div>
                                <div class="col-sm-2">
                                    <a href="/seting/payment/<?= $system->id ?>">Настройки</a>
                                </div>
                                <div class="col-sm-2">
                                    <a href="#" class="toggle_system"><?= $system->show ? 'Скрыть' : 'Показать' ?></a>
                                </div>

                            </div>
                        </div>
                            <div class="col-sm-3">
                                <img src="http://<?= Yii::$app->params['frontendDomen'] . $system->image ?>" alt="">
                            </div>
                        </div>
                </li>
                <?php
            } ?>
        </ul>
    </div>


</div>

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

<script>
    $(function () {
        var group = $("#sortable").sortable({
            update: function (event, ui) {
                var list = $(document).find("ul#sortable li");
                var accounts = [];
                var positions = [];
                var count = list.length;
                var i = 0;
                list.each(function (index) {
                    positions.push(count - i);
                    accounts.push($(this).data('id'));
                    i++;
                });
                group.sortable("disable");
                $.ajax({
                    url: window.location.protocol + "//" + window.location.host + "/seting/sort-accounts-positions",
                    type: "POST",
                    data: {'ids': accounts, 'is_deposit' : true},
                    success: function (data) {
                        data = JSON.parse(data);
                        if (data.status == 'Ok') {
                            group.sortable("enable");
                        }
                    }
                });
            },
        });
        $("#sortable").disableSelection();
    });

    $(document).on('click', '.toggle_system', function () {
        var button = $(this);
        var el = $(this).closest('li');
        var id = el.data('id');
        $.ajax({
            url: window.location.protocol + "//" + window.location.host + "/seting/payment_toggle",
            type: "POST",
            data: {'id': id, 'is_deposit' : true},
            success: function (data) {
                data = JSON.parse(data);
                if (data.status == 'Ok') {
                    if(el.hasClass('is_active')) {
                        el.removeClass('is_active');
                        button.html('Показать');
                    } else {
                        el.addClass('is_active');
                        button.html('Скрыть');
                    }
                }
            }
        });
    })
</script>