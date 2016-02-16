<?php

namespace Ilios\CoreBundle\Tests\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\Manager\VocabularyManagerInterface;
use Ilios\CoreBundle\Entity\VocabularyInterface;

/**
 * Class LoadVocabularyDataTest
 * @package Ilios\CoreBundle\Tests\DataFixtures\ORM
 */
class LoadVocabularyDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'ilioscore.vocabulary.manager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'Ilios\CoreBundle\DataFixtures\ORM\LoadVocabularyData',
        ];
    }

    /**
     * @covers Ilios\CoreBundle\DataFixtures\ORM\LoadVocabularyData::load
     */
    public function testLoad()
    {
        $this->runTestLoad('vocabulary.csv');
    }

    /**
     * @param array $data
     * @param VocabularyInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `vocabulary_id`,`title`,`school_id`
        $this->assertEquals($data[0], $entity->getId());
        $this->assertEquals($data[1], $entity->getTitle());
        $this->assertEquals($data[2], $entity->getSchool()->getId());
    }

    /**
     * @param array $data
     * @return VocabularyInterface
     * @override
     */
    protected function getEntity(array $data)
    {
        /**
         * @var VocabularyManagerInterface $em
         */
        $em = $this->em;
        return $em->findVocabularyBy(['id' => $data[0]]);
    }
}