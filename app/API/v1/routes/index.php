<?php
use API\Exception;
use API\v1\lib;

/**
 * This is the common route for all our controllers
 * (except login of course)
 *
 * Inspired by: https://github.com/mac2000/slim-json-rest-service-example/blob/master/index.php
 */
$app->map('/:entity(/:id)', function ($entity, $id = null) use ($app) {

    try {

        // todo: Here, you could add a auth token verification
        // ***************************************************


        // get the entity called
        // ***************************************************
        $class = "\\API\\v1\\lib\\".ucfirst($entity);


        // Check that class exists
        if (!class_exists($class)) {
            throw new Exception\NotFoundException();
        }
        // Check that class implements RestApiInterface
        if (!is_subclass_of($class, '\API\v1\lib\RestApiInterface')) {
            throw new Exception\NotFoundException();
        }

        /** @var API\v1\lib\RestApiInterface $class */
        $class = $class::getInstance();
        $method = $app->request()->getMethod();

        if ($method == 'GET' && $id == null) {
            $res = $class->find();
        }
        else if ($method == 'GET' && $id !== null) {
            $res = $class->findOne($id);
        }
        else if($method == 'POST') {
            $res = $class->insert(json_decode($app->request()->getBody()));
        }
        else if($method == 'PUT' && $id != null) {
            $res = $class->findAndModify($id, json_decode($app->request()->getBody()));
        }
        else if($method == 'DELETE' && $id != null) {
            $res = $class->remove($id);
        }
        else {
            // Not implemented
            $app->halt(501);
        }

        if(empty($res)) throw new Exception\NotFoundException();

        $class->finalize($res);
    }
    catch (Exception\ValidationException $e) {
        $app->halt(400, $e->getMessage());
    }
    catch (Exception\ForbiddenException $e) {
        $app->halt(403);
    }
    catch (Exception\NotFoundException $e) {
        $app->halt(404, $e->getMessage());
    }
    catch (Exception\UnauthorizedException $e) {
        $app->halt(401, $e->getMessage());
    }
    catch (\Exception $e) {
        //$app->halt(500, $e->getMessage());
    }

})->via('GET', 'POST', 'PUT', 'DELETE')->conditions(array('id' => '\d+'));
