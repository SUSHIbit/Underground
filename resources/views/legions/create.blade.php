<!-- resources/views/legions/create.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Legion') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <a href="{{ route('legions.index') }}" class="text-blue-500 hover:text-blue-700">
                            &larr; Back to Legions
                        </a>
                    </div>
                    
                    <h3 class="text-lg font-medium mb-4">Create New Legion</h3>
                    
                    <form action="{{ route('legions.store') }}" method="POST" enctype="multipart/form-data" class="max-w-xl">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Legion Name</label>
                            <input type="text" name="name" id="name" 
                                   class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm block w-full"
                                   placeholder="Enter a unique legion name"
                                   value="{{ old('name') }}"
                                   required>
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                            <textarea name="description" id="description" rows="3"
                                     class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm block w-full"
                                     placeholder="Describe your legion">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-6">
                            <label for="emblem" class="block text-sm font-medium text-gray-700 mb-1">Legion Emblem (Optional)</label>
                            <input type="file" name="emblem" id="emblem" 
                                   class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm block w-full"
                                   accept="image/*">
                            <p class="text-xs text-gray-500 mt-1">Upload an image for your legion's emblem (max 2MB)</p>
                            @error('emblem')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:shadow-outline-blue transition ease-in-out duration-150">
                                Create Legion
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>