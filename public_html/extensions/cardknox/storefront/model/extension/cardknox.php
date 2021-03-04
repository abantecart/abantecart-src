<?php


if (! defined ( 'DIR_CORE' )) {
 header ( 'Location: static_pages/' );
}

class ModelExtensionCardknox extends Model {
    public function getMethod($address)
    {
        $this->load->language('cardknox/cardknox');

        if ($this->config->get('cardknox_status')) {
            $sql = "SELECT *
                    FROM ".$this->db->table('zones_to_locations')."
                    WHERE location_id = '".(int)$this->config->get('cardknox_location_id')."'
                           AND country_id = '".(int)$address['country_id']."'
                           AND (zone_id = '".(int)$address['zone_id']."' OR zone_id = '0')";
            $query = $this->db->query($sql);

            if (!$this->config->get('cardknox_location_id')) {
                $status = true;
            } elseif ($query->num_rows) {
                $status = true;
            } else {
                $status = false;
            }
        } else {
            $status = false;
        }

        $method_data = array();

        if ($status) {
            $method_data = array(
                'id'         => 'cardknox',
                'title'      => $this->language->get('text_title'),
                'sort_order' => $this->config->get('cardknox_sort_order'),
            );
        }

        return $method_data;
    }

    public function getCreditCardTypes()
    {
        return array(
            'Visa'       => 'Visa',
            'MasterCard' => 'MasterCard',
            'Discover'   => 'Discover',
            'Amex'       => 'American Express',
        );
    }
 }
