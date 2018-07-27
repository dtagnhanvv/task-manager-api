<?php


namespace Biddy\Bundle\AppBundle\Command;

use Biddy\Model\Core\AuctionInterface;
use Biddy\Repository\Core\AuctionRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DailyCloseAuctionsCommand extends ContainerAwareCommand
{
    const COMMAND_NAME = 'biddy.bidding.daily-close-auctions';
    const INPUT_START_DATE = 'startDate';
    const INPUT_END_DATE = 'endDate';
    const YESTERDAY = 'yesterday';

    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->addOption(self::INPUT_START_DATE, 's', InputOption::VALUE_REQUIRED, 'Start date ')
            ->addOption(self::INPUT_END_DATE, 't', InputOption::VALUE_REQUIRED, 'End date')
            ->setDescription('Find automated ending products and close this');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ContainerInterface $container */
        $container = $this->getContainer();
        $io = new SymfonyStyle($input, $output);
        $auctionCloser = $container->get('biddy.service.bidding.core.auction_closer');
        /** @var AuctionRepositoryInterface */
        $auctionRepository = $container->get('biddy.repository.auction');

        $endDateString = $input->getOption(self::INPUT_END_DATE);

        if (empty($endDateString)) {
            $endDateString = self::YESTERDAY;
        }

        $auctions = $auctionRepository->getAutomatedEndingProducts(new \DateTime($endDateString));

        foreach ($auctions as $auction) {
            if (!$auction instanceof AuctionInterface) {
                continue;
            }

            if ($auction->getStatus() == AuctionInterface::STATUS_CLOSED) {
                return;
            }

            try {
                $auctionCloser->closeAuction($auction);
            } catch (\Exception $e) {
                $io->warning($e->getMessage());
            }
        }
    }
}