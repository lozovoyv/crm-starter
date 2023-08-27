<?php
/*
 * This file is part of Opxx Starter project
 *
 * (c) Viacheslav Lozovoy <vialoz@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

/*
 * This file is part of Opxx Starter project
 *
 * (c) Viacheslav Lozovoy <vialoz@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Assets\Dictionary;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * @property int $id
 * @property string $name
 * @property string|null $hint
 * @property bool $enabled
 * @property bool $locked
 * @property int $order
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class TestingEloquentDictionaryModel extends Model
{
    protected $table = 'test_eloquent_dictionary';

    protected $casts = [
        'enabled' => 'bool',
        'locked' => 'bool',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = ['name', 'order', 'value', 'description', 'hint', 'enabled', 'locked'];

    public function getHash(): string
    {
        return 'test hash';
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public static function up(): void
    {
        if (Schema::hasTable('test_eloquent_dictionary')) {
            DB::table('test_eloquent_dictionary')->truncate();
        } else {
            Schema::create('test_eloquent_dictionary', static function (Blueprint $table) {

                $table->unsignedSmallInteger('id', true);

                $table->string('name');
                $table->string('value')->nullable();
                $table->string('description')->nullable();
                $table->string('hint')->nullable();
                $table->boolean('enabled')->default(true);
                $table->boolean('locked')->default(false);

                $table->unsignedSmallInteger('order')->nullable()->default(0);

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public static function down(): void
    {
        Schema::dropIfExists('test_eloquent_dictionary');
    }
}
