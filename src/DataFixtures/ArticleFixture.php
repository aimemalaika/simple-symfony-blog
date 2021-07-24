<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comments;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ArticleFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create("fr_FR");

        for ($i=0; $i < 3; $i++) { 
            $category = new Category();
            $category->setName($faker->sentence())
                     ->setDescription($faker->text(100));
            $manager->persist($category);

            for ($j=0; $j < mt_rand(20,30); $j++) { 
                $article = new Article();

                $article->setTitle($faker->sentence())
                        ->setContent($faker->text(500))
                        ->setImage($faker->imageUrl())
                        ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                        ->setCategory($category);
                $manager->persist($article);

                for ($k=0; $k < mt_rand(20,50); $k++) {
                    $comment = new Comments();
                    $now = new \DateTime();

                    $interval = $now->diff($article->getCreatedAt());
                    $days = $interval->days;

                    $minimun = '-'. $days .'days';
                    $comment->setAuthor($faker->name())
                            ->setContent($faker->text(200))
                            ->setCreatedAt($faker->dateTimeBetween($minimun))
                            ->setArticle($article);
                    $manager->persist($comment);
                }
            }
        }
        $manager->flush();
    }
}
