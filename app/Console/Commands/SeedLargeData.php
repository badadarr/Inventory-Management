<?php

namespace App\Console\Commands;

use Database\Seeders\LargeDataSeeder;
use Illuminate\Console\Command;

class SeedLargeData extends Command
{
    protected $signature = 'db:seed-large {--fresh : Fresh migrate before seeding}';
    protected $description = 'Seed 1000+ data untuk testing konsistensi sistem';

    public function handle()
    {
        if ($this->option('fresh')) {
            $this->warn('⚠️  Menjalankan fresh migration...');
            $this->call('migrate:fresh', ['--seed' => true]);
        }

        $this->info('🚀 Memulai seeding data besar...');
        
        $startTime = microtime(true);
        
        $this->call('db:seed', ['--class' => LargeDataSeeder::class]);
        
        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 2);
        
        $this->newLine();
        $this->info("✅ Seeding selesai dalam {$executionTime} detik");
        
        return Command::SUCCESS;
    }
}
