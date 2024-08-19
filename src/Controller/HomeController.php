<?php

namespace App\Controller;

use App\Repository\TacheRepository;
use App\Repository\NotificationRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
  /**
   *  
   * @Route("/", name="app_home")
   */
  public function index()
  {
    return $this->redirectToRoute('app_tache_index');
  }
}
