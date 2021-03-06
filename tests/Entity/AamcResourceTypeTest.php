<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\AamcResourceType;
use Mockery as m;

/**
 * Tests for Entity AamcResourceType
 * @group model
 */
class AamcResourceTypeTest extends EntityBase
{
    /**
     * @var AamcResourceType
     */
    protected $object;

    /**
     * Instantiate a AamcResourceType object
     */
    protected function setUp(): void
    {
        $this->object = new AamcResourceType();
    }

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'id',
            'title',
            'description'
        );
        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('foo');
        $this->object->setDescription('bar');
        $this->object->setId('baz');
        $this->validate(0);
    }

    /**
     * @covers \App\Entity\AamcResourceType::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getTerms());
    }

    /**
     * @covers \App\Entity\AamcResourceType::setTitle
     * @covers \App\Entity\AamcResourceType::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\AamcResourceType::setDescription
     * @covers \App\Entity\AamcResourceType::getDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers \App\Entity\AamcResourceType::addTerm
     */
    public function testAddTerm()
    {
        $this->entityCollectionAddTest('term', 'Term', false, false, 'addAamcResourceType');
    }

    /**
     * @covers \App\Entity\AamcResourceType::removeTerm
     */
    public function testRemoveTerm()
    {
        $this->entityCollectionRemoveTest('term', 'Term', false, false, false, 'removeAamcResourceType');
    }

    /**
     * @covers \App\Entity\AamcResourceType::getTerms
     */
    public function testGetTerms()
    {
        $this->entityCollectionSetTest('term', 'Term', false, false, 'addAamcResourceType');
    }
}
