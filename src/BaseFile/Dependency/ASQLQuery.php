<?php
/**
 * Created by PhpStorm.
 * User: LINH
 * Date: 10/7/2019
 * Time: 8:51 AM
 */

namespace App\Dependency;


use App\Common\QueryHelpers;
use App\Exceptions\ServerException;
use App\Exceptions\ValidationException;
use App\Helpers\PriceHelpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

abstract class ASQLQuery
{
    /**
     * @var Model
     */
    public $model;

    /**
     * @var
     */
    private $withs;

    /**
     * @var
     */
    private $paramsToRelation;

    /**
     * @var
     */
    private $paramsToTable;

    /**
     * @var
     */
    private $paramsToField;

    /**
     * @var
     */
    private $paramsToCondition;

    /**
     * @var array
     */
    private $pivotRelations = [];

    /**
     * @var array
     */
    private $scopes = [];

    /**
     * @var array
     */
    private $whereGroups = [];

    /**
     * @var array
     */
    private $whereGroupConditions = [];

    /**
     * @var string
     */
    private $withCount = [];

    /**
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param $methods
     * @param $raw
     *
     * @return mixed
     *
     * @throws ServerException
     */
    public function queryRaw($methods, $raw)
    {
        try{
            $result = DB::$methods($raw);
            return $result;
        }catch (\Exception $exception){
            throw new ServerException($exception->getMessage(), $exception);
        }
    }

    /**
     * @param array $params
     * @param array $fields
     * @param bool $getAll
     *
     * @return mixed
     *
     * @throws ServerException
     */
    public function getAll(array $params = [], array $fields = ['*'], bool $getAll = false)
    {
        try{
            $params = $this->clearNullValue($params);
            $query = $this->query($params, $fields);
            if (isset($params['distinct'])) {
                $query->distinct($params['distinct']);
            }
            if (isset($params['limit'])) {
                $query->take($params['limit']);
            }
            if ($getAll) {
                return $query->get();
            } else {
                $perPage = isset($params['per_page']) ? $params['per_page'] : 1;
                return $query->paginate($perPage);
            }
        }catch (\Exception $exception){
            throw new ServerException($exception->getMessage(), $exception);
        }
    }

    /**
     * @param array $params
     * @param array $fields
     *
     * @return mixed
     *
     * @throws ServerException
     */
    public function getCursorBigData(array $params = [], array $fields = ['*'])
    {
        try{
            $params = $this->clearNullValue($params);
            $query = $this->query($params, $fields);
            return $query->cursor();
        }catch (\Exception $exception){
            throw new ServerException($exception->getMessage(), $exception);
        }
    }

    /**
     * @param int $id
     * @param array $params
     * @param array $fields
     *
     * @return mixed
     *
     * @throws ServerException
     */
    public function getOne(int $id, array $params = [], array $fields = ['*'])
    {
        try{
            $params = $this->clearNullValue($params);
            $query = $this->query($params, $fields);
            $query->where('id', $id);
            return $query->first();
        }catch (\Exception $exception){
            throw new ServerException($exception->getMessage(), $exception);
        }
    }

    /**
     * @param array $params
     * @param array $fields
     *
     * @return mixed
     *
     * @throws ServerException
     */
    public function getOneBy(array $params = [], array $fields = ['*'])
    {
        try{
            $params = $this->clearNullValue($params);
            $query = $this->query($params, $fields);
            return $query->first();
        }catch (\Exception $exception){
            throw new ServerException($exception->getMessage(), $exception);
        }
    }

    /**
     * @param array $params
     * @param array $fields
     *
     * @return mixed
     *
     * @throws ServerException
     */
    public function getRandomBy(array $params, array $fields = ['*'])
    {
        try{
            $params = $this->clearNullValue($params);
            $query = $this->query($params, $fields)->inRandomOrder();
            return $query->first();
        }catch (\Exception $exception){
            throw new ServerException($exception->getMessage(), $exception);
        }
    }

    /**
     * @param array $params
     * @return mixed
     *
     * @throws ServerException
     */
    public function countBy(array $params = [])
    {
        try{
            $params = $this->clearNullValue($params);
            $query = $this->query($params, ['id']);
            return $query->count();
        }catch (\Exception $exception){
            throw new ServerException($exception->getMessage(), $exception);
        }
    }

