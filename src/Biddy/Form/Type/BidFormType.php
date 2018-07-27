<?php

namespace Biddy\Form\Type;

use Biddy\Entity\Core\Bid;
use Biddy\Model\Core\BidInterface;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Service\Util\PublicSimpleException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BidFormType extends AbstractRoleSpecificFormType
{
    private $creditTransactionNotEnoughCredit;

    /**
     * BidFormType constructor.
     * @param $creditTransactionNotEnoughCredit
     */
    public function __construct($creditTransactionNotEnoughCredit)
    {
        $this->creditTransactionNotEnoughCredit = $creditTransactionNotEnoughCredit;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id')
            ->add('price')
            ->add('category')
            ->add('quantity')
            ->add('auction');

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var BidInterface $bid */
                $bid = $event->getData();
                $form = $event->getForm();

                $this->validateBuyer($bid, $form);
                $this->validateBuyerCredit($bid);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Bid::class,
            'userRole' => null
        ]);
    }

    public function getName()
    {
        return 'biddy_form_bid';
    }

    /**
     * @param BidInterface $bid
     * @param FormInterface $form
     */
    private function validateBuyer(BidInterface $bid, FormInterface $form)
    {
        $buyer = $bid->getBuyer();

        if (!$buyer instanceof AccountInterface) {
            $form->addError(new FormError(sprintf('Expect buyer is account')));
        }
    }

    /**
     * @param BidInterface $bid
     * @throws PublicSimpleException
     */
    private function validateBuyerCredit(BidInterface $bid)
    {
        //Do not check price if update bid
        if (!empty($bid->getId())) {
            return;
        }

        //Check price to add new bid
        $credit = $bid->getBuyer()->getBasicWallet()->getCredit();
        if ($credit < $bid->getPrice()) {
            throw new PublicSimpleException($this->creditTransactionNotEnoughCredit);
        }
    }
}