<?php

namespace App\Enum;

enum UserStatus: string
{
    case CDI = 'CDI';
    case CDD = 'CDD';
    case FREELANCE = 'FREELANCE';
    case STAGE = 'STAGE';
    case ALTERNANCE = 'ALTERNANCE';
}
