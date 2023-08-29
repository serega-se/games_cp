<?php

namespace App\Service;

use App\Entity\Game;
use App\Entity\Genre;
use App\Entity\Vendor;
use App\Exception\AppException;
use Doctrine\ORM\EntityManager;
use Monolog\Logger;

class GameService
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
            return $this->em->getRepository(Game::class)->findAll();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new AppException("App error");
        }
    }

    /**
     * @throws AppException
     */
    public function add(Game $entity): Game
    {
        $this->logger->info(sprintf("Creating Game name: %s", $entity->getName()));
        try {
            $game = $this->em->getRepository(Game::class)->findOneBy([
                "name" => $entity->getName()
            ]);
            if(!$game){
                $game = new Game();
                $game->setName($entity->getName());

                if($entity->getVendor() instanceof Vendor){
                    $vendor = $this->em->getRepository(Vendor::class)->findOneBy([
                        "name" => $entity->getVendor()->getName()
                    ]);

                    if(!$vendor){
                        $vendor = new Vendor();
                        $vendor->setName($entity->getVendor()->getName());
                        $this->em->persist($vendor);
                    }

                    $game->setVendor($vendor);
                }

                foreach ($entity->getGenres() as $entGenre){
                    $genre = $this->em->getRepository(Genre::class)->findOneBy([
                        "name" => $entGenre->getName()
                    ]);
                    if(!$genre){
                        $genre = new Genre();
                        $genre->setName($entGenre->getName());
                        $this->em->persist($genre);
                    }
                    $game->addGenre($genre);
                }

                $this->em->persist($game);
                $this->em->flush();

                return $game;
            }else{
                $this->logger->info(sprintf("Game name: %s already exists", $entity->getName()));
                throw new AppException("App err");
            }

        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new AppException("App error");
        }
    }

    /**
     * @throws AppException
     */
    public function edit(int $id, Game $entity): Game
    {
        try {
            $this->logger->info(sprintf("Edit game %d", $id));

            $game = $this->em->getRepository(Game::class)->find($id);

            if(!$game){
                $this->logger->error(sprintf("Game with id %d is not found", $id));
                throw new \Exception("App error");
            }

            if($game->getVendor() instanceof Vendor){
                $vendor = $this->em->getRepository(Vendor::class)->findOneBy([
                    "name" => $entity->getVendor()->getName()
                ]);

                if(!$vendor){
                    $vendor = new Vendor();
                    $vendor->setName($entity->getVendor()->getName());
                    $this->em->persist($vendor);
                }

                $game->setVendor($vendor);
            }

            foreach ($entity->getGenres() as $entGenre){
                $genre = $this->em->getRepository(Genre::class)->findOneBy([
                    "name" => $entGenre->getName()
                ]);
                if(!$genre){
                    $genre = new Genre();
                    $genre->setName($entGenre->getName());
                    $this->em->persist($genre);
                }
                $game->addGenre($genre);
            }

            $game->setName($entity->getName());
            $this->em->flush();

            return $game;
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
            $entity = $this->em->getRepository(Game::class)->find($id);

            if(!$entity){
                $this->logger->error(sprintf("Game with id %d is not found", $id));
                throw new \Exception("App error");
            }

            $this->logger->info(sprintf("Deleting Game id %d, name: %s", $id, $entity->getName()));

            $this->em->remove($entity);
            $this->em->flush();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new AppException("App error");
        }
    }
}