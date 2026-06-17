@extends('layouts.app')

@section('title', __('Plans'))

@section('content')
    <div class="text-center mb-10">
        <h1 class="text-3xl font-bold text-white">{{ __('Choose your plan') }}</h1>
        <p class="text-gray-400 mt-2">{{ __('No contracts. Cancel anytime.') }}</p>
    </div>

    @if($subscribed)
        <div class="text-center mb-8">
            <a href="{{ route('billing.portal') }}"
                class="glass-btn-secondary inline-block !px-6 !py-2 text-sm">
                {{ __('Manage subscription in Stripe') }}
            </a>
        </div>
    @endif

    <div class="grid md:grid-cols-3 gap-6 max-w-4xl mx-auto">
        @php
            $plans = [
                (object)[
                    'id' => 'free', 'name' => 'Free', 'price' => '$0', 'desc' => __('For exploring ReplyRadar'),
                    'features' => [__('1 project'), __('5 keywords'), __('50 opportunities/mo'), __('7-day history')],
                    'featured' => false,
                ],
                (object)[
                    'id' => 'pro', 'name' => 'Pro', 'price' => '24€', 'desc' => __('For creators and solopreneurs'),
                    'features' => [__('5 projects'), __('50 keywords'), __('Unlimited opportunities'), __('90-day history'), __('Export to CSV'), __('Opportunities with +80 match')],
                    'featured' => true,
                ],
                (object)[
                    'id' => 'business', 'name' => 'Business', 'price' => '99€', 'desc' => __('For agencies and teams'),
                    'features' => [__('Everything in Pro'), __('Unlimited projects'), __('Unlimited keywords'), __('Multi-source'), __('API access'), __('Priority support')],
                    'featured' => false,
                ],
            ];
        @endphp

        @foreach($plans as $plan)
            <div class="glass !p-8 {{ $plan->featured ? 'gradient-border' : '' }}" {{ $plan->featured ? "style=border-color:transparent" : "" }}>
                @if($plan->featured)
                    <div class="text-xs font-bold text-indigo-400 bg-indigo-500/10 rounded-full px-3 py-1 inline-block mb-3">{{ __('Most popular') }}</div>
                @endif
                <div class="text-lg font-bold text-white">{{ $plan->name }}</div>
                <div class="text-4xl font-extrabold text-white mt-2">
                    {{ $plan->price }}
                    <span class="text-base font-normal text-gray-400">{{ __('/mo') }}</span>
                </div>
                <p class="text-sm text-gray-400 mt-1 mb-5">{{ $plan->desc }}</p>

                <ul class="space-y-2 mb-6">
                    @foreach($plan->features as $f)
                        <li class="flex items-center gap-2 text-sm text-gray-300">
                            <span class="text-green-400 font-bold">✓</span> {{ $f }}
                        </li>
                    @endforeach
                </ul>

                @if($plan->id === 'free' || $plan->id === $currentPlan)
                    <form method="POST" action="{{ route('billing.checkout') }}">
                        @csrf
                        <input type="hidden" name="plan" value="{{ $plan->id }}">
                        <button type="submit" disabled
                            class="w-full py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                                {{ $plan->featured ? 'glass-btn-primary' : 'glass-btn-secondary' }}
                                opacity-60 cursor-default">
                            @if($plan->id === $currentPlan)
                                {{ __('Current plan') }}
                            @else
                                {{ __('Free plan') }}
                            @endif
                        </button>
                    </form>
                @elseif(!$stripeReady)
                    <button type="button" disabled
                        class="w-full py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                            {{ $plan->featured ? 'glass-btn-primary' : 'glass-btn-secondary' }}
                            opacity-60 cursor-default">
                        {{ __('Coming soon') }}
                    </button>
                @else
                    <form method="POST" action="{{ route('billing.checkout') }}">
                        @csrf
                        <input type="hidden" name="plan" value="{{ $plan->id }}">
                        <button type="submit"
                            class="w-full py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                                {{ $plan->featured ? 'glass-btn-primary' : 'glass-btn-secondary' }}">
                            {{ __('Switch to') }} {{ $plan->name }}
                        </button>
                    </form>
                @endif
            </div>
        @endforeach
    </div>
@endsection
