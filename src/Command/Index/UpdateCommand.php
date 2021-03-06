<?php

declare(strict_types=1);

namespace App\Command\Index;

use App\Entity\Manager\CourseManager;
use App\Entity\Manager\LearningMaterialManager;
use App\Entity\Manager\MeshDescriptorManager;
use App\Entity\Manager\UserManager;
use App\Message\CourseIndexRequest;
use App\Message\LearningMaterialIndexRequest;
use App\Message\MeshDescriptorIndexRequest;
use App\Message\UserIndexRequest;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Queues the updating of all indexed items
 */
class UpdateCommand extends Command
{
    public const COMMAND_NAME = 'ilios:index:update';

    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @var CourseManager
     */
    protected $courseManager;

    /**
     * @var MeshDescriptorManager
     */
    protected $descriptorManager;

    /**
     * @var LearningMaterialManager
     */
    protected $learningMaterialManager;

    /**
     * @var MessageBusInterface
     */
    protected $bus;

    public function __construct(
        UserManager $userManager,
        CourseManager $courseManager,
        MeshDescriptorManager $descriptorManager,
        LearningMaterialManager $learningMaterialManager,
        MessageBusInterface $bus
    ) {
        parent::__construct();

        $this->userManager = $userManager;
        $this->courseManager = $courseManager;
        $this->descriptorManager = $descriptorManager;
        $this->learningMaterialManager = $learningMaterialManager;
        $this->bus = $bus;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Queue everything to be updated in the index.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->queueUsers($output);
        //temporarily disable LM indexing for performance reasons.
//        $this->queueLearningMaterials($output);
        $this->queueCourses($output);
        $this->queueMesh($output);

        return 0;
    }

    protected function queueUsers(OutputInterface $output)
    {
        $allIds = $this->userManager->getIds();
        $count = count($allIds);
        $chunks = array_chunk($allIds, UserIndexRequest::MAX_USERS);
        foreach ($chunks as $ids) {
            $this->bus->dispatch(new UserIndexRequest($ids));
        }
        $output->writeln("<info>${count} users have been queued for indexing.</info>");
    }

    protected function queueCourses(OutputInterface $output)
    {
        $allIds = $this->courseManager->getIds();
        $count = count($allIds);
        $chunks = array_chunk($allIds, CourseIndexRequest::MAX_COURSES);
        foreach ($chunks as $ids) {
            $this->bus->dispatch(new CourseIndexRequest($ids));
        }
        $output->writeln("<info>${count} courses have been queued for indexing.</info>");
    }

    protected function queueLearningMaterials(OutputInterface $output)
    {
        $allIds = $this->learningMaterialManager->getFileLearningMaterialIds();
        $count = count($allIds);
        foreach ($allIds as $id) {
            $this->bus->dispatch(new LearningMaterialIndexRequest($id));
        }
        $output->writeln("<info>${count} learning materials have been queued for indexing.</info>");
    }

    protected function queueMesh(OutputInterface $output)
    {
        $allIds = $this->descriptorManager->getIds();
        $count = count($allIds);
        $chunks = array_chunk($allIds, MeshDescriptorIndexRequest::MAX_DESCRIPTORS);
        foreach ($chunks as $ids) {
            $this->bus->dispatch(new MeshDescriptorIndexRequest($ids));
        }
        $output->writeln("<info>${count} descriptors have been queued for indexing.</info>");
    }
}
