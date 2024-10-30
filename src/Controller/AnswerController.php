<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Question;
use App\Form\AnswerEditType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Turbo\TurboStreamResponse;

class AnswerController extends AbstractController
{
    #[Route('/answer/{id}/edit', name: 'answer_edit')]
    public function answerEdit(Request $request, Answer $answer, EntityManagerInterface $em): Response
    {
        // if (
        //     !$request->headers->has('Turbo-Frame') &&
        //     !$request->headers->has('Turbo-Visit')
        // ) {
        //     throw $this->createAccessDeniedException('Access restricted to Turbo requests only');
        // }

        $form = $this->createForm(AnswerEditType::class, $answer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($answer);
            $em->flush();
            // Renvoie la vue actualisée de l'article si la modification est réussie
            return $this->render('answer/_append_answer.html.twig', [
                'answer' => $answer,
            ]);
        }

        // Si le formulaire est affiché ou en cas d'erreur, on renvoie la vue du formulaire
        return $this->render('answer/_edit_answer.html.twig', [
            'form' => $form->createView(),
            'answer' => $answer,
        ]);
    }

    #[Route('/answer/{id}/delete', name: 'answer_delete')]
    public function answerDelete(Answer $answer, EntityManagerInterface $em): Response
    {
        // if (
        //     !$request->headers->has('Turbo-Frame') &&
        //     !$request->headers->has('Turbo-Visit')
        // ) {
        //     throw $this->createAccessDeniedException('Access restricted to Turbo requests only');
        // }
        $idAnswer = $answer->getId();
        $em->remove($answer);
        $em->flush();

        return $this->render('answer/_remove_answer.html.twig', [
            'answer' => $idAnswer,
        ], new TurboStreamResponse());
    }
}
