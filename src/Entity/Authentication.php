<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Annotation as IS;
use App\Classes\SessionUser;
use App\Classes\SessionUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\UserInterface;

/**
 * Class Authentication
 *
 * @ORM\Table(name="authentication")
 * @ORM\Entity(repositoryClass="App\Entity\Repository\AuthenticationRepository")
 *
 * @IS\Entity
 */
class Authentication implements AuthenticationInterface
{
    /**
     * @var UserInterface
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="User", inversedBy="authentication")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="person_id", referencedColumnName="user_id", onDelete="CASCADE")
     * })
     *
     * @Assert\NotBlank()
     *
     * @IS\Type("entity")
     * @IS\Expose
    */
    protected $user;

    /**
     * @ORM\Column(name="username", type="string", unique=true, length=100, nullable=true)
     * @var string
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 100,
     *     allowEmptyString = true
     * )
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    private $username;

    /**
     * @ORM\Column(name="password_sha256", type="string", length=64, nullable=true)
     * @var string
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 64,
     *     allowEmptyString = true
     * )
     *
     */
    private $passwordSha256;

    /**
     * @ORM\Column(name="password_hash", type="string", nullable=true)
     * @var string
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 255,
     *     allowEmptyString = true
     * )
     *
     */
    private $passwordHash;

    /**
     * @ORM\Column(name="invalidate_token_issued_before", type="datetime", nullable=true)
     *
     * @Assert\DateTime()
     *
     * @IS\Expose
     * @IS\ReadOnly
     * @IS\Type("dateTime")
     */
    protected $invalidateTokenIssuedBefore;

    /**
     * @inheritdoc
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @inheritdoc
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @inheritdoc
     */
    public function setPasswordSha256($passwordSha256)
    {
        $this->passwordSha256 = $passwordSha256;
    }

    /**
     * @inheritdoc
     */
    public function getPasswordSha256()
    {
        return $this->passwordSha256;
    }

    /**
     * @inheritdoc
     */
    public function setPasswordHash($passwordHash)
    {
        if ($passwordHash) {
            $this->setPasswordSha256(null);
        }
        $this->passwordHash = $passwordHash;
    }

    /**
     * @inheritdoc
     */
    public function getPasswordHash()
    {
        return $this->passwordHash;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        $newPassword = $this->getPasswordHash();
        $legacyPassword = $this->getPasswordSha256();
        return $newPassword ? $newPassword : $legacyPassword;
    }


    /**
     * @inheritdoc
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * @inheritdoc
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @inheritdoc
     */
    public function isLegacyAccount()
    {
        return (bool) $this->getPasswordSha256();
    }

    /**
     * @inheritdoc
     */
    public function setInvalidateTokenIssuedBefore(\DateTime $invalidateTokenIssuedBefore = null)
    {
        $this->invalidateTokenIssuedBefore = $invalidateTokenIssuedBefore;
    }

    /**
     * @inheritdoc
     */
    public function getInvalidateTokenIssuedBefore()
    {
        return $this->invalidateTokenIssuedBefore;
    }

    /**
    * @inheritdoc
    */
    public function __toString()
    {
        return (string) $this->user;
    }
}
