<?php

namespace App\Controller\Api;

use App\Entity\Worker;
use App\Service\WorkerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

#[Route('/api/v1/workers')]
class WorkerController extends AbstractController

{
    private EntityManagerInterface $entityManager;

    private WorkerService $workerService;
    private SerializerInterface $serializer;

    public function __construct(
        EntityManagerInterface $entityManager,
        WorkerService $workerService,
        SerializerInterface $serializer,
    ) {
        $this->entityManager = $entityManager;
        $this->workerService = $workerService;
        $this->serializer = $serializer;
    }

    /**
     * @OA\Get(
     *     summary="Get a list of workers",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@Model(type=Worker::class, groups={"worker:list"}))
     *         )
     *     )
     * )
    * @OA\Tag(name="Workers")
    *
    * Retrieves a list of workers.
    *
    * @param Request $request The HTTP request object.
    * @return JsonResponse A JSON response containing the list of workers or an error message.
    */
    #[Route('/', name: 'app_worker_index', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $queryBuilder = $request->attributes->get('query_builder');

        $results = $queryBuilder->getQuery()->getResult();

        $data = $this->serializer->normalize($results, null, ['groups' => 'worker:list']);

        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     summary="Add a new worker",
     *     tags={"Workers"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(ref=@Model(type=Worker::class))
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Worker added successfully",
     *         @OA\JsonContent(type="object", @OA\Property(property="message", type="string"))
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid data or database error",
     *         @OA\JsonContent(type="object", @OA\Property(property="error", type="string"))
     *     )
     * )
     *
     * Add a new worker based on the provided data.
     *
     * @param Request $request The HTTP request object containing the new worker data.
     * @return JsonResponse A JSON response indicating success or an error message.
     */
    #[Route('/new', name: 'app_worker_new', methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        try {
            $jsonData = $request->getContent();
            $data = json_decode($jsonData, true);

            if ($this->workerService->isWorkerExist($data['pesel'], $data['email'])) {
                throw new \RuntimeException(
                    'Worker with the same pesel or email already exists',
                    Response::HTTP_BAD_REQUEST
                );
            }
            $this->workerService->addWorker($data);

            return new JsonResponse(['message' => 'New worker added successfully'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid data or database error: ' . $e->getMessage()],
                Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @OA\Get(
     *     summary="Get worker details",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the worker",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Worker")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Worker not found"
     *     )
     * )
     * @OA\Tag(name="Workers")
     *
     * Retrieves the details of a specific worker.
     *
     * @param Worker $worker The worker entity to retrieve details for.
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'app_worker_show', methods: ['GET'])]
    public function show(Worker $worker): JsonResponse
    {
        $data = $this->serializer->normalize($worker, null, ['groups' => 'worker:list']);

        return new JsonResponse($data);
    }

    /**
     * @OA\Put(
     *     summary="Update a worker",
     *     tags={"Workers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the worker to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(ref=@Model(type=Worker::class, groups={"worker:update"}))
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Worker")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid data or database error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Worker not found"
     *     )
     * )
     *
     * Updates a worker based on the provided data.
     *
     * @param Request $request The HTTP request object containing the updated worker data.
     * @param Worker $worker The worker entity to be updated.
     * @return JsonResponse A JSON response containing the updated worker data or an error message.
     */
    #[Route('/{id}', name: 'app_worker_update', methods: ['PUT'])]
    public function update(Request $request, Worker $worker): JsonResponse
    {
        try {
            $jsonData = $request->getContent();
            $data = json_decode($jsonData, true);

            $updatedWorkerData = $this->workerService->updateWorker($worker, $data);

            return new JsonResponse($updatedWorkerData);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid data: ' . $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/{id}", name="app_worker_delete", methods={"DELETE"})
     *
     * @OA\Delete(
     *     summary="Delete a worker",
     *     tags={"Workers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the worker to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Worker deleted successfully",
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to delete worker",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Worker not found"
     *     )
     * )
     *
     * Deletes a worker based on their identifier.
     *
     * @param Worker $worker
     * @return JsonResponse A JSON response containing a success message or an error message.
     */
    #[Route('/{id}', name: 'app_worker_delete', methods: ['DELETE'])]
    public function delete(Worker $worker): JsonResponse
    {
        try {
            $this->entityManager->remove($worker);
            $this->entityManager->flush();

            return new JsonResponse(['message' => 'Worker deleted successfully'], Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to delete worker'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
