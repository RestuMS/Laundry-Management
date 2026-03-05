@extends('layouts.dashboard')
@section('title', 'Log Aktivitas')
@section('header_title', 'Log Aktivitas')

@section('content')
<div class="space-y-6">

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        @php
            $statCards = [
                ['label' => 'Total Log', 'value' => $actionCounts['total'], 'icon' => 'ph-list-bullets', 'color' => 'blue'],
                ['label' => 'Login', 'value' => $actionCounts['login'], 'icon' => 'ph-sign-in', 'color' => 'green'],
                ['label' => 'Dibuat', 'value' => $actionCounts['created'], 'icon' => 'ph-plus-circle', 'color' => 'purple'],
                ['label' => 'Diubah', 'value' => $actionCounts['updated'], 'icon' => 'ph-pencil-simple', 'color' => 'orange'],
                ['label' => 'Dihapus', 'value' => $actionCounts['deleted'], 'icon' => 'ph-trash', 'color' => 'red'],
            ];
            $colorClasses = [
                'blue' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'icon' => 'text-blue-500'],
                'green' => ['bg' => 'bg-green-50', 'text' => 'text-green-600', 'icon' => 'text-green-500'],
                'purple' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-600', 'icon' => 'text-purple-500'],
                'orange' => ['bg' => 'bg-orange-50', 'text' => 'text-orange-600', 'icon' => 'text-orange-500'],
                'red' => ['bg' => 'bg-red-50', 'text' => 'text-red-600', 'icon' => 'text-red-500'],
            ];
        @endphp
        @foreach($statCards as $stat)
            <div class="glass-card p-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl {{ $colorClasses[$stat['color']]['bg'] }} flex items-center justify-center">
                    <i class="ph {{ $stat['icon'] }} text-xl {{ $colorClasses[$stat['color']]['icon'] }}"></i>
                </div>
                <div>
                    <p class="text-[20px] font-bold {{ $colorClasses[$stat['color']]['text'] }}">{{ number_format($stat['value']) }}</p>
                    <p class="text-[11px] font-semibold text-slate-400">{{ $stat['label'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Search & Filter -->
    <div class="glass-card p-5">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari aktivitas atau nama user..." class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 text-[13px] font-medium focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all">
            </div>
            <select name="filter" class="px-4 py-3 rounded-xl border border-slate-200 text-[13px] font-medium focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none bg-white min-w-[160px]">
                <option value="">Semua Aksi</option>
                <option value="login" {{ $filter == 'login' ? 'selected' : '' }}>Login</option>
                <option value="logout" {{ $filter == 'logout' ? 'selected' : '' }}>Logout</option>
                <option value="created" {{ $filter == 'created' ? 'selected' : '' }}>Dibuat</option>
                <option value="updated" {{ $filter == 'updated' ? 'selected' : '' }}>Diubah</option>
                <option value="deleted" {{ $filter == 'deleted' ? 'selected' : '' }}>Dihapus</option>
                <option value="payment" {{ $filter == 'payment' ? 'selected' : '' }}>Pembayaran</option>
            </select>
            <button type="submit" class="px-6 py-3 bg-primary text-white rounded-xl font-bold text-[13px] hover:bg-blue-700 transition-colors shadow-sm flex items-center gap-2">
                <i class="ph ph-funnel text-lg"></i> Filter
            </button>
        </form>
    </div>

    <!-- Activity Log Table -->
    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-[13px]">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100">
                        <th class="text-left px-5 py-4 font-bold text-slate-500 uppercase tracking-wider text-[11px]">Waktu</th>
                        <th class="text-left px-5 py-4 font-bold text-slate-500 uppercase tracking-wider text-[11px]">User</th>
                        <th class="text-left px-5 py-4 font-bold text-slate-500 uppercase tracking-wider text-[11px]">Aksi</th>
                        <th class="text-left px-5 py-4 font-bold text-slate-500 uppercase tracking-wider text-[11px]">Deskripsi</th>
                        <th class="text-left px-5 py-4 font-bold text-slate-500 uppercase tracking-wider text-[11px]">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($logs as $log)
                        @php
                            $actionColors = [
                                'login' => 'bg-green-100 text-green-700',
                                'logout' => 'bg-slate-100 text-slate-600',
                                'created' => 'bg-blue-100 text-blue-700',
                                'updated' => 'bg-orange-100 text-orange-700',
                                'deleted' => 'bg-red-100 text-red-700',
                                'payment' => 'bg-emerald-100 text-emerald-700',
                            ];
                            $actionClass = $actionColors[$log->action] ?? 'bg-slate-100 text-slate-600';
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="text-slate-700 font-semibold">{{ $log->created_at->format('d/m/Y') }}</div>
                                <div class="text-slate-400 text-[11px]">{{ $log->created_at->format('H:i:s') }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-bold text-slate-700">{{ $log->user_name }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold {{ $actionClass }}">
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 max-w-xs">
                                <span class="text-slate-600 line-clamp-2">{{ $log->description }}</span>
                            </td>
                            <td class="px-5 py-4 text-slate-400 font-mono text-[12px]">{{ $log->ip_address }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12">
                                <i class="ph ph-clipboard-text text-5xl text-slate-200 mb-3"></i>
                                <p class="text-slate-400 font-medium text-[14px]">Belum ada log aktivitas</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
