@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="w-full max-w-[380px] mx-auto bg-white rounded-3xl p-8 shadow-[0_20px_50px_rgba(20,94,179,0.3)]">
    
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
        <h2 class="text-2xl font-bold text-[#145eb3] mb-1">Welcome Back!</h2>
        <p class="text-[13px] text-gray-500">Login khusus untuk karyawan & admin</p>
    </div>

    @if (session('success'))
        <div class="bg-blue-50 border border-blue-200 text-[#145eb3] px-3 py-2 rounded-lg relative mb-4 font-medium text-[13px] text-center" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4" x-data="{ showPassword: false }">
        @csrf

        <!-- Email Field -->
        <div>
            <label for="email" class="block text-[13px] font-bold text-[#145eb3] mb-1">Email</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-blue-400">
                    <svg class="w-[18px] h-[18px]" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path></svg>
                </div>
                <input type="email" name="email" id="email" 
                    value="{{ old('email') }}" required autofocus
                    class="block w-full pl-9 pr-3 py-2.5 rounded border {{ $errors->has('email') ? 'border-red-400 focus:border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-[#4b96e6] focus:ring-[#4b96e6]/20' }} text-gray-800 text-[13px] focus:outline-none focus:ring-2 transition-all placeholder-gray-400/80" 
                    placeholder="Enter your email" />
            </div>
            @error('email')
                <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password Field -->
        <div>
            <label for="password" class="block text-[13px] font-bold text-[#145eb3] mb-1">Password</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-blue-400">
                    <svg class="w-[18px] h-[18px]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>
                </div>
                <input :type="showPassword ? 'text' : 'password'" name="password" id="password" required
                    class="block w-full pl-9 pr-9 py-2.5 rounded border {{ $errors->has('password') ? 'border-red-400 focus:border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-[#4b96e6] focus:ring-[#4b96e6]/20' }} text-gray-800 text-[13px] focus:outline-none focus:ring-2 transition-all placeholder-gray-400/80" 
                    placeholder="Enter your password" />
                <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-blue-400 hover:text-[#145eb3] focus:outline-none transition-colors">
                    <svg x-show="!showPassword" class="w-[18px] h-[18px]" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                    </svg>
                    <!-- Eye Slash (Hidden text) -->
                    <svg x-show="showPassword" class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Options -->
        <div class="flex items-center justify-between pt-1 pb-1">
            <div class="flex items-center">
                <input id="remember" name="remember" type="checkbox" class="h-3.5 w-3.5 bg-white border-gray-400 rounded text-[#145eb3] focus:ring-[#145eb3]">
                <label for="remember" class="ml-2 block text-[13px] text-[#145eb3] font-medium">
                    Remember Me
                </label>
            </div>
            <div class="text-[13px]">
                <a href="{{ route('password.request') }}" class="font-medium text-[#145eb3] hover:underline">Forgot Password?</a>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-1">
            <button type="submit" class="w-full flex justify-center py-2.5 px-4 rounded-xl text-[14.5px] font-bold text-white bg-gradient-to-b from-[#4b96e6] to-[#145eb3] shadow-[0_4px_12px_rgba(20,94,179,0.3)] hover:shadow-[0_6px_16px_rgba(20,94,179,0.4)] transform hover:-translate-y-0.5 transition-all focus:outline-none active:scale-[0.98]">
                Login
            </button>
        </div>
    </form>

    <!-- Footer Text -->
    <div class="mt-6 text-center border-t border-gray-100 pt-5">
        <a href="{{ route('landing') }}" class="inline-flex items-center gap-1.5 text-[13px] font-semibold text-[#145eb3] hover:underline mb-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Beranda
        </a>
        <p class="text-[12px] font-medium text-gray-400">
            &copy; {{ date('Y') }} LaundryPro. All rights reserved.
        </p>
    </div>
</div>
@endsection
