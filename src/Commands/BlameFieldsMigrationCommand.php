<?php

namespace Kamansoft\LaravelBlame\Commands;

use Illuminate\Database\Console\Migrations\BaseCommand;
use Illuminate\Support\Composer;
use Illuminate\Support\Facades\Schema;
use Kamansoft\LaravelBlame\Database\Migrations\BlameMigrationCreator;

class BlameFieldsMigrationCommand extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blame:make:migration
        {table : the table to add blaming fields.}
        {--path= : The location where the migration file should be created}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new migration that adds blaming fields.';

    protected $creator;

    /**
     * The Composer instance.
     *
     * @var \Illuminate\Support\Composer
     */
    protected $composer;

    public function __construct(BlameMigrationCreator $creator, Composer $composer)
    {
        parent::__construct();

        $this->creator = $creator;
        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! $this->checkIfTableExists($this->argument('table'))) {
            return self::FAILURE;
        }

        $migrationName = config('blame.migration_name_prefix').$this->argument('table').config('blame.migration_name_suffix');

        $this->writeMigration($migrationName, $this->argument('table'));

        return self::SUCCESS;
    }

    public function checkIfTableExists(string $tableName): bool
    {
        if (Schema::hasTable($tableName)) {
            $this->info($tableName.' table exists.');

            return true;
        }

        $this->error($tableName.' does NOT exist on db');

        return false;
    }

    public function checkIfTableExits(string $tableName): bool
    {
        return $this->checkIfTableExists($tableName);
    }

    /**
     * Get migration path (either specified by '--path' option or default location).
     *
     * @return string
     */
    protected function getMigrationPath()
    {
        if (! is_null($targetPath = $this->input->getOption('path'))) {
            return ! $this->usingRealPath()
                ? $this->laravel->basePath().'/'.$targetPath
                : $targetPath;
        }

        return parent::getMigrationPath();
    }

    /**
     * Write the migration file to disk.
     */
    protected function writeMigration($name, $table)
    {
        $file = $this->creator->create(
            $name, $this->getMigrationPath(), $table
        );

        $this->line("<info>Created Blame migration:</info> {$file}");
    }
}
