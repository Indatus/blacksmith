<?php namespace Generators;

use Illuminate\Filesystem\Filesystem;
use Mustache_Engine;
use Parsers\FieldParser;

class Seed extends Generator implements GeneratorInterface
{

    /**
     * Constructor to set member vars
     * 
     * @param Filesystem $filesystem
     * @param Mustache_Engine $mustache
     */
    public function __construct(Filesystem $filesystem, Mustache_Engine $mustache, FieldParser $fieldParser)
    {
        parent::__construct($filesystem, $mustache, $fieldParser);
    }


    public function make($entity, $sourceTemplate, $destinationDir, $fileName = null, $fieldData = null)
    {
        $result = parent::make($entity, $sourceTemplate, $destinationDir, $fileName, $fieldData);

        if (!$result) {
            return false;
        }

        $this->updateDatabaseSeederRunMethod(basename($this->getFileName(), ".php"), getcwd());

        return true;
    }


    /**
     * Function to add our specific seeder to the global
     * seeder function 
     * @param  string $className 
     * @param  string $dir
     * @return void
     */
    public function updateDatabaseSeederRunMethod($className, $dir)
    {
        $databaseSeederPath = implode(DIRECTORY_SEPARATOR, [$dir, 'app', 'database', 'seeds', 'DatabaseSeeder.php']);

        if (! $this->filesystem->exists($databaseSeederPath)) {
            return false;
        }

        $content = $this->filesystem->get($databaseSeederPath);

        if (! strpos($content, "\$this->call('{$className}');")) {

            $content = preg_replace("/(run\(\).+?)}/us", "$1\t\$this->call('{$className}');\n\t}", $content);
            return $this->filesystem->put($databaseSeederPath, $content);
        }

        return false;
    }
}
