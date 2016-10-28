<?php

namespace JuniorBundle\Controller;

use AppBundle\Entity\Question;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("importQuestion")
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

        return $this->render('AppBundle:Question:import.html.twig', array(
            // ...
        ));
    }

    protected function truncateOperation()
    {
        $em = $this->getDoctrine()->getManager();

        $cmd = $em->getClassMetadata('AppBundle\Entity\Question');
        $connection = $em->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $connection->query('SET FOREIGN_KEY_CHECKS=0');
        $q = $dbPlatform->getTruncateTableSql($cmd->getTableName());
        $connection->executeUpdate($q);
        $connection->query('SET FOREIGN_KEY_CHECKS=1');
    }

    protected function import(UploadedFile $file)
    {
        $path = $file->getRealPath();

        $csv = new \parseCSV($path);

        $lastCreated = null;

        $rows = $csv->data;

        $this->importQuestions($rows);
    }

    protected function importQuestions(array $rows)
    {
        $em = $this->getDoctrine()->getManager();

        foreach ($rows as $row) {

            $op = new Question();
            $op->setDescription($row['description']);
            $op->setAnswer($row['answer']);

            $op->setTheme($row['theme']);
            $op->setSubtheme($row['subtheme']);

            $em->persist($op);
        }

        $em->flush();
    }
}
