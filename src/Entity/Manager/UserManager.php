<?php

declare(strict_types=1);

namespace App\Entity\Manager;

use App\Traits\CalendarEventRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use App\Classes\CalendarEvent;
use App\Classes\UserEvent;
use App\Classes\UserMaterial;
use App\Entity\Repository\UserRepository;
use App\Entity\DTO\UserDTO;
use App\Service\UserMaterialFactory;
use Doctrine\Common\Persistence\ManagerRegistry;
use Exception;

/**
 * Class UserManager
 */
class UserManager extends BaseManager
{
    /**
     * @var UserMaterialFactory
     */
    protected $factory;

    /**
     * @param ManagerRegistry $registry
     * @param string $class
     * @param UserMaterialFactory $factory
     */
    public function __construct(ManagerRegistry $registry, $class, UserMaterialFactory $factory)
    {
        parent::__construct($registry, $class);
        $this->factory = $factory;
    }

    /**
     * @param array $campusIds
     *
     * @return UserDTO[]
     * @throws Exception
     */
    public function findAllMatchingDTOsByCampusIds(
        array $campusIds
    ) {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->findAllMatchingDTOsByCampusIds($campusIds);
    }

    /**
     * @param string $q
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     * @param array $criteria
     *
     * @return UserDTO[]
     * @throws Exception
     */
    public function findUserDTOsByQ(
        $q,
        array $orderBy = null,
        $limit = null,
        $offset = null,
        array $criteria = array()
    ) {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->findDTOsByQ($q, $orderBy, $limit, $offset, $criteria);
    }

