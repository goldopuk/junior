<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use Doctrine\DBAL\Types\DateType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class BudgetController extends Controller
{

    /**
     * @Route("report", name="report")
     */
    public function reportAction(Request $request)
    {
        $operationServ = $this->get('operation_service');

        $categories = $operationServ->getCategories();

        $choices = ['---' =>  0];

        foreach ($categories as $category) {
            /**
             * @var $category Category
             */
            $choices[$category->getName()] = $category->getId();
        }

        $form = $this->createFormBuilder()
            ->add('category', ChoiceType::class, ['choices' => $choices])
            ->add('send', SubmitType::class, array('label' => 'send'))
            ->setMethod('get')
            ->getForm();

        $operationServ = $this->get('operation_service');

        $form->handleRequest($request);

        $category = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $categoryId = $data['category'];

            if ($categoryId) { // case no category were selected
                $category = $operationServ->getCategory($categoryId);
            }
        }

        if ($category) {
            $listSumByMonth = $operationServ->getListOfSumByMonthForCategory($category);
        } else {
            $listSumByMonth = $operationServ->getListOfSumByMonth();
        }

        return $this->render('AppBundle:Budget:report.html.twig', [
            'listSumByMonth' => $listSumByMonth,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("month", name="month")
     */
    public function monthAction(Request $request)
    {

        $monthList = [];

        for ($i = 1; $i <= 12 ; $i++) {
            $monthList[$i] = $i;
        }

        $form = $this->createFormBuilder()
            ->add('year', ChoiceType::class, ['choices' => ["2014" => "2014","2015" => "2015", "2016" => "2016"]])
            ->add('month', ChoiceType::class, ['choices' => $monthList])
            ->add('send', SubmitType::class, array('label' => 'send'))
            ->setMethod('get')
            ->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $year = $data['year'];
            $month = $data['month'];

            $operationServ = $this->get('operation_service');

            $sum = $operationServ->getSumOfMonth($year, $month);

            $list = $operationServ->getListOfSumBySubcategoryForMonth($year, $month);
            $listSumByCategory = $operationServ->getListOfSumByCategoryForMonth($year, $month);
        } else {
            $sum = 0;
            $list = [];
            $listSumByCategory = [];
        }

        return $this->render('AppBundle:Budget:month.html.twig', array(
            "totalSum" => $sum,
            "listSumBySubcategory" => $list,
            "listSumByCategory" => $listSumByCategory,
            'form' => $form->createView()
        ));
    }

    public function headerAction()
    {
        $operationServ = $this->get('operation_service');
        $datastore = [];

        $datastore['categoryColors'] = $operationServ->getCategoryColorMapping();

        return $this->render('AppBundle:Budget:header.html.twig', array(
            'datastore' => $datastore
        ));
    }

}
