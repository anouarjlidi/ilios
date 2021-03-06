<?php

declare(strict_types=1);

namespace App\Tests\Classes;

use App\Classes\PermissionMatrix;
use App\Tests\TestCase;

/**
 * Class PermissionMatrixTest
 * @package App\Tests\Classes
 */
class PermissionMatrixTest extends TestCase
{
    /**
     * @var PermissionMatrix
     */
    protected $permissionMatrix;

    /**
     * @inheritdoc
     */
    public function setUp(): void
    {
        $this->permissionMatrix = new PermissionMatrix();
    }

    /**
     * @inheritdoc
     */
    public function tearDown(): void
    {
        unset($this->permissionMatrix);
    }

    /**
     * @covers PermissionMatrix::setPermission
     * @covers PermissionMatrix::hasPermission
     * @covers PermissionMatrix::getPermittedRoles
     */
    public function testHasPermission()
    {
        $schoolId = 1;
        $capability = 'foo';
        $role1 = 'lorem';
        $role2 = 'ipsum';
        $role3 = 'dolor';

        $this->assertFalse($this->permissionMatrix->hasPermission($schoolId, $capability, [$role1]));
        $this->assertFalse($this->permissionMatrix->hasPermission($schoolId, $capability, [$role2]));
        $this->assertFalse($this->permissionMatrix->hasPermission($schoolId, $capability, [$role3]));


        $this->permissionMatrix->setPermission($schoolId, $capability, [$role1, $role2]);

        $this->assertTrue($this->permissionMatrix->hasPermission($schoolId, $capability, [$role1]));
        $this->assertTrue($this->permissionMatrix->hasPermission($schoolId, $capability, [$role2]));
        $this->assertTrue($this->permissionMatrix->hasPermission($schoolId, $capability, [$role1, $role2]));
        $this->assertTrue($this->permissionMatrix->hasPermission($schoolId, $capability, [$role1, $role2, $role3]));
        $this->assertTrue($this->permissionMatrix->hasPermission($schoolId, $capability, [$role1, $role3]));
        $this->assertFalse($this->permissionMatrix->hasPermission($schoolId, $capability, [$role3]));
    }

    /**
     * @covers PermissionMatrix::getPermittedRoles
     */
    public function testGetPermittedRoles()
    {
        $schoolId = 1;
        $capability = 'foo';
        $role1 = 'lorem';
        $role2 = 'ipsum';

        $this->assertEmpty($this->permissionMatrix->getPermittedRoles($schoolId, $capability));
        $this->permissionMatrix->setPermission($schoolId, $capability, [$role1, $role2]);
        $permittedRoles = $this->permissionMatrix->getPermittedRoles($schoolId, $capability);
        $this->assertEquals(2, count($permittedRoles));
        $this->assertTrue(in_array($role1, $permittedRoles));
        $this->assertTrue(in_array($role2, $permittedRoles));
    }
}
