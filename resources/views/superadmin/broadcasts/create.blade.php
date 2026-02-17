@extends('layouts.superadmin')

@section('title', 'Global Broadcast')
@section('page_title', 'Platform Communication')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-slate-800 rounded-2xl p-8 border border-white/5 relative overflow-hidden shadow-2xl">
        <!-- Decoration -->
        <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-blue-500/10 rounded-full blur-3xl"></div>
        
        <div class="relative">
            <div class="flex items-center space-x-4 mb-8">
                <div class="w-12 h-12 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-600/20">
                    <i class="bi bi-megaphone-fill text-xl"></i>
                </div>
                <div>
                    <h4 class="text-white font-black uppercase tracking-tighter">System-Wide Announcement</h4>
                    <p class="text-xs text-slate-500">Your message will appear in the dashboard of every active business owner.</p>
                </div>
            </div>

            <form action="{{ route('saas.broadcast.send') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3">Transmission Header (Subject)</label>
                    <input type="text" name="title" value="{{ old('title') }}" 
                        placeholder="e.g. Scheduled Maintenance Window"
                        class="w-full bg-slate-900 border border-white/10 rounded-xl px-4 py-4 text-white focus:outline-none focus:border-blue-500 transition-all font-semibold" required>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-3">Payload Content (Message)</label>
                    <textarea name="message" rows="6" 
                        placeholder="Detailed instructions or update notes for your tenants..."
                        class="w-full bg-slate-900 border border-white/10 rounded-xl px-4 py-4 text-white focus:outline-none focus:border-blue-500 transition-all font-medium" required>{{ old('message') }}</textarea>
                </div>

                <div class="bg-blue-500/5 border border-blue-500/10 rounded-xl p-4 flex items-start space-x-3 mb-6">
                    <i class="bi bi-info-circle text-blue-400 mt-0.5"></i>
                    <p class="text-[10px] text-blue-400 font-bold leading-relaxed">
                        TRANSMISSION ADVISORY: This action cannot be revoked once sent. It will trigger both dashboard alerts and email notifications (if configured).
                    </p>
                </div>

                <div class="flex items-center justify-end space-x-4 pt-4 border-t border-white/5">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-black py-4 px-8 rounded-xl transition-all shadow-xl shadow-blue-600/30 uppercase text-xs tracking-widest flex items-center justify-center">
                        Initiate Global Broadcast <i class="bi bi-send-fill ml-3"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
