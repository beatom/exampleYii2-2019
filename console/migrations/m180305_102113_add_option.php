<?php

use yii\db\Migration;

/**
 * Class m180305_102113_add_option
 */
class m180305_102113_add_option extends Migration
{
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

        $this->createTable('investments_plan',[
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull()->comment('Облигации, Готовое решение, Доверительное управление'),
            'income' => $this->integer(11)->null()->comment('Доход в месяц %'),
            'gift_bonus' => $this->integer(11)->null()->comment('Подарочный бонус $'),
            'min_capital' => $this->integer(11)->null()->comment('Минимальный капитал $'),
             ], $tableOptions . ' COMMENT "инвестиционные планы"');

        $this->batchInsert('investments_plan', [ 'name', 'income', 'gift_bonus', 'min_capital'], [
            [
                'name'=>'Доверительное<br>управление',
                'income'=>'50',
                'gift_bonus'=>'100',
                'min_capital'=>'50',

            ],
            [
                'name'=>'Готовое решение',
                'income'=>'20',
                'gift_bonus'=>'1000',
                'min_capital'=>'100',

            ],
            [
                'name'=>'Облигации',
                'income'=>'5',
                'gift_bonus'=>'10000',
                'min_capital'=>'1000',

            ],
        ]);


        $this->batchInsert('options', [ 'key', 'value', 'description'], [
            [
                'key' => 'partnership_fullpage',
                'value' => '',
                'description' => 'Страница "Партнёрская программа" - Пять доходов',
            ],
            [
                'key' => 'partnership_hiw',
                'value' => '',
                'description' => 'Страница "Партнёрская программа" - Начать просто',
            ],
        ]);

        $this->execute('DELETE FROM `options` WHERE `key` = "PLAN_HOME" OR `key`="trede_static_page_top"');

        $this->createTable('traiding_plan',[
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull()->comment('min, standart, profi'),
            'execution' => $this->string()->null()->comment('ИСПОЛНЕНИЕ с текстом'),
            'spred' => $this->string()->null()->comment('спред с текстом'),
            'min_depozit' => $this->integer(11)->null()->comment('Минимальный депозит $'),
            'commission' => $this->string()->null()->comment('КОМИССИЯ '),
            'min_lot' => $this->float(11)->null()->comment('МИНИМАЛЬНЫЙ ЛОТ'),
            'margin_call' => $this->integer(11)->null()->comment('УРОВЕНЬ MARGIN CALL %'),
            'stop_out' => $this->integer(11)->null()->comment('УРОВЕНЬ STOP ОUT %'),
            'min_step' => $this->float(11)->null()->comment('МИНИМАЛЬНЫЙ ШАГ'),
            'leverage' => $this->string()->null()->comment('КРЕДИТНОЕ ПЛЕЧЕ 1:200'),
        ], $tableOptions . ' COMMENT "торговые планы"');

        $this->batchInsert('traiding_plan', [ 'name', 'execution', 'spred', 'min_depozit', 'commission','min_lot',
                                                    'margin_call','stop_out','min_step','leverage'], [
            [
                'name' => 'Mini',
                'execution' => 'Мгновенное',
                'spred' => 'Плавающий от <i>0.5</i>',
                'min_depozit' => '100',
                'commission' => 'Без комиссии',
                'min_lot' => '0.01',
                'margin_call' => '80',
                'stop_out' => '20',
                'min_step' => '0.01',
                'leverage' => '1:500',
            ],
            [
                'name' => 'Standard',
                'execution' => 'Мгновенное',
                'spred' => 'Плавающий от <i>2</i>',
                'min_depozit' => '250',
                'commission' => 'Без комиссии',
                'min_lot' => '0.01',
                'margin_call' => '70',
                'stop_out' => '30',
                'min_step' => '0.01',
                'leverage' => '1:200',
            ],
            [
                'name' => 'Profi',
                'execution' => 'Рыночное',
                'spred' => 'Плавающий от <i>0.5</i>',
                'min_depozit' => '1000',
                'commission' => '10 $ за 1 лот',
                'min_lot' => '0.01',
                'margin_call' => '60',
                'stop_out' => '40',
                'min_step' => '0.01',
                'leverage' => '1:200',
            ],
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('investments_plan');
        $this->dropTable('traiding_plan');

        $this->execute('DELETE FROM `options` WHERE OR `key`="partnership_fullpage"  OR `key`="partnership_hiw"');
        return true;
    }
}
