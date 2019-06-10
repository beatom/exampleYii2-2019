<?php

use yii\helpers\Url;
use common\service\Servis;
use yii\widgets\LinkPager;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\widgets\Pjax;

if (\Yii::$app->language == 'ru') {
    setlocale(LC_ALL, 'ru_RU.UTF-8');
}
$protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https' : 'http';
$service = Servis::getInstance();
$this->title = 'Кэшбэк';
?>

<div class="content col pt-0">
    <div class="row">
        <div class="col-lg-7">
            <div class="personal-balance cashback-balance">
                <div class="personal-balance__item">
                    <div class="personal-balance__item--balance"><?= $service->beautyDecimal($user->balance_partner) ?><span>$</span></div>
                    <div class="personal-balance__item--title">Мой баланс</div>
                    <a id="withdraw_partner_balance" class="personal-balance__item--btn default-btn" href="#">Снять</a>
                    <p>Поделись ссылкой или промо-кодом с друзьями и получай ежедневно по 20% от прибыли invest в течении 30-ти дней!</p>
                    <div class="personal-balance__copy-link">
                        <div class="personal-balance__copy-link--title">Пригласительная ссылка</div>
                        <div class="personal-balance__copy-link--item">
                            <div class="d-flex mr-auto align-items-center">
                                <button class="icon-copy__item copy_btn" type="submit">
                                    <svg class="icon-copy">
                                        <use xlink:href="/img/sprites/sprite.svg#copy-ico"></use>
                                    </svg>
                                </button>
                                <input type="hidden" class="copy_target" value="<?= Url::base(true) . '/' . $user->invitation_code ?>">
                                <div class="form-control"><?= $protocol ?>://<span>invest.biz/<?= $user->invitation_code ?></span></div>
                            </div>
                            <ul class="social-list">
                                <li><a class="icon-vk icon" href="http://vk.com/share.php?url=<?= Url::base(true) . '/' . $user->invitation_code ?>&title=invest&noparse=true">
<!--                                        http://vk.com/share.php?url=[URL]&title=[TITLE]&description=[DESC]&image=[IMAGE]&noparse=true -->
                                        <svg class="ico">
                                            <use xlink:href="/img/sprites/sprite.svg#vk-ico"></use>
                                        </svg></a></li>
                                <li><a class="icon-facebook icon" href="https://www.facebook.com/sharer/sharer.php?u=<?= Url::base(true) . '/' . $user->invitation_code ?>">
                                        <svg class="ico">
                                            <use xlink:href="/img/sprites/sprite.svg#facebook-ico"></use>
                                        </svg></a></li>
                                <li><a class="icon-twitter icon" href="https://twitter.com/share?url=<?= Url::base(true) . '/' . $user->invitation_code ?>&text=invest">
                                        <svg class="ico">
                                            <use xlink:href="/img/sprites/sprite.svg#twitter-ico"></use>
                                        </svg></a></li>
                            </ul>
                        </div>
                        <div class="personal-balance__copy-link--code">Пригласительный промо-код  -<span><?= $user->promo_code ?></span></div>
                        <p>Регистрация по твоему промо-коду даёт <span>скидку 25% на услуги сервиса invest.</span> Спеши поделиться скидкой с друзьями!</p>
                    </div>
                </div>
                <div class="personal-balance__example">
                    <div class="personal-balance__example--title">Рассмотрим кэшбэк на примере:</div>
                    <p>Ты пригласил через партнёрскую ссылку или промо-код своего друга Артёма. Он пополнил свой счёт и в течение двух месяцев заработал 1000$, и заплатил за услуги сервиса 40%, то есть 400$.<br><br>Ты же получаешь 20% от его оплаты сервиса invest, то есть 80$. Поскольку Артём ежедневно оплачивал сервис invest, то и тебе кэшбэк поступал ежедневно!</p>
                </div>
            </div>
            <div class="invitations">
                <div class="invitations--title">Мои пpиглaшённые
                    <?php if( $user->partner_id) { ?>
                        <div class="invitations--invited-me">Меня пригласил <?= $user->partner->username ?></div>
                    <?php } ?>

                </div>
                <div class="table-responsive invitations--table">
                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">Логин</th>
                            <th scope="col">Дата пополнения</th>
                            <th scope="col">На счету</th>
                            <th scope="col">Оплата invest</th>
                            <th scope="col">Мой кэшбэк</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($table as $partner) {
                          ?>
                            <tr>
                                <td><?= $partner->username ?></td>
                                <td class="registration-date"><?= $partner->first_deposit_date ? date('d.m.Y', strtotime($partner->first_deposit_date)) : '-'?></td>
                                <td><?= $service->beautyDecimal($partner->balance) ?>$</td>
                                <td><?= $service->beautyDecimal($partner->difference) ?>$</td>
                                <td class="income"><?= $service->beautyDecimal($partner->result) ?>$</td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>

                <?= $this->render('@app/views/layouts/_paginator.php', [
                    'pages' => $pages,
                    'pageSize' => $pageSize,
                    'now_page' => (isset($_GET['page'])) ? (int)$_GET['page'] : 1,
                    'link' => $this->context->module->requestedRoute
                ]) ?>
                </nav>
            </div>
        </div>
        <div class="col-lg-5 pl-lg-1">
            <div class="growth">
                <div class="growth--title"><b>Знакомьте с invest больше людей</b> и получайте<br> ценные вознаграждения!</div>
                <div class="growth-steps">
                    <?php foreach ($service->getChangeStatusInfo() as $key => $info) { 
                        if($key == 0) {
                            continue;
                        }
                        ?>
                    <div class="growth-steps-item <?= $key <= $user->status_in_partner ? 'active' : null ?>">
                        <div class="growth-steps-item--img">
                            <img src="<?= $info['img'] ?>">
                        </div>
                        <div class="growth-steps-item--info">
                            <span><?= $info['title'] ?></span>
                            <?= $info['description'] ? '<div class="info">' . $info['description'] . '</div>' : null ?>
                            <div class="condition"><b>Условие:</b> Общий чек приглашенных <?= number_format($info['capital'], 0, '', ' ') ?>$</div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <div class="growth-turnover">Общий чек приглашённых -
                    <div class="price"><?= $service->beautyDecimal($user->getInvitedFounds(), 2, '.', ' ') ?>
                        <div class="val">$</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="withdrawPartnerBalance" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-change" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13px" height="13px">
                        <path fill-rule="evenodd" d="M12.157,11.450 L11.450,12.157 L6.500,7.207 L1.550,12.157 L0.843,11.450 L5.793,6.500 L0.843,1.550 L1.550,0.843 L6.500,5.793 L11.450,0.843 L12.157,1.550 L7.207,6.500 L12.157,11.450 Z"></path>
                    </svg>
                </button>
                <div class="change-item message"></div>
                <div class="change-item__links"><a class="yes" href="#" data-dismiss="modal" aria-label="Close">Закрыть</a></div>
            </div>
        </div>
    </div>
</div>