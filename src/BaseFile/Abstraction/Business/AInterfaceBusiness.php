<?php
/**
 * Created by PhpStorm.
 * User: LINH
 * Date: 10/23/2019
 * Time: 10:53 AM
 */

namespace App\Abstraction\Business;


interface AInterfaceBusiness
{
    /**
     * Lấy danh sách phần tử
     *
     * @param array $params
     * @param array $fields
     * @param bool $getAll
     *
     * @return mixed
     */
    public function getAll(array $params = [], array $fields = ['*'], bool $getAll = false);

    /**
     * Lấy 1 phần tử theo id
     *
     * @param $id
     *
     * @return mixed
     */
    public function getOne($id);

    /**
     * Lấy 1 phần tử theo điều kiện
     *
     * @param array $params
     *
     * @return mixed
     */
    public function getOneBy(array $params = []);

    /**
     * Tạo 1 phần tử
     *
     * @param array $params
     * @param boolean $getData
     *
     * @return mixed
     */
    public function create(array $params = [], $getData = false);

    /**
     * Cập nhật 1 phần tử
     *
     * @param $id
     * @param array $params
     * @param boolean $getData
     *
     * @return mixed
     */
    public function update($id, array $params = [], bool $getData = false);

    /**
     * Cập nhật theo điều kiện
     *
     * @param array $params
     * @param array $where
     *
     * @return mixed
     */
    public function updateBy(array $params = [], array $where = []);

    /**
     * Xóa 1 phần tử
     *
     * @param $id
     *
     * @return mixed
     */
    public function delete($id);
}
