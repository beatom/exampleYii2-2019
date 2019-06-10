<?php
use common\service\Servis;
$this->title = Yii::t('app', 'Сообщения');
$service = Servis::getInstance();
?>
<div class="content col pt-0">
    <div class="messages">
        <div class="invitations--title"><a class="back-arrow" href="#">
                <svg class="icon">
                    <use xlink:href="/img/sprites/sprite.svg#left-arrow"></use>
                </svg></a>Сообщения</div>
        <div class="messages-items">
            <?php foreach ($models as $model) { ?>
            <div class="messages-item <?= $model->status ? '' : 'unread'?>" data-id="<?= $model->id ?>">
                <div class="messages-item__user col">
                    <div class="messages-item__user--avatar"><img src="<?= $model->sender->avatar ?>">
                        <?php if(!$model->status ) { ?>
                            <div class="messages-item__user--avatar__count">1</div>
                        <?php }?>
                    </div>
                    <div class="messages-item__user--info">
                        <div class="messages-item__user--info__name"><?= $model->sender->name . ' ' . $model->sender->surname?></div>
                        <div class="messages-item__user--info__date"><?= $service->getDateWord($model->date_add) ?> в <?= date('H:i', strtotime($model->date_add)) ?></div>
                    </div>
                </div>
                <div class="messages-item__message col">
                    <div class="messages-item__message--title"><?= $model->title ?></div>
                    <?= iconv_substr($model->text, 0, 200) . '...' ?></p>
                    <span  class="messages-item__message--text" style="display: none">
                        <?= $model->text ?>
                    </span>
                </div>

                <div class="messages-item__hav-message col">
                <?php if(!$model->status ) { ?>
                    <img class="messages-item__hav-message--ico" src="/img/message-ico.png">
                <?php }?>
                </div>
            </div>
            <?php } ?>
            <?= $this->render('@app/views/layouts/_paginator.php', [
                'pages' => $pages,
                'pageSize' => $pageSize,
                'now_page' => (isset($_GET['page'])) ? (int)$_GET['page'] : 1,
                'link' => $this->context->module->requestedRoute
            ]) ?>
        </div>
        <div class="messages-read">
            <div class="messages-read--incoming">
                <div class="messages-item__user--avatar">
                    <img src="">
                </div>
                <div class="messages-read--incoming__message">
                    <div class="messages-item__user--info">
                        <div class="messages-item__user--info__name"></div>
                        <div class="messages-item__user--info__date"></div>
                    </div>
                    <div class="messages-read--incoming__message--item">
                        
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>