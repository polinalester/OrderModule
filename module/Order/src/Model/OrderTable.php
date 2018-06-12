<?php

namespace Order\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class OrderTable
{
    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    public function fetchAll($paginated=false)
    {
        //if ($paginated) {
        //    return $this->fetchPaginatedResults();
        //}

        return $this->tableGateway->select();
    }
	public function fetchByUser($user_id, $paginated=false)
	{
        if ($paginated) {
            return $this->fetchPaginatedResults($user_id);
        }
		return $this->tableGateway->select(['user_id' => $user_id]);
	}
    private function fetchPaginatedResults($user_id)
    {
        $select = new Select($this->tableGateway->getTable());

        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new Order());

        $paginatorAdapter = new DbSelect(
            $select,
            $this->tableGateway->getAdapter(),
            $resultSetPrototype
        );

        $paginator = new Paginator($paginatorAdapter);
        return $paginator;
    }
    public function getOrder($id)
    {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();
        if (! $row) {
            throw new RuntimeException(sprintf(
                'Could not find row with identifier %d',
                $id
            ));
        }
        return $row;
    }
    public function saveOrder(Order $order)
    {
        $data = [
            //'id' => $order->id,
            'user_id' => $order->user_id,
            'book_id'  => $order->book_id,
            'ordered'  => $order->ordered,
            'finished'  => $order->finished,
            'status' => $order->status,
        ];
        $id = (int) $order->id;
        if ($id === 0) {
            $this->tableGateway->insert($data);
            return;
        }
        if (! $this->getOrder($id)) {
            throw new RuntimeException(sprintf(
                'Cannot update order with identifier %d; does not exist',
                $id
            ));
        }
        $this->tableGateway->update($data, ['id' => $id]);
    }
    public function deleteOrder($id)
    {
        $this->tableGateway->delete(['id' => (int) $id]);
    }
}