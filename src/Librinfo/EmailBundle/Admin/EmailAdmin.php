<?php

namespace Librinfo\EmailBundle\Admin;

use Librinfo\CoreBundle\Admin\CoreAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class EmailAdmin extends CoreAdmin
{

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('sendAjax');
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
                ->add('field_from')
                ->add('field_to')
                ->add('field_cc')
                ->add('field_bcc')
                ->add('field_subject')
                ->add('content')
                ->add('textContent')
                ->add('sent')
                ->add('createdAt')
                ->add('updatedAt')
                ->add('id')
                ->add('tracking')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
                ->add('field_from')
                ->add('field_to')
                ->add('field_cc')
                ->add('field_bcc')
                ->add('field_subject')
                ->add('content')
                ->add('textContent')
                ->add('sent')
                ->add('createdAt')
                ->add('updatedAt')
                ->add('id')
                ->add('tracking')
                ->add('_action', 'actions', array(
                    'actions' => array(
                        'show' => array(),
                        'edit' => array(),
                        'delete' => array(),
                    )
                ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
                ->add('field_from')
                ->add('field_to')
                ->add('field_cc')
                ->add('field_bcc')
                ->add('field_subject')
                ->add('content')
                ->add('textContent')
                ->add('sent')
                ->add('createdAt')
                ->add('updatedAt')
                ->add('id')
                ->add('tracking')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
                ->add('field_from')
                ->add('field_to')
                ->add('field_cc')
                ->add('field_bcc')
                ->add('field_subject')
                ->add('content')
                ->add('textContent')
                ->add('sent')
                ->add('createdAt')
                ->add('updatedAt')
                ->add('id')
                ->add('tracking')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewInstance()
    {
        $object = parent::getNewInstance();

        if ($this->hasRequest()) {
            $force_user = $this->getRequest()->get('force_user');
            if ($force_user) {
                $user = $this->getConfigurationPool()->getContainer()->get('security.context')->getToken()->getUser();
                if ($user)
                    $object->setFieldFrom($user->getEmail());
            }

            $recipients = $this->getRequest()->get('recipients');
            $fieldTo = is_array($recipients) ? implode(', ', $recipients) : $recipients;
            $object->setFieldTo($fieldTo);
        }

        return $object;
    }

}
