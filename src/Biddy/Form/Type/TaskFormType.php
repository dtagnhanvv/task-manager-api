<?php

namespace Biddy\Form\Type;


use Biddy\Entity\Core\Task;
use Biddy\Model\Core\TaskInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskFormType extends AbstractRoleSpecificFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('project')
            ->add('board')
            ->add('cardNumber')
            ->add('owner')
            ->add('releasePlan')
            ->add('review')
            ->add('reviewer')
            ->add('status')
            ->add('url');

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var TaskInterface $task */
                $task = $event->getData();
                $form = $event->getForm();
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
            'userRole' => null
        ]);
    }

    public function getName()
    {
        return 'biddy_form_task';
    }
}