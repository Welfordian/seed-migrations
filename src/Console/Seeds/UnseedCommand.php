<?php

namespace Welfordian\SeedMigrations\Console\Seeds;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Welfordian\SeedMigrations\SeedLog;

class UnseedCommand extends Command
{
    use ConfirmableTrait;

    protected $name = "db:unseed";

    protected $description = "Undo last seed";

    public function handle()
    {
        if (! $this->confirmToProceed()) {
            return;
        }

        $log = SeedLog::query()->orderBy('batch', 'DESC')->first();

        $this->getOutput()->writeln("<info>Un-seeding:</info> {$log['seeder']}");

        $this->undoCreation($log->changes['created']);
        $this->undoDeletion($log->changes['deleted']);
        $this->undoUpdation($log->changes['updated']);

        SeedLog::query()->orderBy('batch', 'DESC')->limit('1')->delete();

        $this->info('Database un-seeding completed successfully.');
    }

    public function undoCreation($created)
    {
        foreach($created as $create) {
            $model = app($create['class']);

            $model = $model->query()->where('id', $create['attributes']['id'])->delete();
        }
    }

    public function undoDeletion($deleted)
    {
        foreach($deleted as $delete) {
            $model = app($delete['class']);

            foreach($delete['attributes'] as $attribute => $value) {
                $model->{$attribute} = $value;
            }

            $model->save();
        }
    }

    public function undoUpdation($updated)
    {
        // Todo: Revert updated changes
    }
}
