<?php

/**
 */

class My_Controller_Action_Helper_Cart extends Zend_Controller_Action_Helper_Abstract
{
    // Session object
    protected $_session;

    // Session namespace
    const SESSION_NAMESPACE = 'MY_SESSION';

    // These are the regular expression rules that we use to validate the product ID and product name
    public $product_id_rules	= '\.a-z0-9_-'; // alpha-numeric, dashes, underscores, or periods
    public $product_name_rules	= '\.\:\-_() a-z0-9'; // alpha-numeric, dashes, underscores, colons or periods

    // Private variables.  Do not change!
    private $_cart_contents	= array();

    public function __construct( )
    {
        $config = Zend_Registry::get('config');
        $config = $config['session'];
        $this->_session = new Zend_Session_Namespace(self::SESSION_NAMESPACE);
        $this->_session->setExpirationSeconds($config['remember_me_seconds']);

        // Grab the shopping cart array from the session table, if it exists
        if ( $this->_session->cart_contents !== FALSE) {
            $this->_cart_contents = $this->_session->cart_contents;
        }
        else {
            // No cart exists so we'll set some base values
            $this->_cart_contents['cart_total'] = 0;
            $this->_cart_contents['total_items'] = 0;
        }

    }

    /**
     * Insert items into the cart and save it to the session table
     *
     * @access	public
     * @param	array
     * @return	bool
     */

