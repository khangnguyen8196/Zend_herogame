<?php
/**
 * Zend validator
 * @author Hoang Tran
 *
 */
class UtilValidator {
	
	/**
	 *
	 * @param unknown $xml        	
	 * @param unknown $data        	
	 * @param unknown $option        	
	 * @param
	 *        	$ignore
	 * @throws Exception
	 * @return multitype:unknown
	 */
	private static $CLASS_NAME = array('url' => 'data-url','display' =>'data-display','values' =>'data-value','item' => 'data-item',
			'key'=>'data-key','type' => 'data-type','class' => 'class','event'=>'data-event',
			'custom'=>'data-custom','field'=>'data-field', 'inogreAll' => 'data-inogre-all');
	public static function check($xml, &$data = array(), $option = array('isTranslate'=>true), $ignore = false) {
		$error = array ();
		if (is_file ( $xml ) == false) {
			throw new Exception ( 'The xml file is not found - ' . $xml );
		}
		$xmlObj = new Zend_Config_Xml ( $xml );
		$xmlObj = $xmlObj->toArray ();
		$validate = new Zend_Validate ();
		$filter = new Zend_Filter ();
		if (isset ( $xmlObj ['form'] ) == true                && 
		    empty ( $xmlObj ['form'] ) == false               && 
		    isset ( $xmlObj ['form'] ['checkrules'] ) == true && 
		    empty ( $xmlObj ['form'] ['checkrules'] ) == false &&
		    is_array( $xmlObj ['form'] ['checkrules'] ) ) {
			foreach ( $xmlObj ['form'] ['checkrules'] as $key => $condition ) {
				if (isset ( $condition ['filter'] ) == true) {
					foreach ( $condition ['filter'] as $className => $item ) {
					    if ( empty( $data [$key] ) == false ) {
    						if (@$item ['custom'] == 'true') {
    							if (isset ( $item ["class"] )) {
    								$filterObj = new $item ["class"] ();
    								if (method_exists ( $filterObj, $className )) {
    									$param = array (
    											$data [$key] 
    									);
    									$data [$key] = call_user_func_array ( $item ["class"] . '::' . $className, $param );
    								}
    							}
    						} else {
    							$data [$key] = $filter->filterStatic ( @$data [$key], $className );
    						}
					    }
					}
				}
				if ( isset ( $condition ['validate'] ) == true && empty( $condition ['validate'] ) == false && is_array( $condition ['validate'] ) ) {
					foreach ( $condition ['validate'] as $className => $rule_item ) {
						
						if (isset ( $rule_item ['skip'] ) == true && isset ( $error [$rule_item ['skip']] ) == true) {
							break;
						}
						if ($className == "NotEmpty" || @$rule_item ['custom'] == 'true' || ($className != "NotEmpty" && isset ( $data [$key] ) == true && (! is_array ( $data [$key] )) && strlen ( $data [$key] ) > 0)) {
							
							if (@$rule_item ['custom'] == 'true') {
								if (isset ( $rule_item ["class"] )) {
									$validator = new $rule_item ["class"] ();
									if ( method_exists ( $validator, $className ) == true) {
										$param = array ();
										// For compare date time data
										if ($className == "CompareDate") {
											$param = array (
													$data [$key],
													$data [$rule_item ['with']] 
											);
											// Check whether array empty or not
										} else if ($className == 'IsArrayEmpty') {
											$param = array (
													$data [$key] 
											);
											// Validate image (include image extentions, image size)
										} else if ($className == 'IsValidImage') {
											$param = array (
													$key 
											);
											// Compare data
										} else if($className == 'IsValidExcel') {
											$param = array (
													$key
											);
										} else if ($className == 'GreaterThan') {
											$param = array (
													$data [$key],
													$data [$rule_item ['with']] 
											);
											// check data which match with other data
										} else if ($className == 'MatchData') {
											$param = array (
													$data [$key],
													$data [$rule_item ['with']] 
											);
											// check data  is not exist in db
										} else if ($className == 'NoRecordExists') {
											
											$excluse = array ();
											if (isset ( $rule_item ['excludesField'] ) == true) {
												
												$excluse ['field'] = @$rule_item ['excludesField'];
												$excluse ['value'] = @$data [$rule_item ['excludesField']];
											}
											$param = array (
													$rule_item ['table'],
													$rule_item ['field'],
													$data [$key],
													$excluse 
											);
											// check data  is exist in db
										} else if ($className == 'RecordExists') {
											$excluse = array ();
											if (isset ( $rule_item ['excludesField'] ) == true) {
												
												$excluse ['field'] = @$rule_item ['excludesField'];
												$excluse ['value'] = @$data [$rule_item ['excludesField']];
											}
											$param = array (
													$rule_item ['table'],
													$rule_item ['field'],
													$data [$key],
													$excluse 
											);
											// compare email with other email
										} else if ($className == 'CheckPassword') {
											$pass = UtilEncryption::encryptPassword ( $data [$key] );
											$param = array (
													@$data ['id'],
													$pass 
											);
										} else if( $className =='CheckExchangeRate' ){
											$param = array (
													@$data[ $rule_item ['with'] ],// amount
													@$data[ $rule_item ['currency'] ], // currency 
													@$data [$key], //RetailAmount
													@$data[ $rule_item ['min'] ],
													@$data[ $rule_item ['max'] ],
													@$data[ $rule_item ['fee'] ] 
											);
										} else if( $className =='CheckObjectId' ){
										    $param = array (
										            @$data[ $rule_item ['with'] ],// object type
										            @$data[ $key ]// object id
										    );
									    } else if( $className =='CheckProductObjectId' ){
									        $param = array (
									                @$data[ $rule_item ['with'] ],// object type
									                @$data[ $key ]// object id
									        );
									    }  else if( $className =='CheckExistCode' ){
									        	$param = array (
									        			@$data[ $rule_item ['with'] ],//id
									        			@$data[ $key ]// code
									        	);
										} else {
											$param = array (
													$data [$key] 
											);
										}
										// if choose otion ignore then check excludes custom
										if ($ignore == true) {
											if (self::checkIgnore ( $xmlObj, $key, $className ) == true) {
												continue;
											}
										}
										$result = call_user_func_array ( $rule_item ["class"] . '::' . $className, $param );
										if ($result == true) {
											$error [$key] = self::_getTranslate ( $rule_item ['message'], $option ['isTranslate'] );
										}
										
										break;
									}
								}
							} else {
								$val = @( string ) $data [$key];
								// if className = 'EmailAddress' $rule_item = array ()
								$validate_rule = $rule_item;
								if ($className == 'EmailAddress') {
									$validate_rule = array ();
								}
								// if choose otion ignore then check excludes
								if ($ignore == true) {
									if (self::checkIgnore ( $xmlObj, $key, $className ) == true) {
										continue;
									}
								}
								$result = Zend_Validate::is ( $val, $className, $validate_rule );
								if ($result == false) {
									$error [$key] = self::_getTranslate ( $rule_item ['message'], $option ['isTranslate'] );
									break;
								}
							}
						}
					}
				}
			}
		}
		return $error;
	}
	/**
	 *
	 * @param unknown $xmlObj:xml        	
	 * @param unknown $condition:email        	
	 * @param unknown $className:<NotEmpty>        	
	 * @return boolean
	 */
	private function checkIgnore($xmlObj, $condition, $className) {
		if (isset ( $xmlObj ['form']['excludes'] ) == true && empty ( $xmlObj ['form']['excludes'] ) == false) {
			foreach ( $xmlObj ['form']['excludes'] as $keyExcludes => $classNameExclude ) {
				if ($keyExcludes == $condition) {
					foreach ( $classNameExclude as $c => $v ) {
						if ($c == $className)
							return true;
					}
				}
			}
		}
		return false;
	}
	/**
	 *
	 * @param unknown $isTranslate        	
	 * @param unknown $keyMessage        	
	 * @return Ambigous <string, unknown>
	 */
	private function _getTranslate($keyMessage, $isTranslate) {
		$message = '';
		if ($isTranslate == true) {
			$message = UtilTranslator::translate ( $keyMessage );
		} else {
			$message = $keyMessage;
		}
		return $message;
	}
	/**
	 *
	 * @param unknown $data        	
	 * @return boolean
	 */
	public static function Float($data) {
		$isError = false;
		$floatValidator = new Zend_Validate_Float (array('locale' => 'en_US'));
		if (empty ( $data ) == false && $floatValidator->isValid ( $data ) == false) {
			$isError = true;
		}
		return $isError;
	}
	/**
	 *
	 * @param datetime $starDate        	
	 * @param datetime $endDate        	
	 * @return boolean
	 */
	public static function CompareDate($endDate, $startDate) {
		$isError = false;
		$startDate = strtotime ( $startDate );
		$endDate = strtotime ( $endDate );
		if ($startDate > $endDate) {
			$isError = true;
		}
		
		return $isError;
	}
	/**
	 *
	 * @param array $data        	
	 * @return boolean
	 */
	public static function IsArrayEmpty($data) {
		$isEmpty = true;
		foreach ( $data as $value ) {
			if (empty ( $value ) == false) {
				$isEmpty = false;
				break;
			}
		}
		return $isEmpty;
	}
	private function mappingClassConfig( $className ){
		$attr = '';
		foreach (self::$CLASS_NAME as $key => $value ){
			if( $className == $key ){
				$attr = $value;
				break;
			}
		}
		return $attr;
	}
	public static  function printSearchConfig( $xml ){
	    $nameOfFile = pathinfo( $xml, PATHINFO_FILENAME );
	    $tagsCache = array( $nameOfFile . '_search' );
	    $modifiedTime = filemtime( $xml );
        $keyCache = UtilEncryption::generateKeyCache( $xml . '_search_' . $modifiedTime );
        $result = UtilCache::loadPersistentCache($keyCache);
	    if ( empty( $result ) == true ) {
	        UtilCache::cleanPersistentCache( Zend_Cache::CLEANING_MODE_MATCHING_TAG, $tagsCache );
    		$constraint = array();
    		$constraintPhp = array();
    		$xmlObj = new Zend_Config_Xml( $xml );
    		$xmlObj = $xmlObj->toArray();
    		if( isset( $xmlObj['formSearch'] ) == true           && 
    		    empty( $xmlObj['formSearch'] ) == false          && 
    		    isset( $xmlObj['formSearch']['config'] ) == true && 
    		    empty( $xmlObj['formSearch']['config'] ) == false ){
    			foreach ( $xmlObj['formSearch']['config'] as $key => $config ){
    				foreach ($config as $className => $item ){
    					if( $className == 'loadAjax' ){
    						if( $item == false ){
    							unset($constraint[$key]);
    							$constraintPhp[$key] = true;
    							break;
    						}
    					}
    					$attr = self::mappingClassConfig($className);
    					if(empty($attr) == false ){
    						$constraint[$key][$attr] = $item;
    					}
    				}
    			}
    		}
    		$result = array (
    			'elements' => $constraint
    		);
    		if (isset ( $xmlObj ['formSearch'] ['name'] ) == true && empty ( $xmlObj ['formSearch'] ['name'] ) == false) {
    			$name = $xmlObj ['formSearch'] ['name'];
    			$result ['name'] = $name;
    		} else {
    			$result ['name'] = 0;
    		}
    		if( empty( $constraintPhp ) == false ){
    			$result['LoadPhp'] = $constraintPhp;
    		}
    		UtilCache::savePersistentCache($keyCache, $result, $tagsCache);
	    }
		return $result;
	}
	public static  function printAjaxConfig( $xml ){
	    $nameOfFile = pathinfo( $xml, PATHINFO_FILENAME );
	    $tagsCache = array( $nameOfFile . '_add' );
	    $modifiedTime = filemtime( $xml );
	    $keyCache = UtilEncryption::generateKeyCache( $xml . '_add_' . $modifiedTime );
	    $result = UtilCache::loadPersistentCache($keyCache);
	    if ( empty( $result ) == true ) {
	        UtilCache::cleanPersistentCache( Zend_Cache::CLEANING_MODE_MATCHING_TAG, $tagsCache );
	        $constraint = array();
	        $constraintPhp = array();
	        $xmlObj = new Zend_Config_Xml( $xml );
	        $xmlObj = $xmlObj->toArray();
	        if( isset( $xmlObj['form'] ) == true           &&
	            empty( $xmlObj['form'] ) == false          &&
	            isset( $xmlObj['form']['config'] ) == true &&
	            empty( $xmlObj['form']['config'] ) == false ){
	            foreach ( $xmlObj['form']['config'] as $key => $config ){
	                foreach ($config as $className => $item ){
	                    if( $className == 'loadAjax' ){
	                        if( $item == false ){
	                            unset($constraint[$key]);
	                            $constraintPhp[$key] = true;
	                            break;
	                        }
	                    }
	                    $attr = self::mappingClassConfig($className);
	                    if( empty($attr) == false ){
	                    	$constraint[$key][$attr] = $item;
	                    }
	                    
	                }
	            }
	        }
	        $result = array (
	                'elements' => $constraint
	        );
	        if (isset ( $xmlObj ['form'] ['name'] ) == true && empty ( $xmlObj ['form'] ['name'] ) == false) {
	            $name = $xmlObj ['form'] ['name'];
	            $result ['name'] = $name;
	        } else {
	            $result ['name'] = 0;
	        }
	        if( empty( $constraintPhp ) == false ){
	            $result['LoadPhp'] = $constraintPhp;
	        }
	        UtilCache::savePersistentCache($keyCache, $result, $tagsCache);
	    }
	    return $result;
	}
	public static  function printSearchForm( $xml ){
	    $nameOfFile = pathinfo( $xml, PATHINFO_FILENAME );
	    $tagsCache = array( $nameOfFile . '_search_form' );
	    $modifiedTime = filemtime( $xml );
	    $keyCache = UtilEncryption::generateKeyCache( $xml . '_search_form_' . $modifiedTime );
	    $result = UtilCache::loadPersistentCache($keyCache);
	    if ( empty( $result ) == true ) {
	        UtilCache::cleanPersistentCache( Zend_Cache::CLEANING_MODE_MATCHING_TAG, $tagsCache );
	        $constraint = array();
	        $constraintPhp = array();
	        $xmlObj = new Zend_Config_Xml( $xml );
	        $xmlObj = $xmlObj->toArray();
	        if( isset( $xmlObj['formSearch'] ) == true && empty( $xmlObj['formSearch'] ) == false ){
	            $result = $xmlObj['formSearch'];
	            UtilCache::savePersistentCache($keyCache, $result, $tagsCache);
	        }
	    }
	    return $result;
	}
	public static  function printDetailForm( $xml ){
	    $nameOfFile = pathinfo( $xml, PATHINFO_FILENAME );
	    $tagsCache = array( $nameOfFile . '_detail_form' );
	    $modifiedTime = filemtime( $xml );
	    $keyCache = UtilEncryption::generateKeyCache( $xml . '_detail_form_' . $modifiedTime );
	    $result = UtilCache::loadPersistentCache($keyCache);
	    if ( empty( $result ) == true ) {
	        UtilCache::cleanPersistentCache( Zend_Cache::CLEANING_MODE_MATCHING_TAG, $tagsCache );
	        $constraint = array();
	        $constraintPhp = array();
	        $xmlObj = new Zend_Config_Xml( $xml );
	        $xmlObj = $xmlObj->toArray();
	        if( isset( $xmlObj['form'] ) == true && empty( $xmlObj['form'] ) == false ){
	            $result = $xmlObj['form'];
	            UtilCache::savePersistentCache($keyCache, $result, $tagsCache);
	        }
	    }
	    return $result;
	}
	public static  function printDataTableConfig( $xml ){
	    $nameOfFile = pathinfo( $xml, PATHINFO_FILENAME );
	    $tagsCache = array( $nameOfFile . '_datatable' );
	    $modifiedTime = filemtime( $xml );
	    $keyCache = UtilEncryption::generateKeyCache( $xml . '_datatable_' . $modifiedTime );
	    $result = UtilCache::loadPersistentCache($keyCache);
	    if ( empty( $result ) == true ) {
	        UtilCache::cleanPersistentCache( Zend_Cache::CLEANING_MODE_MATCHING_TAG, $tagsCache );
	        $constraint = array();
	        $constraintPhp = array();
	        $xmlObj = new Zend_Config_Xml( $xml );
	        $xmlObj = $xmlObj->toArray();
	        if( isset( $xmlObj['table'] ) == true && empty( $xmlObj['table'] ) == false ){
	            $result = $xmlObj['table'];
	            UtilCache::savePersistentCache($keyCache, $result, $tagsCache);
	        }
	    }
	    return $result;
	}
	/**
	 *
	 * @param Object $xml        	
	 * @param string $ignore        	
	 * @return multitype:number unknown Ambigous <multitype:, string, Ambigous, unknown>
	 */
	public static function printCondition( $xml, $ignore = false ) {
	    $nameOfFile = pathinfo( $xml, PATHINFO_FILENAME );
	    $tagsCache = array( $nameOfFile . '_validation' );
	    $modifiedTime = filemtime( $xml );
	    $keyCache = UtilEncryption::generateKeyCache( $xml . '_validation_' . $ignore . '_' . $modifiedTime );
	    $result = UtilCache::loadPersistentCache($keyCache);
	    if ( empty( $result ) == true ) {
	        UtilCache::cleanPersistentCache( Zend_Cache::CLEANING_MODE_MATCHING_TAG, $tagsCache );
    		$constraint = array ();
    		$xmlObj = new Zend_Config_Xml ( $xml );
    		$xmlObj = $xmlObj->toArray ();
    		if (isset ( $xmlObj ['form'] ) == true                && 
    		    empty ( $xmlObj ['form'] ) == false               && 
    		    isset ( $xmlObj ['form'] ['checkrules'] ) == true && 
    		    empty ( $xmlObj ['form'] ['checkrules'] ) == false &&
    		    is_array( $xmlObj ['form'] ['checkrules'] ) ) {
    			foreach ( $xmlObj ['form'] ['checkrules'] as $key => $condition ) {
    				if (isset ( $condition ['attr'] ) == true && $condition ['attr'] == 'multi') {
    					$key .= '[]';
    				}
    				if (isset ( $condition ['validate'] ) == true && is_array ( $condition ['validate'] )) {
    					foreach ( $condition ['validate'] as $className => $rule_item ) {
    						// if choose otion ignore then check excludes
    						if ($ignore == true) {
    							if (self::checkIgnore ( $xmlObj, $key, $className ) == true) {
    								continue;
    							}
    						}
    						// for validation
    						if ($className == 'NotEmpty') {
    							$constraint [$key] ['data-validation-engine'] [] = "validate[required]";
    							$constraint [$key] ['data-errormessage-value-missing'] =  $rule_item ['message'];
    						} elseif ($className == 'StringLength') {
    							if (isset ( $rule_item ['max'] ) == true) {
    								$constraint [$key] ['maxlength'] = $rule_item ['max'];
    								$constraint [$key] ['data-validation-engine'] [] = "validate[maxSize[" . ($rule_item ['max']) . "]]";
    								$constraint [$key] ['data-errormessage-range-overflow'] =  $rule_item ['message'];
    							}
    							if (isset ( $rule_item ['min'] ) == true) {
    								$constraint [$key] ['data-validation-engine'] [] = "validate[minSize[" . ($rule_item ['min']) . "]]";
    								$constraint [$key] ['data-errormessage-range-underflow'] =  $rule_item ['message'];
    							}
    						} elseif ($className == 'GreaterThan' && isset ( $rule_item ['custom'] ) == false) {
    							$constraint [$key] ['data-validation-engine'] [] = "validate[min[" . ($rule_item ["min"]) . "]]";
    							$constraint [$key] ['data-errormessage-range-underflow'] = $rule_item ['message'];
    						} elseif ($className == 'LessThan') {
    							$constraint [$key] ['data-validation-engine'] [] = "validate[max[" . ($rule_item ["max"]) . "]]";
    							$constraint [$key] ['data-errormessage-range-overflow'] = $rule_item ['message'];
    						} elseif ($className == 'MatchData') {
    							$constraint [$key] ['data-validation-engine'] [] = "validate[funcCall[GlobalNameSpace.Validator.checkReEnterPass]]";
    							$constraint [$key] ['data-errormessage-password-mismatch'] = $rule_item ['message'];
    						} elseif ($className == 'EmailAddress') {
    							$constraint [$key] ['data-validation-engine'] [] = "validate[funcCall[GlobalNameSpace.Validator.isValidEmail]]";
    							$constraint [$key] ['data-errormessage-email-error'] =  $rule_item ['message'];
    						} elseif($className == 'CheckType' ) {
    							$constraint [$key] ['data-validation-engine'] [] = "validate[funcCall[GlobalNameSpace.Validator.checkTagsType]]";
    							$constraint [$key] ['data-errormessage-amount-error'] =  $rule_item ['message'];
    						} elseif( $className == 'MaxSize' ) {
    							$constraint [$key] ['data-validation-engine'] [] = "validate[funcCall[GlobalNameSpace.Validator.checkMaxsize]]";
    							$constraint [$key] ['max-size'] = @$rule_item ['maxsize'];
    							$constraint [$key] ['data-error-message'] = $rule_item ['message'];
    						} elseif( $className =='CheckPremiumCall' ) {
    							$constraint [$key] ['data-validation-engine'] [] = "validate[funcCall[GlobalNameSpace.Validator.checkPremiumCall]]";
    							$constraint [$key] ['data-compare'] = @$rule_item ['with'];
    							$constraint [$key] ['data-selected'] = @$rule_item ['data-selected'];
    							$constraint [$key] ['data-error-message'] =  $rule_item ['message'];
    						} elseif( $className =='CheckObjectId' ) {
    						    $constraint [$key] ['data-validation-engine'] [] = "validate[funcCall[GlobalNameSpace.Validator.checkObjectId]]";
    						    $constraint [$key] ['data-compare'] = @$rule_item ['with'];
    						    $constraint [$key] ['data-error-message'] =  $rule_item ['message'];
    						} elseif( $className =='CheckProductObjectId' ) {
    						    $constraint [$key] ['data-validation-engine'] [] = "validate[funcCall[GlobalNameSpace.Validator.checkProductObjectId]]";
    						    $constraint [$key] ['data-compare'] = @$rule_item ['with'];
    						    $constraint [$key] ['data-error-message'] = $rule_item ['message'];
    						} elseif( $className == 'CheckCurrency'){
    							$constraint [$key] ['data-validation-engine'] [] = "validate[funcCall[GlobalNameSpace.Validator.CheckCurrency]]";
    							$constraint [$key] ['data-compare'] = @$rule_item ['with'];
    							$constraint [$key] ['data-check'] = @$rule_item ['check-field'];
    							$constraint [$key] ['data-error-message'] = $rule_item ['message'];
    						} elseif ( $className == 'Digits') {
    							$constraint [$key] ['data-type'] = "number";
    						} elseif ( $className == 'Float') {
    							$constraint [$key] ['data-type'] = "price";
    						} elseif ( $className == 'MatchData') {
    							$constraint [$key] ['compare-field'] = @$rule_item ["with"];
    						} elseif( $className == 'CheckType' ) {
    							$constraint [$key] ['check-field'] = @$rule_item ["with"];
    						}
    						
    					}
    				}
    			}
    		}
    		$result = array (
    				'elements' => $constraint 
    		);
    		if (isset ( $xmlObj ['form'] ['name'] ) == true && empty ( $xmlObj ['form'] ['name'] ) == false) {
    			$name = $xmlObj ['form'] ['name'];
    			$result ['name'] = $name;
    		} else {
    			$result ['name'] = 0;
    		}
    		UtilCache::savePersistentCache($keyCache, $result, $tagsCache);
	    }
		return $result;
	}
	public function loadValidationXml($name, $ignore = false) {
		$path = APPLICATION_PATH . '/xml/' . $name . '.xml';
		return self::printCondition ( $path, $ignore );
	}
	public function loadAjaxConfigXml($name, $ignore = false) {
	    $path = APPLICATION_PATH . '/xml/' . $name . '.xml';
	    return self::printAjaxConfig ( $path );
	}
	public function loadSearchConfigXml( $name ){
		$path = APPLICATION_PATH . '/xml/search/' . $name . '.xml';
		return self::printSearchConfig( $path );
	}
	public function loadSearchFormXml( $name ){
	    $path = APPLICATION_PATH . '/xml/search/' . $name . '.xml';
	    return self::printSearchForm( $path );
	}
	public function loadDataTableConfigXml( $name ){
	    $path = APPLICATION_PATH . '/xml/datatable/' . $name . '.xml';
	    return self::printDataTableConfig( $path );
	}
	public function loadDetailFormXml( $name ){
	    $path = APPLICATION_PATH . '/xml/' . $name . '.xml';
	    return self::printDetailForm( $path );
	}
	/**
	 * Validate image is uploaded from user client
	 * 
	 * @return boolean
	 */
	public function IsValidImage($key ) {
		$adapter = new Zend_File_Transfer_Adapter_Http ();
		$adapter->addValidator ( 'Extension', false, ALLOWED_IMAGE_EXTENSION_LIST );
		$adapter->addValidator ( 'FilesSize', false, MAX_SIZE_IMAGE_ALLOWED );
		$fileInfo = $adapter->getFileInfo ();
		if (empty ( $fileInfo [$key] ["tmp_name"] ) == false) {
			if ( $adapter->isValid ( $key ) == false ) { 
				return true;
			} else {
				return false;
			}
		}
		return false;
	}
	/**
	 * Validate file excel upload
	 * @param  $key
	 * @return boolean
	 */
	public function IsValidExcel( $key ) {
		$adapter = new Zend_File_Transfer_Adapter_Http ();
	
		$adapter->addValidator ( 'Extension', false, ALLOWED_EXCEL_EXTENSION_LIST );
		$adapter->addValidator ( 'Filessize', false, array (
				'max' => MAX_SIZE_IMAGE_ALLOWED
		) );
		$fileInfo = $adapter->getFileInfo ();
		if (empty ( $fileInfo [$key] ["tmp_name"] ) == false) {
			if ($adapter->isValid () == false) {
				return true;
			} else {
				return false;
			}
		}
		return false;
	}
	/**
	 *
	 * @param unknown $confirmPass        	
	 * @param unknown $pass        	
	 * @return boolean
	 */
	public function MatchData($data2, $data1) {
		$isError = false;
		if (strcmp ( $data2, $data1 ) != 0) {
			$isError = true;
		}
		return $isError;
	}
	/**
	 *
	 * @param number $id        	
	 * @param unknown $password        	
	 * @return boolean
	 */
	public function CheckPassword($id = 0, $password) {
		$isError = true;
		$users = new Users ();
		$objectUser = $users->fetchUserByIdAndPassword ( $id, $password );
		if (empty ( $objectUser ) == false) {
			$isError = false;
		}
		return $isError;
	}
	/**
	 * check record exists in db
	 * 
	 * @param string $table        	
	 * @param string $field        	
	 * @param string $values        	
	 * @param array $exclude        	
	 * @return boolean
	 */
	public function RecordExists($table, $field, $values, $exclude = array()) {
		$isError = false;
		$validator = new Zend_Validate_Db_RecordExists ( array (
				'table' => $table,
				'field' => $field 
		) );
		if (empty ( $exclude ) == false) {
			$validator->setExclude ( $exclude );
		}
		if ($validator->isValid ( $values ) == false) {
			$isError = true;
		}
		return $isError;
	}
	/**
	 * check record not exists in db
	 * 
	 * @param unknown $table        	
	 * @param unknown $field        	
	 * @param unknown $values        	
	 * @param unknown $exclude        	
	 * @return boolean
	 */
	public function NoRecordExists($table, $field, $values, $exclude = array()) {
		$isError = false;
		$validator = new Zend_Validate_Db_NoRecordExists ( array (
				'table' => $table,
				'field' => $field 
		) );
		if (empty ( $exclude ) == false) {
			$validator->setExclude ( $exclude );
		}
		if ($validator->isValid ( $values ) == false) {
			$isError = true;
		}
		return $isError;
	}
	/**
	 *
	 * @param unknown $min        	
	 * @param unknown $value        	
	 * @return boolean
	 */
	public function GreaterThan($min, $value) {
		$validation = new Zend_Validate_GreaterThan ( array (
				'min' => $min 
		) );
		$isError = false;
		if ($validation->isValid ( $value ) == true) {
			$isError = true;
		}
		return $isError;
	}
	public function CheckExchangeRate( $amount, $currency, $venueAmount, $min, $max, $fee ){
		//
		$isError = false;
    	if( empty( $listExchangeRate ) == false ) {
    		$exchangeRateInfo = $listExchangeRate['List'][0];
    	}
    	//
    	if( empty( $min ) == false || empty( $max ) == false ){
    		if( $currency != DEFAULT_CURRENCY || $venueAmount != 0 ) {
    			$isError = true;
    		}
    	} else if( $currency == DEFAULT_CURRENCY ) {
    		if(  $amount > $venueAmount ) {
    			$isError = true;
    		}
    	} else { // not setup min max and currency != usd
    		$exchangeRate = new ExchangeRateService();
    		$input = array('to_currency' => $currency );
    		$exchangeRateInfo = array();
    		$listExchangeRate = $exchangeRate->getExchangeRatebyCurrency( $input );
	    	if( empty( $listExchangeRate ) == false ) {
	    		$exchangeRateInfo = $listExchangeRate['List'][0];
	    		$rate = $exchangeRateInfo['Rate']* ( $venueAmount );
	    		$minAmount = $rate - ( $exchangeRateInfo['Delta']*$rate );
	    		$maxAmount = $rate + ( $exchangeRateInfo['Delta']*$rate );
	    		if( ( $amount + $fee ) > $maxAmount || ( $amount + $fee ) < $minAmount ){
	    			$isError = true;
	    		}
	    	} else {
	    		$isError = true;
	    	}
    	}
    	return $isError;
    	//
	}
	/**
	 * Description: validate numvber 
	 *  @param int $amount
	 * @return string
	 */
	public static function validateNumber( $amount ) {
		if ( isset( $amount ) ) {
			if ( is_numeric( $amount ) ) {
				return $amount;
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}
	/**
	 * Check object id base on object type
	 * @return boolean
	 */
	public static function CheckObjectId($objectType, $objectId) {
	    $isError = false;
	    if ( empty( $objectType ) == false && $objectType != ALL && empty( $objectId ) == true ) {
	        $isError = true;
	    }
	
	    return $isError;
	}
	/**
	 * Check object id base on object type
	 * @return boolean
	 */
	public static function CheckProductObjectId($objectType, $objectId) {
	    $isError = false;
	    if ( empty( $objectType ) == false && $objectType != GLOBAL_TYPE && empty( $objectId ) == true ) {
	        $isError = true;
	    }
	
	    return $isError;
	}
	public static function CheckExistCode($id, $code ) {
		$isError = false;
		$mdlReferralFile = new ReferralFile();
		$row = $mdlReferralFile->fetchReferralFileByCode( $code );
		if ( empty( $row ) == false ) {
			if ( $id != $row['id'] ){
				$isError = true; 
			}
		}
		return $isError;
	}
}
