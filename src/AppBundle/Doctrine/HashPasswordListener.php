<?php

namespace AppBundle\Doctrine;

use AppBundle\Entity\UserAccounts\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class HashPasswordListener implements  EventSubscriber
{

    private $passwordEncoder;
    /**
     * @var Request
     */
    private $request;

    public function __construct(UserPasswordEncoder $passwordEncoder,Request $request)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->request = $request;
    }


    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if(!$entity instanceof  User){
            return;
        }

        $this->encodePassword($entity);
    }


    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        $isEncodeRequest = $this->request->getPathInfo() == '/login_check';
        
        if(!$entity instanceof  User || !$isEncodeRequest){
            return;
        }

        $this->encodePassword($entity->getPassword());

        $em = $args->getEntityManager();
        $meta = $em->getClassMetadata(get_class($entity));
        $em->getUnitOfWork()->recomputeSingleEntityChangeSet($meta,$entity);


    }

    public function encodePassword(User $entity)
    {

        if (!$entity->getPlainPassword()) {
            return;
        }

        $encoded = $this->passwordEncoder->encodePassword(
            $entity,
            $entity->getPlainPassword()
        );
        $entity->setPassword($encoded);

    }


    public function getSubscribedEvents()
    {
       return ['prePersist','preUpdate'];
    }

}