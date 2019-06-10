<?php

use yii\db\Migration;

/**
 * Class m181126_101912_add_sms_block_to_user
 */
class m181126_101912_add_sms_block_to_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('sms_block',[
            'id' => $this->primaryKey(),
            'phone' => $this->string(255)->null()->defaultValue(null),
            'date_add' =>  $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'date_end' => $this->dateTime()->defaultValue(null),
            'comment' => $this->string(255)->null()->defaultValue(null),
            'type' => $this->boolean()->notNull()->defaultValue(0),
            'active' => $this->boolean()->notNull()->defaultValue(1),
        ], $tableOptions . ' COMMENT "Списки временно заблокированных номеров для отправки смс"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('sms_block');
        return true;
    }
    
}
