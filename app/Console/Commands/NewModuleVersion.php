<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Artisan;

use Illuminate\Console\Command;

class NewModuleVersion extends Command
{
    public $inputFolder = "";

    public $outputFolder = "";

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app-module:version {name} {version}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new version of app';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->inputFolder = env('INPUT_FOLDER');
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $moduleName = $this->argument('name');
        $version = $this->argument('version');

        $this->createVersionZip($moduleName, $version);

        // $this->createAutoUpdate($version);

        // $this->comment("\n" . $this->outputFolder . '-' . $version . ' is Ready to distribute');
    }

    private function createVersionZip($moduleName, $version)
    {
        $folder = $moduleName . '-' . $version;
        $versionFolder = '../versions/';
        $path = $versionFolder . $folder;
        $local = '../' . $this->inputFolder . '/';
        $modulePath = 'Modules/' . $moduleName . '/';
        $fullAppPath = $local . $modulePath;
        $createdModulePath = $path . '/' . $modulePath;

        $this->comment("\n\n" . '------Generating app.js file------' . "\n\n");
        echo exec('npm run build');

        $this->comment("\n\n" . '------Creating Versions------');
        $this->info(' Removing Old ' . $folder . ' folder to create the new');
        echo exec('rm -rf ' . $versionFolder . $folder);
        echo exec('rm -rf ' . $versionFolder . $folder . '.zip');

        $this->info(' Creating the directory ' . $folder);
        echo exec('mkdir -p ' . $createdModulePath);

        $this->info(' Copying files from ' . $local . ' ' . $path);
        echo exec('rsync -av --progress ' . $fullAppPath . ' ' . $createdModulePath . '  --exclude="__MACOSX" --exclude=".history" --exclude=".vscode" --exclude=".git" --exclude=".phpintel" --exclude=".env" --exclude=".idea"');

        $this->info(' Removing .gitlab folder');
        echo exec('rm -rf ' . $createdModulePath . '/.gitlab');

        $this->info(' removing old version.txt file');
        echo exec('rm ' . $createdModulePath . '/version.txt');

        $this->info(' Copying version to know the version to version.txt file');
        echo exec('echo ' . $version . '>> ' . $createdModulePath . '/version.txt');

        $this->info(' Removing old version.txt file');
        echo exec('rm ' . $fullAppPath . '/version.txt');

        $this->info(' Copying ' . $version . ' version to know the version to version.txt file');
        echo exec('echo ' . $version . '>> ' . $fullAppPath . '/version.txt');

        $this->comment("\n\n" . '------Deleting hot file from public ------' . "\n\n");
        echo exec('rm ' . $path . '/public/hot');

        $this->comment("\n\n" . '------Deleting .DS_Store files recurisvely------' . "\n\n");
        echo exec('find . -name ".DS_Store"');

        $appBuildPath = $local . 'public/build';
        $updateBuildFolderPath = $path . '/public';
        // Created public folder
        $this->info(' Creating the public directory ');
        echo exec('mkdir -p ' . $updateBuildFolderPath);

        echo exec('rsync -av --progress ' . $appBuildPath . ' ' . $updateBuildFolderPath);

        // $anotherFileName = $this->camelCaseToLowerWithUnderscores($moduleName);
        // // FirstFileName
        // echo exec('cp ' . $appBuildPath . '/' . $moduleName . '.js ' . $updateBuildFolderPath);
        // echo exec('cp ' . $appBuildPath . '/' . $moduleName . '.css ' . $updateBuildFolderPath);
        // echo exec('cp ' . $appBuildPath . '/' . $anotherFileName . '.js ' . $updateBuildFolderPath);

        $this->info(' Adding migration file in public path');
        echo exec('echo > ' . $path . '/public/migrate');

        // Zipping the folder and removing __MACOSX from zip
        $this->info(' Zipping the folder');
        echo exec('cd ../versions; zip -r ' . $folder . '.zip ' . $folder . '/; zip -d ' . $folder . '.zip __MACOSX/\*');
    }

    function camelCaseToLowerWithUnderscores($inputString)
    {
        $result = preg_replace('/(?<!^)([A-Z])/', '_$1', $inputString);
        return strtolower($result);
    }
}
