[![Build Status](https://travis-ci.org/chilimatic/chilimatic-framework.svg?branch=master)](https://travis-ci.org/chilimatic/chilimatic-framework)
# chilimatic-framework

this is for feedback :) fork it, use it if you find something usefull steal it :)
let me know what you think about it

#Pretext:

In some parts I broke the best practices on purpose based on the design of the PHP Interpreter. Those decisions,  naturally, can be argued about. Before the hating starts here is an example why.

the node getter and setters. They are still in there but all properties are public this has to do with the behaviour http://jpauli.github.io/2015/01/22/on-php-function-calls.html before phpng. I need to benchmark it with the new engine because they've changed the behaviour of the function calls, so it has less overhead and performs.

https://drive.google.com/file/d/0B3UKOMH_4lgBUTdjUGxIZ3l1Ukk/view 

I'm quite positive that I can use them again. But if you use it with hundereds of nodes the structure would be slower with methods calls than with public access. So the graph structure should be in a private property but inside the graph everything is public.

the same goes for arrays. before php7 i would not recommend to use this with more than 1000 nodes 
-> see memory allocation of arrays https://nikic.github.io/2011/12/12/How-big-are-PHP-arrays-really-Hint-BIG.html

but as mentioned in https://drive.google.com/file/d/0B3UKOMH_4lgBUTdjUGxIZ3l1Ukk/view you can actually see they changed the architecture of the php array 
[copied from the pdf]
● HashTable size reduced from 72 to 56 bytes
● Bucket size reduced from 72 to 32 bytes
● Memory for all Buckets is allocated at once
● Bucket.key now is a pointer to zend_string and it doesn't have to be duplicated (just reference counter should be increased)
● Values of array elements are embedded into the Buckets
● Improved data locality => less CPU cache misses

which means 
```
$a = array();
for ($i = 0; $i < 1000000; $i++) $a[$i] = array("hello");
echo memory_get_usage(true);
```
Memory Usage 428 MB [old] 33 MB [new]
Time 0.49 sec[old] 0.06 sec [new]

This is huge since memory always is an issue esp. because unset() is using the Garbage Collector and if you compare huge amounts of data you can't use unset because the GC won't collect it instantly but "when he feels that it's best" ,which means you run out of memory, so you need to null the values to instantly clear some memory ..... 


# Config
it's a domain based system for the configuration
* *.cfg
* *.example.com.cfg
* subdomain.example.com.cfg

the *.cfg is the root configuration it will be the first setting overwritten by *.example.com this will 
be overwritten by the subdomain.example.com.cfg

as example

***

```
   use \chilimatic\lib\config\Config;
   Config::getInstance(
   [
      'type' => 'File',
      <path/to/folder/withconfigfiles>
   ]
   );
```

content of the *.cfg
```
   # page settings
   default_timezone = 'Europe/Vienna'
   default_page_encoding = 'utf-8'

   #cache settings
   cache_type = 'memcached'
   cache_settings = { "server_list" : [{"host" : "127.0.0.1", "port" : "11211", "weight" : 1 }] }

   #Session data
   session_type = "Cache"
   session_cache = 'Memcached'
```
content of *.example.com
```
session_type = "Mysql"
```
so the if you use on the domain example.com
```
Config::get('session_type'); // returns Mysql
Config::get('cache_settings') // return \stdClass containing the json 
```

this is a simple example how to use the config object

and for the singelton is an antipattern people ! :) since the singelton in this case is just an container wrapping any config inside of it
```
use \chilimatic\lib\config\File as Config;
$config = new Config([\chilimatic\lib\config\File::CONFIG_PATH_INDEX => <path/to/folder/withconfigfiles>]);
```
or the factory ofc
```
use \chilimatic\lib\config\ConfigFactory;
$config = ConfigFactory::make('File',[\chilimatic\lib\config\File::CONFIG_PATH_INDEX => <path/to/folder/withconfigfiles>] );
```
the array is an interface question since the ini can accept multiple parameters
if they would've confirmed this one in php7 https://wiki.php.net/rfc/named_params I would have implemented it just for php7 


