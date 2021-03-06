<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\SessionDescription;
use Mockery as m;

/**
 * Tests for Entity SessionDescription
 * @group model
 */
class SessionDescriptionTest extends EntityBase
{
    /**
     * @var SessionDescription
     */
    protected $object;

    /**
     * Instantiate a SessionDescription object
     */
    protected function setUp(): void
    {
        $this->object = new SessionDescription();
    }

    /**
     * @covers \App\Entity\SessionDescription::setDescription
     * @covers \App\Entity\SessionDescription::getDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers \App\Entity\SessionDescription::setSession
     * @covers \App\Entity\SessionDescription::getSession
     */
    public function testSetSession()
    {
        $this->entitySetTest('session', 'Session');
    }
}
