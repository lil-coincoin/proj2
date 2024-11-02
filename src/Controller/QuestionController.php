<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Tag;
use App\Form\AnswerEditType;
use App\Form\QuestionEditType;
use App\Form\QuestionType;
use App\Repository\QuestionRepository;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use DateTime;
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

    #[Route('/question/add', name: 'question_add')]
    public function add(Request $request, EntityManagerInterface $em, TagRepository $tagRepository, UserRepository $userRepository): Response
    {
        $user = $userRepository->find(1);
        $question = new Question();
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les tags depuis le champ non mappé et décoder le JSON
            $tagData = json_decode($form->get('tags')->getData(), true);

            if (is_array($tagData)) {
                foreach ($tagData as $tagItem) {
                    $name = $tagItem['value']; // Extraire la valeur du tag
                    
                    // Vérifier si le tag existe déjà
                    $tag = $em->getRepository(Tag::class)->findOneBy(['name' => $name]);
                    
                    if (!$tag) {
                        // Créer un nouveau tag si non existant
                        $tag = new Tag();
                        $tag->setName($name);
                        $em->persist($tag);
                    }
                    
                    $question->addTag($tag);
                }
            }
            $question->setCreatedAt(new \DateTime());
            $question->setUpdatedAt(new \DateTime());
            $question->setUser($user);

            $em->persist($question);
            $em->flush();

            return $this->redirectToRoute('question_show', ['id' => $question->getId()]);
        }

        return $this->render('question/new.html.twig', [
            'form' => $form,
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
            'form' => $form,
            'question' => $question,
        ]);
    }
}
