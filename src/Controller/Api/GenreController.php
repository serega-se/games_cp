<?php

namespace App\Controller\Api;

use App\Entity\Genre;
use App\Exception\AppException;
use App\Service\GenreService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\SerializerInterface;

class GenreController extends AbstractController
{
    #[Route('/api/v1/genre', name: 'api_genre_list')]
    public function index(GenreService $genreService, SerializerInterface $serializer): Response
    {
        try {
            $context = (new ObjectNormalizerContextBuilder())
                ->withGroups('list_genre')
                ->toArray();

            return new JsonResponse(
                $serializer->serialize($genreService->list(), 'json', $context ),
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

    #[Route('/api/v1/genre/add', name: 'api_genre_add')]
    public function add(GenreService $genreService, SerializerInterface $serializer, Request $request): Response
    {
        try {
            $genre = $serializer->deserialize($request->getContent(), Genre::class, 'json');

            $context = (new ObjectNormalizerContextBuilder())
                ->withGroups('show_genre')
                ->toArray();
            return new JsonResponse(
                $serializer->serialize($genreService->add($genre), 'json', $context ),
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

    #[Route('/api/v1/genre/{id}/edit', name: 'api_genre_edit')]
    public function edit(GenreService $genreService, SerializerInterface $serializer, Request $request, int $id): Response
    {
        try {
            $genre = $serializer->deserialize($request->getContent(), Genre::class, 'json');

            $context = (new ObjectNormalizerContextBuilder())
                ->withGroups('show_genre')
                ->toArray();
            return new JsonResponse(
                $serializer->serialize($genreService->edit($id, $genre), 'json', $context ),
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

    #[Route('/api/v1/genre/{id}/delete', name: 'api_genre_delete')]
    public function delete(GenreService $genreService, int $id): Response
    {
        try {
            $genreService->delete($id);
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
