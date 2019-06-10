<?php
/**
 * Created by PhpStorm.
 * User: valb1
 * Date: 17.04.19
 * Time: 12:50
 */
use common\service\Servis;
use common\models\ChatMessageMark;
use yii\helpers\Html;
use common\widgets\ChatWidget;
$service = Servis::getInstance();
?>
<input type="hidden" id="chat__user_id" value="<?= $user_id ?>">
<input type="hidden" id="chat__session_id" value="<?= $session_id ?>">
<input type="hidden" id="chat__moderator" value="<?= $moderator ?>">

<div class="client-chat"><img src="/img/client-chat.png"><span>клиентский чат</span><span class="mobile-only">чат</span></div>
<div id="chat_window" class="chat-item" >
    <div class="mobile-close">
        <svg xmlns="http://www.w3.org/2000/svg">
            <path
                d="M21.005,18.830 L19.438,20.400 L10.816,11.767 L2.194,20.400 L0.626,18.830 L9.248,10.197 L0.626,1.564 L2.194,-0.006 L10.816,8.627 L19.438,-0.006 L21.005,1.564 L12.383,10.197 L21.005,18.830 Z"></path>
        </svg>
    </div>
    <div class="container">
        <div class="chat">
            <h2 class="chat-title">Чат клиентов invest</h2>
            <div id="chat_list" class="chat-items--wrapper">
                <?php
                if($has_more) {
                    echo ' <a id="load_more_chat_items" class=" load-messages last-news__link" href="#">Загрузить еще</a>';
                }?>
                <?php foreach ($messages as $message) {
                    echo ChatWidget::addMessage($message, $user_id, $moderator);
                }
                if(empty($messages)) {
                    echo 'Сообщений пока нет';
                }?>
            </div>
        </div>
    </div>
    <div class="chat-items__bottom" data-position="fixed">
        <div class="container">
            <div class="chat-area">
                <form class="form-chat" action="#">
                    <a id="message_to" class="message-to" href="#" style="display: none">Сообщение @<span></span> x </a>
                            <textarea id="chat__message_input" name="message"
                                      placeholder="Написать сообщение..."></textarea>
                    <input id="chat__parent_id" type="hidden">
                    <button id="chat__message_send" class="send-btn" type="submit"></button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="chat-notification--item">
</div>