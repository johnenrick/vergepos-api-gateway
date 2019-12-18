<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TransactionVirtualColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE VIEW transaction_computations AS select transaction_id as id,transaction_id, SUM(vat_sales) as total_vat_sales, SUM(vat_exempt_sales) as total_vat_exempt_sales, SUM(vat_zero_rated_sales) as total_vat_zero_rated_sales, SUM(vat_amount) as total_vat_amount, SUM(discount_amount) as total_discount_amount from transaction_products GROUP BY transaction_id");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      DB::statement("DROP VIEW transaction_computations");
    }
}
