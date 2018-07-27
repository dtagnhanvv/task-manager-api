<?php

namespace Biddy\Bundle\PublicBundle\Controller;

use Biddy\Bundle\UserSystem\AccountBundle\Entity\User;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Service\Util\AccountUtilTrait;
use Biddy\Service\Util\PublicSimpleException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Biddy\Bundle\AdminApiBundle\Handler\UserHandlerInterface;
use Biddy\Bundle\ApiBundle\Controller\RestControllerAbstract;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PublicUserController extends RestControllerAbstract implements ClassResourceInterface
{
    use AccountUtilTrait;

    /**
     * @Rest\Post("/register")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws PublicSimpleException
     */
    public function registerAction(Request $request)
    {
        // 1) build the form
        $user = $this->buildForm($request);

        $accountManager = $this->get('biddy_user.domain_manager.account');
        $messages = [
            'duplicate_email' => $this->getParameter("user_register_duplicate_email"),
            'duplicate_username' => $this->getParameter("user_register_duplicate_username"),
            'duplicate_phone' => $this->getParameter("user_register_duplicate_phone")
        ];

        $this->checkDuplicateUserInfo($user, $accountManager, $messages);

        // 2) Encode the password (you could also do this via Doctrine listener)
        /** @var UserPasswordEncoderInterface $passwordEncoder */
        $passwordEncoder = $this->get('security.password_encoder');
        $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($password);

        // 3) save the User!
        $entityManager = $this->getDoctrine()->getManager();

        try {
            $entityManager->persist($user);
            $entityManager->flush();
        } catch (\Exception $e) {
            throw new PublicSimpleException("Can not register new user");
        }

        // ... do any other work - like sending them an email, etc
        // maybe set a "flash" success message for the user

        return $this->getToken($user);
    }

    /**
     * @inheritdoc
     */
    protected function getResourceName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    protected function getGETRouteName()
    {
        return 'public_1_get_user';
    }

    /**
     * @return UserHandlerInterface
     */
    protected function getHandler()
    {
        return $this->container->get('biddy_public.handler.user');
    }

    public function canonicalize($string)
    {
        if (null === $string) {
            return null;
        }

        $encoding = mb_detect_encoding($string);
        $result = $encoding
            ? mb_convert_case($string, MB_CASE_LOWER, $encoding)
            : mb_convert_case($string, MB_CASE_LOWER);

        return $result;
    }

    /**
     * @param Request $request
     * @return User
     */
    private function buildForm(Request $request)
    {
        $params = array_merge($request->request->all(), $request->query->all());

        $user = new User();
        $user->setEmail($params['email']);
        $user->setEnabled($params['enabled']);
        $user->setFirstName($params['firstName']);
        $user->setLastName($params['lastName']);
        $user->setPhone($params['phone']);
        $user->setPlainPassword($params['plainPassword']);
        $user->setUsername($params['username']);
        $user->setEnabled(true);
        $user->setEnabledModules(User::DEFAULT_MODULES_FOR_ACCOUNT);
        $user->setUsernameCanonical($this->canonicalize($user->getUsername()));
        $user->setEmailCanonical($this->canonicalize($user->getEmail()));

        return $user;
    }

    /**
     * @param AccountInterface $account
     * @return array
     */
    private function getToken(AccountInterface $account)
    {
        $jwtManager = $this->get('lexik_jwt_authentication.jwt_manager');
        $jwtTransformer = $this->get('biddy_api.service.jwt_response_transformer');

        $tokenString = $jwtManager->create($account);

        return $jwtTransformer->transform(['token' => $tokenString], $account);
    }
}
