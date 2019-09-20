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

use Assert\Assertion;
use Behat\Behat\Context\Context;
use MickPrev\Duplicator\Exception\MissingGroupsContextException;
use MickPrev\Duplicator\Exception\RootObjectImmutableException;
use MickPrev\Duplicator\Tests\Fixtures\DataFixtures\LoadData;
use MickPrev\Duplicator\Tests\Fixtures\Entity\Product;

/**
 * @author Mick Prev <support@mickprev.fr>
 */
abstract class AbstractDuplicatorContext implements Context
{
    /** @var MissingGroupsContextException|RootObjectImmutableException|null */
    protected $exception;

    /**
     * @transform the easybreath product
     */
    abstract public function castEasybreathProduct(): Product;

    /**
     * @transform /^(?:a|the) new product$/
     */
    abstract public function castNewProduct(): Product;

    /**
     * @Given /^I have (all groups|an unknown group|no group|only the advice group) in the context$/
     */
    abstract public function handleGroupsInTheContext(string $group): void;

    /**
     * @When /^I duplicate (the easybreath product)$/
     */
    abstract public function iDuplicateTheEasybreathProduct(Product $product): void;

    /**
     * @Then /^(a new product) should have been created$/
     */
    public function aNewProductShouldHaveBeenCreated(Product $newProduct): void
    {
        Assertion::eq($newProduct->title, 'Easybreath');
        Assertion::notEq($newProduct->getId(), LoadData::EASYBREATH_PRODUCT);
    }

    /**
     * @Then /^(the new product) should have (0|1) category$/
     */
    public function theNewProductShouldHaveCategory(Product $newProduct, int $count): void
    {
        Assertion::eq(isset($newProduct->category), $count);
        if (0 !== $count) {
            Assertion::eq($newProduct->category->getId(), LoadData::SNORKELING_CATEGORY);
            Assertion::eq($newProduct->category->name, 'Snorkeling');
        }
    }

    /**
     * @Then /^(the new product) should have (0|1) advice?$/
     */
    public function theNewProductShouldHaveAdvice(Product $newProduct, int $count): void
    {
        Assertion::count($newProduct->advices, $count);
        if (0 !== $count) {
            $firstAdvice = $newProduct->advices->first();
            Assertion::notEq($firstAdvice->getId(), LoadData::ADVICE_SIZE);
            Assertion::eq($firstAdvice->title, 'Which size should you choose?');
        }
    }

    /**
     * @Then /^(the new product) should have (0|2) technical informations?$/
     */
    public function theNewProductShouldHaveTechnicalInformations(Product $newProduct, int $count): void
    {
        Assertion::count($newProduct->technicalInformations, $count);
        if (0 !== $count) {
            $technicalInformationDimensions = $newProduct->technicalInformations[0];
            Assertion::notEq($technicalInformationDimensions->getId(), LoadData::TECHNICAL_INFORMATION_DIMENSIONS);
            Assertion::eq($technicalInformationDimensions->title, 'DIMENSIONS OF THE MASK');

            $technicalInformationWeight = $newProduct->technicalInformations[1];
            Assertion::notEq($technicalInformationWeight->getId(), LoadData::TECHNICAL_INFORMATION_WEIGHT);
            Assertion::eq($technicalInformationWeight->title, 'WEIGHT');
        }
    }

    /**
     * @Then /^(the new product) should have (0|1) common advantage?$/
     */
    public function theNewProductShouldHaveOnlyCommonAdvantages(Product $newProduct, int $count): void
    {
        Assertion::count($newProduct->advantages, $count);
        if (0 !== $count) {
            $firstAdvantage = $newProduct->advantages->first();
            Assertion::eq($firstAdvantage->getId(), LoadData::COMMON_ADVANTAGE);
            Assertion::eq($firstAdvantage->title, 'Anti fogging');
        }
    }

    /**
     * @Then /^(the new product) should be without author$/
     */
    public function theNewProductShouldBeWithoutAuthor(Product $newProduct): void
    {
        Assertion::null($newProduct->author);
    }

    /**
     * @Then I should have an exception for have no group in the context
     */
    public function iShouldHaveAnExceptionForHaveNoGroupInTheContext(): void
    {
        Assertion::isInstanceOf($this->exception, MissingGroupsContextException::class);
        Assertion::eq($this->exception->getMessage(), 'At least one group is required in the context.');
    }

    /**
     * @Then I should have an exception for have an unknown group in the context
     */
    public function iShouldHaveAnExceptionForHaveAnUnknownGroupInTheContext(): void
    {
        Assertion::isInstanceOf($this->exception, RootObjectImmutableException::class);
        Assertion::eq($this->exception->getMessage(), 'A root object cannot be immutable.');
    }
}
