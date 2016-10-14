<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Operation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
     * @Route("/import", name="import operations")
     */
    public function importAction(Request $request)
    {
        if ($request->getMethod() == "POST") {

            /**
             * @var $file UploadedFile
             */
            $file = $request->files->get('operations');

            $path = $file->getRealPath();

            $csv = new \parseCSV($path);

            $em = $this->getDoctrine()->getEntityManager();

            $lastCreated = null;

            $rows = $csv->data;

            foreach ($rows as $row) {

                if ( ! $row['description']) {
                    continue;
                }

                $op = new Operation();
                $op->setAmount($row['amount']);

                if ($row['created']) {
                    $created = $row['created'];

                    $created = \DateTime::createFromFormat('d/m/Y', $created);
                } else if ($lastCreated) {
                    $created = $lastCreated;
                } else {
                    throw new \Exception('missing date');
                }

                $op->setOpDate($created);
                $op->setCurrency($row['currency']);
                $op->setDescription($row['description']);
                $op->setTags($row['tags']);

                $em->persist($op);

                $lastCreated = $op->getOpDate();
            }

            $em->flush();


            exit;



            exit;

        }



        // replace this example code with whatever you need
        return $this->render('default/import.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);
    }
}
