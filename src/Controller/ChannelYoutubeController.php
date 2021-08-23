<?php

namespace App\Controller;

use App\Form\FilterPostType;
use App\Form\SearchPostType;
use App\Service\CallApiService;
use App\Repository\CategoryRepository;
use App\Repository\YoutuberRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ChannelYoutubeController extends AbstractController
{

    private $numberItemsPerPage = 6;

    #[Route('/channel/youtube', name: 'channel_youtube', methods: "GET")]
    public function browse(
                            CallApiService $callApiService,
                            Request $request,
                            YoutuberRepository $youtuberRepository,
                            PaginatorInterface $paginator
                            ): Response
    {

        $boolFormIsSubmit = false;

        $boolSortByDate = false;

        $categories = $callApiService->getCategories()['hydra:member'];

        $searchForm = $this->createForm(SearchPostType::class);
        $requestSearchForm = $searchForm->handleRequest($request);

        if($searchForm->isSubmitted() && $searchForm->isValid()){

            $youtuberSearch = $youtuberRepository->search($requestSearchForm->get('words')->getData());

            $boolFormIsSubmit = true;

            //dd($request->query->getInt('page'));
            $youtuberPaginated = $paginator->paginate(
                $youtuberSearch,
                $request->query->getInt('page',1),
                $this->numberItemsPerPage
            );

            return $this->render('channel_youtube/channel_youtube_search.html.twig', [
               'categories' => $categories,
               'form' => $searchForm->createView(),
               'youtuberSearch' =>  $youtuberPaginated,
               'boolSortByDate' => $boolSortByDate,
               'boolFormIsSubmit' => $boolFormIsSubmit,
           ]);
        }

        return $this->render('channel_youtube/channel_youtube_search.html.twig', [
            'categories' => $categories,
            'form' => $searchForm->createView(),
            'boolSortByDate' => $boolSortByDate,
            'boolFormIsSubmit' => $boolFormIsSubmit
        ]);
    }

    #[Route('/category/{id<\d+>}/youtubechannels', name: 'category_channel_youtube' , methods: "GET")]
    public function readCategoryYoutubeChannels(
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

         $searchForm = $this->createForm(SearchPostType::class, null, ['action' => $this->generateUrl('channel_youtube')]);

         $filterForm = $this->createForm(FilterPostType::class);
         $requestFilterForm = $filterForm->handleRequest($request);
 
         if($filterForm->isSubmitted() && $filterForm->isValid()){

            $optionValue = $requestFilterForm->get('date')->getData();

             if ("DESC" === $optionValue | "ASC" === $optionValue) {
                 $filterPosts = $categoryRepository->filterByYoutuber($optionValue, $id);
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


     return $this->render('channel_youtube/channel_youtube.html.twig', [
            'categoryPosts' => $categoryPosts,
            'categories' => $categories,
            'form' => $searchForm->createView(),
            'filter' => $filterForm->createView(),
            'boolSortByDate' => $boolSortByDate
     ]);
    }
}
