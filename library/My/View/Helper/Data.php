<?php
/**
 * Data Helper
 * @author CuongNguyen
 *
 */
class My_View_Helper_Data extends Zend_View_Helper_Abstract {

    public function data()
    {
        return $this;
    }

    public function makeRandomString($max = 6) {
        $i = 0; //Reset the counter.
        $possible_keys = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $keys_length = strlen($possible_keys);
        $str = ""; //Let's declare the string, to add later.
        while($i<$max) {
            $rand = mt_rand(1,$keys_length-1);
            $str.= $possible_keys[$rand];
            $i++;
        }
        return $str;
    }

    public function genSampleCategories($itemCount = 10, $photoSize = '250x250') {
        $categories = array();
        for ($x = 0; $x < $itemCount; $x++) {
            array_push($categories, array(
                'id' => rand(1, 200),
                'name' => trim(str_repeat($this->makeRandomString() . ' ', 5)),
                'url' => '/category/item/' . rand(1, 200),
                'pcount' => rand(1, 100),
                'photo' => 'http://placehold.it/' . $photoSize,
            ));
        }
        return $categories;
    }

    public function genSampleProducts($itemCount = 10, $photoSize = '250x250') {
        $labelAs = array(
            array(
                'text' => 'Mới',
                'tcolor' => '#ffffff',
                'bcolor' => sprintf("#%06x", rand(0, 16777215))
            ),
            array(
                'text' => 'Bán chạy',
                'tcolor' => '#ffffff',
                'bcolor' => sprintf("#%06x", rand(0, 16777215))
            ),
            array(
                'text' => 'Hết hàng',
                'tcolor' => '#ffffff',
                'bcolor' => sprintf("#%06x", rand(0, 16777215))
            ),
            array(
                'text' => '-10%',
                'tcolor' => '#ffffff',
                'bcolor' => sprintf("#%06x", rand(0, 16777215))
            )
        );
        $labelBs = array(
            array(
                'text' => array('Bảo hành 12 tháng', 'Miễn phí charge thẻ'),
                'tcolor' => '#ffffff',
                'bcolor' => sprintf("#%06x", rand(0, 16777215))
            ),
            array(
                'text' => array('Bảo hành 1 đổi 1', 'Giảm 20% tại chi nhánh A'),
                'tcolor' => '#ffffff',
                'bcolor' => sprintf("#%06x", rand(0, 16777215))
            ),
            array(
                'text' => array('Trả góp 0%', 'Liên kết thẻ để có cơ hội nhận thưởng'),
                'tcolor' => '#ffffff',
                'bcolor' => sprintf("#%06x", rand(0, 16777215))
            )
        );
        $products = array();
        for ($x = 0; $x < $itemCount; $x++) {
            array_push($products, array(
                'id' => rand(1, 200),
                'sku' => $this->makeRandomString(),
                'labelA' => $labelAs[rand(0, 3)],
                'labelB' => $labelBs[rand(0, 2)],
                'brand' => array(
                    'id' => rand(1, 200),
                    'name' => trim(str_repeat($this->makeRandomString() . ' ', 2)),
                    'url' => '/brand/item/' . rand(1, 200)
                ),
                'name' => trim(str_repeat($this->makeRandomString() . ' ', 5)),
                'priceA' => number_format(rand(1000000, 20000000)) . '&#8363',
                'priceB' => number_format(rand(1000000, 20000000)) . '&#8363',
                'url' => '/product/item/' . rand(1, 200),
                'cart' => '/cart/add/' . rand(1, 200),
                'views' => rand(1000, 5000),
                'photo' => 'http://placehold.it/' . $photoSize,
                // 'photo' => 'http://placehold.it/250x250/' . sprintf("%06x", rand(0, 16777215)) . '/ffffff'
                'specs' => '<span>Màn hình: 5.5", Full HD</span><br/>
                            <span>HĐH: Android 6.0 (Marshmallow)</span><br/>
                            <span>CPU: Mediatek MT6750 8 nhân</span><br/>
                            <span>RAM: 4 GB, ROM: 64 GB</span><br/>
                            <span>Camera: 13 MP, Selfie: 16 MP và 8 MP</span><br/>
                            <span>PIN: 3200 mAh</span>'
            ));
        }
        return $products;
    }

    public function genSamplePosts($itemCount = 10, $photoSize = '250x250') {
        $posts = array();
        for ($x = 0; $x < $itemCount; $x++) {
            array_push($posts, array(
                'id' => rand(1, 200),
                'title' => trim(str_repeat($this->makeRandomString() . ' ', 5)),
                'url' => '/post/item/' . rand(1, 200),
                'date' => rand(1, 200),
                'ccount' => rand(1, 200),
                'photo' => 'http://placehold.it/' . $photoSize,
            ));
        }
        return $posts;
    }

    public function genSampleBanners($itemCount = 1, $photoSize = '1040x200') {
        $advs = array();
        $sizes = explode('x', $photoSize);
        for ($x = 0; $x < $itemCount; $x++) {
            array_push($advs, array(
                'width' => $sizes[0],
                'height' => $sizes[1],
                'outside' => rand(0, 1), // Set if this banner link to another website or not. Value: True/False
                'url' => 'http://anothersite.com/' . $this->makeRandomString(),
                'photo' => 'http://placehold.it/' . $photoSize . '/a8c0d8/444444/?text=' . $photoSize . '+-+' . ($x + 1),
            ));
        }
        return $advs;
    }
    public function genSampleCheckList($itemCount = 5) {
    	$list = array();
    	for ($x = 0; $x < $itemCount; $x++) {
    		array_push($list, array(
    				'value' => $this->makeRandomString(),
    				'text' => trim(str_repeat($this->makeRandomString() . ' ', 3))
    		));
    	}
    	return $list;
    }
    
    public function genSampleSortList() {
    	$list = array(
    			array(
    					'value' => "/product/?sort=" + $this->makeRandomString(),
    					'text' => "Mặc định"
    			),
    			array(
    					'value' => "/product/?order=asc&sort=" + $this->makeRandomString(),
    					'text' => "Tên (A-Z)"
    			),
    			array(
    					'value' => "/product/?order=desc&sort=" + $this->makeRandomString(),
    					'text' => "Tên (Z-A)"
    			),
    			array(
    					'value' => "/product/?order=asc&sort=" + $this->makeRandomString(),
    					'text' => "Giá (A-Z)"
    			),
    			array(
    					'value' => "/product/?order=desc&sort=" + $this->makeRandomString(),
    					'text' => "Giá (Z-A)"
    			)
    	);
    	return $list;
    }
    
    public function genSamplePageSizeList() {
    	$list = array(
    			array(
    					'value' => "/product/?limit=20",
    					'text' => "20"
    			),
    			array(
    					'value' => "/product/?limit=32",
    					'text' => "32"
    			),
    			array(
    					'value' => "/product/?limit=56",
    					'text' => "56"
    			)
    	);
    	return $list;
    }
    
}
