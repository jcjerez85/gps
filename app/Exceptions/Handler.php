<?php namespace App\Exceptions;

use Exception;
use LaravelFCM\Response\Exceptions\UnauthorizedRequestException;
use PDOException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException as CoreValidationException;
use Predis\Cluster\Distributor\EmptyRingException;
use Predis\Connection\ConnectionException;
use Predis\Response\ServerException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Exceptions\ResourseNotFoundException;
use App\Exceptions\PermissionException;
use Throwable;
use Tobuli\Exceptions\ValidationException;

use Bugsnag\BugsnagLaravel\Facades\Bugsnag as Bugsnag;


class Handler extends ExceptionHandler {

	/**
	 * A list of the exception types that should not be reported.
	 *
     * @var array<int, class-string<Throwable>>
	 */
    protected $dontReport = [
        HttpException::class,
        ModelNotFoundException::class,
        AuthorizationException::class,

        ResourseNotFoundException::class,
        PermissionException::class,
        DeviceLimitException::class,
        DemoAccountException::class,
        ValidationException::class,
        CoreValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  Throwable  $e
     * @return void
     * @throws Throwable
     */
	public function report(Throwable $e)
	{
        if ($this->reportBugsnag($e))
            return;

        if (app()->environment() != 'local')
            return;

        parent::report($e);
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
	public function render($request, Throwable $e)
	{
        if ($this->isApiCall($request))
            return $this->renderAPI($request, $e);

        if ($request->ajax())
            return $this->renderAjax($request, $e);

		return parent::render($request, $e);
	}

    public function isApiCall($request)
    {
        return $request->is('api/*');
    }

    public function renderAPI($request, Throwable $e)
    {
        return $this->renderJson($request, $e);
    }

    public function renderAjax($request, Throwable $e)
    {
        if ($request->wantsJson())
            return $this->renderJson($request, $e);

        $response = $this->getResponse($e);

        return response(view("front::Layouts.partials.modal_warning", [
            'message' => $response['message'],
            'type' => 'danger'
        ]), $response['statusCode']);
    }
    public function renderJson($request, Throwable $e)
    {
        // Define the response
        $response = $this->getResponse($e);

        $response['status'] = 0;
        $response['errors'] = ['id' => $response['message']];

        if ($e instanceof ValidationException)
            $response['errors'] = $e->getErrors();

        if ($e instanceof CoreValidationException)
            $response['errors'] = $e->errors();

        if ($response['statusCode'] == 403) {
            $response['perm'] = 0;
        }

        // Return a JSON response with the response array and status code
        return response()->json($response, $response['statusCode']);
    }

    public function reportBugsnag(Throwable $e)
    {
        if ( ! app()->bound('bugsnag'))
            return false;

        if (app()->environment() == 'local')
            return false;

        if ($this->shouldntReport($e))
            return false;

        if ($e instanceof ConnectionException)
            return false;
        if ($e instanceof ServerException)
            return false;
        if ($e instanceof \RedisException)
            return false;

        if ($e instanceof \UnexpectedValueException)
            return false;

        if($e instanceof \Swift_TransportException)
            return false;

        if ($e instanceof UnauthorizedRequestException)
            return false;

        if ($e instanceof PDOException && $e->getCode() == 2002)
            return false;

        Bugsnag::setAppVersion(config('tobuli.version'));
        Bugsnag::setMetaData([
            'host' => [
                'ip'   => \CustomFacades\Server::ip(),
                'name' => config('app.server'),
                'build' => config('app.build'),
            ]
        ]);
        Bugsnag::notifyException($e);

        return true;
    }

    protected function getResponse(Throwable $e)
    {
        switch(true) {
            case $e instanceof NotFoundHttpException:
                $message = 'The endpoint you are looking for could not be found.';
                break;
            case $e instanceof ValidationException:
                $message = implode(' ', $e->getErrors()->all());
                break;
            case $e instanceof CoreValidationException:
                $message = implode(' ', (new ValidationException($e->errors()))->getErrors()->all());
                break;
            case $e instanceof AuthorizationException:
            case $e instanceof ResourseNotFoundException:
            case $e instanceof PermissionException:
            case $e instanceof DemoAccountException:
            case $e instanceof DeviceLimitException:
                $message = $e->getMessage();
                break;

            case config('app.debug'):
                $message = $e->getMessage() . ' ' .  $e->getFile() . ' ' . $e->getLine();
                break;

            default:
                $message = 'Whoops, looks like something went wrong.';
        }

        $status = $this->isHttpException($e) ? $e->getStatusCode() : 400;

        return [
            'statusCode'  => $status,
            'message'     => $message,
        ];
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, \Illuminate\Auth\AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest('login');
    }

    /**
     * Convert a validation exception into a JSON response. (to keep old error format)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Validation\ValidationException  $exception
     * @return \Illuminate\Http\JsonResponse
     */
    protected function invalidJson($request, \Illuminate\Validation\ValidationException $exception)
    {
        return response()->json($exception->errors(), $exception->status);
    }

}
