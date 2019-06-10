<?php
namespace backend\models;

use common\models\Options;
use yii\base\Model;
use common\models\User;

/**
 * Signup form
 */
class EventsForm extends Model
{

    public $text;
    public $sender_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sender_id', 'text'], 'required'],
            ['sender_id', 'integer', 'min' => 1],

        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function saveForm()
    {
        if (!$this->validate()) {
            return null;
        }

        Options::setOptionValueByKey('bets_sender_id', $this->sender_id);
        Options::setOptionValueByKey('bets_sender_message', $this->text);
        User::setEventsNotice(true);
        return true;
    }

    public function getData(){
        $options = Options::getOptions(['bets_sender_id', 'bets_sender_message']);
        $this->sender_id = $options[0]->value;
        $this->text = $options[1]->value;

    }
}
