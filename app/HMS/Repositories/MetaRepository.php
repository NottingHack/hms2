<?php

namespace HMS\Repositories;

interface MetaRepository
{
    /**
     * Determine if the given setting value exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key);

    /**
     * Get the specified setting value.
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Get the specified setting value as an int.
     *
     * @param string $key
     * @param int|null $default
     *
     * @return int|null
     */
    public function getInt($key, ?int $default = null);

    /**
     * Set a given setting value.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function set($key, $value = null);

    /**
     * Forget current setting value.
     *
     * @param string $key
     *
     * @return void
     */
    public function forget($key);

    /**
     * @param int $perPage
     * @param string $pageName
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginateAll($perPage = 15, $pageName = 'page');
}
