<?php

namespace Librinfo\EmailBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Librinfo\EmailBundle\Entity\Email;
use Monolog\Logger;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class RecipientsListener implements LoggerAwareInterface, EventSubscriber
{

    /**
     * @var Logger
     */
    private $logger;

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            'loadClassMetadata',
        ];
    }

    /**
     * Dynamic many-to-many mappings between Email and recipient entities
     *
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $email = new Email;
        if (!( $recipientsClasses = $email->getExternallyLinkedClasses() ))
            return;

        /** @var ClassMetadata $metadata */
        $metadata = $eventArgs->getClassMetadata();

        if ($metadata->getName() == Email::class) {
            $this->logger->debug("[RecipientsListener] Entering RecipientsListener for « loadClassMetadata » event");

            foreach ($recipientsClasses as $recipientsClass)
            {
                // mapping with recipientClass entity (many-to-many owning side)
                $rc = new \ReflectionClass($recipientsClass);
                $entity = strtolower($rc->getShortName());  // TODO: camelcase to underscores ?
                $metadata->mapManyToMany([
                    'targetEntity' => $recipientsClass,
                    'fieldName'    => $entity . 's',  // ex. "organisms"
                    'inversedBy'   => 'emailMessages',
                    'joinTable'    => [
                        'name'               => 'librinfo_email_email__' . $entity,  // ex. "librinfo_email_email__organism"
                        'joinColumns'        => ['email_id' => ['referencedColumnName' => 'id']],
                        'inverseJoinColumns' => [$entity . '_id'    => ['referencedColumnName' => 'id']],  // ex. organism_id
                    ]
                ]);
            }

            $this->logger->debug("[RecipientsListener] Added Email / Recipient mapping metadata to Entity", ['class' => $metadata->getName()]);
        }

        elseif (in_array($metadata->getName(), $recipientsClasses)) {
            // Check if parents already have the HasEmailMessages trait
            foreach ($metadata->parentClasses as $parent)
                if ($this->classAnalyzer->hasTrait($parent, 'Librinfo\EmailBundle\Entity\Traits\HasEmailMessages'))
                    return;

            $this->logger->debug("[RecipientsListener] Entering EmailBundleListener for « loadClassMetadata » event");

            // mapping with Emails (many-to-many inverse side)
            $reflectionClass = $metadata->getReflectionClass();
            $metadata->mapManyToMany([
                'targetEntity' => Email::class,
                'fieldName'    => 'emailMessages',
                'mappedBy'     => strtolower($reflectionClass->getShortName()) . 's'  // ex. "organisms"
            ]);

            $this->logger->debug("[RecipientsListener] Added Email mapping metadata to Entity", ['class' => $metadata->getName()]);
        }
    }

    /**
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger
     * @return null
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

}