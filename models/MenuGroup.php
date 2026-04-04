<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int    $id
 * @property string $name
 * @property string $name_ru
 * @property int    $sort_order
 * @property string $min_role
 *
 * @property MenuItem[] $items
 */
class MenuGroup extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%menu_group}}';
    }

    const MIN_ROLE_USER       = 'user';
    const MIN_ROLE_ADMIN      = 'admin';
    const MIN_ROLE_SUPERADMIN = 'superadmin';

    public function attributeLabels()
    {
        return [
            'name'       => \Yii::t('app', 'Name'),
            'sort_order' => \Yii::t('app', 'Sort order'),
            'min_role'   => \Yii::t('app', 'Min role'),
        ];
    }

    public function getLocalizedName()
    {
        if (\Yii::$app->language === 'ru' && !empty($this->name_ru)) {
            return $this->name_ru;
        }
        return $this->name;
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'name_ru'], 'string', 'max' => 128],
            [['sort_order'], 'integer'],
            [['sort_order'], 'default', 'value' => 0],
            [['min_role'], 'in', 'range' => [self::MIN_ROLE_USER, self::MIN_ROLE_ADMIN, self::MIN_ROLE_SUPERADMIN]],
            [['min_role'], 'default', 'value' => self::MIN_ROLE_USER],
        ];
    }

    public function getItems()
    {
        return $this->hasMany(MenuItem::class, ['group_id' => 'id'])
            ->orderBy(['sort_order' => SORT_ASC, 'label' => SORT_ASC]);
    }
}
