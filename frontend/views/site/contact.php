<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = Yii::t('app', 'Контакты');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="outer-indent">
    <div class="contact-head">
        <div class="container">
            <h2 class="contact-head__title">Обращайтесь в службу поддержки invest</h2>
          <h4 class="contact-head__tagline">Наши специалисты на связи и днем и ночью</h4><img
              class="contact-head__help-ico" src="/img/contact-img.png">
        </div>
    </div>
    <div class="contact-body">
        <div class="container">
            <div class="contact-item">
                <div class="contact-info">
                    <h2 class="contact-info__title">Контакты</h2>
                    <div class="contact-info__work-time">
                        <div class="contact-info__work-time--title">Операторская служба</div>
                        <ul class="contact-info__work-time--list">
                            <li>
                                <div class="icons phone"></div>
                                <div class="phone-number">8 800 511-85-03</div>
                            </li>
                            <li>
                                <div class="icons time"></div>ежедневно, с 10-00 до 20-00 МСК
                            </li>
                        </ul>
                        <div class="contact-info__work-time--title">Клиентская служба поддержки</div>
                        <ul class="contact-info__work-time--list">
                            <li>
                                <div class="icons email"></div><a href="mailto:clients@invest.biz">clients@invest.biz</a>
                            </li>
                            <li>
                                <div class="icons time"></div>ежедневно, с 10-00 до 20-00 МСК
                            </li>
                        </ul>
                        <div class="contact-info__work-time--title">Техническая поддержка</div>
                        <ul class="contact-info__work-time--list">
                          <li>
                            <div class="icons email"></div><a href="mailto:support@invest.biz">support@invest.biz</a>
                          </li>
                            <li>
                                <div class="icons time"></div>ежедневно, с 10-00 до 20-00 МСК
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="contact-support__item">
                    <div class="contact-support__item--worker-info wow animated fadeIn" data-wow-delay="0.2s">
                        <div class="contact-support__item--worker-info__avatar"><img class="rounded-circle" src="/img/contact-team-1.png"></div>
                        <div class="contact-support__item--worker-info__name">Александр Маслов<span>Ведущий аналитик</span></div>
                        <ul class="contact-support__item--worker-info__work-time">
                            <li>
                                <div class="icons mail"></div><a href="mailto:aleksmaslow@invest.biz">aleksmaslow@invest.biz</a>
                            </li>
                            <li>
                                <div class="icons vk"></div><a href="https://vk.com/aleksmaslow">vk.com/aleksmaslow</a>
                            </li>
                            <li>
                                <div class="icons telegram"></div><a href="https://telegram.me/aleksmaslow">@aleksmaslow</a>
                            </li>
                        </ul>
                    </div>
                    <div class="contact-support__item--worker-info wow animated fadeIn" data-wow-delay="0.2s">
                        <div class="contact-support__item--worker-info__avatar"><img class="rounded-circle" src="/img/contact-team-2.png"></div>
                        <div class="contact-support__item--worker-info__name">Мария Мельниченко<span>Специалист по работе с клиентами</span></div>
                        <ul class="contact-support__item--worker-info__work-time">
                            <li>
                                <div class="icons mail"></div><a href="mailto:maria@invest.biz">maria@invest.biz</a>
                            </li>
                            <li>
                                <div class="icons vk"></div><a href="https://vk.com/melmarya">vk.com/melmarya</a>
                            </li>
                        </ul>
                    </div>
                    <div class="contact-support__item--worker-info wow animated fadeIn" data-wow-delay="0.2s">
                        <div class="contact-support__item--worker-info__avatar"><img class="rounded-circle" src="/img/contact-team-3.png"></div>
                        <div class="contact-support__item--worker-info__name">Виктор Ковалевский<span>Руководитель аналитического отдела</span></div>
                        <ul class="contact-support__item--worker-info__work-time">
                            <li>
                                <div class="icons mail"></div><a href="mailto:viktor@invest.biz">viktor@invest.biz</a>
                            </li>
                            <li>
                                <div class="icons skype"></div><a href="skype:4d1c562df6bc974c">live:4d1c562df6bc974c</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>