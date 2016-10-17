<?php
// src/AppBundle/Entity/Product.php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="subcategory")
 */
class Subcategory {
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

	/**
	 * @ORM\ManyToOne(targetEntity="Category", inversedBy="subcategories")
	 * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
	 **/
	protected $category;

	/**
	 * @ORM\OneToMany(targetEntity="Operation", mappedBy="subcategory")
	 * @var Operation[]
	 */
	protected $operations;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $slug;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->operations = new ArrayCollection();
	}


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Subcategory
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Subcategory
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set category
     *
     * @param \AppBundle\Entity\Category $category
     *
     * @return Subcategory
     */
    public function setCategory(\AppBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \AppBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Add operation
     *
     * @param \AppBundle\Entity\Operation $operation
     *
     * @return Subcategory
     */
    public function addOperation(\AppBundle\Entity\Operation $operation)
    {
        $this->operations[] = $operation;

        return $this;
    }

    /**
     * Remove operation
     *
     * @param \AppBundle\Entity\Operation $operation
     */
    public function removeOperation(\AppBundle\Entity\Operation $operation)
    {
        $this->operations->removeElement($operation);
    }

    /**
     * Get operations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOperations()
    {
        return $this->operations;
    }
}
