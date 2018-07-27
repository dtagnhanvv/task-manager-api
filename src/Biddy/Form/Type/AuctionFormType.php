<?php

namespace Biddy\Form\Type;


use Biddy\Entity\Core\Auction;
use Biddy\Model\Core\AuctionInterface;
use Biddy\Service\Util\PublicSimpleException;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuctionFormType extends AbstractRoleSpecificFormType
{
    private $paymentNotSupported;

    /**
     * AuctionFormType constructor.
     * @param $paymentNotSupported
     */
    public function __construct($paymentNotSupported)
    {
        $this->paymentNotSupported = $paymentNotSupported;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id')
            ->add('type')
            ->add('startDate')
            ->add('endDate')
            ->add('minimumPrice')
            ->add('showBid')
            ->add('product')
            ->add('objective')
            ->add('incrementType')
            ->add('incrementValue')
            ->add('payment');

        $builder->add('bids', CollectionType::class, [
            'mapped' => true,
            'entry_type' => BidFormType::class,
            'allow_add' => true,
            'allow_delete' => true,
        ]);

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var AuctionInterface $auction */
                $auction = $event->getData();
                $form = $event->getForm();

                $this->validatePayment($auction->getPayment(), $form);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Auction::class,
            'userRole' => null
        ]);
    }

    public function getName()
    {
        return 'biddy_form_auction';
    }

    private function validatePayment($payment, FormInterface $form)
    {
        if (!in_array($payment, AuctionInterface::SUPPORT_PAYMENTS)) {
            throw new PublicSimpleException($this->paymentNotSupported);
        }
    }
}