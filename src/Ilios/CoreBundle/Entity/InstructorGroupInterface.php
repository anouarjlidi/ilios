<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;

/**
 * Interface InstructorGroupInterface
 * @package Ilios\CoreBundle\Entity
 */
interface InstructorGroupInterface extends IdentifiableEntityInterface, TitledEntityInterface
{
    /**
     * @param SchoolInterface $school
     */
    public function setSchool(SchoolInterface $school);

    /**
     * @return SchoolInterface
     */
    public function getSchool();

    /**
     * @param Collection $groups
     */
    public function setLearnerGroups(Collection $learnerGroups);

    /**
     * @param LearnerGroupInterface $learnerGroup
     */
    public function addLearnerGroup(LearnerGroupInterface $learnerGroup);

    /**
     * @return ArrayCollection|LearnerGroupInterface[]
     */
    public function getLearnerGroups();

    /**
     * @param Collection $ilmSessions
     */
    public function setIlmSessions(Collection $ilmSessions);

    /**
     * @param IlmSessionInterface $ilmSession
     */
    public function addIlmSession(IlmSessionInterface $ilmSession);

    /**
     * @return ArrayCollection|IlmSessionInterface[]
     */
    public function getIlmSessions();

    /**
     * @param Collection $users
     */
    public function setUsers(Collection $users);

    /**
     * @param UserInterface $user
     */
    public function addUser(UserInterface $user);

    /**
     * @return ArrayCollection|UserInterface[]
     */
    public function getUsers();

    /**
     * @param Collection $offerings
     */
    public function setOfferings(Collection $offerings);

    /**
     * @param OfferingInterface $offering
     */
    public function addOffering(OfferingInterface $offering);

    /**
     * @return ArrayCollection|OfferingInterface[]
     */
    public function getOfferings();
}