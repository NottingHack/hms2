<?php

namespace HMS\Repositories;

use HMS\Entities\Meta;
use Doctrine\ORM\EntityRepository;
use LaravelDoctrine\ORM\Pagination\Paginatable;

class MetaRepository extends EntityRepository
{
    use Paginatable;

    /**
     * Determine if the given setting value exists.
     *
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return $this->findOneByKey($key) ? true : false;
    }

    /**
     * Get the specified setting value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $meta = $this->findOneByKey($key);

        return is_null($meta) ? $default : $meta->getValue();
    }

    /**
     * Set a given setting value.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value = null)
    {
        $meta = $this->findOneByKey($key);
        if ( ! $meta) {
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
     * @return void
     */
    public function forget($key)
    {
        $meta = $this->findOneByKey($key);
        if ($meta) {
            $this->_em->remove($meta);
            $this->_em->flush();
        }
    }
}
