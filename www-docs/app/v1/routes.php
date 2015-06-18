<?php
use \API\v1\Exception;
use \API\v1\Api;

$app->get('/version', function () use ($app) {
    print('{"version":"' . APP_API_REVISION . '"}');
});


/* ========================================================================================
 * The REST Routes
 * ---------------
 * This is the common route for all our controllers
 * based on https://github.com/mac2000/slim-json-rest-service-example/blob/master/index.php
 * ======================================================================================== */

$app->map('/:entity(/:id(/:action))', function ($entity, $id = null, $action = null) use ($app) {

    // To be able to use abstract methods, we instanciate a dummy object.
    // note: it should be doable in a better way!
    $class = \API\v1\REST\Users::getInstance(); // Dummy

    try {

        // ================================================================================
        // Token verification

        $userID = $app->request()->headers('AUTH-ID');
        $userToken = $app->request()->headers('AUTH-TOKEN');
        $userSession = $app->request()->headers('AUTH-SESSION');

        if (!Api::validateToken($userID, $userSession, $userToken)) {
            throw new Exception\Unauthorized();
        }

        // --------------------------------------------------------------------------------
        // Token is valid and can be renewed
        $db = new \API\v1\DB();
        $db->update("UPDATE user_sessions SET Updated = UTC_TIMESTAMP() WHERE UserID = :UserID AND Created = :Created AND Token = :Token", [
            'Token' => $userToken,
            'UserID' => $userID,
            'Created' => gmdate('c', $userSession)
        ], [1]);


        // ================================================================================
        // Parameters & body

        // --------------------------------------------------------------------------------
        // Get the method with which the api has been called
        $callMethod = $app->request()->getMethod();


        // --------------------------------------------------------------------------------
        // Body. The body object.
        $body = '';
        if ($app->request()->params()) {
            $body = $app->request()->params('data') ?: $app->request()->getBody();
        }

        if ('GET' !== $callMethod && '' != $app->request()->getBody()) {
            $getBody = json_decode($app->request()->getBody());
            if ($getBody) {
                $body = json_encode($getBody->data, true);
            }
            else {
                // If it's not JSON, lest try as form data
                $body = [];
                foreach (explode('&', $app->request()->getBody()) as $chunk) {
                    $param = explode("=", $chunk);
                    $body[urldecode($param[0])] = urldecode($param[1]);
                }
            }
        }

        // We store the params to be usable by any method
        Api::$params = $app->request()->params();


        // ================================================================================
        // Classes for REST API

        $class = "\\API\\v1\\REST\\" . ucfirst($entity);

        // Check that class exists and that class implements RestApiInterface
        if (!class_exists($class) || !is_subclass_of($class, '\API\v1\RestApi')) {
            throw new Exception\NotFound('This method does not exists');
        }

        // Seems valid, let's continue by instancing the class
        /** @var \API\v1\RestApi $class */
        $class = $class::getInstance();


        // --------------------------------------------------------------------------------
        // The REST

        if ('GET' == $callMethod && is_null($id)) {
            $method = empty($action) ? 'getList' : $action;
            if (!method_exists(get_class($class), $method)) {
                throw new Exception\NotImplemented('Method ' . $action . ' is not implemented');
            }

            $output = $class->{$method}($id);
        }
        else if ('GET' == $callMethod && !is_null($id)) {
            $method = empty($action) ? 'get' : $action;
            if (!method_exists(get_class($class), $method)) {
                throw new Exception\NotImplemented('Method ' . $action . ' is not implemented');
            }

            $output = $class->{$method}($id);
        }
        else if ('POST' == $callMethod) {
            $method = empty($action) ? 'set' : $action;
            if (!method_exists(get_class($class), $method)) {
                throw new Exception\NotImplemented('Method ' . $action . ' is not implemented');
            }

            $output = $class->{$method}($body);
        }
        else if ('PUT' == $callMethod && !is_null($id)) {
            $method = empty($action) ? 'set' : $action;
            if (!method_exists(get_class($class), $method)) {
                throw new Exception\NotImplemented('Method ' . $action . ' is not implemented');
            }

            $output = $class->{$method}($id, $body);
        }
        else if ('DELETE' == $callMethod && !is_null($id)) {
            $method = empty($action) ? 'delete' : $action;
            if (!method_exists(get_class($class), $method)) {
                throw new Exception\NotImplemented('Method ' . $action . ' is not implemented');
            }

            $output = $class->{$method}($id);
        }
        else {
            throw new Exception\NotImplemented('Method is not implemented');
        }

        // If the output is empty, return 204.
        if (empty($output) && !is_array($output)) {
            $class->finalize($app, '', 204);
        }

        $class->finalize($app, $output);

    }
    catch (Exception\Validation $e) {
        $app->halt(400, $class->finalize($app, $e, 400));
    }
    catch (Exception\Unauthorized $e) {
        $app->halt(401, $class->finalize($app, $e, 401));
    }
    catch (Exception\Forbidden $e) {
        $app->halt(403, $class->finalize($app, $e, 403));
    }
    catch (Exception\NotFound $e) {
        $app->halt(404, $class->finalize($app, $e, 404));
    }
    catch (Exception\NotImplemented $e) {
        $app->halt(501, $class->finalize($app, $e, 501));
    }
    catch (\PDOException $e) {
        // ------------------------------------------------------
        // Add to debug
        Api::$debug[] = $e;
        $app->halt(500, $class->finalize($app, 'DB error', 500));
    }
    catch (\MongoException $e) {
        // ------------------------------------------------------
        // Add to debug
        Api::$debug[] = $e;
        $app->halt(500, $class->finalize($app, 'DB error', 500));
    }
    catch (Exception $e) {
        $app->halt(500, $class->finalize($app, $e, 500));
    }

})->via('GET', 'POST', 'PUT', 'DELETE')->conditions(['id' => '(all)|(\d+,?)+']);
