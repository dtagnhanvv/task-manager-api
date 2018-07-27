<?php

namespace Biddy\Form\Type;


use Biddy\Entity\Core\Comment;
use Biddy\Model\Core\CommentInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Biddy\Form\DataTransformer\RoleToUserEntityTransformer;
use Biddy\Model\User\Role\AdminInterface;

class CommentFormType extends AbstractRoleSpecificFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content')
            ->add('contentType')
            ->add('raw')
            ->add('product')
            ->add('masterComment');

        if ($options['userRole'] instanceof AdminInterface) {
            $builder->add(
                $builder->create('author')
                    ->addModelTransformer(new RoleToUserEntityTransformer(), false)
            );
        };

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var CommentInterface $comment */
                $comment = $event->getData();
                $form = $event->getForm();
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
            'userRole' => null
        ]);
    }

    public function getName()
    {
        return 'biddy_form_comment';
    }
}