<?php

use yii\db\Migration;

/**
 * Class m180316_120528_change_banner_table
 */
class m180316_120528_change_banner_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('banner', 'title', $this->string()->null()->defaultValue(NULL)->after('status'));
        $this->addColumn('banner', 'text_1', $this->string()->null()->defaultValue(NULL)->after('subtitle'));
        $this->addColumn('banner', 'text_2', $this->string()->null()->defaultValue(NULL)->after('text_1'));

        $this->dropColumn('banner', 'text_banner');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('banner', 'title');
        $this->dropColumn('banner', 'text_1');
        $this->dropColumn('banner', 'text_2');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180316_120528_change_banner_table cannot be reverted.\n";

        return false;
    }
    */
}
