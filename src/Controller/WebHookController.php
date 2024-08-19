<?php

namespace App\Controller;

use App\Entity\WebHook;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WebHookController extends AbstractController
{
    /**
     * @Route("/web/hook", name="app_web_hook")
     */
    public function index(Request $request, EntityManagerInterface $entityManager)
    {
        $data = json_decode($request->getContent(), true);

        $webHook = new WebHook();

        $webHook->setContenu($data);

        $entityManager->persist($webHook);
        $entityManager->flush();

        return $this->json(['success' => true]);
    }
}
