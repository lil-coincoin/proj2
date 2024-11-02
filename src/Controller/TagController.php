<?php

namespace App\Controller;

use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TagController extends AbstractController
{
    #[Route('/tags/suggestions', name: 'tags_suggestions', methods: ['GET'])]
    public function tagSuggestions(EntityManagerInterface $em): JsonResponse
    {
        $tags = $em->getRepository(Tag::class)->findAll();
        $tagNames = array_map(fn($tag) => $tag->getName(), $tags);
    
        return new JsonResponse($tagNames);
    }
}
