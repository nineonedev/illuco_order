<?php

namespace App\Domains\User\Entities;

use App\Domains\User\Enums\UserType;
use App\Domains\User\Repositories\UserRepository;
use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\Traits\SoftDeletes;
use Framework\Security\Auth\Providers\AuthenticatableInterface;
use Framework\Security\Auth\Providers\SupportsTempPasswordInterface;

class User extends Entity implements AuthenticatableInterface, SupportsTempPasswordInterface
{
    use SoftDeletes;

    protected array $fillable = [
        'name',
        'type',
        'email',
        'password',
        'phone',
        'gender',
        'email_verified_at',
        'birth',
        'is_active',
        'created_at',
        'temp_password_hash',
        'temp_password_expires_at',
    ];

    protected array $casts = [
        'name'                => 'string',
        'type'                => 'string',
        'email'               => 'string',
        'password'            => 'string',
        'phone'               => '?string',
        'gender'              => '?string',
        'email_verified_at'   => 'datetime',
        'birth'               => 'date',
        'is_active'           => 'bool',
        'temp_password_hash' => 'string',
        'temp_password_expires_at' => 'datetime',
    ];
    
    public static function table(): string
    {
        return 'users';
    }

    public static function repositoryClass(): string
    {
        return UserRepository::class;
    }

    public function getTempPasswordHash(): ?string
    {
        return $this->temp_password_hash ?? null;
    }

    public function getTempPasswordExpiresAt(): ?\DateTimeInterface
    {
        if (empty($this->temp_password_expires_at)) {
            return null;
        }

        // 이미 DateTime 객체면 그대로 리턴
        if ($this->temp_password_expires_at instanceof \DateTimeInterface) {
            return $this->temp_password_expires_at;
        }

        // 문자열일 경우 DateTimeImmutable로 변환 시도
        try {
            return new \DateTimeImmutable($this->temp_password_expires_at);
        } catch (\Exception $e) {
            return null; // 변환 실패하면 null 반환
        }
    }


    public function clearTempPassword(): void
    {
        $this->temp_password_hash = null;
        $this->temp_password_expires_at = null;
        UserRepository::make()->save($this);
    }

    public function getAuthPasswordCandidates(): array
    {
        $candidates = [];

        // 기본 비밀번호
        if (!empty($this->password)) {
            $candidates[] = $this->password;
        }

        // 임시 비밀번호가 아직 유효한 경우
        if (!empty($this->temp_password_hash) && !empty($this->temp_password_expires_at)) {
            $now = new \DateTimeImmutable();
            if ($this->temp_password_expires_at >= $now) {
                $candidates[] = $this->temp_password_hash;
            }
        }

        return $candidates;
    }

    public function usesTempPassword(): bool
    {
        $now = new \DateTimeImmutable();

        return !empty($this->temp_password_hash) 
            && !empty($this->temp_password_expires_at) 
            && $this->temp_password_expires_at >= $now;
    }

    public function getAuthIdentifier()
    {
        return $this->id;
    }

    public function getAuthPassword(): string
    {
        return $this->password;
    }

    public function isDealer(): bool
    {
        $isDealer = $this->type === UserType::DEALER; 
    
        if ($isDealer) {
            $this->load([UserType::DEALER]);
        }
        
        return $isDealer; 
    }

    public function isAdmin(): bool
    {
        return $this->type === UserType::ADMIN;
    }

    public function isEmployee(): bool
    {
        return $this->type === UserType::EMPLOYEE;
    }

    public function hasRole(string $roleName): bool
    {
        if (!$this->getRelation('roles')){
            $this->load(['roles']); 
        }

        foreach ($this->roles as $role) {
            if ($role === $roleName) {
                return true; 
            }
        }

        return false; 
    }

    public function isSales(): bool
    {
        return $this->hasRole('sales');
    }
}
