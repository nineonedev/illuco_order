<?php

namespace Framework\Database\Model; 

class ModelEvent
{
    const BEFORE_CREATE = 'beforeCreate';
    const AFTER_CREATE  = 'afterCreate';
    const BEFORE_SAVE = 'beforeSave';
    const AFTER_SAVE  = 'afterSave';
    const BEFORE_UPDATE = 'beforeUpdate';
    const AFTER_UPDATE  = 'afterUpdate';
    const BEFORE_DELETE = 'beforeDelete';
    const AFTER_DELETE  = 'afterDelete';

    const AFTER_LOAD = 'afterLoad';
}