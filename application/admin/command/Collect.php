<?php

namespace app\admin\command;
use think\console\Command;
use think\console\Input;
use think\console\Output;


class Collect  extends Command
{

    protected function configure()
    {
        $this->setName('collect')->setDescription('web collect start');
    }

    protected function execute(Input $input, Output $output)
    {
        // // 行为逻辑
        action('admin/Collect/collect');
    }
}