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
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $setting = Setting::first();
        $data = $request->only('company_name', 'activity', 'address', 'phone', 'description');

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($setting->logo_path && \Storage::disk('public')->exists($setting->logo_path)) {
                \Storage::disk('public')->delete($setting->logo_path);
            }
            
            $path = $request->file('logo')->store('logos', 'public');
            $data['logo_path'] = $path;
        }

        $setting->update($data);

        return redirect()->route('settings.edit')->with('status', 'settings-updated');
    }
}
