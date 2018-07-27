<?php

namespace Biddy\Form\Type;


use Biddy\Entity\Core\Product;
use Biddy\Model\Core\AuctionInterface;
use Biddy\Model\Core\ProductInterface;
use Biddy\Model\Core\ProductTagInterface;
use Biddy\Service\Util\ProductUtilTrait;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductFormType extends AbstractRoleSpecificFormType implements MultiFormInterface
{
    use ProductUtilTrait;
    
    /**
     * @inheritdoc
     */
    public function supportsEntity($type)
    {
        return $type == ProductInterface::TYPE_PRODUCT || $type instanceof ProductInterface;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //For product
        $builder = $this->buildProductForm($builder, $options);

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                /** @var ProductInterface $product */
                $product = $event->getData();
                $form = $event->getForm();

                $this->validateBusinessRule($product->getBusinessRule(), $form);
                $this->validateBusinessSetting($product->getBusinessSetting(), $form);
                $this->validateMode($product->getMode(), $form);
                $this->validateCommentVisibility($product->getCommentVisibility(), $form);

                $productTags = $product->getProductTags();
                foreach ($productTags as &$productTag) {
                    if (!$productTag instanceof ProductTagInterface) {
                        continue;
                    }

                    $productTag->setProduct($product);
                }

                $auctions = $product->getAuctions();
                foreach ($auctions as &$auction) {
                    if (!$auction instanceof AuctionInterface) {
                        continue;
                    }

                    $auction->setProduct($product);
                }
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'userRole' => null,
            'allow_extra_fields' => true
        ]);
    }

    public function getName()
    {
        return 'biddy_form_product';
    }

    /**
     * @param $businessRule
     * @param FormInterface $form
     * @return bool
     */
    private function validateBusinessRule($businessRule, FormInterface $form)
    {
        if (!in_array($businessRule, ProductInterface::SUPPORT_BUSINESS_RULES)) {
            $form->addError(new FormError(sprintf('Expect product businessRule is in %s', implode(", ", ProductInterface::SUPPORT_BUSINESS_RULES))));

            return false;
        }

        return true;
    }

    /**
     * @param $businessSetting
     * @param FormInterface $form
     * @return bool
     */
    private function validateBusinessSetting($businessSetting, FormInterface $form)
    {
        if (!in_array($businessSetting, ProductInterface::SUPPORT_BUSINESS_SETTINGS)) {
            $form->addError(new FormError(
                sprintf('Expect product businessSetting is in %s', implode(", ", ProductInterface::SUPPORT_BUSINESS_SETTINGS))));

            return false;
        }

        return true;
    }

    /**
     * @param $mode
     * @param FormInterface $form
     * @return bool
     */
    private function validateMode($mode, FormInterface $form)
    {
        if (!in_array($mode, ProductInterface::SUPPORT_MODES)) {
            $form->addError(new FormError(sprintf('Expect product mode is in %s', implode(", ", ProductInterface::SUPPORT_MODES))));

            return false;
        }

        return true;
    }

    /**
     * @param $commentVisibility
     * @param FormInterface $form
     * @return bool
     */
    private function validateCommentVisibility($commentVisibility, FormInterface $form)
    {
        if (!in_array($commentVisibility, ProductInterface::SUPPORT_COMMENT_VISIBILITIES)) {
            $form->addError(new FormError(
                sprintf('Expect product commentVisibility is in %s', 
                    implode(", ", ProductInterface::SUPPORT_COMMENT_VISIBILITIES))));

            return false;
        }

        return true;
    }
}