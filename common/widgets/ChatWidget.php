<?php

namespace common\widgets;

use common\models\ChatMessage;
use yii\base\Widget;
use Yii;
use frontend\assets\ChatAsset;
use common\service\Servis;
use yii\helpers\Html;

class ChatWidget extends Widget
{
    public $messages;
    public $user_id;
    public $session_id;
    public $user_can_comment;
    public $has_more;
    public $moderator;

    const socials = [
        'vk' => 'vk-ico',
        'facebook' => 'facebook-ico',
        'telegram' => 'telegram',
        'instagram' => '',
    ];

    public function init()
    {
        ChatAsset::register($this->view);
        parent::init();

        $this->messages = ChatMessage::find()
            ->where(['deleted_at' => null, 'parent_id' => null])
            ->with(['user', 'childs'])
            ->orderBy('id DESC, date_add ASC')
            ->limit(10)
            ->all();
        $all_count = ChatMessage::find()
            ->where(['deleted_at' => null, 'parent_id' => null])
            ->count();
        $this->has_more = $all_count > 10 ? true : false;
        $this->messages = $this->messages ? $this->messages : [];
        $this->messages = array_reverse($this->messages);
        $this->user_id = Yii::$app->user->isGuest ? false : Yii::$app->user->id;
        $this->session_id = Yii::$app->session->getId();
        $this->moderator = Yii::$app->user->isGuest ? false : Yii::$app->user->identity->can('moderator');
    }

    public static function addMessage($message, $user_id, $moderator = false)
    {
        $service = Servis::getInstance();
        $response = '<div id="m' . $message->id . '" class="chat-items chat__message" data-message_id="' . $message->id . '" data-name="';
        $response .= $message->user->firstname ? $message->user->firstname : $message->user->username;
        $response .= '"><div class="chat-item__user-info">
                <img class="chat-item__user-info__avatar rounded-circle" src="' . $message->user->avatar . '" alt="">
                <div class="chat-item__user-info__message-count">
                    сообщений:<span>' . $message->user->chat_messages_count . '</span></div>
            </div>
            <div class="chat-item__body-items">
                <div class="chat-item__header">
                    <div class="chat-item__user-name">';

        $response .= $message->user->getNameString();
        if ($message->parent_id) {
            $response .= '<div class="chat-item__reply-to">@';
            $response .= $message->parent->user->firstname ? $message->parent->user->firstname : $message->parent->user->username;
            $response .= '</div>';
        }

        $response .= '</div><div class="chat-item__social-list">';

        foreach ($message->user->social->getArray() as $key => $s) {
            if (key_exists($key, static::socials)) {
                if ($key == 'instagram') {
                    $response .= '<li><a class="icons" href="' . $s . '"><div class="instagram-ico"></div></a></li>';
                } else {
                    $response .= '<li><a class="icons" href="' . $s . '"><svg><use xlink:href="/img/sprites/sprite.svg#' . static::socials[$key] . '"></use></svg></a></li>';
                }
            }
        }

        $response .= '</div>';
        if ($message->user->verified) {
            $response .= '<div class="chat-item__registration-info" data-toggle="tooltip" data-placement="top"
                             title="Данный пользователь является клиентом invest"><img src="/img/svg/verification-ico.svg"
                                                                                       alt=""></div>';
        }

        $response .= '<div class="chat-item__balance">Баланс
                        депозита:<span>' . $service->beautyDecimal($message->user->getBalance(), 0) . '$</span></div>
                </div>
                <div class="chat-item__body">
                    <p>' . nl2br(Html::encode($message->text)) . '</p>
                    <div class="chat-item__body--links">
                        <div class="chat-item__body--rating-items">
                            <div class="chat-item__body--rating like">
                                <svg class="ico">
                                    <use xlink:href="/img/sprites/sprite.svg#like"></use>
                                </svg>' . $message->likes . '
                            </div>
                            <div class="chat-item__body--rating dislike">
                                <svg class="ico">
                                    <use xlink:href="/img/sprites/sprite.svg#dislike"></use>
                                </svg>' . $message->dislikes . '
                            </div>
                        </div>
                        <div class="chat-item__body--posted">' . $service::getDateLifetime($message->date_add) . '</div>';
        if ($user_id != $message->user_id) {
            $response .= '<a class="chat__reply chat-item__body--edit" href="#">Ответить</a>';
        }
        if ($moderator) {
            
            $response .= '<div class="chat_moderator_section"><a class="chat__edit chat-item__body--edit" href="#">Редактировать</a>';
            $response .= '<a class="chat__delete chat-item__body--edit" href="#">Удалить</a></div>';
        }
        $response .= '</div>
                </div>
            </div>
        </div>';
        if (!$message->branch_id AND !empty($message->childs)) {
            $i = 1;
            $response .= '<div id="b' . $message->id . '" class="reply-list">';
            foreach ($message->childs as $child) {
                if ($i == 3) {
                    $response .= '<div class="show-answer">';
                }
                $response .= static::addMessage($child, $user_id, $moderator);
                $i++;
            }
            if ($i > 3) {
                $response .= '</div><div class="chat-item__show-all"><svg><use xlink:href="/img/sprites/sprite.svg#arrow-down"></use></svg>Показать все ответы ' . ($i - 1) . '</div>';
            }
            $response .= '</div>';
        }
        return $response;
    }

    public function run()
    {
        if($_SERVER["REMOTE_ADDR"] == '127.0.0.1' OR (!Yii::$app->user->isGuest AND in_array(@Yii::$app->user->identity->username,['test', 'test1', 'test2', 'test3']))) {
            return $this->render('chat', [
                'has_more' => $this->has_more,
                'messages' => $this->messages,
                'user_id' => $this->user_id,
                'session_id' => $this->session_id,
                'moderator' => $this->moderator
            ]);
        }

    }
}