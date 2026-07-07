<?php

namespace App\Service;

use App\Entity\Product;


class ProductService
{

    public function __construct( )
    {
    }


    public function cleanDescription(?Product $product) : string
    {
        if (empty($product)) return '';        
        // on nettoye /r/n <br /> <br> <br/>
        $desc = str_replace(["\r\n","<br>","<br/>"],"\n",$product->getFullDescription());
        // on récupère un tableau de chaine (qui represente chacune un paragraphe)
        $tabDesc = explode("\n",$desc);
        // on nettoie si il y a besoin les différentes chaines
        $tabDesc = array_filter(array_map('trim', $tabDesc));
        // on formatte les chianes selon le format voulu (<p>...</p>)        
        $tabDesc = array_map(fn($p) => '<p class="my-3">' . $p . '</p>', $tabDesc);
        $description = implode($tabDesc);   

        return $description;    
    }
}