# DI 
so let's think about the service collection the default service collection can be found at
```
lib/general/config/default-service-collection.php
```
this is just ment as an example. I took the closure approach so it's easier to add a service
as an example
```
    $dispatcher = \chilimatic\lib\di\ClosureFactory::getInstance(
        realpath('<path to your closure collection>');
    );
    // set a db object
    $dispatcher->set('db', function() use ($dispatcher) {
        $config = $dispatcher->get('config');

        $mysqlStorage = new \chilimatic\lib\database\mysql\MysqlConnectionStorage();
        $mysqlStorage->addConnection(
            $config->get('mysql_db_host'),
            $config->get('mysql_db_user'),
            $config->get('mysql_db_password'),
            $config->get('mysql_db_name'),
            null
        );

        return $mysqlStorage;
    });
   
    $dispatcher->set('entity-manager', function() use ($dispatcher) {
        $mysqlStorage = $dispatcher->get('db');
        $master = $mysqlStorage->getConnection(0);
        $queryBuilder = $dispatcher->get('query-builder', ['db' => new \chilimatic\lib\database\mysql\Mysql($master)]);

        $em = new \chilimatic\lib\database\orm\EntityManager(
            new \chilimatic\lib\database\mysql\Mysql($master),
            $queryBuilder
        );
        return $em;
    });


    // get a new instance 
    $dispatcher->get('db', []);
    // get it as "singelton"
    $dispatcher->get('em', [], true);
```

but lets start with a basic application example so you can try it out.
You create the "basic" structure
```
<path>
 - app
   - config
     - *.cfg
     - *.example.com.cfg
     - www.example.com.cfg
   - module
     - main [default]
       - controller
         - Index.php
       - view
         - index.phtml
 - public [Docroot]
   - index.php
 - vendor/chilimatic/framework/lib [framework]
```
content of public/index.php
```
<?php

require_once '../vendor/autoload.php';

define('APPLICATION_PATH', realpath('../'));

try {
    use chilimatic\lib\config\Config;

    date_default_timezone_set('Europe/Vienna');
    define('INCLUDE_ROOT', '/var/www/chilimatic.com' );

    set_exception_handler(function($e)
    {
        echo $e->getMessage();
        echo $e->getTraceAsString();
    });
    
    $dispatcher = \chilimatic\lib\di\ClosureFactory::getInstance(
        realpath('../app/config/serviceCollection.php')
    );

    /**
     * Create the config
     */
    $config = $dispatcher->get('config', [
        'type' => 'File',
        \chilimatic\lib\config\File::CONFIG_PATH_INDEX => INCLUDE_ROOT . '/app/config/'
    ]);

    /**
     * Set default timezone based on the config
     */
    date_default_timezone_set((string) $config->get('default_timezone'));

    if (!$config->get((string) 'document_root')) {
        $config->set((string) 'document_root', (string) INCLUDE_ROOT);
    }

    $config->set('app_root', (string) $config->get('document_root') . (string) "/app");
    $config->set('lib_root', (string) $config->get('document_root') . (string) $config->get('lib_dir' ));
    
    $application = new \chilimatic\lib\application\HTTPMVC($dispatcher, $dispatcher->get('config'));
    // this is step so people can inject
    $application->init();
    // returns the rendered result
    echo $application->getHandler()->getContent();
}
catch (Exception $e)
{
    // show error trace
    if (isset($dispatcher) && $dispatcher->get('error-handler', null, true)->getClient()->showError()) {
        $dispatcher->get('error-handler', null, true)->getClient()->log($e->getMessage(), $e->getTraceAsString())->send();
    } else {
        echo 'nothing to concern you with :)';
    }
``` 
content of app/module/main/controller/Index.php
```
namespace chilimatic\app\module\main\controller;

/**
 * Class Index
 * @package \chilimatic\app\default\controller
 */
class Index
{
    /**
     * Class Index
     * @view \chilimatic\lib\view\PHtml()
     */
    public function indexAction(){
        $this->view->pageTitle = 'myPage';
    }
}
```
content of app/module/main/view/index.phtml
```
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8" />
    <title><?php echo $this->pageTitle; ?></title>
</head>
<body></body>
</html>
```


# DataStructures
Normal "Nodes"

```
$node = new \chilimatic\lib\datastructure\graph\Node(null, '.', '');
$node->addChild(new \chilimatic\lib\datastructure\graph\Node($node, 'test', 23));
$node->getChildren(); // return [\chilimatic\lib\datastructure\graph\Node($node, 'test', 23)]
$node->getLastByKey("test"); // return \chilimatic\lib\datastructure\graph\Node($node, 'test', 23)
$node->getLastByKey("test")->getData(); // return 23
```
Binary Tree
```
$bt = \chilimatic\lib\datastructure\graph\tree\binary\BinaryTree();
$bt->insert(\chilimatic\lib\datastructure\graph\tree\binary\BinaryNode('key', [1,2,3]));
$bt->insert(\chilimatic\lib\datastructure\graph\tree\binary\BinaryNode('key1', [1,2,3]));

$bt->findByKey('key1'); // returns \chilimatic\lib\datastructure\graph\tree\binary\BinaryNode('key1', [1,2,3])
```

