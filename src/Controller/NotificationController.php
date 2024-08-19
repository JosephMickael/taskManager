<?php

namespace App\Controller;

use Twig\Environment;
use App\Entity\Projet;
use App\Entity\Notification;
use App\Form\NotificationType;
use Doctrine\ORM\EntityManager;
use App\Repository\TacheRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\NotificationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/notification")
 */
class NotificationController extends AbstractController
{
    private $tacheRepository;
    private $notificationRepository;
    private $session;

    public function __construct(TacheRepository $tacheRepository, SessionInterface $session, NotificationRepository $notificationRepository)
    {
        $this->tacheRepository = $tacheRepository;
        $this->notificationRepository = $notificationRepository;
        $this->session = $session;
    }

    /**
     * @Route("/", name="app_notification_index", methods={"GET"})
     */
    public function index(): Response
    {
        $tabRoles = $this->getUser()->getRoles();
        $username = $this->getUser()->getUserIdentifier();

        if (in_array('ROLE_MANAGER', $tabRoles)) {
            // Nombre de taches pas encore terminé
            $tachesNonTerminees = $this->tacheRepository->findByTachesNonTermine($username);

            $countTachesNonTermine = count($tachesNonTerminees);

            // affichage des taches selon porteur ou chefProjet
            $notifications = $this->notificationRepository->findByConnectedUser($username);
        } else {
            return $this->render('error.html.twig');
        }

        return $this->render('notification/index.html.twig', [
            'notifications' => $notifications,
            'countTachesNonTermine' => $countTachesNonTermine
        ]);
    }

    /**
     * Retourne en json le nombre de taches pas encore vues
     * @Route("/count-taches-non-vues", name="count_taches_non_vues", methods={"GET"})
     */
    public function getCountTachesNonVues()
    {
        $tabRoles = $this->getUser()->getRoles();
        $username = $this->getUser()->getUserIdentifier();

        if (in_array('ROLE_MANAGER', $tabRoles)) {
            $tachesNonVues = $this->tacheRepository->findByTachesNonVue($username);
        }

        $countTachesNonVues = count($tachesNonVues);

        return $this->json(['count' => $countTachesNonVues], 200);
    }

    // /**
    //  * @Route("/new", name="app_notification_new", methods={"GET", "POST"})
    //  */
    // public function new(Request $request, NotificationRepository $notificationRepository): Response
    // {
    //     $tabRoles = $this->getUser()->getRoles();
    //     if (in_array('ROLE_MANAGER', $tabRoles)) {
    //         $notification = new Notification();
    //         $form = $this->createForm(NotificationType::class, $notification);
    //         $form->handleRequest($request);

    //         if ($form->isSubmitted() && $form->isValid()) {
    //             $notificationRepository->add($notification, true);

    //             return $this->redirectToRoute('app_notification_index', [], Response::HTTP_SEE_OTHER);
    //         }

    //         return $this->renderForm('notification/new.html.twig', [
    //             'notification' => $notification,
    //             'form' => $form,
    //         ]);
    //     } else {
    //         return $this->render('error.html.twig');
    //     }
    // }

    /**
     * @Route("/{id}", name="app_notification_show", methods={"GET"})
     */
    public function show(Notification $notification, EntityManagerInterface $entityManager): Response
    {
        $tabRoles = $this->getUser()->getRoles();

        if (in_array('ROLE_MANAGER', $tabRoles)) {
            $notification->setEtat("Vu");

            if ($notification->getTache() != null) {
                $tacheId = $notification->getTache()->getId();
            } else {
                return $this->render('error.html.twig');
            }

            $qb = $entityManager->createQueryBuilder();
            $qb->select('p')
                ->from(Projet::class, 'p')
                ->join('p.taches', 't')
                ->where('t.id = :tacheId')
                ->setParameter('tacheId', $tacheId);

            $projet = $qb->getQuery()->getSingleResult();

            $entityManager->persist($notification);
            $entityManager->flush();

            return $this->render('notification/show.html.twig', [
                'notification' => $notification,
                'nomTache' => $notification->getTache()->getNom(),
                'dateTerminer' => $notification->getTache()->getDateterminer(),
                'userTermine' => $notification->getUser()->getUserIdentifier(),
                'projet' => $projet
            ]);
        } else {
            return $this->render('error.html.twig');
        }
    }

    // /**
    //  * @Route("/{id}/edit", name="app_notification_edit", methods={"GET", "POST"})
    //  */
    // public function edit(Request $request, Notification $notification, NotificationRepository $notificationRepository): Response
    // {
    //     $tabRoles = $this->getUser()->getRoles();
    //     if (in_array('ROLE_MANAGER', $tabRoles)) {
    //         $form = $this->createForm(NotificationType::class, $notification);
    //         $form->handleRequest($request);

    //         if ($form->isSubmitted() && $form->isValid()) {
    //             $notificationRepository->add($notification, true);

    //             return $this->redirectToRoute('app_notification_index', [], Response::HTTP_SEE_OTHER);
    //         }

    //         return $this->renderForm('notification/edit.html.twig', [
    //             'notification' => $notification,
    //             'form' => $form,
    //         ]);
    //     } else {
    //         return $this->render('error.html.twig');
    //     }
    // }

    /**
     * @Route("/{id}", name="app_notification_delete", methods={"POST"})
     */
    public function delete(Request $request, Notification $notification, NotificationRepository $notificationRepository): Response
    {
        $tabRoles = $this->getUser()->getRoles();
        if (in_array('ROLE_MANAGER', $tabRoles) || in_array('ROLE_SUPER_ADMIN', $tabRoles)) {
            if ($this->isCsrfTokenValid('delete' . $notification->getId(), $request->request->get('_token'))) {
                $notificationRepository->remove($notification, true);
            }

            return $this->redirectToRoute('app_notification_index', [], Response::HTTP_SEE_OTHER);
        } else {
            return $this->render('error.html.twig');
        }
    }
}
