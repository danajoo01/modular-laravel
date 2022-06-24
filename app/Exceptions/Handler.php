<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Validation\ValidationException;
use Illuminate\Contracts\Routing\Middleware;
use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

use LERN;

// use Tylercd100\LERN\Facades\LERN;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
        'Illuminate\Session\TokenMismatchException',
        'Laravel\Socialite\Two\InvalidStateException',
        'GuzzleHttp\Exception\ClientException'
    ];

    protected $app;

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        if(getSlackEnv() == 1) {
            if ($this->shouldReport($e)) {
            //Check to see if LERN is installed otherwise you will not get an exception.
                if (app()->bound("lern")) {
                    app()->make("lern")->handle($e); //Record and Notify the Exception

                    /*
                    OR...
                    app()->make("lern")->record($e); //Record the Exception to the database
                    app()->make("lern")->notify($e); //Notify the Exception
                    */
                }
            }
        }

        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        $message = ucwords('Kami Mohon Maaf,<br>Terjadi Kesalahan Pada Sistem Kami.');

        if ($e instanceof \ErrorException) {
            \Log::error('ErrorException on URL Shopdeca : ' . \Request::fullUrl());
        }

        $get_domain = get_domain();
        
        switch ($get_domain['channel']) {
            case '1':
            case '3':
            case '5':
                $folder = "desktop";
                break;
            case '2':
            case '4':
            case '6':                
                $folder = "mobile";
                break;
        }

        if ($e->getMessage() == "soldout") {
            $message = ucwords('Kami Mohon Maaf,<br>Produk Yang Anda Cari Sudah Habis Terjual.');
        }
        

        if($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException)
        {
            $message = ucwords('Kami Mohon Maaf,<br>Halaman yang anda cari tidak ditemukan.');

            $get_domain = get_domain();
            $channel    = $get_domain['channel'];
            $domain     = $get_domain['domain'];
            $domain_id  = $get_domain['domain_id'];

            $fetch_url_redirect = \DB::table('redirect_url')
                                    ->where('short_url', '=', \Request::segment(1))
                                    ->where('category', '=', $domain_id)
                                    ->where('deleted_flag', '=', 2)
                                    ->where('status', '=', 1)
                                    ->first();

            if(isset($fetch_url_redirect->long_url))
            {
                //redirect to landing page                                    
                return \Redirect::to($fetch_url_redirect->long_url);
            }

            return response()->view('errors.'.$get_domain['domain_name'].'.'.$folder.'.404', [ 'message' => $message ], 404);
        }

        if ($e instanceof \Illuminate\Session\TokenMismatchException) {
            // return redirect('/')->withErrors(['token_error' => 'Sorry, your session seems to have expired. Please try again.']);

            \Log::warning('Token Mismatch on URL : ' . \Request::fullUrl());
            \Log::warning($e);
            if(\Request::ajax() == false){
              return redirect('/login')->with('login_error', 'Session anda telah habis. Mohon coba login kembali')->withInput();
            }
        }

        if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException && $e->getStatusCode() == 503) {
            //return response()->view('errors.503', [ 'message' => $message ], 503);
            return response()->view('errors.'.$get_domain['domain_name'].'.'.$folder.'.503', [ 'message' => $message ], 503);
        }
        
        if ($e instanceof \Symfony\Component\Process\Exception\RuntimeException) {
            \Log::warning('RuntimeException on URL : ' . \Request::fullUrl());
            return response()->view('errors.'.$get_domain['domain_name'].'.'.$folder.'.404', [ 'message' => $message ], 404);
        }

        if ($e instanceof \Symfony\Component\Debug\Exception\FatalErrorException) {
            \Log::warning('FatalErrorException on URL : ' . \Request::fullUrl());
            return response()->view('errors.'.$get_domain['domain_name'].'.'.$folder.'.404', [ 'message' => $message ], 404);
        }

        /* Exception for maintenance mode */
        // if($e->getStatusCode() == 503) {
        //     return parent::render($request, $e);
        // }

        if(\Request::segment(2) == 'insert_order_process') return parent::render($request, $e);

        return response()->view('errors.'.$get_domain['domain_name'].'.'.$folder.'.404', [ 'message' => $message ], 404);
    }
}
