<?php

/*
 * This file is part of the duplicator project.
 *
 * (c) Mick Prev <support@mickprev.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MickPrev\Duplicator\Behat;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use MickPrev\Duplicator\DuplicatorInterface;
use MickPrev\Duplicator\Exception\MissingGroupsContextException;
use MickPrev\Duplicator\Exception\RootObjectImmutableException;
use MickPrev\Duplicator\Tests\Fixtures\DataFixtures\LoadData;
use MickPrev\Duplicator\Tests\Fixtures\Entity\Product;

/**
 * @author Mick Prev <support@mickprev.fr>
 */
final class DuplicatorAsAServiceContext extends AbstractDuplicatorContext
{
    public const NEW_EASYBREATH_PRODUCT = '72005364-b732-443b-855b-b832934a9e29';

    /** @var DuplicatorInterface */
    private $duplicator;
    /** @var ManagerRegistry */
    private $registry;
    /** @var array */
    private $groups = [];

    public function __construct(DuplicatorInterface $duplicator, ManagerRegistry $registry)
    {
        $this->duplicator = $duplicator;
        $this->registry = $registry;
    }

    public function castEasybreathProduct(): Product
    {
        return $this->getRepository()->find(LoadData::EASYBREATH_PRODUCT);
    }

    public function castNewProduct(): Product
    {
        return $this->getRepository()->find(self::NEW_EASYBREATH_PRODUCT);
    }

    public function handleGroupsInTheContext(string $group): void
    {
        switch ($group) {
            case 'all groups':
                $this->groups = ['write_advice', 'write_product', 'write_technical_information'];
                break;
            case 'an unknown group':
                $this->groups = ['unknown_group'];
                break;
            case 'no group':
                $this->groups = [];
                break;
            case 'only the advice group':
                $this->groups = ['write_advice', 'write_product_advice'];
                break;
        }
    }

    public function iDuplicateTheEasybreathProduct(Product $product): void
    {
        try {
            /** @var Product $newProduct */
            $newProduct = $this->duplicator->duplicate($product, ['groups' => $this->groups]);
            $newProduct->setId(self::NEW_EASYBREATH_PRODUCT);
            $this->saveProduct($newProduct);
        } catch (MissingGroupsContextException | RootObjectImmutableException $e) {
            $this->exception = $e;
        }
    }

    private function getManager(): ObjectManager
    {
        return $this->registry->getManagerForClass(Product::class);
    }

    private function getRepository(): ObjectRepository
    {
        $manager = $this->getManager();
        $manager->clear();

        return $manager->getRepository(Product::class);
    }

    private function saveProduct(Product $product): void
    {
        $manager = $this->getManager();
        $manager->persist($product);
        $manager->flush();
    }
}
