<?php

/* @var $this yii\web\View */

use common\widgets\NewsHome;
use yii\helpers\Url;


$this->title = \Yii::$app->params['siteName'];

?>

<div class="main-section">
  <div class="container">
    <div class="main-section__item">
      <div class="main-section__item--title"></div>
      <h4>Первый сервис по заработку<span>НА СПОРТИВНЫХ СОБЫТИЯХ</span></h4><a class="main-section__item--btn" href="<?= Url::to(['/site/about']) ?>">Подробнее</a>
      <ul class="main-section__item--list">
          <li><a class="icon" href="https://www.youtube.com/channel/UCG5qyL1sT0Gm_A9ajDK1UDg" target="_blank" data-toggle="tooltip" data-placement="right" data-html="true" title="Ютуб канал">
                  <div class="youtube"></div></a></li>
        <li><a class="icon" href="https://vk.com/invest" target="_blank"  data-toggle="tooltip" data-placement="right" data-html="true" title="Группа ВКонтакте">
            <div class="vk"></div></a></li>
        <li><a class="icon" href="https://t.me/iinvest" target="_blank" data-toggle="tooltip" data-placement="right" data-html="true" title="Телеграм канал">
            <div class="telegram"></div></a></li>

          <li><a class="icon" href="https://t.me/invest_chat" target="_blank" data-toggle="tooltip" data-placement="right" data-html="true" title="Телеграм чат">
                  <div class="telegram"></div></a></li>
      </ul>
    </div>
  </div>
</div>
<div class="about-info" id="about-info">
  <div class="container">
    <div class="about-info__items">
      <div class="about-info__item wow animated fadeInLeft"><div class="about-info__title"></div>
        <div class="about-info__tagline">Прозрачная сервис-площадка для заработка на спортивных событиях</div>
        <p>
          invest компания, которая занимается аналитической деятельностью в сфере спортивных состязаний и позволяет своим клиентам извлекать высокий доход из букмекерского бизнеса. Перенимая лучшее, что было сделано до нас, учась на чужих ошибках, мы создаём самую безопасную и надежную систему по заработку со спортивных событий из всех когда-либо существовавших. <span>Опробовать сервис можно всего с 5$!</span>
        </p><a class="default-btn" href="<?= Url::to(['/site/signup']) ?>">Регистрация</a>
      </div>
    </div>
  </div>
