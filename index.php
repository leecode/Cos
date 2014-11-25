<?php
require_once 'ChromePhp.php';

require_once 'Cos/Cos.php';
\Cos\Cos::registerAutoloader();

$className = 'Cos\http\Request';
//Cos::autoload($className);
$app = new \Cos\Cos();

$app->get('/shits', function() use ($app) {
	//ChromePhp::log('you are calling /shits, using GET');
	$blogs = array();
	$request = $app->request;
	
	for($i = 0; $i < 10; $i++) {
		$blogs[] = array('id' => $i + 1, 'title' => "blog:$i");
	}
	
	echo json_encode($blogs);
	ChromePhp::log('REQUEST:---> ' . var_export($request->params(), true));
});

$app->get('/blogs/:id', function($aDifferentName) {
	ChromePhp::log('blog_id --> ' . $aDifferentName);
});

$app->post('/bullshits', function() {
	ChromePhp::log('your are calling bullshits, using POST');
});

$app->get('/blogs', function() {
	echo 'Your are calling /blogs to get all blogs';
});

$app->post('/blogs', function() {
	echo 'You are calling /blogs to insert a blog';
});

$app->put('/blogs/:id', function($id) {
	echo 'You are calling /blogs/:id to update a blog with id : ' . $id;
});

$app->delete('/blogs/:id', function($id) {
	echo 'You are calling /blogs/:id to delete the blog with id : ' . $id;
});

$app->run();