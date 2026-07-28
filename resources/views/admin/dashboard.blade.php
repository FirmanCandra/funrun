@extends('admin.layouts.admin')

@section('header_title', 'Dashboard Overview')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
    <!-- KPI 1 -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center">
        <div class="w-14 h-14 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mr-4">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        </div>
        <div>
            <p class="text-sm text-slate-500 font-medium mb-1">Total Participants</p>
            <h3 class="text-3xl font-bold text-slate-800">{{ $totalParticipants }}</h3>
        </div>
    </div>

    <!-- KPI 2 -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center">
        <div class="w-14 h-14 rounded-xl bg-green-50 text-green-600 flex items-center justify-center mr-4">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
            <p class="text-sm text-slate-500 font-medium mb-1">Total Revenue</p>
            <h3 class="text-3xl font-bold text-slate-800">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
        </div>
    </div>

    <!-- KPI 3 -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center">
        <div class="w-14 h-14 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center mr-4">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
        </div>
        <div>
            <p class="text-sm text-slate-500 font-medium mb-1">Tickets Sold</p>
            <h3 class="text-3xl font-bold text-slate-800">{{ $ticketsSold }}</h3>
        </div>
    </div>

    <!-- KPI 4 -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex items-center">
        <div class="w-14 h-14 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center mr-4">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <div>
            <p class="text-sm text-slate-500 font-medium mb-1">Checked-In</p>
            <h3 class="text-3xl font-bold text-slate-800">{{ $checkedIn }}</h3>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
    <h3 class="text-lg font-bold mb-4">Quick Actions</h3>
    <div class="flex gap-4">
        <a href="{{ route('admin.scanner') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-medium transition-colors flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
            Open Scanner
        </a>
        <a href="{{ route('admin.participants') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-6 py-3 rounded-xl font-medium transition-colors">
            View All Participants
        </a>
    </div>
</div>
@endsection
