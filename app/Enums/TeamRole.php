<?php

namespace App\Enums;

enum TeamRole: string
{
    case OWNER = 'owner';

    case ADMIN = 'admin';

    case VIEWER = 'viewer';
}
