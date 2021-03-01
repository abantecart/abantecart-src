<?php
$menu = new AMenu ("admin");
$rm = new AResourceManager();
$rm->setType('image');
$language_id = 1;

$menuItem = $menu->getMenuItem('email_templates');
if (!$menuItem) {
    $data = [];
    $data['resource_code'] = '<i class="fa fa-envelope-open-o"></i>&nbsp;';
    $data['name'] = [$language_id => 'Email templates'];
    $data['title'] = [$language_id => ''];
    $data['description'] = [$language_id => ''];
    $resource_id = $rm->addResource($data);

    $menu->insertMenuItem([
            "item_id"         => "email_templates",
            "parent_id"       => "design",
            "item_text"       => "email_templates",
            "item_url"        => "design/email_templates",
            "item_icon_rl_id" => $resource_id,
            "item_type"       => "core",
            "sort_order"      => "8",
        ]
    );
}

$menuItem = $menu->getMenuItem('collections');
if (!$menuItem) {
    $data = [];
    $data['resource_code'] = '<i class="fa fa-paste"></i>&nbsp;';
    $data['name'] = [$language_id => 'Collections'];
    $data['title'] = [$language_id => ''];
    $data['description'] = [$language_id => ''];
    $resource_id = $rm->addResource($data);

    $menu->insertMenuItem([
            "item_id"         => "collections",
            "parent_id"       => "catalog",
            "item_text"       => "text_collection",
            "item_url"        => "catalog/collections",
            "item_icon_rl_id" => $resource_id,
            "item_type"       => "core",
            "sort_order"      => "8",
        ]
    );
}

//Default stripe settings changes
if($this->config->get('default_stripe_status') && $this->config->get('default_stripe_access_token'))
{
    /** @var ModelSettingSetting $mdl */
    $mdl = $this->load->model('setting/setting');
    $mdl->editSetting(
        'default_stripe',
        [
            'default_stripe_pk_live' => $this->config->get('default_stripe_published_key'),
            'default_stripe_sk_live' => $this->config->get('default_stripe_access_token'),
            'default_stripe_pk_test' => $this->config->get('default_stripe_published_key'),
            'default_stripe_sk_test' => $this->config->get('default_stripe_access_token'),
        ]
    );
}