    public function addItem( $items = array()) {
        // Was any cart data passed? No? Bah...
        if ( ! is_array($items) OR count($items) == 0) {
            die( 'The insert method must be passed an array containing data.');
            return FALSE;
        }

        // You can either insert a single product using a one-dimensional array,
        // or multiple products using a multi-dimensional one. The way we
        // determine the array type is by looking for a required array key named "id"
        // at the top level. If it's not found, we will assume it's a multi-dimensional array.

        $save_cart = FALSE;
        if (isset($items[CART_ID])) {
            if ($this->_insert($items) == TRUE) {
                $save_cart = TRUE;
            }
        } else {
            foreach ($items as $val) {
                if (is_array($val) AND isset($val[CART_ID])) {
                    if ($this->_insert($val) == TRUE) {
                        $save_cart = TRUE;
                    }
                }
            }
        }

        // Save the cart data if the insert was successful
        if ($save_cart == TRUE) {
            $this->_save_cart();
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Insert
     *
     * @access	private
     * @param	array
     * @return	bool
     */
    private function _insert($items = array()) {

        if ( ! is_array($items) OR count($items) == 0) {
            return FALSE;
        }

        // --------------------------------------------------------------------

        if ( ! isset($items[CART_ID])
                OR ! isset($items[CART_QTY])
                OR ! isset($items[CART_PRICE])
                OR ! isset($items[CART_NAME])) {
            return FALSE;
        }

        // --------------------------------------------------------------------

        // Prep the quantity. It can only be a number.  Duh...
        $items[CART_QTY] = trim(preg_replace('/([^0-9])/i', '', $items[CART_QTY]));
        // Trim any leading zeros
        $items[CART_QTY] = trim(preg_replace('/(^[0]+)/i', '', $items[CART_QTY]));

        if ( ! is_numeric($items[CART_QTY])
                OR $items[CART_QTY] == 0) {
            return FALSE;
        }

        // --------------------------------------------------------------------

        if ( ! preg_match("/^[".$this->product_id_rules."]+$/i", $items[CART_ID])) {
            return FALSE;
        }

        if ( ! preg_match("/^[".$this->product_name_rules."]+$/i", $items[CART_NAME])){
            return FALSE;
        }

        $items[CART_PRICE] = trim(preg_replace('/([^0-9\.])/i', '', $items[CART_PRICE]));
        $items[CART_PRICE] = trim(preg_replace('/(^[0]+)/i', '', $items[CART_PRICE]));

        if ( ! is_numeric($items[CART_PRICE])) {
            return FALSE;
        }

        if( isset($this->_cart_contents[$items[CART_ID]])) {
            $this->_cart_contents[$items[CART_ID]][CART_QTY]   = $items[CART_QTY];
            $this->_cart_contents[$items[CART_ID]][CART_PRICE] = $items[CART_PRICE];
            $this->_cart_contents[$items[CART_ID]][CART_SUB_TOTAL] = $items[CART_QTY] + $items[CART_PRICE];
            	
        } else {
            $this->_cart_contents[$items[CART_ID]][CART_ID] = CART_ID;
            	
            foreach ($items as $key => $val) {
                $this->_cart_contents[$items[CART_ID]][$key] = $val;
            }
        }

        // Woot!
        return TRUE;
    }

    /**
     * Update the cart
     *
     * This function permits the quantity of a given item to be changed.
     * Typically it is called from the "view cart" page if a user makes
     * changes to the quantity before checkout. That array must contain the
     * product ID and quantity for each item.
     *
     * @access	public
     * @param	array
     * @param	string
     * @return	bool
     */
    public function update($items = array())
    {
        // Was any cart data passed?
        if ( ! is_array($items)
                OR count($items) == 0) {
            return FALSE;
        }
        	
        // You can either update a single product using a one-dimensional array,
        // or multiple products using a multi-dimensional one.  The way we
        // determine the array type is by looking for a required array key named "id".
        // If it's not found we assume it's a multi-dimensional array
        $save_cart = FALSE;
        if (isset($items[CART_ID]) AND isset($items[CART_QTY])) {
            if ($this->_update($items) == TRUE) {
                $save_cart = TRUE;
            }
        } else {
            foreach ($items as $val) {
                if (is_array($val)
                        AND isset($val[CART_ID])
                        AND isset($val[CART_QTY])) {
                    if ($this->_update($val) == TRUE) {
                        $save_cart = TRUE;
                    }
                }
            }
        }

        // Save the cart data if the insert was successful
        if ($save_cart == TRUE) {
            $this->_save_cart();
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Update the cart
     *
     * This function permits the quantity of a given item to be changed.
     * Typically it is called from the "view cart" page if a user makes
     * changes to the quantity before checkout. That array must contain the
     * product ID and quantity for each item.
     *
     * @access	private
     * @param	array
     * @return	bool
     */
    private function _update($items = array())
    {
        // Without these array indexes there is nothing we can do
        if ( ! isset($items[CART_QTY])
                OR ! isset($items[CART_ID])
                OR ! isset($this->_cart_contents[$items[CART_ID]])) {
            return FALSE;
        }

        // Prep the quantity
        $items[CART_QTY] = preg_replace('/([^0-9])/i', '', $items[CART_QTY]);
        // Is the quantity a number?
        if ( ! is_numeric($items[CART_QTY])) {
            return FALSE;
        }
        // Is the new quantity different than what is already saved in the cart?
        // If it's the same there's nothing to do
        if ($this->_cart_contents[$items[CART_ID]][CART_QTY] == $items[CART_QTY]) {
        return FALSE;
        }

        // Is the quantity zero?  If so we will remove the item from the cart.
        // If the quantity is greater than zero we are updating
        if ($items[CART_QTY] == 0) {
            unset($this->_cart_contents[$items[CART_ID]]);
        } else {
            $this->_cart_contents[$items[CART_ID]][CART_QTY] += $items[CART_QTY];
        }

        return TRUE;
    }

    /**
     * Save the cart array to the session DB
     *
     * @access	private
     * @return	bool
     */
    private function _save_cart() {
        // Unset these so our total can be calculated correctly below
        unset($this->_cart_contents['total_items']);
        unset($this->_cart_contents['cart_total']);

        // Lets add up the individual prices and set the cart sub-total
        $total = 0;
        foreach ($this->_cart_contents as $key => $val) {
            // We make sure the array contains the proper indexes
            if ( ! is_array($val)
                    OR ! isset($val[CART_PRICE])
                    OR ! isset($val[CART_QTY])) {
                continue;
            }
            $total += $val[CART_PRICE] * $val[CART_QTY];
            // Set the subtotal
            $this->_cart_contents[$key][CART_SUB_TOTAL] = $val[CART_PRICE] * $val[CART_QTY];
            	
        }
        // Set the cart total and total items.
        $this->_cart_contents['total_items'] = count($this->_cart_contents);
        $this->_cart_contents['cart_total'] = $total;
        // Is our cart empty?  If so we delete it from the session
        if (count($this->_cart_contents) <= 2) {
            unset($this->_session->cart_contents);
            // Nothing more to do... coffee time!
            return FALSE;
        }
        // If we made it this far it means that our cart has data.
        // Let's pass it to the Session class so it can be stored
        $this->_session->cart_contents = $this->_cart_contents;
        // Woot!
        return TRUE;
    }

    public function removeItem($id) {
        // Without these array indexes there is nothing we can do
        if (! isset($this->_cart_contents[$id])) {
            return FALSE;
        }
        unset($this->_cart_contents[$id]);
        return $this->_save_cart();
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $id
     */
    public function checkExist($id) {
        return isset($this->_cart_contents[$id]) ? TRUE : FALSE;
    }

    /**
     * Cart Total
     *
     * @access	public
     * @return	integer
     */
    public function total() {
        return $this->_cart_contents['cart_total'];
    }

    /**
     * Total Items
     *
     * Returns the total item count
     *
     * @access	public
     * @return	integer
     */
    public function total_items() {
        return $this->_cart_contents['total_items'] ? $this->_cart_contents['total_items'] : '0';
    }

    /**
     * Cart Contents
     *
     * Returns the entire cart array
     *
     * @access	public
     * @return	array
     */
    public function contents() {
        $cart = $this->_cart_contents;

        // Remove these so they don't create a problem when showing the cart table
        unset($cart['total_items']);
        unset($cart['cart_total']);

        return $cart;
    }

    /**
     * Has options
     *
     * Returns TRUE if the rowid passed to this function correlates to an item
     * that has options associated with it.
     *
     * @access	public
     * @return	array
     */
    public function has_options($rowid = '') {
        if ( ! isset($this->_cart_contents[$rowid]['options'])
                OR count($this->_cart_contents[$rowid]['options']) === 0)
        {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Product options
     *
     * Returns the an array of options, for a particular product row ID
     *
     * @access	public
     * @return	array
     */
    public function product_options( $cid = '')
    {
        if ( ! isset($this->_cart_contents[$cid]['options']))
        {
            return array();
        }

        return $this->_cart_contents[$cid]['options'];
    }

    /**
     * Format Number
     *
     * Returns the supplied number with commas and a decimal point.
     *
     * @access	public
     * @return	integer
     */
    public function format_number($n = '')	{
        if ($n == '') {
            return '';
        }
        // Remove anything that isn't a number or decimal point.
        $n = trim(preg_replace('/([^0-9\.])/i', '', $n));
        return number_format($n, 2, '.', ',');
    }

    // --------------------------------------------------------------------

    /**
     * Destroy the cart
     *
     * Empties the cart and kills the session
     *
     * @access	public
     * @return	null
     */
    public function destroy() {
        unset($this->_cart_contents);
        $this->_cart_contents['cart_total'] = 0;
        $this->_cart_contents['total_items'] = 0;
        unset($this->_session->cart_contents);
    }

}