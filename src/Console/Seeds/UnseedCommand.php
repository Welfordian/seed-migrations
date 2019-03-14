<?php

namespace Welfordian\SeedMigrations\Console\Seeds;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Symfony\Component\Console\Input\InputOption;
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

        if ($this->option('class') !== null) {
            $log = SeedLog::query()->where('seeder', $this->option('class'))->get();
        }

        if (is_a($log, 'Illuminate\Database\Eloquent\Collection')) {
            $this->getOutput()->writeln("<info>Un-seeding:</info> {$this->option('class')}");

            $log->each(function ($log) {
                $this->undoCreation($log->changes['created']);
                $this->undoDeletion($log->changes['deleted']);
                $this->undoUpdation($log->changes['updated']);
            });

            SeedLog::query()->where('seeder', $this->option('class'))->delete();
        } else {
            $this->getOutput()->writeln("<info>Un-seeding:</info> {$log['class']}");

            $this->undoCreation($log->changes['created']);
            $this->undoDeletion($log->changes['deleted']);
            $this->undoUpdation($log->changes['updated']);

            SeedLog::query()->orderBy('batch', 'DESC')->limit(1)->delete();
        }

        $this->info('Database un-seeding completed successfully.');
    }

    public function undoCreation($created)
    {
        foreach($created as $create) {
            $model = app($create['class']);

            $dispatcher = $model::getEventDispatcher();

            $model::unsetEventDispatcher();

            $model->query()->where('id', $create['attributes']['id'])->delete();

            $model::setEventDispatcher($dispatcher);
        }
    }

    public function undoDeletion($deleted)
    {
        foreach($deleted as $delete) {
            $model = app($delete['class']);

            foreach($delete['attributes'] as $attribute => $value) {
                $model->{$attribute} = $value;
            }

            $dispatcher = $model::getEventDispatcher();

            $model::unsetEventDispatcher();

            $model->save();

            $model::setEventDispatcher($dispatcher);
        }
    }

    public function undoUpdation($updated)
    {
        // Todo: Revert updated changes
    }

    public function getOptions()
    {
        return [
            ['class', null, InputOption::VALUE_OPTIONAL, 'Seed class to unseed.', null],
        ];
    }
}
