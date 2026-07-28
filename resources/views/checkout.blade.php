@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="glass-panel rounded-3xl p-8 shadow-2xl relative">
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-500 to-purple-600"></div>
        
        <h2 class="text-3xl font-bold mb-8 text-center">Complete Your Registration</h2>
        
        <div class="bg-slate-800/50 rounded-2xl p-6 mb-8 border border-slate-700">
            <h3 class="text-xl font-semibold mb-4 border-b border-slate-700 pb-2">Order Summary</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-400">Name</span>
                    <span class="font-medium">{{ $participant->fullname }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Category</span>
                    <span class="font-medium text-blue-400">{{ $participant->category }} Run</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Ticket Code</span>
                    <span class="font-mono bg-slate-900 px-2 rounded">{{ $ticket->ticket_code }}</span>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-700 flex justify-between items-center">
                    <span class="text-lg text-gray-300">Total Amount</span>
                    <span class="text-3xl font-bold text-white">Rp {{ number_format($price, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <form action="{{ route('payment.process', $ticket->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <h3 class="text-lg font-medium mb-4">Select Payment Method</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <label class="relative cursor-pointer">
                    <input type="radio" name="payment_method" value="qris" class="peer sr-only" checked>
                    <div class="p-4 rounded-xl border border-slate-600 bg-slate-800/30 peer-checked:border-blue-500 peer-checked:bg-blue-500/10 transition-all text-center">
                        <div class="font-bold mb-1">QRIS</div>
                        <div class="text-xs text-gray-400">Instant Verification</div>
                    </div>
                </label>
                <label class="relative cursor-pointer">
                    <input type="radio" name="payment_method" value="bank_transfer" class="peer sr-only">
                    <div class="p-4 rounded-xl border border-slate-600 bg-slate-800/30 peer-checked:border-blue-500 peer-checked:bg-blue-500/10 transition-all text-center">
                        <div class="font-bold mb-1">VA BCA</div>
                        <div class="text-xs text-gray-400">80771234567890</div>
                    </div>
                </label>
                <label class="relative cursor-pointer">
                    <input type="radio" name="payment_method" value="ewallet" class="peer sr-only">
                    <div class="p-4 rounded-xl border border-slate-600 bg-slate-800/30 peer-checked:border-blue-500 peer-checked:bg-blue-500/10 transition-all text-center">
                        <div class="font-bold mb-1">DANA</div>
                        <div class="text-xs text-gray-400">081234567890</div>
                    </div>
                </label>
            </div>

            <div class="mb-8">
                <label class="block text-sm font-medium text-gray-300 mb-2">Upload Proof of Payment</label>
                <input type="file" name="proof" accept="image/*" required class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 bg-slate-800/50 rounded-xl border border-slate-700 p-2 cursor-pointer transition-all">
                @error('proof')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 rounded-xl shadow-lg transition-all text-lg flex justify-center items-center gap-2">
                Submit Payment Confirmation
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </form>
    </div>
</div>
@endsection
