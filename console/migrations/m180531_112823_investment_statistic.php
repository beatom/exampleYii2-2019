<?php

use yii\db\Migration;

/**
 * Class m180531_112823_investment_statistic
 */
class m180531_112823_investment_statistic extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('investments', 'summ_show', $this->double()->notNull()->defaultValue(0));
        $this->addColumn('investments', 'earned_show', $this->double()->notNull()->defaultValue(0));
        $this->addColumn('investments', 'profit_show', $this->double()->notNull()->defaultValue(0));
        $this->addColumn('investments', 'statistic_updated', $this->dateTime()->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('investments', 'summ_show');
        $this->dropColumn('investments', 'earned_show');
        $this->dropColumn('investments', 'profit_show');
        $this->dropColumn('investments', 'statistic_updated');
        return true;
    }
    
}
