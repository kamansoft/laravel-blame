<?php

namespace Kamansoft\LaravelBlame\Commands;

use Illuminate\Database\Console\Migrations\BaseCommand;
use Illuminate\Support\Composer;
use Illuminate\Support\Facades\Schema;
use Kamansoft\LaravelBlame\Database\Migrations\BlameMigrationCreator;

class BlameFieldsMigrationCommand extends BaseCommand
{
    public static string $system_user_id_const_name = 'BLAME_SYSTEM_USER_ID';

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
    protected $description = 'Creates a new migration that add blaming ';

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
     *
     * @return int
     */
    public function handle(): int
    {
        if (! $this->checkIfTableExits($this->argument('table'))) {
            return self::FAILURE;
        }

        $migration_name = config('blame.migration_name_prefix').$this->argument('table').config('blame.migration_name_suffix');

        $this->writeMigration($migration_name, $this->argument('table'));

        return self::SUCCESS;
    }

    public function checkIfTableExits(string $table_name): bool
    {
        if (Schema::hasTable($table_name)) {
            $this->info($table_name.' table exits..');

            return true;
        }

        $this->error($table_name.' does NOT exits on db');

        return false;
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
     *
     * @param $name
     * @param $table
     */
    protected function writeMigration($name, $table)
    {
        $file = $this->creator->create(
            $name, $this->getMigrationPath(), $table
        );

        $this->line("<info>Created Blame migration:</info> {$file}");
    }
}
