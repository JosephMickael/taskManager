<?php

namespace App\Controller;

use Exception;
use App\Entity\User;
use App\Form\UserType;
use App\Utils\PasswordForm;
use App\Form\PasswordFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="app_user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        $tabRoles = $this->getUser()->getRoles();

        if (in_array('ROLE_SUPER_ADMIN', $tabRoles)) {
            return $this->render('user/index.html.twig', [
                'users' => $userRepository->findAll(),
            ]);
        }
    }

    /**
     * @Route("/new", name="app_user_new", methods={"GET", "POST"})
     */
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $tabRoles = $this->getUser()->getRoles();

        if (in_array('ROLE_SUPER_ADMIN', $tabRoles)) {
            $user = new User();
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $userRepository->add($user, true);

                return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
                // return new JsonResponse(['redirect' => $this->generateUrl('app_user_index')]);
            }
            return $this->renderForm('user/new.html.twig', [
                'user' => $user,
                'form' => $form,
            ]);
        }
    }

    /**
     * @Route("/{id}", name="app_user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_user_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user, true);

            $this->addFlash('success', 'Modifié avec succès');

            return $this->redirectToRoute('app_user_show', ['id' => $request->get('id')], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/changepassword/{id}", name="change_password")
     */
    public function changePassword(Request $request, User $user, UserPasswordHasherInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {
        $password = new PasswordForm();
        $form = $this->createForm(PasswordFormType::class, $password);
        $form->handleRequest($request);

        // $user = $this->getUser();
        $userId = $request->get('id');

        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $form->get('oldPassword')->getData();
            $newPassword = $form->get('newPassword')->getData();

            // Vérifier que l'ancien mot de passe est correct
            if (!$passwordEncoder->isPasswordValid($user, $oldPassword)) {
                $this->addFlash('error', 'Ancien mot de passe incorrecte');
            } else {
                // Hacher le nouveau mot de passe
                $hashedPassword = $passwordEncoder->hashPassword($user, $newPassword);

                // Mettre à jour le mot de passe de l'utilisateur
                $user->setPassword($hashedPassword);

                // Enregistrer l'utilisateur dans la base de données
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Mot de passe modifié avec succès');

                // Rediriger vers une page de confirmation
                return $this->redirectToRoute('app_user_show', ['id' => $userId]);
            }
        }

        return $this->render('user/changePassword.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="app_user_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        $tabRoles = $this->getUser()->getRoles();

        if (in_array('ROLE_SUPER_ADMIN', $tabRoles)) {
            if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
                $userRepository->remove($user, true);
            }

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }
    }
}
