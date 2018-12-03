<?php

namespace App\Repository;

use App\Entity\Question;
use App\Entity\Answer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Question::class);
    }

    public function findByFilters($category, $difficulty)
    {
        $qb = $this->createQueryBuilder('q');

        if ($category) {
          $qb
            ->andWhere('q.category = :category')
            ->setParameter('category', $category);
        }

        if ($difficulty) {
          $qb
            ->andWhere('q.difficulty = :difficulty')
            ->setParameter('difficulty', $difficulty);
        }

        return $qb
          ->orderBy('q.id', 'DESC')
          ->getQuery()
          ->getResult()
        ;

    }

    public function findByFiltersAssoc($category, $difficulty)
    {
      $connection = $this->getEntityManager()
        ->getConnection();

      $sql =
      'SELECT answer.label AS answer_label,
        question.label AS question_label
        FROM answer
        JOIN question
          ON question.id = answer.question_id'
      ;

      $query = $connection->prepare($sql);
      $query->execute();
      return $query->fetchAll();
    }











    // public function findByJson()
    // {
    //   $em = $this->getEntityManager();
    //   $dql =
    //     'SELECT a.label
    //       FROM App\Entity\Answer a';
    //   $query = $em->createQuery($dql);
    //   return $query->execute();
    // }

}
