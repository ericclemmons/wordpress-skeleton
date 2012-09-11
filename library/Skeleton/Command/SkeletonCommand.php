<?php

namespace Skeleton\Command;

use Skeleton\Skeleton;
use Skeleton\SkeletonAwareInterface;
use Symfony\Component\Console\Command\Command as BaseCommand;

class SkeletonCommand extends BaseCommand implements SkeletonAwareInterface
{
    protected $skeleton;

    public function setSkeleton(Skeleton $skeleton)
    {
        $this->skeleton = $skeleton;
    }
}
