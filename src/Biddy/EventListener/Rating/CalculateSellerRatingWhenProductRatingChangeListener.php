<?php

namespace Biddy\EventListener\Rating;

use Biddy\Model\Core\ProductInterface;
use Biddy\Model\Core\ProductRatingInterface;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Worker\Manager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class CalculateSellerRatingWhenProductRatingChangeListener
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
        $em = $args->getEntityManager();

        if (!$entity instanceof ProductRatingInterface) {
            return;
        }

        $this->changeProductRatings[$entity->getId()] = $entity;
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        $em = $args->getEntityManager();

        if (!$entity instanceof ProductRatingInterface || !$args->hasChangedField('rateValue')) {
            return;
        }

        $this->changeProductRatings[$entity->getId()] = $entity;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
//        $entity = $args->getEntity();
//        $em = $args->getEntityManager();
    }

    /**
     * @param PostFlushEventArgs $event
     */
    public function postFlush(PostFlushEventArgs $event)
    {
//        $em = $event->getEntityManager();
        if (empty($this->changeProductRatings)) {
            return;
        }

        $changeProductRatings = $this->changeProductRatings;
        $this->changeProductRatings = [];
        $sellerIds = [];

        foreach ($changeProductRatings as $changeProductRating) {
            if (!$changeProductRating instanceof ProductRatingInterface ||
                !$changeProductRating->getProduct() instanceof ProductInterface ||
                !$changeProductRating->getProduct()->getSeller() instanceof AccountInterface
            ) {
                continue;
            }

            $seller = $changeProductRating->getProduct()->getSeller();
            $sellerIds[$seller->getId()] = $seller->getId();
        }

        if (empty($sellerIds)) {
            return;
        }

        $this->workerManager->calculateSellerRating(implode(',', $sellerIds));
    }
}