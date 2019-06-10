<?php

use yii\db\Migration;

/**
 * Class m180926_123119_add_amo_custom_fields_table
 */
class m180926_123119_add_amo_custom_fields_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('amo_custom_field',[
            'id' => $this->primaryKey(),
            'amo_field_id' => $this->integer(11)->notNull(),
            'name' => $this->string(255)->null()->defaultValue(null),
            'sort' => $this->integer(11)->null()->defaultValue(null),
            'field_type' => $this->integer(11)->null()->defaultValue(null),
            'is_system' => $this->boolean()->notNull()->defaultValue(0),
            'is_multiple' => $this->boolean()->notNull()->defaultValue(0),
            'is_editable' => $this->boolean()->notNull()->defaultValue(0),
            'is_required' => $this->boolean()->notNull()->defaultValue(0),
            'is_deletable' => $this->boolean()->notNull()->defaultValue(0),
            'is_visible' => $this->boolean()->notNull()->defaultValue(0),
            'params' => $this->text()->null()->defaultValue(null),
        ], $tableOptions . ' COMMENT "Кастомные поля в AmoCrm"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('amo_custom_field');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180926_123119_add_amo_custom_fields_table cannot be reverted.\n";

        return false;
    }
    */
}
