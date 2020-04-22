<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as baseModel;
use Illuminate\Support\Facades\DB;


class Model extends baseModel
{
    public $timestamps = false;

    /**
     *
     * 获取id值
     *
     * @param $id :id
     * @param string $fields :字段
     * @return array
     */
    public static function getById($id, $fields = '*')
    {
        if (empty($id)) {
            return [];
        }
        return collect(self::select($fields)->where('id', intval($id))->first())->toArray();
    }

    /**
     *
     * 批量获取id值
     *
     * @param $column :字段名称
     * @param $val :值
     * @param string $fields :字段
     * @return array
     */
    public static function getByIds($column, $val, $fields = '*')
    {
        if (empty($column) || empty($val)) {
            return [];
        }
        return collect(self::select($fields)->whereIn($column, $val)->get())->toArray();
    }

    /**
     *
     * 批量获取键值对关联数据
     *
     * @param $fld
     * @param $val
     * @param string $key_fld
     * @param string $fields
     * @return array
     */
    public static function getByIdsToKey($fld, $val, $key_fld = 'id', $fields = '*')
    {
        if (empty($fld) || empty($val)) {
            return [];
        }
        if ($fields != '*') {
            $fields = is_array($fields) ? $fields : [$fields];
            $fields[] = $key_fld;
            $fields[] = $fld;
            $fields = array_unique($fields);
        }
        return array_column(
            (array)collect(self::select($fields)->whereIn($fld, $val)->get())->toArray(), null, $key_fld
        );
    }

    /**
     *
     * 获取单条数据
     *
     * @param $where
     * @param string $fields
     * @return array
     */
    public static function fetch($where, $fields = '*')
    {
        if (empty($where)) {
            return [];
        }
        return collect(self::select($fields)->whereRaw($where)->first())->toArray();
    }

    /**
     *
     * 获取单条数据排序
     *
     * @param $where
     * @param string $fields
     * @param string $order
     * @return array
     */
    public static function fetchOrderBy($where, $fields = '*', $order = '')
    {
        if (empty($where)) {
            return [];
        }
        return collect(self::select($fields)->whereRaw($where)->orderByRaw($order)->first())->toArray();
    }

    /**
     *
     * 更新数据
     *
     * @param array $where
     * @param array $data
     * @return bool
     */
    public static function manyWhereUpdate(array $where, array $data)
    {
        if (empty($where) || empty($data)) {
            return false;
        }
        return self::where($where)->update($data);
    }

    /**
     *
     * 添加单条数据并返回 id
     *
     * @param array $data
     * @return int
     */
    public static function add(array $data)
    {
        if (empty($data) || !is_array($data)) {
            return 0;
        }
        return self::insertGetId($data);
    }

    /**
     *
     * 批量添加
     *
     * @param array $data
     * @return int
     */
    public static function adds(array $data)
    {
        if (empty($data) || !is_array($data)) {
            return 0;
        }
        return self::insert($data);
    }

    /**
     *
     * 获取总和
     *
     * @param $where
     * @param $fld
     * @return int
     */
    public static function getSum($where, $fld)
    {
        if (empty($where) || empty($fld)) {
            return 0;
        }
        return self::whereRaw($where)->sum($fld);
    }

    /**
     *
     * 更新表字段
     *
     * @param $where
     * @param array $data
     * @return bool
     */
    public static function updates($where, array $data)
    {
        if (empty($where) || empty($data)) {
            return false;
        }
        return self::whereRaw($where)->update($data);
    }

    /**
     *
     * 删除
     *
     * @param $where
     * @param bool $is_resetid
     * @return bool
     */
    public static function del($where, $is_resetid = false)
    {
        if (empty($where)) {
            return false;
        }
        $res = self::whereRaw($where)->delete();
        if ($is_resetid) {
            self::truncate();
        }
        return $res;
    }

    /**
     *
     * 获取总条数
     *
     * @param $where
     * @return int
     */
    public static function getNum($where)
    {
        if (empty($where)) {
            return 0;
        }
        return self::whereRaw($where)->count();
    }

    /**
     *
     * 获取列表
     *
     * @param $where
     * @param string $order
     * @param string $fields
     * @param int $page
     * @param int $size
     * @return array
     */
    public static function getLists($where, $order = '', $fields = '*', $page = 1, $size = 30)
    {
        if (empty($where)) {
            return [];
        }
        $page = $page < 1 ? 0 : $page - 1;
        return collect(
            self::select($fields)
                ->whereRaw($where)
                ->orderByRaw($order)
                ->skip(intval($page * $size))
                ->take((int)$size)->get()
        )->toArray();
    }

    /**
     *
     * 自定查询
     *
     * @param $where
     * @param string $order
     * @param string $fields
     * @return array
     */
    public static function getData($where, $order = 'id desc', $fields = '*')
    {
        if (empty($where)) {
            return [];
        }
        return collect(
            self::select($fields)
                ->whereRaw($where)
                ->orderByRaw($order)
                ->get()
        )->toArray();
    }

    /**
     *
     * 获取最大值
     *
     * @param $where
     * @param $fields
     * @return int
     */
    public static function getMax($where, $fields)
    {
        if (empty($where) || empty($fields)) {
            return 0;
        }
        return self::whereRaw($where)->max($fields);
    }

    /**
     *
     * 递增
     *
     * @param $where
     * @param $fld
     * @param int $l
     * @return bool
     */
    public static function increments($where, $fld, $l = 1)
    {
        if (empty($where) || empty($fld)) {
            return false;
        }
        return self::whereRaw($where)->increment($fld, $l);
    }

    /**
     *
     * 获取某字段值
     *
     * @param $where
     * @param $fields
     * @return bool
     */
    public static function getPluck($where, $fields)
    {
        if (empty($where) || empty($fields)) {
            return false;
        }
        return self::whereRaw($where)->pluck($fields);
    }

    /**
     *
     * 递减
     *
     * @param $where
     * @param $fld
     * @param int $l
     * @return bool
     */
    public static function decrements($where, $fld, $l = 1)
    {
        if (empty($where) || empty($fld)) {
            return false;
        }
        return self::whereRaw($where)->decrement($fld, $l);
    }

    /**
     *
     * 开始记录日志
     *
     * @return mixed
     */
    public static function enableQueryLog()
    {
        return DB::enableQueryLog();
    }

    /**
     *
     * 返回sql
     *
     * @return mixed
     */
    public static function getQueryLog()
    {
        return DB::getQueryLog();
    }

    /**
     *
     * 手动使用事务
     *
     */
    public static function beginTransaction()
    {
        return DB::beginTransaction();
    }

    /**
     *
     * 事务回滚
     *
     */
    public static function rollBack()
    {
        return DB::rollBack();
    }

    /**
     *
     * 事务提交
     *
     */
    public static function commit()
    {
        return DB::commit();
    }
}