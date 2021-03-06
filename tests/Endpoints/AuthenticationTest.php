<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use Symfony\Component\HttpFoundation\Response;
use App\Tests\DataLoader\UserData;
use App\Tests\ReadWriteEndpointTest;

/**
 * Authentication API endpoint Test.
 * @group api_5
 */
class AuthenticationTest extends ReadWriteEndpointTest
{
    protected $testName =  'authentications';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'App\Tests\Fixture\LoadAuthenticationData',
            'App\Tests\Fixture\LoadUserData',
            'App\Tests\Fixture\LoadAlertData',
            'App\Tests\Fixture\LoadCourseData',
            'App\Tests\Fixture\LoadLearningMaterialData',
            'App\Tests\Fixture\LoadInstructorGroupData',
            'App\Tests\Fixture\LoadLearnerGroupData',
            'App\Tests\Fixture\LoadIlmSessionData',
            'App\Tests\Fixture\LoadOfferingData',
            'App\Tests\Fixture\LoadPendingUserUpdateData',
            'App\Tests\Fixture\LoadSessionLearningMaterialData',
            'App\Tests\Fixture\LoadReportData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'username' => ['username', $this->getFaker()->text(100)],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnlyPropertiesToTest()
    {
        return [
            'invalidateTokenIssuedBefore' => ['invalidateTokenIssuedBefore', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest()
    {
        return [
            'user' => [[1], ['user' => 2]],
            'username' => [[1], ['username' => 'newuser']],
        ];
    }

    protected function createMany($count)
    {
        $userDataLoader = $this->getContainer()->get(UserData::class);
        $users = $userDataLoader->createMany($count);
        $savedUsers = $this->postMany('users', 'users', $users);

        $dataLoader = $this->getDataLoader();

        $data = array_map(function ($user) use ($dataLoader) {
            $arr = $dataLoader->create();
            $arr['user'] = (string) $user['id'];
            $arr['username'] .= $user['id'];

            return $arr;
        }, $savedUsers);

        return $data;
    }

    protected function compareData(array $expected, array $result)
    {
        unset($expected['passwordSha256']);
        unset($expected['passwordBcrypt']);
        unset($expected['password']);
        $this->assertEquals(
            $expected,
            $result
        );
    }

    public function testPostMultipleAuthenticationWithEmptyPassword()
    {
        $data = $this->createMany(101);
        $data = array_map(function ($arr) {
            unset($arr['password']);
            return $arr;
        }, $data);
        $this->postManyTest($data);
    }

    public function testPostAuthenticationWithEmptyPassword()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        unset($data['password']);

        $this->postTest($data, $data);
    }

    public function testPutAuthenticationWithNewUsernameAndPassword()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        unset($data['passwordSha256']);
        unset($data['passwordBcrypt']);
        $data['username'] = 'somethingnew';
        $data['password'] = 'somethingnew';

        $this->putTest($data, $data, $data['user']);
    }

    public function testPostAuthenticationForUserWithNonPrimarySchool()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $data['user'] = '4';
        $user4 = parent::getOne('users', 'users', 4);
        $this->assertSame($user4['school'], '2', 'User #4 should be in school 2 or this test is garbage');

        $this->postTest($data, $data);
    }

    public function testPostAuthenticationWithNoUsernameOrPassword()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        unset($data['username']);
        unset($data['password']);

