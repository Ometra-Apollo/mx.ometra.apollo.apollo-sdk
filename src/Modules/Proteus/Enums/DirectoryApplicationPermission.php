<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Modules\Proteus\Enums;

enum DirectoryApplicationPermission: string
{
    case READ = 'read';
    case WRITE = 'write';
}
