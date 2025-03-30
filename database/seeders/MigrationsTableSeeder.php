<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigrationsTableSeeder extends Seeder
{
    public function run()
    {
        $migrations = [
            ['id' => 1, 'migration' => 'create_users_table', 'batch' => 1],
            ['id' => 2, 'migration' => 'create_cache_table', 'batch' => 1],
            ['id' => 3, 'migration' => 'create_jobs_table', 'batch' => 1],
            ['id' => 4, 'migration' => 'create_banks_table', 'batch' => 1],
            ['id' => 5, 'migration' => 'create_bank_transactions_table', 'batch' => 1],
            ['id' => 6, 'migration' => 'create_actions_table', 'batch' => 1],
            ['id' => 7, 'migration' => 'create_adjustments_table', 'batch' => 1],
            ['id' => 8, 'migration' => 'create_adjustment_details_table', 'batch' => 1],
            ['id' => 9, 'migration' => 'create_admins_table', 'batch' => 1],
            ['id' => 10, 'migration' => 'create_brands_table', 'batch' => 1],
            ['id' => 11, 'migration' => 'create_categories_table', 'batch' => 1],
            ['id' => 12, 'migration' => 'create_customers_table', 'batch' => 1],
            ['id' => 13, 'migration' => 'create_customer_payments_table', 'batch' => 1],
            ['id' => 14, 'migration' => 'create_expenses_table', 'batch' => 1],
            ['id' => 15, 'migration' => 'create_expense_types_table', 'batch' => 1],
            ['id' => 16, 'migration' => 'create_products_table', 'batch' => 1],
            ['id' => 17, 'migration' => 'create_product_stocks_table', 'batch' => 1],
            ['id' => 18, 'migration' => 'create_roles_table', 'batch' => 1],
            ['id' => 19, 'migration' => 'create_sales_table', 'batch' => 1],
            ['id' => 20, 'migration' => 'create_sale_details_table', 'batch' => 1],
            ['id' => 21, 'migration' => 'create_suppliers_table', 'batch' => 1],
            ['id' => 22, 'migration' => 'create_supplier_payments_table', 'batch' => 1],
            ['id' => 23, 'migration' => 'create_transfers_table', 'batch' => 1],
            ['id' => 24, 'migration' => 'create_transfer_details_table', 'batch' => 1],
            ['id' => 25, 'migration' => 'create_units_table', 'batch' => 1],
            ['id' => 26, 'migration' => 'create_warehouses_table', 'batch' => 1],
            ['id' => 27, 'migration' => 'create_admin_notifications_table', 'batch' => 1],
            ['id' => 28, 'migration' => 'create_admin_password_resets_table', 'batch' => 1],
            ['id' => 29, 'migration' => 'create_cache_locks_table', 'batch' => 1],
            ['id' => 30, 'migration' => 'create_failed_jobs_table', 'batch' => 1],
            ['id' => 31, 'migration' => 'create_general_settings_table', 'batch' => 1],
            ['id' => 32, 'migration' => 'create_job_batches_table', 'batch' => 1],
            ['id' => 33, 'migration' => 'create_migrations_table', 'batch' => 1],
            ['id' => 34, 'migration' => 'create_notification_logs_table', 'batch' => 1],
            ['id' => 35, 'migration' => 'create_notification_templates_table', 'batch' => 1],
            ['id' => 36, 'migration' => 'create_password_resets_table', 'batch' => 1],
            ['id' => 37, 'migration' => 'create_password_reset_tokens_table', 'batch' => 1],
            ['id' => 38, 'migration' => 'create_permissions_table', 'batch' => 1],
            ['id' => 39, 'migration' => 'create_permission_role_table', 'batch' => 1],
            ['id' => 40, 'migration' => 'create_purchase_details_table', 'batch' => 1],
            ['id' => 41, 'migration' => 'create_purchases_table', 'batch' => 1],
            ['id' => 42, 'migration' => 'create_purchase_returns_table', 'batch' => 1],
            ['id' => 43, 'migration' => 'create_purchase_return_details_table', 'batch' => 1],
            ['id' => 44, 'migration' => 'create_sale_returns_table', 'batch' => 1],
            ['id' => 45, 'migration' => 'create_sale_return_details_table', 'batch' => 1],
            ['id' => 46, 'migration' => 'create_sessions_table', 'batch' => 1],
            ['id' => 47, 'migration' => 'create_update_logs_table', 'batch' => 1],
            ['id' => 48, 'migration' => 'create_extensions_table', 'batch' => 1],
        ];

        foreach ($migrations as $migration) {
            DB::table('migrations')->updateOrInsert(
                ['id' => $migration['id']],
                $migration
            );
        }
    }
}
