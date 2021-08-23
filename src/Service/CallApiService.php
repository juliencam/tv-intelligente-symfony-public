<?php

namespace App\Service;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CallApiService
{

    private $client;

    private $baseUri = 'https://tvintelligente.fr/api/';

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getPosts(int $page, int $itemPerPage = 10) :array
    {
        $reponse = $this->client->request(
            'GET',
            $this->baseUri . 'posts?page='. $page .'&itemsPerPage='. $itemPerPage
        );

        return $reponse->toArray();
    }

    public function getPost(int $id)
    {
        $reponse = $this->client->request(
            'GET',
            $this->baseUri . 'posts/'. $id
        );

        return $reponse->toArray();
    }

    public function getCategories()
    {
        $reponse = $this->client->request(
            'GET',
            $this->baseUri . 'categories'
        );

        return $reponse->toArray();
    }



    public function getCategory(int $id)
    {
        $reponse = $this->client->request(
            'GET',
            $this->baseUri . 'categories/' . $id
         );

        return $reponse->toArray();
    }

    public function getTags()
    {
        $reponse = $this->client->request(
            'GET',
            $this->baseUri . 'tags'
         );

        return $reponse->toArray();
    }

    public function getTag(int $id)
    {
        $reponse = $this->client->request(
            'GET',
            $this->baseUri . 'tags/' . $id
         );

        return $reponse->toArray();
    }

}