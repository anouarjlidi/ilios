<?php

namespace Ilios\CoreBundle\Handler;

use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityManager;

use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Form\SessionDescriptionType;
use Ilios\CoreBundle\Entity\Manager\SessionDescriptionManager;
use Ilios\CoreBundle\Entity\SessionDescriptionInterface;

class SessionDescriptionHandler extends SessionDescriptionManager
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
     * @return SessionDescriptionInterface
     */
    public function post(array $parameters)
    {
        $sessionDescription = $this->createSessionDescription();

        return $this->processForm($sessionDescription, $parameters, 'POST');
    }

    /**
     * @param SessionDescriptionInterface $sessionDescription
     * @param array $parameters
     *
     * @return SessionDescriptionInterface
     */
    public function put(
        SessionDescriptionInterface $sessionDescription,
        array $parameters
    ) {
        return $this->processForm(
            $sessionDescription,
            $parameters,
            'PUT'
        );
    }
    /**
     * @param SessionDescriptionInterface $sessionDescription
     * @param array $parameters
     *
     * @return SessionDescriptionInterface
     */
    public function patch(
        SessionDescriptionInterface $sessionDescription,
        array $parameters
    ) {
        return $this->processForm(
            $sessionDescription,
            $parameters,
            'PATCH'
        );
    }

    /**
     * @param SessionDescriptionInterface $sessionDescription
     * @param array $parameters
     * @param string $method
     * @throws InvalidFormException when invalid form data is passed in.
     *
     * @return SessionDescriptionInterface
     */
    protected function processForm(
        SessionDescriptionInterface $sessionDescription,
        array $parameters,
        $method = "PUT"
    ) {
        $form = $this->formFactory->create(
            new SessionDescriptionType(),
            $sessionDescription,
            array('method' => $method)
        );
        $form->submit($parameters, 'PATCH' !== $method);

        if ($form->isValid()) {
            $sessionDescription = $form->getData();
            $this->updateSessionDescription($sessionDescription, true);

            return $sessionDescription;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }
}
