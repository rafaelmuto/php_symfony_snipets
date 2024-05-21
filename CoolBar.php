<?php

namespace App\Command\tool;

use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class CoolBar
{
    private ProgressBar $cool_bar;
    private \DateTime $startDatetime;

    public function __construct(OutputInterface $output, int $max_number)
    {
        $this->cool_bar = new ProgressBar($output, $max_number * 1.1, 0);

        ProgressBar::setPlaceholderFormatterDefinition('memory', function (ProgressBar $bar) {
            static $i = 0;
            $mem = 100000 * $i;
            $colors = $i++ ? '41;37' : '44;37';

            return "\033[" . $colors . 'm ' . Helper::formatMemory($mem) . " \033[0m";
        });

        $this->cool_bar->setFormat(" \033[44;37m %title:-37s% \033[0m\n %current%/%max% %bar% %percent:3s%%\n 🏁  %remaining:-10s% %memory:37s%");
        $this->cool_bar->setBarCharacter($done = "\033[32m●\033[0m");
        $this->cool_bar->setEmptyBarCharacter($empty = "\033[31m●\033[0m");
        $this->cool_bar->setProgressCharacter($progress = "\033[32m➤ \033[0m");
    }

    public function start(string $start_msg = '')
    {
        $this->startDatetime = new \DateTime();

        $this->cool_bar->setMessage('[' . $this->startDatetime->format('H:i:s') . '] ' . $start_msg, 'title');
        $this->cool_bar->start();
    }

    public function advance(int $step = 1, string $msg = '')
    {
        if ('' != $msg) {
            $this->cool_bar->setMessage($msg, 'title');
        }

        $this->cool_bar->advance($step);
    }

    public function finish(string $end_msg = '')
    {
        $timeDiff = $this->startDatetime->diff(new \DateTime());

        $this->cool_bar->setMessage('[' . $timeDiff->format('%H:%i:%s') . '] ' . $end_msg, 'title');
        $this->cool_bar->finish();
    }
}
