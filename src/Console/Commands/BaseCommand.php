<?php

namespace StatonLab\TripalTestSuite\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

abstract class BaseCommand extends Command
{
    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * Begin executing the command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $this->handle();
    }

    /**
     * Override this function to run your script upon command call.
     */
    abstract protected function handle();

    /**
     * Write one or multiple lines.
     *
     * @param string|array $line
     */
    public function line($line)
    {
        $this->output->writeln($line);
    }

    /**
     * Write a success or info message.
     *
     * @param string|array $line
     */
    public function info($line)
    {
        if (is_array($line)) {
            $lines = [];
            foreach ($line as $one) {
                $lines[] = "<info>$one</info>";
            }

            $this->output->writeln($lines);

            return;
        }

        $this->output->writeln("<info>$line</info>");
    }

    /**
     * Write an error.
     *
     * @param string|array $line
     */
    public function error($line)
    {
        if (is_array($line)) {
            $lines = [];
            foreach ($line as $one) {
                $lines[] = "<error>$one</error>";
            }

            $this->output->writeln($lines);

            return;
        }

        $this->output->writeln("<error>$line</error>");
    }

    /**
     * Get an argument.
     *
     * @param $name
     * @return mixed
     */
    public function getArgument($name)
    {
        return $this->input->getArgument($name);
    }

    /**
     * Get all available arguments.
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->input->getArguments();
    }

    /**
     * Check if an argument is present.
     *
     * @param $name
     * @return bool
     */
    public function hasArgument($name)
    {
        return $this->input->hasArgument($name);
    }

    /**
     * Get an option.
     *
     * @param $name
     * @return mixed
     */
    public function getOption($name)
    {
        return $this->input->getOption($name);
    }

    /**
     * Get all options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->input->getOptions();
    }

    /**
     * Check if an options is present.
     *
     * @param $name
     * @return bool
     */
    public function hasOption($name)
    {
        return $this->input->hasOption($name);
    }

    /**
     * Ask the user a question.
     *
     * @param $question
     * @param bool $default
     * @return mixed
     */
    public function ask($question, $default = false)
    {
        $handler = $this->getHelper('question');
        $confirmation = new ConfirmationQuestion($question, $default);

        return $handler->ask($this->input, $this->output, $confirmation);
    }
}
