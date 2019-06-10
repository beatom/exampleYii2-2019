<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::$app->language == 'ru' ? 'О компании' : 'About Us';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="about-info-top">
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-12">
                <div class="about-info-top__body wow animated fadeInLeft">
                    <h3>Компания invest</h3>
                    <p>
                        Мир спорта - это огромная индустрия бизнеса, в обороте которой миллиарды долларов.
                        Но для того, чтобы извлекать хоть какую-то прибыль из этого бизнеса нужно быть дисциплинированным,
                        иметь аналитический склад ума и хладнокровно подходить разбору спортивных событий и новостей.
                        Звучит это проще, чем выходит на самом деле, так как в подавляющем большинстве игроки быстро вылетают,
                        потеряв не только деньги, но и драгоценное время
                    </p>
                </div>
            </div>
            <div class="col-md-6 col-12">
                <div class="about-info-top__body what-do wow animated fadeInRight">
                    <h3>Что же предлагает invest?</h3>
                    <p>
                        Мы выявили, что важнейшим фактором, помимо аналитики, является грамотное управление капиталом и
                        оценка риска относительно предлагаемого коэффициента на событие. То есть, главное - не количество выигранных событий,
                        а высокий коэффициент окупаемости инвестиций (ROI - return on investment).
                        Другими словами реально принесенная прибыль в процентном соотношении от вложенных средств.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="about-full-description wow animated fadeInUp" data-wow-delay="1s">
        <div class="about-full-description__top">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="about-full-description__title">Как работает invest?
                            <div class="about-full-description__btn"  data-toggle="modal" data-target="#show-video" data-video="https://www.youtube.com/embed/D2pDzJK3_84" data-title="Как работает invest?">
                                <div class="about-full-description__btn--circle"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="about-full-description__body">
                        <p>
                            invest создал и внедрил уникальную систему по грамотному управлению капиталом на спортивных событиях.Система работает следующим образом: Вы пополняете свой банк и средства хранятся на вашем счету в invest. Специалисты команды invest берут только небольшую часть от этих средств, переводят их в БК, и распределяют сразу среди нескольких событий, делая это в различных букмекерских компаниях и по выгодным коэффициентам. В итоге: на каждое событие своя заранее определённая букмекерская компания и своя сумма относительно оцениваемого риска. Данный процесс полностью исключает эмоциональный фактор и дает высокий показатель окупаемости инвестиций, при низком соотношении риска. После выигрыша событий, средства с букмекерской компании возвращаются к вам обратно в личный кабинет invest, откуда они доступны для вывода в любой день. invest автоматически списывает 40% от чистой прибыли в оплату за свои услуги при подведении итогов дня.
                        </p>
                        <blockquote>
                            <p class="blockquote">В конечном счёте вам остается лишь пополнить свой банк от 5$ и снимать профит в любое удобное время!</p>
                        </blockquote>
                    </div>
                </div>
                   </div>
            <a class="default-btn mobile-only" style="margin-top: 30px;max-width: 190px;" href="<?= Url::to(['/site/signup']) ?>">Хочу начать!</a>

        </div>
    </div>
