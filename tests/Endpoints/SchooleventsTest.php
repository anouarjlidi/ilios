<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Entity\OfferingInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\DataLoader\CourseData;
use App\Tests\DataLoader\IlmSessionData;
use App\Tests\DataLoader\OfferingData;
use App\Tests\DataLoader\SchoolData;
use App\Tests\DataLoader\SessionData;
use App\Tests\AbstractEndpointTest;
use DateTime;

/**
 * SchooleventsTest API endpoint Test.
 * @group api_2
 */
class SchooleventsTest extends AbstractEndpointTest
{
    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'App\Tests\Fixture\LoadOfferingData',
            'App\Tests\Fixture\LoadIlmSessionData',
            'App\Tests\Fixture\LoadSchoolData',
            'App\Tests\Fixture\LoadLearningMaterialData',
            'App\Tests\Fixture\LoadCourseLearningMaterialData',
            'App\Tests\Fixture\LoadSessionLearningMaterialData',
        ];
    }

    public function testAttachedUserMaterialsAreBlankedForStudents()
    {
        $school = $this->getContainer()->get(SchoolData::class)->getOne();
        $userId = 5;
        $events = $this->getEvents($school['id'], 0, 100000000000, $userId);
        $lms = $events[3]['learningMaterials'];
        $this->assertEquals(9, count($lms));
        foreach ($lms as $lm) {
            $this->assertTrue($lm['isBlanked']);
        }
    }

    public function testGetEvents()
    {
        $school = $this->getContainer()->get(SchoolData::class)->getOne();
        $offerings = $this->getContainer()->get(OfferingData::class)->getAll();
        $ilmSessions = $this->getContainer()->get(IlmSessionData::class)->getAll();
        $courses = $this->getContainer()->get(CourseData::class)->getAll();
        $sessions = $this->getContainer()->get(SessionData::class)->getAll();

        $events = $this->getEvents($school['id'], 0, 100000000000);

        $this->assertEquals(12, count($events), 'Expected events returned');


        $this->assertEquals($events[0]['offering'], 3);
        $this->assertEquals($events[0]['startDate'], $offerings[2]['startDate']);
        $this->assertEquals($events[0]['endDate'], $offerings[2]['endDate']);
        $this->assertEquals($events[0]['courseTitle'], $courses[0]['title']);
        $this->assertEquals($events[0]['courseLevel'], $courses[0]['level']);
        $this->assertEquals(
            array_column($events[0]['cohorts'], 'id'),
            $courses[0]['cohorts'],
            'cohorts correct for event 0'
        );
        $this->assertEquals(
            array_column($events[0]['courseTerms'], 'id'),
            $courses[0]['terms'],
            'course terms correct for event 0'
        );
        $this->assertEquals($events[0]['course'], $courses[0]['id']);
        $this->assertTrue($events[0]['attireRequired'], 'attireRequired is correct for event 0');
        $this->assertTrue($events[0]['equipmentRequired'], 'equipmentRequired is correct for event 0');
        $this->assertTrue($events[0]['supplemental'], 'supplemental is correct for event 0');
        $this->assertTrue($events[0]['attendanceRequired'], 'attendanceRequired is correct for event 0');
        $this->assertEquals(
            count($events[0]['learningMaterials']),
            9,
            'Event 0 has the correct number of learning materials'
        );
        $this->assertEquals(
            $events[0]['instructionalNotes'],
            $sessions[1]['instructionalNotes'],
            'instructional notes is correct for event 0'
        );
        $this->assertEquals(
            array_column($events[0]['sessionTerms'], 'id'),
            $sessions[1]['terms'],
            'session terms is correct for event (d)'
        );
        $this->assertEquals(0, count($events[0]['sessionObjectives']));
        $this->assertEquals(1, count($events[0]['courseObjectives']));
        $this->assertEquals(2, $events[0]['courseObjectives'][0]['id']);
        $this->assertEquals('second objective', $events[0]['courseObjectives'][0]['title']);
        $this->assertEquals(0, $events[0]['courseObjectives'][0]['position']);
        $this->assertEquals(1, count($events[0]['courseObjectives'][0]['competencies']));
        $this->assertEquals(3, $events[0]['courseObjectives'][0]['competencies'][0]);
        $this->assertEquals(2, count($events[0]['competencies']));
        $this->assertEquals(3, $events[0]['competencies'][0]['id']);
        $this->assertEquals('third competency', $events[0]['competencies'][0]['title']);
        $this->assertEquals(1, $events[0]['competencies'][0]['parent']);
        $this->assertEquals(1, $events[0]['competencies'][1]['id']);
        $this->assertEquals('first competency', $events[0]['competencies'][1]['title']);
        $this->assertEquals(null, $events[0]['competencies'][1]['parent']);
        $this->assertEquals(1, count($events[0]['postrequisites']));
        $this->assertEquals(6, $events[0]['postrequisites'][0]['offering']);
        $this->assertEquals(3, $events[0]['postrequisites'][0]['session']);
        $this->assertEmpty($events[0]['prerequisites']);


        $this->assertEquals($events[1]['offering'], 4);
        $this->assertEquals($events[1]['startDate'], $offerings[3]['startDate']);
        $this->assertEquals($events[1]['endDate'], $offerings[3]['endDate']);
        $this->assertEquals($events[1]['courseTitle'], $courses[0]['title']);
        $this->assertEquals($events[1]['courseLevel'], $courses[0]['level']);
        $this->assertEquals(
            array_column($events[1]['cohorts'], 'id'),
            $courses[0]['cohorts'],
            'cohorts correct for event 1'
        );
        $this->assertEquals(
            array_column($events[1]['courseTerms'], 'id'),
            $courses[0]['terms'],
            'course terms correct for event 1'
        );
        $this->assertEquals($events[1]['course'], $courses[0]['id']);
        $this->assertTrue($events[1]['attireRequired'], 'attireRequired is correct for event 1');
        $this->assertTrue($events[1]['equipmentRequired'], 'equipmentRequired is correct for event 1');
        $this->assertTrue($events[1]['supplemental'], 'supplemental is correct for event 1');
        $this->assertTrue($events[1]['attendanceRequired'], 'attendanceRequired is correct for event 1');
        $this->assertEquals(
            count($events[1]['learningMaterials']),
            9,
            'Event 1 has the correct number of learning materials'
        );

        $this->assertEquals(
            $events[1]['instructionalNotes'],
            $sessions[1]['instructionalNotes'],
            'instructional notes is correct for event 1'
        );
        $this->assertEquals(
            array_column($events[1]['sessionTerms'], 'id'),
            $sessions[1]['terms'],
            'session terms is correct for event (d)'
        );
        $this->assertEquals(1, count($events[1]['postrequisites']));
        $this->assertEquals(6, $events[1]['postrequisites'][0]['offering']);
        $this->assertEquals(3, $events[1]['postrequisites'][0]['session']);
        $this->assertEmpty($events[1]['prerequisites']);

        $this->assertEquals($events[2]['offering'], 5);
        $this->assertEquals($events[2]['startDate'], $offerings[4]['startDate']);
        $this->assertEquals($events[2]['endDate'], $offerings[4]['endDate']);
        $this->assertEquals($events[2]['courseTitle'], $courses[0]['title']);
        $this->assertEquals($events[2]['courseLevel'], $courses[0]['level']);
        $this->assertEquals(
            array_column($events[2]['cohorts'], 'id'),
            $courses[0]['cohorts'],
            'cohorts correct for event 2'
        );
        $this->assertEquals(
            array_column($events[2]['courseTerms'], 'id'),
            $courses[0]['terms'],
            'course terms correct for event 2'
        );
        $this->assertEquals($events[2]['course'], $courses[0]['id']);
        $this->assertTrue($events[2]['attireRequired'], 'attireRequired is correct for event 2');
        $this->assertTrue($events[2]['equipmentRequired'], 'equipmentRequired is correct for event 2');
        $this->assertTrue($events[2]['supplemental'], 'supplemental is correct for event 2');
        $this->assertTrue($events[2]['attendanceRequired'], 'attendanceRequired is correct for event 2');
        $this->assertEquals(
            count($events[2]['learningMaterials']),
            9,
            'Event 2 has the correct number of learning materials'
        );
        $this->assertEquals(
            $events[2]['instructionalNotes'],
            $sessions[1]['instructionalNotes'],
            'instructional notes is correct for event 2'
        );
        $this->assertEquals(
            array_column($events[2]['sessionTerms'], 'id'),
            $sessions[1]['terms'],
            'session terms is correct for event (d)'
        );
        $this->assertEquals(1, count($events[2]['postrequisites']));
        $this->assertEquals(6, $events[2]['postrequisites'][0]['offering']);
        $this->assertEquals(3, $events[2]['postrequisites'][0]['session']);
        $this->assertEmpty($events[2]['prerequisites']);

        $this->assertEquals($events[3]['offering'], 6);
        $this->assertEquals($events[3]['startDate'], $offerings[5]['startDate']);
        $this->assertEquals($events[3]['endDate'], $offerings[5]['endDate']);
        $this->assertEquals($events[3]['courseTitle'], $courses[1]['title']);
        $this->assertEquals($events[3]['courseLevel'], $courses[1]['level']);
        $this->assertEquals(
            array_column($events[3]['cohorts'], 'id'),
            $courses[1]['cohorts'],
            'cohorts correct for event 3'
        );
        $this->assertEquals(
            array_column($events[3]['courseTerms'], 'id'),
            $courses[1]['terms'],
            'course terms correct for event 3'
        );
        $this->assertEquals($events[3]['course'], $courses[1]['id']);
        $this->assertFalse($events[3]['attireRequired'], 'attireRequired is correct for event 3');
        $this->assertFalse($events[3]['equipmentRequired'], 'equipmentRequired is correct for event 3');
        $this->assertTrue($events[3]['supplemental'], 'supplemental is correct for event 3');
        $this->assertArrayNotHasKey('attendanceRequired', $events[3], 'attendanceRequired is correct for event 3');
        $this->assertEquals(
            7,
            count($events[3]['learningMaterials']),
            'Event 3 has the correct number of learning materials'
        );
        $this->assertEquals(
            $events[3]['instructionalNotes'],
            $sessions[2]['instructionalNotes'],
            'instructional notes is correct for event 3'
        );
        $this->assertEquals(
            array_column($events[3]['sessionTerms'], 'id'),
            $sessions[2]['terms'],
            'session terms is correct for event (d)'
        );
        $this->assertEmpty($events[3]['postrequisites']);
        $this->assertEquals(3, count($events[3]['prerequisites']));
        $sessionIds = array_unique(array_column($events[3]['prerequisites'], 'session'));
        sort($sessionIds);
        $this->assertEquals([2], $sessionIds);

        $this->assertEquals($events[4]['offering'], 7);
        $this->assertEquals($events[4]['startDate'], $offerings[6]['startDate']);
        $this->assertEquals($events[4]['endDate'], $offerings[6]['endDate']);
        $this->assertEquals($events[4]['courseTitle'], $courses[1]['title']);
        $this->assertEquals($events[4]['courseLevel'], $courses[1]['level']);
        $this->assertEquals(
            array_column($events[4]['cohorts'], 'id'),
            $courses[1]['cohorts'],
            'cohorts correct for event 4'
        );
        $this->assertEquals(
            array_column($events[4]['courseTerms'], 'id'),
            $courses[1]['terms'],
            'course terms correct for event 4'
        );
        $this->assertEquals($events[4]['course'], $courses[1]['id']);
        $this->assertFalse($events[4]['attireRequired'], 'attireRequired is correct for event 4');
        $this->assertFalse($events[4]['equipmentRequired'], 'equipmentRequired is correct for event 4');
        $this->assertTrue($events[4]['supplemental'], 'supplemental is correct for event 4');
        $this->assertArrayNotHasKey('attendanceRequired', $events[4], 'attendanceRequired is correct for event 4');
        $this->assertEquals(
            7,
            count($events[4]['learningMaterials']),
            'Event 4 has the correct number of learning materials'
        );
        $this->assertEquals(
            $events[4]['instructionalNotes'],
            $sessions[2]['instructionalNotes'],
            'instructional notes is correct for event 4'
        );
        $this->assertEquals(
            array_column($events[4]['sessionTerms'], 'id'),
            $sessions[2]['terms'],
            'session terms is correct for event (d)'
        );
        $this->assertEmpty($events[4]['postrequisites']);
        $this->assertEquals(3, count($events[4]['prerequisites']));
        $sessionIds = array_unique(array_column($events[4]['prerequisites'], 'session'));
        sort($sessionIds);
        $this->assertEquals([2], $sessionIds);

        $this->assertEquals($events[5]['ilmSession'], 1);
        $this->assertEquals($events[5]['startDate'], $ilmSessions[0]['dueDate']);
        $this->assertEquals($events[5]['courseTitle'], $courses[1]['title']);
        $this->assertEquals($events[5]['courseLevel'], $courses[1]['level']);
        $this->assertEquals(
            array_column($events[5]['cohorts'], 'id'),
            $courses[1]['cohorts'],
            'cohorts correct for event 5'
        );
        $this->assertEquals(
            array_column($events[5]['courseTerms'], 'id'),
            $courses[1]['terms'],
            'course terms correct for event 5'
        );
        $this->assertEquals($events[5]['course'], $courses[1]['id']);
        $this->assertFalse($events[5]['attireRequired'], 'attireRequired is correct for event 5');
        $this->assertFalse($events[5]['equipmentRequired'], 'equipmentRequired is correct for event 5');
        $this->assertFalse($events[5]['supplemental'], 'supplemental is correct for event 5');
        $this->assertArrayNotHasKey('attendanceRequired', $events[5], 'attendanceRequired is correct for event 5');
        $this->assertEquals(count($events[5]['learningMaterials']), 0, 'Event 5 has no learning materials');
        $this->assertArrayNotHasKey(
            'instructionalNotes',
            $events[5],
            'instructional notes is correct for event 5'
        );
        $this->assertEmpty($events[5]['postrequisites']);
        $this->assertEmpty($events[5]['prerequisites']);

        $this->assertEquals($events[6]['ilmSession'], 2);
        $this->assertEquals($events[6]['startDate'], $ilmSessions[1]['dueDate']);
        $this->assertEquals($events[6]['courseTitle'], $courses[1]['title']);
        $this->assertEquals($events[6]['courseLevel'], $courses[1]['level']);
        $this->assertEquals(
            array_column($events[6]['cohorts'], 'id'),
            $courses[1]['cohorts'],
            'cohorts correct for event 6'
        );
        $this->assertEquals(
            array_column($events[6]['courseTerms'], 'id'),
            $courses[1]['terms'],
            'course terms correct for event 6'
        );
        $this->assertEquals($events[6]['course'], $courses[1]['id']);
        $this->assertFalse($events[6]['attireRequired'], 'attireRequired is correct for event 6');
        $this->assertFalse($events[6]['equipmentRequired'], 'equipmentRequired is correct for event 6');
        $this->assertFalse($events[6]['supplemental'], 'supplemental is correct for event 6');
        $this->assertArrayNotHasKey('attendanceRequired', $events[6], 'attendanceRequired is correct for event 6');
        $this->assertEquals(count($events[6]['learningMaterials']), 0, 'Event 6 has no learning materials');
        $this->assertArrayNotHasKey(
            'instructionalNotes',
            $events[6],
            'instructional notes is correct for event 6'
        );
        $this->assertEmpty($events[6]['postrequisites']);
        $this->assertEmpty($events[6]['prerequisites']);

        $this->assertEquals($events[7]['ilmSession'], 3);
        $this->assertEquals($events[7]['startDate'], $ilmSessions[2]['dueDate']);
        $this->assertEquals($events[7]['courseTitle'], $courses[1]['title']);
        $this->assertEquals($events[7]['courseLevel'], $courses[1]['level']);
        $this->assertEquals(
            array_column($events[7]['cohorts'], 'id'),
            $courses[1]['cohorts'],
            'cohorts correct for event 7'
        );
        $this->assertEquals(
            array_column($events[7]['courseTerms'], 'id'),
            $courses[1]['terms'],
            'course terms correct for event 7'
        );
        $this->assertEquals($events[7]['course'], $courses[1]['id']);
        $this->assertFalse($events[7]['attireRequired'], 'attireRequired is correct for event 7');
        $this->assertFalse($events[7]['equipmentRequired'], 'equipmentRequired is correct for event 7');
        $this->assertFalse($events[7]['supplemental'], 'supplemental is correct for event 7');
        $this->assertArrayNotHasKey('attendanceRequired', $events[7], 'attendanceRequired is correct for event 7');
        $this->assertEquals(count($events[7]['learningMaterials']), 0, 'Event 7 has no learning materials');
        $this->assertArrayNotHasKey(
            'instructionalNotes',
            $events[7],
            'instructional notes is correct for event 7'
        );
        $this->assertEmpty($events[7]['postrequisites']);
        $this->assertEmpty($events[7]['prerequisites']);

        $this->assertEquals($events[8]['ilmSession'], 4);
        $this->assertEquals($events[8]['startDate'], $ilmSessions[3]['dueDate']);
        $this->assertEquals($events[8]['courseTitle'], $courses[1]['title']);
        $this->assertEquals($events[8]['courseLevel'], $courses[1]['level']);
        $this->assertEquals(
            array_column($events[8]['cohorts'], 'id'),
            $courses[1]['cohorts'],
            'cohorts correct for event 8'
        );
        $this->assertEquals(
            array_column($events[8]['courseTerms'], 'id'),
            $courses[1]['terms'],
            'course terms correct for event 8'
        );
        $this->assertEquals($events[8]['course'], $courses[1]['id']);
        $this->assertFalse($events[8]['attireRequired'], 'attireRequired is correct for event 8');
        $this->assertFalse($events[8]['equipmentRequired'], 'equipmentRequired is correct for event 8');
        $this->assertFalse($events[8]['supplemental'], 'supplemental is correct for event 8');
        $this->assertArrayNotHasKey('attendanceRequired', $events[8], 'attendanceRequired is correct for event 8');
        $this->assertEquals(0, count($events[8]['learningMaterials']), 'Event 8 has no learning materials');
        $this->assertArrayNotHasKey(
            'instructionalNotes',
            $events[8],
            'instructional notes is correct for event 8'
        );
        $this->assertEmpty($events[8]['postrequisites']);
        $this->assertEmpty($events[8]['prerequisites']);

        $this->assertEquals($events[9]['offering'], 1);
        $this->assertEquals($events[9]['startDate'], $offerings[0]['startDate']);
        $this->assertEquals($events[9]['endDate'], $offerings[0]['endDate']);
        $this->assertEquals($events[9]['courseTitle'], $courses[0]['title']);
        $this->assertEquals($events[9]['courseLevel'], $courses[0]['level']);
        $this->assertEquals(
            array_column($events[9]['cohorts'], 'id'),
            $courses[0]['cohorts'],
            'cohorts correct for event 9'
        );
        $this->assertEquals(
            array_column($events[9]['courseTerms'], 'id'),
            $courses[0]['terms'],
            'course terms correct for event 9'
        );
        $this->assertEquals($events[9]['course'], $courses[0]['id']);
        $this->assertFalse($events[9]['attireRequired'], 'attireRequired is correct for event 9');
        $this->assertArrayNotHasKey('equipmentRequired', $events[9], 'equipmentRequired is correct for event 9');
        $this->assertFalse($events[9]['supplemental'], 'supplemental is correct for event 9');
        $this->assertArrayNotHasKey('attendanceRequired', $events[9], 'attendanceRequired is correct for event 9');
        $this->assertEquals(8, $events[10]['offering']);
        $this->assertEquals(
            count($events[9]['learningMaterials']),
            10,
            'Event 9 has the correct number of learning materials'
        );
        $this->assertEquals(
            $events[10]['instructionalNotes'],
            $sessions[2]['instructionalNotes'],
            'instructional notes is correct for event 10'
        );
        $this->assertEquals(1, count($events[9]['sessionObjectives']));
        $this->assertEquals(3, $events[9]['sessionObjectives'][0]['id']);
        $this->assertEquals('third objective', $events[9]['sessionObjectives'][0]['title']);
        $this->assertEquals(0, $events[9]['sessionObjectives'][0]['position']);
        $this->assertEquals(1, count($events[9]['sessionObjectives'][0]['competencies']));
        $this->assertEquals(3, $events[9]['sessionObjectives'][0]['competencies'][0]);
        $this->assertEquals(1, count($events[9]['courseObjectives']));
        $this->assertEquals(2, $events[9]['courseObjectives'][0]['id']);
        $this->assertEquals('second objective', $events[9]['courseObjectives'][0]['title']);
        $this->assertEquals(0, $events[9]['courseObjectives'][0]['position']);
        $this->assertEquals(1, count($events[9]['courseObjectives'][0]['competencies']));
        $this->assertEquals(3, $events[9]['courseObjectives'][0]['competencies'][0]);
        $this->assertEquals(2, count($events[9]['competencies']));
        $this->assertEquals(3, $events[9]['competencies'][0]['id']);
        $this->assertEquals('third competency', $events[9]['competencies'][0]['title']);
        $this->assertEquals(1, $events[9]['competencies'][0]['parent']);
        $this->assertEquals(1, $events[9]['competencies'][1]['id']);
        $this->assertEquals('first competency', $events[9]['competencies'][1]['title']);
        $this->assertEquals(null, $events[9]['competencies'][1]['parent']);
        $this->assertEquals(0, count($events[9]['postrequisites']));
        $this->assertEmpty($events[9]['prerequisites']);


        /** @var OfferingInterface $offering */
        $offering = $this->fixtures->getReference('offerings8');
        $this->assertEquals($events[10]['startDate'], $offering->getStartDate()->format('c'));
        $this->assertEquals($events[10]['endDate'], $offering->getEndDate()->format('c'));
        $this->assertEquals($events[10]['courseTitle'], $courses[1]['title']);
        $this->assertEquals($events[10]['course'], $courses[1]['id']);
        $this->assertFalse($events[10]['attireRequired'], 'attireRequired is correct for event 10');
        $this->assertFalse($events[10]['equipmentRequired'], 'equipmentRequired is correct for event 10');
        $this->assertTrue($events[10]['supplemental'], 'supplemental is correct for event 10');
        $this->assertArrayNotHasKey('attendanceRequired', $events[10], 'attendanceRequired is correct for event 10');
        $this->assertEquals(
            7,
            count($events[10]['learningMaterials']),
            'Event 10 has the correct number of learning materials'
        );
        $this->assertEmpty($events[10]['postrequisites']);
        $this->assertEquals(3, count($events[10]['prerequisites']));
        $sessionIds = array_unique(array_column($events[10]['prerequisites'], 'session'));
        sort($sessionIds);
        $this->assertEquals([2], $sessionIds);
        foreach ($events as $event) {
            $this->assertEquals($school['id'], $event['school']);
        }
    }

    public function testMultidayEvent()
    {
        $school = $this->getContainer()->get(SchoolData::class)->getOne();
        $offerings = $this->getContainer()->get(OfferingData::class)->getAll();
        $from = new DateTime('2015-01-30 00:00:00');
        $to = new DateTime('2015-01-30 23:59:59');

        $events = $this->getEvents($school['id'], $from->getTimestamp(), $to->getTimestamp());
        $this->assertEquals(1, count($events), 'Expected events returned');

        $this->assertEquals($events[0]['startDate'], $offerings[5]['startDate']);
        $this->assertEquals($events[0]['endDate'], $offerings[5]['endDate']);
        $this->assertEquals($events[0]['offering'], $offerings[5]['id']);
    }

    public function testPrivilegedUsersGetsEventsForUnpublishedSessions()
    {
        $school = $this->getContainer()->get(SchoolData::class)->getOne();
        $events = $this->getEvents($school['id'], 0, 100000000000);

        $event = $events[3];
        $this->assertFalse($event['isPublished']);
        $this->assertFalse($event['isScheduled']);
        $lms = $event['learningMaterials'];

        $this->assertEquals(7, count($lms));
        $this->assertEquals('2', $lms[0]['sessionLearningMaterial']);
        $this->assertEquals('3', $lms[1]['sessionLearningMaterial']);
        $this->assertEquals('4', $lms[2]['sessionLearningMaterial']);
        $this->assertEquals('5', $lms[3]['sessionLearningMaterial']);
        $this->assertEquals('6', $lms[4]['sessionLearningMaterial']);
        $this->assertEquals('7', $lms[5]['sessionLearningMaterial']);
        $this->assertEquals('8', $lms[6]['sessionLearningMaterial']);
    }

    public function testGetEventsBySession()
    {
        $school = $this->getContainer()->get(SchoolData::class)->getOne();
        $userId = 2;
        $sessionId = 1;

        $events = $this->getEventsForSessionId(
            $school['id'],
            $sessionId,
            $userId
        );

        $this->assertEquals(2, count($events), 'Expected events returned');
        $this->assertEquals(
            $sessionId,
            $events[0]['session']
        );
        $this->assertEquals(1, $events[0]['offering']);
        $this->assertEquals($sessionId, $events[0]['session']);
        $this->assertEquals(2, $events[1]['offering']);
        $this->assertEquals($sessionId, $events[1]['session']);
    }

    protected function getEvents($schoolId, $from, $to, $userId = null)
    {
        $parameters = [
            'version' => 'v1',
            'id' => $schoolId,
            'from' => $from,
            'to' => $to
        ];
        $url = $this->getUrl(
            $this->kernelBrowser,
            'ilios_api_schoolevents',
            $parameters
        );

        $userToken = isset($userId) ?
            $this->getTokenForUser($this->kernelBrowser, $userId) :
            $this->getAuthenticatedUserToken($this->kernelBrowser);
        $this->createJsonRequest(
            'GET',
            $url,
            null,
            $userToken
        );

        $response = $this->kernelBrowser->getResponse();

        if (Response::HTTP_NOT_FOUND === $response->getStatusCode()) {
            $this->fail("Unable to load url: {$url}");
        }

        $this->assertJsonResponse($response, Response::HTTP_OK);
        return json_decode($response->getContent(), true)['events'];
    }

    protected function getEventsForSessionId($schoolId, $sessionId, $userId = null)
    {
        $parameters = [
            'version' => 'v1',
            'id' => $schoolId,
            'session' => $sessionId
        ];
        $url = $this->getUrl(
            $this->kernelBrowser,
            'ilios_api_schoolevents',
            $parameters
        );

        $userToken = isset($userId) ?
            $this->getTokenForUser($this->kernelBrowser, $userId) :
            $this->getAuthenticatedUserToken($this->kernelBrowser);
        $this->createJsonRequest(
            'GET',
            $url,
            null,
            $userToken
        );

        $response = $this->kernelBrowser->getResponse();

        if (Response::HTTP_NOT_FOUND === $response->getStatusCode()) {
            $this->fail("Unable to load url: {$url}");
        }

        $this->assertJsonResponse($response, Response::HTTP_OK);
        return json_decode($response->getContent(), true)['events'];
    }
}
