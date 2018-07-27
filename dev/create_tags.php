<?php

namespace biddy\dev;
use AppKernel;
use Biddy\Entity\Core\Tag;
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
for ($i = 0; $i < NUMBER_TAGS; $i++) {
    $tag = new Tag();
    $tag->setName("url");
    $tagManager->save($tag);

    $tag->setName(sprintf("Tag: %s", $tag->getId()));
    $tagManager->save($tag);

    writeln(sprintf('###### Tag %s', $tag->getId()));

    $tag = null;
    gc_collect_cycles();
}

function writeln($str)
{
    echo $str . PHP_EOL;
}