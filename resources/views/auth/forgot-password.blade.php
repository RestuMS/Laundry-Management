@extends('layouts.auth')

@section('title', 'Lupa Password')

@section('content')
<div class="w-full max-w-[380px] mx-auto bg-white rounded-3xl p-8 shadow-[0_20px_50px_rgba(20,94,179,0.3)]">
    
    <!-- Logo & branding -->
    <div class="flex items-center justify-center gap-2 mb-6">
        <svg class="w-8 h-8 text-[#145eb3]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect>
            <circle cx="12" cy="14" r="4"></circle>
            <line x1="8" y1="6" x2="16" y2="6"></line>
            <line x1="8" y1="10" x2="8.01" y2="10"></line>
            <path d="M16 10l2 4l-1 2l-2-4"></path>
        </svg>
        <h1 class="text-xl font-bold text-[#145eb3]">Laundry<span class="font-normal text-[#145eb3]">Service</span></h1>
    </div>

    <!-- Header -->
    <div class="text-center mb-6">
        <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-[#145eb3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-[#145eb3] mb-1">Lupa Password?</h2>
        <p class="text-[13px] text-gray-500 leading-relaxed">Masukkan email akun Anda dan kami akan mengirim link untuk mereset password.</p>
    </div>

    <!-- Status Message -->
    @if (session('status'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl relative mb-4 font-medium text-[13px] text-center flex items-center gap-2 justify-center" role="alert">
            <svg class="w-5 h-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- Email Field -->
        <div>
            <label for="email" class="block text-[13px] font-bold text-[#145eb3] mb-1">Email Akun</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-blue-400">
                    <svg class="w-[18px] h-[18px]" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path></svg>
                </div>
                <input type="email" name="email" id="email" 
                    value="{{ old('email') }}" required autofocus
                    class="block w-full pl-9 pr-3 py-2.5 rounded border {{ $errors->has('email') ? 'border-red-400 focus:border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-[#4b96e6] focus:ring-[#4b96e6]/20' }} text-gray-800 text-[13px] focus:outline-none focus:ring-2 transition-all placeholder-gray-400/80" 
                    placeholder="Masukkan email Anda" />
            </div>
            @error('email')
                <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="pt-1">
            <button type="submit" class="w-full flex justify-center py-2.5 px-4 rounded-xl text-[14.5px] font-bold text-white bg-gradient-to-b from-[#4b96e6] to-[#145eb3] shadow-[0_4px_12px_rgba(20,94,179,0.3)] hover:shadow-[0_6px_16px_rgba(20,94,179,0.4)] transform hover:-translate-y-0.5 transition-all focus:outline-none active:scale-[0.98]">
                Kirim Link Reset Password
            </button>
        </div>
    </form>

    <!-- Back to Login -->
    <div class="mt-6 text-center border-t border-gray-100 pt-5">
        <a href="{{ route('login') }}" class="text-[13px] font-bold text-[#145eb3] hover:underline inline-flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Login
        </a>
    </div>
</div>
@endsection
