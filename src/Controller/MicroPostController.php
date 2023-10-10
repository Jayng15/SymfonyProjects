<?php

namespace App\Controller;

use App\Entity\Comment;
use DateTime;
use App\Entity\MicroPost;
use App\Form\CommentType;
use App\Form\MicroPostType;
use App\Repository\CommentRepository;
use App\Repository\MicroPostRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MicroPostController extends AbstractController
{
    #[Route('/micro-post', name: 'app_micro_post')]
    public function index(MicroPostRepository $p): Response
    {
        return $this->render('micro_post/index.html.twig', [
            'posts' => $p->findAll(),
        ]);
    }


    #[Route('/micro-post/{post}', name: 'app_micro_post_show')]
    public function showOne(MicroPost $post, CommentRepository $comments): Response
    {
        return $this->render('/micro_post/show.html.twig', [
            'post' => $post,
        ]);
    }


    #[Route('/micro-post/{id}/edit', name: 'app_micro_post_edit')]
    public function edit(MicroPost $id, Request $request, MicroPostRepository $p): Response
    {
        $form = $this->createForm(MicroPostType::class, $id);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $p->add($post, true);


            // Add a flush message
            $this->addFlash("success", "your post has been updated");
            // Redirect to different page
            return $this->redirectToRoute('app_micro_post');
        }
        return $this->render(
            'micro_post/edit.html.twig',
            [
                'form' => $form
            ]
        );
    }
    #[Route('/micro-post/add', name: 'app_micro_post_add', priority: '2')]
    public function add(Request $request, MicroPostRepository $p): Response
    {
        $form = $this->createForm(MicroPostType::class, new MicroPost());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $post->setCreated(new DateTime());
            $p->add($post, true);

            // Add a flush message
            $this->addFlash("success", "your post has been upload successfully");

            // Redirect to different page
            return $this->redirectToRoute('app_micro_post');
        }
        return $this->render(
            'micro_post/new.html.twig',
            [
                'form' => $form
            ]
        );
    }

    #[Route('/micro-post/{post}/comment', name: 'app_micro_post_comment')]
    public function addComment(MicroPost $post, Request $request, CommentRepository $comments): Response
    {
        $form = $this->createForm(CommentType::class, new Comment());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setPost($post);
            $comments->add($comment, true);

            //Add flash
            $this->addFlash('success', 'Your comment has been uploaded');

            return $this->redirectToRoute(
                'app_micro_post_show',
                ['post' => $post->getId()]
            );
        }

        return $this->render(
            'micro_post/comment.html.twig',
            [
                'form' => $form,
                'post' => $post
            ]
        );
    }
}
