<?php

use yii\db\Migration;

/**
 * Class m180315_071630_add_messages_templates
 */
class m180315_071630_add_messages_templates extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('email_tempalte', [
            'id' => 4,
            'synonym' => 'Уведомление о создании торгового счета(реального)',
            'title' => 'Поздравляем с созданием нового торгового счета!',
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
                  <p>Теперь Вы можете инвестировать в свой счет и приступать к торговле!</p>',
            'comment' => 'Доступные переменные: {{account_name}} - название счета, {{account_number}} - номер счета, {{password}} - пароль для входа в терминал.'
        ]);
        $this->insert('email_tempalte', [
            'id' => 5,
            'synonym' => 'Уведомление о создании торгового счета(demo)',
            'title' => 'Поздравляем с созданием нового демо счета!',
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
                      <tr>
                        <td>Демо баланс:</td>
                        <td>{{demo_balance}}</td>
                      </tr>   
                    </tbody>
                  </table>',
            'comment' => 'Доступные переменные: {{account_name}} - название счета, {{account_number}} - номер счета, {{password}} - пароль для входа в терминал, {{demo_balance}} - сумма на счету.'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }

}
