<?php

namespace common\service\api;

use common\models\AmoCustomField;
use common\models\AmoQueue;
use common\models\AmoUserPipelines;
use common\models\BalanceLog;
use common\models\Country;
use common\models\ManagerCard;
use common\models\User;
use PHPUnit\Framework\Exception;
use common\models\Options;
use common\service\LogMy;

class AmoCrm
{
    private static $instance = null;
    private $amo_crm_enabled = false;

    private $auth_email = null;
    private $auth_hash = '1'; //68618a98201fba627126350d27009008f6c9e1f7
    private $url = null;
    private $authorised = false;
    private $authorise_action = '/private/api/auth.php?type=json';
    private $authirise_date = null;
    public static $main_manager_id = 2451640;

    const tag_levels = [
        0 => [
            'bottom' => 0,
            'top' => 199,
            'title' => 'Массовый сегмент',
        ],
        1 => [
            'bottom' => 200,
            'top' => 500,
            'title' => 'Категория С',
        ],
        2 => [
            'bottom' => 500,
            'top' => 1000,
            'title' => 'Категория Б',
        ],
        3 => [
            'bottom' => 1000,
            'top' => 5000,
            'title' => 'Категория А',
        ],
        4 => [
            'bottom' => 5000,
            'top' => 999999999,
            'title' => 'Значимый клиент',
        ],
    ];

    const del_tag_array = [
        'Категория Б', 'категория Б', 'категория б', 'Категория б',
        'Категория А', 'категория А', 'категория а', 'Категория а', 'Категория A', 'категория A', 'категория a', 'Категория a',
        'Категория А+', 'категория А+', 'категория а+', 'Категория а+', 'Категория A+', 'категория A+', 'категория a+', 'Категория a+',
        'Категория C', 'Категория c', 'категория с', 'категория С', 'Категория б', 'Значимый клиент', 'значимый клиент', 'Категория С',
        'Массовый сегмент', 'массовый сегмент'
    ];

    const name_levels = [
        0 => 'Потенциальный клиент',
        1 => 'Действующий клиент',
        2 => 'Приостановил сотрудничество'
    ];

    private $custom_fields_depends = [
        'phone' => 'Телефон',
        'email' => 'Email',
        'date_bithday' => 'Дата рожденья',
        'date_reg' => 'Дата регистрации',
        'firstname' => 'ФИО',
        'lastname' => 'ФИО',
        'middlename' => 'ФИО',
        'country_id' => 'Страна/город',
        'city_name' => 'Страна/город',
        'status_in_partner' => 'Должность',
        'ip' => 'Вход на сайт',
        'first_deposit' => 'Введено',
        'balance' => 'Баланс',
        'balance_partner' => 'Баланс',
        'earned' => 'Заработано'
    ];

    public $pipelines_stage = [
        1 => '1340179', //Новый фильтр консультанты
        2 => '1340212', //Действующие инвесторы НОВАЯ
        3 => '1352404', //Инвесторы, прекратившие сотрудничество.
        4 => '1174639', //Воронка (не активные)
        5 => '1175350', //Постпродажное обслуживание (Инвесторы)
        6 => '1463086', // save capital
    ];

    const toucheble_pipelines = [
        '1340179', '1340212', '1352404'
    ];

    const updatable_pipelines = [
        '1340179', '1340212', '1352404', '1174639', '1175350'
    ];

    public $pipelines_status = [
        '1340179' => '21577060', //Новый фильтр консультанты - фильтр
        '1340212' => '21577330', //Действующие инвесторы НОВАЯ - Первичный контакт
        '1352404' => '21679297', //Инвесторы, прекратившие сотрудничество. - фильтр
        '1174639' => '19987168', // Воронка (не активные) - фильтр
        '1175350' => '19993816', //Постпродажное обслуживание (Инвесторы) - Появился новый контакт
        '1463086' => '22666090', //  save capital - Взять в работу
    ];

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();

            self::$instance->auth_email = \Yii::$app->params['amo_auth_email'];
            self::$instance->url = \Yii::$app->params['amo_url'];
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->amo_crm_enabled = Options::getOptionValueByKey('amo_crm_enabled') ? true : false;

    }

    private function __clone()
    {
    }

    public static function getTag($balance)
    {
        $balance = doubleval($balance);
        $tag = [];
        foreach (static::tag_levels as $key => $l) {
            if ($balance >= $l['bottom'] AND $balance < $l['top']) {
                $tag['name'] = $l['title'];
                $tag['level'] = $key;
                break;
            }
        }
        return $tag;
    }

    private function getFileContent($action, $str_get_param)
    {

        \Yii::info(['name' => 'getFileContent запрос',
            'action' => $action,
            'data' => $str_get_param,
        ], 'terminal');


        $url = $this->url . $action;
        $str_get_param = str_replace(' ', '%20', $str_get_param);
        $res = json_decode(file_get_contents($url . '?' . $str_get_param), true);

        if ($res['result'] == 'failed') {
            \Yii::error(['name' => 'getFileContent ответ',
                'url' => $url,
                'response' => $res,
            ], 'terminal_error');
        } else {
            \Yii::info(['name' => 'getFileContent ответ',
                'data' => $res,
            ], 'terminal');
        }

        return $res;
    }

    public function curl($action, $data = null)
    {
        if ((!$this->authorised OR $this->authirise_date < strtotime(date('Y-m-d H:i:s') . ' +5 minutes')) AND $action != $this->authorise_action) {
            $this->login();
        }
        \Yii::info(['name' => 'Curl запрос',
            'action' => $action,
            'data' => $data,
        ], 'amocrm');

        // $data = http_build_query($data);

        $url = $this->url . $action;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
        curl_setopt($curl, CURLOPT_URL, $url);


        if ($data) {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        }
        curl_setopt($curl, CURLOPT_HEADER, false);

        curl_setopt($curl, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
        curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookie.txt');

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);

        $out = curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную


        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE); #Получим HTTP-код ответа сервера
        curl_close($curl); #Завершаем сеанс cURL
        /* Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
        $code = (int)$code;
        $errors = array(
            301 => 'Moved permanently',
            400 => 'Bad request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not found',
            500 => 'Internal server error',
            502 => 'Bad gateway',
            503 => 'Service unavailable'
        );

//        if($this->authorised) {
//            var_dump($out);
//            die;
//        }

        try {
            #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
            if ($code != 200 && $code != 204)
                throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error', $code);
        } catch (Exception $E) {
            $this->authorised = false;
            $out = json_encode($this->curl($action, $data = null));
//            if($E->getCode() == 401) {
//
//            } else {
//                die('Ошибка: ' . $E->getMessage() . PHP_EOL . 'Код ошибки: ' . $E->getCode() . "\n" . $E);
//            }

        }
        /*
         Данные получаем в формате JSON, поэтому, для получения читаемых данных,
         нам придётся перевести ответ в формат, понятный PHP
         */
        return json_decode($out, true);
    }


    public function login()
    {
        if ($this->authorised) {
            return true;
        }
        $action = $this->authorise_action;
        $data = array(
            'USER_LOGIN' => $this->auth_email, #Ваш логин (электронная почта)
            'USER_HASH' => $this->auth_hash #Хэш для доступа к API (смотрите в профиле пользователя)
        );
        $response = $this->curl($action, $data);
        if (isset($response['response'])) {
            $response = $response['response'];
        } else {
            \Yii::error(['name' => 'getFileContent ответ',
                'action' => $action,
                'data' => $data,
                'response' => $response,
            ], 'amocrm_error');
            return false;
        }
        if (isset($response['auth'])) {
            if ($response['auth']) {
                $this->authorised = true;
                return true;
            } elseif (!$response['auth'] AND $response['error_code'] = '401' AND isset($response['domain'])) {
                $this->url = $response['domain'];
                $this->adminLogin();
            } else {
                return false;
            }
        }
        return false;
    }


    public function getContactsList($offset = 0, $limit = 500)
    {
        if (!static::getInstance()->amo_crm_enabled) return;
        $action = 'api/v2/contacts/?limit_rows=' . $limit . '&limit_offset=' . $offset;
        $response = $this->curl($action);
        return $response;
    }

    public function getContactsId($id)
    {
        if (!static::getInstance()->amo_crm_enabled) return;
        $id = is_array($id) ? implode(',', $id) : $id;
        $action = 'api/v2/contacts/?id=' . $id;
        $response = $this->curl($action);
        return $response;
    }

    public function getPipelines()
    {
        if (!$this->amo_crm_enabled) return;
        $action = '/api/v2/pipelines';
        $response = $this->curl($action);
        return $response;
    }


    public function getLeadId($id)
    {
        if (!static::getInstance()->amo_crm_enabled) return;
        $action = is_array($id) ? 'api/v2/leads?id=' . implode(',', $id) : 'api/v2/leads?id=' . $id;
        $response = $this->curl($action);
        return $response;
    }

