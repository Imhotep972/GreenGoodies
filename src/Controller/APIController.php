<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\ProductRepository;

final class APIController extends AbstractController
{
   #[Route('/api/products',name:'api_product',methods:['GET'])]
    public function getListProducts(ProductRepository $pr) : JsonResponse
    {
        // arrive ici ceux qui ont passé avec succès le ApiUserChecker : les comptes actifs avec accès API activé
    
        /** @var \App\entity\User $user */
        $user=$this->getUser();
        
        // meme si en principe ceux qui n'ont pas activé API n'arrivent pas ici
        if(!$user->isApiEnabled())
        {
            $errorMessage = 'Accès API non activé';
            $codestatus = Response::HTTP_FORBIDDEN;            
            return new JsonResponse(['error'=>$errorMessage],$codestatus,);    
        }

        $productsList = $pr->findAll();

        $errorMessage = "";
           
        return $this->json(
            ['product' =>$productsList],
            Response::HTTP_OK,
            [],
            ['groups' => 'getProduct'] 
        );
    }
}
