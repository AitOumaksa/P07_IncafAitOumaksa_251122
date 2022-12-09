<?php

namespace App\Controller;

use App\Entity\Consumer;
use App\Repository\ConsumerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ConsumerController extends AbstractController
{

    #[Route('/api/consumers', name: 'consumer.list', methods: ['GET'])]
    public function getAllConsumers(ConsumerRepository $consumerRepository, SerializerInterface $serializer): JsonResponse
    {
        $consumerList = $consumerRepository->findAll();
        $context = SerializationContext::create()->setGroups(["getConsumers"]);
        $jsonConsumerList = $serializer->serialize($consumerList, 'json', $context);
        return new JsonResponse($jsonConsumerList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/consumers/{id}', name: 'consumer.details', methods: ['GET'])]
    public function getDetailConsumer(Consumer $consumer, SerializerInterface $serializer): JsonResponse
    {
        $context = SerializationContext::create()->setGroups(["getConsumers"]);
        $jsonConsumer = $serializer->serialize($consumer, 'json', $context);
        return new JsonResponse($jsonConsumer, Response::HTTP_OK, [], true);
    }

    #[Route('/api/consumers/{id}', name: 'consumer.delete', methods: ['DELETE'])]
    public function deleteConsumer(Consumer $consumer, EntityManagerInterface $entityManager): JsonResponse
    {
        $this->denyAccessUnlessGranted('delete' , $consumer);
        $entityManager->remove($consumer);
        $entityManager->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/consumers', name: "consumer.create", methods: ['POST'])]
    public function createConsumer(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator
    ): JsonResponse 
    {
        $this->denyAccessUnlessGranted('ROLE_CLIENT');
        $consumer = $serializer->deserialize($request->getContent(), Consumer::class, 'json');
        $consumer->setClient($this->getUser());

        $errors = $validator->validate($consumer);

        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }


        $entityManager->persist($consumer);
        $entityManager->flush();

        $context = SerializationContext::create()->setGroups(["getConsumers"]);
        $jsonBook = $serializer->serialize($consumer, 'json', $context);

        $location = $urlGenerator->generate('consumer.details', ['id' => $consumer->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonBook, Response::HTTP_CREATED, ["Location" => $location], true);
    }
}
