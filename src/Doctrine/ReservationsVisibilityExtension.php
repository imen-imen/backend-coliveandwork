<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Reservation;
use Symfony\Bundle\SecurityBundle\Security;

final class ReservationsVisibilityExtension implements QueryCollectionExtensionInterface
{
    public function __construct(private Security $security) {}

    public function applyToCollection(
        QueryBuilder $qb,
        QueryNameGeneratorInterface $qng,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []             
    ): void
    {
        if ($resourceClass !== Reservation::class) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user) {
            return;
        }

        $rootAlias = $qb->getRootAliases()[0];

        // Cas propriétaire : ne voir que ses réservations
        if ($this->security->isGranted('ROLE_OWNER') && !$this->security->isGranted('ROLE_EMPLOYEE') && !$this->security->isGranted('ROLE_ADMIN')) {
            $qb
                ->join($rootAlias.'.privateSpace', 'ps')
                ->join('ps.colivingSpace', 'cs')
                ->andWhere('cs.owner = :currentOwner')
                ->setParameter('currentOwner', $user);
            return;
        }

        //  Cas client : ne voir que ses propres réservations
        if ($this->security->isGranted('ROLE_USER') && !$this->security->isGranted('ROLE_OWNER') && !$this->security->isGranted('ROLE_EMPLOYEE') && !$this->security->isGranted('ROLE_ADMIN')) {
            $qb
                ->andWhere($rootAlias.'.client = :currentClient')
                ->setParameter('currentClient', $user);
        }
    }
}
