<?php
class OperationServiceTest extends \Symfony\Bundle\FrameworkBundle\Test\KernelTestCase
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    static $container;

    public static function setUpBeforeClass()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        self::$container = $kernel->getContainer();
    }

    function testOne()
    {
        $operationServ = self::$container->get('operation_service');

        $list = $operationServ->getListOfSumByMonth();
        print_r($list);

    }
}