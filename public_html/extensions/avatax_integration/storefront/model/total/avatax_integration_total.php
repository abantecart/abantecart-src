<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2025 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */

class ModelTotalAvataxIntegrationTotal extends Model
{
    public function getTotal(&$total_data, &$total, &$taxes, &$customerData)
    {
        if (!$this->config->get('avatax_integration_status')
            || !$this->config->get('avatax_integration_total_status')
        ) {
            return;
        }

        if ($this->request->get_or_post('order_id')) {
            $customerData['order_id'] = (int)$this->request->get_or_post('order_id');
        }

        $avataxExtension = new ExtensionAvataxIntegration();
        $tax_amount = $avataxExtension->getTax($this, $customerData, false, $total_data);

        if ($tax_amount >= 0) {
            $total_data[] = [
                'id'         => 'avatax_integration_total',
                'title'      => $this->config->get('avatax_integration_tax_name'),
                'text'       => $this->currency->format($tax_amount, $customerData['currency']),
                'value'      => $tax_amount,
                'sort_order' => $this->config->get('avatax_integration_total_sort_order'),
                'total_type' => $this->config->get('avatax_integration_total_total_type'),
            ];
            $total += $tax_amount;
        }
    }

    public function getTaxLines(){
        $session = $this->session->data['fc'] ?: $this->session->data;
        return $session['avatax']['getTaxLines'] ?? [];
    }
}
