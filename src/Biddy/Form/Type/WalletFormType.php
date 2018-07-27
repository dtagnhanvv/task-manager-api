<?php

namespace Biddy\Form\Type;


use Biddy\Entity\Core\Wallet;
use Biddy\Form\DataTransformer\RoleToUserEntityTransformer;
use Biddy\Model\Core\WalletInterface;
use Biddy\Model\User\Role\AdminInterface;
use Biddy\Model\User\Role\SaleInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WalletFormType extends AbstractRoleSpecificFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id')
            ->add('currency')
            ->add('name')
            ->add('type')
            ->add('validFrom')
            ->add('expiredAt');

        if ($options['userRole'] instanceof AdminInterface || $options['userRole'] instanceof SaleInterface) {
            $builder->add(
                $builder->create('owner')
                    ->addModelTransformer(new RoleToUserEntityTransformer(), false)
            );
        };

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var WalletInterface $wallet */
                $wallet = $event->getData();
                $form = $event->getForm();
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Wallet::class,
            'userRole' => null
        ]);
    }

    public function getName()
    {
        return 'biddy_form_wallet';
    }
}