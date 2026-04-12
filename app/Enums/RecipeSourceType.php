<?php

namespace App\Enums;

enum RecipeSourceType: string
{
    case Manual = 'manual';
    case Url = 'url';
    case Photo = 'photo';
    case SocialMedia = 'social_media';
}
