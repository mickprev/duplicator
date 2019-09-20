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

namespace MickPrev\Duplicator\Tests\Fixtures\DataFixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use MickPrev\Duplicator\Tests\Fixtures\Entity\Advantage;
use MickPrev\Duplicator\Tests\Fixtures\Entity\Advice;
use MickPrev\Duplicator\Tests\Fixtures\Entity\Category;
use MickPrev\Duplicator\Tests\Fixtures\Entity\Person;
use MickPrev\Duplicator\Tests\Fixtures\Entity\Product;
use MickPrev\Duplicator\Tests\Fixtures\Entity\TechnicalInformation;

class LoadData implements FixtureInterface
{
    public const EASYBREATH_PRODUCT = '4096488c-59fa-4dc0-abde-4366f013368d';
    public const SNORKELING_CATEGORY = 'b21325d4-9916-440e-a795-bdfb8bff42c8';
    public const COMMON_ADVANTAGE = '3c0686f7-7e73-4f68-9b9c-2b5bc9c09bb0';
    public const ADVICE_SIZE = 'eede6349-90ec-446c-b0cd-add60b43a3e1';
    public const TECHNICAL_INFORMATION_DIMENSIONS = '811fcd9f-e874-4ed5-8848-b61ac042d25a';
    public const TECHNICAL_INFORMATION_WEIGHT = '4794359f-35e6-48e9-b4eb-309cd838259d';

    public function load(ObjectManager $manager): void
    {
        $product = self::getProductEasybreath();
        $manager->persist($product->category);
        $manager->persist($product->author);
        $manager->persist($product);
        $manager->flush();
    }

    public static function getProductEasybreath(): Product
    {
        $product = new Product();
        $product->setId(self::EASYBREATH_PRODUCT);
        $product->title = 'Easybreath';
        $product->description = 'The Easybreath Mask is a revolutionary product and the winner of the Decathlon Innovation Awards in 2014. It allows you to see under the water while breathing naturally. Because it\'s so comfortable and limits water entry through the top of the snorkel, they\'ll be able to build their confidence quickly.';
        $product->category = self::getCategorySnorkeling();
        $product->author = self::getAuthorMichMich();
        $product->addAdvantage(self::getAdvantageEasyBreathing());
        $product->addAdvantage(self::getAdvantageAntiFogging());
        $product->addAdvice(self::getAdviceSize());
        $product->addTechnicalInformation(self::getTechnicalInformationDimensions());
        $product->addTechnicalInformation(self::getTechnicalInformationWeight());

        return $product;
    }

    public static function getCategorySnorkeling(): Category
    {
        $category = new Category();
        $category->setId(self::SNORKELING_CATEGORY);
        $category->name = 'Snorkeling';
        $category->descrption = 'A little description';

        return $category;
    }

    public static function getAdvantageEasyBreathing(): Advantage
    {
        $advantage = new Advantage();
        $advantage->setId('12804d6d-3f67-4355-b608-c32c678ab4af');
        $advantage->title = 'Easy breathing';
        $advantage->description = 'Full mask for natural breathing through the nose and/or mouth.';
        $advantage->icon = '/icons/easy_breathing';
        $advantage->common = false;

        return $advantage;
    }

    public static function getAdvantageAntiFogging(): Advantage
    {
        $advantage = new Advantage();
        $advantage->setId(self::COMMON_ADVANTAGE);
        $advantage->title = 'Anti fogging';
        $advantage->description = 'An exclusive air circulation concept that prevents the formation of fog.';
        $advantage->icon = '/icons/anti_fogging';

        return $advantage;
    }

    public static function getTechnicalInformationDimensions(): TechnicalInformation
    {
        $technicalInformation = new TechnicalInformation();
        $technicalInformation->setId(self::TECHNICAL_INFORMATION_DIMENSIONS);
        $technicalInformation->title = 'DIMENSIONS OF THE MASK';
        $technicalInformation->description = 'Height: 28 cm.<br>Width: 18 cm.<br>Thickness: 12 cm.';

        return $technicalInformation;
    }

    public static function getTechnicalInformationWeight(): TechnicalInformation
    {
        $technicalInformation = new TechnicalInformation();
        $technicalInformation->setId(self::TECHNICAL_INFORMATION_WEIGHT);
        $technicalInformation->title = 'WEIGHT';
        $technicalInformation->description = 'S/M: 0.7 kg<br>M/L: 0.7 kg';

        return $technicalInformation;
    }

    public static function getAdviceSize(): Advice
    {
        $advice = new Advice();
        $advice->setId(self::ADVICE_SIZE);
        $advice->title = 'Which size should you choose?';
        $advice->summary = 'The best idea is to try both sizes in your Decathlon store. Once the mask is tight on your face, it\'s the right size if there\'s no space between your chin and the mask\'s silicone skirt.';
        $advice->description = <<<EOT
The best idea is to try both sizes in your Decathlon store. Once the mask is tight on your face, it's the right size if there's no space between your chin and the mask's silicone skirt.<br/>
If you can\'t get to the store, or if you prefer to order your Easybreath over the internet, here\'s how to choose your size.<br/>
Close your mouth and measure the distance between the trough at the top of your nose and the bottom of your chin :<br>
- Size XS for children over 6 years old.<br>
- Size S/M for adults with thin faces, women, and children over 10 years old.<br>
- Size M/L is the average size for an adult male.<br>
EOT;
        $advice->image = '/pictures/easybreath/size';

        return $advice;
    }

    public static function getAuthorMichMich(): Person
    {
        $person = new Person();
        $person->setId('02c27e31-295c-4810-b543-6ce7d5035ce0');
        $person->email = 'michmich.du.62@fake.com';
        $person->givenName = 'Michel';
        $person->familyName = 'Hmmm';

        return $person;
    }
}
