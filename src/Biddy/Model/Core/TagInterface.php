<?php

namespace Biddy\Model\Core;

use Biddy\Model\ModelInterface;

interface TagInterface extends ModelInterface
{
    /**
     * @param $deletedAt
     * @return ProductInterface
     */
    public function setDeletedAt($deletedAt);

    /**
     * @return mixed
     */
    public function getDeletedAt();

    /**
     * @return mixed
     */
    public function getCreatedDate();

    /**
     * @param mixed $createdDate
     * @return ProductInterface
     */
    public function setCreatedDate($createdDate);

    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param mixed $id
     * @return self
     */
    public function setId($id);

    /**
     * @return mixed
     */
    public function getName();

    /**
     * @param mixed $name
     * @return self
     */
    public function setName($name);

    /**
     * @return mixed
     */
    public function getType();

    /**
     * @param mixed $type
     * @return self
     */
    public function setType($type);

    /**
     * @return mixed
     */
    public function getUrl();

    /**
     * @param mixed $url
     * @return self
     */
    public function setUrl($url);

    /**
     * @return \Biddy\Model\Core\TagInterface
     */
    public function getParentTag();

    /**
     * @param \Biddy\Model\Core\TagInterface $parentTag
     * @return self
     */
    public function setParentTag($parentTag);

    /**
     * @return TagInterface[]
     */
    public function getChildTags();

    /**
     * @param TagInterface[] $childTags
     * @return self
     */
    public function setChildTags($childTags);

    /**
     * @return ProductTagInterface[]
     */
    public function getProductTags();

    /**
     * @param ProductTagInterface[] $productTags
     * @return self
     */
    public function setProductTags($productTags);
}