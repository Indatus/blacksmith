<?php

namespace Console;

use Factories\GeneratorDelegateFactory;

/**
 * Class for reading optional command line parameters
 */
class OptionReader
{

    /**
     * Command line parameters
     * @var ${DS}options array
     */
    protected $options;

    function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * Get the architecture to be generated
     * @return string
     */
    public function getArchitecture()
    {
        return array_key_exists('architecture', $this->options) ? $this->options['architecture'] : GeneratorDelegateFactory::ARCH_HEXAGONAL;
    }

    /**
     * Determine if generation should be forced
     */
    public function isGenerationForced()
    {
        foreach ($this->options as $option => $value) {
            if ($option == 'f' || $option == 'force') {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the fields for the current generation
     * @return array
     */
    public function getFields()
    {
        return array_key_exists('fields', $this->options) ? $this->options['fields'] : [];
    }
} 