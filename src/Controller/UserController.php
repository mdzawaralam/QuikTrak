<?php

namespace App\Controller;

use App\Entity\User;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserController extends AbstractController
{
    /**
     * @Route("/api/createUser", methods={"POST"}, name="create_user")
     * @OA\Post(
     *     path="/api/createUser",
     *     summary="Create a new user",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Full Name of user"),
     *             @OA\Property(property="email", type="string", example="Email Id of User"),
     *             @OA\Property(property="phone", type="integer", example="Phone number of User"),
     *             @OA\Property(property="username", type="string", example="Username of User"),
     *             @OA\Property(property="password", type="string", example="Password of User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User Created successfully",
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
    public function createUser(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        /* User creation $request->getContent() having all data as user passing */
        try {
            $data = json_decode($request->getContent(), true);

            /* checking all input data empty or not as its coming from user. */
            if (
                empty($data['name']) || empty($data['email']) || empty($data['phone']) || empty($data['username']) ||
                empty($data['password'])
            ) {
                return $this->json(['message' => 'All fields are required'], 400);
            }

            $createdAt = new DateTime();

            $user = new User();
            $user->setName($data['name']);
            $user->setEmail($data['email']);
            $user->setPhone($data['phone']);
            $user->setCreatedAt($createdAt);
            $user->setUpdatedAt($createdAt);
            $user->setStatus(true);
            $user->setUsername($data['username']);
            $user->setPassword(password_hash($data['password'], PASSWORD_BCRYPT)); /* Hash password */
            $user->setToken(bin2hex(random_bytes(16))); /* This is dummy Generated token */

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->json(['message' => 'User created successfully', 'status' => 201], 201);
        } catch (\Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    /**
     * @Route("/api/userLogin", methods={"POST"}, name="user_login")
     * @OA\Post(
     *     path="/api/userLogin",
     *     summary="User Login",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="username", type="string", example="username"),
     *             @OA\Property(property="password", type="string", example="User password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User login successfully",
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
    public function userLogin(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;

        if (!$username || !$password) {
            return $this->json(['message' => 'Invalid input'], 400);
        }

        /* Find user by username */
        $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $username]);

        if (!$user) {
            return $this->json(['error' => 'Invalid credentials'], 401);
        }

        /* Verify password */
        if (!$passwordHasher->isPasswordValid($user, $password)) {
            return $this->json(['error' => 'Invalid credentials'], 401);
        }

        /* Return success response, here need to pass LexikJWTAuthenticationBundle token */
        return $this->json([
            'message' => 'Login successful',
            'status' => 200,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'username' => $user->getUsername(),
                'token' => bin2hex(random_bytes(16))
            ],
        ]);
    }
}
