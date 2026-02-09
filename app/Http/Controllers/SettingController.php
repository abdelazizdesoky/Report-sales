<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        if (auth()->user()->cannot('view settings')) {
            abort(403);
        }
        $setting = Setting::firstOrCreate([], [
            'company_name' => '  Alarabia Group',
            'activity' => 'alarabia group',
        ]);

        return view('settings.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        if (auth()->user()->cannot('edit settings')) {
            abort(403);
        }
        $request->validate([
            'company_name' => 'required|string|max:255',
            'activity' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);

        $setting = Setting::first();
        $setting->update($request->only('company_name', 'activity', 'address', 'phone', 'description'));

        return redirect()->route('settings.edit')->with('status', 'settings-updated');
    }
}
