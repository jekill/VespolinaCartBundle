<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * (c) Daniel Kucharski <daniel@xerias.be>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Vespolina\CartBundle\Model;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use Vespolina\CartBundle\Model\CartableItemInterface;
use Vespolina\CartBundle\Model\CartInterface;
use Vespolina\CartBundle\Model\CartItemInterface;
use Vespolina\CartBundle\Model\CartManagerInterface;

abstract class CartManager implements CartManagerInterface
{
    protected $cartClass;
    protected $cartItemClass;

    function __construct($cartClass, $cartItemClass)
    {
        $this->cartClass = $cartClass;
        $this->cartItemClass = $cartItemClass;
    }

    /**
     * @inheritdoc
     */
    public function createCart($cartType = 'default')
    {
        $cart = new $this->cartClass($cartType);
        $this->initCart($cart);

        return $cart;
    }

    /**
     * @inheritdoc
     */
    public function createItem(CartableItemInterface $cartableItem = null)
    {
        $cartItem = new $this->cartItemClass($cartableItem);
        $this->initCartItem($cartItem);

        return $cartItem;
    }

    public function initCart(CartInterface $cart)
    {
        //Set default state (for now we set it to "open")
        $cart->setState(Cart::STATE_OPEN);
    }

    public function initCartItem(CartItemInterface $cartItem)
    {
        //Default cart item description to the product name
        if ($cartableItem = $cartItem->getCartableItem()) {

            $cartItem->setName($cartableItem->getName());
        }
    }

}