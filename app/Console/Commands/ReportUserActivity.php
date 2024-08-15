<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use League\Csv\Writer;
use SplTempFileObject;

class ReportUserActivity extends Command
{
    protected $signature = 'report:user-activity {userId} {--file=}';

    protected $description = 'Сформировать отчет об активности пользователей за последние 30 дней';

    public function handle()
    {
        $userId = $this->argument('userId');
        $filePath = $this->option('file');

        $activities = DB::table('user_activities')
            ->where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(30))
            ->get();

        if ($filePath) {
            $csv = Writer::createFromPath($filePath, 'w+');
            $csv->insertOne(['Route', 'Method', 'Status', 'Duration', 'Created At']);

            foreach ($activities as $activity) {
                $csv->insertOne([
                    $activity->route,
                    $activity->method,
                    $activity->status,
                    $activity->duration,
                    $activity->created_at,
                ]);
            }

            $this->info("Отчет сохранен в {$filePath}");
        } else {
            $this->table(
                ['Route', 'Method', 'Status', 'Duration', 'Created At'],
                $activities->map(function ($activity) {
                    return [
                        $activity->route,
                        $activity->method,
                        $activity->status,
                        $activity->duration,
                        $activity->created_at,
                    ];
                })->toArray()
            );
        }
    }
}

