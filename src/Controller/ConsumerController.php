<?php

namespace App\Controller;

use App\Entity\Consumer;
use App\Repository\ConsumerRepository;
use App\Security\Voter\SecurityVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;

class ConsumerController extends AbstractController
{
    /**
     * Get all consumers linked to a client.
     */

    #[Route('/api/consumers', name: 'consumer.list', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns the lists of consumers',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Consumer::class, groups: ['getConsumers']))
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
    #[OA\Tag(name: 'Consumers')]
    public function getAllConsumers(ConsumerRepository $consumerRepository, SerializerInterface $serializer, Request $request): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);
        $consumerList = $consumerRepository->findAllWithPagination($this->getUser(), $page, $limit);
        $context = SerializationContext::create()->setGroups(["getConsumers"]);
        $jsonConsumerList = $serializer->serialize($consumerList, 'json', $context);
        $response = new JsonResponse($jsonConsumerList, Response::HTTP_OK, [], true);
        $response->setPublic();
        $response->setMaxAge(3600);
        $response->headers->addCacheControlDirective('must-revalidate', true);
        return $response;
    }

    /**
     * Get detail for consumers linked to a client.
     */

    #[Route('/api/consumers/{id}', name: 'consumer.details', methods: ['GET'])]
    public function getDetailConsumer(Consumer $consumer, SerializerInterface $serializer): JsonResponse
    {
        $this->denyAccessUnlessGranted(SecurityVoter::MANAGE, $consumer);
        $context = SerializationContext::create()->setGroups(["getConsumers"]);
        $jsonConsumer = $serializer->serialize($consumer, 'json', $context);
        $response = new JsonResponse($jsonConsumer, Response::HTTP_OK, [], true);
        $response->setPublic();
        $response->setMaxAge(3600);
        $response->headers->addCacheControlDirective('must-revalidate', true);
        return $response;
    }

    /**
     * Delete consumers linked to a client.
     */
    #[Route('/api/consumers/{id}', name: 'consumer.delete', methods: ['DELETE'])]
    public function deleteConsumer(Consumer $consumer, EntityManagerInterface $entityManager): JsonResponse
    {
        $this->denyAccessUnlessGranted(SecurityVoter::MANAGE, $consumer);
        $entityManager->remove($consumer);
        $entityManager->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Create consumers linked to a client.
     */
    #[Route('/api/consumers', name: "consumer.create", methods: ['POST'])]
    public function createConsumer(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $urlGenerator,
        ValidatorInterface $validator
    ): JsonResponse {
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
