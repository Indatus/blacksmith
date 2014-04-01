<?php namespace Generators;

use DateTime;
use Factories\MigrationColumnFactory;
use Illuminate\Support\Str;

class MigrationCreate extends Generator implements GeneratorInterface
{


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
            'migration_timestamp' => date('Y_m_d_His'),
            'year'                => (new DateTime())->format('Y')
        ];
    }
}
