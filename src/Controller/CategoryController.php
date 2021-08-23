<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\FilterPostType;
use App\Form\SearchPostType;
use App\Service\CallApiService;
use App\Repository\PostRepository;
use App\Repository\CategoryRepository;
use App\Repository\YoutuberRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    private $numberItemsPerPage = 6;

    #[Route('/categories', name: 'categories', methods: "GET" )]
    public function browse(
                            CallApiService $callApiService, 
                            Request $request, 
                            PostRepository $postRepository, 
                            PaginatorInterface $paginator
                            ): Response
    {

        $boolSortByDate = false;

        $boolFormIsSubmit = false;

        $categories = $callApiService->getCategories()['hydra:member'];

        $searchForm = $this->createForm(SearchPostType::class);
        $requestSearchForm = $searchForm->handleRequest($request);
 
        if($searchForm->isSubmitted() && $searchForm->isValid()){

            $boolFormIsSubmit = true;

            $postSearch = $postRepository->search($requestSearchForm->get('words')->getData());

             $postPaginated = $paginator->paginate(
                $postSearch,
                $request->query->getInt('page',1),
                $this->numberItemsPerPage
            );

            return $this->render('category_post/category_post_search.html.twig', [
                'categories' => $categories,
                'postSearch' => $postPaginated,
                'form' => $searchForm->createView(),
                'boolSortByDate' => $boolSortByDate,
                'boolFormIsSubmit' => $boolFormIsSubmit,
            ]);

        }

        return $this->render('category_post/category_post_search.html.twig', [
            'categories' => $categories,
            'form' => $searchForm->createView(),
            'boolSortByDate' => $boolSortByDate,
            'boolFormIsSubmit' => $boolFormIsSubmit
        ]);
    }

    #[Route('/category/{id<\d+>}/posts', name: 'category_post', methods: "GET")]
    public function readCategoryPosts(
                                      CallApiService $callApiService,
                                      int $id,
                                      Request $request,
                                      CategoryRepository $categoryRepository,
                                      PaginatorInterface $paginator
                                      ): Response
    {


        $boolSortByDate = true;

        $categoryPosts = $callApiService->getCategory($id);
        $categories = $callApiService->getCategories()['hydra:member'];

        $searchForm = $this->createForm(SearchPostType::class, null, ['action' => $this->generateUrl('categories')]);

        $filterForm = $this->createForm(FilterPostType::class);
        $requestFilterForm = $filterForm->handleRequest($request);

        if($filterForm->isSubmitted() && $filterForm->isValid()){

        $optionValue = $requestFilterForm->get('date')->getData();

            if ("DESC" === $optionValue | "ASC" === $optionValue) {
                $filterPosts = $categoryRepository->filterByPosts($optionValue, $id);
            }

            //dd($mostRecentPosts[0]->getPosts());
            $categoryPosts['posts'] = $filterPosts;

        }

            //dd($request->query->getInt('page'));
            $postPaginated = $paginator->paginate(
                $categoryPosts['posts'],
                $request->query->getInt('page',1),
                $this->numberItemsPerPage
            );

            $categoryPosts['posts'] = $postPaginated;


        //dd($request->query->getInt('page'));

     return $this->render('category_post/category_post.html.twig', [
            'categoryPosts' => $categoryPosts,
            'categories' => $categories,
            'form' => $searchForm->createView(),
            'filter' => $filterForm->createView(),
            'boolSortByDate' => $boolSortByDate
     ]);
    }


}
