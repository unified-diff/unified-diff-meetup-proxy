<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use DMS\Service\Meetup\MeetupKeyAuthClient;

$app->get('/events.json', function () use ($app) {
    $client = MeetupKeyAuthClient::factory(
        array(
            'key' => $app['config']['meetup_api_key']
        )
    );

    $response = $client->getEvents(
        array(
            'group_urlname' => $app['config']['group_urlname']
        )
    );

    return new JsonResponse($response->toArray(), 200, array('Access-Control-Allow-Origin' => '*'));

})
->bind('events')
;

$app->get('/rsvps/{id}.json', function (Silex\Application $app, $id) use ($app) {
        $client = MeetupKeyAuthClient::factory(
            array(
                'key' => $app['config']['meetup_api_key']
            )
        );

        $response = $client->getRSVPs(
            array(
                'event_id' => $id
            )
        );

        return new JsonResponse($response->toArray(), 200, array('Access-Control-Allow-Origin' => '*'));

    })
    ->bind('rsvps')
;

$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    $page = 404 == $code ? '404.html' : '500.html';

    return new Response($app['twig']->render($page, array('code' => $code)), $code);
});
