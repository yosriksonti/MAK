<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @Route("/", name="admin_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="admin_new", methods={"GET", "POST"})
     */
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($user->getRoles()[0] == 'ROLE_DEVELOPER') {
                $user->setRoles(['ROLE_ADMIN', 'ROLE_DEVELOPER', 'ROLE_MODERATOR']);
            }   
            elseif ($user->getRoles()[0] == 'ROLE_ADMIN') {
                $user->setRoles(['ROLE_ADMIN', 'ROLE_MODERATOR']);
            }   
            elseif ($user->getRoles()[0] == 'ROLE_MODERATOR') {
                $user->setRoles(['ROLE_MODERATOR']);
            }

            $user->setPassword($this->passwordHasher->hashPassword(
                $user,
                $form->get('password')->getData()
            ));

            $userRepository->add($user, true);

            return $this->redirectToRoute('admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($user->getRoles()[0] == 'ROLE_DEVELOPER') {
                $user->setRoles(['ROLE_ADMIN', 'ROLE_DEVELOPER', 'ROLE_MODERATOR']);
            }   
            elseif ($user->getRoles()[0] == 'ROLE_ADMIN') {
                $user->setRoles(['ROLE_ADMIN', 'ROLE_MODERATOR']);
            }   
            elseif ($user->getRoles()[0] == 'ROLE_MODERATOR') {
                $user->setRoles(['ROLE_MODERATOR']);
            }

            $user->setPassword($this->passwordHasher->hashPassword(
                $user,
                $form->get('password')->getData()
            ));

            $userRepository->add($user, true);

            return $this->redirectToRoute('admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_delete", methods={"POST"})
     */
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
