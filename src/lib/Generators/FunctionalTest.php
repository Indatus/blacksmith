<?php namespace Generators;

use Illuminate\Filesystem\Filesystem;
use Mustache_Engine;
use Parsers\FieldParser;

class FunctionalTest extends Generator implements GeneratorInterface
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

    /**
     * Function to get the minimum template variables
     * 
     * @return array
     */
    public function getTemplateVars()
    {
        $entity    = $this->getEntityName();
        $fieldData = $this->getFieldData();

        $attributes = [];

        //get fake data for each property
        foreach ($fieldData as $property => $meta) {
            $attributes[$property] = $this->getAttributeMockValue($meta['type']);
        }

        //add override for specific cases like id, created_at etc
        $attributes['id'] = 1;
        $attributes['created_at'] = "'". date('Y-m-d H:i:s') ."'";
        $attributes['updated_at'] = "'". date('Y-m-d H:i:s') ."'";

        $mock_attribute_rows = [];

        //create a "row" for each entry
        foreach ($attributes as $key => $value) {
            $mock_attribute_rows[] = "'{$key}' => {$value},";
        }

        return [
            'Entity'          => Str::studly($entity),
            'Entities'        => Str::plural(Str::studly($entity)),
            'collection'      => Str::plural(Str::snake($entity)),
            'instance'        => Str::singular(Str::snake($entity)),
            'fields'          => $fieldData,
            'mock_attributes' => $mock_attribute_rows
        ];
    }


    /**
     * Function to return the form element type
     * that should be used, given the datatype input
     * 
     * @param  string $dataType Field data type
     * @return string           Form element type to use
     */
    public function getAttributeMockValue($dataType)
    {
        $lookup = [
            'integer'    => 1,
            'bigInteger' => PHP_INT_MAX,
            'string'     => "'dreamcatcher'",
            'decimal'    => 12345.12,
            'float'      => 152853.5047,
            'timestamp'  => time(),
            'date'       => "'". date('Y-m-d') ."'",
            'dateTime'   => "'". date('Y-m-d H:i:s') ."'",
            'text'       => "'Tonx art party PBR&B, Blue Bottle sriracha Bushwick iPhone wolf kale chips Godard typewriter selfies shabby chic church-key 3 wolf moon'",
            'boolean'    => true
        ];

        return array_key_exists($dataType, $lookup) ? $lookup[$dataType] : "text";
    }
}
