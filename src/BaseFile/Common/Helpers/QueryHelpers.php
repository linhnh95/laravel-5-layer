<?php
/**
 * Created by PhpStorm.
 * User: LINH
 * Date: 8/27/2019
 * Time: 11:58 AM
 */

namespace App\Common\Helpers;


use Illuminate\Database\Eloquent\Model;

class QueryHelpers
{
    /**
     * @var
     */
    private static $model;

    /**
     * @var
     */
    private static $table;

    /**
     * @var null
     */

    /**
     * @var null
     */
    public static $query = null;

    /**
     * @param Model $model
     */
    public function setQuery(Model $model)
    {
        self::$table = $model->getTable();
        self::$model = $model->newModelQuery();
    }

    /**
     * @return null
     */
    public function getQuery()
    {
        return self::$query;
    }

    /**
     * @param array $fields
     *
     * @return bool
     */
    public function build(array $fields = ['*'])
    {
        if (!self::$model) {
            return false;
        }
        self::$query = self::$model->select($fields);
    }


    /**
     * @param array $withs
     *
     * @return bool
     */
    public function with(array $withs = [])
    {
        if (empty($withs)) {
            return false;
        }
        if (!self::$query) {
            return false;
        }
        foreach ($withs as $with) {
            self::$query->with($with);
        }
    }

    /**
     * @param $params
     * @param null $paramsToRelation
     * @param null $paramsToTable
     * @param null $paramsToField
     * @param null $paramsToCondition
     *
     * @return bool|null
     */
    public function buildWhere($params, $paramsToRelation = null, $paramsToTable = null, $paramsToField = null, $paramsToCondition = null)
    {
        if (!self::$model) {
            return false;
        }
        self::$query = self::$model;
        $this->where($params, $paramsToRelation, $paramsToTable, $paramsToField, $paramsToCondition);
        return self::$query;
    }

    /**
     * @param $params
     * @param array $groups
     * @param array $conditions
     *
     * @return bool
     */
    public function buildWhereGroup($params, array $groups = [], $conditions = [])
    {
        if (empty($groups)) {
            return false;
        }
        foreach ($groups as $key => $group) {
            if (!isset($params[$key])) {
                continue;
            }
            self::$query->where(function ($q) use ($params, $group, $key, $conditions) {
                foreach ($group as $item) {
                    $valSearch = isset($item['value']) ? ($item['value'] === 'null' ? null : $item['value']) : $params[$key];
                    if ($item['is_relation']) {
                        $q->whereHas($item['relation'], function ($q) use ($item, $key, $valSearch, $conditions) {
                            return $this->whereRelationsGroup($q, $item, $key, $valSearch, $conditions);
                        });
                    } else {
                        $this->whereRelationsGroup($q, $item, $key, $valSearch, $conditions);
                    }
                }
            });
        }
        return true;
    }

    /**
     * @param $q
     * @param $item
     * @param $key
     * @param $valSearch
     * @param $conditions
     *
     * @return mixed
     */
    private function whereRelationsGroup($q, $item, $key, $valSearch, $conditions)
    {
        if ($item['condition'] == 'like') {
            $valSearch = "%" . $valSearch . "%";
        }
        if (isset($conditions[$key])) {
            if ($conditions[$key] == 'OR') {
                if (isset($item['is_group'])) {
                    $q->orWhere(function ($q) use ($valSearch, $item) {
                        return $this->whereRelationsValueGroup($q, $valSearch, $item);
                    });
                } else {
                    $q->orWhere($item['table'] . '.' . $item['key'], $item['condition'], $valSearch);
                }
            }else{
                if (isset($item['is_group'])) {
                    $q->where(function ($q) use ($valSearch, $item) {
                        return $this->whereRelationsValueGroup($q, $valSearch, $item);
                    });
                } else {
                    $q->where($item['table'] . '.' . $item['key'], $item['condition'], $valSearch);
                }
            }
        }else{
            if (isset($item['is_group'])) {
                $q->where(function ($q) use ($valSearch, $item) {
                    return $this->whereRelationsValueGroup($q, $valSearch, $item);
                });
            } else {
                $q->where($item['table'] . '.' . $item['key'], $item['condition'], $valSearch);
            }
        }
        return $q;
    }

    /**
     * @param $q
     * @param $valSearch
     * @param $item
     *
     * @return mixed
     */
    private static function whereRelationsValueGroup($q, $valSearch, $item)
    {
        if (!is_array($valSearch)) {
            return false;
        }
        if (!isset($valSearch['items']) || !is_array($valSearch['items'])) {
            return false;
        }
        foreach ($valSearch['items'] as $vItem) {
            $value = isset($vItem['value']) ? ($vItem['value'] === 'null' ? null : $vItem['value']) : '';
            if ($valSearch['condition'] == 'OR') {
                $q->orWhere($item['table'] . '.' . $item['key'], $vItem['condition'], $value);
            } else {
                $q->where($item['table'] . '.' . $item['key'], $vItem['condition'], $value);
            }
        }
        return $q;
    }

