<?php

use yii\db\Migration;

/**
 * Class m180903_101904_add_banners_eng
 */
class m180903_101904_add_banners_eng extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('banner', 'img_en', $this->string(255)->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('banner', 'img_en');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180903_101904_add_banners_eng cannot be reverted.\n";

        return false;
    }
    */
}
