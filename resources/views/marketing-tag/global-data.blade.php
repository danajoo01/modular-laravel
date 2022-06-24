<script type="text/javascript">
    <?php $user = \Auth::user(); ?>
    var mydata336CC993E54D = {
        customer_id     : @if(!empty($user->customer_id)) '{{ $user->customer_id }}' @else '' @endif,
        customer_fname  : @if(!empty($user->customer_fname)) '{{ $user->customer_fname }}' @else '' @endif,
        customer_lname  : @if(!empty($user->customer_lname)) '{{ $user->customer_lname }}' @else '' @endif,
        customer_email  : @if(!empty($user->customer_email)) '{{ $user->customer_email }}' @else '' @endif,
    }

    @if(!empty($catalogs))
        <?php
            $impression_data = [];
            $ChildId_gtm     = []; 
            $ChildName_gtm   = [];
            $brandID_gtm     = [];
            $brandName_gtm   = [];
            $counter         = 0;
            $index           = 1;
        ?>
    var catalog_data930ad9 = [
        @foreach($catalogs as $row)
            <?php
                if(isset($row->isBannerCatalog) && $row->isBannerCatalog == TRUE){
                    ?>                    
                    @continue    
                    <?php    
                }
                $catalog_ids[] = $row->pid;
                
                
                $impression_data[$counter]['id']   = $row->pid;
                $impression_data[$counter]['name'] = $row->product_name;
                $impression_data[$counter]['list'] = !empty($ref) ?  $ref : '';

                $brand_name = str_replace("'", "\\", $row->brand_name);
                $product_name = str_replace("'", "\\", $row->product_name);
                
                //additional for gtm                
                $arrCat = array_filter(explode(',' , $row->front_end_type));
                if(isset($arrCat)){
                    $ChildId_gtm[] = array_values(array_slice($arrCat, -1))[0];
                }
                
                $arrCatName = array_filter(explode(',' , $row->url_set));
                if(isset($arrCatName)){
                    $ChildName_gtm[] = array_values(array_slice($arrCatName, -1))[0];
                }
                
                $brandID_gtm[] = $row->brand_id;
                $brandName_gtm[] = $row->brand_name;
                
            ?>
            {
                pid      : '{!! $row->pid !!}',
                name     : '{!! $product_name !!}',
                category : '{!! $row->url_set !!}',
                brand    : '{!! $brand_name !!}',
                list     : '{!! !empty($ref) ?  $ref : '' !!}',
                position : {{ $index }}
            },
            <?php
                $counter++;
                $index++;
            ?>
        @endforeach
    ];

    var impression_data0192e3 = {!! json_encode($impression_data); !!};
    var criteo_data0192e3     = {!! json_encode($catalog_ids); !!};
    
    
    var childCatIdGTMList_data0192e3 = {!! json_encode($ChildId_gtm); !!};    
    var childCatNameGTMList_data0192e3 = {!! json_encode($ChildName_gtm); !!};
    
    var brandIdGTMList_data0192e3 = {!! json_encode($brandID_gtm); !!};
    var brandNameGTMList_data0192e3 = {!! json_encode($brandName_gtm); !!};
    @endif

</script> 
