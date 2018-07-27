<?php


namespace Biddy\Service\Util;

use Biddy\Form\DataTransformer\RoleToUserEntityTransformer;
use Biddy\Form\Type\AuctionFormType;
use Biddy\Form\Type\ProductTagFormType;
use Biddy\Model\Core\AuctionInterface;
use Biddy\Model\Core\ProductInterface;
use Biddy\Model\Product\FreelancerInterface;
use Biddy\Model\Product\ProfessionalInterface;
use Biddy\Model\User\Role\AdminInterface;
use Biddy\Model\User\Role\SaleInterface;
use Biddy\Repository\Core\BidRepositoryInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

trait ProductUtilTrait
{
    use UserUtilTrait;

    /**
     * @param $products
     * @return array
     */
    public function serializeProducts($products)
    {
        $groups = [];

        foreach ($products as $product) {
            if (!$product instanceof ProductInterface) {
                continue;
            }
            $groups[] = $this->serializeProduct($product);
        }

        return $groups;
    }

    /**
     * @param ProductInterface $product
     * @return array
     */
    public function serializeProduct(ProductInterface $product)
    {
        $group = [];
        $group = $this->addBasicInfo($product, $group);
        $group = $this->addFreelancerInfo($product, $group);
        $group = $this->addProfessionalInfo($product, $group);

        return $group;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param $options
     * @return mixed
     */
    public function buildProductForm(FormBuilderInterface $builder, $options)
    {
        $builder
            ->add('address')
            ->add('businessRule')
            ->add('businessSetting')
            ->add('commentVisibility')
            ->add('detail')
            ->add('id')
            ->add('longitude')
            ->add('latitude')
            ->add('mode')
            ->add('subject')
            ->add('summary')
            ->add('visibility');

        if ($options['userRole'] instanceof AdminInterface || $options['userRole'] instanceof SaleInterface) {
            $builder->add(
                $builder->create('seller')
                    ->addModelTransformer(new RoleToUserEntityTransformer(), false)
            );
        };

        $builder->add('productTags', CollectionType::class, [
            'mapped' => true,
            'entry_type' => ProductTagFormType::class,
            'allow_add' => true,
            'allow_delete' => true,
        ]);

        $builder->add('auctions', CollectionType::class, [
            'mapped' => true,
            'entry_type' => AuctionFormType::class,
            'allow_add' => true,
            'allow_delete' => true,
        ]);

        return $builder;
    }

    /**
     * @param AuctionInterface $auction
     * @param BidRepositoryInterface $bidRepository
     * @param $user
     * @return mixed|string
     */
    function getBidStatus(AuctionInterface $auction, BidRepositoryInterface $bidRepository, $user)
    {
        $status = $auction->getStatus();
        if ($status == AuctionInterface::STATUS_BIDDING || empty($status)) {
            return $bidRepository->getOnTopProduct($auction, $user);
        }

        return AuctionInterface::STATUS_CLOSED;
    }


    /**
     * @param ProductInterface $product
     * @param $group
     * @return mixed
     */
    public function addBasicInfo(ProductInterface $product, $group)
    {
        $group['id'] = $product->getId();
        $group['address'] = $product->getAddress();
        $group['longitude'] = $product->getLongitude();
        $group['latitude'] = $product->getLatitude();
        $group['visibility'] = $product->getVisibility();
        $group['commentVisibility'] = $product->getCommentVisibility();
        $group['createdDate'] = $product->getCreatedDate();
        $group['detail'] = $product->getDetail();
        $group['subject'] = $product->getSubject();
        $group['seller'] = $product->getSeller();
        $group['rating'] = number_format($product->getRating(), 1);
        $group['type'] = $product->getType();
        $group['mode'] = $product->getMode();
        $group['businessRule'] = $product->getBusinessRule();
        $group['businessSetting'] = $product->getBusinessSetting();

        return $group;
    }

    /**
     * @param $product
     * @param $group
     * @return mixed
     */
    private function addFreelancerInfo($product, $group)
    {
        if (!$product instanceof FreelancerInterface) {
            return $group;
        }

        $group['requirements'] = $product->getRequirements();
        $group['gender'] = $product->getGender();
        $group['ages'] = $product->getAges();

        return $group;
    }

    /**
     * @param $product
     * @param $group
     * @return mixed
     */
    private function addProfessionalInfo($product, $group)
    {
        if (!$product instanceof ProfessionalInterface) {
            return $group;
        }

        $group['requirements'] = $product->getRequirements();
        $group['gender'] = $product->getGender();
        $group['ages'] = $product->getAges();
        $group['skills'] = $product->getSkills();

        return $group;
    }
}