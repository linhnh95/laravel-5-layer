<?php
/**
 * Created by PhpStorm.
 * User: LINH
 * Date: 10/23/2019
 * Time: 10:58 AM
 */

namespace App\Business;


use App\Abstraction\Business\AInterfaceBusiness;
use App\Abstraction\SQL\AInterface;
use App\Common\FileHelpers;

abstract class ABusiness
{
    /**
     * @var AInterfaceBusiness
     */
    public $interface;

    /**
     * ABusiness constructor.
     * @param AInterface $interface
     */
    public function __construct(AInterface $interface)
    {
        $this->interface = $interface;
    }

    /**
     * Lấy danh sách phần tử
     *
     * @param array $params
     * @param array $fields
     * @param bool $getAll
     *
     * @return mixed
     */
    public function getAll(array $params = [], array $fields = ['*'], bool $getAll = false)
    {
        $result = $this->interface->getAll($params, $fields, $getAll);
        if (isset($params['load_image'])) {
            foreach ($result as $item) {
                $item->image_hash = $item->image;
                $item->image = FileHelpers::getFileUrlFromServer($item->image);
            }
        }
        return $result;
    }

    /**
     * Lấy 1 phần tử theo id
     *
     * @param $id
     *
     * @return mixed
     */
    public function getOne($id)
    {
        return $this->interface->getOne($id);
    }

    /**
     * Lấy 1 phần tử theo params
     *
     * @param array $params
     * @param array $fields
     *
     * @return mixed
     */
    public function getOneBy(array $params = [], $fields = [])
    {
        $result = $this->interface->getOneBy($params, $fields);
        if (isset($params['load_image'])) {
            $result->image_hash = $result->image;
            $result->image = FileHelpers::getFileUrlFromServer($result->image);
        }
        return $result;
    }

    /**
     * Tạo 1 phần tử
     *
     * @param array $params
     * @param boolean $getData
     *
     * @return mixed
     */
    public function create(array $params = [], $getData = false)
    {
        return $this->interface->create($params, $getData);
    }

    /**
     * Cập nhật 1 phần tử
     *
     * @param $id
     * @param array $params
     * @param boolean $getData
     *
     * @return mixed
     */
    public function update($id, array $params = [], bool $getData = false)
    {
        return $this->interface->update($id, $params, $getData);
    }

    /**
     * Cập nhật theo điều kiện
     *
     * @param array $params
     * @param array $where
     *
     * @return mixed
     */
    public function updateBy(array $params = [], array $where = [])
    {
        return $this->interface->updateBy($params, $where);
    }

    /**
     * Xóa 1 phần tử
     *
     * @param $id
     *
     * @return mixed
     */
    public function delete($id)
    {
        return $this->interface->delete($id);
    }
}