        $this->postTest($data, $data);
    }

    /**
     * Overridden because authentication users
     * 'user' as the Primary Key
     * @inheritdoc
     */
    public function testDelete()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $this->deleteTest($data['user']);
    }

    /**
     * Overridden because authentication users
     * 'user' as the Primary Key
     * @inheritdoc
     */
    protected function getOneTest()
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $loader = $this->getDataLoader();
        $data = $loader->getOne();
        $returnedData = $this->getOne($endpoint, $responseKey, $data['user']);
        $this->compareData($data, $returnedData);

        return $returnedData;
    }

    /**
     * Overridden because authentication users
     * 'user' as the Primary Key
     * @dataProvider putsToTest
     * @inheritdoc
     */
    public function testPut($key, $value, $skipped = false)
    {
        if ($skipped) {
            $this->markTestSkipped();
        }

        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        if (array_key_exists($key, $data) and $data[$key] == $value) {
            $this->fail(
                "This value is already set for {$key}. " .
                "Modify " . get_class($this) . '::putsToTest'
            );
        }
        unset($data['passwordSha256']);
        unset($data['passwordBcrypt']);
        $data[$key] = $value;

        $postData = $data;
        $this->putTest($data, $postData, $data['user']);
    }

    /**
     * Overridden because authentication users
     * 'user' as the Primary Key
     * @inheritdoc
     */
    public function testPutForAllData()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $faker = $this->getFaker();
        foreach ($all as $data) {
            $data['username'] = $faker->text(50);
            unset($data['passwordSha256']);
            unset($data['passwordBcrypt']);

            $this->putTest($data, $data, $data['user']);
        }
    }


    /**
     * Overridden because authentication users
     * 'user' as the Primary Key
     * @inheritdoc
     */
    public function testPostMany()
    {
        $data = $this->createMany(10);
        $this->postManyTest($data);
    }

    /**
     * Overridden because authentication users
     * 'user' as the Primary Key
     * @inheritdoc
     */
    protected function postTest(array $data, array $postData)
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $postKey = $this->getCamelCasedSingularName();
        $responseData = $this->postOne($endpoint, $postKey, $responseKey, $postData);
        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getOne($endpoint, $responseKey, $responseData['user']);
        $this->compareData($data, $fetchedResponseData);

        return $fetchedResponseData;
    }

    /**
     * Overridden because authentication users
     * 'user' as the Primary Key
     * @inheritdoc
     */
    protected function putTest(array $data, array $postData, $id, $new = false)
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $singularResponseKey = $this->getCamelCasedSingularName();
        $responseData = $this->putOne($endpoint, $singularResponseKey, $id, $postData);
        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getOne($endpoint, $responseKey, $responseData['user']);
        $this->compareData($data, $fetchedResponseData);

        return $fetchedResponseData;
    }

    /**
     * Overridden because authentication users
     * 'user' as the Primary Key
     * @inheritdoc
     */
    protected function postManyTest(array $data)
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $responseData = $this->postMany($endpoint, $responseKey, $data);
        $ids = array_map(function (array $arr) {
            return $arr['user'];
        }, $responseData);
        $filters = [
            'filters[user]' => $ids,
            'limit' => count($ids)
        ];
        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getFiltered($endpoint, $responseKey, $filters);

        foreach ($data as $i => $datum) {
            $response = $fetchedResponseData[$i];
            $this->compareData($datum, $response);
        }

        return $fetchedResponseData;
    }

    /**
     * Overridden because authentication users
     * 'user' as the Primary Key
     * @inheritdoc
     */
    protected function getOne($endpoint, $responseKey, $userId)
    {
        $url = $this->getUrl(
            $this->kernelBrowser,
            'ilios_api_authentication_get',
            ['version' => 'v1', 'object' => $endpoint, 'userId' => $userId]
        );
        $this->createJsonRequest(
            'GET',
            $url,
            null,
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );

        $response = $this->kernelBrowser->getResponse();

        if (Response::HTTP_NOT_FOUND === $response->getStatusCode()) {
            $this->fail("Unable to load url: {$url}");
        }

        $this->assertJsonResponse($response, Response::HTTP_OK);
        return json_decode($response->getContent(), true)[$responseKey][0];
    }

    /**
     * @inheritdoc
     *
     * @dataProvider readOnlyPropertiesToTest
     */
    public function testPutReadOnly($key = null, $id = null, $value = null, $skipped = false)
    {
        if ($skipped) {
            $this->markTestSkipped();
        }
        if (
            null != $key &&
            null != $id &&
            null != $value
        ) {
            $dataLoader = $this->getDataLoader();
            $data = $dataLoader->getOne();
            if (array_key_exists($key, $data) and $data[$key] == $value) {
                $this->fail(
                    "This value is already set for {$key}. " .
                    "Modify " . get_class($this) . '::readOnlyPropertiesToTest'
                );
            }
            unset($data['passwordSha256']);
            unset($data['passwordBcrypt']);
            $postData = $data;
            $postData[$key] = $value;

            //nothing should change
            $this->putTest($data, $postData, $id);
        }
    }
}
