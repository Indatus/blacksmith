<?php namespace Generators;

use Illuminate\Filesystem\Filesystem;
use Mustache_Engine;
use Parsers\FieldParser;
use Illuminate\Support\Str;

class Form extends Generator implements GeneratorInterface
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

        $form_rows = [];

        foreach ($fieldData as $property => $meta) {

            $display           = Str::title(str_replace('_', '', Str::camel($property)));
            $result['label']   = "{{ Form::label('{$property}', '{$display}:') }}";
            
            $elementType       = "text";
            $result['element'] = "{{ Form::{$elementType}('{$property}') }}";
            
            $form_rows[]       = $result;
        }

        return [
            'Entity'     => Str::studly($entity),
            'Entities'   => Str::plural(Str::studly($entity)),
            'collection' => Str::plural(Str::snake($entity)),
            'instance'   => Str::singular(Str::snake($entity)),
            'fields'     => $fieldData,
            'form_rows'  => $form_rows
        ];
    }
}
