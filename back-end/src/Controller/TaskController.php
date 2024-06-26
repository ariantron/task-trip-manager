<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\TaskRepository;
use App\Repository\TripRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class TaskController extends AbstractController
{
    private TaskRepository $taskRepository;
    private TripRepository $tripRepository;

    /**
     * @param TaskRepository $taskRepository
     * @param TripRepository $tripRepository
     */
    public function __construct(
        TaskRepository $taskRepository,
        TripRepository $tripRepository
    )
    {
        $this->taskRepository = $taskRepository;
        $this->tripRepository = $tripRepository;
    }

    #[Route('/tasks', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->json(
            $this->taskRepository->findByQuery($request->query->all()),
            context: [AbstractNormalizer::GROUPS => ['task']]
        );
    }

    #[Route('/tasks/{id}', methods: ['GET'])]
    public function show(string $id): Response
    {
        $task = $this->taskRepository->find($id);

        if (!$task) {
            throw $this->createNotFoundException('Task not found!');
        }

        return $this->json(
            $task,
            context: [AbstractNormalizer::GROUPS => ['task']]
        );
    }

    #[Route('/tasks/assign', methods: ['POST'])]
    public function assign(Request $request): Response
    {
        $task = $this->taskRepository->find($request->get('task_id'));
        $trip = $this->tripRepository->find($request->get('trip_id'));

        if (is_null($task)) {
            throw new Exception('Task not found!');
        } elseif (is_null($trip)) {
            throw new Exception('Trip not found!');
        }

        $this->taskRepository->assign($task, $trip);

        return $this->json(
            ['message' => 'Task assigned to trip successfully!']
        );
    }

    /**
     * @throws Exception
     */
    #[Route('/tasks/unassign', methods: ['POST'])]
    public function unassign(Request $request): Response
    {
        $task = $this->taskRepository->find($request->get('task_id'));
        $trip = $this->tripRepository->find($request->get('trip_id'));

        if (is_null($task)) {
            throw new Exception('Task not found!');
        } elseif (is_null($trip)) {
            throw new Exception('Trip not found!');
        }

        $this->taskRepository->unassign($task, $trip);

        return $this->json(
            ['message' => 'Task unassigned from trip successfully!']
        );
    }

    #[Route('/tasks-no-trip', name: "no_trip_tasks", methods: ['GET'])]
    public function indexWithoutTrip(): Response
    {
        return $this->json(
            $this->taskRepository->findBy(['trip' => null]),
            context: [AbstractNormalizer::GROUPS => ['task']]
        );
    }
}
