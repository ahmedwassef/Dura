<?php

namespace App\Enums;

enum RoleName: string
{
    case Reception = 'reception';
    case Doctor = 'doctor';
    case Admin = 'admin';
    case SuperAdmin = 'super-admin';
}
