<?php

namespace App\Controller\Api;

use App\Entity\Game;
use App\Exception\AppException;
use App\Service\GameService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\SerializerInterface;

class GameController extends AbstractController
{
    #[Route('/api/v1/game', name: 'api_game_list')]
    public function index(GameService $gameService, SerializerInterface $serializer): Response
    {
        try {
            $context = (new ObjectNormalizerContextBuilder())
                ->withGroups('list_games')
                ->toArray();

            return new JsonResponse(
                $serializer->serialize($gameService->list(), 'json', $context ),
                Response::HTTP_OK,
                ['Content-Type' => 'application/json;charset=UTF-8'],
                true
            );

        } catch (AppException $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR,
                headers: ['Content-Type' => 'application/json;charset=UTF-8']
            );
        }
    }

    #[Route('/api/v1/game/add', name: 'api_game_add')]
    public function add(GameService $genreService, SerializerInterface $serializer, Request $request): Response
    {
        try {
            $game = $serializer->deserialize($request->getContent(), Game::class, 'json');

            $context = (new ObjectNormalizerContextBuilder())
                ->withGroups('show_game')
                ->toArray();
            return new JsonResponse(
                $serializer->serialize($genreService->add($game), 'json', $context ),
                Response::HTTP_OK,
                ['Content-Type' => 'application/json;charset=UTF-8'],
                true
            );

        } catch (AppException $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR,
                headers: ['Content-Type' => 'application/json;charset=UTF-8']
            );
        }
    }

    #[Route('/api/v1/game/{id}/edit', name: 'api_game_edit')]
    public function edit(GameService $gameService, SerializerInterface $serializer, Request $request, int $id): Response
    {
        try {
            $game = $serializer->deserialize($request->getContent(), Game::class, 'json');

            $context = (new ObjectNormalizerContextBuilder())
                ->withGroups('show_game')
                ->toArray();
            return new JsonResponse(
                $serializer->serialize($gameService->edit($id, $game), 'json', $context ),
                Response::HTTP_OK,
                ['Content-Type' => 'application/json;charset=UTF-8'],
                true
            );
        } catch (AppException $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR,
                headers: ['Content-Type' => 'application/json;charset=UTF-8']
            );
        }
    }

    #[Route('/api/v1/game/{id}/delete', name: 'api_game_delete')]
    public function delete(GameService $gameService, int $id): Response
    {
        try {
            $gameService->delete($id);
            return $this->json(
                [],
                Response::HTTP_OK,
                headers: ['Content-Type' => 'application/json;charset=UTF-8']
            );
        } catch (AppException $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR,
                headers: ['Content-Type' => 'application/json;charset=UTF-8']
            );
        }
    }
}
