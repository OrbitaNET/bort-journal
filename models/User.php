<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * @property int    $id
 * @property string $username
 * @property string $phone
 * @property string $auth_key
 * @property string $access_token
 * @property int    $telegram_id
 * @property string $telegram_username
 * @property string $auth_code
 * @property int    $auth_code_expires
 * @property string $role
 * @property int    $status
 * @property int    $created_at
 * @property int    $updated_at
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE   = 10;

    const ROLE_SUPERADMIN = 'superadmin';
    const ROLE_ADMIN      = 'admin';
    const ROLE_USER       = 'user';

    const AUTH_CODE_TTL = 300; // 5 minutes

    public static function tableName()
    {
        return '{{%user}}';
    }

    // -------------------------------------------------------------------------
    // IdentityInterface
    // -------------------------------------------------------------------------

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token, 'status' => self::STATUS_ACTIVE]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    // -------------------------------------------------------------------------
    // Finders
    // -------------------------------------------------------------------------

    public static function findByPhone($phone)
    {
        return static::findOne(['phone' => static::normalizePhone($phone)]);
    }

    public static function findByTelegramId($telegramId)
    {
        return static::findOne(['telegram_id' => (int)$telegramId]);
    }

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    // -------------------------------------------------------------------------
    // Phone helpers
    // -------------------------------------------------------------------------

    /**
     * Strip everything except digits and leading +.
     */
    public static function normalizePhone($phone)
    {
        $phone = preg_replace('/[^\d+]/', '', trim($phone));
        // ensure leading +
        if ($phone !== '' && $phone[0] !== '+') {
            $phone = '+' . $phone;
        }
        return $phone;
    }

    // -------------------------------------------------------------------------
    // Auth code
    // -------------------------------------------------------------------------

    public function generateAuthCode()
    {
        $this->auth_code         = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        $this->auth_code_expires = time() + self::AUTH_CODE_TTL;
        $this->save(false);

        return $this->auth_code;
    }

    public function validateAuthCode($code)
    {
        return $this->auth_code !== null
            && $this->auth_code === str_pad((string)$code, 4, '0', STR_PAD_LEFT)
            && $this->auth_code_expires >= time();
    }

    public function clearAuthCode()
    {
        $this->auth_code         = null;
        $this->auth_code_expires = null;
        $this->save(false);
    }

    // -------------------------------------------------------------------------
    // Lifecycle
    // -------------------------------------------------------------------------

    public function isSuperadmin()
    {
        return $this->role === self::ROLE_SUPERADMIN;
    }

    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($insert) {
            $this->auth_key   = Yii::$app->security->generateRandomString(32);
            $this->created_at = time();
        }

        $this->updated_at = time();

        return true;
    }
}
