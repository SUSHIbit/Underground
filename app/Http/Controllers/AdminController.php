<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard with users list.
     */
    public function index()
    {
        // Get students limited to 50
        $students = User::where('role', 'student')
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get();
        
        // Get all lecturers
        $lecturers = User::where('role', 'lecturer')
                    ->orderBy('created_at', 'desc')
                    ->get();
        
        // Get all accessors
        $accessors = User::where('role', 'accessor')
                    ->orderBy('created_at', 'desc')
                    ->get();
                    
        // Get all judges (users with is_judge flag)
        $judges = User::where('is_judge', true)
                    ->orderBy('created_at', 'desc')
                    ->get();
        
        return view('admin.dashboard', compact('students', 'lecturers', 'accessors', 'judges'));
    }
    
    /**
     * Show the form for editing the user role.
     */
    public function editRole(User $user)
    {
        return view('admin.edit-role', compact('user'));
    }
    
    public function updateRole(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|in:student,lecturer,accessor,admin',
            'is_judge' => 'required|in:0,1', // Accept 0 or 1 as valid values
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $user->role = $request->role;
        $user->is_judge = (bool)$request->is_judge; // Convert to boolean
        $user->save();
        
        return redirect()->route('admin.dashboard')->with('success', 'User role updated successfully.');
    }
}