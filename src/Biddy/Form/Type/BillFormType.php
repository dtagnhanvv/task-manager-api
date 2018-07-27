<?php

namespace Biddy\Form\Type;

use Biddy\Entity\Core\Bill;
use Biddy\Model\Core\BillInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BillFormType extends AbstractRoleSpecificFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id')
            ->add('payment')
            ->add('status')
            ->add('noteForSeller');

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var BillInterface $bill */
                $bill = $event->getData();
                $form = $event->getForm();

                if (empty($bill->getId())) {
                    $bill->setStatus(BillInterface::STATUS_UNCONFIRMED);
                }

                $this->validateBillStatus($bill->getStatus(), $form);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Bill::class,
            'userRole' => null
        ]);
    }

    public function getName()
    {
        return 'biddy_form_bill';
    }

    /**
     * @param $status
     * @param $form
     * @return bool
     */
    private function validateBillStatus($status, FormInterface $form)
    {
        if (!in_array($status, BillInterface::SUPPORT_STATUS)) {
            $form->addError(new FormError(sprintf('Expect bill status is in %s',
                implode(", ", BillInterface::SUPPORT_STATUS))));

            return false;
        }

        return true;
    }
}