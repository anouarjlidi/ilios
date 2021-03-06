<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\GetUrlTrait;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\Traits\JsonControllerTest;

/**
 * Class CurriculumInventoryDownloadControllerTest
 */
class CurriculumInventoryDownloadControllerTest extends WebTestCase
{
    use JsonControllerTest;
    use FixturesTrait;
    use GetUrlTrait;

    /**
     * @inheritdoc
     */
    public function setUp(): void
    {
        $this->loadFixtures([
            'App\Tests\Fixture\LoadCurriculumInventoryReportData',
            'App\Tests\Fixture\LoadCurriculumInventoryExportData',
            'App\Tests\Fixture\LoadCurriculumInventoryInstitutionData',
            'App\Tests\Fixture\LoadCurriculumInventorySequenceData',
            'App\Tests\Fixture\LoadCurriculumInventorySequenceBlockData',
            'App\Tests\Fixture\LoadCurriculumInventoryAcademicLevelData',
            'App\Tests\Fixture\LoadSessionData',
            'App\Tests\Fixture\LoadAuthenticationData',
        ]);
    }

    /**
     * @covers \App\Controller\CurriculumInventoryDownloadController::getAction
     */
    public function testGetCurriculumInventoryDownload()
    {
        $client = static::createClient();
        $curriculumInventoryExport = $client->getContainer()
            ->get('App\Tests\DataLoader\CurriculumInventoryExportData')
            ->getOne()
        ;

        $this->makeJsonRequest(
            $client,
            'GET',
            $this->getUrl(
                $client,
                'ilios_api_get',
                [
                    'version' => 'v1',
                    'object' => 'curriculuminventoryreports',
                    'id' => $curriculumInventoryExport['report']
                ]
            ),
            null,
            $this->getAuthenticatedUserToken($client)
        );

        $response = $client->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_OK);
        $data = json_decode($response->getContent(), true)['curriculumInventoryReports'][0];

        $client->request(
            'GET',
            $data['absoluteFileUri']
        );

        $response = $client->getResponse();
        $this->assertEquals($curriculumInventoryExport['document'], $response->getContent());
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), $response->getContent());
        $downloadCookie = null;
        $cookieName = 'report-download-' . $curriculumInventoryExport['report'];
        foreach ($response->headers->getCookies() as $cookie) {
            if ($cookieName === $cookie->getName()) {
                $downloadCookie = $cookie;
                break;
            }
        }
        $this->assertNotNull($downloadCookie);
    }
}
