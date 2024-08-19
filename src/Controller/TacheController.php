<?php

namespace App\Controller;

use DateTime;
use App\Entity\Tache;
use App\Form\TacheType;
use App\Entity\LimitePage;
use App\Entity\Pagination;
use App\Entity\TacheSearch;
use App\Entity\TacheStatut;
use App\Entity\Notification;
use App\Form\LimitePageType;
use App\Form\TacheSearchType;
use App\Form\TacheStatutType;
use Symfony\Component\Mime\Email;
use App\Repository\TacheRepository;
use App\Repository\StatutRepository;
use App\Repository\TicketRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;

/**
 * @Route("/tache")
 */
class TacheController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * Affichage des taches en fonctions du role des utilisateurs
     * @Route("/", name="app_tache_index")
     */
    public function index(Request $request, TacheRepository $tacheRepository): Response
    {
        $search = new TacheSearch();
        $statut = new TacheStatut();
        $limitePage = new LimitePage();
        $tabRoles = $this->getUser()->getRoles();

        $formLimite = $this->createForm(LimitePageType::class, $limitePage);
        $formLimite->handleRequest($request);

        // filtre par utilisateurs
        $form1 = $this->createForm(TacheSearchType::class, $search);
        $form1->handleRequest($request);

        // filtre par statut
        $form2 = $this->createForm(TacheStatutType::class, $statut);
        $form2->handleRequest($request);

        // pagination
        $pagination = new Pagination();
        $page = $request->query->getInt('page', 1);

        $filter = $request->query->get('filter');
        $filterByStatut = $request->query->get('statut');
        $pageNumber = $request->query->get('page');

        $limit = 10;

        if ($formLimite->isSubmitted() && $formLimite->isValid()) {
            $limit = $formLimite->get('nombre')->getData();
            $request->query->set('limit', $limit);
        }

        $limitUrl = $request->query->get('limit');
        // $limit = 5;
        if (in_array('ROLE_MANAGER', $tabRoles) || in_array('ROLE_PORTEUR_PROJET', $tabRoles)) {
            // affichage des taches selon porteur ou chefProjet
            if (in_array('ROLE_MANAGER', $tabRoles)) $name = 'chefprojet';
            else if (in_array('ROLE_PORTEUR_PROJET', $tabRoles)) $name = 'porteur';

            $username = $this->getUser()->getUserIdentifier();

            // filtres
            if ($form1->isSubmitted() && $form1->isValid()) {
                $request->query->set('limit', $limit);

                $devData = $form1->get('user')->getData();
                $usernameFilter = $devData->first()->getUsername();

                // filtrer les taches par utilisateurs
                $taches = $tacheRepository->filterTasksByDev($name, $username, $usernameFilter)->getQuery()->getResult();

                if ($limitUrl <= 0) {
                    $limitUrl = 5;
                }

                if ($limitUrl) {
                    $paginator = $pagination->chunkTable($request, $taches, $limitUrl);
                    $current_chunk = $paginator['current_chunk'];
                    $chunks = $paginator['chunks'];

                    // statut => null
                    $pagination_links = $pagination->generatePaginationLinks(count($chunks), $page, $usernameFilter, null, $limitUrl);
                } else {
                    $paginator = $pagination->chunkTable($request, $taches, $limit);

                    $current_chunk = $paginator['current_chunk'];
                    $chunks = $paginator['chunks'];

                    $pagination_links = $pagination->generatePaginationLinks(count($chunks), $page, $usernameFilter, null, $limit);
                }
            } else if ($form2->isSubmitted() && $form2->isValid()) {
                $request->query->set('page', 1);

                $statutData = $form2->get('statut')->getData();
                $etatFilter = $statutData->first()->getEtat();

                // filtrer les taches par statut
                $taches = $tacheRepository->filterTasksByStatut($name, $username, $etatFilter)->getQuery()->getResult();

                $limitUrl = $request->query->get('limit');

                if ($limitUrl <= 0) {
                    $limitUrl = 5;
                }

                if ($limitUrl) {
                    $paginator = $pagination->chunkTable($request, $taches, $limitUrl);
                    $current_chunk = $paginator['current_chunk'];
                    $chunks = $paginator['chunks'];

                    $pagination_links = $pagination->generatePaginationLinks(count($chunks), $page, $etatFilter, $etatFilter, $limitUrl);
                } else {
                    $paginator = $pagination->chunkTable($request, $taches, $limit);

                    $current_chunk = $paginator['current_chunk'];
                    $chunks = $paginator['chunks'];

                    $pagination_links = $pagination->generatePaginationLinks(count($chunks), $page, $etatFilter, $etatFilter, $limit);
                }
            } else {
                // Récupérer les tâches correspondantes à l'utilisateur connecté
                $taches = $tacheRepository->findAllTasksByConnectedUser($name, $username)->getQuery()->getResult();

                // Filtrer les tâches si un filtre est présent dans la requête
                $filter = $request->query->get('filter');
                if ($filter) {
                    $taches = $tacheRepository->filterTasksByDev($name, $username, $filter)->getQuery()->getResult();
                }

                // Filtrer les tâches par statut si un filtre est présent dans la requête
                $filterByStatut = $request->query->get('statut');
                if ($filterByStatut) {
                    $taches = $tacheRepository->filterTasksByStatut($name, $username, $filterByStatut)->getQuery()->getResult();
                }

                $limitUrl = $request->query->get('limit');

                if ($limitUrl <= 0) {
                    $limitUrl = 5;
                }

                if ($limitUrl) {
                    $paginator = $pagination->chunkTable($request, $taches, $limitUrl);
                    $current_chunk = $paginator['current_chunk'];
                    $chunks = $paginator['chunks'];

                    // Récupérer le numéro de page à afficher
                    $pageNumber = $request->query->getInt('page', 1);

                    $total_pages = count($chunks);

                    // Générer les liens de pagination
                    $pagination_links = $pagination->generatePaginationLinks($total_pages, $pageNumber, $filter, $filterByStatut, $limitUrl);
                } else {
                    $paginator = $pagination->chunkTable($request, $taches, $limit);
                    $current_chunk = $paginator['current_chunk'];
                    $chunks = $paginator['chunks'];

                    // Récupérer le numéro de page à afficher
                    $pageNumber = $request->query->getInt('page', 1);

                    $total_pages = count($chunks);

                    // Générer les liens de pagination
                    $pagination_links = $pagination->generatePaginationLinks($total_pages, $pageNumber, $filter, $filterByStatut, $limit);
                }
            }

            $page = $request->query->getInt('page', 1);

            return $this->render('tache/index.html.twig', [
                'taches' => $current_chunk,
                'formLimite' => $formLimite->createView(),
                'form1' => $form1->createView(),
                'form2' => $form2->createView(),
                'total_pages' => count($chunks),
                'current_page' => $page,
                'pagination_links' => $pagination_links
            ]);
        } else if (in_array('ROLE_SUPER_ADMIN', $tabRoles)) {
            // filtres
            if ($form1->isSubmitted() && $form1->isValid()) {
                $username = $form1->get('user')->getData();
                $usernameFilter = $username->first();

                $taches = $tacheRepository->findBy(["developpeur" => $usernameFilter]);

                $pageNumber = $request->query->getInt('page', 1);

                if ($limitUrl <= 0) {
                    $limitUrl = 5;
                }

                if ($limitUrl) {
                    // pagination du filtre
                    $paginator = $pagination->chunkTable($request, $taches, $limitUrl);
                    $current_chunk = $paginator['current_chunk'];
                    $chunks = $paginator['chunks'];

                    $pagination_links = $pagination->generatePaginationLinks(count($chunks), $pageNumber, $usernameFilter->getUserName(), null, $limitUrl);
                } else {
                    // pagination du filtre
                    $paginator = $pagination->chunkTable($request, $taches, $limit);
                    $current_chunk = $paginator['current_chunk'];
                    $chunks = $paginator['chunks'];

                    $pagination_links = $pagination->generatePaginationLinks(count($chunks), $pageNumber, $usernameFilter->getUserName(), null, $limit);
                }
            } else if ($form2->isSubmitted() && $form2->isValid()) {
                $request->query->set('page', 1);

                $statut = $form2->get('statut')->getData();
                $etatFilter = $statut->first();

                $taches = $tacheRepository->findBy(["statut" => $etatFilter]);

                $pageNumber = $request->query->getInt('page', 1);

                if ($limitUrl) {
                    // pagination du filtre
                    $paginator = $pagination->chunkTable($request, $taches, $limitUrl);
                    $current_chunk = $paginator['current_chunk'];
                    $chunks = $paginator['chunks'];

                    $pagination_links = $pagination->generatePaginationLinks(count($chunks), $pageNumber, $etatFilter->getEtat(), $etatFilter->getEtat(), $limitUrl);
                } else {
                    // pagination du filtre
                    $paginator = $pagination->chunkTable($request, $taches, $limit);
                    $current_chunk = $paginator['current_chunk'];
                    $chunks = $paginator['chunks'];

                    $pagination_links = $pagination->generatePaginationLinks(count($chunks), $pageNumber, $etatFilter->getEtat(), $etatFilter->getEtat(), $limit);
                }
            } else {
                $taches = $tacheRepository->findBy([], ['id' => 'DESC']);

                // pagination des filtres
                if ($filter) {
                    $taches = $tacheRepository->filterDev($filter);
                }

                if ($filterByStatut) {
                    $taches = $tacheRepository->filterStatut($filterByStatut);
                }

                $limitUrl = $request->query->get('limit');

                if ($limitUrl <= 0) {
                    $limitUrl = 5;
                }

                if ($limitUrl) {
                    // Pagination
                    $paginator = $pagination->chunkTable($request, $taches, $limitUrl);
                    $current_chunk = $paginator['current_chunk'];
                    $chunks = $paginator['chunks'];

                    $pageNumber = $request->query->getInt('page', 1);

                    $total_pages = count($chunks);

                    // Générer les liens de pagination
                    $pagination_links = $pagination->generatePaginationLinks($total_pages, $pageNumber, $filter, $filterByStatut, $limitUrl);
                } else {
                    // Pagination
                    $paginator = $pagination->chunkTable($request, $taches, $limit);
                    $current_chunk = $paginator['current_chunk'];
                    $chunks = $paginator['chunks'];

                    $pageNumber = $request->query->getInt('page', 1);

                    $total_pages = count($chunks);

                    // Générer les liens de pagination
                    $pagination_links = $pagination->generatePaginationLinks($total_pages, $pageNumber, $filter, $filterByStatut, $limit);
                }
            }

            return $this->render('tache/index.html.twig', [
                'taches' => $current_chunk,
                'formLimite' => $formLimite->createView(),
                'form1' => $form1->createView(),
                'form2' => $form2->createView(),
                'total_pages' => count($chunks),
                'current_page' => $pageNumber,
                'pagination_links' => $pagination_links
            ]);
        } else {
            return $this->render('error.html.twig');
        }
    }

    /**
     * Liste des taches assocé à un developpeur
     * @Route("/liste", name="tache_liste")
     */
    public function list(Request $request, TacheRepository $tacheRepository)
    {
        $user = $this->getUser();

        $username = $user->getUserIdentifier();

        $statut = new TacheStatut();

        $formStatut = $this->createForm(TacheStatutType::class, $statut);
        $formStatut->handleRequest($request);

        // récupérer la liste des tâches attribuées au ROLE_DEV
        $taches = $tacheRepository->findBy(['developpeur' => $user], ['dateEchUser' => 'ASC']);

        if ($formStatut->isSubmitted() && $formStatut->isValid()) {
            $statutData = $formStatut->get('statut')->getData();
            $statut = $statutData->first()->getEtat();

            $taches = $tacheRepository->filterByStatutForDev($statut, $username);
        }

        // afficher la liste des tâches
        return $this->render('tache/tache_dev.html.twig', [
            'taches' => $taches,
            'formStatut' => $formStatut->createView()
        ]);
    }

    /**
     * ticketProjet = null (le paramètre n'est pas obligatoire)
     * @Route("/new/{ticketProjet}/{ticketId}", name="app_tache_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, TicketRepository $ticketRepository, string $ticketProjet = null, int $ticketId = null): Response
    {
        $tabRoles = $this->getUser()->getRoles();
        if (in_array('ROLE_MANAGER', $tabRoles) || in_array('ROLE_SUPER_ADMIN', $tabRoles)) {
            $tache = new Tache();

            // envoyer la variable ticketProjet dans TacheType
            $form = $this->createForm(TacheType::class, $tache, [
                'ticketProjet' => $ticketProjet,
            ]);

            $form->handleRequest($request);

            $ticket = $ticketRepository->findOneBy(['id' => $ticketId]);

            if ($form->isSubmitted() && $form->isValid()) {
                if ($ticket !== null) {
                    $ticket->setTache($tache);

                    $entityManager->persist($ticket);
                    $entityManager->flush();
                }

                $entityManager->persist($tache);
                $entityManager->flush();

                return $this->redirectToRoute('app_tache_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('tache/new.html.twig', [
                'tache' => $tache,
                'form' => $form,
            ]);
        } else {
            return $this->render('error.html.twig');
        }
    }

    /**
     * @Route("/{id}", name="app_tache_show", methods={"GET"})
     */
    public function show(Tache $tache): Response
    {
        return $this->render('tache/show.html.twig', [
            'tache' => $tache,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_tache_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Tache $tache, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TacheType::class, $tache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tache_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tache/edit.html.twig', [
            'tache' => $tache,
            'form' => $form,
        ]);
    }

    /**
     * methode pour terminer une tache par un developpeur
     * @Route("/terminer/{id}", name="tache_terminer", methods={"GET", "POST"})
     */
    public function editStatutDev(
        Tache $tache,
        EntityManagerInterface $entityManager,
        StatutRepository $statutRepository,
        MailerInterface $mailer,
        Environment $twig
    ) {
        $newStatut = $statutRepository->findOneBy(['etat' => 'Terminé']);

        $timezone = new \DateTimeZone(date_default_timezone_get());
        $date = new DateTime('now', $timezone);

        $notification = new Notification();

        $user = $this->getUser();

        // Recherche l'email du Chef de Projet
        $manager = $tache->getProjet()->getChefprojet();
        $managerUsername = $manager->getUserIdentifier();
        $managerEmail = $manager->getEmail();

        // Recherche du nom du projet
        $projet = $tache->getProjet();

        // charge le template twig pour l'email
        $template = $twig->load('email/email.html.twig');
        $html = $template->render([
            'tache' => $tache,
            'projet' => $projet,
            'managerUsername' => $managerUsername
        ]);

        $userEmail = $user->getEmail();

        // n'envoie pas le mail si le chef de projet n'a pas de mail
        if ($managerEmail != null) {
            // Sending email
            $email = (new Email())
                ->from($userEmail)
                // ->from('contact@planvacances.net')
                // ->from('josephmickael03@gmail.com')
                ->to($managerEmail)
                ->subject('Tache terminé')
                ->html($html);

            $mailer->send($email);
        } else {
            $this->addFlash('warning', "L'email n'a pas été pas envoyé");
        }

        // Date terminer pour tache et ajout nouvelle statut
        $tache->setStatut($newStatut);
        $tache->setDateterminer($date);

        // Notification
        $notification->setTitre("Terminé");
        $notification->setUser($user);
        $notification->setEtat("pas encore vu");
        $notification->setTache($tache);

        $entityManager->persist($notification);
        $entityManager->flush($notification);

        // Persist tache
        $entityManager->persist($tache);
        $entityManager->flush($tache);

        return $this->redirectToRoute('tache_liste', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}", name="app_tache_delete", methods={"POST"})
     */
    public function delete(Request $request, Tache $tache, EntityManagerInterface $entityManager): Response
    {
        $tabRoles = $this->getUser()->getRoles();
        if (in_array('ROLE_MANAGER', $tabRoles) || in_array('ROLE_SUPER_ADMIN', $tabRoles)) {
            if ($this->isCsrfTokenValid('delete' . $tache->getId(), $request->request->get('_token'))) {
                $entityManager->remove($tache);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_tache_index', [], Response::HTTP_SEE_OTHER);
        } else {
            return $this->render('error.html.twig');
        }
    }
}
