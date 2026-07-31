<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;

abstract class BaseController extends Controller
{
    use ApiResponser;
}
