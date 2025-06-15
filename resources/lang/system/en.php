<?php

use App\Domains\System\Entities\FileAttachment;
use App\Domains\Communication\Entities\Claim;
use App\Domains\Communication\Entities\Notice;
use App\Domains\User\Entities\Dealer;
use App\Domains\User\Entities\Employee;

return [
    Employee::alias() => 'Employee',
    Notice::alias() => 'Notice',
    Dealer::alias() => 'Dealer',
    Claim::alias() => 'Claim',
    FileAttachment::alias() => 'File Attachment',

    'email' => 'Email',

    'create' => 'Create',
    'read'   => 'Read',
    'update' => 'Update',
    'delete' => 'Delete',
];