    /**
     * Find all of the events for a user id between two dates.
     *
     * @param int $userId
     * @param DateTime $from
     * @param DateTime $to
     * @return UserEvent[]
     * @throws Exception
     */
    public function findEventsForUser($userId, DateTime $from, DateTime $to)
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->findEventsForUser($userId, $from, $to);
    }

    /**
     * Find all of the events for a user in a session
     *
     * @param int $userId
     * @param int $sessionId
     * @return UserEvent[]
     * @throws Exception
     */
    public function findSessionEventsForUser(int $userId, int $sessionId): array
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->findSessionEventsForUser($userId, $sessionId);
    }

    /**
     * Finds and adds instructors to a given list of calendar events.
     *
     * @param CalendarEvent[] $events
     * @return CalendarEvent[]
     * @throws Exception
     */
    public function addInstructorsToEvents(array $events)
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->addInstructorsToEvents($events);
    }

    /**
     * Finds and adds course- and session-objectives and their competencies to a given list of calendar events.
     *
     * @param CalendarEvent[] $events
     * @return CalendarEvent[]
     * @throws Exception
     */
    public function addSessionDataToEvents(array $events)
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->addSessionDataToEvents($events);
    }

    /**
     * @param array $campusIdFilter an array of the campusIDs to include in our search if empty then all users
     *
     * @return ArrayCollection
     * @throws Exception
     */
    public function findUsersWhoAreNotFormerStudents(array $campusIdFilter = array())
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->findUsersWhoAreNotFormerStudents($campusIdFilter);
    }

    /**
     * Get all the IDs for every user
     * @param $includeDisabled
     * @param $includeSyncIgnore
     *
     * @return array
     * @throws Exception
     */
    public function getIds($includeDisabled = true, $includeSyncIgnore = true)
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->getIds($includeDisabled, $includeSyncIgnore);
    }

    /**
     * Get all the campus IDs for every user
     * @param $includeDisabled
     * @param $includeSyncIgnore
     *
     * @return array
     * @throws Exception
     */
    public function getAllCampusIds($includeDisabled = true, $includeSyncIgnore = true)
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->getAllCampusIds($includeDisabled, $includeSyncIgnore);
    }

    /**
     * Reset the examined flags on every user
     * @throws Exception
     */
    public function resetExaminedFlagForAllUsers()
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->resetExaminedFlagForAllUsers();
    }

    /**
     * Find all of the learning materials for a userId
     *
     * @param int $userId
     * @param array $criteria
     * @return UserMaterial[]
     * @throws Exception
     */
    public function findMaterialsForUser($userId, $criteria)
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->findMaterialsForUser($userId, $this->factory, $criteria);
    }

    /**
     * Finds and adds learning materials to a given list of user events.
     *
     * @param UserEvent[] $events
     * @return UserEvent[]
     * @throws Exception
     */
    public function addMaterialsToEvents(array $events)
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->addMaterialsToEvents($events, $this->factory);
    }

    /**
     * @param int $userId
     * @return array
     * @throws Exception
     * @see UserRepository::getDirectedSchoolIds()
     */
    public function getDirectedSchoolIds($userId): array
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->getDirectedSchoolIds($userId);
    }

    /**
     * @param int $userId
     * @return array
     * @throws Exception
     * @see UserRepository::getAdministeredSchoolIds()
     */
    public function getAdministeredSchoolIds($userId): array
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->getAdministeredSchoolIds($userId);
    }

    /**
     * @param int $userId
     * @return array
     * @throws Exception
     * @see UserRepository::getDirectedCourseAndSchoolIds()
     */
    public function getDirectedCourseAndSchoolIds($userId): array
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->getDirectedCourseAndSchoolIds($userId);
    }

    /**
     * @param int $userId
     * @return array
     * @throws Exception
     * @see UserRepository::getAdministeredCourseAndSchoolIds()
     */
    public function getAdministeredCourseAndSchoolIds($userId): array
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->getAdministeredCourseAndSchoolIds($userId);
    }

    /**
     * @param $userId
     * @return array
     * @throws Exception
     * @see UserRepository::getAdministeredCurriculumInventoryReportAndSchoolIds
     */
    public function getAdministeredCurriculumInventoryReportAndSchoolIds($userId): array
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->getAdministeredCurriculumInventoryReportAndSchoolIds($userId);
    }

    /**
     * @param $userId
     * @return array
     * @throws Exception
     * @see UserRepository::getAdministeredSessionCourseAndSchoolIds()
     */
    public function getAdministeredSessionCourseAndSchoolIds($userId): array
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->getAdministeredSessionCourseAndSchoolIds($userId);
    }

    /**
     * @param $userId
     * @return array
     * @throws Exception
     * @see UserRepository::getInstructedLearnerGroupSchoolIds()
     */
    public function getInstructedLearnerGroupSchoolIds($userId): array
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->getInstructedLearnerGroupSchoolIds($userId);
    }

    /**
     * @param $userId
     * @return array
     * @throws Exception
     * @see UserRepository::getInstructorGroupSchoolIds()
     */
    public function getInstructorGroupSchoolIds($userId): array
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->getInstructorGroupSchoolIds($userId);
    }

    /**
     * @param $userId
     * @return array
     * @throws Exception
     * @see UserRepository::getInstructedOfferingIlmSessionCourseAndSchoolIds()
     */
    public function getInstructedOfferingIlmSessionCourseAndSchoolIds($userId): array
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->getInstructedOfferingIlmSessionCourseAndSchoolIds($userId);
    }

    /**
     * @param $userId
     * @return array
     * @throws Exception
     * @see UserRepository::getDirectedProgramAndSchoolIds()
     */
    public function getDirectedProgramAndSchoolIds($userId): array
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->getDirectedProgramAndSchoolIds($userId);
    }

    /**
     * @param $userId
     * @return array
     * @throws Exception
     * @see UserRepository::getDirectedProgramYearProgramAndSchoolIds()
     */
    public function getDirectedProgramYearProgramAndSchoolIds($userId): array
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->getDirectedProgramYearProgramAndSchoolIds($userId);
    }

    /**
     * @param $userId
     * @return array
     * @throws Exception
     * @see UserRepository::getCoursesCohortsProgramYearAndProgramIdsLinkedToProgramsDirectedByUser()
     */
    public function getCoursesCohortsProgramYearAndProgramIdsLinkedToProgramsDirectedByUser($userId): array
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->getCoursesCohortsProgramYearAndProgramIdsLinkedToProgramsDirectedByUser($userId);
    }

    /**
     * @param $userId
     * @return array
     * @throws Exception
     * @see UserRepository::getLearnerGroupIds()
     */
    public function getLearnerGroupIds($userId): array
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->getLearnerGroupIds($userId);
    }

    /**
     * @param $userId
     * @return array
     * @throws Exception
     * @see CalendarEventRepository::getInstructorGroupIds()
     */
    public function getInstructorGroupIds($userId): array
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->getInstructorGroupIds($userId);
    }

    /**
     * @param int $id
     * @param array $events
     * @return array
     * @throws Exception
     * @see UserRepository::addPreAndPostRequisites()
     */
    public function addPreAndPostRequisites($id, array $events): array
    {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        return $repository->addPreAndPostRequisites($id, $events);
    }
}
