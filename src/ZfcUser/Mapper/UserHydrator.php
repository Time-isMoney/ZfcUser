<?php

namespace ZfcUser\Mapper;

use Zend\Crypt\Password\PasswordInterface as CryptoInterface;
use Zend\Stdlib\Hydrator\ClassMethods;
use ZfcUser\Entity\UserInterface as UserEntityInterface;

class UserHydrator extends ClassMethods
{
    /**
     * @var CryptoInterface
     */
    private $crypto;

    /**
     * @param CryptoInterface $crypto
     * @param bool|array      $underscoreSeparatedKeys
     */
    public function __construct(CryptoInterface $crypto, $underscoreSeparatedKeys = true)
    {
        parent::__construct($underscoreSeparatedKeys);
        $this->crypto = $crypto;
    }

    /**
     * Extract values from an object
     *
     * @param  UserEntityInterface $object
     * @return array
     * @throws Exception\InvalidArgumentException
     */
    public function extract($object)
    {
        $this->guardUserObject($object);
        $data = parent::extract($object);
        return $this->mapField('id', 'user_id', $data);
    }

    /**
     * Hydrate $object with the provided $data.
     *
     * @param  array               $data
     * @param  UserEntityInterface $object
     * @return UserEntityInterface
     * @throws Exception\InvalidArgumentException
     */
    public function hydrate(array $data, $object)
    {
        $this->guardUserObject($object);
        $data = $this->mapField('user_id', 'id', $data);
        if (isset($data['password'])) {
            $data['password'] = $this->crypto->create($data['password']);
        }
        if (isset($data['state'])) {
            $data['state'] = (int) $data['state'];
        }
        return parent::hydrate($data, $object);
    }

    /**
     * @return CryptoInterface
     */
    public function getCrypto()
    {
        return $this->crypto;
    }

    /**
     * Remap an array key
     *
     * @param  string $keyFrom
     * @param  string $keyTo
     * @param  array  $array
     * @return array
     */
    protected function mapField($keyFrom, $keyTo, array $array)
    {
        if (isset($array[$keyFrom])) {
            $array[$keyTo] = $array[$keyFrom];
        }
        unset($array[$keyFrom]);
        return $array;
    }

    /**
     * Ensure $object is an UserEntityInterface
     *
     * @param  mixed $object
     * @throws Exception\InvalidArgumentException
     */
    protected function guardUserObject($object)
    {
        if (!$object instanceof UserEntityInterface) {
            throw new Exception\InvalidArgumentException(
                '$object must be an instance of ZfcUser\Entity\UserInterface'
            );
        }
    }
}
