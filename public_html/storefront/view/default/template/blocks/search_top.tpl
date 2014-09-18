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
    	$categories = $this->cache->get('category.list.-1.0', (int)$this->config->get('storefront_language_id'), (int)$this->config->get('config_store_id'));
    	if($categories){
    		array_unshift($categories, array('category_id'=>0, 'name'=>'All Categories', 'parent_id'=>0));
    ?>
    	<ul class="dropdown dropdown-menu col-md-2 noclose">
    		<!-- dropdown menu links -->
    		<li class="active"><a id="category_selected"><?php echo $categories[0]['name']?></a></li>
    		<li class="divider"></li>
			<li>
				<ul id="search-category">
				<?php foreach($categories as $category){
					if($category['parent_id']>0){ continue;} ?>
					<li><a id="category_<?php echo $category['category_id']?>"><?php echo $category['name']?></a></li>
				<?php } ?>
				</ul>
			</li>
    	</ul>
    <?php } ?>
    </div>
</form>