<?php

namespace Biddy\DomainManager;

use Biddy\Form\Type\MultiFormInterface;
use Biddy\Model\Core\ProductInterface;
use Biddy\Model\User\Role\AccountInterface;
use Biddy\Repository\Core\MultiRepositoryInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use ReflectionClass;
use Biddy\Exception\InvalidArgumentException;
use Biddy\Model\ModelInterface;
use Symfony\Component\HttpFoundation\Request;

class ProductManager implements ProductManagerInterface
{
    protected $om;
    protected $repositories;
    protected $forms;

    public function __construct(ObjectManager $om, $repositories, $forms)
    {
        $this->om = $om;
        $this->repositories = $repositories;
        $this->forms = $forms;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, ProductInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function getRepositoryByModel($model)
    {
        if ($model instanceof Request) {
            $params = array_merge($model->request->all(), $model->query->all());
            $model = isset($params['type']) ? $params['type'] : null;
        }

        foreach ($this->repositories as $repository) {
            if (!$repository instanceof MultiRepositoryInterface) {
                continue;
            }

            if ($repository->supportsEntity($model)) {
                return $repository;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function getFormTypeByModel($model)
    {
        if ($model instanceof Request) {
            $params = array_merge($model->request->all(), $model->query->all());
            $model = isset($params['type']) ? $params['type'] : null;
        }

        foreach ($this->forms as $form) {
            if (!$form instanceof MultiFormInterface) {
                continue;
            }

            if ($form->supportsEntity($model)) {
                return $form;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $model)
    {
        if (!$model instanceof ProductInterface) throw new InvalidArgumentException('expect ProductInterface object');

        $this->om->persist($model);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $model)
    {
        if (!$model instanceof ProductInterface) throw new InvalidArgumentException('expect ProductInterface object');

        $this->om->remove($model);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function createNew($type = null)
    {
        $repository = $this->getRepositoryByModel($type);
        $entity = new ReflectionClass($repository->getClassName());

        return $entity->newInstance();
    }

    /**
     * @inheritdoc
     */
    public function find($id)
    {
        foreach ($this->repositories as $repository) {
            if (!$repository instanceof ObjectRepository) {
                continue;
            }

            $model = $repository->find($id);

            if ($model instanceof ModelInterface) {
                return $model;
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function all($limit = null, $offset = null)
    {
        foreach ($this->repositories as $repository) {
            if (!$repository instanceof ObjectRepository) {
                continue;
            }

            $models = $repository->findBy($criteria = [], $orderBy = null, $limit, $offset);

            if (!empty($models)) {
                return $models;
            }
        }

        return [];
    }

    /**
     * @inheritdoc
     */
    public function getProductsForAccountQuery(AccountInterface $seller)
    {
        foreach ($this->repositories as $repository) {
            if (!$repository instanceof ObjectRepository) {
                continue;
            }

            try {
                $qb = $repository->getAuctionsForAccountQuery($seller);

                $models = $qb->getQuery()->getResult();

                if (!empty($models)) {
                    return $models;
                }
            } catch (\Exception $e) {

            }
        }

        return [];
    }
}