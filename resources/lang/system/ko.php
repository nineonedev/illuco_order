<?php

use App\Domains\System\Entities\FileAttachment;
use App\Domains\Communication\Entities\Claim;
use App\Domains\Communication\Entities\Notice;
use App\Domains\User\Entities\Dealer;
use App\Domains\User\Entities\Employee;

return [
    Employee::alias() => '직원',
    Notice::alias() => '공지',
    Dealer::alias() => '대리점',
    Claim::alias() => '클레임',
    FileAttachment::alias() => '파일첨부',
    
    'email' => '이메일',

    'create' => '생성',
    'read'   => '조회',
    'update' => '수정',
    'delete' => '삭제',
];