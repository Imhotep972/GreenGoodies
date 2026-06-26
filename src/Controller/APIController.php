<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\ProductRepository;

final class APIController extends AbstractController
{
   #[Route('/api/products',name:'api_product',methods:['GET'])]
    public function getListProducts(SerializerInterface $serializer, ProductRepository $pr) : JsonResponse
    {
        // arrive ici ceux qui ont passé avec succès le ApiUserChecker : les comptes actifs avec accès API activé
    
        /** @var \App\entity\User $user */
        $user=$this->getUser();

        
        if ($user->isApiEnabled())
        { // meme si en principe ceux qui n'ont pas activé API n'arrivent pas ici
            $productList = $pr->findAll();
            $jsonProductList = $serializer->serialize($productList,'json',['groups' => 'getProduct']);

            if ($jsonProductList)
            { // la liste n'est pas vide
                $errorMessage = "";
                $codestatus = Response::HTTP_OK;
               
                return new JsonResponse([
                    'productList' => $jsonProductList,
                    'codestatus' => $codestatus,
                    'header' => [],
                    'serialized' => true,
                ]);
            }
        }
        // la liste est vide
        $errorMessage = 'Acces API non active';
        $codestatus = Response::HTTP_FORBIDDEN;
        return new JsonResponse(null,$codestatus,['error'=>$errorMessage]);
    }
}
