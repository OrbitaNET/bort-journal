<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * @property int    $id
 * @property int    $creator_id
 * @property string $status
 * @property float  $start_lat
 * @property float  $start_lng
 * @property string $start_address
 * @property float  $end_lat
 * @property float  $end_lng
 * @property string $end_address
 * @property string $notes
 * @property int    $created_at
 * @property int    $updated_at
 *
 * @property User                  $creator
 * @property ApplicationWaypoint[] $waypoints
 */
class Application extends ActiveRecord
{
    const STATUS_DRAFT      = 'draft';
    const STATUS_CREATED    = 'created';
    const STATUS_PROCESSING = 'processing';
    const STATUS_PROCESSED  = 'processed';
    const STATUS_SENT       = 'sent';

    public static function tableName()
    {
        return '{{%application}}';
    }

    public function rules()
    {
        return [
            [['start_lat', 'start_lng', 'end_lat', 'end_lng'], 'required'],
            [['start_lat', 'end_lat'], 'number', 'min' => -90,  'max' => 90],
            [['start_lng', 'end_lng'], 'number', 'min' => -180, 'max' => 180],
            [['start_address', 'end_address'], 'string', 'max' => 512],
            [['notes'], 'string'],
            [['status'], 'in', 'range' => array_keys(self::statusLabels())],
            [['status'], 'default', 'value' => self::STATUS_DRAFT],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'creator_id'    => Yii::t('app', 'Creator'),
            'status'        => Yii::t('app', 'Status'),
            'start_lat'     => Yii::t('app', 'Start latitude'),
            'start_lng'     => Yii::t('app', 'Start longitude'),
            'start_address' => Yii::t('app', 'Start address'),
            'end_lat'       => Yii::t('app', 'End latitude'),
            'end_lng'       => Yii::t('app', 'End longitude'),
            'end_address'   => Yii::t('app', 'End address'),
            'notes'         => Yii::t('app', 'Notes'),
            'created_at'    => Yii::t('app', 'Created'),
            'updated_at'    => Yii::t('app', 'Updated'),
        ];
    }

    public function getCreator()
    {
        return $this->hasOne(User::class, ['id' => 'creator_id']);
    }

    public function getWaypoints()
    {
        return $this->hasMany(ApplicationWaypoint::class, ['application_id' => 'id'])
            ->orderBy(['sort_order' => SORT_ASC]);
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if ($insert) {
            $this->created_at = time();
            $this->creator_id = Yii::$app->user->id;
        }
        $this->updated_at = time();
        return true;
    }

    public static function statusLabels()
    {
        return [
            self::STATUS_DRAFT      => Yii::t('app', 'Draft'),
            self::STATUS_CREATED    => Yii::t('app', 'App status created'),
            self::STATUS_PROCESSING => Yii::t('app', 'Processing'),
            self::STATUS_PROCESSED  => Yii::t('app', 'Processed'),
            self::STATUS_SENT       => Yii::t('app', 'Sent'),
        ];
    }

    public function getStatusLabel()
    {
        return static::statusLabels()[$this->status] ?? $this->status;
    }

    public function getStatusBadgeClass()
    {
        $map = [
            self::STATUS_DRAFT      => 'secondary',
            self::STATUS_CREATED    => 'primary',
            self::STATUS_PROCESSING => 'warning',
            self::STATUS_PROCESSED  => 'success',
            self::STATUS_SENT       => 'info',
        ];
        return $map[$this->status] ?? 'secondary';
    }

    public function canEdit($userId, $isAdmin)
    {
        if ($isAdmin) {
            return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_CREATED, self::STATUS_PROCESSING]);
        }
        return $this->creator_id == $userId && $this->status === self::STATUS_DRAFT;
    }

    public function canDelete($userId, $isAdmin)
    {
        if ($isAdmin) return true;
        return $this->creator_id == $userId && $this->status === self::STATUS_DRAFT;
    }
}
