<?php

namespace app\models;

use Yii;

class MedicalPoint extends PoiBase
{
    const TYPES = [
        'hospital'          => 'Больница',
        'clinic'            => 'Поликлиника',
        'first_aid'         => 'Медпункт',
        'pharmacy'          => 'Аптека',
        'emergency_station' => 'Станция скорой помощи',
    ];

    public static function tableName() { return '{{%medical_point}}'; }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['address', 'phone', 'type', 'working_hours'], 'string', 'max' => 255],
            [['is_24h', 'ambulance_available'], 'boolean'],
            [['is_24h', 'ambulance_available'], 'default', 'value' => 0],
            [['type'], 'in', 'range' => array_keys(self::TYPES)],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'type'                => Yii::t('app', 'Type'),
            'is_24h'              => Yii::t('app', '24/7'),
            'ambulance_available' => Yii::t('app', 'Ambulance available'),
        ]);
    }
}
