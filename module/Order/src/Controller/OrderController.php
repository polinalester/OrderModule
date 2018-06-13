<?php 
namespace Order\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Stdlib\Hydrator;
use Order\Model\OrderTable;
use Order\Model\Order;
use Order\Form\OrderForm;
use VuFind\Exception\Auth as AuthException;
use VuFind\Controller\MyResearchController;
use VuFind\Record\Loader;

class OrderController extends MyResearchController
{
    private $table;

    public function __construct(OrderTable $table)
    {
        $this->table = $table;
    }

    public function viewOrderAction()
    {
    	if (!$this->getAuthManager()->isLoggedIn()) {
            if ($this->params()->fromQuery('redirect', true)) {
                $this->setFollowupUrlToReferer();
            }
            return $this->forwardTo('MyResearch', 'Login');
        }
    	$user = $this->getUser();
        $user_id =  $user->id;

        $paginator = $this->table->fetchByUser($user_id,true);

        $page = (int) $this->params()->fromQuery('page', 1);
        $page = ($page < 1) ? 1 : $page;
        $paginator->setCurrentPageNumber($page);

        $paginator->setItemCountPerPage(10);

        return new ViewModel(['paginator' => $paginator]);

        //return new ViewModel([
        //    'orders' => $this->table->fetchByUser($user_id),
        //]);
    }

    public function addOrderAction()
    {
    	$user = $this->getUser();
        if (!$user) {
            return $this->forceLogin();
        }

        $user_id =  $user->id;

        $form = new OrderForm();
        $form->get('submit')->setValue('Добавить');

        $request = $this->getRequest();

        if (! $request->isPost()) {
            return ['form' => $form];
        }

        $order= new Order();
        $form->setInputFilter($order->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return ['form' => $form];
        }

        $order->exchangeArray($form->getData());
        $order->user_id = $user_id;
        $order->ordered->date('Y-m-d H:i:s');
        $order->status = 'В обработке';
        $this->table->saveOrder($order);
        return $this->redirect()->toRoute('order', ['action' => 'viewOrder']); 
    }

    public function editOrderAction()
    {
    	$user = $this->getUser();
        if (!$user) {
            return $this->forceLogin();
        }

        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('order', ['action' => 'addOrder']);
        }

        try {
            $order = $this->table->getOrder($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('order', ['action' => 'viewOrder']);
        }

        $form = new OrderForm();


        $form->bind($order);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        $viewData = ['id' => $id, 'form' => $form];

        if (! $request->isPost()) {
            return $viewData;
        }

        //$form->setInputFilter($order->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return $viewData;
        }

        $user_id =  $user->id;

        $order->user_id = $user_id;

        $this->table->saveOrder($order);

        return $this->redirect()->toRoute('order', ['action' => 'viewOrder']);
    }

    public function deleteOrderAction()
    {
    	$user = $this->getUser();
        if (!$user) {
            return $this->forceLogin();
        }

        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('order', ['action' => 'viewOrder']);
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->table->deleteOrder($id);
            }

            // Redirect to list of albums
            return $this->redirect()->toRoute('order', ['action' => 'viewOrder']);
        }

        return [
            'id'    => $id,
            'order' => $this->table->getOrder($id),
        ];
    }
}