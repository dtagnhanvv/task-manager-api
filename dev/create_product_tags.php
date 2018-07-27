<?php

namespace biddy\dev;

use AppKernel;
use Biddy\DomainManager\ProductManagerInterface;
use Biddy\DomainManager\TagManagerInterface;
use Biddy\Entity\Core\ProductTag;
use Biddy\Entity\Core\Tag;
use Biddy\Model\Core\ProductInterface;
use Biddy\Model\Core\TagInterface;
use Doctrine\Common\Collections\Collection;
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
const NUMBER_TAGS = 50000;

$tagManager = $container->get('biddy.domain_manager.tag');
$productManager = $container->get('biddy.domain_manager.product');

for ($i = 0; $i < NUMBER_TAGS; $i++) {
    $product = getRandomProduct($productManager);
    $tag = getRandomTag($tagManager);

    if (!$product instanceof ProductInterface || !$tag instanceof TagInterface) {
        continue;
    }

    $productTags = $product->getProductTags();
    if (count($productTags) > 5) {
        continue;
    }

    if ($productTags instanceof Collection) {
        $productTag = new ProductTag();
        $productTag->setProduct($product);
        $productTag->setTag($tag);
        $productTags->add($productTag);
    }

    $product->setProductTags($productTags);
    $productManager->save($product);

    writeln(sprintf('###### Tag %s, on product %s', $tag->getId(), $product->getId()));

    $tag = null;
    $product = null;
    $productTag = null;
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
 * @param TagManagerInterface $tagManager
 * @return TagInterface
 */
function getRandomTag(TagManagerInterface $tagManager)
{
    $randomTagId = rand(1, 1000);
    $tag = $tagManager->find($randomTagId);

    return $tag;
}