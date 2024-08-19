<?php

namespace App\Twig;

use App\Repository\TacheRepository;
use Twig\Extension\GlobalsInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class GlobalVariables implements GlobalsInterface
{
  private $countTachesNonVues;
  private $tokenStorage;
  private $tacheRepository;

  public function __construct(TokenStorageInterface $tokenStorage, TacheRepository $tacheRepository, int $countTachesNonVues)
  {
    $this->tokenStorage = $tokenStorage;
    $this->tacheRepository = $tacheRepository;
    $this->countTachesNonVues = $this->showNombreTachesNonVues();
  }

  public function getGlobals(): array
  {
    return [
      'countTachesNonVues' => $this->countTachesNonVues,
    ];
  }

  public function showNombreTachesNonVues()
  {
    $user = $this->tokenStorage->getToken()->getUser();
    $username = $user->getUserIdentifier();

    $tachesNonVues = $this->tacheRepository->createQueryBuilder('t')
      ->join('t.projet', 'p')
      ->join('p.chefprojet', 'cp')
      ->join('t.notifications', 'n')
      ->where('cp.username = :username')
      ->andWhere('n.etat = :etat')
      ->setParameter('username', $username)
      ->setParameter('etat', 'pas encore vu')
      ->orderBy('t.id', 'DESC')
      ->getQuery()
      ->getResult();

    return count($tachesNonVues);
  }
}
