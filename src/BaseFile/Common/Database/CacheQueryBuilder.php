<?php
/**
 * Created by PhpStorm.
 * User: LINH
 * Date: 8/29/2019
 * Time: 2:09 PM
 */

namespace App\Common\Database;


trait CacheQueryBuilder
{
    /**
     * Get a new query builder instance for the connection.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function newBaseQueryBuilder()
    {
        $conn = $this->getConnection();

        $grammar = $conn->getQueryGrammar();

        return new Builder($conn, $grammar, $conn->getPostProcessor());
    }
}
