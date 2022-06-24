<?php namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'tags'; //Define your table name

	protected $primaryKey = 'tag_id'; //Define your primarykey

	public $timestamps = false; //Define yout timestamps

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = ['tag_id']; //Define your guarded columns

	/*
	* Define your relationship with other model
	*/
	// public function relation_name()
	// {
	// 	return $this->belongsTo('App\Modules\Product\Models\Model_name');
	// }
        
        public static function TagsList(){
            $define_domain  = get_domain();
            $domain_id      = ($define_domain['domain_id']) ? $define_domain['domain_id'] : 1;                        
            
            $tags           = Self::select('type.type_name AS category', 'tags.tag_id', 'tags.tag_name', 'tags.tag_url')  
                                ->leftJoin('type' , 'type.type_id', '=' , 'tags.tag_category')
                                ->where('tags.tag_status', '=', 1)
                                ->where('domain_id', '=', $domain_id) 
                                ->orderBy('type.type_name', 'ASC') 
                                ->orderBy('tags.tag_name', 'ASC')  
                                ->get();      
            
            return $tags;
        } 
	
	public static function get_tags($tags) {
            //print_r($tags);
            $tag_name = array();
            foreach ($tags as $tag) {
                $get_tag_name   = Self::where('tag_id', '=', $tag)->first();
                $tag_name[]     = (isset($get_tag_name->tag_name)) ? '<a href="' .('/tag/' . $get_tag_name->tag_url) . '">' . ucwords($get_tag_name->tag_name) . "</a>" : NULL;
            }
            $tag_clean      = array_filter($tag_name);
            $tag_name       = (!empty($tag_clean)) ? implode(',', $tag_clean) : NULL;

                 return $tag_name;
            }
	
	public static function get_tag_solr($tag) {
            $tag_name   = array();
            $name       = explode(",", $tag['tag_name']);
            $url        = explode(",", $tag['tag_url']);
            $i=0;
            foreach ($url as $row) {
                    $tag_name[]     = (isset($name[$i])) ? '<a href="' .('/tag/' . $url[$i]) . '">' . ucwords($name[$i]) . "</a>" : NULL;
                    $i++;
            }	
            $tag_clean              = array_filter($tag_name);
            $tag_name               = (!empty($tag_clean)) ? implode(',', $tag_clean) : NULL;		
            return $tag_name;
	}                

}
