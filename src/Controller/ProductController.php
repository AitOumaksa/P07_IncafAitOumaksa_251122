<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Repository\PhoneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    #[Route('/api/products', name: 'product.list' , methods: ['GET'])]
    public function getAllProducts(PhoneRepository $phoneRepository , SerializerInterface $serializer , Request $request): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);
        $phoneList = $phoneRepository->findAllWithPagination($page, $limit);
        $context = SerializationContext::create()->setGroups(["getPhones"]);
        $jsonPhoneList = $serializer->serialize($phoneList, 'json', $context);
        return new JsonResponse($jsonPhoneList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/products/{id}', name: 'product.details', methods: ['GET'])]
    public function getDetailBook(Phone $phone, SerializerInterface $serializer): JsonResponse {
        $context = SerializationContext::create()->setGroups(["getPhones"]);
        $jsonPhone = $serializer->serialize($phone, 'json', $context);
        return new JsonResponse($jsonPhone, Response::HTTP_OK, [], true);
    }
}
