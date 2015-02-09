<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManager;

use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\SessionLearningMaterialType;
use Ilios\CoreBundle\Entity\Manager\SessionLearningMaterialManager;
use Ilios\CoreBundle\Entity\SessionLearningMaterialInterface;

class SessionLearningMaterialHandler extends SessionLearningMaterialManager
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @param EntityManager $em
     * @param string $class
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(EntityManager $em, $class, FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
        parent::__construct($em, $class);
    }

    /**
     * @param array $parameters
     *
     * @return SessionLearningMaterialInterface
     */
    public function post(array $parameters)
    {
        $sessionLearningMaterial = $this->createSessionLearningMaterial();

        return $this->processForm($sessionLearningMaterial, $parameters, 'POST');
    }

    /**
     * @param SessionLearningMaterialInterface $sessionLearningMaterial
     * @param array $parameters
     *
     * @return SessionLearningMaterialInterface
     */
    public function put(
        SessionLearningMaterialInterface $sessionLearningMaterial,
        array $parameters
    ) {
        return $this->processForm(
            $sessionLearningMaterial,
            $parameters,
            'PUT'
        );
    }
    /**
     * @param SessionLearningMaterialInterface $sessionLearningMaterial
     * @param array $parameters
     *
     * @return SessionLearningMaterialInterface
     */
    public function patch(
        SessionLearningMaterialInterface $sessionLearningMaterial,
        array $parameters
    ) {
        return $this->processForm(
            $sessionLearningMaterial,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param SessionLearningMaterialInterface $sessionLearningMaterial
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return SessionLearningMaterialInterface
     */
    protected function processForm(
        SessionLearningMaterialInterface $sessionLearningMaterial,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            new SessionLearningMaterialType(),
            $sessionLearningMaterial,
            array('method' => $method)
        );
        $form->submit($parameters, 'PATCH' !== $method);

        if ($form->isValid()) {
            $sessionLearningMaterial = $form->getData();
            $this->updateSessionLearningMaterial($sessionLearningMaterial, true);

            return $sessionLearningMaterial;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}
