<?php
declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;

/**
 * Parent class for all applications models except User.
 *
 * @property Carbon|null $updated_at
 */
class Model extends \Illuminate\Database\Eloquent\Model
{

}
