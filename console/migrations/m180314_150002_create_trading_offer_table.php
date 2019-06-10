<?php

use yii\db\Migration;

/**
 * Handles the creation of table `trading_offer`.
 */
class m180314_150002_create_trading_offer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

        $this->dropTable('trading_account_demo');
        $this->dropColumn('trading_account', 'honorar');
        $this->dropColumn('trading_account', 'honorar_partner');
        $this->dropColumn('trading_account', 'responsibility');
        $this->dropColumn('trading_account', 'history_show');
        $this->dropColumn('trading_account', 'trading_period');
        $this->dropColumn('trading_account', 'minimum_deposit');
        $this->dropColumn('trading_account', 'pamm');

        $this->createTable('trading_offer', [
            'id' => $this->primaryKey(),
            'trading_account_id' => $this->integer(11)->notNull(),
            'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'honorar' => $this->integer(3)->notNull()->comment('гонорар управляющему, в %'),
            'honorar_partner' => $this->integer(3)->notNull()->comment('вознаграждение партнеру, %'),
            'responsibility' => $this->integer(3)->notNull()->comment('отвественность управляющего, %'),
            'history_show' => $this->boolean()->notNull()->defaultValue(1)->comment('разрешать показывать историю сделок'),
            'trading_period' => $this->integer(11)->notNull()->comment('торговый период, недель'),
            'minimum_deposit' => $this->integer(11)->notNull()->comment('минимальный депозит'),
            'pamm' => $this->boolean()->notNull()->defaultValue(1)->comment('1 - люди могут вкладывать деньги'),
        ], $tableOptions . ' COMMENT "оферты торговых аккаунтов"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('trading_offer');
    }
}
