<?php

namespace Welfordian\SeedMigrations;

use Illuminate\Database\Eloquent\Model;

class SeedLog extends Model
{
    protected $table = 'seed_logs';

    protected $fillable = [
        'seeder',
        'changes',
        'batch'
    ];

    protected $casts = [
        'changes' => 'array'
    ];
}
