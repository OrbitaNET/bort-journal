<?php

namespace app\models;

use Yii;

class Authority extends PoiBase
{
    public static function tableName() { return '{{%authority}}'; }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['address', 'phone', 'website', 'type'], 'string', 'max' => 255],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'type'    => Yii::t('app', 'Type'),
            'website' => Yii::t('app', 'Website'),
        ]);
    }

    public function getOfficials()
    {
        return $this->hasMany(AuthorityOfficial::class, ['authority_id' => 'id'])
            ->orderBy(['full_name' => SORT_ASC]);
    }
}
