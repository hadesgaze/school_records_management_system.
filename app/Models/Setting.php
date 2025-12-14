<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['name', 'value'];

    /**
     * Retrieve a setting value by name.
     */
    public static function getValue($name)
    {
        $setting = self::where('name', $name)->first();
        return $setting ? $setting->value : null;
    }

    /**
     * Create or update a setting.
     */
    public static function setValue($name, $value)
    {
        return self::updateOrCreate(
            ['name' => $name],
            ['value' => $value]
        );
    }
}
