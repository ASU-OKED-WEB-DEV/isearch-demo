<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/vendor/autoload.php';

$app = new \Slim\App();

$app->get('/', function (Request $request, Response $response) {

    $client = new GuzzleHttp\Client();

    $request = $client->request('GET', 'https://isearch.asu.edu/endpoints/dept_tree/json');

    $results = json_decode($request->getBody());

    $oked = $results[0]->children[11];

    print "<h2>" . formatName($oked) . "</h2>";

    $link = "/dept/" . $oked->dept_nid . "/" . $oked->name;

    print "<p><a href='" . $link . "'>Get Employees</a></p>";

    print "<div style='margin-left:60px;'>";

    foreach($oked->children as $dept) {

        print "<h3>" . formatName($dept) . "</h3>";

        print formatLink($dept);

        if(!empty($dept->children)) {

            print "<div style='margin-left:60px;'>";

            foreach($dept->children as $subDept) {

                print "<h4>" . formatName($subDept) . "</h4>";

                print formatLink($subDept);

            }

            print "</div>";

        }

    }

    print "</div>";

});

$app->get('/dept/{nid}/{name}', function (Request $request, Response $response, $args) {

    $client = new GuzzleHttp\Client();

    $request = $client->request('GET', 'http://isearch.asu.edu/endpoints/dept-profiles/json/' . $args['nid']);

    $results = json_decode($request->getBody());

    print "<h2>" . $args['name'] . "</h2>";

    foreach($results as $person) {

        print "<div style='border-bottom:1px solid #EEE;margin-bottom: 20px;padding-bottom:10px;'>";

        print "<h3>" . $person->firstName . " " . $person->lastName . "</h3>";

        print "<p>Titles: " . implode(", ", $person->titles) . "</p>";

        print "<p>Departments: " . implode(", ", $person->departments) . "</p>";

        //print "<p>Department IDs: " . implode(", ", $person->deptids) . "</p>";

        if(isset($person->bio)) {

            print "<h4>Bio</h4>";

            print $person->bio;

        }

        print "</div>";

    }

});

$app->run();

function formatLink($dept) {

    $link =  "/dept/" . $dept->dept_nid . "/" . $dept->name;

    return "<p><a href='" . $link . "' target='_blank'>Get Employees</a></p>";

}

function formatName($dept) {

    return $dept->name . " - " . $dept->dept_id;

}
