<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * Base class for all POI (Point of Interest) models.
 * Provides shared coordinate/timestamp logic.
 */
abstract class PoiBase extends ActiveRecord
{
    public function rules()
    {
        return [
            [['name', 'lat', 'lng'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['lat', 'lng'], 'number'],
            [['lat'], 'number', 'min' => -90,  'max' => 90],
            [['lng'], 'number', 'min' => -180, 'max' => 180],
            [['description'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'name'        => Yii::t('app', 'Name'),
            'lat'         => Yii::t('app', 'Latitude'),
            'lng'         => Yii::t('app', 'Longitude'),
            'address'     => Yii::t('app', 'Address'),
            'phone'       => Yii::t('app', 'Phone'),
            'description' => Yii::t('app', 'Description'),
            'working_hours' => Yii::t('app', 'Working hours'),
            'created_at'  => Yii::t('app', 'Created'),
            'updated_at'  => Yii::t('app', 'Updated'),
        ];
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if ($insert) {
            $this->created_at = time();
        }
        $this->updated_at = time();
        return true;
    }
}
