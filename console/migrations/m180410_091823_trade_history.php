<?php

use yii\db\Migration;

/**
 * Class m180410_091823_trade_history
 */
class m180410_091823_trade_history extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('trading_account_history_terminal', 'id_terminal', $this->integer(11)->comment('id в терминале')->after('id'));
        $this->addColumn('trading_account_history_terminal', 'id_trading', $this->integer(11)->comment('id торгового счета')->after('id_terminal'));

        $this->dropColumn('trading_account_history_terminal', 'SPEND_BONUS');
        $this->dropColumn('trading_account_history_terminal', 'COMMISSION');
        $this->dropColumn('trading_account_history_terminal', 'MARGIN');
        $this->dropColumn('trading_account_history_terminal', 'BONUS');

        $this->addColumn('trading_account_history_terminal', 'SL', $this->integer(11)->defaultValue(0)->comment('цена касания для опциона One Touch, нижний уровень зоны для опциона Range'));
        $this->addColumn('trading_account_history_terminal', 'TP', $this->integer(11)->defaultValue(0)->comment('цена касания для опциона One Touch, верхний уровень зоны для опциона Range'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180410_091823_trade_history cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180410_091823_trade_history cannot be reverted.\n";

        return false;
    }
    */
}
