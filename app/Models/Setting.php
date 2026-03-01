<?php

namespace App\Models;

use App\Scopes\FarmScope;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{

    protected $fillable = [
        'app_title',
        'currency',
        'timezone',
        'inventory_consumption_type',
        'logo_path',

        // Super admin website-only settings
        'site_title',
        'site_description',
        'website_currency',
        'website_logo_path',
    ];

    protected $appends = [
        'currency_symbol',
        'website_currency_symbol',
    ];

    /**
     * Get the currency symbol based on the currency code
     */
    public function getCurrencySymbolAttribute(): string
    {
        return $this->currencyCodeToSymbol($this->currency);
    }

    public function getWebsiteCurrencySymbolAttribute(): string
    {
        return $this->currencyCodeToSymbol($this->website_currency);
    }

    private function currencyCodeToSymbol(?string $code): string
    {
        $currencySymbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'INR' => '₹',
            'JPY' => '¥',
            'CNY' => '¥',
            'AUD' => 'A$',
            'CAD' => 'C$',
            'CHF' => 'Fr',
            'SEK' => 'kr',
            'NZD' => 'NZ$',
            'MXN' => 'Mex$',
            'SGD' => 'S$',
            'HKD' => 'HK$',
            'NOK' => 'kr',
            'KRW' => '₩',
            'TRY' => '₺',
            'RUB' => '₽',
            'BRL' => 'R$',
            'ZAR' => 'R',
            'PKR' => '₨',
            'BDT' => '৳',
            'AED' => 'د.إ',
            'SAR' => '﷼',
        ];

        if (!$code) return '$';

        return $currencySymbols[$code] ?? $code;
    }
}
