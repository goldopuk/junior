<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Category;
use AppBundle\Entity\Subcategory;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Operation;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class OperationsLoader  implements FixtureInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function load(ObjectManager $manager)
    {
        $env = $this->container->get('kernel')->getEnvironment();

        if ($env === 'test') {
            $fileId = '1hYYGMHndufDIFVCA1vSxdTxjw6W3DjruD_u_prqlhw4';
        } elseif ($env === 'dev') {
            $fileId = '16ZwuttUPY2XzpxyIzl6U7GLqdkPexRGNLASC1TvWz8U';
        } else {
            throw new \Exception("incorrect env");
        }

        $this->import($fileId);
    }

    protected function importCategories()
    {
        $operationServ = $this->container->get('operation_service');

        $mapping = $operationServ->getCategorySubcategoryMapping();

        $em = $this->container->get('doctrine.orm.entity_manager');

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

            // to ignore empty lines
            if ( ! $row['subcategory'] && ! $row['amount']) {
                continue;
            }

            if ( ! $row['subcategory']) {
                var_dump($row);
                throw new \Exception('missing subcategory');
            }

            if ( ! in_array($row['subcategory'], $subcategories)) {
                $subcategories[] = $row['subcategory'];
            }
        }

        return $subcategories;
    }

    protected function getCategoryBySubcategorySlug($slug)
    {
        $operationServ = $this->container->get('operation_service');
        $mapping = $operationServ->getCategorySubcategoryMapping();

        foreach ($mapping as $categorySlug => $subcategorySlugs) {
            if (in_array($slug, $subcategorySlugs)) {
                $em = $this->container->get('doctrine.orm.entity_manager');
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
        $em = $this->container->get('doctrine.orm.entity_manager');

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

        $em = $this->container->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository("AppBundle:Subcategory");

        $lastCreated = null;

        foreach ($rows as $row) {

            if ($row['more'] === 'ignore') {
                continue;
            }

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


            $changeRate = $this->getChangeRate($created);
            $op->setChangeRate($changeRate);

            $amount = $row['currency'] === 'EUR' ? round($changeRate * $row['amount']) : $row['amount'];
            $op->setAmount($amount);

            $op->setCurrency('BRL');
            $op->setDescription($row['description']);

            $em->persist($op);

            $lastCreated = $op->getOpDate();
        }

        $em->flush();
    }

    protected function getChangeRate(\DateTime $d)
    {
        switch ($d->format('Y')) {
            case 2012:
                return 2.6;
            case 2013:
                return 3;
            case 2014:
                return 3;
            case 2015:
                return 4;
            case 2016:
                return 3.6;
            default:
                return 3;
        }

    }

    function downloadCsvFromDrive($fileId)
    {
        // Get the API client and construct the service object.
        $client = $this->getClient();
        $service = new \Google_Service_Drive($client);


        $content = $service->files->export($fileId, 'text/csv', array(
            'alt' => 'media' ));
        /**
         * @var $content \GuzzleHttp\Psr7\Response
         */
        $str = (string)$content->getBody();

        $path = '/tmp/tmp.cvf';
        file_put_contents($path, $str);

        return $path;
    }

    protected function import($fileId)
    {
        $path = $this->downloadCsvFromDrive($fileId);

        $csv = new \parseCSV($path);

        $lastCreated = null;

        $rows = $csv->data;

        $this->importCategories();

        $this->importSubcategories($rows);

        $this->importOperations($rows);
    }

    /**
     * Returns an authorized API client.
     * @return \Google_Client the authorized client object
     */
    function getClient() {

        $scopes = implode(' ', array(
            \Google_Service_Drive::DRIVE_METADATA_READONLY,
            \Google_Service_Drive::DRIVE_FILE,
            \Google_Service_Drive::DRIVE,
        ));

        $secretFile = realpath($this->container->get('kernel')->getRootDir() . '/../client_secret.json');

        $client = new \Google_Client();
        $client->setApplicationName('Drive API PHP Quickstart');
        $client->setScopes($scopes);
        $client->setAuthConfig($secretFile);
        $client->setAccessType('offline');

        // Load previously authorized credentials from a file.
        $credentialsPath = '/home/julien/.credentials/drive-php-quickstart.json';
        if (file_exists($credentialsPath)) {
            $accessToken = json_decode(file_get_contents($credentialsPath), true);
        } else {
            die('need access file');
        }
        $client->setAccessToken($accessToken);

        // Refresh the token if it's expired.
        if ($client->isAccessTokenExpired()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
        }


        return $client;
    }

}