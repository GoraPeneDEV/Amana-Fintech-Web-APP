<?php
namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class InvestTime extends Model {
    use GlobalStatus;

    public function exportColumns(): array {
        return [
            'name'            => [
                'name'     => "Name",
                'callback' => function ($item) {
                    return $item->name;
                },
            ],
            'time_id'         => [
                'name'     => "Time",
                'callback' => function ($item) {
                    return $item->time;
                },
            ],
            'status'          => [
                'name'     => "Status",
                'callback' => function ($item) {
                    return $item->status ? 'Enable' : 'Disable';
                },
            ],

        ];
    }

    public function plans() {
        return $this->hasMany(InvestPlan::class, 'time_id');
    }

    public function statusBadge(): Attribute {
        return new Attribute(
            function () {
                $html = '';
                if ($this->featured == Status::DISABLE) {
                    $html = '<span class="badge badge--warning">' . trans('Disable') . '</span>';
                } elseif ($this->featured == Status::ENABLE) {
                    $html = '<span class="badge badge--success">' . trans('Active') . '</span>';
                }
                return $html;
            }
        );
    }
}
