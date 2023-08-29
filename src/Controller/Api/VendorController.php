<?php

namespace App\Controller\Api;

use App\Entity\Vendor;
use App\Exception\AppException;
use App\Service\VendorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\SerializerInterface;

class VendorController extends AbstractController
{
    #[Route('/api/v1/vendor', name: 'api_vendor_list')]
    public function index(VendorService $vendorService, SerializerInterface $serializer): Response
    {
        try {
            $context = (new ObjectNormalizerContextBuilder())
                ->withGroups('list_vendors')
                ->toArray();

            return new JsonResponse(
                $serializer->serialize($vendorService->list(), 'json', $context ),
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

    #[Route('/api/v1/vendor/add', name: 'api_vendor_add')]
    public function add(VendorService $vendorService, SerializerInterface $serializer, Request $request): Response
    {
        try {
            $vendor = $serializer->deserialize($request->getContent(), Vendor::class, 'json');

            $context = (new ObjectNormalizerContextBuilder())
                ->withGroups('show_vendor')
                ->toArray();
            return new JsonResponse(
                $serializer->serialize($vendorService->add($vendor), 'json', $context),
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

    #[Route('/api/v1/vendor/{id}/edit', name: 'api_vendor_edit')]
    public function edit(VendorService $vendorService, SerializerInterface $serializer, Request $request, int $id): Response
    {
        try {
            $genre = $serializer->deserialize($request->getContent(), Vendor::class, 'json');

            $context = (new ObjectNormalizerContextBuilder())
                ->withGroups('show_vendor')
                ->toArray();
            return new JsonResponse(
                $serializer->serialize($vendorService->edit($id, $genre), 'json', $context),
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

    #[Route('/api/v1/vendor/{id}/delete', name: 'api_vendor_delete')]
    public function delete(VendorService $vendorService, int $id): Response
    {
        try {
            $vendorService->delete($id);
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
