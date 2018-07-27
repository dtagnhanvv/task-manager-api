<?php

namespace Biddy\Bundle\PublicBundle\Controller;

use Biddy\Bundle\ApiBundle\Behaviors\GetEntityFromIdTrait;
use Biddy\Bundle\ApiBundle\Controller\RestControllerAbstract;
use Biddy\Handler\HandlerInterface;
use Biddy\Service\Util\CommentUtilTrait;
use Biddy\Service\Util\PublicSimpleException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\UserBundle\Event\GetResponseNullableUserEvent;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Rest\RouteResource("Account")
 */
class ResettingController extends RestControllerAbstract implements ClassResourceInterface
{
    use GetEntityFromIdTrait;
    use CommentUtilTrait;

    /**
     * Get all accounts
     * @Rest\Post("/resetting/sendEmail")
     * @Rest\View(serializerGroups={"account.detail", "user.summary", "account_tag.minimum", "tag.minimum"})
     *
     * @ApiDoc(
     *  section = "Account",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     * @return mixed
     */
    public function postAccountsAction(Request $request)
    {
        $username = $request->request->get('username');

        $accountManager = $this->get('biddy_user.domain_manager.account');
        $user = $accountManager->findUserByUsernameOrEmail($username);

        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        /* Dispatch init event */
        $event = new GetResponseNullableUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        if (null === $user) {
            return $this->render('FOSUserBundle:Resetting:request.html.twig', array(
                'invalid_username' => $username
            ));
        }

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_REQUEST, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        if (null === $user->getConfirmationToken()) {
            /** @var $tokenGenerator TokenGeneratorInterface */
            $tokenGenerator = $this->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }

        /* Dispatch confirm event */
        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_CONFIRM, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }
        $this->get('biddy_api.mailer.mailer')->sendResettingEmailMessage($user);
        $user->setPasswordRequestedAt(new \DateTime());
        $this->get('fos_user.user_manager')->updateUser($user);


        /* Dispatch completed event */
        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_COMPLETED, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        return $user->getEmail();
    }

    /**
     * Reset user password
     * @Rest\Post("resetting/reset/{token}")
     * @Rest\View(serializerGroups={"account.detail", "user.summary", "account_tag.minimum", "tag.minimum"})
     *
     * @ApiDoc(
     *  section = "Account",
     *  resource = true,
     *  statusCodes = {
     *      200 = "Returned when successful"
     *  }
     * )
     *
     * @param Request $request
     * @param $token
     * @return mixed
     * @throws PublicSimpleException
     */
    public function resetAction(Request $request, $token)
    {
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $accountManager = $this->get('biddy_user.domain_manager.account');
        $user = $accountManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $params = array_merge($request->request->all(), $request->query->all());
        if (!isset($params['biddy_user_system_account_resetting_form']['plainPassword'])) {
            throw new PublicSimpleException('Missing new password');
        }

        $plainPasswords = $params['biddy_user_system_account_resetting_form']['plainPassword'];
        if ($plainPasswords['first'] != $plainPasswords['second']) {
            throw new PublicSimpleException('New password not match');
        }

        // 2) Encode the password (you could also do this via Doctrine listener)
        /** @var UserPasswordEncoderInterface $passwordEncoder */
        $passwordEncoder = $this->get('security.password_encoder');

        try {
            $password = $passwordEncoder->encodePassword($user, $plainPasswords['first']);
            $user->setPassword($password);
            $accountManager->save($user);
        } catch (\Exception $e) {
            throw new PublicSimpleException('Can not change user password. Contact system admin for for detail');
        }
    }

    /**
     * @return string
     */
    protected function getResourceName()
    {
        return 'account';
    }

    /**
     * The 'get' route name to redirect to after resource creation
     *
     * @return string
     */
    protected function getGETRouteName()
    {
        return 'api_1_get_account';
    }

    /**
     * @return HandlerInterface
     */
    protected function getHandler()
    {
        return $this->container->get('biddy_api.handler.account');
    }
}