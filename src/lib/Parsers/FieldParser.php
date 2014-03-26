<?php namespace Parsers;

/**
 * This class was copied from the MIT licensed
 * way\generators package (c) Jeffrey Way.
 *
 * @see  https://github.com/JeffreyWay/Laravel-4-Generators/blob/master/src/Way/Generators/Parsers/MigrationFieldsParser.php
 */
class FieldParser
{

    /**
     * Parse a string of fields, like
     * name:string, age:integer
     *
     * @param string $fields
     * @return array
     */
    public function parse($fields)
    {
        if (! $fields) {
            return [];
        }

        // name:string, age:integer
        // name:string(10,2), age:integer
        $fields = preg_split('/\s?,\s/', $fields);
        $parsed = [];

        foreach ($fields as $field) {
            // Example:
            // name:string:nullable => ['name', 'string', 'nullable']
            // name:string(15):nullable
            $chunks = preg_split('/\s?:\s?/', $field, null);

            // The first item will be our property
            $property = array_shift($chunks);

            // The next will be the schema type
            $type = array_shift($chunks);

            $args = null;

            // Sett if args were provided, like:
            // name:string(10)
            if (preg_match('/(.+?)\(([^)]+)\)/', $type, $matches)) {
                $type = $matches[1];
                $args = $matches[2];
            }

            // Finally, anything that remains will
            // be our decorators
            $decorators = $chunks;

            $parsed[$property] = ['type' => $type];

            if (isset($args)) {
                $parsed[$property]['args'] = $args;
            }
            if ($decorators) {
                $parsed[$property]['decorators'] = $decorators;
            }
        }

        return $parsed;
    }
}
