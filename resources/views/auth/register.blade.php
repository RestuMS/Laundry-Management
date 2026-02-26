@extends('layouts.auth')

@section('title', 'Register')

@section('content')
<div class="relative w-full max-w-[400px] mx-auto bg-white rounded-3xl p-8 shadow-[0_20px_50px_rgba(20,94,179,0.3)] my-4">

    <!-- Logo & branding -->
    <div class="flex items-center justify-center gap-2 mb-6">
        <svg class="w-8 h-8 text-[#145eb3]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect>
            <circle cx="12" cy="14" r="4"></circle>
            <line x1="8" y1="6" x2="16" y2="6"></line>
            <line x1="8" y1="10" x2="8.01" y2="10"></line>
            <!-- A tiny tie/towel icon sticking out -->
            <path d="M16 10l2 4l-1 2l-2-4"></path>
        </svg>
        <h1 class="text-xl font-bold text-[#145eb3]">Laundry<span class="font-normal text-[#145eb3]">Service</span></h1>
    </div>

    <!-- Header texts -->
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-[#145eb3] mb-1">Create an Account</h2>
        <p class="text-[13px] text-gray-500">Join us and start your laundry journey!</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4" x-data="{ showPassword: false, showConfirmPassword: false }">
        @csrf

        <!-- Full Name Field -->
        <div>
            <label for="name" class="block text-[13px] font-bold text-[#145eb3] mb-1 flex items-center gap-1.5">
                <svg class="w-[15px] h-[15px]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                Full Name
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-blue-400">
                    <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <input type="text" name="name" id="name" 
                    value="{{ old('name') }}" required autofocus
                    class="block w-full pl-9 pr-3 py-2.5 rounded border {{ $errors->has('name') ? 'border-red-400 focus:border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-[#4b96e6] focus:ring-[#4b96e6]/20' }} text-gray-800 text-[13px] focus:outline-none focus:ring-2 transition-all placeholder-gray-400/80" 
                    placeholder="Enter your full name" />
            </div>
            @error('name')
                <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email Field -->
        <div>
            <label for="email" class="block text-[13px] font-bold text-[#145eb3] mb-1 flex items-center gap-1.5">
                <svg class="w-[15px] h-[15px]" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path></svg>
                Email
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-blue-400">
                    <svg class="w-[18px] h-[18px]" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path></svg>
                </div>
                <input type="email" name="email" id="email" 
                    value="{{ old('email') }}" required
                    class="block w-full pl-9 pr-3 py-2.5 rounded border {{ $errors->has('email') ? 'border-red-400 focus:border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-[#4b96e6] focus:ring-[#4b96e6]/20' }} text-gray-800 text-[13px] focus:outline-none focus:ring-2 transition-all placeholder-gray-400/80" 
                    placeholder="Enter your email" />
            </div>
            @error('email')
                <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Role Dropdown Option -->
        <div class="hidden">
           <input type="hidden" name="role" value="kasir"> <!-- Or user can choose if enabled -->
        </div>

        <!-- Password Field -->
        <div>
            <label for="password" class="block text-[13px] font-bold text-[#145eb3] mb-1 flex items-center gap-1.5">
                <svg class="w-[15px] h-[15px]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                Password
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-blue-400">
                    <svg class="w-[18px] h-[18px]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                </div>
                <input :type="showPassword ? 'text' : 'password'" name="password" id="password" required
                    class="block w-full pl-9 pr-9 py-2.5 rounded border {{ $errors->has('password') ? 'border-red-400 focus:border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-[#4b96e6] focus:ring-[#4b96e6]/20' }} text-gray-800 text-[13px] focus:outline-none focus:ring-2 transition-all placeholder-gray-400/80" 
                    placeholder="Create a password" />
                <!-- Password visibility toggle removed to exactly replicate the register image (which has no toggle on register, only on login) -->
            </div>
            @error('password')
                <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password Field -->
        <div>
            <label for="password_confirmation" class="block text-[13px] font-bold text-[#145eb3] mb-1 flex items-center gap-1.5">
                <svg class="w-[15px] h-[15px]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                Confirm Password
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-blue-400">
                    <svg class="w-[18px] h-[18px]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                </div>
                <input :type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation" id="password_confirmation" required
                    class="block w-full pl-9 pr-9 py-2.5 rounded border border-gray-300 focus:border-[#4b96e6] focus:ring-[#4b96e6]/20 text-gray-800 text-[13px] focus:outline-none focus:ring-2 transition-all placeholder-gray-400/80" 
                    placeholder="Confirm your password" />
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-3">
            <button type="submit" class="w-full flex justify-center py-2.5 px-4 rounded-lg text-sm font-bold text-white bg-gradient-to-b from-[#4b96e6] to-[#145eb3] shadow-[0_4px_10px_rgba(20,94,179,0.4)] hover:shadow-[0_6px_15px_rgba(20,94,179,0.5)] transform hover:-translate-y-0.5 transition-all focus:outline-none active:scale-[0.98]">
                Sign Up
            </button>
        </div>
    </form>

    <!-- Footer Text -->
    <div class="mt-6 text-center border-t border-gray-100 pt-5">
        <p class="text-[12px] font-medium text-[#145eb3]">
            Already have an account? 
            <a href="{{ route('login') }}" class="font-bold underline">Login Here</a>
        </p>
    </div>
</div>
@endsection
