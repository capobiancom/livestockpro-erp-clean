<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Farm;
use Database\Seeders\MedicineGroupSeeder;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'farm_name' => 'required|string|max:255',
            'farm_location' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $farmCode = Str::upper(Str::substr($request->farm_name, 0, 3)) . Str::random(5);

        $farm = Farm::create([
            'name' => $request->farm_name,
            'code' => $farmCode,
            'address' => $request->farm_location,
            'user_id' => $user->id, // Associate the farm with the user
        ]);

        $user->assignRole('farm owner'); // Assign 'farm owner' role to the newly registered user
        $user->farm_id = $farm->id; // Associate the user with the newly created farm
        $user->save();

        // Seed medicine groups for the new farm
        $medicineGroupSeeder = new MedicineGroupSeeder();
        $medicineGroupSeeder->run($farm->id);

        event(new Registered($user));

        Auth::login($user);

        // Only show the demo seeding popup if demo data has not been seeded yet
        if (!$farm->demo_data_seeded) {
            return redirect(route('dashboard', ['show_demo_seeding_popup' => true, 'farm_id' => $farm->id], absolute: false));
        }

        return redirect(route('dashboard', absolute: false));
    }
}
