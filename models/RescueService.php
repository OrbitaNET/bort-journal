<?php

namespace app\models;

use Yii;

class RescueService extends PoiBase
{
    const TYPES = [
        'mchs'   => 'МЧС',
        'fire'   => 'Пожарная служба',
        'search' => 'Поисково-спасательная служба',
        'water'  => 'Водно-спасательная служба',
        'other'  => 'Иная служба спасения',
    ];

    public static function tableName() { return '{{%rescue_service}}'; }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['address', 'phone', 'type'], 'string', 'max' => 255],
            [['is_24h'], 'boolean'],
            [['is_24h'], 'default', 'value' => 0],
            [['type'], 'required'],
            [['type'], 'in', 'range' => array_keys(self::TYPES)],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'type'   => Yii::t('app', 'Type'),
            'is_24h' => Yii::t('app', '24/7'),
        ]);
    }
}
