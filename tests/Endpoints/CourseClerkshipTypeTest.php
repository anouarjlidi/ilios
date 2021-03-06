<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\ReadWriteEndpointTest;

/**
 * CourseClerkshipType API endpoint Test.
 * @group api_4
 */
class CourseClerkshipTypeTest extends ReadWriteEndpointTest
{
    protected $testName =  'courseClerkshipTypes';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'App\Tests\Fixture\LoadCourseClerkshipTypeData',
            'App\Tests\Fixture\LoadCourseData'
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'title' => ['title', $this->getFaker()->text(20)],
            'courses' => ['courses', [3]],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnlyPropertiesToTest()
    {
        return [
            'id' => ['id', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[0, 1], ['id' => [1, 2]]],
            'title' => [[1], ['title' => 'second clerkship type']],
            'courses' => [[0], ['courses' => [1]]],
        ];
    }
}