    /**
     * @param string $field
     * @param array $params
     * @param string $type
     *
     * @return mixed
     *
     * @throws ServerException
     */
    public function aggregatesBy(string $field, array $params = [], string $type = 'sum')
    {
        try{
            $params = $this->clearNullValue($params);
            $query = $this->query($params, [$field]);
            return $query->$type($field);
        }catch (\Exception $exception){
            throw new ServerException($exception->getMessage(), $exception);
        }

    }

    /**
     * @param array $params
     * @return mixed
     * @throws ServerException
     */
    public function existBy(array $params = [])
    {
        try {
            $params = $this->clearNullValue($params);
            $query = $this->query($params, ['id']);
            return $query->exists();
        } catch (\Exception $exception) {
            throw new ServerException($exception->getMessage(), $exception);
        }

    }

    /**
     * @param array $params
     * @param bool $getData
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|Model|Model[]|null
     *
     * @throws ServerException
     */
    public function create(array $params = [], bool $getData = false)
    {
        try {
            $create = $this->formatParams($params);
            $result = $this->model::create($create);
            if ($getData) {
                if ($this->withs) {
                    $result = $this->model::with($this->withs)->find($result->id);
                } else {
                    $result = $this->model::find($result->id);
                }
            }
            $this->buildPivotRelations($result->id, $params);
            return $getData ? $result : $result->id;
        } catch (\Exception $exception) {
            throw new ServerException($exception->getMessage(), $exception);
        }
    }

    /**
     * @param array $params
     * @return bool
     *
     * @throws ServerException
     */
    public function insert(array $params = [])
    {
        try {
            if (empty($params)) {
                return false;
            }
            $insert = [];
            $i = 0;
            foreach ($insert as $data) {
                array_push($create, $data);
                if (count($create) == 1000 || count($create) == count($insert) || count($create) == (count($insert) - (1000 * $i))) {
                    $this->model->insert($create);
                    $create = [];
                    $i++;
                }
            }
            return $this->model->insert($insert);
        } catch (\Exception $exception) {
            throw new ServerException($exception->getMessage(), $exception);
        }
    }

    /**
     * @param int $id
     * @param array $params
     * @param bool $getData
     *
     * @return bool|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|Model|Model[]|null
     *
     * @throws ServerException
     */
    public function update(int $id, array $params = [], bool $getData = false)
    {
        try {
            $update = $this->formatParams($params);
            if (empty($update)) {
                return false;
            }
            $result = $this->model::where('id', $id)->update($update);
            if ($getData) {
                if ($this->withs) {
                    $result = $this->model::with($this->withs)->find($id);
                } else {
                    $result = $this->model::find($id);
                }
            }
            $this->buildPivotRelations($id, $params);
            return $result;
        } catch (\Exception $exception) {
            throw new ServerException($exception->getMessage(), $exception);
        }
    }

    /**
     * @param array $params
     * @param array $where
     * @param bool $getData
     *
     * @return bool|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|Model|Model[]|null
     *
     * @throws ServerException
     */
    public function updateBy(array $params = [], array $where = [], bool $getData = false)
    {
        try {
            $update = $this->formatParams($params);
            if (empty($where)) {
                return false;
            }
            if (empty($update)) {
                return false;
            }
            $query = new QueryHelpers();
            $query->setQuery($this->model);
            $result = $query->buildWhere($where, $this->paramsToRelation, $this->paramsToTable, $this->paramsToField, $this->paramsToCondition);
            $result->update($params);
            if ($getData) {
                if (isset($where['id'])) {
                    if ($this->withs) {
                        $result = $this->model::with($this->withs)->find($where['id']);
                    } else {
                        $result = $this->model::find($where['id']);
                    }
                }
            }
            return $result;
        } catch (\Exception $exception) {
            throw new ServerException($exception->getMessage(), $exception);
        }
    }

    /**
     * @param array $where
     * @param array $raws
     * @param bool $getData
     *
     * @return bool|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|Model|Model[]|null
     *
     * @throws ServerException
     */
    public function updateRaw(array $where = [], array $raws = [], bool $getData = false)
    {
        try {
            if (empty($raws)) {
                return false;
            }
            $query = new QueryHelpers();
            $query->setQuery($this->model);
            $result = $query->buildWhere($where, $this->paramsToRelation, $this->paramsToTable, $this->paramsToField, $this->paramsToCondition);
            $update = [];
            foreach ($raws as $key => $raw) {
                $update[$key] = DB::raw($raw);
            }
            $result->update($update);
            if ($getData) {
                if (isset($where['id'])) {
                    if ($this->withs) {
                        $result = $this->model::with($this->withs)->find($where['id']);
                    } else {
                        $result = $this->model::find($where['id']);
                    }
                }
            }
            return $result;
        } catch (\Exception $exception) {
            throw new ServerException($exception->getMessage(), $exception);
        }
    }

