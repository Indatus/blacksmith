<?php namespace Generators;

use Illuminate\Filesystem\Filesystem;
use Mustache_Engine;
use Parsers\FieldParser;
use Illuminate\Support\Str;

class Form extends Generator implements GeneratorInterface
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

        $form_rows = [];

        foreach ($fieldData as $property => $meta) {

            $display           = Str::title(str_replace('_', ' ', $property));
            $result['label']   = "{{ Form::label('{$property}', '{$display}:') }}";
            
            $elementType       = $this->getElementType($meta['type']);
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


    /**
     * Function to return the form element type
     * that should be used, given the datatype input
     * 
     * @param  string $dataType Field data type
     * @return string           Form element type to use
     */
    public function getElementType($dataType)
    {
        $lookup = [
            'string'  => 'text',
            'float'   => 'text',
            'date'    => 'text',
            'text'    => 'textarea',
            'boolean' => 'checkbox'
        ];

        return array_key_exists($dataType, $lookup) ? $lookup[$dataType] : "text";
    }
}
