<?php

use yii\db\Migration;

/**
 * Class m190408_085150_message_templates
 */
class m190408_085150_message_templates extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('chat_template', ['id', 'synonym','text','comment','title'], [
            [
                'id' => 15,
                'synonym' => 'отказ в верификации',
                'text' => 'В верификации было отказано',
                'comment' => '',
                'title' => 'В верификации отказано',
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190408_085150_message_templates cannot be reverted.\n";

        return false;
    }
    */
}
