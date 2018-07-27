<?php

namespace Biddy\Form\Type;


use Biddy\Entity\Core\Tag;
use Biddy\Model\Core\TagInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagFormType extends AbstractRoleSpecificFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id')
            ->add('name')
            ->add('type')
            ->add('url')
            ->add('parentTag')
            ->add('productTags');

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var TagInterface $tag */
                $tag = $event->getData();
                $form = $event->getForm();
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tag::class,
            'userRole' => null
        ]);
    }

    public function getName()
    {
        return 'biddy_form_tag';
    }
}