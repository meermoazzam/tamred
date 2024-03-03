<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class MakeService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class';

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Create a new command instance.
     *
     * @param  Filesystem  $filesystem
     * @return void
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();

        $this->filesystem = $filesystem;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('name');
        $className = Str::studly($name);
        $filePath = app_path('Services/' . $className . '.php');

        if ($this->filesystem->exists($filePath)) {
            $this->error('Service class already exists!');
            return 1;
        }

        $stub = $this->filesystem->get(__DIR__.'/stubs/service.stub');
        $stub = str_replace('{{class}}', $className, $stub);

        $this->filesystem->put($filePath, $stub);

        $this->info('Service class created successfully: ' . $filePath);

        return 0;
    }
}
