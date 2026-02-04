<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Enum\TaskStatus;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TaskController extends AbstractController
{
    #[Route('/projets/{id}/taches/nouveau', name: 'task_new', requirements: ['id' => '\\d+'])]
    public function new(Project $project, Request $request, EntityManagerInterface $entityManager): Response
    {
        $task = new Task();
        $task->setProject($project);

        $requestedStatus = $request->query->get('status');
        if (is_string($requestedStatus)) {
            $status = TaskStatus::tryFrom($requestedStatus);
            if ($status) {
                $task->setStatus($status);
            }
        }

        $form = $this->createForm(TaskType::class, $task, [
            'project' => $project,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
        }

        return $this->render('task/new.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/taches/{id}', name: 'task_edit', requirements: ['id' => '\\d+'])]
    public function edit(Task $task, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TaskType::class, $task, [
            'project' => $task->getProject(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('project_show', ['id' => $task->getProject()?->getId()]);
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }
}
