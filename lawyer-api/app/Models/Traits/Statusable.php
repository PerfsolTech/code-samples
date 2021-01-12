<?php

namespace App\Models\Traits;

trait Statusable
{
    protected static $statusEnabled = 'ENABLED';
    protected static $statusDisabled = 'DISABLED';

    public function scopeEnabled($query)
    {
        $query->where('status', '=', self::$statusEnabled);
    }

    public function scopeDisabled($query)
    {
        $query->where('status', '=', self::$statusDisabled);
    }

    public function isEnabled()
    {
        return $this->status == self::$statusEnabled;
    }

    public function isDisabled()
    {
        return $this->status == self::$statusDisabled;
    }

    public static function getHumanStatuses()
    {
        return [
            self::$statusEnabled => 'Enabled',
            self::$statusDisabled => 'Disabled',
        ];
    }

    public static function getStatusEnabled(): string
    {
        return self::$statusEnabled;
    }

    public static function getStatusDisabled(): string
    {
        return self::$statusDisabled;
    }
}