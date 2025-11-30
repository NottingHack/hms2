<?php

namespace HMS\Repositories\Doctrine;

use Doctrine\ORM\EntityRepository;
use HMS\Entities\Meta;
use HMS\Repositories\MetaRepository;
use LaravelDoctrine\ORM\Pagination\PaginatesFromRequest;

class DoctrineMetaRepository extends EntityRepository implements MetaRepository
{
    use PaginatesFromRequest;

    /**
     * Determine if the given setting value exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return parent::findOneByKey($key) ? true : false;
    }

    /**
     * Get the specified setting value.
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $meta = parent::findOneByKey($key);

        return is_null($meta) ? $default : $meta->getValue();
    }

    /**
     * Get the specified setting value as an int.
     *
     * @param string $key
     * @param int|null $default
     *
     * @return int|null
     */
    public function getInt($key, ?int $default = null)
    {
        $meta = parent::findOneByKey($key);

        return is_null($meta) ? $default : (int) $meta->getValue();
    }

    /**
     * Set a given setting value.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function set($key, $value = null)
    {
        $meta = parent::findOneByKey($key);
        if (! $meta) {
            $meta = new Meta;
            $meta->create($key);
        }
        $meta->setValue($value);
        $this->_em->persist($meta);
        $this->_em->flush();
    }

    /**
     * Forget current setting value.
     *
     * @param string $key
     *
     * @return void
     */
    public function forget($key)
    {
        $meta = parent::findOneByKey($key);
        if ($meta) {
            $this->_em->remove($meta);
            $this->_em->flush();
        }
    }
}
