<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Operation;
use AppBundle\Entity\Subcategory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\DateTime;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);
    }

	/**
	 * @Route("/test", name="homepage")
	 */
	public function testAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
//
//		$repo = $em->getRepository("AppBundle:Subcategory");
//
//		$subcatSlug = $repo->findOneBySlug('tata slug sub');
//
//		\Doctrine\Common\Util\Debug::dump($subcatSlug);
//		exit;

		$subcategory = new Subcategory();
		$subcategory->setName('tata sub');
		$subcategory->setSlug('tata slug sub');


		$em->persist($subcategory);
		$em->flush();
exit;


		$category = new Category();
		$category->setName('totocat');
		$category->setSlug('slug cat');


		$em->persist($category);

		$subcategory = new Subcategory();
		$subcategory->setName('tata sub');
		$subcategory->setSlug('tata slug sub');

		$subcategory->setCategory($category);

		$em->persist($subcategory);
		$em->flush();

		$operation = new Operation();
	}

	protected function truncateOperation()
	{
		$em = $this->getDoctrine()->getManager();

		$cmd = $em->getClassMetadata('AppBundle\Entity\Operation');
		$connection = $em->getConnection();
		$dbPlatform = $connection->getDatabasePlatform();
		$connection->query('SET FOREIGN_KEY_CHECKS=0');
		$q = $dbPlatform->getTruncateTableSql($cmd->getTableName());
		$connection->executeUpdate($q);
		$connection->query('SET FOREIGN_KEY_CHECKS=1');

		$cmd = $em->getClassMetadata('AppBundle\Entity\Subcategory');
		$connection = $em->getConnection();
		$dbPlatform = $connection->getDatabasePlatform();
		$connection->query('SET FOREIGN_KEY_CHECKS=0');
		$q = $dbPlatform->getTruncateTableSql($cmd->getTableName());
		$connection->executeUpdate($q);
		$connection->query('SET FOREIGN_KEY_CHECKS=1');

		$cmd = $em->getClassMetadata('AppBundle\Entity\Category');
		$connection = $em->getConnection();
		$dbPlatform = $connection->getDatabasePlatform();
		$connection->query('SET FOREIGN_KEY_CHECKS=0');
		$q = $dbPlatform->getTruncateTableSql($cmd->getTableName());
		$connection->executeUpdate($q);
		$connection->query('SET FOREIGN_KEY_CHECKS=1');
	}

	protected function getCategorySubcategoryMapping()
	{
		$mapping = [];
		$mapping['clothing'] = ['clothing', 'shoes', 'laundry'];
		$mapping['transportation'] = ['taxi', 'publicTransport', 'toll', 'transportation', 'parking', 'bike'];
		$mapping['fun'] = ['musicLesson', 'toy', 'costume', 'cafe', 'fun',
			'cigarette', 'entertainment', 'restaurant', 'drink', 'bar',
			'nightlife', 'sport', 'kite', 'forro', 'danceLesson'];
		$mapping['food'] = ['breakfast', 'workfood', 'lunch', 'diner', 'grocery', 'snack'];
		$mapping['culture'] = ['show', 'cinema'];
		$mapping['shelter'] = ['rent', 'houseMove'];
		$mapping['utilities'] = ['scam', 'misc', 'phone'];
		$mapping['medical'] = ['medication'];
		$mapping['insurance'] = [];
		$mapping['household'] = ['cd', 'tech', 'household'];
		$mapping['personal'] = [ 'visaBrasil', 'hairdresser'];
		$mapping['education'] = ['press', 'education', 'course', 'book'];
		$mapping['saving'] = [];
		$mapping['gift'] = ['gift'];
		$mapping['travel'] = ['travel', 'hotel'];
		$mapping['income'] = ['salary'];
		$mapping['tax'] = ['tax'];

		return $mapping;
	}


	protected function importCategories()
	{
		$mapping = $this->getCategorySubcategoryMapping();

		$em = $this->getDoctrine()->getManager();

		foreach (array_keys($mapping) as $slug) {
			$cat = new Category();
			$cat->setName($slug);
			$cat->setSlug($slug);
			$em->persist($cat);
		}

		$em->flush();
	}

	protected function getSubcategorySlugList(array $rows)
	{
		$subcategories = [];

		foreach ($rows as $row) {

			if ( ! $row['subcategory']) {
				continue;
			}

			if ( ! in_array($row['subcategory'], $subcategories)) {
				$subcategories[] = $row['subcategory'];
			}
		}

		return $subcategories;
	}

	protected function getCategoryBySubcategorySlug($slug)
	{
		$mapping = $this->getCategorySubcategoryMapping();

		foreach ($mapping as $categorySlug => $subcategorySlugs) {
			if (in_array($slug, $subcategorySlugs)) {
				$em = $this->getDoctrine()->getManager();
				$repo = $em->getRepository("AppBundle:Category");

				/**
				 * @var $category Category
				 */
				$category = $repo->findOneBySlug($categorySlug);

				if ( ! $category) {
					throw new \Exception("Category $categorySlug missing");
				}

				return $category;
			}
		}

		return null;
	}


	protected function importSubcategories(array $rows)
	{
		$em = $this->getDoctrine()->getManager();

		$categories = $this->getSubcategorySlugList($rows);

		foreach ($categories as $subcatSlug) {

			$sub = new Subcategory();
			$sub->setSlug($subcatSlug);
			$sub->setName($subcatSlug);

			$category = $this->getCategoryBySubcategorySlug($subcatSlug);

			if ($category) {
				$sub->setCategory($category);
			}

			$em->persist($sub);
		}

		$em->flush();
	}

	protected function importOperations(array $rows)
	{

		$em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository("AppBundle:Subcategory");

		$lastCreated = null;

		foreach ($rows as $row) {

			$subcatSlug = trim($row['subcategory']);

			if (!$subcatSlug) {
				continue;
			}

			/**
			 * @var $sub Subcategory
			 */
			$sub = $repo->findOneBySlug($subcatSlug);

			if ( ! $sub) {
				throw new \Exception("missing subcategory $subcatSlug" . print_r($row, true));
			}

			$op = new Operation();
			$op->setAmount($row['amount']);
			$op->setSubcategory($sub);

			if ($row['created']) {
				$created = $row['created'];
				$created = \DateTime::createFromFormat('d/m/Y', $created);
			} else if ($lastCreated) {
				$created = $lastCreated;
			} else {
				throw new \Exception('missing date for row' . print_r($row, true));
			}

			$op->setOpDate($created);
			$op->setCurrency($row['currency']);
			$op->setDescription($row['description']);

			$em->persist($op);

			$lastCreated = $op->getOpDate();
		}

		$em->flush();
	}

	protected function import(UploadedFile $file)
	{
		$path = $file->getRealPath();

		$csv = new \parseCSV($path);

		$lastCreated = null;

		$rows = $csv->data;

		$this->importCategories();

		$this->importSubcategories($rows);

		$this->importOperations($rows);
	}


	/**
     * @Route("/import", name="import operations")
     */
    public function importAction(Request $request)
    {

        if ($request->getMethod() == "POST") {

			$this->truncateOperation();

			/**
			 * @var $file UploadedFile
			 */
			$file = $request->files->get('operations');

			$this->import($file);

			die('import done');
        }

        return $this->render('default/import.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);
    }
}
