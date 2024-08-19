<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Form\TicketType;
use App\Form\TicketSearchType;
use App\Repository\TacheRepository;
use App\Repository\TicketRepository;
use App\Utils\TicketSearch;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/ticket")
 */
class TicketController extends AbstractController
{
    /**
     * @Route("/", name="app_ticket_index")
     */
    public function index(Request $request, TicketRepository $ticketRepository, TacheRepository $tacheRepository): Response
    {
        $tabRoles = $this->getUser()->getRoles();
        $username = $this->getUser()->getUserIdentifier();

        $statut = new TicketSearch();

        $form1 = $this->createForm(TicketSearchType::class, $statut);
        $form1->handleRequest($request);

        if (in_array('ROLE_PORTEUR_PROJET', $tabRoles) || in_array('ROLE_MANAGER', $tabRoles) || in_array('ROLE_SUPER_ADMIN', $tabRoles)) {

            if (in_array('ROLE_MANAGER', $tabRoles) or in_array('ROLE_SUPER_ADMIN', $tabRoles)) $name = 'chefprojet';
            else if (in_array('ROLE_PORTEUR_PROJET', $tabRoles)) $name = 'porteur';

            $tickets = $ticketRepository->findTicketsForConnectedUser($name, $username);

            if ($form1->isSubmitted() && $form1->isValid()) {
                $formData = $form1->get('filter')->getData();

                if ($formData == 'enCours') {
                    $etat = 'En Cours';
                    $tickets = $ticketRepository->findTachesByTickets($name, $username, $etat);
                } else if ($formData == 'termine') {
                    $etat = 'Terminé';
                    $tickets = $ticketRepository->findTachesByTickets($name, $username, $etat);
                } else {
                    $tickets = $ticketRepository->findTicketNonTraite($name, $username);
                }
            }

            return $this->render('ticket/index.html.twig', [
                'tickets' => $tickets,
                'form1' => $form1->createView(),
            ]);
        } else {
            return $this->render('error.html.twig');
        }
    }

    /**
     * Redirection vers le formulaire des taches
     * @Route("/taches/{id}", name="app_ticket_taches", methods={"GET"})
     */
    public function redirectToTache(Ticket $ticket): Response
    {
        $ticketProjet = $ticket->getProjet()->getNom();

        $ticketId = $ticket->getId();

        return $this->redirectToRoute('app_tache_new', ['ticketProjet' => $ticketProjet, 'ticketId' => $ticketId], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/new", name="app_ticket_new", methods={"GET", "POST"})
     */
    public function new(Request $request, TicketRepository $ticketRepository, EntityManagerInterface $entityManager): Response
    {
        $tabRoles = $this->getUser()->getRoles();
        $currentPorteur = $this->getUser();

        if (in_array('ROLE_PORTEUR_PROJET', $tabRoles)) {
            $ticket = new Ticket();
            $form = $this->createForm(TicketType::class, $ticket);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $ticketRepository->add($ticket, true);

                $ticket->setPorteur($currentPorteur);

                $entityManager->persist($ticket);
                $entityManager->flush();

                return $this->redirectToRoute('app_ticket_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('ticket/new.html.twig', [
                'ticket' => $ticket,
                'form' => $form,
            ]);
        } else {
            return $this->render('error.html.twig');
        }
    }

    /**
     * @Route("/{id}", name="app_ticket_show", methods={"GET"})
     */
    public function show(Ticket $ticket, EntityManagerInterface $entityManager): Response
    {
        $tabRoles = $this->getUser()->getRoles();
        if (in_array('ROLE_MANAGER', $tabRoles) || in_array('ROLE_SUPER_ADMIN', $tabRoles)) {
            $ticket->setEtat('Vu');

            $entityManager->persist($ticket);
            $entityManager->flush();

            return $this->render('ticket/show.html.twig', [
                'ticket' => $ticket,
            ]);
        }
    }

    /**
     * @Route("/{id}/edit", name="app_ticket_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Ticket $ticket, TicketRepository $ticketRepository): Response
    {
        $tabRoles = $this->getUser()->getRoles();
        if (in_array('ROLE_PORTEUR_PROJET', $tabRoles)) {
            $form = $this->createForm(TicketType::class, $ticket);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $ticketRepository->add($ticket, true);

                return $this->redirectToRoute('app_ticket_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('ticket/edit.html.twig', [
                'ticket' => $ticket,
                'form' => $form,
            ]);
        } else {
            return $this->render('error.html.twig');
        }
    }

    /**
     * @Route("/{id}", name="app_ticket_delete", methods={"POST"})
     */
    public function delete(Request $request, Ticket $ticket, TicketRepository $ticketRepository): Response
    {
        $tabRoles = $this->getUser()->getRoles();
        if (in_array('ROLE_PORTEUR_PROJET', $tabRoles) || in_array('ROLE_MANAGER', $tabRoles) || in_array('ROLE_SUPER_ADMIN', $tabRoles)) {
            if ($this->isCsrfTokenValid('delete' . $ticket->getId(), $request->request->get('_token'))) {
                $ticketRepository->remove($ticket, true);
            }

            return $this->redirectToRoute('app_ticket_index', [], Response::HTTP_SEE_OTHER);
        } else {
            return $this->render('error.html.twig');
        }
    }
}
