<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Workload Thresholds
    |--------------------------------------------------------------------------
    |
    | These values determine when a developer is flagged as "overloaded".
    | A developer is overloaded if they have more than `max_open_tasks` open
    | tasks OR more than `critical_percentage` of their tasks are high/critical.
    |
    */

    'overload_threshold' => [
        'max_open_tasks' => 10,
        'critical_percentage' => 50,
    ],

    /*
    |--------------------------------------------------------------------------
    | Aging Buckets (in days)
    |--------------------------------------------------------------------------
    |
    | Defines the age ranges for grouping open tasks by age.
    | Each bucket is [min_days, max_days, label, color].
    |
    */

    'aging_buckets' => [
        ['min' => 0,  'max' => 2,    'label' => '0–2 days',  'color' => 'emerald'],
        ['min' => 3,  'max' => 7,    'label' => '3–7 days',  'color' => 'yellow'],
        ['min' => 8,  'max' => 14,   'label' => '8–14 days', 'color' => 'orange'],
        ['min' => 15, 'max' => 9999, 'label' => '15+ days',  'color' => 'red'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Stuck Task Threshold
    |--------------------------------------------------------------------------
    |
    | Tasks in "In Progress" status longer than this many days are flagged
    | as stuck / bottleneck.
    |
    */

    'stuck_days_threshold' => 5,

    /*
    |--------------------------------------------------------------------------
    | SLA Hours
    |--------------------------------------------------------------------------
    |
    | Default SLA target in hours by priority. Used for SLA compliance reports.
    |
    */

    'sla_hours' => [
        'critical' => 24,
        'high'     => 72,
        'medium'   => 168,  // 7 days
        'low'      => 336,  // 14 days
    ],

    /*
    |--------------------------------------------------------------------------
    | Overdue Alert Threshold
    |--------------------------------------------------------------------------
    |
    | When the number of overdue tasks exceeds this count, admin alerts fire.
    |
    */

    'overdue_alert_threshold' => 5,

    /*
    |--------------------------------------------------------------------------
    | Due Soon Windows
    |--------------------------------------------------------------------------
    |
    | Time windows (in hours) for "due soon" task grouping.
    |
    */

    'due_soon_windows' => [
        '24h' => 24,
        '3d'  => 72,
        '1w'  => 168,
    ],

];
