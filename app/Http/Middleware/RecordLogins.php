<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use App\Models\LoginLog;

class RecordLogins
{

    /**
     * This middleware creates a log entry for every token verification
     * If an entry exisits for the user for that day, the entry is updated
     */

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $source = 'app')
    {
        $period = [ (new \DateTime())->format("Y-m-d")." 00:00:00", (new \DateTime())->format("Y-m-d")." 23:59:59" ]; // time period to persist records (default is a day)

        $user = $request->user();
        if (!is_null($user)) {
            // authenticated user
            $thisLog = LoginLog::where('user_id', '=', $user->id)->whereBetween('updated_at', $period)->first();
            if (!is_null($thisLog)) {
                // record exists
                $thisLog['source'] = $source;
                $thisLog['updated_at'] = new \DateTime();
                $thisLog->save();
            } else {
                LoginLog::create([
                    'user_id' => $user->id,
                    'source' => $source
                ]);
            }
        } else {
            // not authenticated
        }
        return $next($request);
    }
}
