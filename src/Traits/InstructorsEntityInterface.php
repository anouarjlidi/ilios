<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\UserInterface;

/**
 * Interface InstructorsEntityInterface
 */
interface InstructorsEntityInterface
{
    /**
     * @param Collection $instructors
     */
    public function setInstructors(Collection $instructors);

    /**
     * @param UserInterface $instructor
     */
    public function addInstructor(UserInterface $instructor);

    /**
     * @param UserInterface $instructor
     */
    public function removeInstructor(UserInterface $instructor);

    /**
    * @return UserInterface[]|ArrayCollection
    */
    public function getInstructors();
}
