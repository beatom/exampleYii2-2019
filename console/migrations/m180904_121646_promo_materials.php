<?php

use yii\db\Migration;

/**
 * Class m180904_121646_promo_materials
 */
class m180904_121646_promo_materials extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('promo_banner',[
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->null()->defaultValue(null),
            'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'show' => $this->boolean()->notNull()->defaultValue(0),
            'folder' => $this->string(255)->null()->defaultValue(null),
        ], $tableOptions . ' COMMENT "Промо баннеры"');

        $this->createTable('promo_banner_image',[
            'id' => $this->primaryKey(),
            'promo_banner_id' => $this->integer(11)->notNull(),
            'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'link' => $this->string(255)->notNull(),
            'type' => $this->string(100)->notNull()->defaultValue('html'),
            'size' => $this->string(255)->notNull(),
            'html_size' => $this->string(255)->null()->defaultValue(null),
            'is_main' => $this->boolean()->notNull()->defaultValue(0),
            'archive_link' => $this->string(255)->null()->defaultValue(null),
        ], $tableOptions . ' COMMENT "Изображения промо баннеров"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('promo_banner');
        $this->dropTable('promo_banner_image');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180904_121646_promo_materials cannot be reverted.\n";

        return false;
    }
    */
}
