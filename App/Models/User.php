<?php

namespace App\Models;

use App\Core\DbModel;
Use App\Core\App;

class User extends DbModel {
    public const SCENARIO_REGISTER = 'register';
    public const SCENARIO_LOGIN = 'login';
    public ?int $id_user = null;
    public string $nama = '';
    public ?string $nim = null;
    public ?string $nip = null;
    public string $email = '';
    public string $password = '';
    public string $confirm_password = '';
    public ?int $id_role = null;
    public ?string $kubaca_img = null;
    public int $peringatan = 0;
    public string $status = 'pending';
    public ?string $jurusan = null;
    public ?string $nomor_hp = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;
    public string $identifier = '';
    public string $scenario = self::SCENARIO_REGISTER;

    public static function tableName(): string {
        return 'users';
    }

    public static function primaryKey(): string {
        return 'id_user';
    }

    public function setScenario(string $scenario): void
    {
        $this->scenario = $scenario;
    }

    public function rules(): array
    {
        if ($this->scenario === self::SCENARIO_LOGIN) {
            return [
                'identifier' => [self::RULE_REQUIRED],
                'password' => [self::RULE_REQUIRED],
            ];
        } else if ($this->scenario === self::SCENARIO_REGISTER) {
            $rules = [
            'nama' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 3]],
            'email' => [self::RULE_REQUIRED, self::RULE_EMAIL, [self::RULE_UNIQUE, 'class' => self::class]],
            'password' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 4], [self::RULE_MAX, 'max' => 24]],
            'confirm_password' => [self::RULE_REQUIRED, [self::RULE_MATCH, 'match' => 'password']],
            'jurusan' => [self::RULE_REQUIRED],
            'nomor_hp' => [self::RULE_REQUIRED, self::RULE_NUMBER],
            'id_role' => [self::RULE_REQUIRED],
        ];

        if ((string)$this->id_role === '3') {
            $rules['nim'] = [
                self::RULE_REQUIRED,
                self::RULE_NUMBER,
                [self::RULE_MIN, 'min' => 10],
                [self::RULE_MAX, 'max' => 10],
                [self::RULE_UNIQUE, 'class' => self::class]
            ];
        } elseif ((string)$this->id_role === '2') {
            $rules['nip'] = [
                self::RULE_REQUIRED,
                self::RULE_NUMBER,
                [self::RULE_MIN, 'min' => 18],
                [self::RULE_MAX, 'max' => 18],
                [self::RULE_UNIQUE, 'class' => self::class]
            ];
        }
        return $rules;
        }

        return [];
    }

    public function attributes(): array {
        return [
            'id_user',
            'nama',
            'nim',
            'nip',
            'email',
            'password',
            'id_role',
            'kubaca_img',
            'peringatan',
            'status',
            'jurusan',
            'nomor_hp',
            'created_at',
            'updated_at',
        ];
    }

    public function save(): bool {
        $this->status = 'pending';
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        return parent::save();
    }

    public function getDisplayName(): string {
        return $this->nama;
    }

    public function isAdmin(): bool {
        return (string)$this->id_role === '1';
    }

    public function isDosen(): bool {
        return (string)$this->id_role === '2';
    }

    public function isMahasiswa(): bool {   
        return (string)$this->id_role === '3';
    }

    public static function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }

    public static function getCurrentUser(): ?User {
        if (self::isLoggedIn()) {
            return self::findOne(['id_user' => $_SESSION['user_id']]);
        }
        return null;
    }

    public function login(): bool
    {
        $user = User::findOne(['email' => $this->identifier]);

        if (!$user) {
            $user = User::findOne(['nim' => $this->identifier]);
        }

        if (!$user) {
            $user = User::findOne(['nip' => $this->identifier]);
        }

        if (!$user) {
            $this->addError('identifier', 'User not found');
            return false;
        }

        if (!password_verify($this->password, $user->password)) {
            $this->addError('password', 'Password is incorrect');
            return false;
        }

        if ($user->status === 'suspended') {
            $this->addError('identifier', 'Your account has been suspended. Please contact support.');
            return false;
        }
        
        if ($user->status === 'pending') {
            $this->addError('identifier', 'Your account is pending verification. Please check your email.');
            return false;
        }

        App::$app->auth->login($user);
        App::$app->user = App::$app->auth->getUser();

        return true;
    }
}
