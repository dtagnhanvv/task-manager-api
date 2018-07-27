<?php

namespace Biddy\Form\Type;


use Biddy\Entity\Core\ProductTag;
use Biddy\Model\Core\ProductTagInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductTagFormType extends AbstractRoleSpecificFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product');

        $builder->add('tag', TagFormType::class);

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var ProductTagInterface $productTag */
                $productTag = $event->getData();
                $form = $event->getForm();
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductTag::class,
            'userRole' => null
        ]);
    }

    public function getName()
    {
        return 'biddy_form_product_tag';
    }
}