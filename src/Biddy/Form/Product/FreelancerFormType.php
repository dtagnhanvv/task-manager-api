<?php

namespace Biddy\Form\Product;


use Biddy\Entity\Product\Freelancer;
use Biddy\Form\Type\AbstractRoleSpecificFormType;
use Biddy\Form\Type\MultiFormInterface;
use Biddy\Model\Core\ProductInterface;
use Biddy\Model\Product\FreelancerInterface;
use Biddy\Service\Util\ProductUtilTrait;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FreelancerFormType extends AbstractRoleSpecificFormType implements MultiFormInterface
{
    use ProductUtilTrait;

    /**
     * @inheritdoc
     */
    public function supportsEntity($type)
    {
        return $type == ProductInterface::TYPE_FREELANCER || $type instanceof FreelancerInterface;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //For product
        $builder = $this->buildProductForm($builder, $options);

        //For freelancer
        $builder
            ->add('requirements')
            ->add('gender')
            ->add('ages');

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var FreelancerInterface $freelancer */
                $freelancer = $event->getData();
                $form = $event->getForm();
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Freelancer::class,
            'userRole' => null,
            'allow_extra_fields' => true,
            'cascade_validation' => true,
        ]);
    }

    public function getName()
    {
        return 'biddy_form_freelancer';
    }
}