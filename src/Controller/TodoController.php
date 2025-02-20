<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Form\TodoType;
use App\Repository\TodoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;


class TodoController extends AbstractController
{
    private TodoRepository $todoRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(TodoRepository $todoRepository, EntityManagerInterface $entityManager)
    {
        $this->todoRepository = $todoRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'todo_index')]
    public function index(): Response
    {
        $todos = $this->todoRepository->findAll();

        return $this->render('todo/index.html.twig', [
            'todos' => $todos,
        ]);
    }

    #[Route('/todo/create', name: 'todo_create')]
    public function create(Request $request,LoggerInterface $logger): Response
    {
        $todo = new Todo();

        // Create and handle the form
        $form = $this->createForm(TodoType::class, $todo);
        $form->handleRequest($request);

     
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($todo);
            $this->entityManager->flush();

            return $this->redirectToRoute('todo_index');
        }
       

        return $this->render('todo/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/todo/{id}/delete', name: 'todo_delete')]
    public function delete(Todo $todo): Response
    {
           // Check if the user has the ROLE_ADMIN role
           if (!$this->isGranted('ROLE_ADMIN')) {
             // Add a flash message
                $this->addFlash('error', 'You do not have permission to delete this task.');

             // Redirect to the task list or another appropriate page
             return $this->redirectToRoute('todo_index');
        }
        
        $this->entityManager->remove($todo);
        $this->entityManager->flush();

        return $this->redirectToRoute('todo_index');
    }

    #[Route('/todo/{id}/toggle', name: 'todo_toggle')]
    public function toggle(Todo $todo): Response
    {
        $todo->setCompleted(!$todo->isCompleted());
        $this->entityManager->flush();

        return $this->redirectToRoute('todo_index');
    }
}
