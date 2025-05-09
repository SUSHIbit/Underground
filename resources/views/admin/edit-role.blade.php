<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit User Role') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <a href="{{ route('admin.dashboard') }}" class="text-blue-500 hover:text-blue-700">
                            &larr; Back to Admin Dashboard
                        </a>
                    </div>
                    
                    <h3 class="text-lg font-medium mb-4">Edit Role for {{ $user->name }}</h3>
                    
                    <div class="flex flex-col md:flex-row">
                        <div class="md:w-1/3 mb-6 md:mb-0">
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <h4 class="font-medium mb-2">User Information</h4>
                                <p><span class="font-medium">ID:</span> {{ $user->id }}</p>
                                <p><span class="font-medium">Name:</span> {{ $user->name }}</p>
                                <p><span class="font-medium">Username:</span> {{ $user->username }}</p>
                                <p><span class="font-medium">Email:</span> {{ $user->email }}</p>
                                <p>
                                    <span class="font-medium">Current Role:</span>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $user->role === 'student' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $user->role === 'lecturer' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $user->role === 'accessor' ? 'bg-purple-100 text-purple-800' : '' }}
                                        {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </p>
                                @if($user->is_judge)
                                    <p class="mt-2">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                            Judge Capability
                                        </span>
                                    </p>
                                @endif
                            </div>
                        </div>
                        
                        <div class="md:w-2/3 md:pl-6">
                            <form action="{{ route('admin.update-role', $user) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                @if($errors->any())
                                    <div class="bg-red-50 text-red-700 p-4 mb-4 rounded-lg">
                                        <ul class="list-disc list-inside">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                
                                <div class="mb-4">
                                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                                    <select name="role" id="role" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="student" {{ $user->role === 'student' ? 'selected' : '' }}>Student</option>
                                        <option value="lecturer" {{ $user->role === 'lecturer' ? 'selected' : '' }}>Lecturer</option>
                                        <option value="accessor" {{ $user->role === 'accessor' ? 'selected' : '' }}>Accessor</option>
                                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                </div>
                                
                                <div class="mb-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="is_judge" id="is_judge" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" {{ $user->is_judge ? 'checked' : '' }}>
                                        <label for="is_judge" class="ml-2 block text-sm font-medium text-gray-700">Judge Capability</label>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">Users with judge capability can access the Judge Dashboard and grade tournament submissions.</p>
                                </div>
                                
                                <div class="mt-6">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:shadow-outline-blue transition ease-in-out duration-150">
                                        Update Role
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>