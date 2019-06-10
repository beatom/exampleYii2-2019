<?php
use common\models\Events;
$this->title = Yii::t('app', 'Ставки');
$profit = $day->getCurrentProfit();
$result_titles = [
    1 => 'Событие прошло',
    2 => 'Событие не прошло',
    3 => 'Возврат ставки',
];
?>
<div class="content col pt-0">
    <div class="row">
        <div class="col-lg-6">
            <div class="rates-table">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col"><?= Yii::t('app', 'Событие') ?></th>
                            <th scope="col"><?= Yii::t('app', '% от банка') ?></th>
                            <th scope="col"><?= Yii::t('app', 'Ставка') ?></th>
                            <th scope="col"><?= Yii::t('app', 'Кф.') ?></th>
                            <th scope="col"><?= Yii::t('app', 'Результат') ?></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($events as $event) {
                            $result_class = Events::$results_classes[$event->result];
                            ?>
                            <tr>
                                <td><?= $updated_at <= $event->updated_at ? '<div class="notification"><span></span></div>' : null ?><?= $event->title ?></td>
                                <td><?= $event->bank_percent ? $event->bank_percent . '%': '' ?></td>
                                <td><?= $event->bet ?></td>
                                <td><?= $event->coefficient ? $event->coefficient : '' ?></td>
                                <td>
                                    <div class="result <?= $result_class ?>" data-toggle="tooltip" data-placement="bottom" title="<?= $event->result ? $result_titles[$event->result] : null?>">
                                        <div class="result-position"></div>
                                    </div>
                                </td>
                                <td> 
                                <a class="result-title nothing" href="#">
                                    <?php if ($event->free) { ?>
                                        <div class="unlock-rates" data-toggle="tooltip" data-placement="bottom" title="Ставка опубликована абсолютно бесплатно!"></div>
                                    <?php } else { ?>
                                        <div class="lock-rates" data-toggle="tooltip" data-placement="bottom" title="Ставка скрыта для исключения возможности копирования."></div>
                                    <?php }  ?>
                                </a>
                                </td>

                            </tr>
                        <?php } ?>

                        </tbody>
                    </table>
                </div>
                <div class="about-full-description__body">
                  <blockquote>
                    <p class="blockquote">Здесь представлены события, среди которых invest распределил клиентский капитал.</p>
                  </blockquote>
                </div>
            </div>
        </div>
        <div class="col-lg-6 pl-lg-1">
            <div class="growth rates-chat h-auto">
                <div class="rates-chat__item">
                    <?php print_r($message ? '<p>' . nl2br($message) . '</p>' : null); ?>
                    <div class="current-day-result">
                        <div class="current-day-result__title">Текущий<br>результат дня</div>
                        <div class="ProgressBar ProgressBar--animateAll" data-progress="<?= $profit ?>">
                            <svg class="ProgressBar-contentCircle" viewbox="0 0 200 200">
                                <defs>
                                    <lineargradient id="gradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                        <stop offset="0%" stop-color="rgb(255,96,0)" stop-opacity="1"></stop>
                                        <stop offset="100%" stop-color="rgb(147,32,125)" stop-opacity="1"></stop>
                                    </lineargradient>
                                </defs>
                                <circle class="ProgressBar-background" transform="rotate(-90, 100, 100)" cx="100" cy="100" r="95"></circle>
                                <circle class="ProgressBar-circle" transform="rotate(-90, 100, 100)" cx="100" cy="100" r="95" stroke="url(#gradient)"></circle>
                                <span></span>
                                <span class="ProgressBar-percentage ProgressBar-percentage--count"><?= \common\service\Servis::getInstance()->numberSymbol($profit) . \common\service\Servis::getInstance()->beautyProfit($profit, true) ?> </span>
                            </svg>
                        </div>
<!--                        <a class="current-day-result__link show__chat" href="#">Обсудить</a>-->
                    </div>
                </div>
                <?php if($sender) { ?>
                <div class="rates-chat__item--analyst">
                    <div class="rates-chat__item--analyst__avatar"><img src="<?= $sender->avatar ?>"></div>
                    <div class="rates-chat__item--analyst__name"><?= $sender->name ?>, <?= $sender->position ?></div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>