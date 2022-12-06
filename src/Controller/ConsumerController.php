<?php

namespace App\Controller;

use App\Entity\Consumer;
use App\Repository\ConsumerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Response;

class ConsumerController extends AbstractController
{
    #[Route('/api/consumers', name: 'consumer.list' , methods: ['GET'])]
    public function getAllConsumers(ConsumerRepository $consumerRepository , SerializerInterface $serializer): JsonResponse
    {
        $consumerList = $consumerRepository->findAll();
        $context = SerializationContext::create()->setGroups(["getConsumers"]);
        $jsonConsumerList = $serializer->serialize($consumerList, 'json', $context);
        return new JsonResponse($jsonConsumerList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/consumers/{id}', name: 'consumer.details', methods: ['GET'])]
    public function getDetailConsumer(Consumer $consumer , SerializerInterface $serializer): JsonResponse {
        $context = SerializationContext::create()->setGroups(["getConsumers"]);
        $jsonConsumer = $serializer->serialize($consumer, 'json', $context);
        return new JsonResponse($jsonConsumer, Response::HTTP_OK, [], true);
    }

    #[Route('/api/consumers/{id}', name: 'consumer.delete', methods: ['DELETE'])]
    public function deleteConsumer(Consumer $consumer , EntityManagerInterface $entityManager): JsonResponse {
        $entityManager->remove($consumer);
        $entityManager->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
