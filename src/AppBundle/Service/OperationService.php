<?php
namespace AppBundle\Service;

use AppBundle\Entity;
use Doctrine\ORM\EntityManager;

class OperationService
{

    function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getListOfSumByMonth()
    {
        $em = $this->em;

        $connection = $em->getConnection();

        $sql = 'SELECT DATE_FORMAT(o.op_date, "%m/%Y") as `date`, SUM(o.amount) as `amount`, o.currency
            FROM operation o
            JOIN subcategory s on s.id = o.subcategory_id
            JOIN category c  on c.id = s.category_id
            WHERE o.amount > 0
            group by  DATE_FORMAT(o.op_date, "%Y%m")
            ;'
        ;

        $stmt = $connection->query($sql);

        $rows = [];
        foreach ($stmt as $row) {
            $rows[] = $row;
        }

        return $rows;
    }

    public function getListOfSumByMonthForCategory(Entity\Category $category, $currency = "BRL")
    {
        $em = $this->em;

        $connection = $em->getConnection();

        $sql = 'SELECT DATE_FORMAT(o.op_date, "%m/%Y") as `date`, SUM(o.amount) as `amount`, o.currency
            FROM operation o
            JOIN subcategory s on s.id = o.subcategory_id
            JOIN category c  on c.id = s.category_id
            WHERE o.amount > 0
            AND c.id = :categoryId
            group by  DATE_FORMAT(o.op_date, "%Y%m")
            ;'
        ;

        $stmt = $connection->prepare($sql);

        $stmt->execute([":categoryId" => $category->getId()]);

        $resultSet =  $stmt->fetchAll();

        $rows = [];
        foreach ($resultSet as $row) {
            $rows[] = $row;
        }

        return $rows;
    }


    protected function getListOfSumBySubcategoryAndByMonth()
    {
        $em = $this->em;

        $connection = $em->getConnection();
        $sql = 'SELECT
			DATE_FORMAT(o.op_date, "%m/%Y") as `date`,
			c.name as `category`,
			s.name as `subcategory`,
			SUM(amount) as  `amount`, currency
		FROM operation o
		JOIN subcategory s on s.id = o.subcategory_id
		JOIN category c  on c.id = s.category_id
		WHERE o.amount > 0
		group by  DATE_FORMAT(o.op_date, "%Y%m"), o.subcategory_id
		order by DATE_FORMAT(o.op_date, "%Y%m"), s.name
		;';

        $stmt = $connection->query($sql);

        $rows = [];
        foreach ($stmt as $row) {
            $rows[$row['date']][] = $row;
        }

        return $rows;
    }

