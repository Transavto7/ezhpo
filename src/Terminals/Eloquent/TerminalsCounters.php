<?php
declare(strict_types=1);

namespace Src\Terminals\Eloquent;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property int $terminal_id
 * @property int $count
 * @property string $type
 */
final class TerminalsCounters extends Model
{
    use HasTimestamps, HasUuid;

    protected $table = 'terminals_counters';

    protected $guarded = ['id'];
}
