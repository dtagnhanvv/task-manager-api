<?php

namespace Biddy\EventListener\Product;

use Biddy\Model\Core\ProductInterface;
use Biddy\Worker\Manager;
use Doctrine\ORM\Event\LifecycleEventArgs;

class CreateDailyCloseAuctionsWhenAddProductsListener
{
    protected $changeProductRatings = [];

    /** @var Manager */
    protected $workerManager;

    /**
     * CalculateSellerRatingWhenProductRatingChangeListener constructor.
     * @param Manager $workerManager
     */
    public function __construct(Manager $workerManager)
    {
        $this->workerManager = $workerManager;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof ProductInterface) {
            return;
        }

        $this->workerManager->dailyCloseAuctionsWorker();
    }
}