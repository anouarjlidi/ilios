<?php

declare(strict_types=1);

namespace App\Tests\RelationshipVoter;

use App\RelationshipVoter\AbstractVoter;
use App\RelationshipVoter\ProgramYearSteward as Voter;
use App\Service\PermissionChecker;
use App\Entity\Program;
use App\Entity\ProgramYear;
use App\Entity\ProgramYearSteward;
use App\Entity\School;
use App\Service\Config;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class ProgramYearStewardTest extends AbstractBase
{
    public function setup(): void
    {
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);
    }

    public function testAllowsRootFullAccess()
    {
        $this->checkRootEntityAccess(m::mock(ProgramYearSteward::class));
    }

    public function testCanView()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(ProgramYearSteward::class);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanEdit()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(ProgramYearSteward::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $programYear = m::mock(ProgramYear::class);
        $programYear->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getProgramYear')->andReturn($programYear);
        $program = m::mock(Program::class);
        $program->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getProgram')->andReturn($program);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getProgramOwningSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateProgramYear')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Edit allowed");
    }

    public function testCanNotEdit()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(ProgramYearSteward::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $programYear = m::mock(ProgramYear::class);
        $programYear->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getProgramYear')->andReturn($programYear);
        $program = m::mock(Program::class);
        $program->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getProgram')->andReturn($program);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getProgramOwningSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateProgramYear')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Edit denied");
    }

    public function testCanDelete()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(ProgramYearSteward::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $programYear = m::mock(ProgramYear::class);
        $programYear->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getProgramYear')->andReturn($programYear);
        $program = m::mock(Program::class);
        $program->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getProgram')->andReturn($program);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getProgramOwningSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateProgramYear')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Delete allowed");
    }

    public function testCanNotDelete()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(ProgramYearSteward::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $programYear = m::mock(ProgramYear::class);
        $programYear->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getProgramYear')->andReturn($programYear);
        $program = m::mock(Program::class);
        $program->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getProgram')->andReturn($program);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getProgramOwningSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateProgramYear')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Delete denied");
    }

    public function testCanCreate()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(ProgramYearSteward::class);
        $programYear = m::mock(ProgramYear::class);
        $programYear->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getProgramYear')->andReturn($programYear);
        $program = m::mock(Program::class);
        $program->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getProgram')->andReturn($program);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getProgramOwningSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateProgramYear')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Create allowed");
    }

    public function testCanNotCreate()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(ProgramYearSteward::class);
        $programYear = m::mock(ProgramYear::class);
        $programYear->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getProgramYear')->andReturn($programYear);
        $program = m::mock(Program::class);
        $program->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getProgram')->andReturn($program);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getProgramOwningSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateProgramYear')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Create denied");
    }
}
