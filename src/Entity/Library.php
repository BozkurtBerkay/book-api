<?php

namespace App\Entity;

use App\Repository\LibraryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=LibraryRepository::class)
 */
class Library
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var User
     * @Groups("reader_id")
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $reader;

    /**
     * @var Book
     * @Groups("book_id")
     * @ORM\ManyToOne(targetEntity=Book::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $book;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $readable;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReader(): ?User
    {
        return $this->reader;
    }

    public function setReader(?User $reader): self
    {
        $this->reader = $reader;

        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): self
    {
        $this->book = $book;

        return $this;
    }

    public function getReadable(): ?bool
    {
        return $this->readable;
    }

    public function setReadable(?bool $readable): self
    {
        $this->readable = $readable;

        return $this;
    }
}
