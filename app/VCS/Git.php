<?php

namespace App\VCS;

use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Git
{
    /** @var Filesystem */
    protected $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function clone(Repository $repository) : Repository
    {
        $path = $this->temporaryPath();

        /** @var Process $process */
        $process = app(Process::class, ['git clone "'.$repository->sshUrl().'" "."']);
        $process->setWorkingDirectory($path);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $repository->setPath($path);

        return $repository;
    }

    public function checkoutTag(Repository $repository, Tagged $version): bool
    {
        /** @var Process $process */
        $process = app(Process::class, ['git checkout tags/'.$version->patchTag()]);
        $process->setWorkingDirectory($repository->path());
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return true;
    }

    protected function temporaryPath(): string
    {
        $path = storage_path('git/'.uniqid());

        $this->filesystem->makeDirectory($path);

        return $path;
    }
}