    /**
     * @param int $id
     * @return mixed
     *
     * @throws ValidationException
     */
    public function delete(int $id)
    {
        try {
            $delete = $this->model::where('id', $id)->delete();
            return $delete;
        } catch (\Exception $exception) {
            throw new ValidationException($exception->getMessage(), $exception);
        }
    }

    /**
     * @param array $params
     * @return bool|null
     *
     * @throws ServerException
     */
    public function deleteBy(array $params = [])
    {
        try {
            $query = new QueryHelpers();
            $query->setQuery($this->model);
            $result = $query->buildWhere($params, $this->paramsToRelation, $this->paramsToTable, $this->paramsToField, $this->paramsToCondition);
            $result->delete();
            return $result;
        } catch (\Exception $exception) {
            throw new ServerException($exception->getMessage(), $exception);
        }
    }

    /**
     * @param int $id
     * @return mixed
     *
     * @throws ServerException
     */
    public function forceDelete(int $id)
    {
        try {
            $this->buildPivotRelations($id, [], true);
            $delete = $this->model::withTrashed()->where('id', $id)->delete();
            return $delete;
        } catch (\Exception $exception) {
            throw new ServerException($exception->getMessage(), $exception);
        }
    }

    /**
     * @param array $params
     * @return bool|null
     * @throws ServerException
     */
    public function forceDeleteBy(array $params = [])
    {
        try {
            $query = new QueryHelpers();
            $query->setQuery($this->model);
            $result = $query->buildWhere($params, $this->paramsToRelation, $this->paramsToTable, $this->paramsToField, $this->paramsToCondition);
            $ids = $result->withTrashed()->select(['id'])->get();
            if (!empty($ids)) {
                $ids = $ids->pluck('id')->toArray();
                foreach ($ids as $id) {
                    $this->buildPivotRelations($id, [], true);
                }
            }
            $result->withTrashed()->delete();
            return $result;
        } catch (\Exception $exception) {
            throw new ServerException($exception->getMessage(), $exception);
        }
    }

    /**
     * @param int $id
     * @return mixed
     *
     * @throws ServerException
     */
    public function restore(int $id)
    {
        try {
            $delete = $this->model::withTrashed()->where('id', $id)->restore();
            return $delete;
        } catch (\Exception $exception) {
            throw new ServerException($exception->getMessage(), $exception);
        }
    }

    /**
     * @param array $params
     * @return bool|null
     *
     * @throws ServerException
     */
    public function restoreBy(array $params = [])
    {
        try {
            $query = new QueryHelpers();
            $query->setQuery($this->model);
            $result = $query->buildWhere($params, $this->paramsToRelation, $this->paramsToTable, $this->paramsToField, $this->paramsToCondition);
            $result->withTrashed()->restore();
            return $result;
        } catch (\Exception $exception) {
            throw new ServerException($exception->getMessage(), $exception);
        }
    }

    /**
     * @param $params
     *
     * @return array
     */
    public function formatParams(array $params = [])
    {
        if (empty($params)) {
            return $params;
        }
        $builder = $this->model->getConnection()->getSchemaBuilder();
        $listColumns = $builder->getColumnListing($this->model->getTable());
        $result = [];
        foreach ($listColumns as $column) {
            foreach ($params as $key => $value) {
                if ($column == $key) {
                    $result[$column] = $this->formatValueWithType($value, $column, $builder);
                }
            }
        }
        return $result;
    }

    /**
     * @param $value
     * @param $column
     * @param $builder
     *
     * @return int
     */
    private function formatValueWithType($value, $column, $builder)
    {
        $typeOfColumn = $builder->getColumnType($this->model->getTable(), $column);
        switch ($typeOfColumn) {
            case 'integer' :
                $value = $value !== null ? PriceHelpers::clearSpecialNumber($value) : 0;
                break;
            case 'bigint' :
                $value = $value !== null ? PriceHelpers::clearSpecialNumber($value) : 0;
                break;
            case 'float' :
                $value = $value !== null ? PriceHelpers::clearSpecialNumber($value) : 0;
                break;
            case 'boolean' :
                $value = PriceHelpers::clearSpecialNumber($value);
                break;
            default :

                break;
        }
        return $value;
    }

