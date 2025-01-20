<?php

namespace App\Command;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class BackupDatabaseCommand extends Command
{

    private string $database;
    private string $username;
    private string $password;
    private string $path;
    private Filesystem $fs;

    protected function configure()
    {
        $this->setName('backup:database')
            ->setDescription('Dump database.')
            ->addArgument('file', InputArgument::REQUIRED, 'Absolute path for the file you need to dump database to.');
    }

}