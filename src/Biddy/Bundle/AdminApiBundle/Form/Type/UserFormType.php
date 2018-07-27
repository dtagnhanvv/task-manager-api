<?php

namespace Biddy\Bundle\AdminApiBundle\Form\Type;

use Biddy\Bundle\UserBundle\DomainManager\AccountManagerInterface;
use Biddy\Bundle\UserSystem\AccountBundle\Entity\User;
use Biddy\Model\User\Role\UserRoleInterface;
use Biddy\Service\Util\AccountUtilTrait;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Biddy\Form\Type\AbstractRoleSpecificFormType;
use Biddy\Model\User\Role\AdminInterface;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Model\User\UserEntityInterface;

class UserFormType extends AbstractRoleSpecificFormType
{
    use AccountUtilTrait;

    const MODULE_CONFIG = 'moduleConfigs';
    const ABBREVIATION_KEY = 'abbreviation';

    /** @var AccountManagerInterface */
    private $accountManager;
    private $messages;

    /**
     * UserFormType constructor.
     * @param UserRoleInterface $userRole
     * @param AccountManagerInterface $accountManager
     * @param $userRegisterDuplicateEmail
     * @param $userRegisterDuplicateUsername
     * @param $userRegisterDuplicatePhone
     */
    public function __construct(UserRoleInterface $userRole, AccountManagerInterface $accountManager, $userRegisterDuplicateEmail, $userRegisterDuplicateUsername, $userRegisterDuplicatePhone)
    {
        $this->setUserRole($userRole);
        $this->accountManager = $accountManager;

        $this->messages = [
            'duplicate_email' => $userRegisterDuplicateEmail,
            'duplicate_username' => $userRegisterDuplicateUsername,
            'duplicate_phone' => $userRegisterDuplicatePhone
        ];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('plainPassword')
            ->add('firstName')
            ->add('lastName')
            ->add('company')
            ->add('email')
            ->add('phone')
            ->add('city')
            ->add('state')
            ->add('address')
            ->add('postalCode')
            ->add('country')
            ->add('bidders')
            ->add('userGroup')
            ->add('exchanges')
            ->add('emailSendAlert')
            ->add('profileImageUrl')
            ->add('enabledModules', ChoiceType::class, [
                'empty_data' => null,
                'multiple' => true,
                'choices' => \Biddy\Bundle\UserBundle\Entity\User::DEFAULT_MODULES_FOR_ACCOUNT
            ])
            ->add('enabled')
            ->add('userPreferences');

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $account = $event->getData();

                if ($this->userRole instanceof AdminInterface) {
                    if (array_key_exists('enabledModules', $account)) {
                        $modules = $account['enabledModules'];

                        if (null !== $modules && is_array($modules)) {
                            $modules = array_intersect($modules, User::DEFAULT_MODULES_FOR_ACCOUNT);
                            $account['enabledModules'] = $modules;
                            $event->setData($account);
                        }
                    }
                }
            }
        );

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var UserEntityInterface|AccountInterface $account */
                $account = $event->getData();
                $form = $event->getForm();

                $this->checkDuplicateUserInfo($account, $this->accountManager, $this->messages);

                if ($this->userRole instanceof AdminInterface) {
                    if ($account->getId() === null) {
                        $account->generateAndAssignUuid();
                    }

                    $modules = $form->get('enabledModules')->getData();

                    if (null !== $modules && is_array($modules)) {
                        $account->setEnabledModules($modules);
                    }
                }
            }
        );
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => User::class,
                'validation_groups' => ['Admin', 'Default'],
            ]);
    }

    public function getName()
    {
        return 'biddy_form_admin_api_user';
    }
}