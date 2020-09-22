<?php

namespace StefanBauer\TailwindcssBreakpointDetector\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use StefanBauer\TailwindcssBreakpointDetector\TailwindcssBreakpointDetector;

class InjectDetector
{
    private TailwindcssBreakpointDetector $detector;

    public function __construct(TailwindcssBreakpointDetector $detector)
    {
        $this->detector = $detector;
    }

    public function handle(Request $request, \Closure $next)
    {
        if (! $this->detector->isEnabled()) {
            return $next($request);
        }

        try {
            /** @var \Illuminate\Http\Response $response */
            $response = $next($request);
        } catch (\Exception $e) {
            // no-op.
        }

        // Inject the detector when returning an HTTP response
        if ($response instanceof Response) {
            $this->detector->injectDetector($request, $response);
        }

        return $response;
    }
}
