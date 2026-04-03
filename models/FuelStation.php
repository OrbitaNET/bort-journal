<?php

namespace app\models;

use Yii;

class FuelStation extends PoiBase
{
    public static function tableName() { return '{{%fuel_station}}'; }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['address', 'phone', 'fuel_types', 'working_hours'], 'string', 'max' => 255],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'fuel_types'    => Yii::t('app', 'Fuel types'),
            'working_hours' => Yii::t('app', 'Working hours'),
        ]);
    }
}
