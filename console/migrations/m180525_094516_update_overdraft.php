<?php

use yii\db\Migration;

/**
 * Class m180525_094516_update_overdraft
 */
class m180525_094516_update_overdraft extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('overdraft', 'user_balance', $this->double()->notNull()->defaultValue(0)->comment('Баланс на счету пользователя после получения овердрафта'));
        $this->addColumn('overdraft', 'percent', $this->double()->notNull()->defaultValue(0)->comment('Соотношение овердрафта к балансу пользователя до его получения'));
        $this->addColumn('overdraft', 'full_summ', $this->double()->notNull()->defaultValue(0)->comment('Полная сумма овердрафта'));
        $this->addColumn('overdraft', 'start_comment', $this->text()->null()->defaultValue(null)->comment('Описание вычеслений на начало овердрафта'));
        $this->addColumn('overdraft', 'end_comment', $this->text()->null()->defaultValue(null)->comment('Описание вычеслений при закрытии овердрафта'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('overdraft', 'user_balance');
        $this->dropColumn('overdraft', 'percent');
        $this->dropColumn('overdraft', 'full_summ');
        $this->dropColumn('overdraft', 'start_comment');
        $this->dropColumn('overdraft', 'end_comment');
        
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180525_094516_update_overdraft cannot be reverted.\n";

        return false;
    }
    */
}
