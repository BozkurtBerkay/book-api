<?php

namespace App\Controller;

use App\Form\Type\User\UserRegisterType;
use App\Form\Type\User\UserUpdateType;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractApiController
{
    private $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @Route("/register", name="user_register", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function register(Request $request): Response
    {
        $body = json_decode($request->getContent(), true);
        $emptyUser = $this->userIsEmpty($body);
        if (empty(!$emptyUser)) {
            return $this->respond(["message" => "Email or Username is available"]);
        }

        $form = $this->buildForm(UserRegisterType::class);

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }
        $user = $this->userManager->createUser();
        $user->setEmail($body["email"]);
        $user->setEmailCanonical($body["email"]);
        $user->setUsername($body["username"]);
        $user->setUsernameCanonical($body["username"]);
        $user->setPlainPassword($body["password"]);
        $user->setName($body["name"]);
        empty(!$body["surname"]) ? $user->setSurname($body["surname"]): $user->setSurname(null);
        $user->setEnabled(true);

        $this->userManager->updateUser($user);

        return $this->respond($user);
    }

    /**
     * @Route("/api/users", name="user_update", methods={"PUT"})
     * @param Request $request
     * @return Response
     */
    public function update(Request $request): Response
    {
        $user = $this->getUser();
        if (empty($user)) {
            return $this->respond(['message' => "User Not Found"]);
        }

        $updateUser = $this->userManager->findUserByUsername($user->getUsername());
        $form = $this->buildForm(UserUpdateType::class, $updateUser,[
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        $body = json_decode($request->getContent(), true);

        $updateUser->setName($body["name"]);
        $updateUser->setSurname($body["surname"]);

        $this->userManager->updateUser($updateUser);
        return $this->respond($user);
    }

    private function userIsEmpty($data): bool
    {
        if ($this->userManager->findUserByUsername($data["username"]) || $this->userManager->findUserByEmail($data["email"])) {
            return true;
        }
        return false;
    }
}