<?php

namespace Framework\Security\Auth\Policies;

use Framework\Security\Auth\Contracts\PolicyInterface;

abstract class Policy implements PolicyInterface
{
    /**
     * 기본적으로 모든 정책은 구현 클래스에서 정의됩니다.
     * 이 베이스 클래스는 확장성과 통일성을 제공합니다.
     */
}