</div>
<div id="team" class="team">
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="team-title">
                    <!--.team-title__before.wow.delay-1s.animated.pulse-->
                    <div class="team-title__typing wow delay-3s">Наша команда - наш самый ценный актив</div>
                    <!--.team-title__after.wow.delay-1s.animated.pulse-->
                </div>
                <div class="team-body slider">
                    <div class="team-item wow animated rotateIn">
                        <div class="team-item__top"><img class="team-item__avatar" src="./img/team-1.png">
                            <div class="team-item__info">
                                <div class="team-item__name">Александр Маслов</div>
                                <div class="team-item__position">Ведущий аналитик invest</div>
                            </div>
                        </div>
                    </div>
                    <div class="team-item wow animated rotateIn" data-wow-delay="0.5s">
                        <div class="team-item__top"><img class="team-item__avatar" src="./img/team-2.png">
                            <div class="team-item__info">
                                <div class="team-item__name">Виктория Ломакина</div>
                                <div class="team-item__position">Руководитель клиентской поддержки</div>
                            </div>
                        </div>
                    </div>
                    <div class="team-item wow animated rotateIn" data-wow-delay="1s">
                        <div class="team-item__top"><img class="team-item__avatar" src="./img/team-3.png">
                            <div class="team-item__info">
                                <div class="team-item__name">Тимур Тагиев</div>
                                <div class="team-item__position">Маркетолог</div>
                            </div>
                        </div>
                    </div>
                    <div class="team-item wow animated rotateIn" data-wow-delay="1.5s">
                        <div class="team-item__top"><img class="team-item__avatar" src="./img/team-4.png">
                            <div class="team-item__info">
                                <div class="team-item__name">Виктор Ковалевский</div>
                                <div class="team-item__position">Руководитель аналитического отдела</div>
                            </div>
                        </div>
                    </div>
                    <div class="team-item wow animated rotateIn" data-wow-delay="2s">
                        <div class="team-item__top"><img class="team-item__avatar" src="./img/team-5.png">
                            <div class="team-item__info">
                                <div class="team-item__name">Денис Ляхов</div>
                                <div class="team-item__position">Аналитик invest</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div  id="benefits" class="benefits">
    <div class="container">
        <div class="row">
            <div class="col">
                <h2>Преимущества работы с invest</h2>
                <div class="benefits-body">
                    <div class="benefits-item wow animated fadeIn">
                        <svg class="benefit-ico">
                            <use xlink:href="./img/sprites/sprite.svg#1"></use>
                        </svg>
                        <div class="benefits-item__title">Широкий выбор  вариантов пополнений</div>
                        <p>Карты Visa/MasterCard, ЯндексДеньги, Альфа-клик и ещё более 10 платёжных систем</p>
                    </div>
                    <div class="benefits-item wow animated fadeIn" data-wow-delay="0.5s">
                        <svg class="benefit-ico">
                            <use xlink:href="./img/sprites/sprite.svg#2"></use>
                        </svg>
                        <div class="benefits-item__title">Деньги всегда под рукой</div>
                        <p>Выводите нужную сумму в любой день и на удобные реквизиты!</p>
                    </div>
                    <div class="benefits-item wow animated fadeIn" data-wow-delay="1s">
                        <svg class="benefit-ico">
                            <use xlink:href="./img/sprites/sprite.svg#3"></use>
                        </svg>
                        <div class="benefits-item__title">Бонусный Кэшбэк</div>
                        <p>20% заработка компании от  приглашённого на постоянной основе</p>
                    </div>
                    <div class="benefits-item wow animated fadeIn" data-wow-delay="1.5s">
                        <svg class="benefit-ico">
                            <use xlink:href="./img/sprites/sprite.svg#4"></use>
                        </svg>
                        <div class="benefits-item__title">Официальный договор</div>
                        <p>Отношение с сервисом invest подкреплены официальным клиентским договором публичной оферты</p>
                    </div>
                    <div class="benefits-item wow animated fadeIn" data-wow-delay="2s">
                        <svg class="benefit-ico">
                            <use xlink:href="./img/sprites/sprite.svg#5"></use>
                        </svg>
                        <div class="benefits-item__title">Прозрачность</div>
                        <p>Только invest открыто публикует ещё до начала спортивных событий совершённые ставки</p>
                    </div>
                    <div class="benefits-item wow animated fadeIn" data-wow-delay="2.5s">
                        <svg class="benefit-ico">
                            <use xlink:href="./img/sprites/sprite.svg#file"></use>
                        </svg>
                        <div class="benefits-item__title">Регистрация в РФ</div>
                        <p>Компания предоставляющая аналитику ООО “Айрейз” зарегистрирована на территории России под номером 6165219331</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="agreements" class="treaties-agreements">
    <div class="container">
        <div class="row">
            <div class="col">
                <h2 class="wow animated fadeInUp">Договоры и соглашения</h2>
                <p class="wow animated fadeInUp">ООО «Айрейз» – ключевая компания, входящая в структуру сервиса invest. Организация зарегистрирована в России и специализируется на поставке подробной аналитики в сегменте спортивных событий. В её штате работают ведущие специалисты, которые собирают, систематизируют и обрабатывают информацию. Эксперты компании определяют вероятные исходы событий и занимаются распределением капитала.Компания invest LTD использует технические ресурсы сервиса invest.biz.</p>
                <ul>
                    <li class="wow animated fadeIn"><a target="_blank" href="/img/documents/MAIN_invest.pdf">Компания ООО “Айрейз"</a></li>
                    <li class="wow animated fadeIn"><a target="_blank" href="/img/documents/invest_LTD.pdf">Компания invest LTD</a></li>
                </ul>
                <div class="treaties-agreements__body">
                    <a class="treaties-agreements-item wow animated fadeIn" href="/img/doc/CLIENT_AGREEMENT.pdf" target="_blank" data-wow-delay="0.5s">
                        <img class="treaties-agreements-ico" src="/img/pdf-ico.png">
                        <span>Клиентский договор</span>
                    </a>
                    <a class="treaties-agreements-item wow animated fadeIn" href="/img/doc/AML.pdf" target="_blank" data-wow-delay="1s">
                        <img class="treaties-agreements-ico" src="/img/pdf-ico.png">
                        <span>Политика AML</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>