<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comments;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\Texter;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo): Response
    {
        // $repo = $this->getDoctrine()->getRepository(Article::class);
        $artcle = $repo->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $artcle
        ]);
    }
    /**
     * @Route("/", name="home")
    */

    public function home () {
        return $this->render('blog/home.html.twig', [
            "title" => "Bienvenu les amies",
            "age" => 16
        ]);
    }

    /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit")
    */

    public function form(Article $article = null, Request $request, EntityManagerInterface $manager) {
        if($article == null){
            $article = new Article();
        }
        // $formbuilder = $this->createFormBuilder($article);
        // $formbuilder->add('title', TextType::class);
        // $formbuilder->add('content', TextareaType::class);
        // $formbuilder->add('image', TextType::class);
        $form = $this->createForm(ArticleType::class, $article);
        if (!$article->getId()) {
            $form->add('Submit', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success'
                ]
            ]);
        }else{
            $form->add('Update', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-info'
                ]
            ]);
        }
        // $form = $form->getForm();
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            if(!$article->getId()){
                $article->setCreatedAt(new \DateTime());
            }
            $manager->persist($article);
            $manager->flush();
            return $this->redirectToRoute('blog_show',['id' => $article->getId()]);
        }
        // if($request->request->count() > 0){

        //     $article = new Article();
        //     $article->setTitle($request->request->get('title'));
        //     $article->setContent($request->request->get('content'));
        //     $article->setImage($request->request->get('image'));
        //     $article->setCreatedAt(new \DateTimeImmutable());
        //     $manager->persist($article);
        //     $manager->flush();
        //     return $this->redirectToRoute('blog_show',['id' => $article->getId()]);
        // }
        return $this->render("blog/create-blog.thml.twig",[
            'formArticle' => $form->createView()
        ]);
    }
    
    /**
     * @Route("/blog/{id}", name="blog_show")
    */
    public function show (Article $article, Request $request, EntityManagerInterface $manager){
        $comment = new Comments();
        $form = $this->createForm(CommentType::class, $comment);
        $form->add("Submit",SubmitType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $comment->setCreatedAt(new \DateTime())
                    ->setArticle($article);
            $manager->persist($comment);
            $manager->flush();
            return $this->redirectToRoute("blog_show",["id" => $article->getId()]);
        }
        return $this->render("blog/show.html.twig",[
            'article' => $article,
            'commentForm' => $form->createView()
        ]);
    }
}
