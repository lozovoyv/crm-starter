<?php
declare(strict_types=1);

namespace App\Models;

use App\Interfaces\HashCheckable;
use App\Traits\HashCheck;

/**
 * Parent class for all applications models except User.
 */
class Model extends \Illuminate\Database\Eloquent\Model implements HashCheckable
{
    use HashCheck;

    /**
     * Instance hash.
     *
     * @return  string|null
     */
    public function hash(): ?string
    {
        return $this->updated_at?->toString();
    }
}
