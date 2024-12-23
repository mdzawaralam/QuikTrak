<?php

namespace App\Controller;

use App\Entity\AssignedJobToUser;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MyjobController extends AbstractController
{
	
	/**
     * @Route("/api/getmMyJobs/{id}", methods={"GET"}, name="get_my_jobs")
     * @OA\Get(
     *     path="/api/getmMyJobs/{id}",
     *     summary="Assigned my Job List",
     *     tag={"User"},
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
     *     ),
	 *     @OA\Response(
     *         response=500,
     *         description="An error occurred"
     *     )
     * )
     */
    public function getmMyJobs(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            /* Fetch all my assigned jobs from the database */
            $jobs = $entityManager->createQueryBuilder();

            $jobs->select(
                'u.id AS user_id',
                'u.name AS userFullname',
                'j.id AS job_id',
                'j.name AS jobName',
                't.name AS timezone_name',
                't.location AS location',
                'aj.id AS AssignedJobId',
                'aj.assigned_at AS AssignedDate',
                'aj.job_status AS JobStatus',
                'aj.job_deadline AS jobdeadline'
            )
                ->from('App\Entity\AssignedJobToUser', 'aj')
                ->join('App\Entity\User', 'u', 'WITH', 'u.id = aj.user_id')
                ->join('App\Entity\Jobs', 'j', 'WITH', 'aj.job_id = j.id')
                ->join('App\Entity\Timezone', 't', 'WITH', 'aj.timezone_id = t.id')

                ->where('aj.user_id = :userId')
                ->groupBy('aj.id')
                ->orderBy('aj.id', 'ASC')
                ->setParameter('userId', $id);

            $results = $jobs->getQuery()->getArrayResult();

            /* If no jobs found, return an empty list */
            if (!$results) {
                return new JsonResponse([
                    'message' => 'No jobs found'
                ], 404);
            }

            /* Return the list of jobs as a JSON response */
            return $this->json([
                'myAssignedJobs' => $results
            ]);
        } catch (\Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }
	
    /**
     * @Route("/api/editMyJob/{id}", methods={"PATCH"}, name="edit_myjob")
     * @OA\Patch(
     *     path="/api/editMyJob/{id}",
     *     summary="Edit My Job",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="job_status", type="string", example="In Progress")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Assigned Job Edited successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Assigned Job updated successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="JobStatus", type="string", example="In Progress"),
     *                 @OA\Property(property="updatedAt", type="string", format="date-time", example="2024-12-22T10:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Job not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An error occurred"
     *     )
     * )
     */
    public function editMyJob(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            // Find the job by ID
            $job = $entityManager->getRepository(AssignedJobToUser::class)->find($id);

            if (!$job) {
                throw new NotFoundHttpException('Job not found');
            }

            $data = json_decode($request->getContent(), true);

            if (isset($data['job_status'])) {
                $job->setJobStatus($data['job_status']);
            }

            $updatedAt = new DateTimeImmutable();
            $job->setUpdatedAt($updatedAt);
            $job->setCompletedJobAt($updatedAt);

            $entityManager->flush();

            return $this->json([
                'message' => 'Assigned Job updated successfully',
                'data' => [
                    'id' => $job->getId(),
                    'JobStatus' => $job->getJobStatus(),
                    'updatedAt' => $job->getUpdatedAt()->format(DateTimeImmutable::ATOM),
                ],
            ], 200);
        } catch (\Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }
}
