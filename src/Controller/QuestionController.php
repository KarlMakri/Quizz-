<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Question;
use App\Entity\Answer;
use App\Entity\Category;
use App\Form\QuestionType;
use App\Form\AnswerType;

class QuestionController extends AbstractController
{
    /**
     * @Route("/question", name="question")
     */
    public function index(Request $request)
    {
      // récupération paramètres URL
      $category = $request->query->get('category');
      $difficult = $request->query->get('difficulty');

      $questions = $this->getDoctrine()
        ->getRepository(Question::class)
        //->findAll()
        ->findByFilters($category, $difficult)
        ;

      // filtres de recherche
      $categories = $this->getDoctrine()
        ->getRepository(Category::class)
        ->findAll();

      $difficulty = array(
        'Facile' => 1,
        'Intermédiaire' => 2,
        'Difficile' => 3
      );




      return $this->render('question/index.html.twig', [
          'questions' => $questions,
          'categories' => $categories,
          'difficulty' => $difficulty
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
