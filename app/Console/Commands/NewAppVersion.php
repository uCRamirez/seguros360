<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Artisan;

use Illuminate\Console\Command;
use Nwidart\Modules\Facades\Module;

class NewAppVersion extends Command
{
    public $inputFolder = "";

    public $outputFolder = "";

    public $buildModuleCodeIncludedInUpdate = ['StockiflyHrm'];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:version {version}';

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
        $this->outputFolder = env('OUTPUT_FOLDER');


        $this->buildModuleCodeIncludedInUpdate = Module::all();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $version = $this->argument('version');

        $this->clear();

        $this->createVersionZip($version);

        $this->createAutoUpdate($version);

        $this->comment("\n" . $this->outputFolder . '-' . $version . ' is Ready to distribute');
    }

    private function clear()
    {
        $this->comment("\n ------Cleaning-------");
        $this->info(' php artisan debugbar:clear');
        try {
            Artisan::call('debugbar:clear');
        } catch (\Exception $exception) {
            $this->info(' Debugbar not present');
        }

        $this->info(' php artisan vendor:cleanup');
        Artisan::call('vendor:cleanup');

        $this->info(' php artisan cache:clear');
        Artisan::call('cache:clear');

        $this->info(' php artisan view:clear');
        Artisan::call('view:clear');

        $this->info(' php artisan config:clear');
        Artisan::call('config:clear');
    }

    private function createVersionZip($version)
    {
        $folder = $this->outputFolder . '-' . $version;
        $versionFolder = '../versions/';
        $path = $versionFolder . $folder;
        $local = '../' . $this->inputFolder . '/';

        $this->comment("\n\n" . '------Generating app.js file------' . "\n\n");
        echo exec('npm run build');

        $this->comment("\n\n" . '------Creating Versions------');
        $this->info(' Removing Old ' . $folder . ' folder to create the new');
        echo exec('rm -rf ' . $versionFolder . $folder);
        echo exec('rm -rf ' . $versionFolder . $folder . '.zip');

        $this->info(' Creating the directory ' . $folder . '/script');
        echo exec('mkdir -p ' . $path);

        $this->info(' Copying files from ' . $local . ' ' . $path);
        echo exec('rsync -av --progress ' . $local . ' ' . $path . '  --exclude="__MACOSX" --exclude=".history" --exclude=".vscode" --exclude=".git" --exclude=".phpintel" --exclude=".env" --exclude=".idea"');


        $this->info(' Removing installed');
        echo exec('rm -rf ' . $path . '/storage/installed');

        $this->info(' Removing NewAppVersion Common File');
        echo exec('rm ' . $path . '/app/Console/Commands/VendorCleanUpCommand.php');
        echo exec('rm ' . $path . '/app/Console/Commands/NewAppVersion.php');
        echo exec('rm ' . $path . '/app/Console/Commands/NewModuleVersion.php');


        $this->info(' Removing .gitlab folder');
        echo exec('rm -rf ' . $path . '.gitlab');


        $this->info(' Delete Storage Folder Files');
        echo exec('rm -rf ' . $path . '/public/storage');

        $this->info(' Removing uploads folders');
        echo exec('rm -rf ' . $path . '/public/uploads/*');

        $versionFileFullName = app_type() == 'saas' ? 'public/superadmin_version.txt' : 'public/version.txt';
        $this->info(' removing old version.txt file');
        echo exec('rm ' . $local . $versionFileFullName);
        $this->info(' Copying version to know the version to version.txt file');
        echo exec('echo ' . $version . '>> ' . $local . $versionFileFullName);

        $this->info(' Removing auto-update zip files from storage folder');
        echo exec('rm -rf ' . $path . '/storage/app/*.zip');

        $this->info(' Removing modules_status.json');
        echo exec('rm -rf ' . $path . '/modules_statuses.json');

        $this->info(' Adding blank array in modules_statues.json file');
        echo exec('echo {}>> ' . $path . '/modules_statuses.json');

        $this->info(' Removing symlink');
        echo exec('find ' . $path . '/storage/app/public \! -name ".gitignore" -delete');

        $envFileFullPath = app_type() == 'saas' ? '/.env.superadmin ' : '/.env.example ';
        $envFileName = app_type() == 'saas' ? '.env.superadmin ' : '.env.example ';
        $this->info(' Copying ' . $envFileName . ' to .env');
        echo exec('cp ' . $path . $envFileFullPath . $path . '/.env');

        $this->info(' Delete log files');
        echo exec('rm ' . $path . '/storage/logs/*.log');

        $this->info(' Delete down files');
        echo exec('rm ' . $path . '/storage/app/down');

        $this->info(' gitlab_Ci');
        echo exec('rm -rf ' . $path . '/.gitlab-ci.yml');

        $this->info(' Removing old version.txt file');
        echo exec('rm ' . $path . '/' . $versionFileFullName);

        $this->info(' Removing node_modules folder');
        echo exec('rm -rf ' . $path . '/node_modules');

        $this->info(' Copying ' . $version . ' version to know the version to version.txt file');
        echo exec('echo ' . $version . '>> ' . $path . '/' . $versionFileFullName);

        $this->comment("\n\n" . '------Deleting hot file from public ------' . "\n\n");
        echo exec('rm ' . $path . '/public/hot');

        // No need to create migrate file
        // Because on first time setup
        // Database settings not setup so
        // How we run migration

        $this->comment("\n\n" . '------Emptying Public Js Directroy------' . "\n\n");
        echo exec('rm -rf ' . $path . '/public/js/*');

        $this->comment("\n\n" . '------Emptying Modules Directroy------' . "\n\n");
        echo exec('rm -rf ' . $path . '/Modules/*');

        $this->comment("\n\n" . '------Deleting .DS_Store files recurisvely------' . "\n\n");
        echo exec('find . -name ".DS_Store"');

        // Zipping the folder and removing __MACOSX from zip
        $this->info(' Zipping the folder');
        echo exec('cd ../versions; zip -r ' . $folder . '.zip ' . $folder . '/; zip -d ' . $folder . '.zip __MACOSX/\*');

        return $path;
    }

    private function createAutoUpdate($version)
    {
        // $folder = $this->outputFolder . '-' . $version;
        // $versionFolder = '../versions/';
        // $path = $versionFolder . $folder;
        // $local = '../' . $this->inputFolder . '/';

        //start quick update version
        $this->output->progressStart(8);
        $folder = $this->inputFolder . '-auto-' . $version;
        $path = '../versions/auto-update/' . $this->inputFolder;
        $local = '../' . $this->inputFolder . '/';

        $this->comment("\n\n\n" . '------Creating Auto update version------');
        $this->info(' Removing Old ' . $folder . ' folder to create the new');
        echo exec('rm -rf ' . $path);
        echo exec('mkdir -p ' . $path);

        $this->info(' Copying files from ' . $local . ' to ' . $path);
        echo exec('rsync -av --progress ' . $local . ' ' . $path . ' --exclude="__MACOSX" --exclude=".history" --exclude=".vscode" --exclude=".git" --exclude=".phpintel" --exclude=".env" --exclude=".idea"');

        $this->info(' Removing NewAppVersion Common File');
        echo exec('rm ' . $path . '/app/Console/Commands/VendorCleanUpCommand.php');
        echo exec('rm ' . $path . '/app/Console/Commands/NewAppVersion.php');
        echo exec('rm ' . $path . '/app/Console/Commands/NewModuleVersion.php');

        $this->info(' Delete Storage Folder Files');
        echo exec('rm -rf ' . $path . '/public/storage');

        $this->info(' Removing uploads folders');
        echo exec('rm -rf ' . $path . '/public/uploads/*');

        $this->info(' Removing .gitlab folder');
        echo exec('rm -rf ' . $path . '.gitlab');

        $this->info(' Delete Language Folder Files');
        echo exec('rm -rf ' . $path . '/resources/lang/*');

        $this->info(' Creating the en directory ' . $path . '/resources/lang');
        echo exec('mkdir -p ' . $path . '/resources/lang/en');

        $this->info(' Copy English Language Folder Files');
        echo exec('cp ' . $local . 'resources/lang/en/* ' . $path . '/resources/lang/en/');

        $this->info(' Removing symlink');
        echo exec('find ' . $path . '/storage/app/public \! -name ".gitignore" -delete');

        $this->info(' Delete log files');
        echo exec('rm ' . $path . '/storage/logs/*.log');

        $this->info(' Delete down files');
        echo exec('rm ' . $path . '/storage/app/down');

        $this->info(' Removing modules_status.json');
        echo exec('rm -rf ' . $path . '/modules_statuses.json');

        $this->info(' Removing Zip files');
        echo exec('rm -rf ' . $path . '/storage/app/*.zip');

        $this->info(' Removing Legal and Reviewed file');
        echo exec('rm -rf ' . $path . 'storage/legal');
        echo exec('rm -rf ' . $path . 'storage/reviewed');


        $this->info(' Removing Documentation folder');
        echo exec('rm -rf ' . $path . '/documentation');

        $this->info(' Removing node_modules folder');
        echo exec('rm -rf ' . $path . '/node_modules');

        $this->info(' gitlab_Ci');
        echo exec('rm -rf ' . $path . '/.gitlab-ci.yml');

        $versionFileFullName = app_type() == 'saas' ? 'public/superadmin_version.txt' : 'public/version.txt';
        $this->info(' removing old version.txt file');
        echo exec('rm ' . $path . '/' . $versionFileFullName);

        $this->info(' Copying version to know the version to version.txt file');
        echo exec('echo ' . $version . '>> ' . $path . '/' . $versionFileFullName);

        $this->info(' Adding migration file in public path');
        echo exec('echo > ' . $path . '/public/migrate');

        $this->comment("\n\n" . '------Deleting hot file from public ------' . "\n\n");
        echo exec('rm ' . $path . '/public/hot');

        $this->comment("\n\n" . '------Emptying Public Js Directroy------' . "\n\n");
        echo exec('rm -rf ' . $path . '/public/js/*');

        $this->comment("\n\n" . '------Emptying Modules Directroy------' . "\n\n");
        echo exec('rm -rf ' . $path . '/Modules/*');

        $this->comment("\n\n" . '------Deleting .DS_Store files recurisvely------' . "\n\n");
        echo exec('find . -name ".DS_Store"');

        // Remove modules js files available inside the array
        // From public build folder
        foreach ($this->buildModuleCodeIncludedInUpdate as $removeBuildModuleName) {
            $anotherFileName = $this->camelCaseToLowerWithUnderscores($removeBuildModuleName);

            // FirstFileName
            echo exec('rm ' . $path . '/public/build/assets/' . $removeBuildModuleName . '.js ');
            echo exec('rm ' . $path . '/public/build/assets/' . $removeBuildModuleName . '.css ');
            echo exec('rm ' . $path . '/public/build/assets/' . $anotherFileName . '.js ');
        }


        return $path;
    }

    function camelCaseToLowerWithUnderscores($inputString)
    {
        $result = preg_replace('/(?<!^)([A-Z])/', '_$1', $inputString);
        return strtolower($result);
    }
}
