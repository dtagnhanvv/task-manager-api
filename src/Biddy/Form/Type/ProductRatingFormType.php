<?php

namespace Biddy\Form\Type;

use Biddy\Entity\Core\ProductRating;
use Biddy\Model\Core\BillInterface;
use Biddy\Model\Core\ProductRatingInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Biddy\Form\DataTransformer\RoleToUserEntityTransformer;
use Biddy\Model\User\Role\AdminInterface;

class ProductRatingFormType extends AbstractRoleSpecificFormType
{
    private $maxRating;
    private $minRating;

    /**
     * ProductRatingFormType constructor.
     * @param $maxRating
     * @param $minRating
     */
    public function __construct($maxRating, $minRating)
    {
        $this->maxRating = $maxRating;
        $this->minRating = $minRating;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id')
            ->add('product')
            ->add('bill')
            ->add('rateValue')
            ->add('rateMessage');

        if ($options['userRole'] instanceof AdminInterface) {
            $builder->add(
                $builder->create('reviewer')
                    ->addModelTransformer(new RoleToUserEntityTransformer(), false)
            );
        };

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var ProductRatingInterface $productRating */
                $productRating = $event->getData();
                $form = $event->getForm();

                $this->validateRateValue($productRating->getRateValue(), $form);
                $bill = $productRating->getBill();
                if ($bill instanceof BillInterface) {
                    $bill->setProductRating($productRating);
                    $productRating->setBill($bill);
                }
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductRating::class,
            'userRole' => null
        ]);
    }

    public function getName()
    {
        return 'biddy_form_product_rating';
    }

    /**
     * @param $rateValue
     * @param FormInterface $form
     */
    private function validateRateValue($rateValue, FormInterface $form)
    {
        if (!is_numeric($rateValue) || $rateValue < $this->minRating || $rateValue > $this->maxRating) {
            $form->addError(new FormError(sprintf('Expect rate value in (%s)', implode(", ", [$this->minRating, $this->maxRating]))));
        }
    }
}