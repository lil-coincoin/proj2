<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Vote;
use App\Enum\VoteType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class VoteController extends AbstractController
{
    #[Route('/question/{id}/upvote', name: 'question_upvote')]
    public function upVoteQuestion(Question $question, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $user = $userRepository->find(1);

        $existingVote = $entityManager->getRepository(Vote::class)->findOneBy([
            'user' => $user,
            'question' => $question,
        ]);

        if($existingVote){
            return $this->json(['message' => 'You have already voted for this question'], 400);
        }

        $vote = new Vote();
        $vote->setUser($user);
        $vote->setQuestion($question);
        $vote->setType(VoteType::UPVOTE);
        $vote->setCreatedAt(new \DateTime());

        $entityManager->persist($vote);
        $entityManager->flush();

        return $this->render('question/_question.html.twig', [
            'question' => $question,
        ]);
    }

    #[Route('/question/{id}/downvote', name: 'question_downvote')]
    public function downVoteQuestion(Question $question, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $user = $userRepository->find(1);

        $existingVote = $entityManager->getRepository(Vote::class)->findOneBy([
            'user' => $user,
            'question' => $question,
        ]);

        if($existingVote){
            return $this->json(['message' => 'You have already voted for this question'], 400);
        }

        $vote = new Vote();
        $vote->setUser($user);
        $vote->setQuestion($question);
        $vote->setType(VoteType::DOWNVOTE);
        $vote->setCreatedAt(new \DateTime());

        $entityManager->persist($vote);
        $entityManager->flush();

        return $this->render('question/_question.html.twig', [
            'question' => $question,
        ]);
    }

    #[Route('/answer/{id}/upvote', name: 'answer_upvote')]
    public function upVoteAnswer(Answer $answer, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $user = $userRepository->find(1);

        $existingVote = $entityManager->getRepository(Vote::class)->findOneBy([
            'user' => $user,
            'answer' => $answer,
        ]);

        if($existingVote){
            return $this->json(['message' => 'You have already voted for this question'], 400);
        }

        $vote = new Vote();
        $vote->setUser($user);
        $vote->setAnswer($answer);
        $vote->setType(VoteType::UPVOTE);
        $vote->setCreatedAt(new \DateTime());

        $entityManager->persist($vote);
        $entityManager->flush();

        return $this->render('answer/_append_answer.html.twig', [
            'answer' => $answer,
        ]);
    }

    #[Route('/answer/{id}/downvote', name: 'answer_downvote')]
    public function downVoteAnswer(Answer $answer, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $user = $userRepository->find(1);

        $existingVote = $entityManager->getRepository(Vote::class)->findOneBy([
            'user' => $user,
            'answer' => $answer,
        ]);

        if($existingVote){
            return $this->json(['message' => 'You have already voted for this question'], 400);
        }

        $vote = new Vote();
        $vote->setUser($user);
        $vote->setAnswer($answer);
        $vote->setType(VoteType::DOWNVOTE);
        $vote->setCreatedAt(new \DateTime());

        $entityManager->persist($vote);
        $entityManager->flush();

        return $this->render('answer/_append_answer.html.twig', [
            'answer' => $answer,
        ]);
    }
}
