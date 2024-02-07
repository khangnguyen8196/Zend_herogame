<?php

/**
 * Common functions
 * @author Phuong Nguyen
 *
 */
class Commons {
	public static function url_slug($str, $options = array()) {
		// Make sure string is in UTF-8 and strip invalid UTF-8 characters
		$str = mb_convert_encoding ( ( string ) $str, 'UTF-8', mb_list_encodings () );
		
		$defaults = array (
				'delimiter' => '-',
				'limit' => null,
				'lowercase' => true,
				'replacements' => array (),
				'transliterate' => false 
		);
		
		// Merge options
		$options = array_merge ( $defaults, $options );
		
		$char_map = array (
				// Latin
				'À' => 'A',
				'Á' => 'A',
				'Â' => 'A',
				'Ã' => 'A',
				'Ä' => 'A',
				'Å' => 'A',
				'Æ' => 'AE',
				'Ç' => 'C',
				'È' => 'E',
				'É' => 'E',
				'Ê' => 'E',
				'Ë' => 'E',
				'Ì' => 'I',
				'Í' => 'I',
				'Î' => 'I',
				'Ï' => 'I',
				'Ð' => 'D',
				'Ñ' => 'N',
				'Ò' => 'O',
				'Ó' => 'O',
				'Ô' => 'O',
				'Õ' => 'O',
				'Ö' => 'O',
				'Ő' => 'O',
				'Ø' => 'O',
				'Ù' => 'U',
				'Ú' => 'U',
				'Û' => 'U',
				'Ü' => 'U',
				'Ű' => 'U',
				'Ý' => 'Y',
				'Þ' => 'TH',
				'ß' => 'ss',
				'à' => 'a',
				'á' => 'a',
				'â' => 'a',
				'ã' => 'a',
				'ä' => 'a',
				'å' => 'a',
				'æ' => 'ae',
				'ç' => 'c',
				'è' => 'e',
				'é' => 'e',
				'ê' => 'e',
				'ë' => 'e',
				'ì' => 'i',
				'í' => 'i',
				'î' => 'i',
				'ï' => 'i',
				'ð' => 'd',
				'ñ' => 'n',
				'ò' => 'o',
				'ó' => 'o',
				'ô' => 'o',
				'õ' => 'o',
				'ö' => 'o',
				'ő' => 'o',
				'ø' => 'o',
				'ù' => 'u',
				'ú' => 'u',
				'û' => 'u',
				'ü' => 'u',
				'ű' => 'u',
				'ý' => 'y',
				'þ' => 'th',
				'ÿ' => 'y',
				// Latin symbols
				'©' => '(c)',
				// Greek
				'Α' => 'A',
				'Β' => 'B',
				'Γ' => 'G',
				'Δ' => 'D',
				'Ε' => 'E',
				'Ζ' => 'Z',
				'Η' => 'H',
				'Θ' => '8',
				'Ι' => 'I',
				'Κ' => 'K',
				'Λ' => 'L',
				'Μ' => 'M',
				'Ν' => 'N',
				'Ξ' => '3',
				'Ο' => 'O',
				'Π' => 'P',
				'Ρ' => 'R',
				'Σ' => 'S',
				'Τ' => 'T',
				'Υ' => 'Y',
				'Φ' => 'F',
				'Χ' => 'X',
				'Ψ' => 'PS',
				'Ω' => 'W',
				'Ά' => 'A',
				'Έ' => 'E',
				'Ί' => 'I',
				'Ό' => 'O',
				'Ύ' => 'Y',
				'Ή' => 'H',
				'Ώ' => 'W',
				'Ϊ' => 'I',
				'Ϋ' => 'Y',
				'α' => 'a',
				'β' => 'b',
				'γ' => 'g',
				'δ' => 'd',
				'ε' => 'e',
				'ζ' => 'z',
				'η' => 'h',
				'θ' => '8',
				'ι' => 'i',
				'κ' => 'k',
				'λ' => 'l',
				'μ' => 'm',
				'ν' => 'n',
				'ξ' => '3',
				'ο' => 'o',
				'π' => 'p',
				'ρ' => 'r',
				'σ' => 's',
				'τ' => 't',
				'υ' => 'y',
				'φ' => 'f',
				'χ' => 'x',
				'ψ' => 'ps',
				'ω' => 'w',
				'ά' => 'a',
				'έ' => 'e',
				'ί' => 'i',
				'ό' => 'o',
				'ύ' => 'y',
				'ή' => 'h',
				'ώ' => 'w',
				'ς' => 's',
				'ϊ' => 'i',
				'ΰ' => 'y',
				'ϋ' => 'y',
				'ΐ' => 'i',
				// Turkish
				'Ş' => 'S',
				'İ' => 'I',
				'Ç' => 'C',
				'Ü' => 'U',
				'Ö' => 'O',
				'Ğ' => 'G',
				'ş' => 's',
				'ı' => 'i',
				'ç' => 'c',
				'ü' => 'u',
				'ö' => 'o',
				'ğ' => 'g',
				// Russian
				'А' => 'A',
				'Б' => 'B',
				'В' => 'V',
				'Г' => 'G',
				'Д' => 'D',
				'Е' => 'E',
				'Ё' => 'Yo',
				'Ж' => 'Zh',
				'З' => 'Z',
				'И' => 'I',
				'Й' => 'J',
				'К' => 'K',
				'Л' => 'L',
				'М' => 'M',
				'Н' => 'N',
				'О' => 'O',
				'П' => 'P',
				'Р' => 'R',
				'С' => 'S',
				'Т' => 'T',
				'У' => 'U',
				'Ф' => 'F',
				'Х' => 'H',
				'Ц' => 'C',
				'Ч' => 'Ch',
				'Ш' => 'Sh',
				'Щ' => 'Sh',
				'Ъ' => '',
				'Ы' => 'Y',
				'Ь' => '',
				'Э' => 'E',
				'Ю' => 'Yu',
				'Я' => 'Ya',
				'а' => 'a',
				'б' => 'b',
				'в' => 'v',
				'г' => 'g',
				'д' => 'd',
				'е' => 'e',
				'ё' => 'yo',
				'ж' => 'zh',
				'з' => 'z',
				'и' => 'i',
				'й' => 'j',
				'к' => 'k',
				'л' => 'l',
				'м' => 'm',
				'н' => 'n',
				'о' => 'o',
				'п' => 'p',
				'р' => 'r',
				'с' => 's',
				'т' => 't',
				'у' => 'u',
				'ф' => 'f',
				'х' => 'h',
				'ц' => 'c',
				'ч' => 'ch',
				'ш' => 'sh',
				'щ' => 'sh',
				'ъ' => '',
				'ы' => 'y',
				'ь' => '',
				'э' => 'e',
				'ю' => 'yu',
				'я' => 'ya',
				// Ukrainian
				'Є' => 'Ye',
				'І' => 'I',
				'Ї' => 'Yi',
				'Ґ' => 'G',
				'є' => 'ye',
				'і' => 'i',
				'ї' => 'yi',
				'ґ' => 'g',
				// Czech
				'Č' => 'C',
				'Ď' => 'D',
				'Ě' => 'E',
				'Ň' => 'N',
				'Ř' => 'R',
				'Š' => 'S',
				'Ť' => 'T',
				'Ů' => 'U',
				'Ž' => 'Z',
				'č' => 'c',
				'ď' => 'd',
				'ě' => 'e',
				'ň' => 'n',
				'ř' => 'r',
				'š' => 's',
				'ť' => 't',
				'ů' => 'u',
				'ž' => 'z',
				// Polish
				'Ą' => 'A',
				'Ć' => 'C',
				'Ę' => 'e',
				'Ł' => 'L',
				'Ń' => 'N',
				'Ó' => 'o',
				'Ś' => 'S',
				'Ź' => 'Z',
				'Ż' => 'Z',
				'ą' => 'a',
				'ć' => 'c',
				'ę' => 'e',
				'ł' => 'l',
				'ń' => 'n',
				'ó' => 'o',
				'ś' => 's',
				'ź' => 'z',
				'ż' => 'z',
				// Latvian
				'Ā' => 'A',
				'Č' => 'C',
				'Ē' => 'E',
				'Ģ' => 'G',
				'Ī' => 'i',
				'Ķ' => 'k',
				'Ļ' => 'L',
				'Ņ' => 'N',
				'Š' => 'S',
				'Ū' => 'u',
				'Ž' => 'Z',
				'ā' => 'a',
				'č' => 'c',
				'ē' => 'e',
				'ģ' => 'g',
				'ī' => 'i',
				'ķ' => 'k',
				'ļ' => 'l',
				'ņ' => 'n',
				'š' => 's',
				'ū' => 'u',
				'ž' => 'z' 
		);
		
		// Make custom replacements
		$str = preg_replace ( array_keys ( $options ['replacements'] ), $options ['replacements'], $str );
		
		// Transliterate characters to ASCII
		if ($options ['transliterate']) {
			$str = str_replace ( array_keys ( $char_map ), $char_map, $str );
		}
		
		// Replace non-alphanumeric characters with our delimiter
		$str = preg_replace ( '/[^\p{L}\p{Nd}]+/u', $options ['delimiter'], $str );
		
		// Remove duplicate delimiters
		$str = preg_replace ( '/(' . preg_quote ( $options ['delimiter'], '/' ) . '){2,}/', '$1', $str );
		
		// Truncate slug to max. characters
		$str = mb_substr ( $str, 0, ($options ['limit'] ? $options ['limit'] : mb_strlen ( $str, 'UTF-8' )), 'UTF-8' );
		
		// Remove delimiter from ends
		$str = trim ( $str, $options ['delimiter'] );
		
		return $options ['lowercase'] ? mb_strtolower ( $str, 'UTF-8' ) : $str;
	}
	public static function cwUpload($field_name = '', $target_folder = '', $file_name = '', $thumb = FALSE, $thumb_folder = '', $thumb_width = '400', $thumb_height = '300') {
		// folder path setup
		$target_path = $target_folder;
		$thumb_path = $thumb_folder;
		
		// file name setup
		$filename_err = explode ( ".", $_FILES [$field_name] ['name'] );
		$filename_err_count = count ( $filename_err );
		$file_ext = $filename_err [$filename_err_count - 1];
		if ($file_name != '') {
			$fileName = Commons::_createFileName ( $file_name . '.' . $file_ext );
		} else {
			$fileName = Commons::_createFileName ( $_FILES [$field_name] ['name'] );
		}
		
		// upload image path
		$upload_image = $target_path . basename ( $fileName );
		
		// upload image
		if (move_uploaded_file ( $_FILES [$field_name] ['tmp_name'], $upload_image )) {
			// thumbnail creation
			if ($thumb == TRUE) {
				$thumbnail = $thumb_path . $fileName;
				list ( $width, $height ) = getimagesize ( $upload_image );
				$thumb_create = imagecreatetruecolor ( $thumb_width, $thumb_height );
				switch ($file_ext) {
					case 'jpg' :
						$source = imagecreatefromjpeg ( $upload_image );
						break;
					case 'jpeg' :
						$source = imagecreatefromjpeg ( $upload_image );
						break;
					
					case 'png' :
						$source = imagecreatefrompng ( $upload_image );
						break;
					case 'gif' :
						$source = imagecreatefromgif ( $upload_image );
						break;
					default :
						$source = imagecreatefromjpeg ( $upload_image );
				}
				
				imagecopyresized ( $thumb_create, $source, 0, 0, 0, 0, $thumb_width, $thumb_height, $width, $height );
				switch ($file_ext) {
					case 'jpg' || 'jpeg' :
						imagejpeg ( $thumb_create, $thumbnail, 100 );
						break;
					case 'png' :
						imagepng ( $thumb_create, $thumbnail, 100 );
						break;
					
					case 'gif' :
						imagegif ( $thumb_create, $thumbnail, 100 );
						break;
					default :
						imagejpeg ( $thumb_create, $thumbnail, 100 );
				}
			}
			
			return $fileName;
		} else {
			return false;
		}
	}
	public static function makedirs($dirpath, $mode = 0777) {
		return is_dir ( $dirpath ) || mkdir ( $dirpath, $mode, true );
	}
	public static function _clean($string) {
		$string = str_replace ( ' ', '-', $string ); // Replaces all spaces with hyphens.
		$string = preg_replace ( '/[^A-Za-z0-9\-]/', '', $string ); // Removes special chars.
		return preg_replace ( '/-+/', '-', $string ); // Replaces multiple hyphens with single one.
	}
	public static function _createFileName($fileName) {
		$date = getdate ();
		$now = $date ['mday'] . $date ['mon'] . $date ['year'] . $date ['hours'] . $date ['minutes'] . $date ['seconds'];
		$ranString = uniqid ( rand ( 0, 100000 ), true );
		$uniqString = Commons::_clean ( $ranString );
		$imageName = $now . '_' . $ranString . '_' . $fileName;
		return $imageName;
	}
	public static function getWidthHeightImg($field) {
		$result = array (
				'width' => '200',
				'height' => '160' 
		);
		if (! empty ( $field ) && ! empty ( $_FILES [$field] ["tmp_name"] ) == true) {
			$image_info = getimagesize ( $_FILES [$field] ["tmp_name"] );
			$result ['width'] = $image_info [0] / 2;
			$result ['height'] = $image_info [1] / 2;
		}
		return $result;
	}
	public function truncateStr($str) {
		$pos = strpos ( $str, '_', 37 );
		$temp = substr ( $str, $pos + 1 );
		$posDot = strpos ( $temp, '.' );
		$result = substr ( $temp, 0, $posDot );
		return $result;
	}
	public static function getStatusOrder() {
		return array (
				'1' => 'Đang Xử Lý',
				'2' => 'Xác Nhận',
				'3' => 'Đang Vận Chuyển',
				'4' => 'Giao Thành Công',
				'5' => 'Hủy' 
		);
	}
	public static function getSettingByKey($list, $keyword) {
		if (empty ( $list ) == false) {
			foreach ( $list as $key => $value ) {
				if ($value ['key'] == $keyword) {
					return $value;
				}
			}
		}
		return '';
	}
	