    /**
     * @param $params
     * @param $fields
     *
     * @return QueryHelpers
     */
    private function query($params, $fields)
    {
        $query = new QueryHelpers();
        $query->setQuery($this->model);
        $query->build($fields);
        if ($this->withs) {
            $query->with($this->withs);
        }
        $query->buildWhereGroup($params, $this->whereGroups, $this->whereGroupConditions);
        $query->where($params, $this->paramsToRelation, $this->paramsToTable, $this->paramsToField, $this->paramsToCondition, $this->scopes);
        $query = $query->getQuery();
        $query = $this->buildOrderBy($query, $params);
        $query->getQuery();
        if ($this->withCount) {
            $query->withCount($this->withCount);
        }
        return $query;
    }

    /**
     * @param $query
     * @param $params
     *
     * @return mixed
     */
    private function buildOrderBy($query, $params)
    {
        if (isset($params['order_by'])) {
            $order = isset($params['order']) ? strtoupper($params['order']) : 'ASC';
            $query->orderBy($params['order_by'], $order);
        }
        return $query;
    }

    /**
     * @param $id
     * @param array $params
     * @param bool $isDelete
     *
     * @return bool
     */
    private function buildPivotRelations($id, $params = [], bool $isDelete = false)
    {
        if (empty($this->pivotRelations)) {
            return false;
        }
        if ($isDelete) {
            foreach ($this->pivotRelations as $key => $relation) {
                $model = $this->model->find($id);
                $model->$relation()->detach();
            }
            return true;
        }
        if (empty($params)) {
            return false;
        }
        foreach ($this->pivotRelations as $key => $relation) {
            if (!isset($params[$key]) && (!isset($params['dont_remove_pivot']) || isset($params['dont_remove_pivot']) && $params['dont_remove_pivot'] === false)) {
                $model = $this->model->find($id);
                $model->$relation()->detach();
            }
            foreach ($params as $k => $item) {
                if ($key === $k) {
                    $model = $this->model->find($id);
                    $model->$relation()->detach();
                    $model->$relation()->attach($item);
                }
            }
        }
    }

    /**
     * @param $params
     *
     * @return array
     */
    private function clearNullValue($params)
    {
        if (empty($params)) {
            return [];
        }
        $result = [];
        foreach ($params as $key => $item) {
            if ($item !== '') {
                $result[$key] = $item;
            }
        }
        return $result;
    }

    /**
     * @param array $withs
     */
    protected function queryWith($withs = [])
    {
        $this->withs = $withs;
    }

    /**
     * @param string $with
     */
    protected function appendWith($with = '')
    {
        array_push($this->withs, $with);
    }

    /**
     * @param string $with
     *
     * @return bool
     */
    protected function removeWith(string $with = '')
    {
        if ($with === '') {
            return false;
        }
        if (empty($this->withs)) {
            return false;
        }
        if (!in_array($with, $this->withs)) {
            return false;
        }
        array_splice($this->withs, array_search($with, $this->withs), 1);
    }

    /**
     * @param array $withs
     *
     * @return bool
     */
    protected function removeWiths(array $withs = [])
    {
        if (empty($withs)) {
            return false;
        }
        if (empty($this->withs)) {
            return false;
        }
        $result = [];
        foreach ($this->withs as $with) {
            if (in_array($with, $withs)) {
                continue;
            }
            array_push($result, $with);
        }
        $this->withs = $result;
    }

    /**
     * @param array $tables
     */
    protected function paramsToTable($tables = [])
    {
        $this->paramsToTable = $tables;
    }

    /**
     * @param $relations
     */
    protected function paramsToRelation($relations = [])
    {
        $this->paramsToRelation = $relations;
    }

    /**
     * @param array $fields
     */
    protected function paramsToField($fields = [])
    {
        $this->paramsToField = $fields;
    }

    /**
     * @param array $conditions
     */
    protected function paramsToCondition($conditions = [])
    {
        $this->paramsToCondition = $conditions;
    }

    /**
     * @param array $relations
     */
    protected function pivotRelations($relations = [])
    {
        $this->pivotRelations = $relations;
    }

    /**
     * @param array $scopes
     */
    protected function scopes($scopes = [])
    {
        $this->scopes = $scopes;
    }

    /**
     * @param array $groups
     */
    protected function whereGroups($groups = [])
    {
        $this->whereGroups = $groups;
    }

    /**
     * @param array $conditions
     */
    protected function whereGroupConditions($conditions = [])
    {
        $this->whereGroupConditions = $conditions;
    }

    /**
     * @param array $withCount
     */
    protected function withCount(array $withCount = [])
    {
        $this->withCount = $withCount;
    }
}
