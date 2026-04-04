<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int    $id
 * @property int    $application_id
 * @property string $poi_type
 * @property int    $poi_id
 * @property int    $sort_order
 *
 * @property Application $application
 */
class ApplicationWaypoint extends ActiveRecord
{
    const POI_TYPES = [
        'fuel_station'    => ['class' => 'app\models\FuelStation',    'label' => 'Топливная станция'],
        'marina'          => ['class' => 'app\models\Marina',         'label' => 'Марина'],
        'medical_point'   => ['class' => 'app\models\MedicalPoint',   'label' => 'Медпункт'],
        'rescue_service'  => ['class' => 'app\models\RescueService',  'label' => 'Спасслужба'],
        'service_station' => ['class' => 'app\models\ServiceStation', 'label' => 'Сервисная станция'],
        'authority'       => ['class' => 'app\models\Authority',      'label' => 'Ведомство'],
    ];

    public static function tableName()
    {
        return '{{%application_waypoint}}';
    }

    public function getApplication()
    {
        return $this->hasOne(Application::class, ['id' => 'application_id']);
    }

    public function getPoiTypeLabel()
    {
        return self::POI_TYPES[$this->poi_type]['label'] ?? $this->poi_type;
    }

    /**
     * Load and return the underlying POI ActiveRecord.
     * @return ActiveRecord|null
     */
    public function getPoi()
    {
        $info = self::POI_TYPES[$this->poi_type] ?? null;
        if (!$info) {
            return null;
        }
        return ($info['class'])::findOne($this->poi_id);
    }

    /**
     * Return key→value pairs for PDF/view rendering.
     */
    public function getPoiDetails()
    {
        $poi = $this->getPoi();
        if (!$poi) {
            return [];
        }
        $details = [];
        if (!empty($poi->address))       $details['Адрес']           = $poi->address;
        if (!empty($poi->phone))         $details['Телефон']         = $poi->phone;
        if (!empty($poi->working_hours)) $details['Режим работы']    = $poi->working_hours;
        if (!empty($poi->description))   $details['Описание']        = $poi->description;

        // Type-specific fields
        if ($poi instanceof \app\models\Marina) {
            if (!empty($poi->berths_count)) $details['Мест']          = $poi->berths_count;
            if (!empty($poi->max_draft))    $details['Макс. осадка']  = $poi->max_draft . ' м';
            if (!empty($poi->services))     $details['Сервисы']       = $poi->services;
        }
        if ($poi instanceof \app\models\FuelStation) {
            if (!empty($poi->fuel_types))   $details['Виды топлива']  = $poi->fuel_types;
        }
        if ($poi instanceof \app\models\MedicalPoint) {
            if (!empty($poi->is_24h))       $details['Круглосуточно'] = $poi->is_24h ? 'Да' : 'Нет';
        }
        if ($poi instanceof \app\models\ServiceStation) {
            if (!empty($poi->service_types)) $details['Услуги']       = $poi->service_types;
        }
        if ($poi instanceof \app\models\Authority) {
            if (!empty($poi->website))      $details['Сайт']          = $poi->website;
        }
        if ($poi instanceof \app\models\RescueService) {
            if (!empty($poi->is_24h))       $details['Круглосуточно'] = $poi->is_24h ? 'Да' : 'Нет';
        }
        return $details;
    }
}
