<?php

use yii\db\Migration;

/**
 * Class m180320_084352_insert_into_email_tempalte_table
 */
class m180320_084352_insert_into_email_tempalte_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('email_tempalte', [
            'id' => 10,
            'synonym' => 'Уведомление о создании счета ДУ',
            'title' => 'Поздравляем с созданием нового счета доверительного управления',
            'text' => '<table class="form__table">
                    <tbody>
                    <tr>
                        <td>Счёт:</td>
                        <td>{{account_name}}</td>
                      </tr>
                      <tr>
                        <td>Номер счёта:</td>
                        <td>{{account_number}}</td>
                      </tr>
                      <tr>
                        <td>Пароль:</td>
                        <td>{{password}}</td>
                      </tr>                
                    </tbody>
                  </table>
                  <p>
                  <p>Данный счет не активен, до тех пор пока не заполнена оферта управляющего и не внесен минимальный капитал 500$.</p>',
            'comment' => 'Доступные переменные: {{account_name}} - название счета, {{account_number}} - номер счета, {{password}} - пароль для входа в терминал.'
        ]);

        $this->addColumn('trading_offer', 'remark', $this->text()->null()->defaultValue(NULL));
        $this->addColumn('trading_offer', 'connect_with_me', $this->text()->null()->defaultValue(NULL));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180320_084352_insert_into_email_tempalte_table cannot be reverted.\n";

        return false;
    }
    */
}