    /**
     * @param $year
     * @param $month
     * @param string $currency
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getListOfSumBySubcategoryForMonth($year, $month, $currency = "BRL")
    {
        $startDate  = "$year-$month-01";
        $endDate  = "$year-$month-31";

        $em = $this->em;

        $connection = $em->getConnection();
        $sql = '
            SELECT
                c.name as `category`,
                s.name as `subcategory`,
                SUM(amount) as  `amount`,
                currency
            FROM operation o
            JOIN subcategory s on s.id = o.subcategory_id
            JOIN category c  on c.id = s.category_id
            WHERE
                o.amount > 0
                AND (o.op_date BETWEEN :startDate AND :endDate)
            group by  o.subcategory_id
            order by c.name, s.name;'
        ;

        $stmt = $connection->prepare($sql);

        $stmt->execute([":startDate" => $startDate, ":endDate" => $endDate]);

        $resultSet =  $stmt->fetchAll();

        $rows = [];
        foreach ($resultSet as $row) {
            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * @param $year
     * @param $month
     * @param string $currency
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getListOfSumByCategoryForMonth($year, $month)
    {
        $startDate  = "$year-$month-01";
        $endDate  = "$year-$month-31";

        $em = $this->em;

        $connection = $em->getConnection();
        $sql = '
            SELECT
                c.name as `category`,
                SUM(amount) as  `amount`,
                currency
            FROM operation o
            JOIN subcategory s on s.id = o.subcategory_id
            JOIN category c  on c.id = s.category_id
            WHERE
                o.amount > 0
                AND (o.op_date BETWEEN :startDate AND :endDate)
            group by  c.id
            order by c.name, s.name;'
        ;

        $stmt = $connection->prepare($sql);

        $stmt->execute([":startDate" => $startDate, ":endDate" => $endDate]);

        $resultSet =  $stmt->fetchAll();

        $rows = [];
        foreach ($resultSet as $row) {
            $rows[] = $row;
        }

        return $rows;
    }

    public function getCategorySubcategoryMapping()
    {
        $mapping = [];
        $mapping['clothing'] = ['clothing', 'shoes', 'laundry'];
        $mapping['transportation'] = ['taxi', 'publicTransport', 'toll', 'transportation', 'parking', 'petrol', 'blablacar'];
        $mapping['fun'] = ['deezer',  'netflix',  'musicLesson', 'musicInstrument', 'toy', 'costume', 'cafe', 'fun',
            'cigarette', 'entertainment', 'restaurant', 'drink', 'bar',
            'nightlife', 'sport', 'kite', 'forro', 'danceLesson', 'bike', 'camping', 'surfLesson', 'sportItem'];
        $mapping['food'] = ['breakfast', 'workfood', 'lunch', 'diner', 'grocery', 'snack'];
        $mapping['culture'] = ['show', 'cinema', 'museum'];
        $mapping['shelter'] = ['rent', 'houseMove', 'condominio', 'gaz', 'internet'];
        $mapping['utilities'] = ['scam', 'misc', 'nothing', 'unidentified', 'post'];
        $mapping['communication'] = ['phone', 'modem3g', 'skype'];
        $mapping['medical'] = ['medication', 'dental','lens', 'glasses', 'earplug'];
        $mapping['insurance'] = ['maif'];
        $mapping['bank'] = ['creditcard', 'zen', 'itau'];
        $mapping['household'] = ['cd', 'tech', 'household', 'furniture', 'tool', 'papeterie', 'map', 'apps'];
        $mapping['personal'] = [ 'visaBrasil', 'hairdresser'];
        $mapping['education'] = ['press', 'education', 'course', 'book', 'coaching'];
        $mapping['saving'] = ['assurancevie', 'fund', 'pension'];
        $mapping['gift'] = ['gift'];
        $mapping['travel'] = ['travel', 'hotel', 'bus', 'flight', 'train','hostel'];
        $mapping['income'] = ['salary','cheque', 'sale'];
        $mapping['tax'] = ['tax','urssaf'];
        $mapping['pro'] = ['server', 'domainName', 'catho'];

        return $mapping;
    }

    function getCategoryColorMapping()
    {
        $mapping = [];
        $mapping['clothing'] = 'red';
        $mapping['transportation'] = 'yellow';
        $mapping['food'] = 'blue';
        $mapping['fun'] = 'SkyBlue';
        $mapping['culture'] = 'green';
        $mapping['shelter'] = 'black';
        $mapping['utilities'] = 'orange';
        $mapping['medical'] = 'grey';
        $mapping['insurance'] = 'brown';
        $mapping['household'] = 'coral';
        $mapping['personal'] = 'cyan';
        $mapping['education'] = 'indigo';
        $mapping['saving'] = 'lime';
        $mapping['gift'] = 'MediumPurple';
        $mapping['travel'] = 'wheat';
        $mapping['income'] = 'Navy ';
        $mapping['tax'] = 'OliveDrab';

        return $mapping;
    }

    function getCategories()
    {
        return $this->em->getRepository('AppBundle:Category')->findAll();
    }

    function getCategory($categoryId)
    {
        return $this->em->getRepository('AppBundle:Category')->find($categoryId);
    }


    protected function getListOfSumByCategoryAndByMonth()
    {
        $em = $this->em;

        $connection = $em->getConnection();
        $sql = '	SELECT
			DATE_FORMAT(o.op_date, "%m/%Y") as `date`,
			c.name as `category`,
			SUM(amount) as  `amount`, currency
		FROM operation o
		JOIN subcategory s on s.id = o.subcategory_id
		JOIN category c  on c.id = s.category_id
		WHERE o.amount > 0
		group by  DATE_FORMAT(o.op_date, "%Y%m"), c.id
		order by DATE_FORMAT(o.op_date, "%Y%m"), c.name
		;';

        $stmt = $connection->query($sql);

        $rows = [];
        foreach ($stmt as $row) {
            $rows[$row['date']][] = $row;
        }

        return $rows;
    }

    function getSumOfMonth($year, $month, $currency = 'BRL')
    {
        $connection = $this->em->getConnection();

        $startDate  = "$year-$month-01";
        $endDate  = "$year-$month-31";

        $sql = 'SELECT SUM(o.amount) as `amount`
            FROM operation o
            WHERE
                o.amount > 0
                AND o.op_date BETWEEN :startDate AND :endDate
            group by  DATE_FORMAT(o.op_date, "%Y%m")
            ;'
        ;

        $stmt = $connection->prepare($sql);

        $stmt->execute([":startDate" => $startDate, ":endDate" => $endDate]);

        return $stmt->fetchColumn();
    }

}