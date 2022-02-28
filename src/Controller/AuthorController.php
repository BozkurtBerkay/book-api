<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Author;
use App\Form\Type\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Nelmio\ApiDocBundle\Annotation\Security as SC;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @Route("/api/author", name="author_")
 * @Security("has_role('ROLE_ADMIN')")
 * @SC(name="Bearer")
 * @OA\Response (
 *     response="401",
 *     description="Unauthorized"
 * )
 * @OA\Response (
 *     response="400",
 *     description="Bad Request"
 * )
 */
class AuthorController extends AbstractApiController
{
    /**
     * @Route("/", name="list", methods={"GET"})
     * @OA\Response(
     *     response="200",
     *     description="Successfull",
     *     @OA\JsonContent(
     *         type="array",
     *         @OA\Items(
     *         ref=@Model(type="Author:class")
     *   )
     *  )
     * )
     * @param AuthorRepository $repository
     * @return Response
     */
    public function list(AuthorRepository $repository): Response
    {
        $authors = $repository->findAll();

        return $this->respond($authors);
    }

    /**
     * @Route("/", name="create", methods={"POST"})
     * @OA\RequestBody (
     *     description="Input data format",
     *     @OA\MediaType(
     *     mediaType="application/x-www-form-urlencoded",
     *         @OA\Schema(
     *             type="object",
     *             @OA\Property(
     *                 property="name",
     *                 description="Name of the author",
     *                 type="string",
     *             ),
     *             @OA\Property(
     *                 property="surname",
     *                 description="Surname of the author",
     *                 type="string",
     *             ),
     *             @OA\Property(
     *                 property="country",
     *                 description="Country of the author",
     *                 type="string",
     *             )
     *        )
     *     )
     * )
     * @OA\Response (
     *     response="201",
     *     description="Success"
     * )
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->buildForm(AuthorType::class);

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        /**
         * @var Author $author
         */
        $author = $form->getData();

        $entityManager->persist($author);
        $entityManager->flush();

        return $this->respond($author, Response::HTTP_CREATED);
    }

    /**
     * @Route("/{id}", name="read", methods={"GET"})
     * @OA\Parameter (
     *     name="id",
     *     in="path",
     *     description="ID of Author to return"
     * )
     * @param int $id
     * @param AuthorRepository $repository
     * @return Response
     */
    public function show(int $id, AuthorRepository $repository): Response
    {
        $author = $repository->find($id);
        if (empty($author)) {
            return $this->respond(["message" => "Author Not Found"]);
        }

        return $this->respond($author);
    }

    /**
     * @Route("/{id}", name="update", methods={"PUT"})
     * @OA\Parameter (
     *     name="id",
     *     in="path",
     *     description="ID of Author to update"
     * )
     * @OA\RequestBody (
     *     description="Input data format",
     *     @OA\MediaType(
     *     mediaType="application/x-www-form-urlencoded",
     *         @OA\Schema(
     *             type="object",
     *             @OA\Property(
     *                 property="name",
     *                 description="Updated name of the author",
     *                 type="string",
     *             ),
     *             @OA\Property(
     *                 property="surname",
     *                 description="Updated surname of the author",
     *                 type="string",
     *             ),
     *             @OA\Property(
     *                 property="country",
     *                 description="Updated country of the author",
     *                 type="string",
     *             )
     *        )
     *     )
     * )
     * @OA\Response (
     *     response="201",
     *     description="Success"
     * )
     * @param Request $request
     * @param int $id
     * @param AuthorRepository $repository
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function edit(Request $request, int $id, AuthorRepository $repository, EntityManagerInterface $entityManager): Response
    {
        $author = $repository->find($id);
        if (empty($author)) {
            return $this->respond(["message" => "Author Not Found"]);
        }

        $form = $this->buildForm(AuthorType::class, $author, [
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        /** @var Author $author */
        $author = $form->getData();

        $entityManager->persist($author);
        $entityManager->flush();

        return $this->respond($author, Response::HTTP_CREATED);

    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param int $id
     * @param AuthorRepository $repository
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(int $id, AuthorRepository $repository, EntityManagerInterface $entityManager): Response
    {
        $author = $repository->find($id);

        if (empty($author)) {
            return $this->respond(["message" => "Author Not Found"]);
        }

        $entityManager->remove($author);
        $entityManager->flush();

        return $this->respond(["message" => "Author delete successfully", "author" => $author]);
    }
}