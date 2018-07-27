<?php

namespace biddy\dev;

use AppKernel;
use Biddy\Bundle\UserSystem\AccountBundle\Entity\User;
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

const TOTAL_USERS = 10000;

$accountManager = $container->get('biddy_user.domain_manager.account');

writeln('### Start creating test users ###');
for ($i = 1; $i < TOTAL_USERS; $i++) {
    $user = new User();
    $username = 'account' . $i;
    $user
        ->setUsername($username)
        ->setPlainPassword('123456')
        ->setEmail(sprintf('usertest%d@biddy.com', $i + 2))
        ->setEnabledModules(\Biddy\Bundle\UserBundle\Entity\User::DEFAULT_MODULES_FOR_ACCOUNT)
        ->setEnabled(true);
    try {
        $accountManager->save($user);
        writeln(sprintf('###### User %s', $user->getId()));
    } catch (\Exception $e) {
        writeln(sprintf('###### Can not create account %s', $i));
    }

    $user = null;
    gc_collect_cycles();
}

function writeln($str)
{
    echo $str . PHP_EOL;
}