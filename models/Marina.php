<?php

namespace app\models;

use Yii;

class Marina extends PoiBase
{
    public static function tableName() { return '{{%marina}}'; }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['address', 'phone', 'working_hours'], 'string', 'max' => 255],
            [['services'], 'string'],
            [['berths_count'], 'integer', 'min' => 0],
            [['max_draft'], 'number', 'min' => 0, 'max' => 99],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'berths_count' => Yii::t('app', 'Berths count'),
            'max_draft'    => Yii::t('app', 'Max draft (m)'),
            'services'     => Yii::t('app', 'Services'),
        ]);
    }
}
