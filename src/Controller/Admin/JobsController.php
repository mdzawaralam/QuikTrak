<?php

namespace App\Controller\Admin;

use App\Entity\Jobs;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class JobsController extends AbstractController
{
    /**
     * @Route("/api/addJob", methods={"POST"}, name="add_job")
     * @OA\Post(
     *     path="/api/addJob",
     *     summary="Add a new Job",
     *     tags={"Admin"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Job name")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Job Added successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="status", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */

    public function addJob(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        /* Job creation, $request->getContent() having all data as user passing */
        try {
            $data = json_decode($request->getContent(), true);

            /* checking input data empty or not as its coming from user. */
            if (
                empty($data['name'])
            ) {
                return $this->json(['message' => 'Name field are required'], 400);
            }

            $createdAt = new DateTimeImmutable();

            $job = new Jobs();
            $job->setName($data['name']);
            $job->setCreatedAt($createdAt);
            $job->setUpdatedAt($createdAt);
            $job->setStatus(true);

            $entityManager->persist($job);
            $entityManager->flush();

            return $this->json(['message' => 'Job created successfully', 'status' => 201], 201);
        } catch (\Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }


    /**
     * @Route("/api/getAllJobs", methods={"GET"}, name="get_all_jobs")
     * @OA\Post(
     *     path="/api/getAllJobs",
     *     summary="Job List",
     *     tags={"Admin"},
     *     @OA\Response(
     *         response=200,
     *         description="Job List",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="status", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */

    public function getAllJobs(EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            /* Fetch all jobs from the database */
            $jobs = $entityManager->getRepository(Jobs::class)->findAll();

            /* If no jobs found, return an empty list */
            if (!$jobs) {
                return new JsonResponse([
                    'message' => 'No jobs found'
                ], 404);
            }

            /* Map jobs to an array of data to be displayed in the response */
            $jobData = [];
            foreach ($jobs as $job) {
                $jobData[] = [
                    'id' => $job->getId(),
                    'Name' => $job->getName(),
                    'CreatedAt' => $job->getCreatedAt(),
                    'UpdatedAt' => $job->getUpdatedAt(),
                ];
            }

            /* Return the list of jobs as a JSON response */
            return $this->json([
                'jobs' => $jobData
            ]);
        } catch (\Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }


    /**
     * @Route("/api/editJob/{id}", methods={"PATCH"}, name="edit_job")
     * @OA\PATCH(
     *     path="/api/editJob/{id}",
     *     summary="Edit Job",
     *     tags={"Admin"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Job name")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Job Edited successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="status", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */

    public function editJob(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        /* Edit job, $request->getContent() having all data as user passing */
        try {
            /* Find the job by its ID */
            $job = $entityManager->getRepository(Jobs::class)->find($id);

            $updatedAt = new DateTimeImmutable();

            /* If the job is not found, throw a 404 exception */
            if (!$job) {
                throw new NotFoundHttpException('Job not found');
            }

            $data = json_decode($request->getContent(), true);

            /* edited job name. */
            if (isset($data['name'])) {
                $job->setName($data['name']);
            }
            $job->setUpdatedAt($updatedAt);
            $entityManager->flush();

            /* Return a success response */
            return $this->json([
                'message' => 'Job updated successfully',
                'data' => [
                    'id' => $job->getId(),
                    'name' => $job->getName(),
                    'updatedAt' => $job->getUpdatedAt()
                ],
            ]);
        } catch (\Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }


    /**
     * @Route("/api/deleteJob/{id}", methods={"DELETE"}, name="delete_job")
     * @OA\Post(
     *     path="/api/deleteJob/{id}",
     *     summary="Delete Job",
     *     tags={"Admin"},
     *     @OA\Response(
     *         response=200,
     *         description="Job Deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="status", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */

    public function deleteJob(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        /* Delete job */
        try {
            /* Find the job by its ID */
            $job = $entityManager->getRepository(Jobs::class)->find($id);

            /* If the job is not found, throw a 404 exception */
            if (!$job) {
                throw new NotFoundHttpException('Job not found');
            }

            $entityManager->remove($job);
            $entityManager->flush();  /* Persist the change (deletion) */

            /* Return a success response */
            return new JsonResponse([
                'message' => 'Job deleted successfully',
                'code' => 200,
                'job_id' => $id
            ]);
        } catch (\Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }
}
