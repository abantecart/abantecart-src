<form id="search_form" class="form-search top-search">
    <input  type="hidden" name="filter_category_id" id="filter_category_id" value="0"/>
    <div class="btn-group search-bar">
    	<input type="text"
			   id="filter_keyword"
			   name="filter_keyword"
			   autocomplete="off"
    		   class="pull-left input-medium search-query dropdown-toggle"
			   placeholder="<?php echo $text_keyword; ?>"
			   value=""
    		   data-toggle="dropdown"/>
    	 <div class="button-in-search" title="<?php echo $button_go; ?>"><i class="fa fa-search"></i></div>
    <?php
    	if($top_categories){
    		array_unshift($top_categories, array('category_id' => 0, 'name' => $text_category, 'parent_id' => 0));
    ?>
    	<ul  id="search-category" class="dropdown dropdown-menu col-md-2 noclose">
    		<li class="active"><a id="category_selected"><?php echo $top_categories[0]['name']?></a></li>
    		<li class="divider"></li>
    		<?php foreach($top_categories as $category){
				if($category['parent_id'] > 0){ continue;} ?>
    			<li class="search-category">
				    <a id="category_<?php echo $category['category_id']?>"><?php echo $category['name']?></a>
			    </li>
    		<?php } ?>
    	</ul>
    <?php } ?>
    </div>
</form>