	/**
	 *
	 * @param type $res        	
	 * @return type
	 */
	public static function _buildProductResponse($res) {
		$productRes = array ();
		if (empty ( $res ) == false) {
			foreach ( $res as $key => $value ) {
				$productRes [] = self::_buildProductData ( $value );
			}
		}
		return $productRes;
	}

	/**
	 *
	 * @param type $value        	
	 * @return type
	 */
	public static function _buildProductData($value) {
		$titleText = '';
		$bcolor = '';
		$now = date('Y-m-d H:i:s');
		if ($value ["is_promotion"]) {
			if($value ["enable_promo"]==1 and $now < $value["count_time"] and $value['price_flash_sale']>0){
				$titleText = 100 - round ( ($value ["price_flash_sale"] / $value ["price"]) * 100 );
				if ($titleText > 0) {
					$titleText = '-' . $titleText;
				} else if ($titleText == 0) {
					$titleText = "";
				}
				if (empty ( $titleText ) == false) {
					$titleText = $titleText . "%";
					$bcolor = '#ff9601';
				}
			}else{
				$titleText = 100 - round ( ($value ["price_sales"] / $value ["price"]) * 100 );
				if ($titleText > 0) {
					$titleText = '-' . $titleText;
				} else if ($titleText == 0) {
					$titleText = "";
				}
				if (empty ( $titleText ) == false) {
					$titleText = $titleText . "%";
					$bcolor = '#ff9601';
				}
			}
		}elseif($value ["new_product"]) {
			$titleText = 'Mới';
			$bcolor = '#66b800';
		}elseif ($value ["best_sell"]) {
			$titleText = 'Pre-Order';
			$bcolor = '#cc2600';
		} 
		if ($value ["status"] == 2) {
			$titleText = 'Tạm hết hàng';
			$bcolor = '#000';
		}
		$product ["status"] = $value ["status"];
		$product ["id"] = $value ["id"];
		$product ["labelA"] = array (
				'text' => $titleText,
				'tcolor' => '#ffffff',
				'bcolor' => $bcolor 
		);
		
		$shortDes = '';
		if (empty ( $value ["notice_message"] ) == false) {
			$shortDes = $value ["notice_message"];
		}
		$bcolorLabelB = '#189eff';
		if (empty ( $value ["color"] ) == false) {
			$bcolorLabelB = $value ["color"];
		}
		$product ["labelB"] = array (
				'text' => array (
						$shortDes 
				),
				'tcolor' => '#ffffff',
				'bcolor' => $bcolorLabelB 
		);
		$product ["name"] = $value ["title"];
		$product ["priceA"] = number_format ( $value ["price_sales"] ) . '&#8363';
		$product ["priceB"] = number_format ( $value ["price"] ) . '&#8363';
		$product ["priceC"] = number_format ( $value ["price_flash_sale"] ) . '&#8363';
		// $product["url"] = "/san-pham/chi-tiet/name/" . $value["url_product"];
		$product ["url"] = "/" . $value ["url_product"];
		$product ["photo"] = '';
		if (empty ( $value ["image"] ) == false) {
			$product ["photo"] = "/upload/images" . $value ["image"];
		}
		$product ["specs"] = $value ["description"];
		$product ["count_down"] = $value ["count_time"];
		$product ["enable_promo"] = $value ["enable_promo"];
		return $product;
	}	
	/**
	 *
	 * @return array
	 */
	public static function pageSizeList() {
		$list = array (
				array (
						'value' => PAGINNATOR_LIMIT_ROW,
						'text' => PAGINNATOR_LIMIT_ROW 
				),
				array (
						'value' => PAGINNATOR_LIMIT_ROW + 20,
						'text' => PAGINNATOR_LIMIT_ROW + 20 
				),
				array (
						'value' => PAGINNATOR_LIMIT_ROW + 45,
						'text' => PAGINNATOR_LIMIT_ROW + 45 
				),
				array (
						'value' => PAGINNATOR_LIMIT_ROW + 70,
						'text' => PAGINNATOR_LIMIT_ROW + 70 
				) 
		);
		return $list;
	}
	/**
	 *
	 * @return array
	 */
	public static function sortList() {
		$list = array (
				array (
						'value' => "priority_desc",
						'text' => "Mặc định" 
				),
				array (
						'value' => "title_asc",
						'text' => "Tên (A-Z)" 
				),
				array (
						'value' => "title_desc",
						'text' => "Tên (Z-A)" 
				),
				array (
						'value' => "price_sales_asc",
						'text' => "Giá Tăng Dần" 
				),
				array (
						'value' => "price_sales_desc",
						'text' => "Giá Giảm Dần" 
				) 
		);
		return $list;
	}
	/**
	 *
	 * @param type $sorted        	
	 * @return string
	 */
	public static function getSortRealValue($sorted) {
		$sortValue = '';
		switch ($sorted) {
			case "title_asc" :
				$sortValue = "title ASC";
				break;
			case "title_desc" :
				$sortValue = "title DESC";
				break;
			case "price_sales_asc" :
				$sortValue = "price_sales ASC";
				break;
			case "price_sales_desc" :
				$sortValue = "price_sales DESC";
				break;
			default :
				$sortValue = "priority DESC";
				break;
		}
		return $sortValue;
	}
	/**
	 *
	 * @param type $status        	
	 * @return string
	 */
	public static function getProductStatus($status) {
		$txt = '';
		switch ($status) {
			case 1 :
				$txt = "Còn Hàng";
				break;
			case 2 :
				$txt = "Tạm Hết Hàng";
				break;
			case -1 :
				$txt = "Sản phẩm ngừng kinh doanh";
				break;
			default :
				$txt = "Còn Hàng";
				break;
		}
		return $txt;
	}
	public static function getMaxProductPrice() {
		$productMdl = new Product ();
		$max = $productMdl->getMaxProductPrice ();
		return $max ["max_price"];
	}
	public static function getMinProductPrice() {
		$productMdl = new Product ();
		$min = $productMdl->getMinProductPrice ();
		return $min ["min_price"];
	}
	
