<?php

namespace App\Enums;

enum ContentType: string
{
    case Blog = 'blog';
    case News = 'news';
    case CaseStudy = 'case_study';
    case Page = 'page';
}
