<?php

class Feeds extends BaseModel {
    public function getSource() {
        return 'feeds';
    }

    public function getProductList() {
        $date = date('Y-m-d');
        $sql_fetch = "
            SELECT 
                p.id as product_id, p.slug, p.title, p.sku, p.price, p.description,
                d.price as discount_price,
                ci.src as coverimage_src,
                i.src as image_src,
                c.name as category_name,
                p.updated_at,
                p.active
            FROM
                CategoryProducts cp
            RIGHT JOIN Categories c on c.id = cp.category_id
            RIGHT JOIN Products p on p.id = cp.product_id
            RIGHT JOIN Images ci on ci.id = p.coverimage_id
            RIGHT JOIN Images i on i.product_id = cp.product_id
            LEFT JOIN Discounts d on (d.active=1 and d.product_id = cp.product_id AND '{$date}' between d.campaign_start and d.campaign_end)
            ORDER BY cp.product_id DESC
        ";
        $query_fetch = new Phalcon\Mvc\Model\Query($sql_fetch, $this->getDI());
        $result_fetch = $query_fetch->execute()->toArray();

        $products = [];

        foreach($result_fetch as $proto_product) {
            if($proto_product['product_id'] == null)
                continue 1;
            if(strpos($proto_product['coverimage_src'], 'http') === FALSE) {
                $proto_product['coverimage_src'] = 'http://bonzoportal.hu'.$proto_product['coverimage_src'];
            }

            if(strpos($proto_product['image_src'], 'http') === FALSE) {
                $proto_product['image_src'] = 'http://bonzoportal.hu'.$proto_product['image_src'];
            }

            if(array_key_exists($proto_product['product_id'], $products)) {
                $products[ $proto_product['product_id'] ]['images'][] = $proto_product['image_src'];
                $products[ $proto_product['product_id'] ]['categories'][] = $proto_product['category_name'];
            } else {
                $products[ $proto_product['product_id'] ] = [
                    'product_id' => $proto_product['product_id'],
                    'title' => $proto_product['title'],
                    'active' => $proto_product['active'],
                    'updated_at' => $proto_product['updated_at'],
                    'sku' => $proto_product['sku'],
                    'description' => preg_replace("/\s+/", " ",str_replace("<br />", " ", nl2br(strip_tags($proto_product['description'])))),
                    'slug' => $proto_product['slug'],
                    'url' => 'http://bonzoportal.hu/termek/'.$proto_product['slug'],
                    'price' => $proto_product['price'],
                    'discount_price' => $proto_product['discount_price'],
                    'sale_price' => ($proto_product['discount_price']?$proto_product['discount_price']:$proto_product['price']),
                    'coverimage_src' => $proto_product['coverimage_src'],
                    'categories' => [
                        $proto_product['category_name']
                    ],
                    'images' => [
                        $proto_product['image_src'],
                    ]
                ];
            }
        }

        return $products;
    }

    public function renderFeed($slug) {
        $feed = self::find('slug = "'.$slug.'"');
        if(count($feed)) {
            $feed = $feed[0];

            $products = $this->getProductList();

            $compiler = new \Phalcon\Mvc\View\Engine\Volt($this->getDI()->get('view'));
            $compiler->getCompiler()->addFunction('in_array', 'in_array');
            $compiler->getCompiler()->addFunction('implode', 'implode');
            $compiler->getCompiler()->addFunction('array_unique', 'array_unique');
            $parsed = $compiler->getCompiler()->compileString($feed->template);
            $this->escaper = new Phalcon\Escaper();

            if(!array_key_exists('no_xml', $_GET)) {
            header ("Content-Type:text/xml");

            }
            eval('; ?>'. $parsed);
            die();
        }
    }
}