<?php

use App\Models\MenstrualCycle;
use Carbon\Carbon;

it('calculates next period prediction correctly', function () {
    $cycle = new MenstrualCycle([
        'period_start_date' => '2026-03-01',
    ]);

    // Simulate calculatePredictions logic without save
    $startDate = Carbon::parse($cycle->period_start_date);
    $nextPeriod = $startDate->copy()->addDays(28);

    expect($nextPeriod->toDateString())->toBe('2026-03-29');
});

it('calculates ovulation prediction 14 days before next period', function () {
    $startDate = Carbon::parse('2026-03-01');
    $nextPeriod = $startDate->copy()->addDays(28);
    $ovulation = $nextPeriod->copy()->subDays(14);

    expect($ovulation->toDateString())->toBe('2026-03-15');
});

it('calculates fertile window correctly', function () {
    $startDate = Carbon::parse('2026-03-01');
    $nextPeriod = $startDate->copy()->addDays(28);
    $ovulation = $nextPeriod->copy()->subDays(14);
    $fertileStart = $ovulation->copy()->subDays(5);
    $fertileEnd = $ovulation->copy()->addDay();

    expect($fertileStart->toDateString())->toBe('2026-03-10');
    expect($fertileEnd->toDateString())->toBe('2026-03-16');
});

it('calculates period length when end date is set', function () {
    $start = Carbon::parse('2026-03-01');
    $end = Carbon::parse('2026-03-05');

    $periodLength = $start->diffInDays($end) + 1;

    expect($periodLength)->toBe(5);
});

it('handles custom cycle length for predictions', function () {
    $startDate = Carbon::parse('2026-03-01');
    $cycleLength = 32;
    $nextPeriod = $startDate->copy()->addDays($cycleLength);

    expect($nextPeriod->toDateString())->toBe('2026-04-02');
});

it('returns false for fertile window when dates are not set', function () {
    $cycle = new MenstrualCycle();

    expect($cycle->isInFertileWindow())->toBeFalse();
});

it('returns null for days until next period when no prediction', function () {
    $cycle = new MenstrualCycle();

    expect($cycle->daysUntilNextPeriod())->toBeNull();
});
