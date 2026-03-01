<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Storage;
use DateTimeZone;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::firstOrCreate([]);
        $timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        $consumptionTypes = [
            'FIFO' => \App\Enums\InventoryConsumptionType::FIFO->value,
            'FEFO' => \App\Enums\InventoryConsumptionType::FEFO->value,
        ];

        return Inertia::render('Settings/Index', [
            'settings' => $settings,
            'timezones' => $timezones,
            'consumptionTypes' => $consumptionTypes,
            'currencyOptions' => [
                'USD',
                'EUR',
                'GBP',
                'INR',
                'JPY',
                'CNY',
                'AUD',
                'CAD',
                'CHF',
                'SEK',
                'NZD',
                'MXN',
                'SGD',
                'HKD',
                'NOK',
                'KRW',
                'TRY',
                'RUB',
                'BRL',
                'ZAR',
                'PKR',
                'BDT',
                'AED',
                'SAR',
            ],
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'app_title' => 'required|string|max:255',
            'currency'  => 'required|string|max:10',
            'timezone'  => 'required|string|max:255',
            'inventory_consumption_type' => 'required|string|in:FIFO,FEFO',
        ]);

        $settings = Setting::firstOrCreate([]);

        // Conditionally validate logo_path only if a new file is provided
        if ($request->hasFile('logo_path')) {
            $request->validate([
                'logo_path' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
        }

        if ($request->hasFile('logo_path')) {

            // Delete old logo
            if ($settings->logo_path) {
                Storage::disk('public')->delete($settings->logo_path);
            }

            $image = $request->file('logo_path');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $path = 'logos/' . $filename;

            // Read, resize, encode (v3 correct way)
            $img = Image::read($image)
                ->resize(300, 300)
                ->encode(); // returns EncodedImage

            // Store as string
            Storage::disk('public')->put($path, (string) $img);

            $validated['logo_path'] = $path;
        }

        $settings->update($validated);

        return back()->with('success', 'Settings updated successfully.');
    }
}
