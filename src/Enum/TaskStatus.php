<?php

namespace App\Enum;

enum TaskStatus: string
{
    case ToDo = 'to_do';
    case Done = 'done';
    case Doing = 'doing';
}
