<?php

namespace Biddy\Form\Type;


use Biddy\Entity\Core\Alert;
use Biddy\Form\DataTransformer\RoleToUserEntityTransformer;
use Biddy\Model\Core\AlertInterface;
use Biddy\Model\User\Role\AdminInterface;
use Biddy\Model\User\Role\SaleInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AlertFormType extends AbstractRoleSpecificFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code')
            ->add('detail')
            ->add('isRead')
            ->add('type')
            ->add('isSent')
            ->add('targetType')
            ->add('targetId');

        if ($options['userRole'] instanceof AdminInterface || $options['userRole'] instanceof SaleInterface) {
            $builder->add(
                $builder->create('account')
                    ->addModelTransformer(new RoleToUserEntityTransformer(), false)
            );
        };

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var AlertInterface $alert */
                $alert = $event->getData();
                $form = $event->getForm();
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Alert::class,
            'userRole' => null
        ]);
    }

    public function getName()
    {
        return 'biddy_form_alert';
    }
}