# Session
you can easily implement your own session storage system
```
// uses memcached as session storage
$session = new chilimatic\lib\session\handler\Session(
                chilimatic\lib\session\engine\Factory::make('Cache', ['type' => 'memcached'])
            );
$session->set('value', 'data');
$session->get('value'); // returns data
```
# Routing
possible types are 
* stdclass
* array numeric
* array assoc
* lambda function
* function call
* static content

the routing is store in binary trees so i hope it performs
you can always fallback to the common routing

```
 \chilimatic\route\Router::register('/test/(:num)', array('job', 'load'), '/');
 \chilimatic\route\Router::register('/user/add/(:num)', array('object' => 'user', 'method' => 'add', 'namespace' => '\\user\\', 'param' => array(true, false)));
 \chilimatic\route\Route::register('/test/(:char)', array('object' => 'user', 'method' => 'add', 'namespace' => '\\user\\', 'param' => array(true, false)));
\chilimatic\route\Route::register('/mytest/(:array)[|]',  function($num) { foreach($num as $val) {  echo $val . ': this is a test'; }});
```

# Caching

```
$cache = \chilimatic\lib\cache\engine\CacheFactory::make('memcached', []);
// this will get you a memcached I added a listing for memcached so you can actually see 
$cache->set('myData', [], $ttl = 10)
$cache->listCache(); // returns ['myData', {'data'}]
$cache->delete('myData'); // returns true
$cache->listCache(); // returns []
```


#ORM / database
```
// you can have multiple connections in the storage -> if you just want to use pdo / mysqli
$mysqlStorage = new \chilimatic\lib\database\mysql\MysqlConnectionStorage();
$mysqlStorage->addConnection('localhost','username', 'password', 'database';
$master = $mysqlStorage->getConnection(0);

// the entity manager
$em = new \chilimatic\lib\database\orm\EntityManager(new \chilimatic\lib\database\mysql\Mysql($master));
// the entityManager needs the corret QueryBuilder -> atm there is only MySQL supportet
$queryBuilder = new \chilimatic\lib\database\orm\querybuilder\MysqlQueryBuilder();
$queryBuilder->setCache(\chilimatic\lib\di\ClosureFactory::getInstance()->get('cache', ['type' => 'shmop']));
$em->setQueryBuilder($queryBuilder);


/**
 * Class Model
 *
 * @package \app\model
 */
class Model1 extends AbstractModel {

    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $model2_id;

    /**
     * maps it to the model2 id and adds the model here
     * @ORM model2_id = \Model2;
     */
    protected $model2;

    /**
     * @param int $id
     */
    public function setModel1($id) {
        $this->model1 = (int) $id;
    }

    /**
     * @param int $id
     */
    public function setModel2($id) {
        $this->model2 = (int) $id;
    }

    /**
     * @return array
     */
    public function jsonSerialize() {
        return [
          'id' => $id,
          'model2_id' => $model2_id
        ];
    }
}

/**
 * Class Model2
 * @ORM table=database.anyTable;
 */
class Model2 extends AbstractModel {
    /**
     * @var int
     */
    protected $id;
}
// returns the model
$model1 = $em->findOneBy(new Model1(), [
    'id' => 1
]);

$model1->setModel2(3);
$em->persist($model1); // updates the model


// create a new entry
$newModel = new Model1();
$newModel->setModel1(2);
$newModel->setModel2(3);

// creates a new table entry
$em->persist($newModel);  
```


The whole Framework is mainly academic but if you find a nice app you wanna use it for or you just want see some concepts or give me some usefull feedback. I would be happy


# Node Filters
this was an approach I took from Doctrine It's a basic filter system. 

```
$filterFactory = new \chilimatic\lib\datastructure\graph\filter\Factory();
// the validator checks if the name is conform to the convetions this-will-be
$filterFactory->setValidator(new chilimatic\lib\validator\DynamicCallNamePreTransformed());
// the transformer will change the this-will-be string to ThisWillBe
$filterFactory->setTransformer(new chilimatic\lib\transformer\string\DynamicObjectCallName());
$filterLastNode = $filterFactory->make('lastNode');
$filterFirstNode = $filterFactory->make('firstNode');


// so lets create a bunch of nodes
$mainNode = new \chilimatic\lib\datastructure\graph\Node($node, 'test', null);
for ($i = 0; $i < 10; $i++) {
   $mainNode->addChild(new \chilimatic\lib\datastructure\graph\Node($node, 'test', $i))
}

$mainNode->getByKey('test', $filterLastNode); // this should return the node with the value 10
$mainNode->getByKey('test', $filterFirstNode); // this should return the node with the value 0 
``` 
