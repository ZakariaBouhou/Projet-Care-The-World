<?php


namespace App\Service;


use Symfony\Component\String\Slugger\SluggerInterface;

class CategorySlugger{

    private $slugger;


    public function __construct(SluggerInterface $slugger)
    {   
        $this->slugger =$slugger;
    }

    /**
     * Prend une chaine de caractères et la retourne sous forme de slug
     * @param string $string
     * @retour string
     */

    public function slugify(string $string): string
    {
        return strtolower($this->slugger->slug($string));
    }
}