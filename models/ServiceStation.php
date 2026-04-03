<?php

namespace app\models;

use Yii;

class ServiceStation extends PoiBase
{
    public static function tableName() { return '{{%service_station}}'; }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['address', 'phone', 'working_hours'], 'string', 'max' => 255],
            [['service_types'], 'string'],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'service_types' => Yii::t('app', 'Service types'),
        ]);
    }
}
