<?php

namespace App\Controller;

use App\Entity\Statut;
use App\Form\StatutType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/statut")
 */
class StatutController extends AbstractController
{
    /**
     * @Route("/", name="app_statut_index", methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $tabRoles = $this->getUser()->getRoles();
        if (in_array('ROLE_MANAGER', $tabRoles) || in_array('ROLE_SUPER_ADMIN', $tabRoles)) {
            $statuts = $entityManager
                ->getRepository(Statut::class)
                ->findAll();

            return $this->render('statut/index.html.twig', [
                'statuts' => $statuts,
            ]);
        } else {
            return $this->render('error.html.twig');
        }
    }

    /**
     * @Route("/new", name="app_statut_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tabRoles = $this->getUser()->getRoles();
        if (in_array('ROLE_MANAGER', $tabRoles) || in_array('ROLE_SUPER_ADMIN', $tabRoles)) {
            $statut = new Statut();
            $form = $this->createForm(StatutType::class, $statut);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->persist($statut);
                $entityManager->flush();

                return $this->redirectToRoute('app_statut_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('statut/new.html.twig', [
                'statut' => $statut,
                'form' => $form,
            ]);
        } else {
            return $this->render('error.html.twig');
        }
    }

    /**
     * @Route("/{id}", name="app_statut_show", methods={"GET"})
     */
    public function show(Statut $statut): Response
    {
        return $this->render('statut/show.html.twig', [
            'statut' => $statut,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_statut_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Statut $statut, EntityManagerInterface $entityManager): Response
    {
        $tabRoles = $this->getUser()->getRoles();
        if (in_array('ROLE_MANAGER', $tabRoles) || in_array('ROLE_SUPER_ADMIN', $tabRoles)) {
            $form = $this->createForm(StatutType::class, $statut);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->flush();

                return $this->redirectToRoute('app_statut_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('statut/edit.html.twig', [
                'statut' => $statut,
                'form' => $form,
            ]);
        } else {
            return $this->render('error.html.twig');
        }
    }

    /**
     * @Route("/{id}", name="app_statut_delete", methods={"POST"})
     */
    public function delete(Request $request, Statut $statut, EntityManagerInterface $entityManager): Response
    {
        $tabRoles = $this->getUser()->getRoles();
        if (in_array('ROLE_MANAGER', $tabRoles) || in_array('ROLE_SUPER_ADMIN', $tabRoles)) {
            if ($this->isCsrfTokenValid('delete' . $statut->getId(), $request->request->get('_token'))) {
                $entityManager->remove($statut);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_statut_index', [], Response::HTTP_SEE_OTHER);
        } else {
            return $this->render('error.html.twig');
        }
    }
}
