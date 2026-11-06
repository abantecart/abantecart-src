<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

class ControllerResponsesIndexChart extends AController
{
    /** cache lifetime slot in seconds. Data can be stale for up to this period */
    const CACHE_SLOT = 600;

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('common/home');

        $range = $this->request->get['range'] ?? 'month';
        if (!in_array($range, ['day', 'week', 'month', 'year'])) {
            $range = 'month';
        }

        $cacheKey = 'chart.admin.dashboard.' . $range . '.' . $this->language->getLanguageID()
            . '.' . floor(time() / self::CACHE_SLOT);
        $data = $this->cache->pull($cacheKey);
        if ($data === false) {
            $data = $this->buildChartData($range);
            $this->cache->push($cacheKey, $data);
        }
        $this->data['chart'] = $data;

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->load->library('json');
        $this->response->setOutput(AJson::encode($this->data['chart']));
    }

    /**
     * @param string $range
     *
     * @return array
     * @throws AException
     */
    protected function buildChartData(string $range)
    {
        $data = [
            'order'    => [
                'label' => $this->language->get('text_order'),
                'data'  => [],
            ],
            'customer' => [
                'label' => $this->language->get('text_customer'),
                'data'  => [],
            ],
            'xaxis'    => [],
        ];

        $points = [];
        switch ($range) {
            case 'day':
                $dateFrom = date('Y-m-d 00:00:00');
                $dateTo = date('Y-m-d 00:00:00', strtotime('+1 day'));
                $groupBy = 'HOUR(date_added)';
                for ($i = 0; $i < 24; $i++) {
                    $points[$i] = date('H', mktime($i, 0, 0, date('n'), date('j'), date('Y')));
                }
                break;
            case 'week':
                $weekStart = strtotime(date('Y-m-d') . ' -' . date('w') . ' days');
                $dateFrom = date('Y-m-d 00:00:00', $weekStart);
                $dateTo = date('Y-m-d 00:00:00', strtotime('+7 days', $weekStart));
                //0 - Sunday
                $groupBy = 'DATEDIFF(DATE(date_added), \'' . date('Y-m-d', $weekStart) . '\')';
                for ($i = 0; $i < 7; $i++) {
                    $points[$i] = date('D', strtotime('+' . $i . ' days', $weekStart));
                }
                break;
            case 'year':
                $dateFrom = date('Y-01-01 00:00:00');
                $dateTo = (date('Y') + 1) . '-01-01 00:00:00';
                $groupBy = 'MONTH(date_added)';
                for ($i = 1; $i <= 12; $i++) {
                    $points[$i] = date('M', mktime(0, 0, 0, $i, 1, date('Y')));
                }
                break;
            case 'month':
            default:
                $dateFrom = date('Y-m-01 00:00:00');
                $dateTo = date('Y-m-01 00:00:00', strtotime('first day of next month'));
                $groupBy = 'DAY(date_added)';
                for ($i = 1; $i <= date('t'); $i++) {
                    $points[$i] = (string) $i;
                }
                break;
        }

        $orders = $this->getTotals('orders', $groupBy, $dateFrom, $dateTo, "order_status_id > '0'");
        $customers = $this->getTotals('customers', $groupBy, $dateFrom, $dateTo);

        foreach ($points as $i => $label) {
            $data['order']['data'][] = [$i, (int) ($orders[$i] ?? 0)];
            $data['customer']['data'][] = [$i, (int) ($customers[$i] ?? 0)];
            $data['xaxis'][] = [$i, $label];
        }

        return $data;
    }

    /**
     * Returns totals grouped by given expression with single query
     *
     * @param string $table
     * @param string $groupBy
     * @param string $dateFrom
     * @param string $dateTo
     * @param string $extraWhere
     *
     * @return array - [point => total]
     * @throws AException
     */
    protected function getTotals(string $table, string $groupBy, string $dateFrom, string $dateTo, string $extraWhere = '')
    {
        $query = $this->db->query(
            "SELECT " . $groupBy . " AS point, COUNT(*) AS total
            FROM `" . $this->db->table($table) . "`
            WHERE date_added >= '" . $this->db->escape($dateFrom) . "'
                AND date_added < '" . $this->db->escape($dateTo) . "'
                " . ($extraWhere ? " AND " . $extraWhere : "") . "
            GROUP BY point"
        );
        $output = [];
        foreach ($query->rows as $row) {
            $output[(int) $row['point']] = (int) $row['total'];
        }
        return $output;
    }
}
