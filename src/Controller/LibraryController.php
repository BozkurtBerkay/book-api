<?php


namespace App\Controller;


use App\Entity\Library;
use App\Form\Type\Library\LibraryType;
use App\Form\Type\Library\LibraryUpdateType;
use App\Repository\LibraryRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LibraryController extends AbstractApiController
{
    /**
     * @Route("/api/users/library", name="library_show", methods={"GET"})
     * @param Request $request
     * @param LibraryRepository $repository
     * @param UserManagerInterface $userManager
     * @return Response
     */
    public function show(Request $request, LibraryRepository $repository, UserManagerInterface $userManager): Response
    {
        $user = $userManager->findUserByUsername($this->getUser()->getUsername());

        $library = $repository->findBy([
            'reader' => $user->getId()
        ]);

        if (empty($library)) {
            return $this->respond(["message" => "Library is empty"]);
        }
        return $this->respond($library);
    }

    /**
     * @Route("/api/users/library", name="library_add", methods={"POST"})
     * @param Request $request
     * @param UserManagerInterface $userManager
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function new(Request $request, UserManagerInterface $userManager, EntityManagerInterface $entityManager): Response
    {
        $user = $userManager->findUserByUsername($this->getUser()->getUsername());

        $form = $this->buildForm(LibraryType::class);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        /** @var Library $library */
        $library = $form->getData();
        $library->setReader($user);

        $entityManager->persist($library);
        $entityManager->flush();

        return $this->respond($library);
    }

    /**
     * @Route("/api/users/library/{id}", name="library_update", methods={"PATCH"})
     * @param int $id
     * @param Request $request
     * @param LibraryRepository $repository
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function update(int $id, Request $request, LibraryRepository $repository, EntityManagerInterface $entityManager): Response
    {
        $library = $repository->find($id);

        if (empty($library)) {
            return $this->respond(["message" => "Library not found"]);
        }

        $form = $this->buildForm(LibraryUpdateType::class, $library, [
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        /** @var Library $library */
        $library = $form->getData();

        $entityManager->persist($library);
        $entityManager->flush();

        return $this->respond($library, Response::HTTP_CREATED);

    }

    /**
     * @Route("/api/users/library/{id}", name="library_delete", methods={"DELETE"})
     * @param int $id
     * @param LibraryRepository $repository
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(int $id, LibraryRepository $repository, EntityManagerInterface $entityManager): Response
    {
        $library = $repository->find($id);

        if (empty($library)) {
            return $this->respond(["message" => "Library not found"]);
        }

        $entityManager->remove($library);
        $entityManager->flush();

        return $this->respond(["message" => "Library delete successfully", "library" => $library]);
    }
}