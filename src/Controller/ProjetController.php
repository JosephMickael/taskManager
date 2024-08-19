<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Form\ProjetType;
use App\Repository\TacheRepository;
use App\Repository\ProjetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/projet")
 */
class ProjetController extends AbstractController
{
    /**
     * @Route("/", name="app_projet_index", methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager, ProjetRepository $projetRepository): Response
    {
        $tabRoles = $this->getUser()->getRoles();
        if (in_array('ROLE_MANAGER', $tabRoles)) {
            // utiliser pour la concatenation dans queryBuilder
            if (in_array('ROLE_MANAGER', $tabRoles)) $name = 'chefprojet';
            else if (in_array('ROLE_PORTEUR_PROJET', $tabRoles)) $name = 'porteur';

            $currentUser = $this->getUser()->getUserIdentifier();

            $qb = $entityManager->createQueryBuilder();

            $qb->select('p')
                ->from(Projet::class, 'p')
                ->join('p.' . $name, 'u')
                ->where('u.username = :currentUser')
                ->setParameter('currentUser', $currentUser);

            $projets = $qb->getQuery()->getResult();

            return $this->render('projet/index.html.twig', [
                'projets' => $projets
            ]);
        } else if (in_array('ROLE_SUPER_ADMIN', $tabRoles)) {
            return $this->render('projet/index.html.twig', [
                'projets' => $projetRepository->findAll()
            ]);
        } else {
            return $this->render('error.html.twig');
        }
    }

    /**
     * Liste des taches associé au developpeur connecté
     * @Route("/liste", name="projet_liste")
     */
    public function list(ProjetRepository $projetRepository)
    {
        $user = $this->getUser();

        $projets = $projetRepository->findBy(['porteur' => $user]);

        // afficher la liste des tâches
        return $this->render('projet/projet_porteur.html.twig', [
            'projets' => $projets
        ]);
    }

    /**
     * @Route("/new", name="app_projet_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ProjetRepository $projetRepository, EntityManagerInterface $em): Response
    {
        $tabRoles = $this->getUser()->getRoles();

        // Get the current user
        $user = $this->getUser();

        if (in_array('ROLE_MANAGER', $tabRoles) || in_array('ROLE_SUPER_ADMIN', $tabRoles)) {
            $projet = new Projet();
            $form = $this->createForm(ProjetType::class, $projet);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $projetRepository->add($projet, true);
                $projet->setChefprojet($user);

                $em->persist($projet);
                $em->flush();

                return $this->redirectToRoute('app_projet_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('projet/new.html.twig', [
                'projet' => $projet,
                'form' => $form,
            ]);
        } else {
            return $this->render('error.html.twig');
        }
    }

    /**
     * @Route("/{id}", name="app_projet_show", methods={"GET"})
     */
    public function show(Projet $projet): Response
    {
        $tabRoles = $this->getUser()->getRoles();
        if (in_array('ROLE_MANAGER', $tabRoles) || in_array('ROLE_SUPER_ADMIN', $tabRoles)) {
            return $this->render('projet/show.html.twig', [
                'projet' => $projet,
            ]);
        } else {
            return $this->render('error.html.twig');
        }
    }

    /**
     * @Route("/{id}/edit", name="app_projet_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Projet $projet, ProjetRepository $projetRepository): Response
    {
        $tabRoles = $this->getUser()->getRoles();
        if (in_array('ROLE_MANAGER', $tabRoles) || in_array('ROLE_SUPER_ADMIN', $tabRoles)) {
            $form = $this->createForm(ProjetType::class, $projet);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $projetRepository->add($projet, true);

                return $this->redirectToRoute('app_projet_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('projet/edit.html.twig', [
                'projet' => $projet,
                'form' => $form,
            ]);
        } else {
            return $this->render('error.html.twig');
        }
    }

    // /**
    //  * @Route("/{id}", name="app_projet_delete", methods={"POST"})
    //  */
    // public function delete(Request $request, Projet $projet, ProjetRepository $projetRepository): Response
    // {
    //     $tabRoles = $this->getUser()->getRoles();
    //     if (in_array('ROLE_MANAGER', $tabRoles) || in_array('ROLE_SUPER_ADMIN', $tabRoles)) {
    //         if ($this->isCsrfTokenValid('delete' . $projet->getId(), $request->request->get('_token'))) {
    //             $projetRepository->remove($projet, true);
    //         }

    //         return $this->redirectToRoute('app_projet_index', [], Response::HTTP_SEE_OTHER);
    //     } else {
    //         return $this->render('error.html.twig');
    //     }
    // }
}
