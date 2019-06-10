<?php

use yii\db\Migration;

/**
 * Class m180619_090539_documents_uploaded_base64field
 */
class m180619_090539_documents_uploaded_base64field extends Migration
{
    use common\models\traits\TextTypesTrait;
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('users_documents_uploaded', 'base_image', $this->longText()->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('users_documents_uploaded', 'base_image');
        return true;
    }

}
