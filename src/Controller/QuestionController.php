<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Question;
use App\Entity\Answer;
use App\Form\QuestionType;
use App\Form\AnswerType;

class QuestionController extends AbstractController
{
    /**
     * @Route("/question", name="question")
     */
    public function index()
    {
      $questions = $this->getDoctrine()
        ->getRepository(Question::class)
        ->findAll();

        return $this->render('question/index.html.twig', [
            'questions' => $questions,
        ]);
    }

    /**
     * @Route("/question/detail/{id}", name="question_detail")
     */
    public function detail($id, Request $request)
    {
      $question = $this->getDoctrine()
        ->getRepository(Question::class)
        ->find($id);

      $answer = new Answer();
      $answer->setQuestion($question);
      $form = $this->createForm(AnswerType::class, $answer);

      $form->handleRequest($request);
      if ($form->isSubmitted()) {
        $answer = $form->getData();
        $em = $this->getDoctrine()->getManager();
        $em->persist($answer);
        $em->flush();
      }

      return $this->render('question/detail.html.twig', [
          'question' => $question,
          'form' => $form->createView()
      ]);
    }

    /**
     * @Route("/question/add", name="question_add")
     */
    public function add(Request $request)
    {
      $question = new Question();
      $form = $this->createForm(QuestionType::class, $question);

      $form->handleRequest($request);
      if ($form->isSubmitted()) {
        $question = $form->getData();
        $em = $this->getDoctrine()->getManager();
        $em->persist($question);
        $em->flush();
      }

      return $this->render('form.html.twig', [
          'form' => $form->createView(),
      ]);
    }
}
