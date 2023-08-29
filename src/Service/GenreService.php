<?php

namespace App\Service;

use App\Entity\Genre;
use App\Exception\AppException;
use Doctrine\ORM\EntityManager;
use Monolog\Logger;

class GenreService
{
    private EntityManager $em;
    private Logger $logger;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em, Logger $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
    }

    /**
     * @throws AppException
     */
    public function list(): array
    {
        try {
            return $this->em->getRepository(Genre::class)->findAll();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new AppException("App error");
        }
    }

    /**
     * @throws AppException
     */
    public function add(Genre $entity): Genre
    {
        try {
            $this->logger->info(sprintf("Creating genre name: %s", $entity->getName()));

            $this->em->persist($entity);
            $this->em->flush();

            return $entity;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new AppException("App error");
        }
    }

    /**
     * @throws AppException
     */
    public function edit(int $id, Genre $genre): Genre
    {
        try {
            $entity = $this->em->getRepository(Genre::class)->find($id);

            if(!$entity){
                $this->logger->error(sprintf("Genre with id %d is not found", $id));
                throw new \Exception("App error");
            }

            $this->logger->info(sprintf("Rename genre id %d, name: %s, new name %s", $id, $entity->getName(), $entity->getName()));

            $entity->setName($genre->getName());
            $this->em->flush();

            return $entity;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new AppException("App error");
        }
    }

    /**
     * @throws AppException
     */
    public function delete(int $id): void
    {
        try {
            $entity = $this->em->getRepository(Genre::class)->find($id);

            if(!$entity){
                $this->logger->error(sprintf("Genre with id %d is not found", $id));
                throw new \Exception("App error");
            }

            $this->logger->info(sprintf("Deleting genre id %d, name: %s", $id, $entity->getName()));

            $this->em->remove($entity);
            $this->em->flush();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new AppException("App error");
        }
    }
}