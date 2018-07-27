<?php

namespace Biddy\Form\Type;


use Biddy\Entity\Core\Reaction;
use Biddy\Model\Core\ReactionInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Biddy\Form\DataTransformer\RoleToUserEntityTransformer;
use Biddy\Model\User\Role\AdminInterface;

class ReactionFormType extends AbstractRoleSpecificFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('emotion')
            ->add('comment')
            ->add('product');

        if ($options['userRole'] instanceof AdminInterface) {
            $builder->add(
                $builder->create('viewer')
                    ->addModelTransformer(new RoleToUserEntityTransformer(), false)
            );
        };

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var ReactionInterface $reaction */
                $reaction = $event->getData();
                $form = $event->getForm();
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reaction::class,
            'userRole' => null
        ]);
    }

    public function getName()
    {
        return 'biddy_form_reaction';
    }
}