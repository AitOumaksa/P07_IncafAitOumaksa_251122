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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class ConsumerController extends AbstractController
{

    #[Route('/api/consumers', name: 'consumer.list', methods: ['GET'])]
    public function getAllConsumers(ConsumerRepository $consumerRepository, SerializerInterface $serializer, Request $request, TagAwareCacheInterface $cache): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);
        $idCache = "getConsumers-". $this->getUser()->getId() . "-" . $page . "-" . $limit;
        $jsonConsumerList = $cache->get($idCache, function (ItemInterface $item) use ($consumerRepository, $page, $limit, $serializer) {
            $item->tag("consumersCache");
            $consumerList = $consumerRepository->findAllWithPagination($this->getUser() ,$page, $limit);
            $context = SerializationContext::create()->setGroups(["getConsumers"]);
            return $serializer->serialize($consumerList, 'json', $context);
        });
        return new JsonResponse($jsonConsumerList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/consumers/{id}', name: 'consumer.details', methods: ['GET'])]
    public function getDetailConsumer(Consumer $consumer, SerializerInterface $serializer): JsonResponse
    {
        $this->denyAccessUnlessGranted(SecurityVoter::MANAGE, $consumer);
        $context = SerializationContext::create()->setGroups(["getConsumers"]);
        $jsonConsumer = $serializer->serialize($consumer, 'json', $context);
        return new JsonResponse($jsonConsumer, Response::HTTP_OK, [], true);
    }

    #[Route('/api/consumers/{id}', name: 'consumer.delete', methods: ['DELETE'])]
    public function deleteConsumer(Consumer $consumer, EntityManagerInterface $entityManager, TagAwareCacheInterface $cache): JsonResponse
    {
        $this->denyAccessUnlessGranted(SecurityVoter::MANAGE, $consumer);
        $entityManager->remove($consumer);
        $entityManager->flush();
        $cache->invalidateTags(["consumersCache"]);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

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
