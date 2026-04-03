<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int    $id
 * @property string $name
 * @property int    $sort_order
 *
 * @property MenuItem[] $items
 */
class MenuGroup extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%menu_group}}';
    }

    public function attributeLabels()
    {
        return [
            'name'       => \Yii::t('app', 'Name'),
            'sort_order' => \Yii::t('app', 'Sort order'),
        ];
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 128],
            [['sort_order'], 'integer'],
            [['sort_order'], 'default', 'value' => 0],
        ];
    }

    public function getItems()
    {
        return $this->hasMany(MenuItem::class, ['group_id' => 'id'])
            ->orderBy(['sort_order' => SORT_ASC, 'label' => SORT_ASC]);
    }
}
