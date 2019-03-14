<?php

namespace Welfordian\SeedMigrations;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;

class SeedsMigrations extends Seeder
{
    protected $down = false;

    public function __construct(SeedLogger $logger)
    {
        $this->logger = $logger;
    }

    public function run()
    {
        Event::listen(['eloquent.created: *'], function($event, $model) {
            $this->logger->created($model[0]);
        });

        Event::listen(['eloquent.updated: *'], function($event, $model) {
            $this->logger->updated($model[0]);
        });

        Event::listen(['eloquent.deleted: *'], function ($event, $model) {
            $this->logger->deleted($model[0]);
        });

        $this->handle();
    }

    public function down()
    {
        $this->down = true;

        $reflection = new \ReflectionClass($this);

        Artisan::call('db:unseed', ['--class' => $reflection->name]);
    }

    public function __destruct()
    {
        if ($this->down) {
            return;
        }

        $reflection = new \ReflectionClass($this);

        $this->logger->commit($reflection->name);
    }
}