	/**
	 *
	 * @param type $t        	
	 * @param type $cart_list        	
	 */
	public static function countItemInCart($cart_list) {
		$countItem = 0;
		if (isset($cart_list) && is_array($cart_list)) {
			if(!empty($cart_list['products'])){
				foreach ($cart_list['products'] as $value) {
					if(!empty($value['qty'])){
						$qty = ($value['qty']);
					}
					if(!empty($value['variant']['qty'])){
						$qty = ($value['variant']['qty']);
					}
					$countItem += $qty;
				}
			}
			if(!empty($cart_list['combos'])){
				foreach ($cart_list['combos'] as $combo) {
					if(!empty($combo['qty'])){
						$qty = $combo['qty'];
					}
					$countItem += $qty;
				}
			}
		}
		return $countItem;
	}

	/**
	 * [getProductColor description]
	 * @param  [type] $color_list [description]
	 * @return [type]             [description]
	 */
	public static function getProductColor($colors){
		$color_list = array();
        
        if( empty( $colors ) == false ){
            $exploded_data = explode(',', $colors);
            $mdlProductColor = new ProductColor();
            if( empty($exploded_data) == false ){
                $color_list = $mdlProductColor->fetchAllColorByGroup($exploded_data);
            }
        }
        return $color_list;
	}
}
