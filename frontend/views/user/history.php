<?php
use common\models\BalanceLog;
use common\service\Servis;

if (\Yii::$app->language == 'ru') {
    setlocale(LC_ALL, 'ru_RU.UTF-8');
}

$service = Servis::getInstance();
$this->title = Yii::t('cab', 'История переводов');
$status_classes = ['treatment','successfully','rejected','treatment','treatment'];
?>

<div class="content col pt-0">
    <div class="history-operations">
        <div class="invitations--title">Мои операции</div>
        <div class="table-responsive invitations--table">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col"><?= Yii::t('cab', 'Сумма') ?></th>
                    <th scope="col"><?= Yii::t('cab', 'Операция') ?></th>
                    <th scope="col"><?= Yii::t('cab', 'Время') ?></th>
                    <th scope="col"><?= Yii::t('cab', 'ID') ?></th>
                    <th scope="col"><?= Yii::t('cab', 'Заявка') ?></th>
                </tr>
                <tr>
                </thead>
                <tbody>
                <?php
                foreach ($models as $item){
                    $status = $item->status == 3 ? BalanceLog::$status_name[0] : BalanceLog::$status_name[$item->status];

                    ?>
                    <tr>
                        <td class="value"><?= $service->beautyDecimal($item->summ) ?>$</td>
                        <td><?= Yii::t('cab', $item->operation == 3 ? 'Пополнение счета' :BalanceLog::$operation_name[$item->operation]) ?></td>
                        <td><?= $service->getDateWord($item->date_add) ?></td>
                        <td><?= $item->id ?></td>
                        <td class="<?= $status_classes[$item->status] ?>"><?= Yii::t('cab', $status) ?></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
        <?= $this->render('@app/views/layouts/_paginator.php', [
            'pages' => $pages,
            'pageSize' => $pageSize,
            'now_page' => (isset($_GET['page'])) ? (int)$_GET['page'] : 1,
            'link' => $this->context->module->requestedRoute
        ]) ?>
    </div>
</div>