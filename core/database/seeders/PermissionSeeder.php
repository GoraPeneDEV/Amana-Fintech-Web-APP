<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $permissions = [
            "send money"        => [
                "view send money",
                "manage send money charge",
            ],
            "cash out"          => [
                "view cash out",
                "manage cash out charge",
            ],
            "cash in"           => [
                "view cash in",
                "manage cash in charge",
            ],
            "investment"        => [
                "view investment",
                "manage investment plan",
                "manage investment time",
            ],
            "payment"           => [
                "view payment",
                "manage payment charge",
            ],
            "bank transfer"     => [
                "view bank transfer",
                "manage bank transfer bank",
                "manage bank transfer charge",
                "approve bank transfer",
                "reject bank transfer",
            ],
            "microfinance"      => [
                "view microfinance",
                "manage microfinance ngo",
                "manage microfinance charge",
                "approve microfinance",
                "reject microfinance",
            ],
            "mobile recharge"   => [
                "view mobile recharge",
                "manage mobile operator",
                "manage mobile recharge charge",
                "approve mobile recharge",
                "reject mobile recharge",
            ],
            "airtime"           => [
                "view airtime",
                "manage airtime operator",
            ],
            "utility bill"      => [
                "view utility bill",
                "manage utility bill charge",
                "manage bill category",
                "manage bill company",
                "approve utility bill",
                "reject utility bill",
            ],
            "gift card"         => [
                "manage gift card category",
                "manage gift card",
                "view gift card purchases",
            ],
            "education fee"     => [
                "view education fee",
                "manage education fee charge",
                "manage institution",
                "manage institution category",
                "approve education fee",
                "reject education fee",
            ],
            "donation"          => [
                "view donation",
                "manage charity",
            ],
            "virtual card"      => [
                "view virtual card",
                "configure virtual card provider",
                "manage virtual card charge",
            ],
            "request money"     => [
                "view money requests",
            ],
            "manage user"       => [
                "view users",
                "send user notification",
                "view user notifications",
                "update user balance",
                "ban user",
                "login as user",
                "update user",
            ],
            "manage agent"      => [
                "view agents",
                "send agent notification",
                "view agent notifications",
                "update agent balance",
                "ban agent",
                "login as agent",
                "update agent",
                "verify agent kyc",
            ],
            "manage merchant"   => [
                "view merchants",
                "send merchant notification",
                "view merchant notifications",
                "update merchant balance",
                "ban merchant",
                "login as merchant",
                "update merchant",
                "verify merchant kyc",
            ],
            "promotion"         => [
                "manage banners",
                "manage offers",
            ],
            "admin"             => [
                "view admin",
                "add admin",
                "edit admin",
            ],
            "user add money"    => [
                "view user add money",
                "approve user add money",
                "reject user add money",
            ],
            "agent add money"   => [
                "view agent add money",
                "approve agent add money",
                "reject agent add money",
            ],
            "agent withdraw"    => [
                "view agent withdraw",
                "approve agent withdraw",
                "reject agent withdraw",
            ],
            "merchant withdraw" => [
                "view merchant withdraw",
                "approve merchant withdraw",
                "reject merchant withdraw",
            ],
            "role"              => [
                "view roles",
                "add role",
                "edit role",
                "assign permissions",
            ],
            "gateway"           => [
                "manage gateways",
                "manage withdraw methods",
            ],
            "setting"           => [
                "update general settings",
                "update brand settings",
                "system configuration",
                "notification settings",
                "module settings",
                "manage reloadly api",
                "country settings",
                "manage qr code",
                "update maintenance mode",
                "security settings",
                "seo settings",
            ],
            "report"            => [
                "view all transactions",
                "view user transactions",
                "view agent transactions",
                "view merchant transactions",
                "view login history",
                "view all notifications",
            ],
            "support ticket"    => [
                "view user tickets",
                "view agent tickets",
                "view merchant tickets",
                "answer tickets",
                "close tickets",
            ],
            "manage content"    => [
                "manage pages",
                "manage sections",
            ],
            "other"             => [
                "view dashboard",
                "manage extensions",
                "manage languages",
                "manage subscribers",
                "view application info",
                "manage cron job",
            ],
        ];

        foreach ($permissions as $k => $permission) {
            foreach ($permission as $item) {
                $exists = Permission::where("name", $item)->where('group_name', $k)->exists();
                if ($exists) {
                    continue;
                }

                $permission             = new Permission();
                $permission->name       = $item;
                $permission->group_name = $k;
                $permission->guard_name = "admin";
                $permission->save();
            }
        }
    }
}
