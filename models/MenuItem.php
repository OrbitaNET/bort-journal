<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int    $id
 * @property int    $group_id
 * @property string $label
 * @property string $controller
 * @property string $action
 * @property int    $sort_order
 * @property int    $is_active
 *
 * @property MenuGroup $group
 */
class MenuItem extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%menu_item}}';
    }

    public function attributeLabels()
    {
        return [
            'label'      => \Yii::t('app', 'Label'),
            'controller' => \Yii::t('app', 'Controller'),
            'action'     => \Yii::t('app', 'Action'),
            'group_id'   => \Yii::t('app', 'Group'),
            'sort_order' => \Yii::t('app', 'Sort order'),
            'is_active'  => \Yii::t('app', 'Active'),
        ];
    }

    public function rules()
    {
        return [
            [['label', 'controller'], 'required'],
            [['label', 'controller', 'action'], 'string', 'max' => 128],
            [['action'], 'default', 'value' => 'index'],
            [['group_id', 'sort_order'], 'integer'],
            [['group_id'], 'default', 'value' => null],
            [['sort_order'], 'default', 'value' => 0],
            [['is_active'], 'boolean'],
            [['is_active'], 'default', 'value' => 1],
            [['group_id'], 'exist', 'targetClass' => MenuGroup::class, 'targetAttribute' => 'id', 'allowArray' => false, 'skipOnEmpty' => true],
        ];
    }

    public function getGroup()
    {
        return $this->hasOne(MenuGroup::class, ['id' => 'group_id']);
    }

    public function getUrl()
    {
        return ['/' . $this->controller . '/' . $this->action];
    }
}
