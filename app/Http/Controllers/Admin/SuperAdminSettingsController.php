<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSuperAdminSettingsRequest;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Intervention\Image\Laravel\Facades\Image;

class SuperAdminSettingsController extends Controller
{
    public function edit()
    {
        $settings = Setting::query()->firstOrCreate([]);

        return Inertia::render('Admin/Settings/SuperAdmin', [
            'settings' => [
                'site_title' => $settings->site_title,
                'site_description' => $settings->site_description,
                'website_currency' => $settings->website_currency,
                'website_logo_url' => $settings->website_logo_path ? Storage::url($settings->website_logo_path) : null,
            ],
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

    public function update(UpdateSuperAdminSettingsRequest $request)
    {
        $settings = Setting::query()->firstOrCreate([]);

        $validated = $request->validated();

        if ($request->hasFile('website_logo')) {
            // Delete old website logo
            if ($settings->website_logo_path) {
                Storage::disk('public')->delete($settings->website_logo_path);
            }

            $image = $request->file('website_logo');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $path = 'website-logos/' . $filename;

            // Resize to a reasonable size for header usage
            $img = Image::read($image)
                ->resize(320, 320)
                ->encode();

            Storage::disk('public')->put($path, (string) $img);

            $validated['website_logo_path'] = $path;
        }

        $settings->fill($validated);
        $settings->save();

        return back()->with('success', 'Website settings updated.');
    }
}
