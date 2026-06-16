<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

#[Signature('app:import-sql {file=presensismk.sql : Nama file SQL di folder database} {--force : Paksa jalankan proses}')]
#[Description('Jalankan migrate:fresh, import SQL, dan db:seed sekaligus')]
class ImportSql extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        
        $fileName = $this->argument('file');
        $filePath = database_path($fileName);

        // 1. Cek apakah file ada
        if (!File::exists($filePath)) {
            $this->error("File '{$fileName}' tidak ditemukan di: {$filePath}");
            return Command::FAILURE;
        }

        $this->info("Memulai proses: Migrate Fresh -> Import SQL -> Seed");

        // 2. Migrate:Fresh
        $this->info("Menjalankan migrate:fresh...");
        Artisan::call('migrate:fresh'['--force']);
        $this->line("Migrasi selesai.");

        // 3. Import SQL
        $this->info("Mengimport file: {$fileName}...");
        
        try {
            $sql = File::get($filePath);
            $cleanSql = trim($sql); 

            if (empty($cleanSql)) {
                $this->warn("File SQL kosong atau hanya berisi spasi, langkah import dilewati.");
            } else {
                DB::unprepared($cleanSql);
                $this->line("Import SQL berhasil.");
            }
        } catch (\Exception $e) {
            $this->error("Gagal import SQL: " . $e->getMessage());
            return Command::FAILURE;
        }

        // 4. DB:Seed
        $this->info("Menjalankan db:seed...");
        Artisan::call('db:seed',['--force']);
        $this->line("Seeding selesai.");

        $this->info("Semua proses berhasil dilakukan!");
        return Command::SUCCESS;
    }
}