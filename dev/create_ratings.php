<?php

namespace biddy\dev;

use AppKernel;
use Biddy\Bundle\UserBundle\DomainManager\AccountManagerInterface;
use Biddy\DomainManager\ProductManagerInterface;
use Biddy\Entity\Core\ProductRating;
use Biddy\Model\Core\ProductInterface;
use Biddy\Model\User\Role\AccountInterface;
use Symfony\Component\Debug\Debug;
use Symfony\Component\DependencyInjection\ContainerInterface;

$loader = require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/AppKernel.php';
Debug::enable();

$kernel = new AppKernel('dev', true);
$kernel->loadClassCache();
$kernel->boot();

/** @var ContainerInterface $container */
$container = $kernel->getContainer();

const TOTAL_COMMENTS_FOR_PRODUCT = 500000;

$accountManager = $container->get('biddy_user.domain_manager.account');
$productManager = $container->get('biddy.domain_manager.product');
$productRatingManager = $container->get('biddy.domain_manager.product_rating');

writeln('### Start creating test comments ###');
for ($i = 0; $i < TOTAL_COMMENTS_FOR_PRODUCT; $i++) {
    $product = getRandomProduct($productManager);
    $user = getRandomUser($accountManager);

    if (!$product instanceof ProductInterface || !$user instanceof AccountInterface) {
        continue;
    }

    $rating = new ProductRating();
    $rating
        ->setProduct($product)
        ->setReviewer($user)
        ->setRateValue(random_int(1, 5))
        ->setRateMessage(sprintf("Rating: %s", uniqid()));

    try {
        $productRatingManager->save($rating);
        writeln(sprintf('###### Rating %s', $rating->getId()));
    } catch (\Exception $e) {
    }

    $rating = null;
    $product = null;
    $user = null;

    gc_collect_cycles();
}

function writeln($str)
{
    echo $str . PHP_EOL;
}

/**
 * @param ProductManagerInterface $productManager
 * @return ProductInterface
 */
function getRandomProduct(ProductManagerInterface $productManager)
{
    $randomProductId = rand(1, 1000);
    $product = $productManager->find($randomProductId);

    return $product;
}

/**
 * @param AccountManagerInterface $accountManager
 * @return AccountInterface
 */
function getRandomUser(AccountManagerInterface $accountManager)
{
    $randomAccountId = rand(1, 1000);
    $account = $accountManager->find($randomAccountId);

    return $account;
}