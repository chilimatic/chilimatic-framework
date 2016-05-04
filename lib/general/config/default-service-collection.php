<?php
use chilimatic\lib\Database\Sql\Mysql\Connection\MySQLConnectionStorage;
use chilimatic\lib\Database\Sql\Mysql\Querybuilder\MySQLQueryBuilder;
use chilimatic\lib\Database\Sql\Orm\EntityManager;
use chilimatic\lib\log\client\PrintOut;

return
    [
        'view'                => function ($setting = []) {
            return new \chilimatic\lib\view\PHtml();
        },
        'db'                  => function ($setting = []) {
            return new PDO($setting['dns']);
        },
        'request-handler'     => function ($setting = []) {
            return \chilimatic\lib\request\Handler::getInstance($setting);
        },
        'application-handler' => function ($setting = []) {
            return new chilimatic\lib\handler\HTTPHandler($setting);
        },
        'routing'             => function ($setting = []) {
            return new \chilimatic\lib\route\Router($setting['type']);
        },
        'session'             => function ($setting = []) {
            return new chilimatic\lib\session\handler\Session(
                chilimatic\lib\session\engine\Factory::make(@$setting['type'], @$setting['param'])
            );
        },
        'template-resolver'   => function ($setting = []) {
            return new chilimatic\lib\view\resolver\templatePathStack($setting);
        },
        'cache'               => function ($setting = []) {
            return chilimatic\lib\cache\engine\CacheFactory::make($setting['type'], isset($setting['setting']) ? $setting['setting'] : null);
        },
        'entity-manager'      => function ($setting = []) {
            $mysqlStorage = new MySQLConnectionStorage();
            $mysqlStorage->addConnection(
                $setting['host'],
                $setting['username'],
                $setting['password'],
                isset($setting['database']) ? $setting['database'] : null,
                isset($setting['port']) ? $setting['port'] : null,
                isset($setting['charset']) ? $setting['charset'] : null
            );

            $master = $mysqlStorage->getConnection(0);
            $em     = new EntityManager(new Mysql($master));

            return $em->setQueryBuilder(\chilimatic\lib\di\ClosureFactory::getInstance()->get('query-builder'));
        },
        'query-builder'       => function ($setting = []) {
            $queryBuilder = new MysqlQueryBuilder(\chilimatic\lib\di\ClosureFactory::getInstance()->get('cache', ['type' => 'shmop']));
            return $queryBuilder;
        },
        'error-handler'       => function ($setting = []) {
            return new \chilimatic\lib\error\Handler(new PrintOut());
        },
    ];