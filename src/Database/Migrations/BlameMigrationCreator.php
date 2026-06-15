<?php

namespace Kamansoft\LaravelBlame\Database\Migrations;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class BlameMigrationCreator
{
    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * The custom app stubs directory.
     *
     * @var string
     */
    protected $customStubPath;

    /**
     * The registered post create hooks.
     *
     * @var array
     */
    protected $postCreate = [];

    /**
     * Create a new migration creator instance.
     *
     * @param  string  $customStubPath
     * @return void
     */
    public function __construct(Filesystem $files, $customStubPath)
    {
        $this->files = $files;
        $this->customStubPath = $customStubPath;
    }

    /**
     * @param  null  $table
     * @return string
     */
    public function create($name, $path, $table)
    {
        $this->ensureMigrationDoesntAlreadyExist($name, $path);

        $stub = $this->getStub();

        $path = $this->getPath($name, $path);

        $this->files->ensureDirectoryExists(dirname($path));

        $this->files->put(
            $path, $this->populateStub($name, $stub, $table)
        );

        $this->firePostCreateHooks($table);

        return $path;
    }

    /**
     * Ensure that a migration with the given name doesn't already exist.
     *
     * @param  string  $name
     * @param  string  $migrationPath
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    protected function ensureMigrationDoesntAlreadyExist($name, $migrationPath = null)
    {
        if (! empty($migrationPath)) {
            $migrationFiles = $this->files->glob($migrationPath.'/*.php');

            foreach ($migrationFiles as $migrationFile) {
                $this->files->requireOnce($migrationFile);
            }
        }

        if (class_exists($className = $this->getClassName($name))) {
            throw new \InvalidArgumentException("A {$className} class already exists.");
        }
    }

    /**
     * Get the migration stub file.
     *
     * @return string
     */
    protected function getStub()
    {
        $stub = $this->files->exists($customPath = $this->customStubPath.'/migration.add.blaming.int.fields.stub')
            ? $customPath
            : $this->stubPath().'/migration.add.blaming.int.fields.stub';

        return $this->files->get($stub);
    }

    /**
     * Populate the place-holders in the migration stub.
     *
     * @param  string  $name
     * @param  string  $stub
     * @param  string|null  $table
     * @return string
     */
    protected function populateStub($name, $stub, $table)
    {
        $stub = str_replace(
            ['DummyClass', '{{ class }}', '{{class}}'],
            $this->getClassName($name), $stub
        );

        if (! is_null($table)) {
            $stub = str_replace(
                ['DummyTable', '{{ table }}', '{{table}}'],
                $table, $stub
            );
        }

        return $stub;
    }

    /**
     * Get the class name of a migration name.
     *
     * @param  string  $name
     * @return string
     */
    protected function getClassName($name)
    {
        return Str::studly($name);
    }

    /**
     * Get the full path to the migration.
     *
     * @param  string  $name
     * @param  string  $path
     * @return string
     */
    protected function getPath($name, $path)
    {
        return $path.'/'.$this->getDatePrefix().'_'.$name.'.php';
    }

    /**
     * Fire the registered post create hooks.
     *
     * @param  string|null  $table
     * @return void
     */
    protected function firePostCreateHooks($table)
    {
        foreach ($this->postCreate as $callback) {
            $callback($table);
        }
    }

    /**
     * Register a post migration create hook.
     *
     * @return void
     */
    public function afterCreate(\Closure $callback)
    {
        $this->postCreate[] = $callback;
    }

    /**
     * Get the date prefix for the migration.
     *
     * @return string
     */
    protected function getDatePrefix()
    {
        return date('Y_m_d_His');
    }

    /**
     * Get the path to the stubs.
     *
     * @return string
     */
    public function stubPath()
    {
        return __DIR__.'/stubs';
    }

    /**
     * Get the filesystem instance.
     *
     * @return Filesystem
     */
    public function getFilesystem()
    {
        return $this->files;
    }
}
