<?php

declare(strict_types=1);

namespace App\Tests\DataFixtures\ORM;

use App\Entity\MeshPreviousIndexingInterface;

/**
 * Class LoadMeshPreviousIndexingDataTest
 */
class LoadMeshPreviousIndexingDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'App\Entity\Manager\MeshPreviousIndexingManager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
          'App\DataFixtures\ORM\LoadMeshPreviousIndexingData',
        ];
    }

    /**
     * @covers \App\DataFixtures\ORM\LoadMeshPreviousIndexingData::load
     * @group mesh_data_import
     */
    public function testLoad()
    {
        $this->runTestLoad('mesh_previous_indexing.csv', 10);
    }

    /**
     * @param array $data
     * @param MeshPreviousIndexingInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `mesh_descriptor_uid`,`previous_indexing`,`mesh_previous_indexing_id`
        $this->assertEquals($data[0], $entity->getDescriptor()->getId());
        $this->assertEquals($data[1], $entity->getPreviousIndexing());
        $this->assertEquals($data[2], $entity->getId());
    }

    /**
     * @inheritdoc
     */
    protected function getEntity(array $data)
    {
        $em = $this->em;
        return $em->findOneBy(['id' => $data[2]]);
    }
}
