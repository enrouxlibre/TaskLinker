<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\Task;
use App\Entity\User;
use App\Enum\TaskStatus;
use App\Enum\UserStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Clock\DatePoint;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $usersData = [
            ['firstName' => 'Max', 'lastName' => 'Mégaloman', 'email' => 'max.megalo@evil-corp.example', 'status' => UserStatus::CDI, 'joinDate' => '2018-01-05'],
            ['firstName' => 'Ivy', 'lastName' => 'Infiltrix', 'email' => 'ivy.infiltrix@evil-corp.example', 'status' => UserStatus::CDD, 'joinDate' => '2023-07-01'],
            ['firstName' => 'Dr', 'lastName' => 'Bricolator', 'email' => 'dr.bricolator@evil-corp.example', 'status' => UserStatus::FREELANCE, 'joinDate' => '2021-04-12'],
            ['firstName' => 'Nina', 'lastName' => 'Neon', 'email' => 'nina.neon@evil-corp.example', 'status' => UserStatus::ALTERNANCE, 'joinDate' => '2024-09-02'],
            ['firstName' => 'Hugo', 'lastName' => 'Stagiaire', 'email' => 'hugo.stagiaire@evil-corp.example', 'status' => UserStatus::STAGE, 'joinDate' => '2025-01-15'],
        ];

        $users = [];
        foreach ($usersData as $data) {
            $user = (new User())
                ->setFirstName($data['firstName'])
                ->setLastName($data['lastName'])
                ->setEmail($data['email'])
                ->setStatus($data['status'])
                ->setJoinDate(new DatePoint($data['joinDate']));

            $manager->persist($user);
            $users[] = $user;
        }

        $projectNames = [
            'Plan Zéro Clou (prise de contrôle marketing)',
            'Satellites Sourire™ (propagande positive)',
            'Application "Obéissance Facile"'
        ];
        $projects = [];

        foreach ($projectNames as $name) {
            $project = (new Project())->setName($name);
            $manager->persist($project);
            $projects[] = $project;
        }

        $users[0]->addProject($projects[0]);
        $users[1]->addProject($projects[0]);
        $users[2]->addProject($projects[0]);
        $users[0]->addProject($projects[1]);
        $users[3]->addProject($projects[1]);
        $users[4]->addProject($projects[1]);
        $users[1]->addProject($projects[2]);
        $users[2]->addProject($projects[2]);

        $tasksData = [
            ['name' => 'Rédiger le slogan mondial', 'description' => 'Doit être souriant, rassurant et totalement inévitable.', 'status' => TaskStatus::Doing, 'date' => '2025-09-12', 'project' => 0, 'user' => 0],
            ['name' => 'Lancer la campagne "gratuite"', 'description' => 'Les guillemets sont strictement contractuels.', 'status' => TaskStatus::ToDo, 'date' => '2025-09-20', 'project' => 0, 'user' => 1],
            ['name' => 'Former l’équipe au rire machiavélique', 'description' => 'Atelier voix grave + cape noire.', 'status' => TaskStatus::Done, 'date' => '2025-08-30', 'project' => 0, 'user' => 2],
            ['name' => 'Calibrer les satellites Sourire™', 'description' => 'Ils doivent émettre à 98% de "bonne humeur".', 'status' => TaskStatus::Doing, 'date' => '2025-10-05', 'project' => 1, 'user' => 3],
            ['name' => 'Ajouter un mode nuit', 'description' => 'Parce que même les méchants dorment (un peu).', 'status' => TaskStatus::ToDo, 'date' => '2025-10-14', 'project' => 1, 'user' => 4],
            ['name' => 'Tester le bouton "J’accepte"', 'description' => 'Surtout ne pas afficher le lien "Refuser".', 'status' => TaskStatus::Doing, 'date' => '2025-10-22', 'project' => 2, 'user' => 2],
            ['name' => 'Ajouter un tutoriel trop mignon', 'description' => 'Les utilisateurs baissent la garde quand il y a des chatons.', 'status' => TaskStatus::ToDo, 'date' => '2025-10-28', 'project' => 2, 'user' => 1],
        ];

        foreach ($tasksData as $data) {
            $task = (new Task())
                ->setName($data['name'])
                ->setDescription($data['description'])
                ->setStatus($data['status'])
                ->setDate(new DatePoint($data['date']));

            $projects[$data['project']]->addTask($task);
            $task->setMembre($users[$data['user']]);

            $manager->persist($task);
        }

        $manager->flush();
    }
}