</div>
<div id="why_us" class="why-trusted">
    <div class="container">
        <h2>Почему invest стоит доверять?</h2>
        <div class="why-trusted__items">
            <div class="row">
                <div class="col-lg-12 col">
                    <div class="why-trusted__item file-item wow" data-wow-delay="1s" data-wow-offset="10">
                        <div class="why-trusted__item--ico">
                            <div class="why-trusted__item--ico__item file-ico wow animated">
                                <svg width="100%" height="100%" viewbox="0 0 426 426" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                    <path class="path" stroke="#ffffff" stroke-width="10" fill="none" d="M213,423 C328.979803,423 423,328.979803 423,213 C423,97.0201968 328.979803,3 213,3 C97.0201968,3 3,97.0201968 3,213 C3,328.979803 97.0201968,423 213,423 Z"></path>
                                </svg>
                            </div>
                            <svg class="icon file animated pulse">
                                <use xlink:href="/img/sprites/sprite.svg#file"></use>
                            </svg>
                        </div>
                        <p>Компания поставляющая информационную аналитику зарегистрирована в России.
                          ООО “Айрейз”контролируется в соответствии
                          с Российским законодательством.
                        </p><a class="why-trusted__item--link" href="<?= Url::to(['/about#agreements']) ?>">Смотреть документацию</a>
                    </div>
                </div>
                <div class="col-lg-6 col">
                    <div class="ml-0 why-trusted__item teamwork-item wow" data-wow-delay="3s">
                        <div class="why-trusted__item--ico">
                            <div class="why-trusted__item--ico__item teamwork-ico animated wow delay-2s">
                                <svg width="100%" height="100%" viewbox="0 0 426 426" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                    <path class="path" stroke="#ffffff" stroke-width="10" fill="none" d="M213,423 C328.979803,423 423,328.979803 423,213 C423,97.0201968 328.979803,3 213,3 C97.0201968,3 3,97.0201968 3,213 C3,328.979803 97.0201968,423 213,423 Z"></path>
                                </svg>
                            </div>
                            <img class="teamwork icon animated pulse" src="/img/teamwork.png">
                        </div>
                        <p>Открытая и сплочённая команда аналитиков.<br>Суммарный опыт аналитиков invest более 23-х лет.</p><a class="why-trusted__item--link" href="<?= Url::to(['/about#team']) ?>">познакомиться с командой</a>
                    </div>
                </div>
                <div class="col-lg-6 col">
                    <div class="mr-0 why-trusted__item analysis-item wow" data-wow-delay="2s">
                        <div class="why-trusted__item--ico">
                            <div class="why-trusted__item--ico__item analysis-ico animated wow delay-2s">
                                <svg width="100%" height="100%" viewbox="0 0 426 426" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                    <path class="path" stroke="#ffffff" stroke-width="10" fill="none" d="M213,423 C328.979803,423 423,328.979803 423,213 C423,97.0201968 328.979803,3 213,3 C97.0201968,3 3,97.0201968 3,213 C3,328.979803 97.0201968,423 213,423 Z"></path>
                                </svg>
                            </div>
                            <svg class="icon analysis animated pulse">
                                <use xlink:href="/img/sprites/sprite.svg#analysis"></use>
                            </svg>
                        </div>
                        <p>Только компания invest устраивает Открытые дни и<br>заранее демонстрирует на какие события распределяется<br>клиентский капитал! Вы можете убедиться и<br>поставить на события самостоятельно.</p><a class="why-trusted__item--link" href="<?= Url::to(['/profitability#bets']) ?>">Смотреть Ставки invest</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="how-work">
  <div class="container">
    <h2>Как работает площадка invest?</h2>
    <div class="section section-cards">
      <div class="section-content">
        <div class="cards-wrapper">
          <ul>
            <li class="card-01">
              <figure class="card-container">
                <div class="bg"></div>
                <span class="card">
                        <div class="card-count">01</div>
                        <h4 class="card-title">Пополняете личный кабинет от<span> 5<sup>$</sup></span></h4>
                        <div class="device-lockup">
                          <figure class="image-hardware"></figure>
                        </div></span>
              </figure>
            </li>
            <li class="card-02">
              <figure class="card-container">
                <div class="bg"></div>
                <span class="card">
                        <div class="card-count">02</div>
                        <h4 class="card-title">Необходимая сумма распределяется среди выбранных событий в букмекерских компаниях</h4>
                        <div class="device-lockup">
                          <figure class="image-hardware"></figure>
                        </div></span>
              </figure>
            </li>
            <li class="card-03">
              <figure class="card-container">
                <div class="bg"></div>
                <span class="card">
                        <div class="card-count">03</div>
                        <h4 class="card-title">В 10-00 средства поступают обратно в Ваш личный кабинет вместе с прибылью. Откуда их можно снять в любой день!</h4>
                        <div class="device-lockup">
                          <figure class="image-hardware"></figure>
                        </div></span>
              </figure>
            </li>
          </ul>
        </div>
      </div>
    </div>
      <a class="default-btn mobile-only" style="margin-top: 30px;max-width: 190px;" href="<?= Url::to(['/site/signup']) ?>">Хочу начать!</a>
  </div>
</div>
<div class="cashback">
  <div class="container">
    <div class="row">
      <div class="col-md-7">
        <div class="cashback__item">
          <h2 class="wow animated fadeIn">Кэшбэк прямо в личный кабинет</h2>
          <div class="cashback-list">
            <div class="cashback-list__item wow animated fadeIn" data-wow-delay="0.5s">Расскажи о invest друзьям и получай по 20% от дохода сервиса ежедневно!</div>
            <div class="cashback-list__item wow animated fadeIn" data-wow-delay="1s">Тебя ждёт множество денежных бонусов и мегакрутых призов!</div>
          </div>
          <div class="d-flex"><a class="cashback__item-btn wow animated fadeIn" href="<?= Url::to(['/user/cashback']) ?>" data-wow-delay="1.5s">Получать кэшбэк</a></div>
        </div>
      </div>
      <div class="col-lg-5 col wow animated fadeIn">
        <div class="d-flex align-items-end h-100">
          <div class="cashback__img"><img class="img-fluid" src="/img/phone.png">
            <div class="cashback__cash"><span>20<sup>%</sup></span>от дохода<br>компании</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="service-evaluated">
    <div class="container">
        <h2>Оценили свои возможности с сервисом invest?</h2>
        <p>Начните реализовывать свои финансовые цели прямо сейчас!</p><a class="default-btn" href="<?= Url::to(['/site/signup']) ?>">присоединиться</a>
    </div>
</div>
<?= NewsHome::widget() ?>
    


