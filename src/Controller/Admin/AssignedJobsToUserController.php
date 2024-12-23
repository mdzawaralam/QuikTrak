<?php

namespace App\Controller\Admin;

use DateTimeImmutable;
use App\Entity\AssignedJobToUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AssignedJobsToUserController extends AbstractController
{
    /**
     * @Route("/api/assignJob", methods={"POST"}, name="assign_job")
     * @OA\Post(
     *     path="/api/assignJob",
     *     summary="Assign a new Job",
     *     tags={"Admin"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="user_id", type="integer", example="user Id"),
     * @OA\Property(property="job_id", type="integer", example="job Id"),
     * @OA\Property(property="timezone_id", type="integer", example="timezone Id"),
     * @OA\Property(property="job_deadline", type="date", example="jobdeadline")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Assigned Job successfully",
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

    public function assignJob(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        /* Job creation, $request->getContent() having all data as user passing */
        try {
            $data = json_decode($request->getContent(), true);

            /* checking input data empty or not as its coming from user. */
            if (
                empty($data['user_id']) || empty($data['job_id']) || empty($data['timezone_id']) || empty($data['job_deadline'])
            ) {
                return $this->json(['message' => 'All field are required'], 400);
            }

            /* Convert the job deadline to a DateTime object */
            try {
                $deadline = new \DateTime($data['job_deadline']);
            } catch (\Exception $e) {
                return $this->json(['error' => 'Invalid date format. Expected format: YYYY-MM-DD'], 400);
            }

            $createdAt = new DateTimeImmutable();

            $job = new AssignedJobToUser();
            $job->setUserId($data['user_id']);
            $job->setJobId($data['job_id']);
            $job->setTimezoneId($data['timezone_id']);
            $job->setJobDeadline($deadline);
            $job->setAssignedAt($createdAt);
            $job->setUpdatedAt($createdAt);
            $job->setJobStatus("Pending");

            $entityManager->persist($job);
            $entityManager->flush();

            return $this->json(['message' => 'Job Assigned successfully', 'status' => 201], 201);
        } catch (\Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    /**
     * @Route("/api/getAllAssignedJobs", methods={"GET"}, name="get_all_assignedjobs")
     * @OA\GET(
     *     path="/api/getAllAssignedJobs",
     *     summary="Assigned Job List",
     *     tags={"Admin"},
     *     @OA\Response(
     *         response=201,
     *         description="Assigned Job List",
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

    public function getAllAssignedJobs(EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            /* Fetch all assigned jobs from the database */
            $jobs = $entityManager->createQueryBuilder();

            $jobs->select(
                'u.id AS user_id',
                'u.name AS userFullname',
                'j.id AS job_id',
                'j.name AS jobName',
                't.name AS timezone_name',
                't.location AS location',
                'aj.assigned_at AS AssignedDate',
                'aj.id AS AssignedJobId',
                'aj.job_status AS JobStatus',
                'aj.job_deadline AS jobdeadline'
            )
                ->from('App\Entity\AssignedJobToUser', 'aj')
                ->join('App\Entity\User', 'u', 'WITH', 'u.id = aj.user_id')
                ->join('App\Entity\Jobs', 'j', 'WITH', 'aj.job_id = j.id')
                ->join('App\Entity\Timezone', 't', 'WITH', 'aj.timezone_id = t.id')
                ->groupBy('aj.id')
                ->orderBy('aj.id', 'ASC');
            // Optional: Order the results

            $results = $jobs->getQuery()->getArrayResult();

            /* If no jobs found, return an empty list */
            if (!$results) {
                return new JsonResponse([
                    'message' => 'No jobs found'
                ], 404);
            }

            /* Return the list of jobs as a JSON response */
            return $this->json([
                'allAssignedJobs' => $results
            ]);
        } catch (\Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }


    /**
     * @Route("/api/editAssignedJob/{id}", methods={"PATCH"}, name="edit_assignedjob")
     * @OA\PATCH(
     *     path="/api/editAssignedJob/{id}",
     *     summary="Edit Assigned Job",
     *     tags={"Admin"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object"@OA\Property(property="user_id", type="integer", example="user Id"),
     * @OA\Property(property="job_id", type="integer", example="job Id"),
     * @OA\Property(property="timezone_id", type="integer", example="timezone Id"),
     * @OA\Property(property="job_deadline", type="date", example="jobdeadline")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Assigned Job Edited successfully",
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

    public function editAssignedJob(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        /* Edit Assigned job, $request->getContent() having all data as user passing */
        try {
            /* Find the job by its ID */
            $job = $entityManager->getRepository(AssignedJobToUser::class)->find($id);

            $updatedAt = new DateTimeImmutable();

            /* If the job is not found, throw a 404 exception */
            if (!$job) {
                throw new NotFoundHttpException('Job not found');
            }

            $data = json_decode($request->getContent(), true);

            /* edited assigned jobs. */
            if (isset($data['user_id'])) {
                $job->setUserId($data['user_id']);
            }
            if (isset($data['job_id'])) {
                $job->setJobId($data['job_id']);
            }
            if (isset($data['timezone_id'])) {
                $job->setTimezoneId($data['timezone_id']);
            }
            if (isset($data['job_deadline'])) {
                /* Convert the job deadline to a DateTime object */
                try {
                    $deadline = new \DateTime($data['job_deadline']);
                } catch (\Exception $e) {
                    return $this->json(['error' => 'Invalid date format. Expected format: YYYY-MM-DD'], 400);
                }
                $job->setJobDeadline($deadline);
            }

            $job->setUpdatedAt($updatedAt);
            $entityManager->flush();

            /* Return a success response */
            return $this->json([
                'message' => 'Assigned Job updated successfully',
                'data' => [
                    'id' => $job->getId(),
                    'updatedAt' => $job->getUpdatedAt()
                ],
            ]);
        } catch (\Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    /**
     * @Route("/api/deleteAssignedJob/{id}", methods={"DELETE"}, name="delete_assignedjob")
     * @OA\DELETE(
     *     path="/api/deleteAssignedJob/{id}",
     *     summary="Delete Assigned Job",
     *     tags={"Admin"},
     *     @OA\Response(
     *         response=201,
     *         description="Assigned Job Deleted successfully",
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

    public function deleteAssignedJob(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        /* Delete assigned job */
        try {
            /* Find the job by its ID */
            $job = $entityManager->getRepository(AssignedJobToUser::class)->find($id);

            /* If the job is not found, throw a 404 exception */
            if (!$job) {
                throw new NotFoundHttpException('Job not found');
            }

            $entityManager->remove($job);
            $entityManager->flush();  /* Persist the change (deletion) */

            /* Return a success response */
            return new JsonResponse([
                'message' => 'Assigned Job deleted successfully',
                'job_id' => $id
            ]);
        } catch (\Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }
}
