<?php

namespace Skeleton\Console\Helper;

use Symfony\Component\Console\Helper\DialogHelper as BaseDialogHelper;
use Symfony\Component\Console\Output\OutputInterface;

class DialogHelper extends BaseDialogHelper
{
    public function writeSection(OutputInterface $output, $text, $style = 'bg=cyan;fg=black')
    {
        $output->writeln(array(
            '',
            $this->getHelperSet()->get('formatter')->formatBlock($text, $style, true),
            '',
        ));
    }

    public function getQuestion($question, $default, $sep = ':')
    {
        return $default ? sprintf('<info>%s</info> [<comment>%s</comment>]%s ', $question, $default, $sep) : sprintf('<info>%s</info>%s ', $question, $sep);
    }
}
