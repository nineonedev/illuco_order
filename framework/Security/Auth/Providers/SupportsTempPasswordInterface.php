<?php
namespace Framework\Security\Auth\Providers;

interface SupportsTempPasswordInterface extends AuthenticatableInterface
{
    public function getTempPasswordHash(): ?string;

    /** 만료시간이 없거나 지났다면 null 반환해도 OK */
    public function getTempPasswordExpiresAt(): ?\DateTimeInterface;

    /** 임시비밀번호 성공 로그인 시 즉시 호출해 만료/삭제 */
    public function clearTempPassword(): void;

    /** (선택) 임시로 로그인하면 비번변경 강제 여부 */
    // public function mustChangePassword(): bool;
}
