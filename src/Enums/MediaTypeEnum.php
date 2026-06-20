<?php

declare(strict_types=1);

namespace Ometra\Apollo\Sdk\Enums;

enum MediaTypeEnum: string
{
    case video = 'video';
    case audio = 'audio';
    case image = 'image';
}
