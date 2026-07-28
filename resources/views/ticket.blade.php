@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    @if(session('success'))
        <div class="bg-green-500/20 border border-green-500/50 text-green-400 px-6 py-4 rounded-xl mb-8 flex items-center gap-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="glass-panel rounded-3xl overflow-hidden shadow-2xl relative">
        <div class="absolute top-0 left-0 w-full h-3 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500"></div>
        
        <div class="p-8 pb-0 text-center border-b border-dashed border-slate-700/50 relative">
            <!-- Cutout holes for ticket effect -->
            <div class="absolute -left-4 bottom-[-16px] w-8 h-8 rounded-full bg-[#0f172a]"></div>
            <div class="absolute -right-4 bottom-[-16px] w-8 h-8 rounded-full bg-[#0f172a]"></div>

            <h2 class="text-3xl font-extrabold tracking-tight mb-2">{{ $ticket->participant->event->title ?? 'SeTiket' }}</h2>
            <p class="text-gray-400 mb-6">{{ \Carbon\Carbon::parse($ticket->participant->event->date ?? '2026-09-15')->format('F d, Y') }} | {{ $ticket->participant->event->location ?? 'City Square' }}</p>
            
            <div class="inline-block bg-white p-4 rounded-2xl mb-8 shadow-xl">
                @if($ticket->status === 'valid' || $ticket->status === 'checked-in')
                    <!-- Using a mock QR generation API based on the QR code string -->
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($ticket->qr_code) }}" alt="QR Code" class="w-48 h-48">
                @else
                    <div class="w-48 h-48 flex items-center justify-center bg-slate-100 border-2 border-dashed border-slate-300 rounded-xl">
                        <span class="text-slate-400 font-medium text-sm text-center px-4">QR Code will appear after payment verification</span>
                    </div>
                @endif
            </div>
            <p class="text-xs text-gray-500 tracking-widest font-mono mb-8">{{ $ticket->ticket_code }}</p>
        </div>
        
        <div class="p-8 bg-slate-800/20">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Participant Name</p>
                    <p class="font-bold text-lg">{{ $ticket->participant->fullname }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Category</p>
                    <p class="font-bold text-lg text-blue-400">{{ $ticket->participant->category }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Jersey Size</p>
                    <p class="font-bold text-lg">{{ $ticket->participant->jersey_size }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Status</p>
                    <div class="inline-flex items-center gap-1.5 bg-green-500/20 text-green-400 px-3 py-1 rounded-full text-sm font-semibold border border-green-500/30">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        {{ strtoupper($ticket->status) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 bg-blue-600/10 border-t border-slate-700/50 text-center text-sm text-gray-400">
            Please present this QR code at the event registration desk to claim your Race Pack and BIB number.
        </div>
    </div>
    
    <div class="text-center mt-8">
        <button onclick="window.print()" class="glass-panel px-6 py-3 rounded-full hover:bg-white/10 transition-colors flex items-center gap-2 mx-auto">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Download PDF / Print
        </button>
    </div>
</div>

<style>
    @media print {
        body { background: white; color: black; }
        .glass-panel { background: white !important; border: 1px solid #ccc; color: black; }
        nav, footer, button { display: none !important; }
        .text-gray-400, .text-gray-500 { color: #666 !important; }
        .text-white { color: black !important; }
    }
</style>
@endsection
