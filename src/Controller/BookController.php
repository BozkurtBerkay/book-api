<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Book;
use App\Form\Type\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/book", name="book_")
 */
class BookController extends AbstractApiController
{
    /**
     * @Route("/", name="list", methods={"GET|HEAD"})
     * @param Request $request
     * @param BookRepository $repository
     * @return Response
     */
    public function list(Request $request, BookRepository $repository): Response
    {
        $fields = $request->query->all();
        if (array_key_exists("name", $fields) || array_key_exists("author", $fields)) {
            $searchBook = $repository->searchBookAndAuthor($fields["name"], $fields["author"]);
            return $this->respond($searchBook);
        }

        $books = $repository->findAll();

        return $this->respond($books);
    }

    /**
     * @Route("/", name="create", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->buildForm(BookType::class);

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        /**
         * @var Book $book
         */
        $book = $form->getData();

        $entityManager->persist($book);
        $entityManager->flush();

        return $this->respond($book, Response::HTTP_CREATED);
    }

    /**
     * @Route("/{id}", name="read", methods={"GET"})
     * @param int $id
     * @param BookRepository $repository
     * @return Response
     */
    public function show(int $id, BookRepository $repository): Response
    {
        $book = $repository->find($id);
        if (empty($book)) {
            return $this->respond(["message" => "Book Not Found"]);
        }

        return $this->respond($book);
    }

    /**
     * @Route("/{id}", name="update", methods={"PUT"})
     * @param Request $request
     * @param int $id
     * @param BookRepository $repository
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function edit(Request $request, int $id, BookRepository $repository, EntityManagerInterface $entityManager): Response
    {
        $book = $repository->find($id);
        if (empty($book)) {
            return $this->respond(["message" => "Book Not Found"]);
        }

        $form = $this->buildForm(BookType::class, $book, [
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        /** @var Book $book */
        $book = $form->getData();

        $entityManager->persist($book);
        $entityManager->flush();

        return $this->respond($book, Response::HTTP_CREATED);

    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param int $id
     * @param BookRepository $repository
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function delete(int $id, BookRepository $repository, EntityManagerInterface $entityManager): Response
    {
        $book = $repository->find($id);

        if (empty($book)) {
            return $this->respond(["message" => "Book Not Found"]);
        }

        $entityManager->remove($book);
        $entityManager->flush();

        return $this->respond(["message" => "Author delete successfully", "book" => $book]);
    }
}