<?php

namespace App\Enums;

enum HealthStatus: string
{
    case Healthy = 'healthy';
    case Weak = 'weak';
    case Critical = 'critical';
}
