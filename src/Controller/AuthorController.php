<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Author;
use App\Form\Type\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/author", name="author_")
 */
class AuthorController extends AbstractApiController
{
    /**
     * @Route("/", name="list", methods={"GET|HEAD"})
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

        if(empty($author))
        {
            return $this->respond(["message" => "Author Not Found"]);
        }

        $entityManager->remove($author);
        $entityManager->flush();

        return $this->respond(["message" => "Author delete successfully", "author" => $author]);
    }
}