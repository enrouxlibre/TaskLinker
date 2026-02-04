<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\Task;
use App\Entity\User;
use App\Enum\TaskStatus;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $project = $options['project'];

        $builder
            ->add('name', TextType::class, [
                'label' => 'Titre de la tâche',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
            ])
            ->add('date', DateType::class, [
                'label' => 'Date',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => TaskStatus::cases(),
                'choice_label' => static function (TaskStatus $status): string {
                    return match ($status) {
                        TaskStatus::ToDo => 'To Do',
                        TaskStatus::Doing => 'Doing',
                        TaskStatus::Done => 'Done',
                    };
                },
                'choice_value' => static fn(?TaskStatus $status) => $status?->value,
            ])
            ->add('membre', EntityType::class, [
                'class' => User::class,
                'choice_label' => fn(User $user) => $user->getFirstName() . ' ' . $user->getLastName(),
                'query_builder' => function (EntityRepository $repository) use ($project) {
                    $builder = $repository->createQueryBuilder('u')
                        ->orderBy('u.lastName', 'ASC')
                        ->addOrderBy('u.firstName', 'ASC');

                    if (!$project) {
                        return $builder->where('1 = 0');
                    }

                    return $builder
                        ->innerJoin('u.projects', 'p')
                        ->andWhere('p = :project')
                        ->setParameter('project', $project);
                },
                'required' => false,
                'placeholder' => '',
                'label' => 'Membre',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
            'project' => null,
        ]);

        $resolver->setAllowedTypes('project', [Project::class, 'null']);
    }
}
