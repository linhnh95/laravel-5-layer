<?php
/**
 * Created by PhpStorm.
 * User: LINH
 * Date: 8/29/2019
 * Time: 2:06 PM
 */

namespace App\Common\Database;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\Cache;

class Builder extends QueryBuilder
{
    /**
     * Run the query as a "select" statement against the connection.
     *
     * @return array
     */
    protected function runSelect()
    {
        return Cache::store('request')->remember($this->getCacheKey(), 1, function() {
            return parent::runSelect();
        });
    }

    /**
     * Returns a Unique String that can identify this Query.
     *
     * @return string
     */
    protected function getCacheKey()
    {
        return json_encode([
            $this->toSql() => $this->getBindings()
        ]);
    }
}
