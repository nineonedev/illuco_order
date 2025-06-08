<?php

use App\User\Entities\File;
use App\User\Entities\MultiFile;
use App\User\Entities\Notice;
use App\User\Entities\Post;
use App\User\Entities\User;
use Framework\Database\ORM\RelationMap;

RelationMap::setConfig([
    User::class => [
        RelationMap::hasMany('posts', Post::class, 'user_id'),
        RelationMap::morphOne('profile_image', File::class),
    ],
    Post::class => [
        RelationMap::belongsTo('user', User::class, 'user_id'),
        RelationMap::morphOne('thumb_image', File::class),
    ],
    Notice::class => [
        RelationMap::morphedByMany('attachments', File::class, 'multi_files', 'file_id'),
    ],
    File::class => [
        RelationMap::morphTo('owner'),
        RelationMap::morphToMany('owners', File::class, 'mutli_files', 'file_id')
    ],
]);
