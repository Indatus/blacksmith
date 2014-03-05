<?php namespace Factories;

/**
 * This class was partially coppied from the MIT licensed
 * way\generators package (c) Jeffrey Way.
 *
 * @see https://github.com/JeffreyWay/Laravel-4-Generators/blob/master/src/Way/Generators/Syntax/AddToTable.php
 */
class MigrationColumnFactory
{

    /**
     * Factory method to return the migration column data
     * 
     * @param  array                    $fields array of data about the columns to create
     * @param  MigrationColumnFactory   $worker factory instance to use
     * @return string                           migration add column code
     */
    public static function make($fields, MigrationColumnFactory $worker = null)
    {
        $factory = $worker ?: new static;
        return $factory->addColumns($fields);
    }

    /**
     * Return string for adding all columns
     *
     * @param $fields
     * @return array
     */
    protected function addColumns($fields)
    {
        $schema = [];

        foreach ($fields as $property => $details) {

            $schema[] = $this->addColumn($property, $details);
        }

        return $schema;
    }

    /**
     * Return string for adding a column
     *
     * @param $property
     * @param $details
     * @return string
     */
    private function addColumn($property, $details)
    {
        $type = $details['type'];
        $output = "\$table->$type('$property')";

        if (isset($details['args'])) {

            $output = "\$table->$type('$property', " . $details['args'] . ")";
        }

        if (isset($details['decorators'])) {

            $output .= $this->addDecorators($details['decorators']);
        }

        return $output . ';';
    }

    /**
     * @param $decorators
     * @return string
     */
    protected function addDecorators($decorators)
    {
        $output = '';

        foreach ($decorators as $decorator) {
            $output .= "->$decorator";

            // Do we need to tack on the parens?
            if (strpos($decorator, '(') === false) {
                $output .= '()';
            }
        }

        return $output;
    }
}
