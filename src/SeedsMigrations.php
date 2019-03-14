<?php

namespace Welfordian\SeedMigrations;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Event;

class SeedsMigrations extends Seeder
{
    public function __construct(SeedLogger $logger)
    {
        $this->logger = $logger;
    }

    public function run(SeedLogger $logger)
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

        $this->up();
    }

    public function up()
    {

    }

    public function down()
    {
        $log = SeedLog::where('seeder', get_class($this))->orderBy('batch', 'DESC')->first();

        dd($log);
    }

    public function undo()
    {

    }

    public function __destruct()
    {
        $this->logger->commit(get_class($this));
    }
}
