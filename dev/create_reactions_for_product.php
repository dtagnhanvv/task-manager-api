<?php

namespace biddy\dev;

use AppKernel;
use Biddy\Bundle\UserBundle\DomainManager\AccountManagerInterface;
use Biddy\DomainManager\ProductManagerInterface;
use Biddy\Entity\Core\Reaction;
use Biddy\Model\Core\ProductInterface;
use Biddy\Model\Core\ReactionInterface;
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

const TOTAL_REACTIONS_FOR_PRODUCT = 500000;
const TOTAL_REACTIONS_FOR_COMMENT = 500000;

$accountManager = $container->get('biddy_user.domain_manager.account');
$productManager = $container->get('biddy.domain_manager.product');
$reactionManager = $container->get('biddy.domain_manager.reaction');

writeln('### Start creating reactions ###');
for ($i = 0; $i < TOTAL_REACTIONS_FOR_PRODUCT; $i++) {
    $product = getRandomProduct($productManager);
    $user = getRandomUser($accountManager);

    if (!$product instanceof ProductInterface || !$user instanceof AccountInterface) {
        continue;
    }

    $reaction = new Reaction();
    $reaction
        ->setProduct($product)
        ->setViewer($user)
        ->setEmotion(ReactionInterface::SUPPORT_REACTIONS[array_rand(ReactionInterface::SUPPORT_REACTIONS)]);

    try {
        $reactionManager->save($reaction);
        writeln(sprintf('###### Reaction %s', $reaction->getId()));
    } catch (\Exception $e) {
    }

    $product = null;
    $user = null;
    $reaction = null;
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
 * @return ProductInterface
 */
function getRandomUser(AccountManagerInterface $accountManager)
{
    $randomAccountId = rand(1, 1000);
    $account = $accountManager->find($randomAccountId);

    return $account;
}