//    public function ChangeUserLead($user_id, $new_stage)
//    {
//        $action = '/api/v2/leads?id=' . $id;
//        $response = $this->curl($action);
//        return $response;
//    }


    public function setContacts($data)
    {
        if (!static::getInstance()->amo_crm_enabled) return;
        $action = 'api/v2/contacts';
        $response = $this->curl($action, $data);
        if (isset($response['_embedded']['errors'])) {
            \Yii::error(['name' => 'getFileContent ответ',
                'action' => 'contacts',
                'data' => $data,
                'response' => $response,
            ], 'amocrm_error');
            return false;
        }
        return $response['_embedded']['items'][0]['id'];
    }

    public function getUsers()
    {
        if (!static::getInstance()->amo_crm_enabled) return;
        $action = 'api/v2/account?with=users';
        $response = $this->curl($action);
        return $response;
    }

    public function getFields()
    {
        if (!static::getInstance()->amo_crm_enabled) return;
        $action = 'api/v2/account?with=custom_fields';
        $response = $this->curl($action);
        return $response;
    }

    public function addLead($amo_contact_id, $responsible_user_id = 2451640, $sum = 0, $pipeline_id = 1340179, $status_id = 21577060, $name = 'Потенциальный клиент')
    {
        if (!static::getInstance()->amo_crm_enabled) return;
        $action = 'api/v2/leads';
        $leads['add'] = array(
            array(
                'name' => $name,
                'created_at' => time(),
                'pipeline_id' => $pipeline_id,
                'status_id' => $status_id,
                'responsible_user_id' => $responsible_user_id,
                'contacts_id' => $amo_contact_id,
                'sale' => $sum,
            )
        );
        $response = $this->curl($action, $leads);
        return $response;
    }

    public function updateLead($data)
    {
        if (!static::getInstance()->amo_crm_enabled) return;
        $action = 'api/v2/leads';
        $response = $this->curl($action, $data);
        //var_dump($data, $response);
        return $response;
    }


    public function saveFields($force_update = false)
    {
        if (!static::getInstance()->amo_crm_enabled) return;
        $response = static::getFields();
        if (!isset($response['_embedded']['custom_fields']['contacts']) OR empty($response['_embedded']['custom_fields']['contacts'])) {
            return false;
        }
        foreach ($response['_embedded']['custom_fields']['contacts'] as $f) {
            if (AmoCustomField::find()->where(['amo_field_id' => $f['id']])->exists() AND !$force_update) {
                continue;
            }
            $field = new AmoCustomField();
            $field->amo_field_id = $f['id'];
            $field->name = $f['name'];
            $field->field_type = $f['field_type'];
            $field->sort = $f['sort'];
            $field->is_multiple = $f['is_multiple'];
            $field->is_system = $f['is_system'];
            $field->is_editable = $f['is_editable'];
            $field->is_required = $f['is_required'];
            $field->is_deletable = $f['is_deletable'];
            $field->is_visible = $f['is_visible'];
            $field->params = serialize($f['params']);
            $field->save();
        }
    }


    public function findUser($u)
    {
        if (!static::getInstance()->amo_crm_enabled) return;
        if (!$user = User::find()->where(['amo_contact_id' => $u['id']])->orWhere(['username' => $u['name']])->one()) {
            if (isset($u['custom_fields'])) {
                foreach ($u['custom_fields'] as $c) {
                    if ($c['name'] == 'Email') {
                        foreach ($c['values'] as $v) {
                            $c_email = $v['value'];
                            if ($user = User::findByEmail($c_email)) {
                                break(2);
                            }
                        }
                    }
                }
            }
        }
        if (!$user) {
            return false;
        }
        return $user;
    }


    public function addUser($user, $data)
    {
        $responsible_user_id = static::$main_manager_id;
        if (!$user->amo_contact_id) {
            $contact['add'] = [
                [
                    'name' => $user->username,
                    'responsible_user_id' => $responsible_user_id,
                    'created_at' => time(),
                    'custom_fields' => $this->prepareCustomFields($user, $data)
                ]
            ];
            $user->amo_contact_id = $this->setContacts($contact);
            $user->amo_contact_stage = 1;
            $user->save();
        }


        $name = static::name_levels[$user->amo_name_level];
        $user_pipelines = AmoUserPipelines::getOrCreate($user->id);
        $new_tags[] = static::tag_levels[$user->amo_tag_level]['title'];

        $leads['add'] = [];
        if (!$user_pipelines->synergy_1) {
            $leads['add'][] = [
                'name' => $name,
                'created_at' => time(),
                'pipeline_id' => 1601320, // Synergy
                'status_id' => 24370213, //Первичный контакт
                'responsible_user_id' => $responsible_user_id,
                'contacts_id' => $user->amo_contact_id,
                'tags' => implode(',', $new_tags),
                'request_id' => 1
            ];
        }
        if (!$user_pipelines->mailing_material) {
            $leads['add'][] = [
                'name' => $name,
                'created_at' => time(),
                'pipeline_id' => 1618561, // Рассылочный материал
                'status_id' => 24594199, //Первичный контакт
                'responsible_user_id' => $responsible_user_id,
                'contacts_id' => $user->amo_contact_id,
                'tags' => implode(',', $new_tags),
                'request_id' => 2
            ];
        }
        if (!$user_pipelines->trading_school) {
            $leads['add'][] = [
                'name' => $name,
                'created_at' => time(),
                'pipeline_id' => 1592986, // Школа трейдинга
                'status_id' => 24234025, //Первичный контакт
                'responsible_user_id' => $responsible_user_id,
                'contacts_id' => $user->amo_contact_id,
                'tags' => implode(',', $new_tags),
                'request_id' => 3
            ];
        }
        if (!$user_pipelines->vebinar_seminar) {
            $leads['add'][] = [
                'name' => $name,
                'created_at' => time(),
                'pipeline_id' => 1618564, // vebinar/seminar
                'status_id' => 24594211, //Первичный контакт
                'responsible_user_id' => $responsible_user_id,
                'contacts_id' => $user->amo_contact_id,
                'tags' => implode(',', $new_tags),
                'request_id' => 4
            ];
        }

        if (!empty($leads['add'])) {
            $action = 'api/v2/leads';
            $response = $this->curl($action, $leads);

            foreach ($response['_embedded']['items'] as $item) {
                switch ($item['request_id']) {
                    case 1:
                        $user_pipelines->synergy_1 = $item['id'];
                        break;
                    case 2:
                        $user_pipelines->mailing_material = $item['id'];
                        break;
                    case 3:
                        $user_pipelines->trading_school = $item['id'];
                        break;
                    case 4:
                        $user_pipelines->vebinar_seminar = $item['id'];
                        break;
                }
            }
        }
        $user_pipelines->save();
    }

    public function addLeadMoscow($user_id, $status_number = 1)
    {
        $status_numbers = [
            1 => 23356225, // Фильтр (Ожидает приглашения в офис)
            2 => 24623455, // входящая заявка
        ];
        $awaiting_status = $status_numbers[$status_number];
        if (!$user = User::findIdentity($user_id) OR !$user->amo_contact_id) {
            return false;
        }
        $user_pipelines = AmoUserPipelines::getOrCreate($user->id);

        $responsible_user_id = static::$main_manager_id;
        $name = static::name_levels[$user->amo_name_level];
        $user_leads = static::getLeadsByAmoContactId($user->amo_contact_id);
        if (!$lead = static::getPipelineLeadId($user_leads, 1515664)) { //ветка "Встречи (для Москвы и МО)"
            $new_tags[] = static::tag_levels[$user->amo_tag_level]['title'];
            $leads['add'][] = [
                'name' => $name,
                'created_at' => time(),
                'pipeline_id' => 1515664, // "Встречи (для Москвы и МО)"
                'status_id' => $awaiting_status,
                'responsible_user_id' => $responsible_user_id,
                'contacts_id' => $user->amo_contact_id,
                'tags' => implode(',', $new_tags),
                'request_id' => 5
            ];

            $action = 'api/v2/leads';
            $response = $this->curl($action, $leads);

            foreach ($response['_embedded']['items'] as $item) {
                switch ($item['request_id']) {
                    case 5:
                        $lead['id'] = $item['id'];
                        break;
                }
            }
        } elseif ($status_number == 2 AND $lead['status_id'] == $status_numbers[1]) {
            $update_array = serialize([
                'id' => $lead['id'],
                'name' => $name,
                'pipeline_id' => 1515664,
                'status_id' => $awaiting_status,
                'updated_at' => time(),
            ]);
            AmoQueue::addTask('actionUpdateLead', $update_array);
        }

        if (!$user_pipelines->meet_up_moscow) {
            $user_pipelines->meet_up_moscow = $lead['id'];
            $user_pipelines->save();
        }

        if ($lead = static::getPipelineLeadId($user_leads, 1618564) AND $lead['status_id'] != 25104526) { //ветка “Вебинар/Семинар” этап "Москва"
            $update_array = serialize([
                'id' => $lead['id'],
                'name' => $name,
                'pipeline_id' => 1618564,
                'status_id' => 25104526,
                'updated_at' => time(),
            ]);
            AmoQueue::addTask('actionUpdateLead', $update_array);
        }

        return true;
    }

    public function addLeadSaveCapital($user_id, $status_number = 1)
    {
        $status_numbers = [
            1 => 22666090, // Взять в работу
            2 => 22666090, // Тут должен быть этап “Входящая заявка”
        ];
        $awaiting_status = $status_numbers[$status_number];
        if (!$user = User::findIdentity($user_id) OR !$user->amo_contact_id) {
            return false;
        }
        $user_pipelines = AmoUserPipelines::getOrCreate($user->id);


        $responsible_user_id = static::$main_manager_id;
        $name = static::name_levels[$user->amo_name_level];
        $user_leads = static::getLeadsByAmoContactId($user->amo_contact_id);
        if (!$lead = static::getPipelineLeadId($user_leads, 1463086)) { //ветка "Продукт "Save Capital""
            $new_tags[] = static::tag_levels[$user->amo_tag_level]['title'];
            $leads['add'][] = [
                'name' => $name,
                'created_at' => time(),
                'pipeline_id' => 1463086, // "Продукт "Save Capital""
                'status_id' => $awaiting_status,
                'responsible_user_id' => $responsible_user_id,
                'contacts_id' => $user->amo_contact_id,
                'tags' => implode(',', $new_tags),
                'sale' => 1000,
                'request_id' => 5
            ];

            $action = 'api/v2/leads';
            $response = $this->curl($action, $leads);

            foreach ($response['_embedded']['items'] as $item) {
                switch ($item['request_id']) {
                    case 5:
                        $lead['id'] = $item['id'];
                        break;
                }
            }
        } elseif ($status_number == 2 AND $lead['status_id'] == $status_numbers[1]) {
            $update_array = serialize([
                'id' => $lead['id'],
                'name' => $name,
                'pipeline_id' => 1463086,
                'status_id' => $awaiting_status,
                'sale' => 1000,
                'updated_at' => time(),
            ]);
            AmoQueue::addTask('actionUpdateLead', $update_array);
        }

        if (!$user_pipelines->save_capital) {
            $user_pipelines->save_capital = $lead['id'];
            $user_pipelines->save();
        }

        return true;
    }

    public function addLeadMeaningfulCustomerCard($user_id)
    {
        $user_pipelines = AmoUserPipelines::getOrCreate($user_id);
        if ($user_pipelines->meaningful_customer_card) {
            return true;
        }
        if (!$user = User::findIdentity($user_id) OR !$user->amo_contact_id) {
            return false;
        }
        $user_leads = static::getLeadsByAmoContactId($user->amo_contact_id);

        if (!$lead = static::getPipelineLeadId($user_leads, 1583488)) { //ветка "Карта зн. клиента"
            $name = static::name_levels[$user->amo_name_level];
            $responsible_user_id = static::$main_manager_id;
            $new_tags[] = static::tag_levels[$user->amo_tag_level]['title'];
            $leads['add'][] = [
                'name' => $name,
                'created_at' => time(),
                'pipeline_id' => 1583488, // "Карта зн. клиента"
                'status_id' => 24021292, // Первичный контакт
                'responsible_user_id' => $responsible_user_id,
                'contacts_id' => $user->amo_contact_id,
                'tags' => implode(',', $new_tags),
                'request_id' => 6,
                'sale' => 5000
            ];

            $action = 'api/v2/leads';
            $response = $this->curl($action, $leads);

            foreach ($response['_embedded']['items'] as $item) {
                switch ($item['request_id']) {
                    case 6:
                        $lead['id'] = $item['id'];
                        break;
                }
            }
        }

        $user_pipelines->meaningful_customer_card = $lead['id'];
        $user_pipelines->save();

        return true;
    }

    public function updateLeadTradingSchool($user_id)
    {
        $user_pipelines = AmoUserPipelines::getOrCreate($user_id);
        if (!$user = User::findIdentity($user_id) OR !$user->amo_contact_id) {
            return false;
        }
        $user_leads = static::getLeadsByAmoContactId($user->amo_contact_id);
        $name = static::name_levels[$user->amo_name_level];
        $responsible_user_id = static::$main_manager_id;
        $new_tags[] = static::tag_levels[$user->amo_tag_level]['title'];
        $lead_created = false;

        if (!$user_pipelines->trading_school) {
            if (!$lead = static::getPipelineLeadId($user_leads, 1592986)) { //ветка "Школа трейдинга"

                $leads['add'][] = [
                    'name' => $name,
                    'created_at' => time(),
                    'pipeline_id' => 1592986, // "Карта зн. клиента"
                    'status_id' => 142, // Успешно реализовано
                    'responsible_user_id' => $responsible_user_id,
                    'contacts_id' => $user->amo_contact_id,
                    'tags' => implode(',', $new_tags),
                    'request_id' => 3
                ];

                $action = 'api/v2/leads';
                $response = $this->curl($action, $leads);
                $lead_created = true;
                foreach ($response['_embedded']['items'] as $item) {
                    switch ($item['request_id']) {
                        case 3:
                            $lead['id'] = $item['id'];
                            break;
                    }
                }

            }
            $user_pipelines->trading_school = $lead['id'];
            $user_pipelines->save();
        }
        if(!$lead_created) {
            $update_array = serialize([
                'id' => $user_pipelines->trading_school,
                'name' => $name,
                'pipeline_id' => 1463086,
                'status_id' => 142,
                'updated_at' => time(),
            ]);
            AmoQueue::addTask('actionUpdateLead', $update_array);
        }
        return true;
    }

    public function addLeadPlusFifty($user_id, $stage)
    {
        if (!$user = User::findIdentity($user_id) OR !$user->amo_contact_id) {
            return false;
        }
        $pipeline_id = 1578337;
        $status_numbers = [
            1 => 24627634, // Демо
            2 => 24627637, // Cent
            3 => 24627640, // Classic
        ];
        $awaiting_status = $status_numbers[$stage];

        $user_pipelines = AmoUserPipelines::getOrCreate($user_id);
        $user_leads = static::getLeadsByAmoContactId($user->amo_contact_id);
        $name = static::name_levels[$user->amo_name_level];
        $responsible_user_id = static::$main_manager_id;
        $new_tags[] = static::tag_levels[$user->amo_tag_level]['title'];
        $lead_created = false;

        if (!$user_pipelines->plus_50) {
            if (!$lead = static::getPipelineLeadId($user_leads, $pipeline_id)) { //ветка "Школа трейдинга"
                $leads['add'][] = [
                    'name' => $name,
                    'created_at' => time(),
                    'pipeline_id' => $pipeline_id, // "Карта зн. клиента"
                    'status_id' => $awaiting_status, // Успешно реализовано
                    'responsible_user_id' => $responsible_user_id,
                    'contacts_id' => $user->amo_contact_id,
                    'tags' => implode(',', $new_tags),
                    'request_id' => 3
                ];

                $action = 'api/v2/leads';
                $response = $this->curl($action, $leads);
                $lead_created = true;
                foreach ($response['_embedded']['items'] as $item) {
                    switch ($item['request_id']) {
                        case 3:
                            $lead['id'] = $item['id'];
                            break;
                    }
                }

            }
            $user_pipelines->plus_50 = $lead['id'];
            $user_pipelines->save();
        }
        if(!$lead_created) {

            $lead = static::getPipelineLeadId($user_leads, $pipeline_id);
            if(in_array($lead['status_id'],$status_numbers) AND $lead['status_id'] >= $awaiting_status ) {

            } else {
                $update_array = serialize([
                    'id' => $user_pipelines->plus_50,
                    'name' => $name,
                    'pipeline_id' => $pipeline_id,
                    'status_id' => $awaiting_status,
                    'updated_at' => time(),
                ]);
                AmoQueue::addTask('actionUpdateLead', $update_array);
            }
        }
        return true;
    }

    public function addLeadLoyaltyProgram($user_id)
    {
        $user_pipelines = AmoUserPipelines::getOrCreate($user_id);
        if ($user_pipelines->loyalty_program) {
            return false;
        }
        if (!$user = User::findIdentity($user_id) OR !$user->amo_contact_id) {
            return false;
        }
        $user_leads = static::getLeadsByAmoContactId($user->amo_contact_id);

        if (!$lead = static::getPipelineLeadId($user_leads, 1618573)) { //ветка "Программа лояльности"
            $name = static::name_levels[$user->amo_name_level];
            $responsible_user_id = static::$main_manager_id;
            $new_tags[] = static::tag_levels[$user->amo_tag_level]['title'];
            $leads['add'][] = [
                'name' => $name,
                'created_at' => time(),
                'pipeline_id' => 1618573, // "Программа лояльности"
                'status_id' => 24594238, // Первичный контакт
                'responsible_user_id' => $responsible_user_id,
                'contacts_id' => $user->amo_contact_id,
                'tags' => implode(',', $new_tags),
                'request_id' => 8
            ];

            $action = 'api/v2/leads';
            $response = $this->curl($action, $leads);

            foreach ($response['_embedded']['items'] as $item) {
                switch ($item['request_id']) {
                    case 8:
                        $lead['id'] = $item['id'];
                        break;
                }
            }
        }

        $user_pipelines->trading_school = $lead['id'];
        $user_pipelines->save();
        return true;
    }

    public function addLeadSynergyInvest($user_id, $sum)
    {
        if (!$user = User::findIdentity($user_id) OR !$user->amo_contact_id) {
            return false;
        }
        $new_tags[] = static::tag_levels[$user->amo_tag_level]['title'];
        $user_leads = static::getLeadsByAmoContactId($user->amo_contact_id);
        $responsible_user_id = static::$main_manager_id;
        $name = static::name_levels[$user->amo_name_level];

        $pipline1 = false;
        $pipline2 = false;
        foreach ($user_leads as $user_lead) {
            if ($user_lead['pipeline_id'] == 1601320 AND $user_lead['status_id'] != 142) {
                $pipline1 = true;
                $update_array = serialize([
                    'id' => $user_lead['id'],
                    'name' => $name,
                    'pipeline_id' => 1601320,
                    'status_id' => 142,
                    'sale' => $sum,
                    'updated_at' => time(),
                ]);
                AmoQueue::addTask('actionUpdateLead', $update_array);
            }
            if ($user_lead['pipeline_id'] == 1601338 AND $user_lead['status_id'] != 142) {
                $pipline2 = true;
                $update_array = serialize([
                    'id' => $user_lead['id'],
                    'name' => $name,
                    'pipeline_id' => 1601338,
                    'status_id' => 142,
                    'sale' => $sum,
                    'updated_at' => time(),
                ]);
                AmoQueue::addTask('actionUpdateLead', $update_array);
            }
        }
        $leads['add'] = [];
        if (!$pipline1) {
            $leads['add'][] = [
                'name' => $name,
                'created_at' => time(),
                'pipeline_id' => 1601320, // "Synergy""
                'status_id' => 142, //первичный контакт
                'responsible_user_id' => $responsible_user_id,
                'contacts_id' => $user->amo_contact_id,
                'sale' => $sum,
                'tags' => implode(',', $new_tags),
            ];
        }
        if (!$pipline2) {
            $leads['add'][] = [
                'name' => $name,
                'created_at' => time(),
                'pipeline_id' => 1601338, // "Synergy Вебинар"
                'status_id' => 142, //первичный контакт
                'responsible_user_id' => $responsible_user_id,
                'contacts_id' => $user->amo_contact_id,
                'sale' => $sum,
                'tags' => implode(',', $new_tags),
            ];
        }
        $leads['add'][] = [
            'name' => $name,
            'created_at' => time(),
            'pipeline_id' => 1601341, // "Synergy этап 2""
            'status_id' => 24372244, //первичный контакт
            'responsible_user_id' => $responsible_user_id,
            'contacts_id' => $user->amo_contact_id,
            'sale' => $sum,
            'tags' => implode(',', $new_tags),
        ];

        $action = 'api/v2/leads';
        $this->curl($action, $leads);

        return true;
    }

    public function addLeadSaveCapitalVebinar($user_id)
    {
        $user_pipelines = AmoUserPipelines::getOrCreate($user_id);
        if ($user_pipelines->save_capital_vebinar) {
            return false;
        }
        if (!$user = User::findIdentity($user_id) OR !$user->amo_contact_id) {
            return false;
        }
        $user_leads = static::getLeadsByAmoContactId($user->amo_contact_id);

        if (!$lead = static::getPipelineLeadId($user_leads, 1664620)) { //ветка "Save Capital / Вебинар"
            $name = static::name_levels[$user->amo_name_level];
            $responsible_user_id = static::$main_manager_id;
            $new_tags[] = static::tag_levels[$user->amo_tag_level]['title'];
            $leads['add'][] = [
                'name' => $name,
                'created_at' => time(),
                'pipeline_id' => 1664620, // "Save Capital / Вебинар"
                'status_id' => 25104352, // Первичный контакт
                'responsible_user_id' => $responsible_user_id,
                'contacts_id' => $user->amo_contact_id,
                'tags' => implode(',', $new_tags),
                'request_id' => 9
            ];

            $action = 'api/v2/leads';
            $response = $this->curl($action, $leads);

            foreach ($response['_embedded']['items'] as $item) {
                switch ($item['request_id']) {
                    case 9:
                        $lead['id'] = $item['id'];
                        break;
                }
            }
        }

        $user_pipelines->save_capital_vebinar = $lead['id'];
        $user_pipelines->save();
        return true;
    }

    public function updateUser($user, $data)
    {
        //test
        // if (!static::getInstance()->amo_crm_enabled) return;
        if (!$user->amo_contact_id) {
            return false;
        }
        if ($task = AmoQueue::find()->where(['additional_params' => $user->id, 'task' => 'actionUpdateUsers'])->one()) {
            if ($task->worked) {
                $task->worked = 0;
                $task->date_add = date('Y-m-d H:i:s');
                $task->params = implode(',', $data);
            } else {
                $old_data = explode(",", $task->params);
                foreach ($data as $d) {
                    if (!in_array($d, $old_data)) {
                        $old_data[] = $d;
                    }
                }
                $task->params = implode(',', $old_data);
            }
            $task->save();
        } else {
            AmoQueue::addTask('actionUpdateUsers', implode(',', $data), $user->id);
        }
    }


    public function arrayCustomFields()
    {
        if (!static::getInstance()->amo_crm_enabled) return;
        $custom_fields = AmoCustomField::find()->asArray()->all();
        $fields = [];
        foreach ($custom_fields as $f) {
            $fields[$f['name']] = $f['amo_field_id'];
        }
        return $fields;
    }

    public function getUpdateContactsResponsible($user_id)
    {
        if (!static::getInstance()->amo_crm_enabled) return;
        $amoCrm = static::getInstance();
        if (!$user = User::findIdentity($user_id) OR !$user->amo_contact_id) {
            return false;
        }
        $user_data = $amoCrm->getContactsId($user->amo_contact_id);

        if (!isset($user_data['_embedded']['items'][0]['leads']) OR empty($user_data['_embedded']['items'][0]['leads']['id'])) {
            return false;
        }
        $leads = $amoCrm->getLeadId($user_data['_embedded']['items'][0]['leads']['id']);
        if (!isset($leads['_embedded']['items'])) {
            return false;
        }
        $main_lead = false;
        $leads = $leads['_embedded']['items'];
        foreach ($leads as $lead) {
            $pipeline = $lead['pipeline']['id'];
            if (in_array($pipeline, $amoCrm::toucheble_pipelines)) {
                $main_lead = $lead['responsible_user_id'];
                break;
            }
        }
        if (!$main_lead) {
            foreach ($leads as $lead) {
                $pipeline = $lead['pipeline']['id'];
                if (in_array($pipeline, $amoCrm::updatable_pipelines)) {
                    $main_lead = $lead['responsible_user_id'];
                    break;
                }
            }
        }
        if (!$main_lead) {
            foreach ($leads as $lead) {
                $main_lead = $lead['responsible_user_id'];
                break;
            }
        }
        if (!$main_lead) {
            return false;
        }
        if ($manager = ManagerCard::find()->where(['amo_user_id' => $main_lead])->one() AND $user->manager_card_id != $manager->id) {
            $user->manager_card_id = $manager->id;
            $user->save();
            LogMy::getInstance()->setLog(['message' => "manager updated for user $user->id"], 'amo_responsible');
        }
        $return = [];
        $new_leads = [];
        foreach ($leads as $lead) {
            if ($lead['responsible_user_id'] != $main_lead) {
                $update_array = array(
                    'id' => $lead['id'],
                    'name' => $lead['name'],
                    'updated_at' => time(),
                    'responsible_user_id' => $main_lead,
                );
                $new_leads['update'] = array(
                    $update_array
                );
                LogMy::getInstance()->setLog(['message' => "user $user->id lead update"], 'amo_responsible');
            }
        }

        $return['leads'] = $new_leads;
        $new_contact = false;
        if ($user_data['_embedded']['items'][0]['responsible_user_id'] != $main_lead) {
            $new_contact = [
                'id' => $user_data['_embedded']['items'][0]['id'],
                'updated_at' => time(),
                'name' => $user_data['_embedded']['items'][0]['name'],
                'responsible_user_id' => $main_lead
            ];
            LogMy::getInstance()->setLog(['message' => "user $user->id contact update"], 'amo_responsible');
        }
        $return['contacts'] = $new_contact;

        return $return;
    }


    public function prepareCustomFields($user, $data)
    {
        if (!static::getInstance()->amo_crm_enabled) return;
        //  var_dump($data);
        $custom_fields = $this->arrayCustomFields();
        $return = [];
        $country_seted = false;
        $fio_seted = false;
        foreach ($data as $key => $value) {
            if (!isset($this->custom_fields_depends[$key])) continue;
            $custom_field_name = $this->custom_fields_depends[$key];
            $arr = [];
            $arr['id'] = $custom_fields[$custom_field_name];
            if ($key == 'country_id' OR $key == 'city_name') {
                if ($country_seted) continue;
                $country_seted = true;
                $value = Country::getCountry($user->country_id) . '/' . $user->city_name;
            }
            if (in_array($key, ['firstname', 'lastname', 'middlename'])) {
                if ($fio_seted) continue;
                $fio_seted = true;
                $value = trim(preg_replace("/ {2,}/", " ", $user->firstname . ' ' . $user->lastname . ' ' . $user->middlename));
            }
            if ($key == 'status_in_partner') {
                $value = User::$partner_staus[$value];
            }
            if (in_array($key, ['date_bithday', 'date_reg'])) {
                $value = strtotime($value);
            }
            $value_arr = [
                'value' => $value,
            ];
            if (in_array($key, ['phone', 'email'])) {
                $value_arr['enum'] = 'WORK';
            }
            $arr['values'] = [$value_arr];
            $return[] = $arr;
        }
        return $return;
    }

    public $deal_name = [1 => 'Потенциальный клиент', 2 => 'Действующие инвесторы', 3 => 'Прекратил сотрудничество'];

    public function changeUserLead($user_id, $data)
    {
        if (!static::getInstance()->amo_crm_enabled) return;
        $amoCrm = static::getInstance();
        if (!$user = User::findIdentity($user_id) OR !$user->amo_contact_id OR !isset($data['stage'])) {
            return false;
        }
        $new_stage = $data['stage'] != 0 ? $data['stage'] : 1;
        $user_data = $amoCrm->getContactsId($user->amo_contact_id);
        $amo_contact_id = $user->amo_contact_id;
        $responsible_user_id = static::$main_manager_id;
        $first_deposit = $data['first_deposit'];

        $name = isset($amoCrm->deal_name[$data['stage']]) ? $amoCrm->deal_name[$data['stage']] : $amoCrm->deal_name[2];
        if (!isset($user_data['_embedded']['items'][0])) {
            return false;
        }
        $answer = false;
        if (!isset($user_data['_embedded']['items'][0]['leads']['id']['0'])) {
            $answer = $amoCrm->addLead($amo_contact_id, $responsible_user_id, $first_deposit, $amoCrm->pipelines_stage[$new_stage], $amoCrm->pipelines_status[$amoCrm->pipelines_stage[$new_stage]], $name);
        } else {
            $leads_id = $user_data['_embedded']['items'][0]['leads']['id'];
            $leads = $amoCrm->getLeadId($leads_id);

            if (isset($leads['_embedded']['items'])) {
                foreach ($leads['_embedded']['items'] as $lead) {
                    if (isset($lead['pipeline']['id']) AND in_array($lead['pipeline']['id'], $amoCrm::updatable_pipelines)) {
                        $pipeline_id = $lead['pipeline']['id'];

                        $tags = [];
                        $tags_array = $lead['tags'];
                        foreach ($tags_array as $tag) {
                            $tags[] = $tag['name'];
                        }
                        $new_tags = static::prepareTagsForUser($tags);
                        if ($data['tag_level'] > 0) {
                            $new_tags[] = static::tag_levels[$data['tag_level']]['title'];
                        }

                        if (!in_array($new_stage, [1, 2, 3])) {
                            $name = $lead['name'];
                        }
//                        if (in_array($pipeline_id, $amoCrm->pipelines_stage)) {
//                            foreach ($amoCrm->pipelines_stage as $key => $value) {
//                                if ($value == $pipeline_id) {
//                                    $current_stage = $key;
//                                }
//                            }
//                            if (!$user->amo_contact_stage) {
//                                $current_stage = 6;
//                            }
//                        } else {
//                            $current_stage = 6;
//                        }
                        if ($pipeline_id == $amoCrm->pipelines_stage[$new_stage] OR !in_array($pipeline_id, $amoCrm::toucheble_pipelines) OR !in_array($amoCrm->pipelines_stage[$new_stage], $amoCrm::toucheble_pipelines)) {
                            $update_array = array(
                                'id' => $lead['id'],
                                'name' => $name,
                                'updated_at' => time(),
                                'sale' => intval($first_deposit),
                                //   'responsible_user_id' => $responsible_user_id,
                                'tags' => implode(',', $new_tags)
                            );

                            $leads['update'] = array(
                                $update_array
                            );
                        } else {
                            $update_array = array(
                                'id' => $lead['id'],
                                'name' => $name,
                                'updated_at' => time(),
                                'status_id' => intval($amoCrm->pipelines_status[$amoCrm->pipelines_stage[$new_stage]]),
                                'pipeline_id' => intval($amoCrm->pipelines_stage[$new_stage]),
                                'sale' => intval($first_deposit),
                                //    'responsible_user_id' => $responsible_user_id,
                                'tags' => implode(',', $new_tags)
                            );
                            //  $update_array['tags'] = implode(',', $new_tags);
                            $leads['update'] = array(
                                $update_array
                            );
                        }
                        $answer = $amoCrm->updateLead($leads);
                    }
                }
            }
        }

        if ($answer) {
            $user->amo_contact_stage = $new_stage;
            $user->save();
        }
        return true;
    }

    public function prepareUpdateUserData($data)
    {
        if (!static::getInstance()->amo_crm_enabled) return;
        $custom_fields = $this->arrayCustomFields();
        $update_data = explode(',', $data['params']);
        $new_tags = static::getInstance()->prepareTagsForUser(static::getInstance()->getUserTags($data['amo_contact_id']));

        $update_leads = false;
        if (($tag_level = static::getTag($data['balance']) AND $tag_level['level'] > $data['amo_tag_level'])) {
            $update_leads = true;
            $data['amo_tag_level'] = $tag_level['level'];
            User::updateAll(['amo_tag_level' => $data['amo_tag_level']], ['id' => $data['id']]);

            if (in_array($tag_level['level'], [2, 3]) AND !$data['meaningful_customer_card']) { // Если тег "Категория А" или "Категория Б"
                AmoQueue::addTask('actionAddLeadMeaningfulCustomerCard', $data['id']);
            }
            if ($tag_level['level'] == 2 AND !$data['save_capital_vebinar']) { // Если тег  "Категория Б"
                AmoQueue::addTask('actionAddLeadSaveCapitalVebinar', $data['id']);
            }
            if (in_array($tag_level['level'], [3, 4]) AND !$data['save_capital']) { // Если тег "Категория А" или "Значимый клиент"
                AmoQueue::addTask('actionAddLeadSaveCapital', $data['id']);
            }
            
        }
        $new_tags[] = static::tag_levels[$data['amo_tag_level']]['title'];

        if ($data['first_deposit'] > 0) {
            $update_data[] = 'first_deposit';
            if ($data['amo_name_level'] != 1 AND $data['balance'] > 10) {
                $update_leads = true;
                $data['amo_name_level'] = 1;
                User::updateAll(['amo_name_level' => $data['amo_name_level']], ['id' => $data['id']]);
            } elseif ($data['amo_name_level'] == 1 AND $data['balance'] < 7) {
                $update_leads = true;
                $data['amo_name_level'] = 2;
                User::updateAll(['amo_name_level' => $data['amo_name_level']], ['id' => $data['id']]);
            }
        }

        if ($update_leads) {
            $serialized_data = serialize(['name_level' => $data['amo_name_level'], 'tag_level' => $data['amo_tag_level']]);
            if (!$task = AmoQueue::find()->where(['params' => $data['id'], 'task' => 'actionUpdateUserLeads'])->orderBy('date_add DESC')->one() OR $task->additional_params != $serialized_data) {
                AmoQueue::addTask('actionUpdateUserLeads', $data['id'], $serialized_data);
            }
        }


        $return = [
            'id' => $data['amo_contact_id'],
            'updated_at' => time(),
        ];

        if (in_array('username', $update_data)) {
            $return['name'] = $data['username'];
        }
        if (in_array('manager_card_id', $update_data) AND $amo_manager_id = ManagerCard::findIdentity($data['manager_card_id'])->amo_user_id) {
            $return['responsible_user_id'] = $amo_manager_id;
            if ($data['synergy_1']) {
                $update_array = serialize([
                    'id' => $data['synergy_1'],
                    'name' => static::name_levels[$data['amo_name_level']],
                    'updated_at' => time(),
                    'tags' => implode(',', $new_tags)
                ]);
                AmoQueue::addTask('actionUpdateLead', $update_array);
            }
        }
        $country_seted = false;
        $fio_seted = false;
        $balance_seted = false;
        $custom = [];

        if (in_array('balance', $update_data)) {
            $update_data[] = 'earned';
            if ($data['earned'] >= 200 AND !$data['loyalty_program']) {
                AmoQueue::addTask('actionAddLeadLoyaltyProgram', $data['id']);
            }
        }
        foreach ($update_data as $key) {
            if (!isset($this->custom_fields_depends[$key])) {
                continue;
            }
            $custom_field_name = $this->custom_fields_depends[$key];
            $arr = [];
            $arr['id'] = $custom_fields[$custom_field_name];
            $value = $data[$key];
            if (!isset($data[$key]) OR $value == '') {
                continue;
            }
            if ($key == 'country_id' OR $key == 'city_name') {
                if ($country_seted) continue;
                $country_seted = true;
                $value = Country::getCountry($data['country_id']) . '/' . $data['city_name'];
                $data['city_name'] = trim(mb_convert_case($data['city_name'], MB_CASE_LOWER, "UTF-8"));

                if (in_array('city_name', $update_data) AND in_array(strtolower(trim($data['city_name'])), ['moscow', 'moskva', 'москва', 'default city', 'msk', 'мск']) AND !$data['meet_up_moscow']) {
                    AmoQueue::addTask('actionCreateMoscowLead', $data['id']);
                }
            }
            if (in_array($key, ['firstname', 'lastname', 'middlename'])) {
                if ($fio_seted) continue;
                $fio_seted = true;
                $value = trim(preg_replace("/ {2,}/", " ", $data['firstname'] . ' ' . $data['lastname'] . ' ' . $data['middlename']));
            }
            if ($key == 'status_in_partner') {
                $value = User::$partner_staus[$data['status_in_partner']];
            }
            if (in_array($key, ['balance', 'balance_partner'])) {
                if ($balance_seted) continue;
                $balance_seted = true;
                $value = number_format($data['balance'], 2, '.', '');

            }
            if (in_array($key, ['date_bithday', 'date_reg', 'ip'])) {
                $value = strtotime($data[$key]);
            }

            $value_arr = [
                'value' => $value,
            ];
            if (in_array($key, ['phone', 'email'])) {
                $value_arr['enum'] = 'WORK';
            }
            $arr['values'] = [$value_arr];
            $custom[] = $arr;
        }
        if (!empty($custom)) {
            $return['custom_fields'] = $custom;
        }
        if ($new_tags) {
            $return['tags'] = implode(',', $new_tags);
        }


        return $return;
    }

    public
    static function getBadids()
    {
        $user_ids = AmoQueue::find()->select('params')->where("`date_add` BETWEEN '2018-12-10 01:00:00' AND '2018-12-10 04:59:00' AND `task` = 'actionChangeUserLead'")->asArray()->all();
        $ids = [];
        foreach ($user_ids as $u_id) {
            $ids[] = $u_id['params'];
        }
        return $ids;
    }


    public static function prepareTagsForUser($tags)
    {
        return array_diff($tags, static::del_tag_array);
    }

    public static function getUserTags($amo_contact_id)
    {
        $amoCrm = AmoCrm::getInstance();
        $tags_array = $amoCrm->getContactsId($amo_contact_id)['_embedded']['items'][0]['tags'];
        $tags = [];
        if (empty($tags_array)) {
            return $tags;
        }
        foreach ($tags_array as $tag) {
            $tags[] = $tag['name'];
        }
        return $tags;
    }


    private function curl_test($action, $data = null)
    {
        if ((!$this->authorised OR $this->authirise_date < strtotime(date('Y-m-d H:i:s') . ' +5 minutes')) AND $action != $this->authorise_action) {
            $this->login();
        }
        \Yii::info(['name' => 'Curl запрос',
            'action' => $action,
            'data' => $data,
        ], 'amocrm');

        // $data = http_build_query($data);

        $url = $this->url . $action;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
        curl_setopt($curl, CURLOPT_URL, $url);


        if ($data) {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        }
        curl_setopt($curl, CURLOPT_HEADER, false);

        curl_setopt($curl, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
        curl_setopt($curl, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/cookie.txt');

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('IF-MODIFIED-SINCE: Thu, 13 Dec 2018 18:35:00'));

        $out = curl_exec($curl); #Инициируем запрос к API и сохраняем ответ в переменную


        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE); #Получим HTTP-код ответа сервера
        curl_close($curl); #Завершаем сеанс cURL
        /* Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
        $code = (int)$code;
        $errors = array(
            301 => 'Moved permanently',
            400 => 'Bad request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not found',
            500 => 'Internal server error',
            502 => 'Bad gateway',
            503 => 'Service unavailable'
        );

//        if($this->authorised) {
//            var_dump($out);
//            die;
//        }

        try {
            #Если код ответа не равен 200 или 204 - возвращаем сообщение об ошибке
            if ($code != 200 && $code != 204)
                throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error', $code);
        } catch (Exception $E) {
            die('Ошибка: ' . $E->getMessage() . PHP_EOL . 'Код ошибки: ' . $E->getCode() . "\n" . $E);
        }
        /*
         Данные получаем в формате JSON, поэтому, для получения читаемых данных,
         нам придётся перевести ответ в формат, понятный PHP
         */
        return json_decode($out, true);
    }


    public function getEventsList($offset = 0, $limit = 500)
    {
        if (!static::getInstance()->amo_crm_enabled) return;
        $action = 'api/v2/notes?type=lead&limit_rows=' . $limit . '&limit_offset=' . $offset;
        $response = $this->curl_test($action);
        return $response;
    }


    public function updateUserLeads($user_id, $data)
    {
        $amoCrm = static::getInstance();
        if (!$user = User::findIdentity($user_id) OR !$user->amo_contact_id) {
            return false;
        }

        $user_data = $amoCrm->getContactsId($user->amo_contact_id);
        if (empty($user_data['_embedded']['items'][0]['leads']['id'])) {
            return false;
        }
        $leads = $amoCrm->getLeadId($user_data['_embedded']['items'][0]['leads']['id']);
        $name = static::name_levels[$data['name_level']];

        $user->amo_name_level = $data['name_level'];
        $user->amo_tag_level = $data['tag_level'];
        $user->save(); 
        
        foreach ($leads['_embedded']['items'] as $lead) {
            $tags = [];
            $tags_array = $lead['tags'];
            foreach ($tags_array as $tag) {
                $tags[] = $tag['name'];
            }
            $new_tags = static::prepareTagsForUser($tags);
            if ($data['tag_level'] > 0) {
                $new_tags[] = static::tag_levels[$data['tag_level']]['title'];
            }

            $update_array = serialize([
                'id' => $lead['id'],
                'name' => $name,
                'updated_at' => time(),
                'tags' => implode(',', $new_tags)
            ]);
            AmoQueue::addTask('actionUpdateLead', $update_array);
        }
        return true;
    }

    public function getLeadsByAmoContactId($amo_contact_id)
    {
        $amoCrm = static::getInstance();

        $user_data = $amoCrm->getContactsId($amo_contact_id);
        if (empty($user_data['_embedded']['items'][0]['leads']['id'])) {
            return [];
        }
        $leads = $amoCrm->getLeadId($user_data['_embedded']['items'][0]['leads']['id']);
        if (isset($leads['_embedded']['items']) AND !empty($leads['_embedded']['items'])) {
            return $leads['_embedded']['items'];
        }
        return [];
    }

    public function getPipelineLeadId($leads, $pipeline_id)
    {
        foreach ($leads as $lead) {
            if ($lead['pipeline_id'] == $pipeline_id) {
                return [
                    'id' => $lead['id'],
                    'status_id' => $lead['status_id'],
                ];
            }
        }
        return false;
    }

}
