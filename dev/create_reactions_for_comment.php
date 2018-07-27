<?php

namespace biddy\dev;

use AppKernel;
use Biddy\Bundle\UserBundle\DomainManager\AccountManagerInterface;
use Biddy\DomainManager\CommentManagerInterface;
use Biddy\Entity\Core\Reaction;
use Biddy\Model\Core\CommentInterface;
use Biddy\Model\Core\ReactionInterface;
use Biddy\Model\User\Role\AccountInterface;
use Doctrine\ORM\EntityManagerInterface;
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

const TOTAL_REACTIONS_FOR_COMMENT = 500000;

/** @var EntityManagerInterface $em */
$accountManager = $container->get('biddy_user.domain_manager.account');
$commentManager = $container->get('biddy.domain_manager.comment');
$reactionManager = $container->get('biddy.domain_manager.reaction');

writeln('### Start creating reactions ###');
for ($i = 0; $i < TOTAL_REACTIONS_FOR_COMMENT; $i++) {
    $user = getRandomUser($accountManager);
    $comment = getRandomComment($commentManager);

    if (!$comment instanceof CommentInterface || !$user instanceof AccountInterface) {
        continue;
    }

    $reaction = new Reaction();
    $reaction
        ->setComment($comment)
        ->setViewer($user)
        ->setEmotion(ReactionInterface::SUPPORT_REACTIONS[array_rand(ReactionInterface::SUPPORT_REACTIONS)]);

    try {
        $reactionManager->save($reaction);
        writeln(sprintf('###### Reaction %s', $reaction->getId()));
    } catch (\Exception $e) {
    }

    $reaction = null;
    $user = null;
    $comment = null;
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

/**
 * @param CommentManagerInterface $commentManager
 * @return CommentInterface
 */
function getRandomComment(CommentManagerInterface $commentManager)
{
    $randomAccountId = rand(1, 1000);
    $comment = $commentManager->find($randomAccountId);

    return $comment;
}