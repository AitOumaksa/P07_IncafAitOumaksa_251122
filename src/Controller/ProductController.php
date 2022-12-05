<?php

namespace App\Controller;

use App\Repository\PhoneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    #[Route('/api/products', name: 'product.list' , methods: ['GET'])]
    public function getAllProducts(PhoneRepository $phoneRepository , SerializerInterface $serializer): JsonResponse
    {
        $phoneList = $phoneRepository->findAll();
        $context = SerializationContext::create()->setGroups(["getPhones"]);
        $jsonPhoneList = $serializer->serialize($phoneList, 'json', $context);
        return new JsonResponse($jsonPhoneList, Response::HTTP_OK, [], true);
    }
}
