<?php
    return
    [
        'view' => function($setting = []) {
            return new \chilimatic\lib\view\phtml();
        },
        'db' => function($setting = []) {
            return new PDO($setting['dns']);
        },
        'request-handler' => function($setting = []) {
            return \chilimatic\lib\request\Handler::getInstance();
        },
        'application-handler' => function($setting = []) {
            return new chilimatic\lib\handler\HTTPHandler();
        },
        'routing' => function($setting = []) {
            return new \chilimatic\lib\route\Router($setting['type']);
        },
        'session' => function($setting = []){
            return new chilimatic\lib\session\handler\Session(
                chilimatic\lib\session\engine\Factory::make(@$setting['type'], @$setting['param'])
            );
        },
        'template-resolver' => function ($setting = []) {
            return new chilimatic\lib\view\resolver\templatePathStack($setting);
        },
        'cache' => function($setting = []) {
            return chilimatic\lib\cache\engine\CacheFactory::make($setting['type'], isset($setting['setting']) ? $setting['setting'] : null);
        },
        'entity-manager' => function($setting = []) {
            $mysqlStorage = new \chilimatic\lib\database\mysql\MysqlConnectionStorage();
            $mysqlStorage->addConnection(
                $setting['host'],
                $setting['username'],
                $setting['password'],
                isset($setting['database']) ? $setting['database'] : null,
                isset($setting['port']) ? $setting['port'] : null,
                isset($setting['charset']) ? $setting['charset'] : null
            );
            $master = $mysqlStorage->getConnection(0);
            $em = new \chilimatic\lib\database\orm\EntityManager(new \chilimatic\lib\database\mysql\Mysql($master));
            return $em->setQueryBuilder(\chilimatic\lib\di\ClosureFactory::getInstance()->get('query-builder'));
        },
        'query-builder' => function($setting = []) {
            $queryBuilder = new \chilimatic\lib\database\orm\MysqlQueryBuilder();
            return $queryBuilder->setCache(\chilimatic\lib\di\ClosureFactory::getInstance()->get('cache', ['type' => 'shmop']));
        },
        'error-handler' => function($setting = []) {
            return new \chilimatic\lib\error\Handler(new \chilimatic\lib\log\client\printOut());
        },
    ];