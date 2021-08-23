<?php

namespace App\Controller;

use App\Form\FilterPostType;
use App\Form\SearchPostType;
use App\Service\CallApiService;
use App\Repository\TagRepository;
use App\Repository\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TagController extends AbstractController
{
    private $numberItemsPerPage = 6;

    #[Route('/tag', name: 'tag', methods: "GET")]
    public function browse(
                            CallApiService $callApiService,
                            Request $request,
                            PostRepository $postRepository,
                            PaginatorInterface $paginator
                            ): Response
    {
        $boolSortByDate = false;

        $boolFormIsSubmit = false;

        $tags = $callApiService->getTags()['hydra:member'];

        $searchForm = $this->createForm(SearchPostType::class);
        $requestSearchForm = $searchForm ->handleRequest($request);

        if($searchForm ->isSubmitted() && $searchForm ->isValid()){

                $boolFormIsSubmit = true;

                $postSearch = $postRepository->search($requestSearchForm->get('words')->getData());
                
                $postPaginated = $paginator->paginate(
                                $postSearch,
                                $request->query->getInt('page',1),
                                $this->numberItemsPerPage
                                );

                $tagPost['posts'] = $postPaginated;

                return $this->render('tag/tag_search.html.twig', [
                    'tags' => $tags,
                    'tagPost' => $tagPost,
                    'form' => $searchForm->createView(),
                    'boolSortByDate' => $boolSortByDate,
                    'boolFormIsSubmit' => $boolFormIsSubmit
                ]);
        }

        return $this->render('tag/tag_search.html.twig', [
            'tags' => $tags,
            'form' => $searchForm->createView(),
            'boolSortByDate' => $boolSortByDate,
            'boolFormIsSubmit' => $boolFormIsSubmit
        ]);
    }

    #[Route('/tag/{id<\d+>}/post', name: 'tag_post', methods: "GET")]
    public function readTagPost(
                                CallApiService $callApiService, 
                                int $id,
                                Request $request,
                                TagRepository $tagRepository,
                                PaginatorInterface $paginator
                                ): Response
    {
        $boolSortByDate = true;

        $tagPost = $callApiService->getTag($id);
        $tags = $callApiService->getTags()['hydra:member'];

        $searchForm = $this->createForm(SearchPostType::class, null, ['action' => $this->generateUrl('tag')]);

        $filterForm = $this->createForm(FilterPostType::class);
        $requestFilterForm = $filterForm->handleRequest($request);

        if($filterForm->isSubmitted() && $filterForm->isValid()){

           $optionValue = $requestFilterForm->get('date')->getData();

            if ("DESC" === $optionValue | "ASC" === $optionValue) {
                $filterPosts = $tagRepository->filterByPosts($optionValue, $id);
            }

            $tagPost['posts'] = $filterPosts;
        }

            $postPaginated = $paginator->paginate(
                $tagPost['posts'],
                $request->query->getInt('page',1),
                $this->numberItemsPerPage
            );

            $tagPost['posts'] = $postPaginated;

        return $this->render('tag/tag.html.twig', [
            'tags' => $tags,
            'tagPost' => $tagPost,
            'form' => $searchForm->createView(),
            'filter' => $filterForm->createView(),
            'boolSortByDate' => $boolSortByDate
     ]);
    }
}
