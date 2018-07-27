<?php

namespace biddy\dev;

use AppKernel;
use Biddy\Bundle\UserSystem\SaleBundle\Entity\User;
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

const TOTAL_SALES = 10000;

$saleManager = $container->get('biddy_user.domain_manager.sale');

writeln('### Start creating test users ###');
for ($i = 0; $i < TOTAL_SALES; $i++) {
    $sale = new User();
    $username = 'sale' . $i;
    $sale
        ->setUsername($username)
        ->setPlainPassword('123456')
        ->setEmail(sprintf('saletest%d@biddy.com', $i + 2))
        ->setEnabledModules(\Biddy\Bundle\UserBundle\Entity\User::DEFAULT_MODULES_FOR_SALE)
        ->setEnabled(true);

    try {
        $saleManager->save($sale);
        writeln(sprintf('###### Sale %s', $sale->getId()));
    } catch (\Exception $e) {
        writeln(sprintf('###### Can not create sale %s', $i));
    }

    $sale = null;
    gc_collect_cycles();
}

function writeln($str)
{
    echo $str . PHP_EOL;
}