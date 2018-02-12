<?php
declare(strict_types=1);

namespace App\Command;

use App\Controller\ContentController;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * The console command to run via cron
 */
class AppAddtweetsCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'app:add-tweets';

    /** @var ContentController */
    private $contentController;

    /**
     * @param ContentController $contentController
     * @param null|string $name
     */
    public function __construct(ContentController $contentController, ?string $name = null)
    {
        parent::__construct($name);
        $this->contentController = $contentController;
    }

    protected function configure()
    {
        $this
            ->setDescription('The console command to add tweets as news articles from a given account')
            ->addArgument('acc', InputArgument::REQUIRED, 'Twitter account to get news from')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $argument = $input->getArgument('acc');

        $result = $this->contentController->addContentFromTwitter($argument);

        $io->writeln($result);
    }
}
