<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\StaleObjectException;
use yii\web\UploadedFile;

class ChangeUserDataForm extends Model
{
    const SCENARIO_SECURITY = 'security';

    public ?string $username = null;
    public ?string $email = null;
    public ?string $old_password = null;
    public ?string $password = null;
    public ?string $password_repeat = null;
    public ?string $avatar = null;
    public ?string $birthday = null;
    public ?string $phone = null;
    public ?string $telegram = null;
    public ?string $details = null;
    public ?array $categories = null;

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            [['username', 'email'], 'required', 'on' => self::SCENARIO_DEFAULT],
            [['old_password', 'password', 'password_repeat'], 'required', 'on' => self::SCENARIO_SECURITY],
            [['username', 'old_password', 'password', 'password_repeat'], 'string', 'max' => 255],
            ['phone', 'string', 'max' => 11],
            ['telegram', 'string', 'max' => 64],
            ['email', 'email'],
            ['email', 'validateEmail'],
            ['avatar', 'file', 'maxFiles' => 1],
            ['details', 'string'],
            [['birthday', 'categories'], 'safe'],
            ['password_repeat', 'compare', 'compareAttribute' => 'password'],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function attributeLabels(): array
    {
        return [
            'username' => 'Имя пользователя',
            'email' => 'Email',
            'city_id' => 'Город',
            'old_password' => 'Старый пароль',
            'password' => 'Пароль',
            'password_repeat' => 'Повтор пароля',
            'is_executor' => 'Is Executor',
            'avatar' => 'Аватар',
            'birthday' => 'День рождения',
            'phone' => 'Номер телефона',
            'telegram' => 'Telegram',
            'details' => 'Информация о себе',
            'categories' => 'Специализации'
        ];
    }

    /**
     * Валидация почтового адреса для изменения данных аккаунта
     *
     * @return bool
     */
    public function validateEmail(): bool
    {
        $user = User::findOne(['email' => $this->email]);

        if ($user->id === Yii::$app->user->id) {
            return true;
        }

        if (!$user) {
            return true;
        }


        $this->addError('email', 'Вы не можете использовать данный Email.');
        return false;
    }

    /**
     * Изменяет данные пользователя, исходя из информации, введенной в форму
     *
     * @return bool
     * @throws StaleObjectException
     *
     * @throws \Throwable
     */
    public function change(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $user = User::findOne(Yii::$app->user->id);

        $user->username = $this->username;
        $user->email = $this->email;
        $user->birthday = $this->birthday;
        $user->phone_number = $this->phone;
        $user->telegram = $this->telegram;
        $user->details = $this->details;

        foreach (UserCategory::findAll(['user_id' => Yii::$app->user->id]) as $userCategory) {
            $userCategory->delete();
        }

        foreach ($this->categories as $category) {
            $userCategory = new UserCategory();
            $userCategory->user_id = Yii::$app->user->id;
            $userCategory->category_id = $category;

            $userCategory->save();
        }

        $file = UploadedFile::getInstance($this, 'avatar');

        if ($file) {
            $newFile = new File();
            $extension = $file->getExtension();

            $name = uniqId('upload') . ".$extension";

            $file->saveAs("@webroot/uploads/$name");

            $newFile->url = "/uploads/$name";
            $newFile->type = $extension;
            $newFile->size = $file->size;

            $newFile->save();

            $user->avatar_url = $newFile->url;
        }

        if ($this->password && $user->password) {
            if ($user->validatePassword($this->old_password)) {
                $user->password = Yii::$app->security->generatePasswordHash($this->password);
            } else {
                $this->addError('old_password', 'Неверный пароль');
                return false;
            }
        } else if ($this->password && !$user->password) {
            $user->password = Yii::$app->security->generatePasswordHash($this->password);
        }

        if ($user->save(false)) {
            return Yii::$app->user->login($user);
        }

        return false;
    }
}