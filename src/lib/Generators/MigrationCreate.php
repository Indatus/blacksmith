<?php namespace Generators;

use Illuminate\Filesystem\Filesystem;
use Mustache_Engine;
use Parsers\FieldParser;
use Factories\MigrationColumnFactory;
use Illuminate\Support\Str;

class MigrationCreate extends Generator implements GeneratorInterface
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

        $this->updateDatabaseSeederRunMethod(basename($this->getFileName(), ".php"));

        return true;
    }


    /**
     * Function to get the minimum template variables
     * 
     * @return array
     */
    public function getTemplateVars()
    {
        $entity    = $this->getEntityName();
        $fieldData = $this->getFieldData();

        return [
            'Entity'              => Str::studly($entity),
            'Entities'            => Str::plural(Str::studly($entity)),
            'collection'          => Str::plural(Str::snake($entity)),
            'instance'            => Str::singular(Str::snake($entity)),
            'fields'              => $fieldData,
            'columns'             => implode("\n\t\t\t", MigrationColumnFactory::make($fieldData)),
            'migration_timestamp' => date('Y_m_d_His')
        ];
    }


    /**
     * Function to add our specific seeder to the global
     * seeder function 
     * @param  string $className 
     * @return void
     */
    public function updateDatabaseSeederRunMethod($className)
    {
        $databaseSeederPath = implode(DIRECTORY_SEPARATOR, [getcwd(), 'database', 'seeds', 'DatabaseSeeder.php']);

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
