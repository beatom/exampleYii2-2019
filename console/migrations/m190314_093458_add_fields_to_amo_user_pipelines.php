<?php

use yii\db\Migration;

/**
 * Class m190314_093458_add_fields_to_amo_user_pipelines
 */
class m190314_093458_add_fields_to_amo_user_pipelines extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('amo_user_pipelines','trading_school', $this->string(50)->null()->defaultValue(null));
        $this->addColumn('amo_user_pipelines','mailing_material', $this->string(50)->null()->defaultValue(null));
        $this->addColumn('amo_user_pipelines','vebinar_seminar', $this->string(50)->null()->defaultValue(null));
        $this->addColumn('amo_user_pipelines','save_capital_vebinar', $this->string(50)->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('amo_user_pipelines','trading_school');
        $this->dropColumn('amo_user_pipelines','mailing_material');
        $this->dropColumn('amo_user_pipelines','vebinar_seminar');
        $this->dropColumn('amo_user_pipelines','save_capital_vebinar');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190314_093458_add_fields_to_amo_user_pipelines cannot be reverted.\n";

        return false;
    }
    */
}
