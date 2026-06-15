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
        /** @var \App\entity\User $user */
        $user=$this->getUser();
        if (empty($user))
        {// pas connecte, a priori courtcircuiter par JWT (message dans Body)
            $errorMessage = "Identifiants incorrect";
            $codestatus = Response::HTTP_UNAUTHORIZED;
            return new JsonResponse(null,$codestatus, ['error'=> $errorMessage] );
        }
        if ($user->getArchive())
        { // compte supprimé donc plus d'acces
            $errorMessage = "Compte supprime";
            $codestatus = Response::HTTP_UNAUTHORIZED;
            return new JsonResponse(null,$codestatus, ['error'=> $errorMessage] );
        }
        if ($user->getAPI())
        {
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
