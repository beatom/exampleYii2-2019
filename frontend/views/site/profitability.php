<?php
use yii\helpers\Url;

$mounts = ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December',];
if (\Yii::$app->language == 'ru') {
    setlocale(LC_ALL, 'ru_RU.UTF-8');
    $mounts = ['', 'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];
}

?>
    <div class="last-profitability">
        <div class="container">
            <div class="row">
                <div class="col">
                    <h2>Цифры говорят сами за себя</h2>
                    <div class="row flex-md-row flex-column">
                        <div class="col number-info wow animated fadeInLeft">
                            <div class="display_table">
                                <div class="odometer-item">
                                    <div class="odometer-item__value">+</div>
                                    <div class="odometer" id="odometer" data-start="<?= $data['hundred'] ?>"></div>
                                    <div class="odometer-item__value">%</div>
                                </div>
                            </div>
                            <p class="counter-desription">Доходность invest за последние 100 дней</p>
                            <div class="input-group"><a class="settings-account__btn"
                                                        href="<?= Url::to(['/site/signup']) ?>">Регистрация</a></div>
                        </div>
                        <div class="col section-col">
                            <div class="section-number__pie">
                                <div
                                    class="section-number__img-wrapper section-number__img-wrapper_first wow animated fadeInUp"
                                    data-wow-delay="0s">
                                    <div class="choose-day-month">
                                        <div class="choose-day-month--title">Выберите месяц/день</div>
                                    </div>
                                </div>
                                <div
                                    class="section-number__img-wrapper section-number__img-wrapper_second wow animated fadeInUp"
                                    data-wow-delay=".7s">
                                    <div class="grafic-tabs">
                                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                                            <li class="nav-item"><a class="nav-link active" id="month-tab"
                                                                    data-toggle="tab" href="#month" role="tab"
                                                                    aria-controls="home" aria-selected="true">месяцы</a>
                                            </li>
                                            <li class="nav-item"><a class="nav-link" id="days-tab" data-toggle="tab"
                                                                    href="#days" role="tab" aria-controls="days"
                                                                    aria-selected="false">дни</a></li>
                                        </ul>
                                        <div class="tab-content" id="myTabContent">
                                            <div class="tab-pane fade show active" id="month" role="tabpanel"
                                                 aria-labelledby="month-tab">
                                                <div class="month-grafic" id="month-grafic"></div>
                                            </div>
                                            <div class="tab-pane fade" id="days" role="tabpanel"
                                                 aria-labelledby="days-tab">
                                                <div class="month-grafic days-grafic" id="days-grafic"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <a class="default-btn mobile-only" style="margin-top: 30px;" href="<?= Url::to(['/site/signup']) ?>">Хочу начать!</a>
    </div>
    <div class="management-history">
        <div class="container">
            <div class="row">
                <div class="col wow animated fadeIn">
                    <h2>История управления</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-5 col">
                    <div class="personal-history wow animated fadeInLeft">
                        <div class="personal-history__top">
                            <div class="personal-history__top--select-date">Выберите дату</div>

                            <div class="personal-history__top--slider">
                                <div class="slider single-item slider-for">
                                    <?php foreach ($data['months'] as $key => $value) { ?>
                                        <div>
                                            <div
                                                class="date-item"><?= $mounts[date("n", $key)] . ' ' . date('Y', $key) ?></div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="personal-history__item">
                            <div class="slider-nav">
                                <?php
                                $today = strtotime(date('Y-m-d') . ' -1 day');
                                if (date('H') < 10) {
                                    $today = strtotime(date('Y-m-d') . ' -2 days');
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
                                                <th class="day-off" scope="col">Сб</th>
                                                <th class="day-off" scope="col">Вс</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($month as $day) {
                                                if ($i == 1) {
                                                    echo '<tr>';
                                                }
                                                ?>
                                                <td class="<?= $today == $day['date'] ? 'active' : null ?>" <?= $day['id'] ? 'data-day="' . $day['id'] . '"' : null ?>>
                                                    <div
                                                        class="table-item <?= $day['result'] ?>">
                                                        <?= $day['profit'] ?>
                                                        <span class="not-mobile"><?= $day['date_short'] ?></span>
                                                        <span class="mobile-only"><?= date('d', $day['date']) ?></span>
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
                <div class="col-md-7">
                    <div class="rates-table wow animated fadeInRight">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th scope="col">Событие</th>
                                    <th scope="col">% от банка</th>
                                    <th scope="col">Ставка</th>
                                    <th scope="col">Коэффициент</th>
                                    <th scope="col">Результат</th>
                                    <th scope="col"></th>
                                </tr>
                                </thead>
                                <tbody id="day_events_list">
                                <?php
                                if (empty($data['current']['events'])) {
                                    echo '<tr><td colspan="6" style="text-align: center;">Новых событий пока нет</td></tr>';
                                } else {
                                    foreach ($data['current']['events'] as $event) {
                                        ?>
                                        <tr>
                                            <td><?= $event['title'] ?></td>
                                            <td><?= $event['bank_percent'] ?></td>
                                            <td><?= $event['bet'] ?></td>
                                            <td><?= $event['coefficient'] ?></td>
                                            <td>
                                                <div class="result <?= $event['result_class'] ?>">
                                                    <div class="result-position"></div>
                                                </div>
                                            </td>
                                            <td>
                                                <a class="result-title nothing" href="#">
                                                    <?php if ($event['free']) { ?>
                                                        <div class="unlock-rates" data-toggle="tooltip" data-placement="bottom" title="Ставка опубликована абсолютно бесплатно!"></div>
                                                    <?php } else { ?>
                                                        <div class="lock-rates" data-toggle="tooltip" data-placement="bottom" title="Ставка скрыта для исключения возможности копирования."></div>
                                                    <?php } ?>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="registration">
        <div class="container">
            <div class="row">
                <div class="col d-flex align-items-center flex-lg-row flex-column"><img
                        class="registration-img wow animated fadeInLeft" src="/img/invest_Stavki_INDEX (8).png" alt="">
                    <div class="registration-info wow animated fadeInRight">
                        <h4>Хотите узнать на какие события invest распределила деньги сегодня?<br>Просто пройдите
                            регистрацию!</h4><a class="settings-account__btn" href="<?= Url::to(['/site/signup']) ?>">Зарегистрироваться</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var days_events = <?php print_r(json_encode($data['days'])) ?>;
    </script>

<?php
$graphics = 'Highcharts.chart(\'month-grafic\', {
        chart: {
            styledMode: true,
            type: \'area\',
            style: {
                fontFamily: \'serif\'
            }
        },
        title: {
            text: \'\',
        },
        yAxis: {
            title: {
                text: \'\'
            },
            labels: {
                format: \'{value}%\'
            },
        },
        xAxis: {
            categories: [';
foreach ($data['graf_months']['name'] as $key => $item) {
    $graphics .= $key > 0 ? ',"' . $item . '"' : '"' . $item . '"';
}
$graphics .= '],
            title: {
                enabled: false
            }
        },
        tooltip: {
            valueSuffix: \'%\',
            split: false,
            borderRadius: 30,
            distance: 30,
            padding: 10,
            shared: true
        },
        legend: {
            enabled: false
        },
        defs: {
            gradient0: {
                tagName: \'linearGradient\',
                id: \'gradient-0\',
                x1: 0,
                y1: 0,
                x2: 0,
                y2: 1,
                children: [{
                    tagName: \'stop\',
                    offset: 0
                }, {
                    tagName: \'stop\',
                    offset: 1
                }]
            },
            gradient1: {
                tagName: \'linearGradient\',
                id: \'gradient-1\',
                x1: 0,
                y1: 0,
                x2: 0,
                y2: 1,
                children: [{
                    tagName: \'stop\',
                    offset: 0
                }, {
                    tagName: \'stop\',
                    offset: 1
                }]
            }
        },
        series: [{
            name: \'Доход\',
            marker: {
                symbol: \'url(../img/grafic-ico.png)\',
                width: 20,
                height: 20
            },
            data: [';
foreach ($data['graf_months']['value'] as $key => $item) {
    $graphics .= $key > 0 ? ',' . $item : $item;
}
$graphics .= '],
            dashStyle: \'longdash\'
        }],
        responsive: {
            rules: [{
                condition: {
                    maxWidth: 930
                },
                chartOptions: {
                    legend: {
                        layout: \'horizontal\',
                        align: \'center\',
                        verticalAlign: \'bottom\'
                    }
                }
            }]
        }
    });

    <!-- days grafic -->
    var prev_y = 0;
    Highcharts.chart(\'days-grafic\', {
        chart: {
            styledMode: true,
            type: \'area\'
        },
        title: {
            text: \'\',
        },
        yAxis: {
            title: {
                text: \'\'
            },
            labels: {
                format: \'{value}%\'
            },
        },
        xAxis: {
            categories: [';
foreach ($data['graf_days']['name'] as $key => $item) {
    $graphics .= $key > 0 ? ',"' . $item . '"' : '"' . $item . '"';
}
$graphics .= '],
            title: {
                enabled: false
            }
        },
        tooltip: {
            valueSuffix: \'%\',
            split: false,
            borderRadius: 30,
            distance: 30,
            padding: 10,
            shared: true,
            formatter: function () {
            
               var s = \'\';
                $.each(this.points, function () {
                   s += this.x + \'<br>\' + this.point.z + \' %\';
                });
                return s;
            },
        },
        legend: {
            enabled: false
        },
        defs: {
            gradient0: {
                tagName: \'linearGradient\',
                id: \'gradient-2\',
                x1: 0,
                y1: 0,
                x2: 0,
                y2: 1,
                children: [{
                    tagName: \'stop\',
                    offset: 0
                }, {
                    tagName: \'stop\',
                    offset: 1
                }]
            },
            gradient1: {
                tagName: \'linearGradient\',
                id: \'gradient-3\',
                x1: 0,
                y1: 0,
                x2: 0,
                y2: 1,
                children: [{
                    tagName: \'stop\',
                    offset: 0
                }, {
                    tagName: \'stop\',
                    offset: 1
                }]
            }
        },
        series: [{
            name: \'Доход\',
            marker: {
                symbol: \'url(../img/grafic-ico.png)\',
                width: 20,
                height: 20
            },
            data: [';
foreach ($data['graf_days']['values'] as $key => $item) {
    $graphics .= $key > 0 ? ',{"y" : ' . $item['y'] . ', "z" : ' . $item['z'] . '}' : '{"y" : ' . $item['y'] . ', "z": ' . $item['z'] . '}';
}
$graphics .= '],
            dashStyle: \'longdash\'
        }],
        responsive: {
            rules: [{
                condition: {
                    maxWidth: 930
                },
                chartOptions: {
                    legend: {
                        layout: \'horizontal\',
                        align: \'center\',
                        verticalAlign: \'bottom\'
                    }
                }
            }]
        }
    });';

$this->registerJS($graphics); ?>