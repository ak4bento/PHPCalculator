<?php

namespace Jakmall\Recruitment\Calculator\Commands;

use Illuminate\Console\Command;
use Jakmall\Recruitment\Calculator\History\Infrastructure\CommandHistoryManagerInterface;

class HistoryListCommand extends Command
{
    //use HasCommand;

    /**
     * @var string
     */
    protected $command = 'history:list';

    /**
     * Choose Option --driver = [file|latest|composite]
     * 
     * @var string
     */
    protected $optionDriver = 'composite';

    public function __construct()
    {
        $this->getDescriptions();
        $command = $this->command;
        $argument = "Find the data by the specified id";
        $optionDriver = "Driver opsional for connecting";
        $this->signature = sprintf(
            '%s {commands?* : %s} {--driver=composite : %s}',
            $command, $argument, $optionDriver
        );

        parent::__construct();
    }

    public function handle(CommandHistoryManagerInterface $history): void
    {
        $commands = $this->argument('commands');
        $driver = $this->option('driver');

        $data = $history->findAll($driver, $commands);

        if(!empty($data)) {
            $headers = ['ID', 'Command', 'Operation', 'Result'];
            $this->table($headers, $data);
        } else {
            $this->comment("History is empty");
        }
    }

    protected function getDescriptions() : void
    {
        $this->description = 'Show result history with request';
    }
}
