<?php

declare(strict_types=1);

namespace App;

use App\Entity\SomeEntity;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use ReflectionObject;

final class SomeService
{
    private EntityRepository $repository;

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(SomeEntity::class);
    }

    public function doSomething(): void
    {
        $reflection = new ReflectionObject($this->repository);
        $getEntityManager = $reflection->getMethod('getEntityManager');
        $getEntityManager->setAccessible(true);
        $repositoryEntityManager = $getEntityManager->invoke($this->repository);

        dump('Injected EntityManager is a proxy: ' . get_class($this->entityManager));
        dump('Injected EntityManager is a proxy: ' . spl_object_id($this->entityManager));
        dump('Repository EntityManager is an actual instance: ' . get_class($repositoryEntityManager));
        dump('Repository EntityManager is an actual instance: ' . spl_object_id($repositoryEntityManager));

        dump('Repository UOW: ' . spl_object_id($repositoryEntityManager->getUnitOfWork()));
        dump('EntityManager UOW: ' . spl_object_id($this->entityManager->getUnitOfWork()));

        // Working with the repository after the service reset has the effect that changes to the entities are not covered
        // by a flush. One SomeCommand execution only increases the counter by one, even though this method is called twice.
         $entity = $this->repository->find(1);
         $entity->counter++;
         $this->entityManager->flush();

         dump('Counter is now ' . $entity->counter);
    }
}
