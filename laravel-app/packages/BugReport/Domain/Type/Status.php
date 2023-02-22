<?php

namespace BugReport\Domain\Type;

/**
 * バグレポート状態
 */
enum Status: int
{
    case NEW      = 0;
    case READY    = 1;
    case DOING    = 2;
    case PENDING  = 9;
    case DONE     = 10;
    case CLOSED   = 90;
    case CANCELED = 91;

    /**
     * 表示名を返却する
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::NEW      => '新規',
            self::READY    => '未着手',
            self::DOING    => '実行中',
            self::PENDING  => '保留',
            self::DONE     => '処理済',
            self::CLOSED   => '完了',
            self::CANCELED => 'キャンセル',
        };
    }
}
