<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use Symfony\Component\HttpFoundation\Response;
use App\Tests\DataLoader\ProgramYearData;
use App\Tests\PutEndpointTestable;
use App\Tests\PutEndpointTestInterface;
use App\Tests\ReadEndpointTest;

/**
 * Cohort API endpoint Test.
 * @group api_2
 */
class CohortTest extends ReadEndpointTest implements PutEndpointTestInterface
{
    use PutEndpointTestable;

    protected $testName =  'cohorts';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'App\Tests\Fixture\LoadCohortData',
            'App\Tests\Fixture\LoadProgramYearData',
            'App\Tests\Fixture\LoadProgramYearStewardData',
            'App\Tests\Fixture\LoadCourseData',
            'App\Tests\Fixture\LoadLearnerGroupData',
            'App\Tests\Fixture\LoadUserData'
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'title' => ['title', $this->getFaker()->text(60)],
            'courses' => ['courses', [1]],
            'learnerGroups' => ['learnerGroups', [1], $skipped = true],
            'users' => ['users', [1]],
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
            'ids' => [[1, 2], ['id' => [2, 3]]],
            'title' => [[1], ['title' => 'Class of 2018']],
            'programYear' => [[1], ['programYear' => 2]],
            'courses' => [[2], ['courses' => [4]]],
            'learnerGroups' => [[1], ['learnerGroups' => [2]]],
            'users' => [[0], ['users' => [2]]],
            'schools' => [[3], ['schools' => [2]]],
            'startYears' => [[1, 3, 4], ['startYears' => ['2014', '2016']]],
        ];
    }

    public function testPostFails()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $this->createJsonRequest(
            'POST',
            $this->getUrl($this->kernelBrowser, 'ilios_api_post', ['version' => 'v1', 'object' => 'cohorts']),
            json_encode(['cohort' => $data]),
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_GONE);
    }

    public function testCreateWithPutFails()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $this->createJsonRequest(
            'PUT',
            $this->getUrl($this->kernelBrowser, 'ilios_api_put', [
                'version' => 'v1',
                'object' => 'cohorts',
                'id' => $data['id']
            ]),
            json_encode(['cohort' => $data]),
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_GONE);
    }

    public function testDeleteFails()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                $this->kernelBrowser,
                'ilios_api_delete',
                ['version' => 'v1', 'object' => 'cohorts', 'id' => $data['id']]
            ),
            null,
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_GONE);
    }

    /**
     * Unlock program years before attempting to PUT cohorts
     */
    public function testPutForAllData()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $putsToTest = $this->putsToTest();
        $firstPut = array_shift($putsToTest);
        $changeKey = $firstPut[0];
        $changeValue = $firstPut[1];

        foreach ($all as $cohort) {
            $programYearId = $cohort['programYear'];

            $programYear = $this->getProgramYear($programYearId);
            $programYear['locked'] = false;
            $programYear['archived'] = false;
            $this->putOne('programyears', 'programYear', $programYearId, $programYear);
            $cohort[$changeKey] = $changeValue;
            $this->putTest($cohort, $cohort, $cohort['id']);
        }
    }

    /**
     * Get programYear data from loader by id
     * @param int $id
     * @return array
     */
    protected function getProgramYear($id)
    {
        $programYearDataLoader = $this->getContainer()->get(ProgramYearData::class);
        $allProgramYears = $programYearDataLoader->getAll();
        $programYearsById = [];
        foreach ($allProgramYears as $arr) {
            $programYearsById[$arr['id']] = $arr;
        }

        return $programYearsById[$id];
    }
}
