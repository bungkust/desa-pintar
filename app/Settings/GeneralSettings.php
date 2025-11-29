<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $site_name;
    
    public string $village_address;
    
    public string $whatsapp;
    
    public ?string $logo_path;
    
    public ?string $instagram;

    public bool $show_statistics_section = true;

    public bool $show_lurah_section = true;

    public bool $show_berita_section = true;

    public bool $show_transparansi_section = true;

    public static function group(): string
    {
        return 'general';
    }
}

