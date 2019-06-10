<?php

use yii\db\Migration;

/**
 * Class m190325_132600_add_events_table
 */
class m190325_132600_add_events_table extends Migration
{
    use common\models\traits\TextTypesTrait;
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

        $this->createTable('days_log',[
            'id' => $this->primaryKey(),
            'date_add' => $this->date()->notNull()->unique(),
            'profit' => $this->double()->notNull()->defaultValue(0),
            'sum_start' => $this->double()->notNull()->defaultValue(0),
            'sum_end' => $this->double()->notNull()->defaultValue(0),
            'comment' => $this->longText()->null()->defaultValue(null)
        ], $tableOptions . ' COMMENT "События"');

        $this->createTable('events',[
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->null()->defaultValue(null),
            'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'bet' => $this->string(255)->null()->defaultValue(null),
            'bank_percent' => $this->double()->notNull()->defaultValue('0'),
            'coefficient' => $this->double()->null()->defaultValue('0'),
            'bookmaker' => $this->string(255)->null()->defaultValue(null),
            'result' => $this->integer(2)->notNull()->defaultValue(0),
            'free' => $this->boolean()->notNull()->defaultValue(FALSE),
            'responsible_user_id' => $this->integer(11)->null()->defaultValue(NULL)
        ], $tableOptions . ' COMMENT "События"');

        $this->addColumn('user', 'payment_system', $this->integer(2)->null()->defaultValue(NULL));
        $this->addColumn('user', 'payment_address', $this->string(255)->null()->defaultValue(NULL));

        $this->dropColumn('user', 'popup_banner_shown');
        $this->dropColumn('user', 'pamm_available');
        $this->dropColumn('user', 'utip_login');
        $this->dropColumn('user', 'utip_email');
        $this->dropColumn('user', 'utip_password');
        $this->dropColumn('user', 'user_category_id');

        $this->dropTable('log_demo_bonus');
        $this->dropTable('main_banner');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('days_log');
        $this->dropTable('events');
        $this->dropColumn('user', 'payment_system');
        $this->dropColumn('user', 'payment_address');
        return true;
    }
}
