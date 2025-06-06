<?php

namespace Framework\Database\ORM\Repositories;

final class RepositoryEvent
{
    // 기본적인 라이프사이클 이벤트
    const BEFORE_SAVE     = 'beforeSave';
    const AFTER_SAVE      = 'afterSave';
    const BEFORE_CREATE   = 'beforeCreate';
    const AFTER_CREATE    = 'afterCreate';
    const BEFORE_UPDATE   = 'beforeUpdate';
    const AFTER_UPDATE    = 'afterUpdate';
    const BEFORE_DELETE   = 'beforeDelete';
    const AFTER_DELETE    = 'afterDelete';
    const BEFORE_SOFT_DELETE = 'beforeSoftDelete';
    const AFTER_SOFT_DELETE  = 'afterSoftDelete';
}
