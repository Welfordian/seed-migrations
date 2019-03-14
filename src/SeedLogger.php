<?php

namespace Welfordian\SeedMigrations;

class SeedLogger
{
    protected $logs = [
        'created' => [],
        'updated' => [],
        'deleted' => []
    ];

    public function created($model)
    {
        $this->logs['created'][] = ['class' => get_class($model), 'attributes' => $model->getAttributes()];
    }

    public function updated($model)
    {
        $this->logs['updated'][] = ['class' => get_class($model), 'attributes' => $model->getAttributes()];
    }

    public function deleted($model)
    {
        $this->logs['deleted'][] = ['class' => get_class($model), 'attributes' => $model->getAttributes()];
    }

    public function commit($seeder)
    {
        $lastLog = SeedLog::query()->orderBy('batch', 'DESC')->first();

        if ($lastLog === null) {
            $batch = 1;
        } else {
            $batch = $lastLog->batch + 1;
        }

        $log = new SeedLog([
            'seeder' => $seeder,
            'changes' => $this->logs,
            'batch' => $batch
        ]);

        $log->save();
    }
}
