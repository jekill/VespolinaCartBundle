<?php
/**
 * (c) Vespolina Project http://www.vespolina-project.org
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Vespolina\CartBundle\Document;

use Symfony\Component\DependencyInjection\Container;
use Doctrine\ODM\MongoDB\DocumentManager;

use Vespolina\CartBundle\Document\Cart;
use Vespolina\CartBundle\Model\CartableItemInterface;
use Vespolina\CartBundle\Model\CartInterface;
use Vespolina\CartBundle\Model\CartItemInterface;
use Vespolina\CartBundle\Model\CartManager as BaseCartManager;
/**
 * @author Daniel Kucharski <daniel@xerias.be>
 * @author Richard Shank <develop@zestic.com>
 */
class CartManager extends BaseCartManager
{
    protected $cartClass;
    protected $cartRepo;
    protected $dm;
    protected $primaryIdentifier;

    public function __construct(DocumentManager $dm, $cartClass, $cartItemClass)
    {
        $this->dm = $dm;

        $this->cartClass = $cartClass;
        $this->cartRepo = $this->dm->getRepository($cartClass);

        parent::__construct($cartClass, $cartItemClass);
    }

    public function addItemToCart(CartInterface $cart, CartableItemInterface $cartableItem)
    {
        $item = $this->createItem($cartableItem);
        $cart->addItem($item);
        // todo: just update this cart, don't flush everything
        if ($cart->getId() !== $cart->getId()) {
            $this->dm->createQueryBuilder($this->cartClass)
                ->findAndUpdate()
                ->field('id')->equals($cart->getId())
                ->field('items')->set($cart->getItems())
                ->getQuery()
                ->execute()
            ;
        } else {
            $this->updateCart($cart);
        }
        return $item;
    }

    public function findOpenCartByOwner($owner)
    {
        return $this->dm->createQueryBuilder($this->cartClass)
                    ->field('owner')->equals($owner)
                    ->field('state')->equals(Cart::STATE_OPEN)
                    ->getQuery()
                    ->getSingleResult();
    }

    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->cartRepo->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function findCartById($id)
    {
        return $this->cartRepo->find($id);
    }

    /**
     * @inheritdoc
     */
    public function findCartByIdentifier($name, $code)
    {
        return;
    }

    /**
     * @inheritdoc
     */
    public function updateCart(CartInterface $cart, $andFlush = true)
    {
        $this->dm->persist($cart);
        if ($andFlush) {
            $this->dm->flush();
        }
    }
}
