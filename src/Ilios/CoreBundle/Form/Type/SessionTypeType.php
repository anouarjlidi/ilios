<?php

namespace Ilios\CoreBundle\Form\Type;

use Ilios\CoreBundle\Form\DataTransformer\RemoveMarkupTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SessionTypeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('sessionTypeCssClass', null, ['required' => false])
            ->add('assessment', null, ['required' => false])
            ->add('assessmentOption', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:AssessmentOption"
            ])
            ->add('school', 'tdn_single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:School"
            ])
            ->add('aamcMethods', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:AamcMethod"
            ])
            ->add('sessions', 'tdn_many_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Session"
            ])
        ;

        $transformer = new RemoveMarkupTransformer();
        foreach (['title', 'sessionTypeCssClass'] as $element) {
            $builder->get($element)->addViewTransformer($transformer);
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\SessionType'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'sessiontype';
    }
}
