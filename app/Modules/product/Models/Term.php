<?php
namespace App\Modules\Product\Models;
use Illuminate\Database\Eloquent\Model;

class Term extends Model 
{
    // table yang akan digunakan
    protected 	$table 		= 'terms';  //Define your table name
    protected 	$primaryKey = 'id'; //Define your primarykey
	public 		$timestamps = false; //Define yout timestamps
	protected 	$guarded 	= ['id']; //Define your guarded columns
}
 
?>