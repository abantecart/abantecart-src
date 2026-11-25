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

class ModelCatalogContent extends Model
{
    /**
     * @return array
     */
    public function getContents()
    {
        $contents = array();
        $content_manager = new AContentManager();
        $results = $content_manager->getContents();
        $contents[] = array(
            'content_id' => '0',
            'title'      => $this->language->get('text_none'),
        );
        foreach ($results as $r) {
            $contents[] = array(
                'content_id' => $r['content_id'],
                'title'      => $r['title'],
            );
        }
        return $contents;
    }

    /**
     * Returns content pages which contain a link to the product
     *
     * @param int $productId
     * @param array $keywords - SEO keywords of product
     * @param int|null $storeId
     *
     * @return array
     * @throws AException
     */
    public function getContentsMentioningProduct(int $productId, array $keywords, ?int $storeId = null)
    {
        /** @var ModelCatalogProduct $mdl */
        $mdl = $this->load->model('catalog/product');
        $languageId = (int) $this->language->getContentLanguageID();
        $sql = "SELECT c.content_id, c.parent_content_id, cd.language_id, cd.title, cd.content, cd.description
                FROM " . $this->db->table('contents') . " c
                INNER JOIN " . $this->db->table('content_descriptions') . " cd
                    ON cd.content_id = c.content_id ";
        if ($storeId !== null) {
            $sql .= " INNER JOIN " . $this->db->table('contents_to_stores') . " c2s
                        ON (c2s.content_id = c.content_id AND c2s.store_id = " . $storeId . ")";
        }
        $sql .= " WHERE " . $mdl->getProductMentionSqlCondition(['cd.content', 'cd.description'], $productId, $keywords)
            . " ORDER BY c.content_id, (cd.language_id = " . $languageId . ") DESC";
        $result = $this->db->query($sql);

        $output = [];
        foreach ($result->rows as $row) {
            $id = (int) $row['content_id'];
            if (isset($output[$id])) {
                continue;
            }
            if ($mdl->isProductMentionedInText($row['content'], $productId, $keywords)
                || $mdl->isProductMentionedInText($row['description'], $productId, $keywords)
            ) {
                $output[$id] = [
                    'content_id'        => $id,
                    'parent_content_id' => (int) $row['parent_content_id'],
                    'title'             => $row['title'],
                ];
            }
        }
        return array_values($output);
    }
}
