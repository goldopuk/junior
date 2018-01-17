<?php
namespace AppBundle\Service;


use AppBundle\Entity\Category;
use AppBundle\Entity\Subcategory;
use Doctrine\ORM\EntityManager;

class CategoryService
{
    /**
     * @var EntityManager
     */
    protected $em;

    function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    function createCategory($name, $slug)
    {
        $cat = new Category();
        $cat->setName($name);
        $cat->setSlug($slug);
        $this->em->persist($cat);
        $this->em->flush();
    }

    function createSubcategory(Category $category, $name, $slug)
    {
        $sub = new Subcategory();
        $sub->setSlug($slug);
        $sub->setName($name);

        $sub->setCategory($category);

        $this->em->persist($sub);
        $this->em->flush();
    }
}