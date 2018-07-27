<?php

namespace biddy\dev;
use AppKernel;
use Biddy\Bundle\UserBundle\DomainManager\AccountManagerInterface;
use Biddy\DomainManager\ProductManagerInterface;
use Biddy\Entity\Core\Product;
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

const TOTAL_PRODUCT = 10000;

$accountManager = $container->get('biddy_user.domain_manager.account');
/** @var ProductManagerInterface $productManager */
$productManager = $container->get('biddy.domain_manager.product');
$detail = file_get_contents("dev/sample_product.html");

writeln('### Start creating test products ###');
for ($i = 0; $i < TOTAL_PRODUCT; $i++) {
    $user = getRandomUser($accountManager);

    if (!$user instanceof AccountInterface) {
        continue;
    }

    $product = new Product();
    $product
        ->setSubject('test')
        ->setDetail($detail)
        ->setLongitude(21.028511)
        ->setLatitude(105.804817)
        ->setBusinessRule(ProductInterface::SUPPORT_BUSINESS_RULES[array_rand(ProductInterface::SUPPORT_BUSINESS_RULES)])
        ->setBusinessSetting(ProductInterface::SUPPORT_BUSINESS_SETTINGS[array_rand(ProductInterface::SUPPORT_BUSINESS_SETTINGS)])
        ->setMode(ProductInterface::SUPPORT_MODES[array_rand(ProductInterface::SUPPORT_MODES)])
        ->setVisibility(ProductInterface::SUPPORT_COMMENT_VISIBILITIES[array_rand(ProductInterface::SUPPORT_COMMENT_VISIBILITIES)])
        ->setCommentVisibility(ProductInterface::SUPPORT_COMMENT_VISIBILITIES[array_rand(ProductInterface::SUPPORT_COMMENT_VISIBILITIES)])
        ->setSeller($user);

    try {
        $productManager->save($product);
        $product->setSubject(sprintf("Dự án chung cư tại khu đô thị đẳng cấp và hiện đại %s", $product->getId()));
        $productManager->save($product);
        writeln(sprintf('###### Product %s', $product->getId()));
    } catch (\Exception $e) {
    }

    $product = null;
    $user = null;
    gc_collect_cycles();
}

function writeln($str)
{
    echo $str . PHP_EOL;
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