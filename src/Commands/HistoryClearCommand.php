<?php

namespace Jakmall\Recruitment\Calculator\Commands;

use Illuminate\Console\Command;
use Jakmall\Recruitment\Calculator\History\Infrastructure\CommandHistoryManagerInterface;

class HistoryClearCommand extends Command
{
    //use HasCommand;

    /**
     * @var string
     */
    protected $command = 'history:clear';

    public function __construct()
    {
        $this->getDescriptions();
        $command = $this->command;
        $argument = "Clear the data by all data or the specified id";
        $this->signature = sprintf(
            '%s {commands?* : %s}',
            $command, $argument
        );

        parent::__construct();
    }

    public function handle(CommandHistoryManagerInterface $history): void
    {
        $commands = $this->argument('commands');

        if (empty($commands)) {
            $history->clearAll('mesinhitung.log', 'delete');
            $history->clearAll('latest.log', 'delete');
            $this->comment(sprintf('Success delete all data in every driver', $commands));
        } else {
            $history->clear('latest', $commands[0]);
            $history->clear('file', $commands[0]);
            $this->comment(sprintf('Success delete data id "%s" in every driver.', $commands[0]));
        }
        
    }

    protected function getDescriptions() : void
    {
        $this->description = 'Clear result history with all or id';
    }
}
