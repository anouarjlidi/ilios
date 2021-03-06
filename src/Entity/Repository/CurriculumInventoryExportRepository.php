<?php

declare(strict_types=1);

namespace App\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class CurriculumInventoryExportRepository
 */
class CurriculumInventoryExportRepository extends EntityRepository implements DTORepositoryInterface
{
    public function findDTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        throw new \Exception('DTOs for CurriculumInventoryExports are not implemented yet');
    }
}
