<?php

namespace Biddy\Form\Type;

use Biddy\Entity\Core\CreditTransaction;
use Biddy\Model\Core\CreditTransactionInterface;
use Biddy\Model\Core\WalletInterface;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Model\User\Role\SaleInterface;
use Biddy\Service\Util\PublicSimpleException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Biddy\Model\User\Role\AdminInterface;

class CreditTransactionFormType extends AbstractRoleSpecificFormType
{
    private $creditTransactionAmountNotSet;
    private $creditTransactionInvalidAmount;
    private $creditTransactionNotEnoughCredit;
    private $walletNotExist;

    /**
     * CreditTransactionFormType constructor.
     * @param $creditTransactionAmountNotSet
     * @param $creditTransactionInvalidAmount
     * @param $creditTransactionNotEnoughCredit
     * @param $walletNotExist
     */
    public function __construct($creditTransactionAmountNotSet, $creditTransactionInvalidAmount, $creditTransactionNotEnoughCredit, $walletNotExist)
    {
        $this->creditTransactionAmountNotSet = $creditTransactionAmountNotSet;
        $this->creditTransactionInvalidAmount = $creditTransactionInvalidAmount;
        $this->creditTransactionNotEnoughCredit = $creditTransactionNotEnoughCredit;
        $this->walletNotExist = $walletNotExist;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fromWallet')
            ->add('targetWallet')
            ->add('amount')
            ->add('type')
            ->add('detail');

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var CreditTransactionInterface $creditTransaction */
                $creditTransaction = $event->getData();
                $form = $event->getForm();

                $this->validateAmount($creditTransaction);
                $creditTransaction = $this->setTypeForCreditTransaction($creditTransaction);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CreditTransaction::class,
            'userRole' => null
        ]);
    }

    public function getName()
    {
        return 'biddy_form_credit_transaction';
    }

    /**
     * @param $creditTransaction
     * @throws PublicSimpleException
     */
    private function validateAmount(CreditTransactionInterface $creditTransaction)
    {
        if (!is_numeric($creditTransaction->getAmount())) {
            throw new PublicSimpleException($this->creditTransactionAmountNotSet);
        }

        if ($creditTransaction->getAmount() <= 0) {
            throw new PublicSimpleException($this->creditTransactionInvalidAmount);
        }

        $fromWallet = $creditTransaction->getFromWallet();
        if (!$fromWallet instanceof WalletInterface) {
            throw new PublicSimpleException($this->walletNotExist);
        }

        if ($fromWallet->getOwner() instanceof AdminInterface) {
            return;
        }

        if (empty($fromWallet->getCredit()) || $fromWallet->getCredit() < $creditTransaction->getAmount()) {
            throw new PublicSimpleException($this->creditTransactionNotEnoughCredit);
        }
    }

    /**
     * @param CreditTransactionInterface $creditTransaction
     * @return CreditTransactionInterface
     */
    private function setTypeForCreditTransaction(CreditTransactionInterface $creditTransaction)
    {
        $fromWallet = $creditTransaction->getFromWallet();
        $targetWallet = $creditTransaction->getTargetWallet();

        $type = sprintf("from %s to %s", $this->getUserType($fromWallet->getOwner()), $this->getUserType($targetWallet->getOwner()));
        $creditTransaction->setType($type);

        return $creditTransaction;
    }

    /**
     * @param $user
     * @return string
     */
    private function getUserType($user)
    {
        if ($user instanceof AdminInterface) {
            return 'admin';
        }
        if ($user instanceof SaleInterface) {
            return 'sale';
        }
        if ($user instanceof AccountInterface) {
            return 'account';
        }

        return 'undefined';
    }
}