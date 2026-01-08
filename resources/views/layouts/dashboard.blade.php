@extends('layouts.app')

@section('title', 'Dashboard - TFMS')

@section('content')
<div class="surface-card p-6 lg:p-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <span class="pill">Dashboard</span>
            <h1 class="text-3xl font-bold text-utm-maroon mt-2">Welcome back, {{ Auth::user()->name }}</h1>
            <p class="muted-label text-sm mt-1">{{ Auth::user()->role->name ?? 'User' }} • {{ Auth::user()->email }}</p>
        </div>
        <div class="rounded-xl bg-utm-sand/80 border border-utm-gold/30 px-4 py-3 text-sm text-utm-maroon shadow-inner">
            <p class="font-semibold">Last login</p>
            <p class="muted-label">
                {{ Auth::user()->last_login_at?->format('M d, Y H:i') ?? 'First login' }}
            </p>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="rounded-xl bg-white/90 border border-utm-maroon/10 shadow-sm p-4">
            <p class="muted-label">Account Active</p>
            <p class="mt-1 text-lg font-semibold @if(Auth::user()->is_active) text-green-600 @else text-red-600 @endif">
                @if(Auth::user()->is_active) Active @else Inactive @endif
            </p>
        </div>
        <div class="rounded-xl bg-white/90 border border-utm-maroon/10 shadow-sm p-4">
            <p class="muted-label">Failed Login Attempts</p>
            <p class="mt-1 text-lg font-semibold">{{ Auth::user()->failed_login_attempts }}/3</p>
        </div>
        <div class="rounded-xl bg-white/90 border border-utm-maroon/10 shadow-sm p-4">
            <p class="muted-label">Account Locked</p>
            <p class="mt-1 text-lg font-semibold @if(Auth::user()->isLocked()) text-red-600 @else text-green-600 @endif">
                @if(Auth::user()->isLocked()) Locked @else Not Locked @endif
            </p>
        </div>
    </div>
</div>
@endsection
