<?php

namespace biddy\dev;

use AppKernel;
use Biddy\Bundle\UserBundle\DomainManager\AccountManagerInterface;
use Biddy\DomainManager\CommentManagerInterface;
use Biddy\Entity\Core\Comment;
use Biddy\Model\Core\CommentInterface;
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

const TOTAL_COMMENTS_FOR_COMMENT = 200000;

/** @var EntityManagerInterface $em */
$accountManager = $container->get('biddy_user.domain_manager.account');
$commentManager = $container->get('biddy.domain_manager.comment');

for ($i = 0; $i < TOTAL_COMMENTS_FOR_COMMENT; $i++) {
    $user = getRandomUser($accountManager);
    $masterComment = getRandomComment($commentManager);

    if (!$masterComment instanceof CommentInterface || !$user instanceof AccountInterface) {
        continue;
    }

    $comment = new Comment();
    $comment
        ->setMasterComment($masterComment)
        ->setAuthor($user)
        ->setContent(sprintf("Comment tự động"));

    try {
        $commentManager->save($comment);
        writeln(sprintf('###### Child comment %s', $comment->getId()));
    } catch (\Exception $e) {
    }

    $comment = null;
    $user = null;
    $masterComment = null;

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
