<?php
/**
 * Created by PhpStorm.
 * User: LINH
 * Date: 10/4/2019
 * Time: 10:35 AM
 */

namespace App\Abstraction\Dependency;


interface AInterface
{
    /**
     * Thực hiện query
     *
     * @param $methods
     * @param $raw
     *
     * @return mixed
     */
    public function queryRaw($methods, $raw);

    /**
     * Lây tất cả rows trong bảng hoặc phân trang theo param getAll
     *
     * @param array $params
     * @param array $fields
     * @param bool $getAll
     *
     * @return mixed
     */
    public function getAll(array $params = [], array $fields = ['*'], bool $getAll = false);

    /**
     * Lấy tất cả dữ liệu số lượng lớn theo điều kiện
     *
     * @param array $params
     * @param array $fields
     *
     * @return mixed
     */
    public function getCursorBigData(array $params = [], array $fields = []);

    /**
     * Lấy 1 phần tử trong bảng
     *
     * @param int $id
     * @param array $params
     * @param array $fields
     *
     * @return mixed
     */
    public function getOne(int $id, array $params = [], array $fields = ['*']);

    /**
     * Lấy 1 phần tử theo params
     *
     * @param array $params
     * @param array $fields
     * @return mixed
     */
    public function getOneBy(array $params = [], array $fields = ['*']);

    /**
     * Lấy 1 phần tử bất kì
     *
     * @param array $params
     * @param array $fields
     *
     * @return mixed
     */
    public function getRandomBy(array $params, array $fields = ['*']);

    /**
     * Lấy tổng số phần tử theo điều kiện
     *
     * @param array $params
     *
     * @return mixed
     */
    public function countBy(array $params = []);

    /**
     * Tính tổng số của 1 phần tử theo điều kiện
     *
     * @param string $field
     * @param array $params
     * @param string $type
     *
     * @return mixed
     */
    public function aggregatesBy(string $field, array $params = [], string $type = 'sum');

    /**
     * Kiểm tra phần tử tồn tại hay không theo điều kiện
     *
     * @param array $params
     *
     * @return mixed
     */
    public function existBy(array $params = []);

    /**
     * Tạo 1 phần tử trong bảng
     *
     * @param array $params
     * @param bool $getData
     *
     * @return mixed
     */
    public function create(array $params = [], bool $getData = false);

    /**
     * Insert danh sách phần tử
     *
     * @param array $params
     *
     * @return mixed
     */
    public function insert(array $params = []);

    /**
     * Cập nhật 1 phần tử trong bảng
     *
     * @param int $id
     * @param array $params
     * @param bool $getData
     *
     * @return mixed
     */
    public function update(int $id, array $params = [], bool $getData = false);

    /**
     * Cập nhật nhiều phần tử
     *
     * @param array $params
     * @param array $where
     * @param bool $getData
     *
     * @return mixed
     */
    public function updateBy(array $params = [], array $where = [], bool $getData = false);

    /**
     * Cập nhật nhiều phần tử bằng SQL
     *
     * @param array $where
     * @param array $raws
     * @param bool $getData
     *
     * @return mixed
     */
    public function updateRaw(array $where = [], array $raws = [], bool $getData = false);

    /**
     * Xóa 1 phần tử trong bảng
     *
     * @param int $id
     *
     * @return mixed
     */
    public function delete(int $id);

    /**
     * Xóa phần tử trong bảng theo điều kiện
     *
     * @param array $params
     *
     * @return mixed
     */
    public function deleteBy(array $params = []);

    /**
     * Xóa vĩnh viễn 1 phần tử trong bảng
     *
     * @param int $id
     *
     * @return mixed
     */
    public function forceDelete(int $id);

    /**
     * Xóa vĩnh viễn phần tử trong bảng theo điều kiện
     *
     * @param array $params
     *
     * @return mixed
     */
    public function forceDeleteBy(array $params = []);

    /**
     * Phục hồi 1 phần tử đã xóa
     *
     * @param int $id
     *
     * @return mixed
     */
    public function restore(int $id);

    /**
     * Phục hồi phần tử trong bảng theo điều kiện
     *
     * @param array $params
     *
     * @return mixed
     */
    public function restoreBy(array $params = []);

    /**
     * Chuẩn hóa dữ liệu trước khi insert/update database
     *
     * @param array $params
     *
     * @return mixed
     */
    public function formatParams(array $params = []);
}
