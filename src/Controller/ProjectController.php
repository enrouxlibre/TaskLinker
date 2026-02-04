<?php

namespace App\Controller;

use App\Entity\Project;
use App\Enum\TaskStatus;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

class ProjectController extends AbstractController
{
    #[Route('/', name: 'project_index')]
    public function index(ProjectRepository $projectRepository): Response
    {
        return $this->render('project/index.html.twig', [
            'projects' => $projectRepository->findAll(),
        ]);
    }

    #[Route('/projets/nouveau', name: 'project_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
        }

        return $this->render('project/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/projets/{id}/edit', name: 'project_edit', requirements: ['id' => '\\d+'])]
    public function edit(Project $project, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
        }

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/projets/{id}', name: 'project_show', requirements: ['id' => '\\d+'])]
    public function show(Project $project): Response
    {
        $tasksByStatus = [
            TaskStatus::ToDo->value => ['label' => 'To Do', 'items' => []],
            TaskStatus::Doing->value => ['label' => 'Doing', 'items' => []],
            TaskStatus::Done->value => ['label' => 'Done', 'items' => []],
        ];

        foreach ($project->getTasks() as $task) {
            $statusKey = $task->getStatus()?->value ?? TaskStatus::ToDo->value;
            $tasksByStatus[$statusKey]['items'][] = $task;
        }

        return $this->render('project/show.html.twig', [
            'project' => $project,
            'tasksByStatus' => $tasksByStatus,
        ]);
    }
}
