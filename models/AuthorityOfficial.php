<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class AuthorityOfficial extends ActiveRecord
{
    public static function tableName() { return '{{%authority_official}}'; }

    public function rules()
    {
        return [
            [['authority_id', 'full_name'], 'required'],
            [['authority_id'], 'integer'],
            [['full_name', 'position', 'phone', 'email'], 'string', 'max' => 255],
            [['email'], 'email'],
            [['authority_id'], 'exist', 'targetClass' => Authority::class, 'targetAttribute' => 'id'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'full_name' => Yii::t('app', 'Full name'),
            'position'  => Yii::t('app', 'Position'),
            'phone'     => Yii::t('app', 'Phone'),
            'email'     => Yii::t('app', 'Email'),
        ];
    }

    public function getAuthority()
    {
        return $this->hasOne(Authority::class, ['id' => 'authority_id']);
    }
}
