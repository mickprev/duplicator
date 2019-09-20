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

use MickPrev\Duplicator\ChainDuplicator;
use MickPrev\Duplicator\DefaultDuplicator;
use MickPrev\Duplicator\DuplicatorInterface;
use MickPrev\Duplicator\Exception\MissingGroupsContextException;
use MickPrev\Duplicator\Exception\RootObjectImmutableException;
use MickPrev\Duplicator\IterableDuplicator;
use MickPrev\Duplicator\ObjectDuplicator;
use MickPrev\Duplicator\Tests\Fixtures\DataFixtures\LoadData;
use MickPrev\Duplicator\Tests\Fixtures\Duplicator\AdvantageDuplicator;
use MickPrev\Duplicator\Tests\Fixtures\Entity\Product;

/**
 * @author Mick Prev <support@mickprev.fr>
 */
final class DuplicatorAsALibraryContext extends AbstractDuplicatorContext
{
    /** @var DuplicatorInterface */
    private $duplicator;
    /** @var Product */
    private $newProduct;
    /** @var array */
    private $groups = [];

    public function __construct()
    {
        $this->duplicator = new ChainDuplicator(
            [
                new AdvantageDuplicator(),
                new IterableDuplicator(),
                new ObjectDuplicator(),
                new DefaultDuplicator(),
            ]
        );
    }

    public function castEasybreathProduct(): Product
    {
        return LoadData::getProductEasybreath();
    }

    public function castNewProduct(): Product
    {
        return $this->newProduct;
    }

    public function handleGroupsInTheContext(string $group): void
    {
        switch ($group) {
            case 'all groups':
                $this->groups = ['duplicate', 'duplicate_advice', 'duplicate_technical_information'];
                break;
            case 'an unknown group':
                $this->groups = ['unknown_group'];
                break;
            case 'no group':
                $this->groups = [];
                break;
            case 'only the advice group':
                $this->groups = ['duplicate_advice', 'duplicate_product_advice'];
                break;
        }
    }

    public function iDuplicateTheEasybreathProduct(Product $product): void
    {
        try {
            $this->newProduct = $this->duplicator->duplicate($product, ['groups' => $this->groups]);
        } catch (MissingGroupsContextException | RootObjectImmutableException $e) {
            $this->exception = $e;
        }
    }
}
