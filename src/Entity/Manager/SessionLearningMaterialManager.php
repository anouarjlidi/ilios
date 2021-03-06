<?php

declare(strict_types=1);

namespace App\Entity\Manager;

/**
 * Class SessionLearningMaterialManager
 */
class SessionLearningMaterialManager extends BaseManager
{
    /**
     * @return int
     */
    public function getTotalSessionLearningMaterialCount()
    {
        return $this->em->createQuery('SELECT COUNT(l.id) FROM App\Entity\SessionLearningMaterial l')
            ->getSingleScalarResult();
    }
}
