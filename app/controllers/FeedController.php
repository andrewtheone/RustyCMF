<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
class FeedController extends ControllerBase {
    public function array_to_xml($array, $level=1) {

        $array2XmlConverter  = new XmlDomConstructor('1.0', 'utf-8');
        $array2XmlConverter->xmlStandalone   = TRUE;
        $array2XmlConverter->formatOutput    = TRUE;

        try {
            $array2XmlConverter->fromMixed( $array );
            $array2XmlConverter->normalizeDocument ();
            $xml    = $array2XmlConverter->saveXML();
            return  $xml;
        }
        catch( Exception $ex )  {
            return  $ex;
        }
    }

    public function viewAction($slug) {
        (new Feeds)->renderFeed($slug);
    }

    public function groovyAction() {
        $b_products = (new Products)->groovyList();

        die(json_encode(['products' => $b_products]));
    }

    public function akcioAction() {
        $b_products = (new Products)->akciomania();
        $products = [];
        foreach($b_products as $p) {
            $i = count($products);

            $products[$i] = $p;
            if(strpos($p['kep1'], 'http') === FALSE) {
                $products[$i]['kep1'] = 'http://bonzoportal.hu'.$p['kep1'];
            }
        }
        die($this->array_to_xml(['akciok' => ['akcio' => $products]]));
    }

    public function vouchersAction() {
        $b_products = (new Products)->vouchers();
        $products = [];

        foreach($b_products as $p) {
            $i = count($products);
            $products[$i] = $p;
            $products[$i]['images'] = [
                'image' => [
                    ['original' => $p['images']]
                ]
            ];
            $products[$i]['description'] = [
                ['original' => $p['description']]
            ];
        }
        die($this->array_to_xml(['vouchers' => ['voucher' => $products]]));
    }

    public function arukeresoAction() {
        $b_products = (new Products)->groovyList();
        $products = [];

        foreach($b_products as $p) {
            $category = "";

            foreach($p['categories'] as $c) {
                if((strlen($category) < strlen($c)) && ($c != "Készletkisöprés") && ($c != "Újdonságok"))
                    $category = $c;
            }

            $products[] = [
                'manufacturer' => 'Bonzoportál',
                'name' => $p['title'],
                'category' => str_replace("/", ">", $category),
                'product_url' => $p['url'],
                'price' => $p['sale_price'],
                'image_url' => $p['coverimage_src'],
                'description' => $p['description'], 
            ];
    }
        die($this->array_to_xml(['products' => ['product' => $products]]));
    }

    public function qponverzumAction() {
        $b_products = (new Products)->groovyList();
        $products = [];

        $last_product_id = null;

        foreach($b_products as $p) {
            if($last_product_id == $p['product_id']) {

            } else {
                $i = count($products);

                if(strpos($p['coverimage_src'], 'http') === FALSE) {
                    $p['coverimage_src'] = 'http://bonzoportal.hu'.$p['coverimage_src'];
                }
                if($p['product_id']) {
                    $products[$i] = [
                        'deal_id' => $p['product_id'],
                        'deal_hun_url' => 'http://bonzoportal.hu/termek/'.$p['slug'],
                        'deal_hun_title' => $p['title'],
                        'deal_eng_url' => 'http://bonzoportal.hu/termek/'.$p['slug'],
                        'deal_eng_title' => $p['title'],
                        'deal_previous_price' => $p['price'],
                        'deal_price' => $p['discount_price'],
                        'deal_discount' => ($p['discount_price'])?((1-$p['discount_price']/$p['price'])*100):0,
                        'deal_start' => '2015-01-12 00:00:00',
                        'deal_end' => '2015-12-31 00:00:00',
                        'deal_image' => $p['coverimage_src'],
                        'deal_service_provider' => 'Bonzoportál',
                        'deal_locations' => [
                            [
                                'deal_location' => [
                                    [
                                        'deal_city' => 'Budapest',
                                        'deal_address' => '1071 Budapest, Damjanich u. 51.',
                                        'lat' => '47.50923',
                                        'lng' => '19.082287'
                                    ]
                                ]
                            ]
                        ],
                        'deal_sales' => 0,
                        'deal_min' => 1,
                        'deal_active' => $p['active']?'true':'false',
                        'deal_appear' => $p['active']?'true':'false',
                        'deal_last_update' => $p['updated_at']
                    ];

                    $last_product_id = $p['product_id'];
                }
            }
        }
        die($this->array_to_xml(['deals' => ['deal' => $products]]));
    }
}

class   XmlDomConstructor   extends DOMDocument {

    public  function    fromMixed($mixed, DOMElement $domElement = null) {

        $domElement = is_null($domElement) ? $this : $domElement;

        if (is_array($mixed)) {
            foreach( $mixed as $index => $mixedElement ) {

                $cdata = ['deal_image', 'description', 'deal_hun_title', 'deal_service_provider', 'deal_address'];

                if(in_array($index, $cdata) && !is_int($index)) {
                    $anode = $this->createElement($index);
                    $node = $this->createCDATASection($mixedElement);
                    $anode->appendChild($node);
                    $domElement->appendChild($anode);
                } else {
                    if ( is_int($index) ) {
                        if ( $index == 0 ) {
                            $node = $domElement;
                        } 
                        else {
                            $node = $this->createElement($domElement->tagName);
                            $domElement->parentNode->appendChild($node);
                        }
                    }
                    else {
                        $node = $this->createElement($index);
                        $domElement->appendChild($node);
                    }

                    $this->fromMixed($mixedElement, $node);
                }
            }
        } 
        else {
            $domElement->appendChild($this->createTextNode($mixed));
        }
    }
} 