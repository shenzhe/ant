<?php

namespace dao;

use ZPHP\Core\Config as ZConfig,
    ZPHP\Db\Pdo as ZPdo;

abstract class Base
{
    private $entity;

    private static $_dbs = [];
    /**
     * @var ZPdo
     */
    private $_db = null;
    private $_dbTag = null;

    /**
     * Base constructor.
     * @param $entity
     * @param string $useDb //使用的库名,不使用数据库,可设置为空
     */
    public function __construct($entity, $useDb = 'common')
    {
        $this->entity = $entity;
        if ($useDb) {
            $this->useDb($useDb);
        }
    }

    /**
     * @param $tag
     * @return null|ZPdo
     * @throws \Exception
     * @desc 使用db
     */
    public function useDb($tag)
    {
        if (empty(self::$_dbs[$tag])) {
            $config = ZConfig::getField('pdo', $tag);
            self::$_dbs[$tag] = new ZPdo($config, $this->entity, $config['dbname']);
            self::$_dbs[$tag]->setClassName($this->entity);
        } else {
            self::$_dbs[$tag]->checkPing();
        }
        $this->_db = self::$_dbs[$tag];
        $this->_dbTag = $tag;
        $this->_db->checkPing();
        return $this->_db;
    }

    /**
     * @param $tableName
     * @desc 更换表
     */
    public function changeTable($tableName)
    {
        $this->_db->setTableName($tableName);
    }

    /**
     * @return bool
     * @desc 关闭db
     */
    public function closeDb($tag = null)
    {
        if (empty($tag)) {
            $tag = $this->_dbTag;
        }
        if (empty($tag)) {
            return true;
        }
        if (empty($this->_db)) {
            return true;
        }
        $this->_db->close();
        unset(self::$_dbs[$tag]);
        return true;
    }

    /**
     * @param $id
     * @return mixed
     * @desc 跟据 id 获取记录
     */
    public function fetchById($id)
    {
        return $this->_db->fetchEntity("id={$id}");
    }

    /**
     * @param $where
     * @param null $params
     * @param string $fields
     * @param null $orderBy
     * @return mixed
     */
    public function fetchEntity($where, $params = null, $fields = '*', $orderBy = null)
    {
        return $this->_db->fetchEntity($this->parseWhere($where), $params, $fields, $orderBy);
    }

    /**
     * @param array $items
     * @param null $params
     * @param string $fields
     * @param null $orderBy
     * @param null $limit
     * @return mixed
     * @desc 多行记录获取
     */
    public function fetchAll(array $items = [], $params = null, $fields = '*', $orderBy = null, $limit = null)
    {
        return $this->_db->fetchAll($this->parseWhere($items), $params, $fields, $orderBy, $limit);
    }

    /**
     * @param $items
     * @return string
     * @desc 解析where
     */
    private function parseWhere($items)
    {

        if (empty($items)) {
            return 1;
        }

        if (is_string($items)) {
            return $items;
        }

        $where = '1';

        if (!empty($items['union'])) {
            foreach ($items['union'] as $union) {
                $where .= " {$union}";
            }
            unset($items['union']);
        }

        foreach ($items as $k => $v) {
            $where .= " AND {$k} {$v}";
        }

        return $where;
    }

    /**
     * @param string $where
     * @return mixed
     */
    public function fetchWhere($where = '')
    {
        return $this->_db->fetchAll($this->parseWhere($where));
    }

    /**
     * @param $attr
     * @param array $items
     * @param int $change
     * @return mixed
     */
    public function update($attr, $items = [], $change = 0)
    {
        if (empty($attr)) {
            $attr = new $this->entity;
            $attr->create();
        }

        $fields = array();
        $params = array();
        if (is_object($attr)) {
            foreach ($attr->getFields() as $key) {
                $fields[] = $key;
                $params[$key] = $attr->$key;
            }
        } else {
            $fields = array_keys($attr);
            $params = $attr;
        }

        if (!empty($items)) {
            $where = $this->parseWhere($items);
        } else {
            $pkid = $attr::PK_ID;
            $where = "`{$pkid}`=" . $attr->$pkid;
        }
        return $this->_db->update($fields, $params, $where, $change);
    }

    /**
     * @param $attr
     * @return mixed
     */
    public function add($attr)
    {
        if (empty($attr) || is_array($attr)) {
            $entity = new $this->entity;
            $entity->create($attr);
        } elseif (is_object($attr)) {
            $entity = $attr;
        }
        return $this->_db->add($entity, $entity->getFields());
    }

    /**
     * @param $where
     * @return mixed
     * @throws \common\MyException
     */
    public function remove($where)
    {
        if (empty($where)) {
            throw new \common\MyException('remove where empty', \common\ERROR::REMOVE_WHERE_EMPTY);
        }
        return $this->_db->remove($this->parseWhere($where));
    }

    /**
     * @param array $items
     * @param string $fields
     * @param null $orderBy
     * @param null $start
     * @param null $limit
     * @return mixed
     */
    public function fetchArray(array $items = [], $fields = "*", $orderBy = null, $start = null, $limit = null)
    {
        if (empty($items)) {
            return $this->_db->fetchArray(1, $fields, $orderBy, $start, $limit);
        }
        return $this->_db->fetchArray($this->parseWhere($items), $fields, $orderBy, $start, $limit);
    }

    /**
     * @param array $items
     * @return mixed
     */
    public function fetchCount($items = [])
    {
        return $this->_db->fetchCount($this->parseWhere($items));
    }

    /**
     * @param array $items
     * @param string $fields
     * @return mixed
     */
    public function fetchOne($items = [], $fields = "*")
    {
        return $this->_db->fetchEntity($this->parseWhere($items), null, $fields);
    }

    /**
     * @param array $items
     * @return mixed
     * @throws \Exception
     */
    public function fetchByUnion($items = [])
    {
        $fields = "";
        $tables = "";
        $dbname = ZConfig::getField('pdo', $this->_dbTag, 'dbname');
        foreach ($items['fields'] as $table => $fieldArr) {
            foreach ($fieldArr as $field) {
                $fields .= "{$table}.{$field},";
            }
            $tables .= "{$dbname}.$table,";
        }
        $fields = rtrim($fields, ',');
        $tables = rtrim($tables, ',');
        $wheres = "1";

        foreach ($items['where'] as $item) {
            $wheres .= " and " . $item;
        }

        $order = "";
        if (!empty($items['order'])) {
            $order = $items['order'];
        }

        $sql = "select {$fields} from {$tables} where {$wheres}{$order}";
        return $this->fetchBySql($sql);
    }

    /**
     * @param $sql
     * @return mixed
     * @desc 执行一个sql
     */

    public function fetchBySql($sql)
    {
        return $this->_db->fetchBySql($sql);
    }


    public function checkPing()
    {
        if(!empty(self::$_dbs)) {
            foreach (self::$_dbs as $db) {
                $db->checkPing();
            }
        }
    }

}