<?php

namespace Biddy\Bundle\AdminApiBundle\Form\Type;

use Biddy\Bundle\UserSystem\SaleBundle\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Biddy\Form\Type\AbstractRoleSpecificFormType;
use Biddy\Model\User\Role\AdminInterface;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Model\User\UserEntityInterface;
use Biddy\Service\Util\StringUtilTrait;

class SaleFormType extends AbstractRoleSpecificFormType
{
    use StringUtilTrait;

    const MODULE_CONFIG = 'moduleConfigs';
    const ABBREVIATION_KEY = 'abbreviation';

    public function __construct(UserEntityInterface $userRole)
    {
        $this->setUserRole($userRole);
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
                'choices' => User::DEFAULT_MODULES_FOR_SALE
            ])
            ->add('enabled');

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $account = $event->getData();

                if ($this->userRole instanceof AdminInterface) {
                    if (array_key_exists('enabledModules', $account)) {
                        $modules = $account['enabledModules'];

                        if (null !== $modules && is_array($modules)) {
                            $modules = array_intersect($modules, User::DEFAULT_MODULES_FOR_SALE);
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
                'validation_groups' => ['Sale', 'Default'],
            ]);
    }

    public function getName()
    {
        return 'biddy_form_admin_api_user';
    }
}