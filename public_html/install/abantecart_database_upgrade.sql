alter table `ac_global_attributes_values`
    add price_modifier float default 0.0 null after attribute_id;

alter table `ac_global_attributes_values`
    add price_prefix char(1) null after price_modifier;

