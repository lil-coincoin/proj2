<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Question;
use App\Form\AnswerEditType;
use App\Form\QuestionEditType;
use App\Repository\QuestionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Turbo\TurboStreamResponse;

class QuestionController extends AbstractController
{
    #[Route('/', name: 'app_question')]
    public function index(QuestionRepository $questionRepository): Response
    {
        $questions = $questionRepository->getPaginatedQuestions(1, 10);
        // $popularTags = $tagRepository->getPopularTags(10);

        return $this->render('question/index.html.twig', [
            'questions' => $questions,
            // 'popularTags' => $popularTags,
            // 'selectedTag' => null,
        ]);
    }

    #[Route('/questions/ajax/{page}', name: 'app_ajax_question')]
    public function loadMoreQuestions(int $page, QuestionRepository $questionRepository): Response
    {
        $questions = $questionRepository->getPaginatedQuestions($page, 10);

        return $this->render('question/_questions_append.html.twig', [
            'questions' => $questions,
        ]);
    }

    #[Route('/question/{id}', name: 'question_show')]
    public function show(Question $question, Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        if (!$question) {
            throw $this->createNotFoundException('Question non trouvée');
        }

        // Récupération des réponses associées à la question
        $answers = $question->getAnswers();

        $user = $userRepository->find(1);
        $newAnswer = new Answer();
        $newAnswer->setQuestion($question);
        $newAnswer->setUser($user);
        $newAnswer->setCreatedAt(new \DateTime());
        $newAnswer->setUpdatedAt(new \DateTime());

        $form = $this->createForm(AnswerEditType::class, $newAnswer);
        $cloneForm = clone $form;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($newAnswer);
            $entityManager->flush();
            // Renvoie la vue actualisée de l'article si la modification est réussie
            return $this->render('question/_add_answer.html.twig', [
                'answer' => $newAnswer,
                'formNewAnswer' => $cloneForm,
            ], new TurboStreamResponse());
        }

        return $this->render('question/detail_question.html.twig', [
            'question' => $question,
            'answers' => $answers,
            'formNewAnswer' => $form,
        ]);
    }

    #[Route('/question/{id}/edit', name: 'question_edit')]
    public function edit(Request $request, Question $question, EntityManagerInterface $em): Response
    {
        if (
            !$request->headers->has('Turbo-Frame') &&
            !$request->headers->has('Turbo-Visit')
        ) {
            throw $this->createAccessDeniedException('Access restricted to Turbo requests only');
        }

        $form = $this->createForm(QuestionEditType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($question);
            $em->flush();
            // Renvoie la vue actualisée de l'article si la modification est réussie
            return $this->render('question/_question.html.twig', [
                'question' => $question,
            ]);
        }

        // Si le formulaire est affiché ou en cas d'erreur, on renvoie la vue du formulaire
        return $this->render('question/_edit_question.html.twig', [
            'form' => $form->createView(),
            'question' => $question,
        ]);
    }

}
