<?php

namespace Framework\Filesystem;

use Framework\Core\ServiceProvider;

class FilesystemServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $disks = config('filesystem.disks') ?? [];
        $default = config('filesystem.default_disk') ?? 'local'; 
        $links = config('filesystem.symlinks') ?? [];

        $file = new File();
        $this->app->instance(File::class, $file); 
        $this->app->instance(DiskManager::class, new DiskManager($disks, $default, $file));

        foreach ($links as $target => $link) {
            $symlink = new SymLink($target, $link);

            if (!$symlink->exists()) {
                $symlink->create();
            } elseif ($symlink->isBroken() || !$symlink->isPointingTo($target)) {
                $symlink->create(true);
            }
        }

    }
}