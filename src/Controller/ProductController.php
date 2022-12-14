<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Repository\PhoneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use OpenApi\Attributes as OA;

class ProductController extends AbstractController
{


     /**
     * Get all products.
     */
    #[Route('/api/products', name: 'product.list', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the lists of products',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Phone::class, groups: ['getPhones']))
        )
    )]
    #[OA\Parameter(
        name: 'page',
        in: 'query',
        description: 'Page wanted',
        schema: new OA\Schema(type: 'int')
    )]
    #[OA\Parameter(
        name: 'limit',
        in: 'query',
        description: 'Nomber élements wanted',
        schema: new OA\Schema(type: 'int')
    )]
    #[OA\Tag(name: 'Products')]
    public function getAllProducts(PhoneRepository $phoneRepository, SerializerInterface $serializer, Request $request, TagAwareCacheInterface $cache): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);
        $idCache = "getPhones-" . $page . "-" . $limit;
        $jsonPhoneList = $cache->get($idCache, function (ItemInterface $item) use ($phoneRepository, $page, $limit, $serializer) {
            $item->tag("getPhones");
            $phoneList = $phoneRepository->findAllWithPagination($page, $limit);
            $context = SerializationContext::create()->setGroups(["getPhones"]);
            return $serializer->serialize($phoneList, 'json', $context);
        });
        return new JsonResponse($jsonPhoneList, Response::HTTP_OK, [], true);
    }

     /**
     * Get detail of products.
     */
    #[Route('/api/products/{id}', name: 'product.details', methods: ['GET'])]
    public function getDetailBook(Phone $phone, SerializerInterface $serializer): JsonResponse
    {
        $context = SerializationContext::create()->setGroups(["getPhones"]);
        $jsonPhone = $serializer->serialize($phone, 'json', $context);
        return new JsonResponse($jsonPhone, Response::HTTP_OK, [], true);
    }
}
