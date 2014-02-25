<?php namespace Generators;

use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Mustache_Engine;

abstract class Generator
{
    /**
     * Filesystem object used to write
     * oute files / make dirs
     * 
     * @var Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * Mustache Template Engine used for
     * parsing templates
     * 
     * @var Mustache_Engine
     */
    protected $mustache;

    /**
     * Name of the file that should be written
     * 
     * @var string
     */
    protected $destinationFileName;

    /**
     * Name of the entity the generator
     * is working with
     * 
     * @var string
     */
    protected $entity;


    /**
     * Constructor to set member vars
     * 
     * @param Filesystem $filesystem
     * @param Mustache_Engine $mustache
     */
    public function __construct(Filesystem $filesystem, Mustache_Engine $mustache)
    {
        $this->filesystem = $filesystem;
        $this->mustache = $mustache;
    }



    public function make($entity, $sourceTemplate, $destinationDir, $fileName = null)
    {
        //set the entity we're creating for
        //later template parsing operations
        $this->entity = $entity;

        //set into a class var vor getFileName() to use if necessary
        $this->destinationFileName = $fileName ?: null;

        //get the compiled template
        $template = $this->getTemplate($sourceTemplate);

        //find out where to write the file
        $destination = implode(DIRECTORY_SEPARATOR, [$destinationDir, $this->getFileName()]);

        //actually do the write operation
        if (! $this->filesystem->exists($destination)) {
            return $this->filesystem->put($destination, $template) !== false;
        }

        return false;
    }


    /**
     * Function to get the minimum template variables
     * 
     * @return array
     */
    public function getTemplateVars()
    {
        $name   = $this->getFileName();
        $entity = $this->getEntityName();

        return [
            'ClassName'  => basename($name, ".". pathinfo($name, PATHINFO_EXTENSION)),
            'Entity'     => Str::studly($entity),
            'Entities'   => Str::plural(Str::studly($entity)),
            'collection' => Str::plural(Str::snake($entity)),
            'instance'   => Str::singular(Str::snake($entity))
        ];
    }


    /**
     * Function to get the name of the entity
     * we're working with
     * 
     * @return string
     */
    public function getEntityName()
    {
        return $this->entity;
    }


    /**
     * Function to return the name of the file
     * that should be written
     * 
     * @return string
     */
    abstract public function getFileName();


    /**
     * Read the template file from the Filesystem and compile
     *
     * @param  string $sourceTemplate
     * @param  $name Name of file
     * @return string
     */
    public function getTemplate($sourceTemplate)
    {
        $rawTemplate = $this->filesystem->get($sourceTemplate);
        return $this->mustache->render($rawTemplate, $this->getTemplateVars());
    }
}
