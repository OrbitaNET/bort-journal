<?php

namespace app\models;

use yii\db\ActiveRecord;

class MapPolygon extends ActiveRecord
{
    public static function tableName() { return '{{%map_polygon}}'; }

    public function rules()
    {
        return [
            [['name', 'coordinates'], 'required'],
            [['name', 'color'], 'string', 'max' => 255],
            [['coordinates'], 'string'],
        ];
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) return false;
        if ($insert) $this->created_at = time();
        $this->updated_at = time();
        return true;
    }
}
