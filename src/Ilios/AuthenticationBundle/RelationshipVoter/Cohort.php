<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\CohortInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class Cohort extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        if ($this->abstain) {
            return false;
        }

        return $subject instanceof CohortInterface
            && in_array(
                $attribute,
                [self::CREATE, self::VIEW, self::EDIT, self::DELETE]
            );
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }
        if ($user->isRoot()) {
            return true;
        }

        switch ($attribute) {
            case self::VIEW:
                return true;
                break;
            case self::EDIT:
                return $this->permissionChecker->canUpdateCohort($user, $subject);
                break;
            case self::CREATE:
                return $this->permissionChecker->canCreateCohort($user, $subject->getProgramYear());
                break;
            case self::DELETE:
                return $this->permissionChecker->canDeleteCohort($user, $subject);
                break;
        }

        return false;
    }
}