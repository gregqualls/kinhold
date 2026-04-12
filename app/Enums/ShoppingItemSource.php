<?php

namespace App\Enums;

enum ShoppingItemSource: string
{
    case Manual = 'manual';
    case Recipe = 'recipe';
    case Staple = 'staple';
}
