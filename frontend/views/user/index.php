<?php

use yii\helpers\Url;
use common\service\Servis;
use yii\widgets\LinkPager;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\widgets\Pjax;

$mounts = ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December',];
if (\Yii::$app->language == 'ru') {
    setlocale(LC_ALL, 'ru_RU.UTF-8');
    $mounts = ['', 'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];
}

$service = Servis::getInstance();
$this->title = 'Личный кабинет';
$show_7_bonus = false;
if (!$user->seven_bonus_received AND $user->balance >= 4) {
    if ($first_deposit = \common\models\BalanceLog::find()->where(['user_id' => $user->id, 'operation' => [0, 3], 'status' => 1])->orderBy('date_add ASC')->one() AND $first_deposit->date_add <= date('Y-m-d H:i:s', strtotime('-3 days'))) {
        $show_7_bonus = true;
    }
}
?>

    <div class="content col pt-0">
        <div class="row">
            <div class="col-lg-7">
                <div class="personal-balance">
                    <div class="personal-balance__item">
                        <div class="personal-balance__item--balance"><?= $service->beautyDecimal($user->balance) ?><span>$</span>
                            <?php if ($arriving_sum) { ?>
                                <div class="personal-balance__item--title arriving__sum">
                                    (<?= $service->beautyDecimal($arriving_sum) ?>$ поступят в 15:00)
                                </div>
                            <?php } ?>
                        </div>
                        <div class="personal-balance__item--title">Мой банк</div>
                        <div class="d-flex"><a class="personal-balance__item--btn default-btn"
                                               href="<?= Url::to('/user/deposit') ?>">Пополнить</a></div>
                    </div>
                    <div class="personal-balance__items">
                        <div class="personal-balance__price">
                            <div class="personal-balance__items--price"><?= $service->beautyDecimal($last_day_result) ?>
                                <span>$</span></div>
                            <div class="personal-balance__items--title">Результат дня</div>
                        </div>
                        <div class="personal-balance__price">
                            <div class="personal-balance__items--price"><?= $service->beautyDecimal($current_using_summ) ?><span>$</span></div>
                            <div class="personal-balance__items--title">Используемая сумма
                                <div class="personal-balance--tooltip" data-toggle="tooltip" data-placement="bottom" data-html="true"
                                     title="<div class='popup-tooltip'>Часть средств Вашего баланса, которая используется в ставках. Подробнее на какие события распределен капитал смотрите в Ставки invest.</div>">
                                    ?
                                </div>
                            </div>
                            <a class="view-rates" href="<?= Url::to('/user/bets') ?>">Посмотреть ставки</a>
                        </div>
                        <div class="personal-balance__price">
                            <div class="personal-balance__items--price"><?= $service->beautyDecimal($total_profit) ?>
                                <span>$</span></div>
                            <div class="personal-balance__items--title">Мой профит</div>
                            <?php if ($show_7_bonus) { ?>
                                <a class="view-rates" href="#" data-toggle="modal" data-target="#dropBonus">Получить бонус<span>+7%</span>
                                    <div class="personal-balance--tooltip" data-toggle="tooltip" data-placement="bottom" data-html="true" title="<p class='popup-tooltip'>Расскажи о своих успехах с сервисом invest в ВКонтакте и Instagram и получи +7% на свой банк в подарок от компании.</p>">?</div>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 pl-lg-1">
                <div class="personal-history">
                    <div class="personal-history__top">
                        <h2 class="personal-history__top--title">История управления</h2>
                        <div class="personal-history__top--slider">
                            <div class="slider single-item slider-for">
                                <?php foreach ($data['months'] as $key => $value) { ?>
                                    <div>
                                        <div class="date-item"><?= $mounts[date("n", $key)] . ' ' . date('Y', $key) ?></div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="personal-history__item">
                        <div class="slider-nav">
                            <?php
                            $today = strtotime(date('Y-m-d'));
                            if (date('H') < 15) {
                                $today = strtotime(date('Y-m-d') . ' -1 day');
                            }

                            foreach ($data['months'] as $month) {
                                $i = 1;
                                ?>
                                <div class="management-history__table table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th scope="col">Пн</th>
                                            <th scope="col">Вт</th>
                                            <th scope="col">Ср</th>
                                            <th scope="col">Чт</th>
                                            <th scope="col">Пт</th>
                                            <th scope="day-off">Сб</th>
                                            <th scope="day-off">Вс</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($month as $day) {
                                            if ($i == 1) {
                                                echo '<tr>';
                                            }
                                            ?>
                                            <td class="<?= $today == $day['date'] ? 'active' : null ?>">
                                                <div
                                                    class="table-item <?= $day['result'] ?>">
                                                    <?= $day['profit'] ?>
                                                    <span>
                                                    <?= $day['date_short'] ?>
                                                </span>
                                                </div>
                                            </td>
                                            <?php
                                            if ($i == 7) {
                                                echo '</tr>';
                                                $i = 1;
                                            } else {
                                                $i++;
                                            }
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="personal-purposeful new_objective" style="<?= $show_model ? null : 'display:none;' ?>">
                    <?php $form = ActiveForm::begin(['id' => 'form-signup', 'options' => ['enctype' => 'multipart/form-data']]); ?>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="personal-purposeful__form">
                                <div class="personal-purposeful__title">Для целеустремленных</div>
                                <div class="personal-purposeful__description">Для целеустремленных invest дарит денежные
                                    бонусы, чтобы вы быстрее достигли желаемого
                                </div>
                                <?php if ($need_balance_error) { ?>
                                    <div class="personal-purposeful__get-balance">Для начала нужно пополнить свой баланс.</div>
                                <?php } ?>
                                <?= $form->field($model, 'comment')
                                    ->textInput([
                                        'placeholder' => 'Пример: Акустическая гитара',
                                    ])->label('Поставьте себе финансовую цель') ?>
                                <?= $form->field($model, 'sum_end')
                                    ->textInput([
                                        'placeholder' => 'Ориентировочная сумма',
                                    ])->label('Стоимостью (введите в долларах)') ?>

                                <div class="form-group">
                                    <?= Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'default-btn']) ?>
                                </div>
                                <p>Если ты ставишь цели, то или достигаешь их, или нет,</br>Если не ставишь, то вариантов достижения нет вообще</p>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="personal-purposeful__load-img">
                                <div class="main-img-preview">
                                    <div class="main-img-preview__item">
                                        <img class="thumbnail img-preview" src="<?= $model->image ? $model->image : '/img/load-img.jpg' ?>"
                                             alt=""></div>
                                </div>
                                <?= $form->field($model, 'image_file', ['template' => '<div class="fileUpload"><label class="control-label" for="userobjectives-image_file" style="cursor: pointer;">Загрузить файл</label>{input} </div>{error}'])
                                    ->fileInput(['class' => "attachment_upload", 'style' => 'display:none;'
                                    ])->label(false) ?>


                            </div>
                        </div>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
                <?php if ($objective) {
                    ?>
                    <div class="personal-purposeful current_objective" style="<?= $show_model ? 'display:none;' : null ?>">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="personal-purposeful__step">
                                    <div class="personal-purposeful__title">Для целеустремленных</div>
                                    <div class="personal-purposeful__wish"><?= $objective->comment ?></div>
                                    <div class="slider-range" data-start="<?= $objective->percent ?>"></div>
                                    <div class="d-flex flex-wrap progress-info-item">
                                        <div class="ProgressBar ProgressBar--animateAll"
                                             data-progress="<?= $objective->percent ?>">
                                            <svg class="ProgressBar-contentCircle" viewbox="0 0 200 200">
                                                <defs>
                                                    <lineargradient id="gradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                                        <stop offset="0%" stop-color="rgb(255,96,0)" stop-opacity="1"></stop>
                                                        <stop offset="100%" stop-color="rgb(147,32,125)"
                                                              stop-opacity="1"></stop>
                                                    </lineargradient>
                                                </defs>
                                                <circle class="ProgressBar-background" transform="rotate(-90, 100, 100)"
                                                        cx="100"
                                                        cy="100" r="95"></circle>
                                                <circle class="ProgressBar-circle" transform="rotate(-90, 100, 100)" cx="100"
                                                        cy="100" r="95" stroke="url(#gradient)"></circle>
                                                <span class="ProgressBar-percentage"><?= $service->getDaysToPeriod($objective->days_to) ?></span>
                                            </svg>
                                        </div>
                                        <div class="progress-info"><?= $objective->days_to > 300 ? 'Слишком маленькая сумма для такой цели' : Yii::t('app', 'Ориентировочно осталось до достижения твоей цели') ?></div>
                                    </div>
                                    <div class="info"><?= $objective->data->title ?></div>
                                    <p><?= $objective->data->description ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="personal-purposeful__load-img loaded-img">
                                    <div class="main-img-preview">
                                        <div class="main-img-preview__item">
                                            <img class="thumbnail img-preview img-preview" src="<?= $objective->image ?>">
                                        </div>
                                    </div>

                                    <div class="input-group-btn form-group">
                                        <div class="fileUpload" data-toggle="modal" data-target="#changeMyObjective">
                                            <label class="control-label"><?= $objective->date_end ? 'новая цель' : 'Заменить цель' ?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="modal fade" id="changeMyObjective" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-change" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13px" height="13px">
                            <path fill-rule="evenodd" d="M12.157,11.450 L11.450,12.157 L6.500,7.207 L1.550,12.157 L0.843,11.450 L5.793,6.500 L0.843,1.550 L1.550,0.843 L6.500,5.793 L11.450,0.843 L12.157,1.550 L7.207,6.500 L12.157,11.450 Z"></path>
                        </svg>
                    </button>
                    <div class="change-item">Вы точно хотите изменить цель? Текущая цель будет сброшена</div>
                    <div class="change-item__links"><a id="yes_change_my_objective" class="yes" href="#">Да</a><a class="no" href="#" data-dismiss="modal" aria-label="Close">Нет</a></div>
                </div>
            </div>
        </div>
    </div>

<?php
if ($show_7_bonus) {
    $bonusModel = new \common\models\UserBonusRequest();
    $open_bonus_request = \common\models\UserBonusRequest::checkOpen($user->id);
    $user_social = $user->social;
    if(!$open_bonus_request) {
        if ($user_social->vk) {
            $bonusModel->vk = $user_social->vk;
        }
        if ($user_social->instagram) {
            $bonusModel->instagram = $user_social->instagram;
        }
    } else {
        $bonusModel = $open_bonus_request;
    }

    ?>
    <div class="modal fade" id="dropBonus" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog dropBonus" role="document">
            <div class="modal-content">
                <button class="close" type="button" data-dismiss="modal" aria-label="Close" style="position: absolute;right: 15px;top: 15px;">
                    <svg width="14px" height="14px">
                        <path fill-rule="evenodd" fill="rgb(49, 49, 49)" d="M13.660,13.216 L13.216,13.660 L7.000,7.444 L0.784,13.660 L0.340,13.216 L6.556,7.000 L0.340,0.783 L0.784,0.339 L7.000,6.556 L13.216,0.339 L13.660,0.783 L7.444,7.000 L13.660,13.216 Z"></path>
                    </svg>
                </button>
                <div class="modal-steps--title">Для получения на баланс +7% требуется всего 2 простых шага</div>
                <?php $bonus_form = ActiveForm::begin(['id' => 'formSevenBonus', 'options' => ['class' => '']]); ?>
                <div class="modal-steps--flex">

                    <div class="modal-steps--item">
                        <div class="modal-steps--item-icon"><img src="/img/vk-ico.png" alt=""/></div>
                        <div class="modal-steps--item-text">
                            Нажми здесь, чтобы Поделиться с друзьями
                            на своей стене ВКонтакте успехами
                            в сотрудничестве с invest
                        </div>

                        <div class="settings">
                            <button id="vk_share_button" target="_blank" class="settings-account__btn">Поделиться</button>
                            <?php echo '<div class="input-group">';

                                echo $bonus_form->field($bonusModel, 'vk', [
                                    'options' => ['class' => 'form-group px-0'],
                                    'template' => '{label}{input}{error}<div class="input-group-append">
                        <div class="vk icons"></div>
                    </div>',
                                    'labelOptions' => ['class' => '']
                                ])
                                    ->input('text', ['placeholder' => 'https://vk.com/xxxxxx', 'disabled' =>  $bonusModel->id ? true : false])
                                    ->label(false);
                            echo '</div>';
                            ?>

                        </div>
                    </div>
                    <div class="modal-steps--item">
                        <div class="modal-steps--item-icon"><img src="/img/instagram-ico.png" alt=""/></div>
                        <div class="modal-steps--item-text">
                          Опубликуйте пост с отзывом о сервисе invest в своем Instagram профиле. В качестве фотографии поставьте скриншот с сайта сервиса, а в описании вместе с отзывом добавьте сайт invest.biz и теги #доход #business #invest #invest.biz
                        </div>
                        <div class="settings">
                            <div class="input-group">
                                <?= $bonus_form->field($bonusModel, 'instagram', [
                                    'options' => ['class' => 'form-group px-0'],
                                    'template' => '{label}{input}{error}<div class="input-group-append">
                        <div class="instagram icons"></div>
                    </div>',
                                    'labelOptions' => ['class' => '']
                                ])
                                    ->input('text', ['placeholder' => 'https://instagram.com/xxxxxx', 'disabled' => $bonusModel->id ? true : false])
                                    ->label(false) ?>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="settings conditions-list">
                  <div class="input-group">
                    <div class="list-title">Важные условия!</div>
                    <div class="info">
                      <p></p>
                      <ul>
                        <li>Посты нельзя удалять в течении недели;</li>
                        <li>К тебе может постучаться в друзья наш модератор для проверки наличия постов;</li>
                        <li>Страницы должны быть твоими и реальными;</li>
                        <li>После проверки подарок сразу поступит в твой банк.</li>
                      </ul>
                    </div>
                  </div>
                </div>
                    <div id='bonusbuttons' class="input-group">
                        <p style="text-align: center;margin: 50px auto 0;<?= $open_bonus_request ? null : 'display:none' ?>">Запрос на получение бонуса отправлен</p>
                      <p style="<?= $open_bonus_request ? null : 'display:none' ?>" class="bonus-text">Посты в течение 7-ми дней удалять нельзя. После, тебе придет сообщение на аккаунт в invest и подарок от сервиса!</p>
                        <button type="submit" class="modal-steps--btn default-btn" style="border: none;width: 185px;    padding: 12px 17px; <?= $open_bonus_request ? 'display:none' : null ?>">Получить +7%</button>
                    </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
    <script>
        document.querySelector('#vk_share_button').onclick = function() {
            var redirectWindow = window.open("http://vk.com/share.php?url=<?= Url::base(true) . '/' . $user->invitation_code ?>&title=Я зарабатываю с крутым сервисом invest! И ты пробуй!&noparse=true&image=https://invest.biz/img/vk_share.png", '_blank');
            redirectWindow.location;
        };
    </script>
<?php } ?>