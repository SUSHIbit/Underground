<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class SettingsController extends Controller
{
    /**
     * Display the user's settings page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();
        
        return view('settings.index', compact('user'));
    }

    /**
     * Update the user's theme preference.
     */
    public function updateTheme(Request $request)
    {
        $validated = $request->validate([
            'theme' => 'required|in:dark,rose,darker,custom',
        ]);
        
        $user = auth()->user();
        $user->theme_preference = $validated['theme'];
        $user->save();
        
        return back()->with('success', 'Theme updated successfully.');
    }
}