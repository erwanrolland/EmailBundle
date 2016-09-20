<?php

namespace Librinfo\EmailBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * File upload form type
 */
class DropzoneType extends AbstractType
{

    public function configureOptions(OptionsResolver $resolver)
    {
        
    }

    public function getParent()
    {
        return 'form';
    }

    public function getName()
    {
        return 'dropzone';
    }

}
