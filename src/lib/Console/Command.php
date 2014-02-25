<?php namespace Console;

/**
 * This file was copied from and inspired by a similar
 * file in the Laravel / Envoy package which is released
 * under the the MIT license.
 *
 * @see  https://github.com/laravel/envoy
 */

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Common functions used in other commands
 */
trait Command {

    /**
     * Execute the command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        return $this->fire();
    }

    /**
     * Get an argument from the input.
     *
     * @param  string  $key
     * @return string
     */
    public function argument($key)
    {
        return $this->input->getArgument($key);
    }

    /**
     * Get an option from the input.
     *
     * @param  string  $key
     * @return string
     */
    public function option($key)
    {
        return $this->input->getOption($key);
    }

    /**
     * Gather the dynamic options for the command.
     *
     * @return void
     */
    protected function getOptions()
    {
        $options = [];

        // Here we will gather all of the command line options that have been specified with
        // the double hyphens in front of their name.
        foreach ($_SERVER['argv'] as $argument) {
            preg_match('/^\-\-(.*?)=(.*)$/', $argument, $match);

            if (count($match) > 0) {
                $options[$match[1]] = $match[2];
            }
        }

        return $options;
    }

    /**
     * Write out a comment to the CLI
     * 
     * @param  string  $heading [heading]: to prepend ouptput with
     * @param  string  $message the message to write out
     * @param  boolean $error   if error, message will be red
     * @return void
     */
    public function comment($heading, $message, $error = false)
    {
        $str = "";

        if ($heading) {
            $str .= "<comment>[{$heading}]</comment>: ";
        }

        if ($error) {
            $str .= '<error>'.trim($message).'</error>';
        } else {
            $str .= trim($message);
        }

        $str .= PHP_EOL;

        $this->output->write($str);
    }

    /**
     * Ask the user the given question.
     *
     * @param  string  $question
     * @return string
     */
    public function ask($question)
    {
        $question = '<comment>'.$question.'</comment> ';

        return $this->getHelperSet()->get('dialog')->ask($this->output, $question);
    }

    /**
     * Ask the user the given secret question.
     *
     * @param  string  $question
     * @return string
     */
    public function secret($question)
    {
        $question = '<comment>'.$question.'</comment> ';

        return $this->getHelperSet()->get('dialog')->askHiddenResponse($this->output, $question, false);
    }
}