    /**
     * @param $params
     * @param null $paramsToRelation
     * @param null $paramsToTable
     * @param null $paramsToField
     * @param null $paramsToCondition
     * @param array $scopes
     */
    public function where($params, $paramsToRelation = null, $paramsToTable = null, $paramsToField = null, $paramsToCondition = null, array $scopes = [])
    {
        if (!empty($scopes)) {
            foreach ($scopes as $scope) {
                self::$query->$scope();
            }
        }
        if ($paramsToRelation) {
            $this->buildWhereRelation($params, $paramsToRelation, $paramsToTable, $paramsToField, $paramsToCondition);
        }
        $this->buildWhereInTable($params, $paramsToRelation, $paramsToTable, $paramsToField, $paramsToCondition);
    }

    /**
     * @param $params
     * @param $paramsToRelation
     * @param $paramsToTable
     * @param $paramsToField
     *
     * @param $paramsToCondition
     */
    private function buildWhereInTable($params, $paramsToRelation, $paramsToTable, $paramsToField, $paramsToCondition)
    {
        foreach ($params as $key => $value) {
            if ($value === '') {
                continue;
            }

            if (!isset($paramsToRelation[$key]) && !isset($paramsToField[$key]) && !isset($paramsToTable[$key]) && !isset($paramsToCondition[$key])) {
                continue;
            }
            if ($paramsToRelation[$key] === self::$table) {
                if ($paramsToCondition[$key] === 'not_exist') {
                    self::$query->doesntHave($value);
                } elseif ($paramsToCondition[$key] === 'exist') {
                    self::$query->whereHas($value);
                } else {
                    if (is_array($value)) {
                        if ($paramsToCondition[$key] == '!=') {
                            self::$query->whereNotIn($paramsToField[$key], $value);
                        } else {
                            self::$query->whereIn($paramsToField[$key], $value);
                        }
                    } else {
                        $valSearch = $value === 'null' ? null : $value;
                        if ($paramsToCondition[$key] == 'like') {
                            $valSearch = "%" . $valSearch . "%";
                        }
                        self::$query->where($paramsToField[$key], $paramsToCondition[$key], $valSearch);
                    }
                }
            }
        }
    }

    /**
     * Build Query With Relations
     *
     * @param $params
     * @param $paramsToRelation
     * @param $paramsToTable
     * @param $paramsToField
     * @param $paramsToCondition
     *
     * @return bool
     */
    private function buildWhereRelation($params, $paramsToRelation, $paramsToTable, $paramsToField, $paramsToCondition)
    {
        $relations = self::parseRelations($paramsToRelation, self::$table);
        if (!empty($relations)) {
            foreach ($relations as $key => $relation) {
                $listFields = [];
                foreach ($relation as $field) {
                    if (isset($params[$field])) {
                        array_push($listFields, $field);
                    }
                }
                if (empty($listFields)) {
                    continue;
                }
                self::$query->whereHas($key, function ($q) use ($params, $listFields, $paramsToField, $paramsToTable, $paramsToCondition) {
                    foreach ($params as $pKey => $value) {
                        if (!in_array($pKey, $listFields)) {
                            continue;
                        }
                        if ($value == '') {
                            continue;
                        }
                        if (!isset($paramsToField[$pKey]) && !isset($paramsToTable[$pKey]) && !isset($paramsToCondition[$pKey])) {
                            continue;
                        }
                        if (is_array($value)) {
                            if ($paramsToCondition[$pKey] == '!=') {
                                $q->whereNotIn($paramsToTable[$pKey] . '.' . $paramsToField[$pKey], $value);
                            } else {
                                $q->whereIn($paramsToTable[$pKey] . '.' . $paramsToField[$pKey], $value);
                            }
                        } else {
                            $valSearch = $value === 'null' ? null : $value;
                            if ($paramsToCondition[$pKey] == 'like') {
                                $valSearch = "%" . $valSearch . "%";
                            }
                            $q->where($paramsToTable[$pKey] . '.' . $paramsToField[$pKey], $paramsToCondition[$pKey], $valSearch);
                        }
                    }
                });
            }
        }
        return false;
    }

    /**
     * Convert relations
     *
     * @param $data
     * @param $table
     *
     * @return array
     */
    private function parseRelations($data, $table)
    {
        if (empty($data)) {
            return [];
        }
        $list = [];
        foreach ($data as $key => $value) {
            if ($table == $value) {
                continue;
            }
            if (!isset($list[$value])) {
                $list[$value] = [];
            }
            array_push($list[$value], $key);
        }
        return $list;
    }
}
