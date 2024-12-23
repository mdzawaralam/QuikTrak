<?php

namespace App\Controller\Admin;

use App\Entity\Timezone;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TimezoneController extends AbstractController
{
    /**
     * @Route("/api/addTimezone", methods={"POST"}, name="add_timezone")
     * @OA\Post(
     *     path="/api/addTimezone",
     *     summary="Add a new TimeZone",
     *     tags={"Admin"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Job name"),
     * @OA\Property(property="location", type="string", example="Location name")
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

    public function addTimezone(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        /* Timezone creation, $request->getContent() having all data as user passing */
        try {
            $data = json_decode($request->getContent(), true);

            /* checking input data empty or not as its coming from user. */
            if (
                empty($data['name']) || empty($data['location'])
            ) {
                return $this->json(['message' => 'All field are required'], 400);
            }

            $createdAt = new DateTimeImmutable();

            $timezone = new Timezone();
            $timezone->setName($data['name']);
            $timezone->setLocation($data['location']);
            $timezone->setCreatedAt($createdAt);
            $timezone->setUpdatedAt($createdAt);
            $timezone->setStatus(true);

            $entityManager->persist($timezone);
            $entityManager->flush();

            return $this->json(['message' => 'Timezone created successfully', 'status' => 201], 201);
        } catch (\Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    /**
     * @Route("/api/getAllTimezone", methods={"GET"}, name="get_all_timezone")
     * @OA\GET(
     *     path="/api/getAllTimezone",
     *     summary="Timezone List",
     *     tags={"Admin"},
     *     @OA\Response(
     *         response=200,
     *         description="Timezone List",
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

    public function getAllTimezone(EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            /* Fetch all timezone from the database */
            $timezones = $entityManager->getRepository(Timezone::class)->findAll();

            /* If no timezone found, return an empty list */
            if (!$timezones) {
                return new JsonResponse([
                    'message' => 'No timezone found'
                ], status: 404);
            }

            /* Map timezone to an array of data to be displayed in the response */
            $timezoneData = [];
            foreach ($timezones as $timezone) {
                $timezoneData[] = [
                    'id' => $timezone->getId(),
                    'Name' => $timezone->getName(),
                    'location' => $timezone->getLocation(),
                    'CreatedAt' => $timezone->getCreatedAt(),
                    'UpdatedAt' => $timezone->getUpdatedAt(),
                ];
            }

            /* Return the list of jobs as a JSON response */
            return $this->json([
                'timezone' => $timezoneData
            ]);
        } catch (\Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    /**
     * @Route("/api/editTimezone/{id}", methods={"PATCH"}, name="edit_timezone")
     * @OA\PATCH(
     *     path="/api/editTimezone/{id}",
     *     summary="Edit Timezone",
     *     tags={"Admin"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="location", type="string", example="location name"),
     * @OA\Property(property="name", type="string", example="Timezone name")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Timezone Edited successfully",
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

    public function editTimezone(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        /* Edit Timezone, $request->getContent() having all data as user passing */
        try {
            /* Find the timezone by its ID */
            $timezone = $entityManager->getRepository(Timezone::class)->find($id);

            $updatedAt = new DateTimeImmutable();

            /* If the timezone is not found, throw a 404 exception */
            if (!$timezone) {
                throw new NotFoundHttpException('timezone not found');
            }

            $data = json_decode($request->getContent(), true);

            /* edited Timezone. */
            if (isset($data['name']) || isset($data['location'])) {
                $timezone->setName($data['name']);
                $timezone->setLocation($data['location']);
            }
            $timezone->setUpdatedAt($updatedAt);
            $entityManager->flush();

            /* Return a success response */
            return $this->json([
                'message' => 'Timezone updated successfully',
                'code' => 201,
                'data' => [
                    'id' => $timezone->getId(),
                    'name' => $timezone->getName(),
                    'location' => $timezone->getLocation(),
                    'updatedAt' => $timezone->getUpdatedAt()
                ],
            ]);
        } catch (\Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    /**
     * @Route("/api/deleteTimezone/{id}", methods={"DELETE"}, name="delete_timezone")
     * @OA\DELETE(
     *     path="/api/deleteTimezone/{id}",
     *     summary="Delete timezone",
     *     tags={"Admin"},
     *     @OA\Response(
     *         response=200,
     *         description="Timezone Deleted successfully",
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

    public function deleteTimezone(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        /* Delete Timezone */
        try {
            /* Find the Timezone by its ID */
            $timezone = $entityManager->getRepository(Timezone::class)->find($id);

            /* If the Timezone is not found, throw a 404 exception */
            if (!$timezone) {
                throw new NotFoundHttpException('Timezone not found');
            }

            $entityManager->remove($timezone);
            $entityManager->flush();  /* Persist the change (deletion) */

            /* Return a success response */
            return new JsonResponse([
                'message' => 'Timezone deleted successfully',
                'status' => 200,
                'Timezone_id' => $id
            ]);
        } catch (\Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }
}
