<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Bug Eloquent Model
 *
 * @property int $id
 * @property int $status ステータス
 * @property string $summary 内容
 * @property int $reported_by 報告者ID
 * @property string $reported_at 報告日時
 * @property int $created_by 登録者ID
 * @property \Illuminate\Support\Carbon $created_at 登録日時
 * @property int $updated_by 更新者ID
 * @property \Illuminate\Support\Carbon $updated_at 更新日時
 * @property int|null $deleted_by 削除者ID
 * @property \Illuminate\Support\Carbon|null $deleted_at 削除日時
 */
class Bug extends Model
{
    use HasFactory;
}
