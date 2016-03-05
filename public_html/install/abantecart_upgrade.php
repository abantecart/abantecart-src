<?php

// add new media page menu item
$rm = new AResourceManager();
$rm->setType('image');
$language_id = $this->language->getContentLanguageID();

$data = array();
$data['resource_code'] = '<i class="fa fa-photo"></i>&nbsp;';
$data['name'] = array($language_id => 'Menu Icon Media Page');
$data['title'] = array($language_id => '');
$data['description'] = array($language_id => '');
$resource_id = $rm->addResource($data);

$menu = new AMenu ( "admin" );
$menu->insertMenuItem ( array (  "item_id" => "rl_manager",
								 "parent_id"=>"catalog",
								 "item_text" => "text_rl_manager",
								 "item_url" => "tool/rl_manager",
								 "item_icon_rl_id" => $resource_id,
								 "item_type"=>"core",
								 "sort_order"=>"8")
								);

$data = array();
$data['resource_code'] = '<i class="fa fa-bullhorn"></i>&nbsp;';
$data['name'] = array($language_id => 'Icon Settings IM');
$data['title'] = array($language_id => '');
$data['description'] = array($language_id => '');
$resource_id = $rm->addResource($data);

$menu->insertMenuItem ( array (  "item_id" => "settings_im",
								 "parent_id"=>"settings",
								 "item_text" => "text_settings_im",
								 "item_url" => "setting/setting/im",
								 "item_icon_rl_id" => $resource_id,
								 "item_type"=>"core",
								 "sort_order"=>"7")
								);

//update indexes for task_steps table

//make all step_id values unique
$sql = "SELECT max(step_id) as max FROM ".$this->db->table('task_steps');
$result = $this->db->query($sql);
$max_id = (int)$result->row['max']+1;


$result = $this->db->query("SELECT step_id, count(task_id) as cnt
							FROM ".$this->db->table('task_steps')."
							GROUP BY step_id
							HAVING count(task_id)>1;");

foreach($result->rows as $row){
	$sql = "SELECT task_id
			FROM ".$this->db->table('task_steps')."
			WHERE step_id='".$row['step_id']."';";
	$r = $this->db->query($sql);
	foreach($r->rows as $task){

		$sql = "UPDATE ".$this->db->table('task_steps')."
				SET step_id = '".$max_id."'
				WHERE task_id='".$task['task_id']."';";
		$this->db->query($sql);
		$max_id++;
	}
}

//update indexes
$sql = "ALTER TABLE `".$this->db->table('task_steps')."`
		DROP PRIMARY KEY,
		ADD PRIMARY KEY (`step_id`),
		ADD INDEX `task_steps_idx` (`task_id` ASC)";
$this->db->query($sql);
