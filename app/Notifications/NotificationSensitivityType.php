<?php

namespace App\Notifications;

abstract class NotificationSensitivityType
{
    public const ANY = 'ANY';
    public const PUBLIC = 'PUBLIC';
    public const PRIVATE = 'PRIVATE';
}
