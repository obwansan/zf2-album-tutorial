<?php

namespace Album;

use Album\Model\Album;
use Album\Model\AlbumTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface
{
    public function getAutoloaderConfig()
    {
        // Using Composer you can leave this empty and add an autoload entry
        // to composer.json

        // return array(
        //     'Zend\Loader\ClassMapAutoloader' => array(
        //         __DIR__ . '/autoload_classmap.php',
        //     ),
        //     'Zend\Loader\StandardAutoloader' => array(
        //         'namespaces' => array(
        //             __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
        //         ),
        //     ),
        // );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Album\Model\AlbumTable' =>  function($sm) {
                    $tableGateway = $sm->get('AlbumTableGateway');
                    $table = new AlbumTable($tableGateway);
                    return $table;
                },
                //  The TableGateway classes use the prototype pattern for creation
                //  of result sets and entities. This means that instead of instantiating
                //  when required, the system clones a previously instantiated object.
                //  See PHP Constructor Best Practices and the Prototype Pattern for more details.
                'AlbumTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Album());
                    return new TableGateway('album', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
}
