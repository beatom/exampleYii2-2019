<?php

use yii\db\Migration;

/**
 * Class m180316_084300_change_sms_log
 */
class m180316_084300_change_sms_log extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('sms_log', 'user_id', $this->integer(11)->null()->defaultValue(null));

        $this->batchInsert('options', [ 'key', 'value', 'description'], [
            [
                'key' => 'about_best_result',
                'value' => '501060',
                'description' => 'Лучший результат партнёра',
            ],
            [
                'key' => 'about_speed_request',
                'value' => '46',
                'description' => 'Средняя скорость ответа поддержки',
            ],
            [
                'key' => 'about_alternative_banks',
                'value' => '90',
                'description' => ' более выгодная альтернатива банкам',
            ],
            [
                'key' => 'about_paid_month',
                'value' => '1500000',
                'description' => 'Выплачено за месяц',
            ],
            [
                'key' => 'about_count_partner',
                'value' => '5062',
                'description' => 'Партнёров invest',
            ],
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('DELETE FROM `options` WHERE `key` = "about_best_result" OR `key`="about_speed_request" OR `key`="about_alternative_banks" OR `key`="about_paid_month" OR `key`="about_count_partner"');
        return true;
    }
}
