<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Comment;
use App\Entity\PostLike;
use App\Service\CallApiService;
use App\Repository\PostRepository;
use App\Repository\CommentRepository;
use App\Repository\PostLikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Stmt\TryCatch;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    #[Route('/article/{id<\d+>}/categories', name: 'article_category', methods: "GET")]
    public function readArticleCategory(CallApiService $callApiService, int $id, CommentRepository $commentRepository, Post $postObject): Response
    {

        $comments = $commentRepository->getCommentsSortByDate($id);

        $post = $callApiService->getPost($id);

        $categories = $callApiService->getCategories()['hydra:member'];

        return $this->render('article/article_category.html.twig', [
            'categories' => $categories,
            'post' => $post,
            'comments' => $comments,
            'postObject' => $postObject
        ]);
    }


    #[Route('/article/{id<\d+>}/tag', name: 'article_tag', methods: "GET")]
    public function readArticleTag(
        CallApiService $callApiService, 
        CommentRepository $commentRepository, 
        int $id, 
        Post $postObject): Response
    {

        $post = $callApiService->getPost($id);
        $tags = $callApiService->getTags()['hydra:member'];
        $comments = $commentRepository->getCommentsSortByDate($id);

        return $this->render('article/article_tag.html.twig', [
            'tags' => $tags,
            'post' => $post,
            'postObject' => $postObject,
            'comments' => $comments
     ]);
    }
    
    /**
     * Permet de liker ou unliker un article
     *
     * @param  Post $post
     * @param  EntityManagerInterface $entityManagerInterface
     * @param  PostLikeRepository $postLikeRepository
     * @return Response
     */
    #[Route('/article/{id<\d+>}/postlike', name: 'article_postlike', methods: ["POST"] )]
    public function postLike(
        Post $post,
        EntityManagerInterface $entityManagerInterface,
        PostLikeRepository $postLikeRepository,
        ): Response
    {

        $user = $this->getUser();

        if (!$user) {
            return $this->json(['message'=> 'Unauthorized'],401);
        }

        if ($post->isLikedByUser($user)) {

            $postLike = $postLikeRepository->findOneBy(['post'=> $post, 'user' => $user]);

            $entityManagerInterface->remove($postLike);
            $entityManagerInterface->flush();

            return $this->json([
                'message' => 'Like bien supprimé',
                'nbrLikes' => $postLikeRepository->count(['post'=> $post])],
                200);
        }

        $postLike = new PostLike();
        $postLike->setPost($post)
                 ->setUser($user);

        $entityManagerInterface->persist($postLike);
        $entityManagerInterface->flush();

        return $this->json([
            'message' => 'Like bien ajouté',
            'nbrLikes' => $postLikeRepository->count(['post'=> $post])],
            200);
    }

    /**
     * Permet de créer un commentaire
     *
     * @param  int $postId
     * @return Response
     */
    #[Route('/article/postcomment/{postId<\d+>}', name: 'article_postcomment', methods: ["POST"] )]
    public function postComment(
        PostRepository $postRepository, 
        int $postId, 
        Request $request, 
        EntityManagerInterface $entityManagerInterface,
        CsrfTokenManagerInterface $tokenManager
        ): Response
    {

        if ($request->isMethod('post')) {

            $data = json_decode($request->getContent(), true);

            $post = $postRepository->find($postId);

            $user = $this->getUser();

            if (!$user) {
                return $this->json(['message'=> 'Unauthorized'],401);
            }

            $contentComment = $data['contentComment'];

            $comment = new Comment();
            $comment->setContent($contentComment);
            $comment->setUser($user);
            $comment->setPost($post);
    
            $entityManagerInterface->persist($comment);
            $entityManagerInterface->flush();

            $token = $tokenManager->refreshToken('delete'.$comment->getId())->getValue();
    
            return $this->json([
                'message' => 'Le commentaire à bien été ajouté',
                'comment' => $comment,
                'pseudonym' => $user->getPseudonym(),
                'token' => $token,
                'postId' => $postId],
                200);
        }

    }


    /**
     * Permet d'éditer un commentaire
     *
     * @param  int $commentId
     * @return Response
     */
    #[Route('/article/{postId<\d+>}/editcomment/{commentId<\d+>}', name: 'article_editcomment', methods: "POST" )]
    public function editComment(
        int $commentId,
        CommentRepository $commentRepository,
        Request $request,
        EntityManagerInterface $entityManagerInterface
        ): Response
    {

        $user = $this->getUser();

        $comment = $commentRepository->find($commentId);

        if ($comment->isCommentedByUser($user)) {
            # code...
            if ($request->isMethod('post')) {
                $data = json_decode($request->getContent(), true);
    
                $contentComment = $data['contentComment'];

                $comment->setContent($contentComment);

                $entityManagerInterface->persist($comment);
                $entityManagerInterface->flush();

                return $this->json(
                    [
                'message' => 'Le commentaire a bien été supprimé',
                'comment' => $comment,
                'pseudonym' => $user->getPseudonym()],
                    200
                );
            }
        }


    }

    /**
     * Permet de supprimer un commentaire
     *
     * @param  int $commentId
     * @return Response
     */
    #[Route('/article/deletecomment/{commentId<\d+>}', name: 'article_deletecomment', methods: "POST" )]
    public function deleteComment(int $commentId, Request $request, CommentRepository $commentRepository): Response
    {

        if (null === $commentRepository->find($commentId)) {
            throw $this->createNotFoundException('Commentaire non trouvé.');
        }

        $comment = $commentRepository->find($commentId);

        $data = json_decode($request->getContent(), true);


        // 'delete-movie' is the same value used in the template to generate the token
        if (!$this->isCsrfTokenValid('delete'.$commentId, $data['token'])) {
            // On jette une 403
            throw $this->createAccessDeniedException('Are you token to me !??!??');
        }

        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $data['token'])) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();

            return $this->json([
                'message' => 'Le commentaire a bien été supprimé'],
                200);
            }


    }


}
