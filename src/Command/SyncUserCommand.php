<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use App\Entity\Manager\UserManager;
use App\Entity\Manager\AuthenticationManager;
use App\Entity\Manager\PendingUserUpdateManager;
use App\Service\Directory;

/**
 * Sync a user with their directory information
 *
 * Class SyncUserCommand
 */
class SyncUserCommand extends Command
{
    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @var AuthenticationManager
     */
    protected $authenticationManager;

    /**
     * @var PendingUserUpdateManager
     */
    protected $pendingUserUpdateManager;
    
    /**
     * @var Directory
     */
    protected $directory;

    public function __construct(
        UserManager $userManager,
        AuthenticationManager $authenticationManager,
        PendingUserUpdateManager $pendingUserUpdateManager,
        Directory $directory
    ) {
        $this->userManager = $userManager;
        $this->authenticationManager = $authenticationManager;
        $this->pendingUserUpdateManager = $pendingUserUpdateManager;
        $this->directory = $directory;
        
        parent::__construct();
    }
    
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ilios:sync-user')
            ->setAliases(['ilios:directory:sync-user'])
            ->setDescription('Sync a user from the directory.')
            ->addArgument(
                'userId',
                InputArgument::REQUIRED,
                'A valid user id.'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userId = $input->getArgument('userId');
        /** @var User $user */
        $user = $this->userManager->findOneBy(['id' => $userId]);
        if (!$user) {
            throw new \Exception(
                "No user with id #{$userId} was found."
            );
        }
        
        $userRecord = $this->directory->findByCampusId($user->getCampusId());
        
        if (!$userRecord) {
            $output->writeln('<error>Unable to find ' . $user->getCampusId() . ' in the directory.');
            return 1;
        }
        
        $table = new Table($output);
        $table
            ->setHeaders([
                'Record',
                'Campus ID',
                'First',
                'Last',
                'Display Name',
                'Email',
                'Phone Number'
            ])
            ->setRows([
                [
                    'Ilios User',
                    $user->getCampusId(),
                    $user->getFirstName(),
                    $user->getLastName(),
                    $user->getDisplayName(),
                    $user->getEmail(),
                    $user->getPhone()
                ],
                [
                    'Directory User',
                    $userRecord['campusId'],
                    $userRecord['firstName'],
                    $userRecord['lastName'],
                    $userRecord['displayName'],
                    $userRecord['email'],
                    $userRecord['telephoneNumber']
                ]
            ])
        ;
        $table->render();
        
        $helper = $this->getHelper('question');
        $output->writeln('');
        $question = new ConfirmationQuestion(
            '<question>Do you wish to update this Ilios User with the data ' .
            'from the Directory User? </question>' . "\n",
            true
        );
        
        if ($helper->ask($input, $output, $question)) {
            $user->setFirstName($userRecord['firstName']);
            $user->setLastName($userRecord['lastName']);
            $user->setDisplayName($userRecord['displayName']);
            $user->setEmail($userRecord['email']);
            $user->setPhone($userRecord['telephoneNumber']);
            $authentication = $user->getAuthentication();
            if (!$authentication) {
                $authentication = $this->authenticationManager->create();
                $authentication->setUser($user);
            }

            $authentication->setUsername($userRecord['username']);
            $this->authenticationManager->update($authentication, false);
            
            $this->userManager->update($user);

            foreach ($user->getPendingUserUpdates() as $update) {
                $this->pendingUserUpdateManager->delete($update);
            }

            $output->writeln('<info>User updated successfully!</info>');

            return 0;
        } else {
            $output->writeln('<comment>Update canceled.</comment>');

            return 1;
        }
    }
}
