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

namespace MickPrev\Duplicator\Tests\Fixtures\Doctrine\ORM;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\UuidGenerator;

class AppUuidGenerator extends UuidGenerator
{
    /**
     * @param object $entity
     */
    public function generate(EntityManager $em, $entity)
    {
        $class = $em->getClassMetadata(\get_class($entity));
        $idField = $class->getIdentifierFieldNames()[0];

        return $class->getFieldValue($entity, $idField) ?: parent::generate($em, $entity);
    }
}
