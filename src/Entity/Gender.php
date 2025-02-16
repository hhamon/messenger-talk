<?php

declare(strict_types=1);

namespace App\Entity;

enum Gender: string
{
    case MALE = 'male';
    case FEMALE = 'female';
    case OTHER = 'other';
}
