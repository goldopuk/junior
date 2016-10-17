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


	/**
     * @Route("/import", name="import operations")
     */
    public function importAction(Request $request)
    {

        if ($request->getMethod() == "POST") {

			$em = $this->getDoctrine()->getManager();

			$cmd = $em->getClassMetadata('AppBundle\Entity\Operation');
			$connection = $em->getConnection();
			$dbPlatform = $connection->getDatabasePlatform();
			$connection->query('SET FOREIGN_KEY_CHECKS=0');
			$q = $dbPlatform->getTruncateTableSql($cmd->getTableName());
			$connection->executeUpdate($q);
			$connection->query('SET FOREIGN_KEY_CHECKS=1');

            /**
             * @var $file UploadedFile
             */
            $file = $request->files->get('operations');

            $path = $file->getRealPath();

            $csv = new \parseCSV($path);

            $lastCreated = null;

            $rows = $csv->data;

            foreach ($rows as $row) {

                if ( ! $row['description']) {
                    continue;
                }

				$subcatSlug = $row['subcategory'];

				$repo = $em->getRepository("AppBundle:Subcategory");

				$sub = $repo->findOneBySlug($subcatSlug);

				if ( ! $sub) {
					$sub = new Subcategory();
					$sub->setSlug($subcatSlug);
					$sub->setName($subcatSlug);

					$em->persist($sub);

					$em->flush();
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
				$em->flush();
            }



            exit;



            exit;

        }



        // replace this example code with whatever you need
        return $this->render('default/import.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);
    }
}
